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
	 * Class configuration sets can be adjusted by passing additional configuration parameters.
	 * @param  array  $config New configuration parameters.
	 * @return void
	 */
	final public static function config(array $config)
	{
		static::$config = array_merge_recursive(static::$config, $config);
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
		return call_user_func_array([$this, 'init'], func_get_args());
	}

}
