<?php
/**
 * Create a MongoDB connection and store it.
 * @version 0.1.1
 */

return function($self, $args) {
	try {
		$settings = $args[0];
		$server = $settings['server'];
		unset($settings['server']);

		# Try to use the newer \MongoClient class.
		if (class_exists('\MongoClient', false)) {
			$connection = new \MongoClient();
		} elseif (class_exists('\Mongo', false)) {
			$connection = new \Mongo($server, $settings);
		} else {
			throw new \Exception\MissingDependency("The PECL extension 'Mongo' required for MongoDB is missing!");
		}

		$connection = $connection->{$settings['database']};

		\Core\Blueprint\Model::config([
				'established_connection' => $connection
		]);

		# Lets get the best settings for this collection using its priority.
		switch (true) {
			case ($self->priority <= 3):
				$self->_mongoWriteConcern = 'majority';
				$self->_mongoReadPreference = \MongoClient::RP_PRIMARY;
			case ($self->priority <= 2):
				$self->_mongoWriteConcern = 1;
				$self->_mongoReadPreference = \MongoClient::RP_PRIMARY_PREFERRED;
			case ($self->priority <= 1):
				$self->_mongoWriteConcern = 0;
				$self->_mongoReadPreference = \MongoClient::RP_NEAREST;
			break;
			default:
				$self->_mongoWriteConcern = 1;
				$self->_mongoReadPreference = \MongoClient::RP_PRIMARY_PREFERRED;
		}

		$collection = $connection->{$self->name};
		$collection->setReadPreference($self->_mongoReadPreference);

		$self->collection = $collection;
	} catch (\MongoException $e) {
		throw new \Exception\DBConnection("Could not connect to MongoDB with and caught `".get_class($e)."` with: `".$e->getMessage()."`");
	}
};
