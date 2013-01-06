<?php
/**
 * @version 0.1.0
 */

namespace Core\Wireframe\Merchant;

/**
 * Provides a pre determined interface of interacting with flash messages.
 */
interface FlashMessaging
{
	public static function refresh();
	public static function addMessage($message, $key = 'rbhpi', $persistence = 1);
	public static function getMessage($key = 'rbhpi');
	public static function getMessages($key = 'rbhpi');
	public static function clearMessages($key = 'rbhpi');
}
