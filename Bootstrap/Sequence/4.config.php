<?php
/**
 * Load default config and user-configuration files. In natural descending order.
 * Stores the loader as a function if we are running in CLI, which allows the script to decide how much should be loaded.
 */

$_RBHPI['load_core_config'] = function() {
	foreach (glob(ROOT.'/Bootstrap/Config/*.php') as $file) {
		require_once $file;
	}
};

$_RBHPI['load_app_config'] = function() {
	foreach (glob(ROOT . '/App/Config/*.php') as $file) {
		require_once $file;
	}
};

if (php_sapi_name() !== 'cli') {
	$_RBHPI['load_core_config']();
	$_RBHPI['load_app_config']();
}
