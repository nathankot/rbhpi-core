<?php
/**
 * @version 0.1.0
 */

namespace Core\Wireframe\Blueprint;

/**
 * The Model is a representation of a collection of data.
 * This representation should be moldable and mutable.
 */
interface Model extends \Iterator, \Countable
{
	public function init($query = []);
	public function find($query = []);
	public function findOne($query);
	public function remove($query, $options = []);
	public function removeOne($entry, $options = []);
	public function skip($int);
	public function limit($int);
	public function create($existing = []);
	public function save($entry = []);
}
