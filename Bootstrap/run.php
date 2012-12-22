<?php
/**
 * Includes all files in `./Sequence` in ascending natural sort order.
 */

foreach (glob(__DIR__.'/Sequence/*.php') as $file) {
	require_once $file;
}
