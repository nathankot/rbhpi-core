<?php
/**
 * Set up the PSR-0 compliant Autoloader.
 */

spl_autoload_register(function($className) {
	$className = '\\' . ltrim($className, '\\');
	$classPath = str_replace(array('\\', '_'), '/', $className);
	$file = ROOT . '/Vendor' . $classPath . '.php';
	require_once $file;
});
