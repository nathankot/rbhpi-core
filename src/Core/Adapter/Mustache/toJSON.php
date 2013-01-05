<?php
/**
 * Converts data of the passed object into JSON.
 * @version 0.1.0
 */

return function($self, $args) {
	return json_encode($self->getData());
};
