<?php
/**
 * @version 0.1.0
 */

namespace Core\Blueprint;

/**
 * The Model is a representation of a collection of data.
 * This representation is moldable and mutable.
 */
class Model extends \Core\Blueprint\Object implements
	\Core\Wireframe\Blueprint\Model
{

	use \Core\Augmentation\Adaptable, \Core\Augmentation\RBHPSchema;

	/**
	 * Model Class-wide Configuration.
	 * @var array
	 */
	public static $config = [
			'connection_settings' => null
		,	'established_connection'
	];

	/**
	 * The name of the collection.
	 * @var string
	 */
	public $name;

	/**
	 * The Schema of the collection, given in RBHPi Schema format.
	 * @var array
	 */
	public $schema;

	/**
	 * This property **may** be used by adapters to hold the connection to the
	 * current collection.
	 * @var mixed
	 */
	public $collection;

	/**
	 * The priority level of this collection. 1 being minimum priority, 3 being maximum priority.
	 * Depending on this level, data should be treated differently. Either optimizing for speed
	 * by sacrificing reliability, or vice versa.
	 * @var integer
	 */
	public $priority = 2;

	/**
	 * This holds the last query executed with `self::find()`, and thus
	 * represents the mutated state of the current object's data.
	 * @var array
	 */
	public $last_query = [];

	/**
	 * Run sanity checks, and execute the initial query if it is given.
	 * @param  array $query An initial query to mold this Model Object.
	 * @return void
	 */
	final public function init($query = [])
	{
		$model_class = __CLASS__;
		# Sanity check.
		if (!$this->name) {
			throw new \Exception\BadModel("The model {$model_class} does not have a name!");
		}
		if (!$this->schema) {
			throw new \Exception\BadModel("The model {$model_class} does not have a schema!");
		}
		# //end
		$this->connect(self::$config['connection_settings']);
		if (!empty($query)) {
			$this->find($query);
		}
	}

	/**
	 * Connect to the relevant DB by using the adapter.
	 */
	private function connect($settings)
	{
		return $this->useAdapter(__FUNCTION__, func_get_args());
	}

	/**
	 * Holds the encapsulated data. This can be an object or array depending on
	 * the adapter that we are using.
	 * @var mixed
	 */
	private $data; /* The encapsulated data. */

	/**
	 * Use the adapter to find the relevant data entries according to the query.
	 * @param  array $query A search query.
	 * @return self        Returns a mutated instance of itself.
	 */
	final public function find($query = [])
	{
		$result = $this->useAdapter('find', [$query]);
		$this->data = $result;
		return $this;
	}

	/**
	 * Use the adapter to return one entry out of the current mutated data
	 * instance that matches the given query.
	 * @param  array $query Search Query.
	 * @return array        An entry.
	 */
	public function findOne($query)
	{
		return $this->useAdapter(__FUNCTION__, func_get_args());
	}

	public function remove($query, $options = [])
	{
		return $this->useAdapter(__FUNCTION__, func_get_args());
	}

	public function removeOne($entry, $options = [])
	{
		return $this->useAdapter(__FUNCTION__, func_get_args());
	}

	public function skip($int)
	{
		$this->data = $this->useAdapter(__FUNCTION__, func_get_args());
	}

	public function limit($int)
	{
		$this->data = $this->useAdapter(__FUNCTION__, func_get_args());
	}

	public function create($entry = [])
	{
		$default = $this->createEntryFromSchema($this->schema);
		return extend($default, $entry);
	}

	public function save($entry = [], $options = [])
	{
		$errors = $this->checkEntryAgainstSchema($entry, $this->schema);
		if (is_array($errors)) {
			$e = new \Exception\Schema
				("Bad Entry-Schema match in model {$this->name}");
			$e->setBreakdown($errors);
			throw $e;
			return false;
		}
		return $this->useAdapter('save', [$entry, $options]);
	}

	/**
	 * Helper for finding the best \Iterator method of the proxied object.
	 * @param  string $method_name
	 * @return mixed
	 */
	private function bestMethod($method_name)
	{
		if (method_exists($this->data, $method_name)) {
			return $this->data->$method_name();
		} else {
			return call_user_func_array($method_name, [$this->data]);
		}
	}

	# Implemented from \Iterator
	public function current()
	{
		return $this->bestMethod('current');
	}

	public function key()
	{
		return $this->bestMethod('key');
	}

	public function next()
	{
		return $this->bestMethod('next');
	}

	public function rewind()
	{
		return $this->bestMethod('rewind');
	}

	public function valid()
	{
		return $this->bestMethod('valid');
	}

	# Implemented from \Countable
	public function count()
	{
		return $this->bestMethod('count');
	}
}
