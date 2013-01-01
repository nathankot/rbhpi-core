<?php
/**
 * @version 0.1.0
 */

namespace Core\Blueprint;

/**
 * Takes data, and can render it into a variety of views.
 */
abstract class View extends \Core\Blueprint\Object implements
	\Core\Wireframe\Blueprint\View
{
	use \Core\Augmentation\Adaptable;

	/**
	 * The configuration for this view.
	 * @var array
	 */
	protected static $config = [];

	/**
	 * The data that it works with.
	 * @var mixed
	 */
	public $data;

	/**
	 * The template name to use.
	 * @var string
	 */
	protected $template;

	/**
	 * Preconfiguration of the View class. (These are the default adapters)
	 * @return void
	 */
	public static function preConfig()
	{
		self::config([
				'mustache_escape' => function($text) {
					return htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
				}
			,	'mustache_helpers' => []
		]);

		self::adapt('toJSON', function($self, $args) {
			return json_encode($self->getData());
		});

		self::adapt('toHTML', function($self, $args) {
			$mustache = new \Mustache_Engine([
					'cache' => ROOT.'/_tmp'
				,	'loader' => new \Core\Prototype\MustacheLoader(ROOT.'/App/Template/')
				,	'partials_loader' => new \Core\Prototype\MustacheLoader(ROOT.'/App/Template/Partial/')
				,	'escape' => self::$config['mustache_escape']
				,	'helpers' => self::$config['mustache_helpers']
			]);
			$template = $mustache->loadTemplate($self->getTemplate());
			return $template->render($self);
		});
	}

	/**
	 * Constructor for the Object.
	 * @param  array $data The data for the View object to use.
	 * @return void
	 */
	final public function init($data, $template = null)
	{
		self::config();
		$this->data = $data;
		if ($template !== null) {
			$this->template = $template;
		}
	}

	/**
	 * Use a different template.
	 * @param string $template Name of the template.
	 */
	final public function setTemplate($template)
	{
		$this->template = $template;
	}

	/**
	 * Get the template that the view is using.
	 * @return string The template.
	 */
	final public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * Getter for the Data that the View is using.
	 * @return array Encapsulated data.
	 */
	final public function getData()
	{
		return $this->data;
	}

	/**
	 * Return the data formatted as JSON.
	 * @return string JSON formatted string.
	 */
	final public function toJSON()
	{
		return $this->useAdapter(__FUNCTION__, func_get_args());
	}

	/**
	 * Return the data as HTML.
	 * @return string HTML formatted string.
	 */
	final public function toHTML()
	{
		return $this->useAdapter(__FUNCTION__, func_get_args());
	}
}
