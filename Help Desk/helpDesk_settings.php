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
use Gibbon\Forms\Form;

$page->breadcrumbs->add(__('Manage Help Desk Settings'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_settings.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    //I do this to control the order, there is probably another way, until then, too bad!
    $settings = [
        'simpleCategories',
        'issueCategory',
        'issuePriority',
        'issuePriorityName',
        'techNotes'
    ];

    $settingGateway = $container->get(SettingGateway::class);

    $form = Form::create('helpDeskSettings',  $session->get('absoluteURL') . '/modules/' . $session->get('module') . '/helpDesk_settingsProcess.php');
    $form->addHiddenValue('address', $session->get('address'));

    foreach ($settings as $settingName) {
        $setting = $settingGateway->getSettingByScope('Help Desk', $settingName, true);
        $row = $form->addRow();
            $row->addLabel($setting['name'], __($setting['nameDisplay']))->description($setting['description']);
            switch ($settingName) {
                case 'issueCategory':
                case 'issuePriority':
                    $row->addTextArea($setting['name'])
                        ->setValue($setting['value']);
                    break;
                case 'issuePriorityName':
                    $row->addTextField($setting['name'])
                        ->setValue($setting['value'])
                        ->required();
                    break;
                case 'simpleCategories':
                case 'techNotes':
                    $row->addCheckbox($setting['name'])
                        ->checked(intval($setting['value']));
                    break;
            }
    }

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
?>
