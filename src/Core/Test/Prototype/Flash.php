<?php
/**
 * @version 0.1.0
 */

namespace Core\Test\Prototype;

use \Core\Merchant\FlashMessaging;
use \Core\Prototype\Flash as Subject;

/**
 * This object should be used by the view to access flash messages.
 */
class Flash extends \Core\Test\Base
{
	public function init()
	{
		FlashMessaging::addMessage('One', 'test');
		FlashMessaging::addMessage('Two', 'test');
	}

	public function test()
	{
		$this->message('Creating Flash Object');
		$flash = new Subject();

		$this->message('Testing existence check of flash key.');
		$result = $flash->has_test;
		assert($result === true);
		$result = $flash->has_not;
		assert($result === false);

		$this->message('Testing get of single message');
		$result = $flash->test;
		assert($result === 'One');
		$result = $flash->collect_test;
		assert($result === ['One', 'Two']);
	}
}



