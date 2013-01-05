<?php
/**
 * The save handler for MongoDB.
 * @version 0.1.0
 */

return function($self, $args) {
	$options = array_merge([
		'w' => $self->_mongoWriteConcern
	], $args[1]);
	return $self->collection->save($args[0], $args[1]);
};
