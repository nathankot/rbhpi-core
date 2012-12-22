<?php
/**
 * Load user-configuration files. In natural descending order.
 */

foreach (glob(ROOT . '/App/Config/*.php') as $file) {
	require_once $file;
}
