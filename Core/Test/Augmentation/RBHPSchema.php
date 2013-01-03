<?php

namespace Core\Test\Augmentation;

class RBHPSchema extends \Core\Test\Base
{
	use \Core\Augmentation\RBHPSchema;

	public function test()
	{
		$this->message('Creating Mock Schema');

		$schema = [
				'string' => 'string'
			,	'integer' => 'integer'
			,	'float' => 'float'
			,	'boolean' => 'boolean'
			,	'default_value' => 'string?:default_value'
			,	'required' => 'string:required'
			,	'nested' => [
						'name' => 'string:name'
					,	'phone' => 'string:phone'
					,	'url' => 'string:url'
				]
		];

		$this->message('Testing default value.');

		$entry = $this->createEntryFromSchema($schema);
		assert($entry['default_value'] === 'default_value');

		$this->message('Testing required value throws error');

		$errors = $this->checkEntryAgainstSchema($entry, $schema);
		assert(in_array('required', $errors['required']));

		$this->message('Testing a pass Schema check.');
		$entry['required'] = 'fulfilled';
		$errors = $this->checkEntryAgainstSchema($entry, $schema);
		assert($errors === true);

		$this->message('Testing string type filter.');
		$entry['string'] = 123;
		$errors = $this->checkEntryAgainstSchema($entry, $schema);
		assert(in_array('string', $errors['string']));
		$entry['string'] = 'strinnnngg';
		$errors = $this->checkEntryAgainstSchema($entry,$schema);
		assert($errors === true);

		$this->message('Testing integer type filter.');
		$entry['integer'] = 'string';
		$errors = $this->checkEntryAgainstSchema($entry, $schema);
		assert(in_array('integer', $errors['integer']));
		$entry['integer'] = 1234;
		$errors = $this->checkEntryAgainstSchema($entry, $schema);
		assert($errors === true);

		$this->message('Testing float type filter.');
		$entry['float'] = 1234;
		$errors = $this->checkEntryAgainstSchema($entry, $schema);
		assert(in_array('float', $errors['float']));
		$entry['float'] = 123.434;
		$errors = $this->checkEntryAgainstSchema($entry, $schema);
		assert($errors === true);

		$this->message('Testing boolean type filter.');
		$entry['boolean'] = 'string';
		$errors = $this->checkEntryAgainstSchema($entry, $schema);
		assert(in_array('boolean', $errors['boolean']));
		$entry['boolean'] = true;
		$errors = $this->checkEntryAgainstSchema($entry, $schema);
		assert($errors === true);
	}
}
