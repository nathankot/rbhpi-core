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
	}

	/**
	 * Constructor called after `__construct()`. This will prevent user-defined constructors from overriding
	 * default system constructors.
	 */
	protected function init() {}

	/**
	 * Call the user-constructor
	 */
	public function __construct()
	{
		return call_user_func_array(array($this, 'init'), func_get_args());
	}

}
