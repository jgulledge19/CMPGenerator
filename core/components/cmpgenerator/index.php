<?php
/**
 * This file is calls on the controller if you mapped your contorller to this file
 * rather then the controllers/index.php file
 */
$output = include dirname(__FILE__).'/controllers/index.php';
return $output;