<?php
/**
 * Figure out the root paths and store them as constants.
 */

define('ROOT', dirname(dirname(dirname(dirname(dirname(__DIR__))))));
define('CORE', ROOT.'/Vendor/rbhpi/core');
define('CORE_SRC', CORE.'/src');
