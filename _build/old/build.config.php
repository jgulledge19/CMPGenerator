<?php
// http://rtfm.modx.com/display/revolution20/Using+Custom+Database+Tables+in+your+3rd+Party+Components
//define('MODX_BASE_PATH', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . 'test/');

// Change the folder to match your CMP folder name
define('CMP_NAME', 'cmpgenerator');

define('MODX_BASE_PATH', dirname(dirname(__FILE__)).'/' );
define('MODX_CORE_PATH', MODX_BASE_PATH . 'core/');
define('MODX_MANAGER_PATH', MODX_BASE_PATH . 'manager/');
define('MODX_CONNECTORS_PATH', MODX_BASE_PATH . 'connectors/');
define('MODX_ASSETS_PATH', MODX_BASE_PATH . 'assets/');

// echo 'MODX_BASE_PATH: '.MODX_BASE_PATH;
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
    'root' => $root,
    'assets' => $root.'assets/components/'.CMP_NAME.'/',
    'build' => $root .'_build/',
    'core' => $root.'core/components/'.CMP_NAME.'/',
    'data' => $root . '_build/data/',
    'docs' => $root.'core/components/'.CMP_NAME.'/docs/',
    'lexicon' => $root . 'core/components/'.CMP_NAME.'/lexicon/',
    'model' => $root.'core/components/'.CMP_NAME.'/model/',
    'resolvers' => $root . '_build/resolvers/',
    'source_core' => $root.'core/components/'.CMP_NAME.'',
    'source_assets' => $root.'assets/components/'.CMP_NAME.'',
    'schema' => $root.'_build/schema/',
);
?>