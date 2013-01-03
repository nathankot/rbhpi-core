<?php
/**
 * Finds one object within the encapsulated data of the model.
 * @version 0.1.0
 */

return function($self, $args) {
	if (empty($self->last_query)) {
		$query = $args[0];
	} else {
		$query = array_merge($args[0], $self->last_query);
	}
	$result = $self->collection->findOne($query);
	return $result === null ? false : $result;
};
