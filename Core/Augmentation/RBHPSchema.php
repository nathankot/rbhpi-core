<?php

namespace Core\Augmentation;

use \Core\Merchant\Filter;

/**
 * Provides helpers to handle RBHP Schema's
 */
trait RBHPSchema
{
	/**
	 * Space-delimited list of filters that are interpreted as component 'type'
	 */
	private $_types = 'integer string array float boolean';

	/**
	 * If this is present, the string behind this delimiter is construed as the
	 * default value.
	 */
	private $_default_delimiter = '?:';

	/**
	 * Create an empty entry using the structure and default values of the given
	 * Schema.
	 * @param  array $schema Given Schema.
	 * @return array         Empty Entry.
	 */
	private function createEntryFromSchema($schema)
	{
		array_walk_recursive($schema, function (&$value) {
			$component = $this->parseSchemaComponent($value);
			$value = $component['default'] ?: null;
			if ($component['type'] != 'mixed') {
				settype($value, $component['type']);
			}
		});
		return $schema;
	}

	/**
	 * Recursive function that goes over each Schema item, making sure that
	 * the corresponding entry item conforms to it.
	 * @param  array $entry  Entry item.
	 * @param  array $schema Schema item.
	 * @return mixed         TRUE if everything is okay, otherwise an array
	 *                       of errors.
	 */
	private function checkEntryAgainstSchema($entry, $schema)
	{
		$result = [];
		foreach ($schema as $key => $ruleset) {
			$current_errors = [];
			$current_value = $entry[$key];

			if (is_array($current_value)) {
				$current_errors = $this->checkEntryAgainstSchema($current_value, $ruleset);
			} else {
				$rule_components = $this->parseSchemaComponent($ruleset);
				foreach ($rule_components['filters'] as $filter) {
					if (
						($current_value === null || $current_value === '') &&
			 			$filter !== 'required'
					) {
						continue;
					}
					if (!Filter::$filter($current_value)) {
						$current_errors[] = $filter;
					}
				}
			}

			if (is_array($current_errors) && !empty($current_errors)) {
				$result[$key] = $current_errors;
			}
		}

		$result = empty($result) ? true : $result;
		return $result;
	}

	/**
	 * Take a string in schema component format, and returns the type
	 * of the entry and the filters that need to be applied to it.
	 * @param  string $component Schema component
	 * @return array            Array with ['type'], ['default'] and ['filters']
	 */
	private function parseSchemaComponent($component)
	{
		$return = array();

		$parts = explode($this->_default_delimiter, $component);
		if (count($parts) > 1) {
			$return['default'] = $parts[1];
		}

		$parts = explode(':', $parts[0]);
		if (strpos($this->_types, strtolower($parts[0])) !== false) {
			$return['type'] = $parts[0];
		} else {
			$return['type'] = null;
		}

		$return['filters'] = (array)$parts;

		return $return;
	}
}
