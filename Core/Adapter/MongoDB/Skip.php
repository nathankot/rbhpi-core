<?php
/**
 * Returns a handler that skips a MongoCursor.
 * @version 0.1.0
 */

return function($self, $args) {
	$self->data->skip($args[0]);
	return $self->data;
};
