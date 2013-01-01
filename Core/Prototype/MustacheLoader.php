<?php
/**
 * @version 0.1.0
 */

namespace Core\Prototype;

/**
 * Custom loader for Mustache template files.
 * @todo Caching
 */
class MustacheLoader implements \Mustache_Loader
{
	/**
	 * Base Directory
	 * @var string
	 */
	private $base_dir;

	/**
	 * Constructor that stores the Base Directory.
	 * @param string $base_dir Path/To/Base/Dir
	 */
	public function __construct($base_dir = null)
	{
		$this->base_dir = ltrim($base_dir,'/').'/';
	}

	/**
	 * Load the file contents of the given template name.
	 * @param  string $template_name Template name.
	 * @return string                File contents.
	 */
	public function load($template_name)
	{
		$template_files = [
				$template_name
			,	$this->base_dir.$template_name
			,	$this->base_dir.$template_name.'.mustache'
			, $this->base_dir.$template_name.'.php'
			,	ROOT.'/Core/Core/Test/Mock/'.$template_name.'.mustache' # Used for Tests
		];

		$template_files = array_filter($template_files, function($value) {
			return is_readable($value);
		});

		if (empty($template_files)) {
			throw new \Exception\BadTemplate("The template named `{$template_name}` cannot be found!");
		}

		return file_get_contents(current($template_files));
	}
}
