<?php

namespace Core\Test\Merchant;

use \Core\Merchant\FlashMessaging as Subject;

class FlashMessaging extends \Core\Test\Base
{
	public function test()
	{
		$this->message('Creating mock messages.');
		Subject::addMessage('Message one.', 'one');
		Subject::addMessage('Second message one.', 'one');
		Subject::addMessage('Message two.', 'two', 2);
		Subject::addMessage('Message three.', 'three');

		$this->message('Testing retrieval of messages.');
		$message = Subject::getMessage('one');
		assert($message === 'Message one.');
		$messages = Subject::getMessages('one');
		assert($messages === ['Message one.', 'Second message one.']);
		$message = Subject::getMessage('two');
		assert($message === 'Message two.');

		$this->message('Testing removal of message.');
		$message = Subject::getMessage('three');
		assert($message === 'Message three.');
		Subject::clearMessages('three');
		$message = Subject::getMessage('three');
		assert($message === null);

		$this->message('Testing persistence of messages.');
		Subject::refresh();
		$message = Subject::getMessage('one');
		assert($message === null);
		$message = Subject::getMessage('two');
		assert($message === 'Message two.');

		$this->message('Testing persistence count of messages.');
		Subject::refresh();
		$message = Subject::getMessage('two');
		asserT($message === null);
	}
}
