<?php

namespace Core\Augmentation;

/**
 * Gives an Object a set of tools that allows it to have Adaptable methods.
 */
trait Adaptable
{
	/**
	 * Stores all the adapters for the current class.
	 * @var array
	 */
	protected static $adapters;

	/**
	 * Find the right adapter for the given method, and return the reuslt.
	 * @param  string $method Method name.
	 * @param  Array  $args   Arguments to tbe passed to the method, typically from `func_get_args()`.
	 * @return mixed         The result of the call.
	 */
	protected function useAdapter($method, Array $args)
	{
		if (!isset(self::$adapters[$method])) {
			throw new \Exception\AdapterNotFound("The adapter for `".__CLASS__."::".$method."()` does not exist!");
		}
		$args = [$this, $args]; # First argument: Current Object, Second: Passed Arguments.
		return call_user_func_array(self::$adapters[$method], $args);
	}

	/**
	 * Creates an adapter for the given method.
	 * @param  string   $method Method to adapt.
	 * @param  callable $handle The handler.
	 * @return void
	 */
	public static function adapt($method, callable $handle)
	{
		self::$adapters[$method] = $handle;
	}

	/**
	 * Adds additional functionality to the current class to be able to use arbitrary adapters. I.e methods that have
	 * not been defined in the class, but have specified adapters.
	 * @param  string $function The method name.
	 * @param  array $args     The array of arguments.
	 * @return mixed           Returns the result of using the adapter.
	 */
	public function __call($function, $args)
	{
		return $this->useAdapter($function, $args);
	}
}
