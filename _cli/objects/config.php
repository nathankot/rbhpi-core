<?php
/**
 * Config is a class used by the CLI for interfacing with different JSON config files.
 *
 * @author Nathan Kot <nk@nathankot.com>
 * @version 0.1.1
 */

class config
{
	private $current_config;
	private $file;
	private $file_contents;
	private $config;

	/**
	 * Creates the object
	 * @param string $file Name of config file
	 * @ignore
	 */
	public function __construct($name)
	{
		$this->current_config = trim(file_get_contents(CLI_ROOT.DS.'config'.DS.'current_config'), "\n");
		$this->file = CLI_ROOT . DS . 'config' . DS . $this->current_config . DS . $name . '.json';
		$this->file_contents = is_readable($this->file) ? file_get_contents($this->file) : '{}';
		if ($this->file_contents == '{}' && is_readable($this->file.'.default')) {
			error("Configuration {$this->file} doesn't exist, creating from default.");
			$this->file_contents = file_get_contents($this->file.'.default');
		}
		$this->config = json_decode($this->file_contents, true);
	}

	public function getInnerArray()
	{
		return $this->config;
	}

	public function current_config()
	{
		return $this->current_config;
	}

	/**
	 * Retrieve the value of a configuration
	 * @param  string $name Name of configuration.
	 * @return string       Corresponding value
	 */
	public function get($name)
	{
		return $this->config[$name];
	}

	/**
	 * Set the value of a configuration
	 * @param string $name  Name of configuration
	 * @param string $value The new value
	 */
	public function set($name, $value)
	{
		$this->config[$name] = $value;
	}

	/**
	 * Remove a configuration item
	 * @param  string $name Name of configuration
	 */
	public function remove($name)
	{
		unset($this->config[$name]);
	}

	/**
	 * Display configurations, allow user to confirm or change them
	 * @return boolean
	 */
	public function confirm()
	{
		message("Using configuration set `{$this->current_config()}`");
		message('Please confirm the following configuration.');

		$this->listAll();
		line();

		$message = 'Type ';
		$message .= color::bold('yes');
		$message .= ' to confirm and continue, ';
		$message .= color::bold('no');
		$message .= ' to abort, and ';
		$message .= color::bold('change');
		$message .= ' to adjust the settings';
		message($message);

		$stdin = request_feedback();

		if ($stdin === 'yes') {
			return true;
		}

		if ($stdin === 'change') {
			$this->adjustAll();
			return $this->confirm();
		}

		abort();
	}

	/**
	 * Interactively adjust each configuration item
	 * @return void Interactive
	 */
	public function adjustAll()
	{
		$this->adjust();
	}

	/**
	 * Interactively adjust each configuration item given in the array
	 * @param  array $array Items to adjust
	 * @return void        Exits on fail
	 */
	public function adjust($keys = false)
	{

		$array = is_array($keys) ? array_intersect_key($this->config, array_flip($keys)) : $this->config;

		if (empty($this->config) || empty($array)) {
			error('No configuration specified');
			exit;
			return;
		}

		$adjust = function (array &$array, $prefix) use (&$adjust) {
			foreach ($array as $name => &$value) {
				if(is_array($value)) {
					$adjust($value, $prefix.$name.':');
					continue;
				}
				message('Setting: ' . $prefix . color::bold($name) . ' / ' . "Current value: ".color::green($value));
				message('Type a new value, or press enter to skip.');
				$feedback = request_feedback();
				if (!empty($feedback)) {
					$value = $feedback;
				}
			}
		};

		$adjust($array, '');
		$this->config = array_merge($this->config, $array);
	}

	/**
	 * List every configuration item and its value.
	 * @return void Prints it
	 */
	public function listAll($config = false)
	{
		$config = $config ?: $this->config;
		if (empty($config)) {
			error('No configuration specified');
			exit;
			return;
		}
		$list = function (array $array, $indent_level) use (&$list) {
			foreach ($array as $name => $value) {
				if(is_array($value)) {
					message(color::bold("{$name}"));
					$list($value, $indent_level + 1);
					continue;
				}
				$indents = '';
				for ($x=0; $x < $indent_level; $x++) {
					$indents .= "\t";
				}
				message($indents.color::bold("{$name}") . ": {$value}");
			}
		};
		$list($config, 0);
	}

	/**
	 * List every configuration item & value in the given array
	 * @param  array $array Config names to list
	 * @return void        Exits on error
	 */
	public function listConfig($array)
	{
		$array = array_intersect_key($this->config, array_flip($array));
		$this->listAll($array);
	}

	/**
	 * Writes new config to file on end.
	 * @ignore
	 */
	public function __destruct()
	{
		$this->file_contents = $this->prettyJSON(json_encode($this->config));
		if (!empty($this->file_contents) && $this->file_contents != 'null') {
			$file_handle = fopen($this->file, 'w+');
			fwrite($file_handle, $this->file_contents);
		}
	}

	/**
	 * Pretty-formats json so that config files can be
	 * easier read
	 * @param  string $string json string
	 * @return string         pretty json string
	 */
	private function prettyJSON($string)
	{
	 $pattern = array(',"', '{', '}');
	 $replacement = array(",\n\t\"", "{\n\t", "\n}");
	 return str_replace($pattern, $replacement, $string);
	}
}
