<?php
/**
 * Find entries from the MongoDB using the given search query.
 * @version 0.1.0
 */

return function($self, $args) {
	$query = $args[0];
	$self->last_query = (array)$query;
	return $self->collection->find($query);
};
