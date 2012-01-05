<?php
/**
 * @package doodle
 * @subpackage processors
 */

/* get obj */
if ( empty($scriptProperties['id']) ) {
    return $modx->error->failure($modx->lexicon('cmpgenerator.err_nf'));
}

$cmp = $modx->getObject('CmpGenerator',$scriptProperties['id']);
if (empty($cmp)){
    return $modx->error->failure($modx->lexicon('cmpgenerator.err_nf'));
}

// 1 get the CMP Package
$package_name = $cmp->get('package');
// 2 delete all files:
function rmdir_files($dir) {
    foreach( glob( $dir . '*', GLOB_MARK ) as $file) {
        if (is_dir($file)) {
            rmdir_files($file."/");
            rmdir($file);
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
foreach ($directories as $folder) {
    if ( !file_exists($folder) ) {
        continue;
    } else {
        // delete the files and sub folders:
        rmdir_files($folder);
    }
}


// 3. delete the database row record:
/* remove */
if ($cmp->remove() == false) {
    return $modx->error->failure($modx->lexicon('cmpgenerator.err_remove'));
}


return $modx->error->success('',$cmp); 