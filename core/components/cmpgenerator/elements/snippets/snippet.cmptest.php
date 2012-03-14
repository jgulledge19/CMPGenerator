<?php
/**
 * Show a list of tables for a given table (assuming your DB user has access)
 */
$output = '';
$manager = $modx->getManager();
$dbname= 'my_othter_db';

$tableLike = ''; //" LIKE '{$tablePrefix}%'";

$tablesStmt= $manager->xpdo->prepare("SHOW TABLES FROM {$dbname}{$tableLike}");
$tablesStmt->execute();
$tables= $tablesStmt->fetchAll(PDO::FETCH_NUM);
if ($manager->xpdo->getDebug() === true) {
    $manager->xpdo->log(xPDO::LOG_LEVEL_DEBUG, print_r($tables, true));
}
foreach ($tables as $table) {
    $xmlObject= array();
    $xmlFields= array();
    $xmlIndices= array();
    
    $output .= '<br>Table: '. $table[0];
    
}
    
return $output;