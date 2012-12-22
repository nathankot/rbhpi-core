<?php

namespace Core\Augmentation;

/**
 * Classes using this trait become singletones.
 */
trait Singleton {
	/**
	 * Return instance of the singleton.
	 * @return Object The instance
	 */
	public static function getInstance()
	{
		static $instance = null;
		return $instance ?: $instance = new static();
	}

	/**
	 * Prevent cloning.
	 * @return void
	 */
	public function __clone()
	{
		trigger_error('Cloning '.__CLASS__.' is not allowed.',E_USER_ERROR);
	}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup()
	{
		trigger_error('Unserializing '.__CLASS__.' is not allowed.',E_USER_ERROR);
	}

}
