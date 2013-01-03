<?php
/**
 * Returns a handler that limits a MongoCursor.
 * @version 0.1.0
 */

return function($self, $args) {
	$self->data->limit($args[0]);
	return $self->data;
};
