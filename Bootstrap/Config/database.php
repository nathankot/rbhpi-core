<?php
/**
 * Sets up the Database adapters to the default (MongoDB)
 * @version 0.1.0
 */

\Core\Blueprint\Model::adapt('skip', 'Core\Adapter\MongoDB\Skip');
\Core\Blueprint\Model::adapt('limit', 'Core\Adapter\MongoDB\Limit');
\Core\Blueprint\Model::adapt('find', 'Core\Adapter\MongoDB\Find');
\Core\Blueprint\Model::adapt('findOne', 'Core\Adapter\MongoDB\FindOne');
\Core\Blueprint\Model::adapt('connect', 'Core\Adapter\MongoDB\Connect');
\Core\Blueprint\Model::adapt('save', 'Core\Adapter\MongoDB\Save');

# Default MongoDB Connection Setting
\Core\Blueprint\Model::config([
		'connection_settings' => [
				'server' => 'mongodb://localhost:27017'
			,	'connect' => true
			,	'timeout' => 2500
			,	'database' => 'rbhpi'
			,	'username' => null
			,	'password' => null
			,	'replicaSet' => null
			,	'readPreference' => null
			,	'w' => 1 # Write Concern
			,	'wTimeout' => 2500
		]
]);
