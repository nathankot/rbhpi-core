<?php
/**
 * @version 0.1.0
 */

namespace \Core\Augmentation;

/**
 * Provides a means of access to flash messaging.
 * Makes use of PHP sessions. (Which can be adapted to use Memcache via
 * setting the session handler.)
 */
trait FlashMessage
{
	protected function clearFlashMessages($key = null)
	{
		$this->initFlashMessages();
		if (!$key) {
			$_SESSION['RBHP_FLASH_MESSAGES'] = [];
		} else {
			$_SESSION['RBHP_FLASH_MESSAGES'][$key] = [];
		}
	}

	protected function addFlashMessage($key, $message, $expiry = 1)
	{
		$this->initFlashMessages();
		$_SESSION['RBHP_FLASH_MESSAGES'][$key][] = $message;
	}

	protected function getFlashMessages($key)
	{
		$this->initFlashMessages();
		return $_SESSION['RBHP_FLASH_MESSAGES'][$key];
	}

	protected function getFlashMessage($key)
	{
		$this->initFlashMessages();
		return $_SESSION['RBHP_FLASH_MESSAGES'][$key][0];
	}

	private function initFlashMessages()
	{
		if (!is_array($_SESSION['RBHP_FLASH_MESSAGES'])) {
			$_SESSION['RBHP_FLASH_MESSAGES'] = [];
		}
	}
}
