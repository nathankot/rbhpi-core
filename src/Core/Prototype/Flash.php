<?php
/**
 * @version 0.1.0
 */

namespace Core\Prototype;

use \Core\Merchant\FlashMessaging;

/**
 * Instances of this access should provide access to FlashMessaging via
 * overloaded properties.
 */
class Flash implements \ArrayAccess
{
	public function __get($name)
	{
		if (strpos($name, 'has_') === 0) {
			$name = substr($name, 4);
			return (boolean)FlashMessaging::getMessage($name);
		}

		if (strpos($name, 'collect_') === 0) {
			$name = substr($name, 8);
			return FlashMessaging::getMessages($name);
		}

		return FlashMessaging::getMessage($name);
	}

	public function __isset($name) {
		return true;
	}

	public function offsetGet($name)
	{
		return $this->__get($name);
	}

	public function offsetExists($name)
	{
		return $this->__get("has_{$name}");
	}

	public function offsetSet($name, $v) {}
	public function offsetUnset($name) {}
}
