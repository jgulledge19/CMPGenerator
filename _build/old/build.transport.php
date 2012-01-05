<?php
// http://rtfm.modx.com/display/revolution20/Transport+Packages
// http://rtfm.modx.com/display/revolution20/Creating+a+3rd+Party+Component+Build+Script
/**
 * Quip build script
 *
 * @package quip
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0); /* makes sure our script doesnt timeout */

$root = dirname(dirname(__FILE__)).'/';
$sources= array (
    'root' => $root,
    'build' => $root .'_build/',
    'resolvers' => $root . '_build/resolvers/',// ??
    'data' => $root . '_build/data/', // ??
    'source_core' => $root.'core/components/churchevents/',
    'lexicon' => $root . 'core/components/churchevents/lexicon/',
    'source_assets' => $root.'assets/components/churchevents/',
    'docs' => $root.'core/components/churchevents/docs/',
);
unset($root); /* save memory */

require_once dirname(__FILE__) . '/build.config.php';  

// The MODx class:
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
/* Okay, it's time for the meat. Let's first off use $modx->loadClass to load the modPackageBuilder class. Then we'll instantiate an instance of it, and create a package.
*/
$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage('churchevents','0.3','beta2'); //name, version, and release
$builder->registerNamespace('churchevents',false,true,'{core_path}components/churchevents/');

// create system settings:
$settings = array();
$settings['churchevents.allowRequests']= $modx->newObject('modSystemSetting');
$settings['churchevents.allowRequests']->fromArray(array(
    'key' => 'churchevents.allowRequests',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'churchevents',
    'area' => 'ChurchEvents',
),'',true,true);
$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'key',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => false,
);
if (!is_array($settings)) {
	$modx->log(modX::LOG_LEVEL_FATAL,'Adding settings failed.');
}
foreach ($settings as $setting) {
    $vehicle = $builder->createVehicle($setting,$attributes);
    $builder->putVehicle($vehicle);
}
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($settings).' system settings.'); flush();
unset($settings,$setting,$attributes);


// Packaging in Objects
/* create category for orginization: */
$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category','ChurchEvents');

/*
	1. Pack Snippet
So, let's look at some examples for creating a vehicle before digging into our build script. 
This first example packages in a simple object, with some parameters:
*/
/*
	THIS is was on the example, not sure why you don't just query it?
	$snippet = $modx->newObject('modSnippet');
	$snippet->set('id',0);
	$snippet->set('name','churchEvents');
*/
$snippet = array();
$snippet[1] = $modx->getObject('modSnippet', array('id' => 13));
/*
	$vehicle = $builder->createVehicle($snippet,array(
		xPDOTransport::UNIQUE_KEY => 'name',
		xPDOTransport::UPDATE_OBJECT => false,
		xPDOTransport::PRESERVE_KEYS => false,
	));
*/
//$snippet[1]->set('id', 0);
/*
$snippet[1] = $modx->newObject('modSnippet');
$snippet[1]->fromArray(array(
    'id' => 13,
    'name' => 'churchevents',
    'description' => 'Church Events Calendar',
    'snippet' => $ch_snippet->get('snippet')//getSnippetContent($sources['source_core'].'/elements/snippets/snippet.quipcount.php'),
));
*/
$category->addMany($snippet);

/*
	Template:
*/
$template = array();
$template[1] = $modx->getObject('modTemplate', array('id' => 4));
/*$template[1] = $modx->newObject('modTemplate');
$template[1]->fromArray(array(
    'id' => 1,
    'templatename' => 'Church Events Calendar',
    'description' => 'A basic template with the calendar built into it.',
    'content' => $ch_template->get('content')//file_get_contents($sources['data'].'templates/template.modxhost.tpl'),
));
*/
// echo print_r($template[1]);
//$template[1]->set('id', 0);
$category->addMany($template);

/* load into category vehicle */
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),/*
        'Chunks' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true,
        ),*/
        'Templates' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'templatename',
        ),/*
        'TemplateVars' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true,
        ),*/
    )
);
$vehicle = $builder->createVehicle($category,$attr);


/*
	2. Files 
*/
$vehicle->resolve('file',array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'] . 'setupoptions.resolver.php',
));
// install the tables:
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'] . 'tables.resolver.php',
));
$builder->putVehicle($vehicle);

/*
	3. Data - table rows
*/
// This will add the package to xPDO, and allow you to use all of xPDO's functions with your model. 
$modx->addPackage('churchevents', $sources['model']);

// Get the Calendar Options:
$cals = $modx->getCollection('chCalendar');

$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'id',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => false,
);
foreach ( $cals as $chCalendar ) {
	$vehicle = $builder->createVehicle($chCalendar,$attributes);
	$builder->putVehicle($vehicle);
}
// Get the Category Options: 
$cats = $modx->getCollection('chECategory');
foreach ( $cats as $chECategory ) {
	$vehicle = $builder->createVehicle($chECategory,$attributes);
	$builder->putVehicle($vehicle);
}


/* load lexicon strings */
$builder->buildLexicon($sources['lexicon']);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'setup-options' => array(
        'source' => $sources['build'].'setup.options.php',
    ),
));

$builder->pack();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(MODX_LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

exit (); 


?>