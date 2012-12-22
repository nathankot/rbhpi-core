<?php
/**
 * Set up the PSR-0 compliant Autoloader.
 */

spl_autoload_register(function($className) {
	$className = '\\' . ltrim($className, '\\');
	$classPath = str_replace(array('\\', '_'), '/', $classPath);
	$file = ROOT . $classPath;
	require_once $file;
});
