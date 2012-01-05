<?php
/**
 * Build the setup options form.
 *
 * @package churchevents
 * @subpackage build
 */
/* set some default values */
$values = array(
    'allowRequests' => '1'
);
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $setting = $modx->getObject('modSystemSetting',array('key' => 'churchevents.allowRequests'));
        if ($setting != null) {
			$values['allowRequests'] = $setting->get('value');
		}
        unset($setting);

    break;
    case xPDOTransport::ACTION_UNINSTALL: break;
}

$output = '
<input type="checkbox" name="allowRequests" id="churchevents_allowRequests" width="300" value="1" '.( $values['allowRequests'] == 1 ? 'checked="checked"' : '' ).'" />
<label for="churchevents_allowRequests">Allow anonymous requests for events</label>
';

return $output;
