<?php
/**
 * This snippet simply installs the db stuff
 */
$package_name = '';
// add package
$s_path = $modx->getOption('core_path').'components/'.$package_name.'/model/';
$modx->addPackage($package_name, $s_path);

$m = $modx->getManager();

// the class table object name(s)
$m->createObjectContainer('jgSearchTerms');
$m->createObjectContainer('jgSearchCount');
$m->createObjectContainer('jgSearchSuggestions');

return 'Tables created.';