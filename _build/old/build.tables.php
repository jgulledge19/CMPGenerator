<?php
/**
 * Build Schema script
 *
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

require_once dirname(__FILE__) . '/build.config.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php'; 
$modx= new modX();
$modx->initialize('mgr');

$modx->loadClass('transport.modPackageBuilder','',false, true);
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

// load xPDOManager and xPDOGenerator, two classes we'll need to build our schema map files.
$manager= $modx->getManager();

// Now create the DB tables:
// add package 
$modx->addPackage(CMP_NAME, $sources['model']);

// the class table object name
$manager->createObjectContainer('CmpGenerator');

echo 'Table created.';

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

echo "\nExecution time: {$totalTime}\n";

exit ();

?>