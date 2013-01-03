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
					,	'url' => [
								'regexp' => '^(?:(?:http|https)://)?(?:[A-z0-9_-]*?\.){1,}[A-z]{2,7}/?(?:[A-z0-9_-]*/?)*(?:[A-z0-9_-]*\.[A-z]{1,10})?$'
							,	'handle' => function($value) {
									# The php FILTER_VALIDATE_URL filter is too strict. (OPINION)
									return (boolean)preg_match("@^".self::getRegexp('url')."$@", $value);
								}
						]
					,	'phone' => [
								'regexp' => '\D*(?:\d\D*){3,}' # Minimum of 3 digits, see <http://stackoverflow.com/a/1118201/1740868>
							,	'handle' => function($value) {
									# Minimum of three digits, over 50% needs to be digits
									$digit_count = preg_match_all('@\d@', $value);
									return  $digit_count >= 3 && ($digit_count / strlen($value)) > 0.5;
								}
						]
					,	'name' => [
								'regexp' => '[\w\s]{0,85}'
							,	'handle' => function($value) {
									return is_string($value) && strlen($value) < 86;
								}
						]
					,	'number' => [
								'regexp' => '[0-9\-\.\,\s]*'
							,	'handle' => function($value) {
									return (boolean)preg_match("@^".self::getRegexp('number')."$@", $value);
								}
						]
					,	'required' => [
								'regexp' => '.{1,}'
							,	'handle' => function($value) {
									return isset($value) && $value !== '';
								}
						]
					,	'integer' => [
								'regexp' => '[0-9]*'
							,	'handle' => function($value) {
									return is_int($value);
								}
						]
					,	'string' => [
								'regexp' => '.*'
							,	'handle' => function($value) {
									return is_string($value);
								}
						]
					,	'array' => [
								'regexp' => '(?:[.*]|{.*})'
							,	'handle' => function($value) {
									return is_array($value);
								}
						]
					,	'float' => [
								'regexp' => '[0-9\.\,]*'
							,	'handle' => function($value) {
									return is_float($value);
								}
						]
					,	'boolean' => [
								'regexp' => '(?:0|1|false|true|FALSE|TRUE)'
							,	'handle' => function($value) {
									return is_bool($value);
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
		self::config();
		if (!isset(self::$config['filters'][$name]['regexp'])) {
			throw new \Exception\BadFilter("Regexp of Filter {$name} doesn't exist.");
		}
		return self::$config['filters'][$name]['regexp'];
	}

	/**
	 * Check if the given filter name exists.
	 * @param  string $name Filter name.
	 * @return boolean
	 */
	public static function checkExist($name)
	{
		self::config();
		return isset(self::$config['filters'][$name]);
	}

	public static function addFilter($name, $regexp, Callable $handle)
	{
		self::config();
		self::$config['filters'][$name] = [
				'regexp' => $regexp
			, 'handle' => $handle
		];
	}

	/**
	 * Handle all attempts to filter something.
	 * @param  string $name Name of the filter to use.
	 * @param  array $args Arguments passed to this static method (Should only be one).
	 * @return boolean       Whether or not the value has passed the filter.
	 */
	public static function __callStatic($name, $args)
	{
		self::config();
		if (!isset(self::$config['filters'][$name])) {
			throw new \Exception\BadFilter("Filter {$name} doesn't exist.");
		}
		$value = current($args);
		$handle = self::$config['filters'][$name]['handle'];
		return (boolean)$handle($value);
	}
}
