<?php
/**
 * @version 0.2.0
 */

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
	public static function adapt($method, $handle)
	{
		if (!is_callable($handle)) {
			$file = trim($handle, '\\');
			$file = ROOT . '/Vendor/' . str_replace(['\\', '\\\\'], '/', $file) . '.php';
			if (!is_readable($file)) {
				throw new \Exception\BadAdapter("Could not resolve `$handle` into an adapter!");
			}
			$handle = include($file);
		}
		self::$adapters[$method] = $handle;
	}

	/**
	 * Takes a folder of adapters, and creates an adapter for each filename in that folder as a method.
	 * @param  string $set_name Namespace to the set
	 * @return void
	 */
	public static function adaptSet($set_name)
	{
		$folder = trim($set_name, '\\');
		$folder = ROOT . '/Vendor/' . str_replace(['\\', '\\\\'], '/', $folder);
		if (!is_dir($folder)) {
			throw new \Exception\BadAdapter("The adapter namespace `{$set_name}` could not be found!");
		}
		foreach (glob($folder.'/*.php') as $file) {
			$method_name = lcfirst(basename($file, '.php'));
			$handle = include($file);
			static::adapt($method_name, $handle);
		}
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
