<?php

message(color::white('Config', 'blue'));

$config_dir = CLI_ROOT.DS.'data'.DS.'config';
$configs = array();

$current = trim(file_get_contents($config_dir.DS.'current_config'), "\n");
$chosen = isset($argv[2]) ? strtolower($argv[2]) : null;

# Create an array of available configs
$handle = opendir($config_dir);

while (($file = readdir($handle)) !== false) {
	if ($file == '.' || $file == '..') {
		continue;
	}
	if (filetype($config_dir.DS.$file) === 'dir') {
		$configs[] = $file;
	}
}

if (!$chosen) {
	error('No configuration specified.');
	exit;
}

/*
List all config items
====================================== */
if ($chosen == 'list') {
	message(color::bold('Available configurations:'));
	line();
	# They want a list of configurations.
	foreach ($configs as $config) {
		if ($config == $current) { $config = '* '.$config; }
		message($config);
	}
	line();
	message('--end--');
	exit;
}

/*
Remove a config item
====================================== */
if ($chosen == 'rm') {
	$remove = $argv[3];
	if (!in_array($remove, $configs)) {
		error("Configuration named `{$remove}` does not exist");
		abort();
	}
	if ($current == $remove) {
		error("Cannot remove your chosen configuration");
		abort();
	}
	exec("rm -rf {$config_dir}/{$remove}");
	message("{$remove} configuration has been removed.");
	exit;
}

/*
Choose/create config item
====================================== */
if (!in_array($chosen, $configs)) {
	message(color::yellow("{$chosen} is not a configuration yet. Create one?"));
	message('Type yes to create, no to cancel');
	if (request_feedback() === 'yes') {
		message("Creating new configuration `{$chosen}`, from `{$current}`");
		newConfig($chosen, $current);
		message('Switching to newly created configuration...');
		switchConfig($chosen);
	} else {
		abort();
	}
} else {
	message("Switching to {$chosen}");
	switchConfig($chosen);
}

function newConfig($name, $copyOf)
{
	global $config_dir;
	$new_dir = $config_dir . DS . $name;
	$copied_dir = $config_dir . DS . $copyOf;
	if (!is_dir($copied_dir)) {
		error("{$copied_dir} is not a directory!");
		abort();
	}
	$new_dir = escapeshellarg($new_dir);
	$copied_dir = escapeshellarg($copied_dir);
	exec("cp -rf {$copied_dir} {$new_dir}");
}

function switchConfig($new)
{
	global $config_dir;
	$fh = fopen($config_dir.DS.'current_config', 'w');
	fwrite($fh, $new);
}
