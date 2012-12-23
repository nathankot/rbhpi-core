<?php
/**
 * Reload remote server conf
 */

$config = new Config('reload');
$deploy_config = new Config('deploy');

/*
Allow user to go over settings
====================================== */
$config->confirm();

/*
Confirmed, lets run the reload script
====================================== */
$ssh = new Ssh(array(
		'host' => $deploy_config->get('remote_host')
	,	'port' => $deploy_config->get('remote_port')
	,	'user' => $deploy_config->get('remote_admin')
));

$ssh->interact();
$ssh->command("sudo {$config->get('remote_reload_script_program')} {$config->get('remote_reload_script')} {$config->get('remote_project_etc_root')}");
$output = $ssh->run();
