<?php
/**
 * Deploy.php is a cli command. It handles the deployment of the application into production
 * via Git Push.
 *
 * Server requirements
 * ---
 * App & core repo's setup correctly as bare repositories.
 * RSA auth setup for ssh, and fingerprints recognized.
 *
 * @version 0.1.0
 * @see  ../rbhp.php
 */

message(Color::white('Deploy', 'blue'));

$config = new Config('deploy');
$config_repos = new Config('repos');

/*
Allow user to go over settings
====================================== */
$config->confirm();
$config_repos->confirm();

/*
We got confirmation, so deployment logic
goes here.
====================================== */

/*
Pre-flight checks
====================================== */
message("Checking existence of remotes.");
foreach ($config_repos->getInnerArray() as $name => $settings) {
	$shell_output = array();
	chdir($settings['local_root']);
	exec('git remote', $shell_output);
	if (!in_array($settings['remote_name'], $shell_output)) {
		error("Please ensure `{$config['remote_name']}` is configured as a remote of your {$name}'s repo.");
	}
}

# Connect to remote
$ssh = new Ssh(array(
		'host' => $config->get('remote_host')
	,	'port' => $config->get('remote_port')
	,	'user' => $config->get('remote_user')
));

message('Updating git post-receive hooks');
$post_receive = file_get_contents(CLI_ROOT . DS . 'data' . DS . 'template' . DS . 'post-receive');
foreach ($config_repos->getInnerArray() as $name => $settings) {
	$file = str_replace('{{remote_root}}', $settings['remote_root'], $post_receive);
	$file = str_replace('{{production_branch}}', $settings['production_branch'], $file);
	# Update post-receive hook
	$ssh->command('echo -e \''.$ssh->encodeFileForCommand($file).'\' > '.$settings['remote_repo_root'].'/hooks/post-receive');
	# Make sure it's executable
	$ssh->command('chmod u+x '.$settings['remote_repo_root'].'/hooks/post-receive');
}

$result = $ssh->run();

line();

message(Color::blue('Pushing repositories'));

$process_git_response = function ($output, $status, $branch, $remote) {
	if (count($output) === 1 && strpos($output[0], 'Everything up') !== false) {
		message('Up to date, no changes pushed.');
		return true;
	}
	if (strpos(end($output), "{$branch} -> {$branch}") !== false) {
		message("Changes have been pushed.");
		return true;
	}
	error("Could not push repository to {$remote}, did you run `rbhp setup` yet?");
	exit;
};

foreach ($config_repos->getInnerArray() as $name => $settings) {
	message("Pushing repository `{$name}`");
	$shell_output = array();
	$status = null;
	chdir($settings['local_root']);
	exec("git push {$settings['remote_name']} {$settings['production_branch']}:{$settings['production_branch']} --force 2>&1", $shell_output, $status);
	$process_git_response($shell_output, $status, $settings['production_branch'], $settings['remote_name']);
}

message(color::blue('End push'));
line();

message(color::green('Deployment success.'));
message(color::light_gray('If you\'ve made any server configuration changes, use `rbhp reload` to reload your server.'));
