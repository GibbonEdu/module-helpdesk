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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

use Gibbon\Domain\System\SettingGateway;
use Gibbon\Domain\User\RoleGateway;
use Gibbon\Domain\DataSet;
use Gibbon\Module\HelpDesk\Data\Setting;
use Gibbon\Module\HelpDesk\Data\SettingManager;
use Psr\Container\ContainerInterface;

function getSettings(ContainerInterface $container) {
    $settingManager = new SettingManager($container->get(SettingGateway::class), 'Help Desk');

    $settingManager->addSetting('simpleCategories')
        ->setRenderer(function($data, $row) {
            $row->addCheckbox($data['name'])
                ->checked(intval($data['value']));
        })
        ->setProcessor(function($data) {
            return $data !== null ? 1 : 0;
        });

    $settingManager->addSetting('issueCategory')
        ->setRenderer(function($data, $row) {
            $row->addTextArea($data['name'])
                ->setValue($data['value']);
        })
        ->setProcessor(function($data) {
            return implode(',', explodeTrim($data ?? ''));
        });

    $settingManager->addSetting('issuePriority')
        ->setRenderer(function($data, $row) {
            $row->addTextArea($data['name'])
                ->setValue($data['value']);
        })
        ->setProcessor(function($data) {
            return implode(',', explodeTrim($data ?? ''));
        });

    $settingManager->addSetting('issuePriorityName')
        ->setRenderer(function($data, $row) {
            $row->addTextField($data['name'])
                ->setValue($data['value'])
                ->required();
        })
        ->setProcessor(function($data) {
            return empty($data) ? false : $data;
        });

    $settingManager->addSetting('techNotes')
        ->setRenderer(function($data, $row) {
            $row->addCheckbox($data['name'])
                ->checked(intval($data['value']));
        })
        ->setProcessor(function($data) {
            return $data !== null ? 1 : 0;
        });

    return $settingManager;
}


function explodeTrim($commaSeperatedString) {
    //This could, in theory, be made for effiicent, however, I don't care to do so.
    return array_filter(array_map('trim', explode(',', $commaSeperatedString)));
}

function getRoles(ContainerInterface $container) {
	$roleGateway = $container->get(RoleGateway::class);
    $criteria = $roleGateway->newQueryCriteria()
        ->sortBy(['gibbonRole.name']);

    return array_reduce($roleGateway->queryRoles($criteria)->toArray(), function ($group, $role) {
        $group[$role['gibbonRoleID']] = __($role['name']) . ' (' . __($role['category']) . ')';
        return $group; 
    }, []);
}

function statsOverview(DataSet $logs) {
    //Count each log entry
	$items = array_count_values($logs->getColumn('title'));

    //Sort by the title of the entry
    ksort($items);

    //Map the associative array to be displayed in the table
    array_walk($items, function (&$value, $key) {
        $value = ['name' => $key, 'value' => $value];
    });

    return $items;
}

function formatExpandableSection($title, $content) {
    $output = '';

    $output .= '<h6>' . $title . '</h6></br>';
    $output .= nl2brr($content);

    return $output;
}

?>
