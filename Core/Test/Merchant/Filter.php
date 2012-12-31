<?php

namespace Core\Test\Merchant;

use Core\Test\Base;
use Core\Merchant\Filter as Subject;

class Filter extends Base
{
	private function testRegexp($value, $test) {
		$regexp = Subject::getRegexp($test);
		return (boolean)preg_match("@^{$regexp}$@", $value);
	}

	public function testAddFilter()
	{
		$this->message('Testing filter creation');
		Subject::addFilter('testFilter', '[abcd]*', function($value) {
			return $value === 'test';
		});

		assert(Subject::testFilter('test') === true);
		assert(Subject::testFilter('wrong') === false);
		assert($this->testRegexp('1234', 'testFilter') === false);
		assert($this->testRegexp('acbd', 'testFilter') === true);
	}

	public function testEmail()
	{
		$this->message('Testing email filter');
		$mock = \Faker\Internet::email();
		assert(Subject::email($mock) === true);
		assert($this->testRegexp($mock, 'email') === true);
		$mock = \Faker\Internet::freeEmail();
		assert(Subject::email($mock) === true);
		assert($this->testRegexp($mock, 'email') === true);
		$mock = \Faker\Internet::safeEmail();
		assert(Subject::email($mock) === true);
		assert($this->testRegexp($mock, 'email') === true);
		$mock = 'not.an@email';
		assert(Subject::email($mock) === false);
		assert($this->testRegexp($mock, 'email') === false);
	}

	public function testInteger()
	{
		$this->message('Testing integer filter');
		$mock = 1234;
		assert(Subject::integer($mock) === true);
		assert($this->testRegexp($mock, 'integer') === true);
		$mock = '1234';
		assert(Subject::integer($mock) === false);
		assert($this->testRegexp($mock, 'integer') === true);
		$mock = 'notanint';
		assert(Subject::integer($mock) === false);
		assert($this->testRegexp($mock, 'integer') === false);
	}

	public function testUrl()
	{
		$this->message('Testing URL filter');
		$mock = 'nathankot.com';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, 'url') === true);
		$mock = 'http://nathankot.com';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, 'url') === true);
		$mock = 'https://nathankot.com';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, 'url') === true);
		$mock = 'one.nathankot.com';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, 'url') === true);
		$mock = 'http://one.nathankot.com/';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, 'url') === true);
		$mock = 'http://one.nathankot.com/one/two/three';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, 'url') === true);
		$mock = 'http://one.nathankot.com/one/two/three.end';
		assert(Subject::url($mock) === true);
		assert($this->testRegexp($mock, 'url') === true);
	}

	public function testPhone()
	{
		$this->message('Testing Phone Number filter');
		$mock = '1234 534-930';
		assert(Subject::phone($mock) === true);
		assert($this->testRegexp($mock, 'phone') === true);
		$mock = \Faker\PhoneNumber::phoneNumber();
		assert(Subject::phone($mock) === true);
		assert($this->testRegexp($mock, 'phone') === true);
		$mock = 'not a number 12';
		assert(Subject::phone($mock) === false);
		assert($this->testRegexp($mock, 'phone') === false);
		$mock = 'Definitely not';
		assert(Subject::phone($mock) === false);
		assert($this->testRegexp($mock, 'phone') === false);
	}

	public function testRequired()
	{
		$this->message('Testing Required Filter');
		$mock = '';
		assert(Subject::required($mock) === false);
		assert($this->testRegexp($mock, 'required') === false);
		$mock = 0;
		assert(Subject::required($mock) === true);
		assert($this->testRegexp($mock, 'required') === true);
		$mock = 'pass';
		assert(Subject::required($mock) === true);
		assert($this->testRegexp($mock, 'required') === true);
	}
}
