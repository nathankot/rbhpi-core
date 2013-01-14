<?php

$exit_code = 0;

message(Color::white('Test', 'blue'));
line();

$class_name = str_replace(array('_', '/'), '\\', $argv[2]);
$class_name = '\\' . trim($class_name, '\\');

$_RBHPI['load_core_config']();

if (strpos($class_name, '\\Core') !== 0) {
	$_RBHPI['load_app_config']();
}

if ($class_name === '\\Core') {
	$non_tested = ['mock'];
	foreach (glob(CORE_SRC.'/Core/Test/*/*.php') as $file) {
		preg_match('@'.preg_quote(CORE_SRC).'\/Core\/Test\/(.*)\/(.*)\.php@', $file, $file_parts);
		$class_name = "\\Core\\Test\\{$file_parts[1]}\\{$file_parts[2]}";
		if (in_array(strtolower($file_parts[1]), $non_tested)) {
			continue;
		}
		test($class_name);
	}
	return;
}

test($class_name);

function test($class_name) {
	try {
		if (class_exists($class_name)) {
			message(Color::yellow("Testing {$class_name}"));
			new $class_name();
			message(Color::green('Testing complete.'));
			line();
		} else {
			error("Class {$class_name} does not exist!");
		}
	} catch (\Exception $e) {
		error($e->getMessage());
		$exit_code = 1;
	}
}

exit($exit_code);
