<?php 
/**
 * This file will generate/build all schema files for a package
 */
 // From Grid
/* parse JSON */
/*
if (empty($scriptProperties['data'])) return $modx->error->failure('Invalid data.');
$_DATA = $modx->fromJSON($scriptProperties['data']);
if (!is_array($_DATA)) return $modx->error->failure('Invalid data.');
*/

// required fields
if (empty($scriptProperties['package']) ) {
    $modx->error->addField('package',$modx->lexicon('cmpgenerator.err_required'));
}
if (empty($scriptProperties['tables']) ) {
    $modx->error->addField('tables',$modx->lexicon('cmpgenerator.err_required'));
}
if (empty($scriptProperties['database']) && empty($scriptProperties['table_prefix']) ) {
    $modx->error->addField('table_prefix',$modx->lexicon('cmpgenerator.err_required'));
}
if ($modx->error->hasError()) {
    return $modx->error->failure();
}
/* package, tables, table_classes - auto generated, prefix, build_scheme, build_package */
$package_name = $scriptProperties['package'];
$my_tables = explode(',', $scriptProperties['tables']);
// If your tables use a prefix, this will help identify them and it ensures that
// the class names appear "clean", without the prefix.
$table_prefix = $scriptProperties['table_prefix']; //$modx->config['table_prefix'];
// If you specify a table prefix, you probably want this set to 'true'. E.g. if you
// have custom tables alongside the modx_xxx tables, restricting the prefix ensures
// that you only generate classes/maps for the tables identified by the $table_prefix.
$restrict_prefix = true;

if ( empty($scriptProperties['id']) || $scriptProperties['id'] < 1  ) {
    // create
    $cmp = $modx->newObject('CmpGenerator');
    $scriptProperties['create_date'] = strftime('%Y-%m-%d %H:%M:%S');
    
} else {
    // it exists
    $cmp = $modx->getObject('CmpGenerator',$scriptProperties['id']);
    unset($scriptProperties['create_date']);
    unset($scriptProperties['last_ran']);
    
}
$scriptProperties['last_ran'] = strftime('%Y-%m-%d %H:%M:%S');
if (!is_object($cmp) ) {
    return $modx->error->failure('Not an object id: '.$scriptProperties['id'] );
}
$cmp->fromArray($scriptProperties);

$scheme = $cmp->get('build_scheme');
/* set fields */
if ( $scheme == 'Yes' || $scriptProperties['build_scheme'] == $modx->lexicon('yes') ) {
    $cmp->set('build_scheme', '1');
} else {
    $cmp->set('build_scheme', '0');
}
if ( $cmp->get('build_package') == 'Yes' || $scriptProperties['build_package'] == $modx->lexicon('yes') ) {
    $cmp->set('build_package', '1');
} else {
    $cmp->set('build_package', '0');
}

// echo 'Scheme: '.$cmp->get('build_scheme').'  ';
//return $modx->error->failure('build_scheme: '.$scriptProperties['build_scheme'].' - '.$cmp->get('build_scheme') );

/* save */
if ($cmp->save() == false) {
    return $modx->error->failure($modx->lexicon('cmpgenerator.err_save').' ID: '.$scriptProperties['id']);
}

// 1 delete all files for testing:
function rmdir_files($dir) {
    foreach( glob( $dir . '*', GLOB_MARK ) as $file) {
        if (is_dir($file)) {
            rmdir_files($file."/");
            if( is_dir($file) ) {
                rmdir($file);
            }
        } elseif( is_file($file) )  {
            unlink($file);
        }
    }
    if (is_dir($dir) ){
        if( rmdir( $dir ) ){
            return true;
        }
        return false;
    }
}
$directories = array(
    'assets' => MODX_ASSETS_PATH.'components/'.$package_name.'/',
    'code_base' => MODX_CORE_PATH.'components/'.$package_name.'/'
);
// Delete the directory folders:
if ( 1==2 ) {
    foreach ($directories as $folder) {
        if ( !file_exists($folder) ) {
            continue;
        } else {
            // delete the files and sub folders:
            rmdir_files($folder);
        }
    }
}
// create new files:
$directories = array(
    'assets' => MODX_ASSETS_PATH.'components/'.$package_name.'/',
        'assets_js' => MODX_ASSETS_PATH.'components/'.$package_name.'/js/',
            'assets_mgr' => MODX_ASSETS_PATH.'components/'.$package_name.'/js/mgr/',
                'assets_sections' => MODX_ASSETS_PATH.'components/'.$package_name.'/js/mgr/sections/',
                'assets_widgets' => MODX_ASSETS_PATH.'components/'.$package_name.'/js/mgr/widgets/',
    'code_base' => MODX_CORE_PATH.'components/'.$package_name.'/',
        'controllers' => MODX_CORE_PATH.'components/'.$package_name.'/controllers/',
            'controllers_mgr' => MODX_CORE_PATH.'components/'.$package_name.'/controllers/mgr/',
        'docs' => MODX_CORE_PATH.'components/'.$package_name.'/docs/',
        'elements' => MODX_CORE_PATH.'components/'.$package_name.'/elements/',
            'chunks' => MODX_CORE_PATH.'components/'.$package_name.'/elements/chunks/',
            'plugins' => MODX_CORE_PATH.'components/'.$package_name.'/elements/plugins/',
            'snippets' => MODX_CORE_PATH.'components/'.$package_name.'/elements/snippets/',
            'templates' => MODX_CORE_PATH.'components/'.$package_name.'/elements/templates/',
        'lexicon' => MODX_CORE_PATH.'components/'.$package_name.'/lexicon/',
            'en' => MODX_CORE_PATH.'components/'.$package_name.'/lexicon/en/',
        'model' => MODX_CORE_PATH.'components/'.$package_name.'/model/',
            'my_model' => MODX_CORE_PATH.'components/'.$package_name.'/model/'.$package_name.'/',
                'mysql' => MODX_CORE_PATH.'components/'.$package_name.'/model/'.$package_name.'/mysql',
            'request' => MODX_CORE_PATH.'components/'.$package_name.'/model/request/',
        'processors' => MODX_CORE_PATH.'components/'.$package_name.'/processors/',
            'processors_mgr' => MODX_CORE_PATH.'components/'.$package_name.'/processors/mgr/',
                // each table?
);

// now create directories if they do not exist
foreach ($directories as $folder) {
    if ( !file_exists($folder) ) {
        if ( !mkdir($folder) ) {
            return false;
        }
    }
    if ( !is_writable($folder) ) {
        return false;
    }
}

$manager = $modx->getManager();

$loaded = include_once(MODX_CORE_PATH . 'components/cmpgenerator/model/cmpgenerator/' . $modx->config['dbtype'] . '/my_xpdogenerator.class.php');


if ($loaded) {
    $generatorClass = 'my_xPDOGenerator_' . $modx->config['dbtype'];
    $generator = new $generatorClass ($manager);
    
    // set the allowed tables:
    $generator->setAllowedTables($my_tables);
    
        
    $xml_schema_file = $directories['my_model'].$package_name.'.mysql.schema.xml';
    // (re)Build the schema file
    // echo 'Scheme: '.$cmp->get('build_scheme');
    if ( $cmp->get('build_scheme') ) {
        // set the db:
        $dbname = $cmp->get('database');
        $generator->setDatabase($dbname);
        $prefix = $cmp->get('table_prefix');
        if ( !empty($dbname) && empty($prefix) ) {
            $restrict_prefix = false;
        }
        // now generate the scheme
        $xml = $generator->writeTableSchema($xml_schema_file, $package_name, 'xPDOObject', $table_prefix, $restrict_prefix);
    }
    // (re)Build the table classes(package):
    if ( $cmp->get('build_package') ) { // package
        // delete any old/current class and map keys:
        rmdir_files($directories['mysql']);
        // now create the class and map keys:
        $generator->parseSchema($xml_schema_file, $directories['model']);
    }
    // (re)Build the table classes:
    if ( $cmp->get('build_code') ) { // package
        // now generate base code:
        
    }
    
}
// create lexicon file:

// now generate and custimize php and js files:
if ( $cmp->get('build_package') ) {
    
}
/**
 * See http://rtfm.modx.com/display/revolution20/Custom+Manager+Pages+Tutorial for more help
    a. Create Namespace:  System->Namespace
        Click Create New and then fill exactly for Name: slideshowmanager 
        and for Path: {core_path}components/slideshowmanager/
    b. Create the Action
        System->Actions
        Right-click slideshowmanager from the list of namespaces and select "Create Action Here".
        Controller: controllers/index
        Namespace: yes, use the same namespace: slideshowmanager
        Check Load Headers
        Language Topics: slideshowmanager:default
        Now click save
    c. Create the Menu Object
        Right-Click "Components" and choose "Place Action Here"
        Lexicon Key: slideshowmanager
        Description: slideshowmanager.desc
        Action: slideshowmanager.desc - index
        Save (you can ignore the Icon, Parameters, Handler, and Permissions fields for now)
 */
return $modx->error->success('',$cmp);
 