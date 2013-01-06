<?php
/**
 * @version 0.1.0
 */

namespace Core\Merchant;

/**
 * This Merchant provides a way of storing and restrieving messages within the same session,
 * between pages.
 */
class FlashMessaging extends \Core\Blueprint\Object implements
	\Core\Wireframe\Merchant\FlashMessaging
{
	/**
	 * The $_SESSION key that will store all relevant data.
	 */
	const RBHPI_FLASH_MESSAGE_KEY = '__RBHPI_FLASH_MESSAGES';

	/**
	 * This holds the reference to the $_SESSION data being used.
	 * @var reference
	 */
	private static $data;

	/**
	 * Set self::$data as a reference to the $_SESSION data. Create an array if it doesn't exist.
	 * @return void
	 */
	public static function init()
	{
		$_SESSION[self::RBHPI_FLASH_MESSAGE_KEY] = $_SESSION[self::RBHPI_FLASH_MESSAGE_KEY] ?: [];
		self::$data =& $_SESSION[self::RBHPI_FLASH_MESSAGE_KEY];
	}

	/**
	 * Go through each message and reduce their persistence by 1. Remove the message if the persistence is up.
	 * @return void
	 */
	public static function refresh()
	{
		self::config();
		self::$data = array_map(function($value) {
			foreach ($value as $key => &$message) {
				$message['persistence'] -= 1;
				if ($message['persistence'] <= 0) {
					unset($value[$key]);
				}
			}
			return $value;
		}, self::$data);
	}

	/**
	 * Add a message to a given FlashMessage key/category.
	 * @param string  $message     The message.
	 * @param string  $key         FlashMessage category.
	 * @param integer $persistence How many requests should it persist through.
	 */
	public static function addMessage($message, $key = 'rbhpi', $persistence = 1)
	{
		self::config();
		# Sanity Checks
		if (!is_string($message)) {
			throw new \InvalidArgumentException("Flash message must be a string!");
		}
		if (!is_string($key)) {
			throw new \InvalidArgumentException("Flash message key must be a string!");
		}
		if (!is_integer($persistence)) {
			throw new InvalidArgumentException("Flash message persistence must be an integer!");
		}

		if (!is_array(self::$data[$key])) {
			self::$data[$key] = [];
		}

		self::$data[$key][] =	[
				'message' => (string)$message
			,	'persistence' => (integer)$persistence
		];
	}

	/**
	 * Get the first message given a key.
	 * @param  string $key Key/Category.
	 * @return string      The relevant message.
	 */
	public static function getMessage($key = 'rbhpi')
	{
		self::config();
		reset(self::$data[$key]);

		if (!is_string($key)) {
			throw new \InvalidArgumentException("Flash message key must be a string!");
		}

		if (!isset(current(self::$data[$key])['message'])) {
			return null;
		}

		return current(self::$data[$key])['message'];
	}

	/**
	 * Get an array of messages according to a key/category.
	 * @param  string $key Key/category.
	 * @return array      Array of messages.
	 */
	public static function getMessages($key = 'rbhpi')
	{
		self::config();
		if (!is_string($key)) {
			throw new \InvalidArgumentException("Flash message key must be a string!");
		}

		$result = array_map(function($value) {
			return $value['message'];
		}, self::$data[$key]);

		return $result;
	}

	/**
	 * Clear all messages that belong to a certain key/category.
	 * @param  string $key Key/category.
	 * @return void
	 */
	public static function clearMessages($key = 'rbhpi')
	{
		self::config();
		self::$data[$key] = [];
	}
}
