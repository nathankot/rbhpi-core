<?php
/**
 * rbhp.php is the front for the command-line tool.
 *
 * It auto-loads all the dependencies in the objects directory.
 * And loads a file in the command directory according to the command
 * given by the user.
 *
 * In addition, this file defines some vital functions for the command line tool.
 *
 * @author Nathan Kot <nk@nathankot.com>
 * @version 0.1.0
 */

/*
Developers: Why is this looking procedural?
---
Well IMO I think a command line utility is
just a fancy way of grouping scripts together.
Sure, some can be interactive, but in the end
they are just scripts - and that reeks of procedural.

So what we have here is kind of a hybrid of
OOP and procedural programming. You can really
do what you want. Commands are stored in
./commands and each file is designated to the
command of its file name. Write the command
in a way that suits what it does. If it's dead
simple, just write a script. If it's interactive
and more complex, use some objects. You can chuck
your dependencies in ./objects and they will be
loaded on init.

PS. commands should be self-executing.
====================================== */

require_once dirname(dirname(__DIR__)) . '/Bootstrap/run.php';

define('CLI_ROOT', dirname(dirname(__DIR__)).'/_cli');
define('CORE_CLI_ROOT', __DIR__);
define('BIN_DIR', CLI_ROOT.'/bin');
define('DS', DIRECTORY_SEPARATOR);

$app_name = explode('/', ROOT);
$app_name = array_pop($app_name);
$command = isset($argv[1]) ? strtolower($argv[1]) : null;

# Introduction
echo <<<HEREDOC
	\n
	# RBHP Command Line Tool
	>> Hello :) Connected to the RBHP app '$app_name'
	\n
HEREDOC;

# Core dependencies
$object_dir = CORE_CLI_ROOT . DS . 'objects';
$object_dir_handle = opendir($object_dir);
while ($file = readdir($object_dir_handle)) {
	if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
		require_once($object_dir . DS . $file);
	}
}

# All objects in the objects dir
# Chuck all the dependencies in here
$object_dir = BIN_DIR . DS . 'objects';
$object_dir_handle = opendir($object_dir);
while ($file = readdir($object_dir_handle)) {
	if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
		require_once($object_dir . DS . $file);
	}
}

# Core commands
$commands_dir = BIN_DIR . DS . 'commands';
if (is_readable($commands_dir . DS . $command . '.php')) {
	include $commands_dir . DS . $command . '.php';
} else {
	$commands_dir = CORE_CLI_ROOT . DS . 'commands';
	if (is_readable($commands_dir . DS . $command . '.php')) {
		include $commands_dir . DS . $command . '.php';
	} else {
		error('Un-recognized command');
		exit();
	}
}

/**
 * Prints a line of `$message`, indented one tab
 * @param  string $message The message
 * @return void
 */
function message($message)
{
	echo "\t" . $message . "\n";
}

/**
 * Prints a red message
 * @param string $message The error message
 * @return void
 */
function error($message)
{
	echo color::red("\n\t! " . $message . "\n\n");
}

/**
 * Prints an empty line
 * @return void
 */
function line()
{
	echo "\n";
}

/**
 * Wait for the user's feedback and return what they typed
 * @return string The user-feedback
 */
function request_feedback() {
	message('Waiting for input...');
	return trim(fgets(STDIN));
}

function abort()
{
	error('Aborting...');
	exit;
}
