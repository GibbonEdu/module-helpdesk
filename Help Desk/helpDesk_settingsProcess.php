<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

use Gibbon\Domain\System\SettingGateway;

require_once '../../gibbon.php';

$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $gibbon->session->get('module') . '/helpDesk_settings.php' ;

if (!isActionAccessible($guid, $connection2, '/modules/' . $gibbon->session->get('module') . '/helpDesk_settings.php')) {
    //Fail 0
    $URL .= '&return=error0' ;
    header("Location: {$URL}");
} else {
    //Not a fan, but too bad!
    $settings = array(
        'resolvedIssuePrivacy',
        'issuePriorityName',
        'issueCategory',
        'issuePriority',
    );

    $privacyTypes = array(
        'Everyone',
        'Related',
        'Owner',
        'No one',
    );

    $settingGateway = $container->get(SettingGateway::class);

    $dbFail = false;

    //Is this really better, potentially, but I'm not going to worry about it too much
    foreach ($settings as $setting) {
        if (isset($_POST[$setting])) {
            $value = '';
            switch ($setting) {
                case 'issueCategory':
                case 'issuePriority':
                    $value = implode(',', array_filter(array_map('trim', explode(',', $_POST[$setting]))));
                    break;
                case 'resolvedIssuePrivacy':
                    if (!in_array($_POST[$setting], $privacyTypes)) {
                        $URL .= '&return=error1';
                        header("Location: {$URL}");
                        exit();
                    }
                case 'issuePriorityName':
                    $value = $_POST[$setting];
                    if (empty($value)) {
                        $URL .= '&return=error1';
                        header("Location: {$URL}");
                        exit();
                    }
                    break;
            }
            $dbFail |= !$settingGateway->updateSettingByScope('Help Desk', $setting, $value);
        }
    }

    setLog($connection2, $gibbon->session->get('gibbonPersonID'), $gibbon->session->get('gibbonModuleID'), $gibbon->session->get('gibbonPersonID'), 'Help Desk Settings Edited', null);

    getSystemSettings($guid, $connection2) ;
    $return = 'success0';
    if ($dbFail) {
        $return = 'warning1';
    }
    $URL .= '&return='.$return ;
    header("Location: {$URL}");
}
?>
