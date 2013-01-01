<?php
/**
 * Load default config and user-configuration files. In natural descending order.
 */

foreach (glob(ROOT.'/Bootstrap/Config/*.php') as $file) {
	echo $file;
	require_once $file;
}

foreach (glob(ROOT . '/App/Config/*.php') as $file) {
	require_once $file;
}
