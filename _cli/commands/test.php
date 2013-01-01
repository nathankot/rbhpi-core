<?php

message(Color::white('Test', 'blue'));
line();

$class_name = str_replace(array('_', '/'), '\\', $argv[2]);

if ($class_name === 'core') {
	$non_tested = ['mock'];
	foreach (glob(ROOT.'/Vendor/Core/Test/*/*.php') as $file) {
		preg_match('@\/Vendor\/Core\/Test\/(.*)\/(.*)\.php@', $file, $file_parts);
		$class_name = "\\Core\\Test\\{$file_parts[1]}\\{$file_parts[2]}";
		if (in_array(strtolower($file_parts[1]), $non_tested)) {
			continue;
		}
		test($class_name);
	}
	return;
}

try {
	test($class_name);
} catch (\RuntimeException $e) {
	error($e->getMessage());
}

function test($class_name) {
	if (class_exists($class_name)) {
		message("Testing {$class_name}");
		line();
		new $class_name();
		line();
		message(Color::green('Testing complete.'));
	} else {
		error("Class {$class_name} does not exist!");
	}
}
