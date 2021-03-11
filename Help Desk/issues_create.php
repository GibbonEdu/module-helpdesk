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
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Forms\Form;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\SubcategoryGateway;
use Gibbon\Domain\School\FacilityGateway;

require_once __DIR__ . '/moduleFunctions.php';

$page->breadcrumbs->add(__('Create Issue'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_create.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $moduleName = $gibbon->session->get('module');
    
    if (isset($_GET['issueID'])) {
        $page->return->setEditLink($gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $moduleName . '/issues_discussView.php&issueID=' . $_GET['issueID']);
    }

    $techGroupGateway = $container->get(TechGroupGateway::class);
    $settingGateway = $container->get(SettingGateway::class);

    $priorityOptions = explodeTrim($settingGateway->getSettingByScope($moduleName, 'issuePriority'));
    $categoryOptions = explodeTrim($settingGateway->getSettingByScope($moduleName, 'issueCategory'));
    $simpleCategories = ($settingGateway->getSettingByScope($moduleName, 'simpleCategories') == '1');

    $form = Form::create('createIssue', $gibbon->session->get('absoluteURL') . '/modules/' . $moduleName . '/issues_createProccess.php', 'post');
    $form->setFactory(DatabaseFormFactory::create($pdo));     
    $form->addHiddenValue('address', $gibbon->session->get('address'));
    
    $row = $form->addRow();
        $row->addLabel('issueName', __('Issue Subject'));
        $row->addTextField('issueName')
            ->required()
            ->maxLength(55);
    
    if ($simpleCategories) {
        if (count($categoryOptions) > 0) {
            $row = $form->addRow();
                $row->addLabel('category', __('Category'));
                $row->addSelect('category')
                    ->fromArray($categoryOptions)
                    ->placeholder()
                    ->required();
        }
    } else {
        $subcategoryGateway = $container->get(SubcategoryGateway::class);
        $gibbonRoleID = $gibbon->session->get('gibbonRoleIDCurrent');
        $criteria = $subcategoryGateway->newQueryCriteria()
            ->sortBy(['departmentName', 'subcategoryName'])
            ->filterBy('gibbonRoleID', $gibbonRoleID)
            ->fromPOST();

        $subcategoryData = $subcategoryGateway->querySubcategories($criteria);

        if ($subcategoryData->getTotalCount() == 0) {
            $page->addError(__('No Categories exist. You will not be able to create an issue.'));
        }

        $row = $form->addRow();
            $row->addLabel('subcategoryID', __('Category'));
            $row->addSelect('subcategoryID')
                ->fromDataSet($subcategoryData, 'subcategoryID', 'subcategoryName', 'departmentName')
                ->placeholder()
                ->required();
    }

   $row = $form->addRow();
        $row->addLabel('gibbonSpaceID', __('Facility'));
        $row->addSelectSpace('gibbonSpaceID')
            ->placeholder();
    
    $row = $form->addRow();
        $column = $row->addColumn();
            $column->addLabel('description', __('Description'));
            $column->addEditor('description', $guid)
                    ->setRows(5)
                    ->showMedia()
                    ->required();
        
    if (count($priorityOptions) > 0) {
        $row = $form->addRow();
            $row->addLabel('priority', __($settingGateway->getSettingByScope($moduleName, 'issuePriorityName')));
            $row->addSelect('priority')
                ->fromArray($priorityOptions)
                ->placeholder()
                ->required();
    }
    
    if ($techGroupGateway->getPermissionValue($gibbon->session->get('gibbonPersonID'), 'createIssueForOther')) {
        $row = $form->addRow();
            $row->addLabel('createFor', __('Create on behalf of'))
                ->description(__('Leave blank if creating issue for self.'));
            $row->addSelectStaff('createFor')
                ->placeholder();
    }

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
?>
