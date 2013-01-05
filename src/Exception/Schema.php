<?php

namespace Exception;

class Schema extends \UnexpectedValueException
{
	/**
	 * The breakdown of the schema error, containing Filters that failed for
	 * each key.
	 * @var array
	 */
	private $breakdown;

	/**
	 * Get the breakdown.
	 * @return array Breakdown of errors.
	 */
	public function getBreakdown()
	{
		return $this->breakdown;
	}

	/**
	 * Set the breakdown.
	 * @param array $breakdown Breakdown of errors.
	 */
	public function setBreakdown($breakdown)
	{
		$this->breakdown = $breakdown;
		return $this;
	}
}
