<?php

namespace Core\Blueprint;

/**
 * The **Base Class**. Defines common characteristics between all objects in the framework.
 */
abstract class Object
{

	/**
	 * Every class has a configuration set.
	 * @var array
	 */
	protected static $config = [];

	/**
	 * Has the static init method been called?
	 * @var boolean
	 */
	protected static $is_init = false;

	/**
	 * Class configuration sets can be adjusted by passing additional configuration parameters.
	 * @param  array  $config New configuration parameters.
	 * @return void
	 */
	final public static function config($config = [])
	{
		if (!static::$is_init) {
			static::$is_init = true;
			$class_name = get_called_class();

			if (method_exists($class_name, 'init')) {
				$reflection = new \ReflectionMethod($class_name, 'init');
				if ($reflection->isStatic()) {
					$reflection->invoke(null);
				}
			}
			if (method_exists($class_name, 'preConfig')) {
				$reflection = new \ReflectionMethod($class_name, 'preConfig');
				if ($reflection->isStatic()) {
					$reflection->invoke(null);
				}
			}
		}
		static::$config = array_merge(static::$config, $config);
		return static::$config;
	}

	/**
	 * Retrieve the current configuration.
	 * @return array Current Configuration.
	 */
	final public static function getConfig()
	{
		return static::$config;
	}

	/**
	 * Apply a filter to a given method of the Object.
	 * @param  string $method_name  Method name.
	 * @param  callable $handle     Callable handle, takes `$chain`, and `$self`.
	 * @return boolean              Whether the filter was successfully applied.
	 */
	final public static function filter($method_name, $handle)
	{

	}

	/**
	 * Constructor called after `__construct()`. This will prevent user-defined constructors from overriding
	 * default system constructors.
	 */
	// public function init() {}

	/**
	 * Call the user-constructor
	 */
	public function __construct()
	{
		if (method_exists($this, 'init')) {
			return call_user_func_array([$this, 'init'], func_get_args());
		}
	}

}
