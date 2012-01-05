<?php
/**
 * Loads the header for mgr pages.
 *
 * @package cmpController
 * @subpackage controllers
 * 
 * No need to edit this file
 */

$modx->regClientStartupScript($cmpController->config['jsUrl'].'mgr/'.$cmpController->config['packageName'].'.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    Cmp'./*ucfirst($cmpController->config['packageName']).*/'.config = '.$modx->toJSON($cmpController->config).';
});
</script>');

return '';

/* ORG Code: 
$modx->regClientStartupScript($doodles->config['jsUrl'].'mgr/doodles.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    Doodles.config = '.$modx->toJSON($doodles->config).';
});
</script>');


return '';
 */