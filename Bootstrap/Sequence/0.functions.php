<?php
/**
 * Defines some custom functions here.
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
