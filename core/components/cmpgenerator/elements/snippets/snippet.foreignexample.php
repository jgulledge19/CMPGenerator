<?php

/**
 * mytest table
 */
$limit = $modx->getOption('limit',$scriptProperties,20);

require $modx->getOption('core_path').'/config/bc_config.inc.php';

$output = '';// this is what the snippet will return

/*$foreignDB = new xPDO('mysql:host=' . $foreign_database_host.';dbname='.$foreign_database_name/*.';charset='.$foreign_database_charset* /,
        $foreign_database_username,
        $foreign_database_password );*/
        
$package_path = $modx->getOption('core_path').'components/foreigndb/model/';
require_once $package_path.'foreignconnect.class.php';

$foreignDB = ForeignConnect::getInstance($database_dsn, $database_user, $database_password);

// see the scheme file and the xml model element and you will see the attribute package and that must match here
// make sure you set the prefix as empty if you don't use it
if ( !$foreignDB->addPackage('foreigndb', $package_path, '') ) {
    return 'Can not load package';
}

// lets add some data!
// see the scheme file and the xml object element and you will see the attribute class and that must match here
/*
$myRow = $foreignDB->newObject('EventName');
$data = array(
        'name' => 'MODX Revolution',
        'description' => 'A great CMS product...'
    );
$myRow->fromArray($data);

if ( !$myRow->save() ) {
    $output .= '<p>Could not create row</p>';
} else {
    $output .= '<p>Created row successfully</p>';
}
*/
// now lets show the data in a quick and dirty table:
$output .= '
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
    </tr>';

// Note for all HTML you should be using Chunks see: http://rtfm.modx.com/display/revolution20/Chunks#Chunks-ProcessingChunkviatheAPI
/* build query */

$query = $foreignDB->newQuery('EventName');
$query->limit($limit);
$rows = $foreignDB->getIterator('EventName', $query);

/* iterate */
$list = array();
foreach ($rows as $row) {
    // from object to array you can also do $row->get('name');
    $row_array = $row->toArray();

    $output .= '
    <tr>
        <td>'.$row_array['event_name_id'].'</td>
        <td>'.$row_array['name'].'</td>
        <td>'.$row_array['description'].'</td>
    </tr>';
}
$output .= '
</table>';

return $output;