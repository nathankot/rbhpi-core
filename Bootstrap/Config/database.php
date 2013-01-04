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
\Core\Blueprint\Model::adapt('remove', 'Core\Adapter\MongoDB\Remove');
\Core\Blueprint\Model::adapt('removeOne', 'Core\Adapter\MongoDB\RemoveOne');

# Default MongoDB Connection Setting
\Core\Blueprint\Model::config([
		'connection_settings' => [
				'server' => 'mongodb://localhost:27017'
			,	'database' => 'rbhpi'
			,	'connect' => true
			,	'timeout' => 2500
			,	'username' => null
			,	'password' => null
			,	'replicaSet' => null
			,	'readPreference' => null
			,	'w' => 1 # Write Concern
			,	'wTimeout' => 2500
		]
]);
