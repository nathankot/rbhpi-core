<?php

namespace Core\Blueprint;

/**
 * The **Base Class**. Defines common characteristics between all objects in the framework.
 */
abstract class Object
{

	/**
	 * Every class should have a configuration set. If it does not, it would use
	 * the Object base class' configuration set.
	 * @var array
	 */
	protected static $config = [];

	/**
	 * Has the static init method been called?
	 * @var boolean
	 */
	protected static $initiated_classes = [];

	/**
	 * Class configuration sets can be adjusted by passing additional configuration parameters.
	 * @param  array  $config New configuration parameters.
	 * @return void
	 */
	final public static function config($config = [])
	{
		$class_name = get_called_class();
		if (!isset(self::$initiated_classes[$class_name])) {
			self::$initiated_classes[$class_name] = true;
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
		static::$config = extend(static::$config, $config);
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
