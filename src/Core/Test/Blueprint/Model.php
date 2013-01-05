<?php

namespace Core\Test\Blueprint;

class Model extends \Core\Test\Base
{
	public function test()
	{
		$this->message('Creating Mock model');
		$model = new \Core\Test\Mock\Model();

		$this->message('Cleaning up database');
		$model->remove([]);

		$this->message('Creating Mock model entries');
		$entry = $model->create([
				'name' => 'nathan'
		]);
		$model->save($entry);
		assert($entry['name'] === 'nathan' && $entry['default'] === '12345');

		$entry = $model->create([
				'name' => 'andrew'
		]);
		$model->save($entry);

		$this->message('Testing Model::findOne()');
		$found = $model->findOne(['name' => 'nathan']);
		assert($found['name'] === 'nathan');

		$this->message('Testing Model iteration');
		$model->find([]);
		foreach ($model as $iterated_entry) {
			assert(in_array($iterated_entry['name'], ['nathan', 'andrew']));
		}

		$this->message('Testing Model Count');
		assert(count($model) === 2);

		$this->message('Testing Model updating entry');
		$entry = $model->findOne(['name' => 'nathan']);
		$entry['name'] = 'changed';
		$model->save($entry);
		assert(count($model) === 2);
		$found = $model->findOne(['name' => 'nathan']);
		assert($found === false);
	}
}
