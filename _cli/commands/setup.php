<?php

message(Color::white('Setup', 'blue'));
message('This script will setup your server so that it is deployment ready. However, deployment relys on SSH keys and git so please make sure that you have key auth and git setup.');
line();

$config = new Config('deploy');
$config_repos = new Config('repos');

/*
Confirm config
====================================== */
$config->confirm();
$config_repos->confirm();

# Only get as array after all the conf is done.
$settings_repos = $config_repos->getInnerArray();

message(Color::bold('What is going to happen:'));
foreach ($settings_repos as $name => $repo) {
	message("Create a bare git repo at {$repo['remote_repo_root']}");
	message("Create an empty directory at {$repo['remote_root']}");
	message("Add a remote called `{$repo['remote_name']}` to your `{$name}` repo.");
}

line();
message('Type '.Color::bold('yes').' to proceed.');
if (request_feedback() !== 'yes') {
	abort();
}

$ssh = new Ssh(array(
		'host' => $config->get('remote_host')
	,	'port' => $config->get('remote_port')
	,	'user' => $config->get('remote_user')
));

foreach ($settings_repos as $name => $repo) {
	$ssh->command('mkdir -p '.$repo['remote_repo_root']);
	$ssh->command("cd {$repo['remote_repo_root']}");
	$ssh->command("git init --bare");
	$ssh->command("mkdir -p {$repo['remote_root']}");
	$shell_output = array();
	chdir($repo['local_root']);
	$remote_url = "ssh://{$config->get('remote_user')}@{$config->get('remote_host')}:{$config->get('remote_port')}/{$repo['remote_repo_root']}";
	exec("git remote add {$repo['remote_name']} {$remote_url} 2>&1", $shell_output);
}

message('Running setup...');
$ssh->run();
