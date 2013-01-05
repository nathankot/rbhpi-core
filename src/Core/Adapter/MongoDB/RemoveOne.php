<?php
/**
 * Remove one item matching a query with MongoDB
 * @version 0.1.0
 */

return function($self, $args) {
	$options = array_merge([
				'w' => $self->_mongoWriteConcern
			,	'justOne' => true
	], (array)$args[1]);
	return $self->collection->remove($args[0], $options);
};
