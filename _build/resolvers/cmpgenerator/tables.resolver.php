<?php

/**
 * MyComponent resolver script - runs on install.
 *
 * Copyright 2011 Your Name <you@yourdomain.com>
 * @author Your Name <you@yourdomain.com>
 * 1/1/11
 *
 * MyComponent is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * MyComponent is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * MyComponent; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package mycomponent
 */
/**
 * Description: Resolver script for MyComponent package
 * @package mycomponent
 * @subpackage build
 */

/* Example Resolver script */

/* The $modx object is not available here. In its place we
 * use $object->xpdo
 */

if ($object->xpdo) {
    $modx =& $object->xpdo;
    // add package
    $s_path = $modx->getOption('core_path').'components/cmpgenerator/model/';
    $modx->addPackage('cmpgenerator', $s_path);
    
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            
            //$modx->log(xPDO::LOG_LEVEL_INFO,'Package Path: '.$s_path);
            $manager = $modx->getManager();
            $modx->setLogLevel(modX::LOG_LEVEL_ERROR);
            $manager->createObjectContainer('CmpGenerator');
            $modx->setLogLevel(modX::LOG_LEVEL_INFO);
            break;
    }
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            break;
        case xPDOTransport::ACTION_UPGRADE:
            /*
            $modx->exec("ALTER TABLE {$modx->getTableName('ChurchEvents')} 
                DROP COLUMN `locations`,
                ADD COLUMN `prominent` SET('Yes','No') DEFAULT 'No' NULL AFTER `web_user_id`,
                ADD COLUMN `personal_subscribers` TEXT NULL AFTER `contact_phone`,
                ADD COLUMN `extended` TEXT NULL AFTER `country`,
                CHANGE `public_desc` `public_desc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
                CHANGE `notes` `notes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
                CHANGE `office_notes` `office_notes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
                ADD COLUMN `exceptions` TEXT NULL AFTER `days`
                ");*/
            break;
    }
}
$modx->log(xPDO::LOG_LEVEL_INFO,'Tables resolver actions completed');

return true;