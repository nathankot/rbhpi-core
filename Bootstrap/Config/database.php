<?php
/**
 * Sets up the Database adapters to the default (MongoDB)
 * @version 0.2.0
 */

\Core\Blueprint\Model::adaptSet('Core\Adapter\MongoDB');

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
