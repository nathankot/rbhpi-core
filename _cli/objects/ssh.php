<?php

/**
 * Allows simple ssh interaction via command chaining.
 */
class ssh
{
	private $first_command = false;
	private $command;
	private $config;
	private $interact = false;

	public function __construct($config)
	{
		$this->config = $config;
		$this->command = 'ssh ';
		$this->command .= '-t ';
		if(isset($config['port'])) {
			$this->command .= "-p {$config['port']} ";
		}
		$this->command .= "{$config['user']}";
		if (isset($config['password'])) {
			$this->command .= ":{$config['password']}";
		}
		$this->command .= '@' . $config['host'] . ' ';
		message("Connecting to {$this->config['user']}@{$this->config['host']}");
	}

	public function interact($bool = true)
	{
		$this->interact = (boolean)$bool;
	}

	public function command($command)
	{
		if (!$this->first_command) {
			$this->command .= '"';
			$this->first_command = true;
		} else {
			$this->command .= '&& ';
		}
		$this->command .= $command . ' ';
	}

	public function run()
	{
		$this->command .= '" 2>&1';
		if ($this->interact) {
			system($this->command, $status);
		} else {
			exec($this->command, $output, $status);
		}
		if ($status !== 0) {
			error("Error when executing command via SSH");
		}

		message('Connection closed.');

		if (isset($output)) {
			return $output;
		} else {
			return true;
		}
	}

	public function encodeFileForCommand($contents)
	{
		return str_replace("\n", '\n', $contents);
	}
}
