<?php

namespace Core\Merchant;

abstract class Filter extends \Core\Blueprint\Object implements
	\Core\Wireframe\Merchant\Filter
{
	protected static $config = [];

	/**
	 * Configures initial filters. Each filter has two validation methods - one through a callable handle,
	 * which is used within the logic and therefore is strict. And one regexp match for the filter. The regexp is
	 * used by the Router to test different URI's and does not to be as strict.
	 * @return void
	 */
	public static function init()
	{
		self::config([
				'filters' => [
						'email' => [
								'regexp' => '\b[A-z0-9._%+-]+\@[A-z0-9.-]+\.[A-z]{2,4}\b'
							,	'handle' => function($value) {
									return filter_var($value, FILTER_VALIDATE_EMAIL);
								}
						]
					,	'integer' => [
								'regexp' => '[0-9]*'
							,	'handle' => function($value) {
									return is_int($value);
								}
						]
					,	'url' => [
								'regexp' => '^(?:(?:http|https)://)?(?:[A-z0-9_-]*?\.){1,}[A-z]{2,7}/?(?:[A-z0-9_-]*/?)*(?:[A-z0-9_-]*\.[A-z]{1,10})?$'
							,	'handle' => function($value) {
									# The php FILTER_VALIDATE_URL filter is too strict. (OPINION)
									return (boolean)preg_match("@^".self::getRegexp('url')."$@", $value);
								}
						]
				]
		]);
	}

	/**
	 * Get the Regexp variant of the filter.
	 * @param  string $name Name of the filter
	 * @return strign       Regexp
	 */
	public static function getRegexp($name)
	{
		if (empty(self::$config)) {
			self::init();
		}
		if (!isset(self::$config['filters'][$name]['regexp'])) {
			throw new \Exception\BadFilter("Regexp of Filter {$name} doesn't exist.");
		}
		return self::$config['filters'][$name]['regexp'];
	}

	public static function checkExist($name)
	{
		if (empty(self::$config)) {
			self::init();
		}
		return isset(self::$config['filters'][$name]);
	}

	/**
	 * Handle all attempts to filter something.
	 * @param  string $name Name of the filter to use.
	 * @param  array $args Arguments passed to this static method (Should only be one).
	 * @return boolean       Whether or not the value has passed the filter.
	 */
	public static function __callStatic($name, $args)
	{
		if (empty(self::$config)) {
			self::init();
		}
		if (!isset(self::$config['filters'][$name])) {
			throw new \Exception\BadFilter("Filter {$name} doesn't exist.");
		}
		$value = current($args);
		$handle = self::$config['filters'][$name]['handle'];
		return (boolean)$handle($value);
	}
}
