<?php

message(Color::white('Test', 'blue'));
line();

$class_name = str_replace(array('_', '/'), '\\', $argv[2]);

try {
	if (class_exists($class_name)) {
		message("Testing {$class_name}");
		line();
		new $class_name();
		line();
		message(Color::green('Testing complete.'));
	} else {
		error("Class {$class_name} does not exist!");
	}
} catch (\RuntimeException $e) {
	error($e->getMessage());
}
