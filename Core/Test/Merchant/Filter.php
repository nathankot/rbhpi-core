<?php

namespace Core\Test\Merchant;

use Core\Test\Base;
use Core\Merchant\Filter as Subject;

class Filter extends Base
{
	private function testRegexp($value, $regexp) {
		return (boolean)preg_match("@^{$regexp}$@", $value);
	}

	public function testEmail()
	{
		$this->message('Testing email filter');
		$mock = 'nk@nathankot.com';
		assert(Subject::email($mock) === true);
		assert($this->testRegexp($mock, Subject::getRegexp('email')) === true);
		$mock = 'not.an@email';
		assert(Subject::email($mock) === false);
		assert($this->testRegexp($mock, Subject::getRegexp('email')) === false);
	}

	public function testInteger()
	{
		$this->message('Testing integer filter');
		$mock = 1234;
		assert(Subject::integer($mock) === true);
		assert($this->testRegexp($mock, Subject::getRegexp('integer')) === true);
		$mock = '1234';
		assert(Subject::integer($mock) === false);
		assert($this->testRegexp($mock, Subject::getRegexp('integer')) === true);
		$mock = 'notanint';
		assert(Subject::integer($mock) === false);
		assert($this->testRegexp($mock, Subject::getRegexp('integer')) === false);
	}

	public function testUrl()
	{
		$this->message('Testing URL filter');
		$mock = 'nathankot.com';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, Subject::getRegexp('url')) === true);
		$mock = 'http://nathankot.com';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, Subject::getRegexp('url')) === true);
		$mock = 'https://nathankot.com';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, Subject::getRegexp('url')) === true);
		$mock = 'one.nathankot.com';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, Subject::getRegexp('url')) === true);
		$mock = 'http://one.nathankot.com/';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, Subject::getRegexp('url')) === true);
		$mock = 'http://one.nathankot.com/one/two/three';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, Subject::getRegexp('url')) === true);
		$mock = 'http://one.nathankot.com/one/two/three.end';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, Subject::getRegexp('url')) === true);
	}
}
