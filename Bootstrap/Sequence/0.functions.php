<?php
/**
 * Defines some custom functions here.
 * @version 0.2.0
 */

/**
 * jQuery-style extend.
 * @return array $extended
 * @see  http://bit.ly/S2FEWr
 **/
function extend($a, $b) {
		foreach($b as $k=>$v) {
				if( is_array($v) ) {
						if( !isset($a[$k]) ) {
								$a[$k] = $v;
						} else {
								$a[$k] = extend($a[$k], $v);
						}
				} else {
						$a[$k] = $v;
				}
		}
		return $a;
}

/**
 * Flattens an array.
 * @param  Array $array
 * @return Array
 */
function array_flatten($array) {
	if (!is_array($array)) {
    return array($array);
	}
	$result = array();
	foreach ($array as $value) {
    $result = array_merge($result, array_flatten($value));
	}
	return $result;
}
