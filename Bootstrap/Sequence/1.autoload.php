<?php
/**
 * Set up the PSR-0 compliant Autoloader.
 */

spl_autoload_register(function($className) {
	$className = '\\' . ltrim($className, '\\');
	$classPath = str_replace(array('\\', '_'), '/', $className);
	$file = ROOT . '/Vendor' . $classPath . '.php';
	if (!is_readable($file)) {
		return false;
	} else {
		require_once $file;
		if (!class_exists($className, false) &&
				!interface_exists($className, false) &&
				!trait_exists($className, false)) {
			throw new \Exception\BadClass("Class `{$className}` does not exist!");
		}
	}
});

# Register the Composer autoloader.
$composer_loader = ROOT . '/Vendor/autoload.php';
if (is_readable($composer_loader)) {
	require $composer_loader;
}
