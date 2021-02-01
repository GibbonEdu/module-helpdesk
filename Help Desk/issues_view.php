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
use Gibbon\Domain\DataSet;
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Forms\Form;
use Gibbon\Services\Format;
use Gibbon\Tables\DataTable;
use Gibbon\Domain\User\UserGateway;
use Gibbon\Domain\School\FacilityGateway;
use Gibbon\Module\HelpDesk\Domain\DepartmentGateway;
use Gibbon\Module\HelpDesk\Domain\IssueGateway;
use Gibbon\Module\HelpDesk\Domain\SubcategoryGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;
use Gibbon\Domain\System\SettingGateway;

//Module includes
require_once __DIR__ . '/moduleFunctions.php';

$page->breadcrumbs->add(__('Issues'));

if (!isModuleAccessible($guid, $connection2)) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    
    $gibbonPersonID = $gibbon->session->get('gibbonPersonID');
    $moduleName = $gibbon->session->get('module');
    $year = $_GET['year'] ?? $gibbon->session->get('gibbonSchoolYearID');
    $relation = $_GET['relation'] ?? null;

    if (isset($_GET['return'])) {
        $editLink = null;
        if (isset($_GET['issueID'])) {
            $editLink = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $moduleName . '/issues_discussView.php&issueID=' . $_GET['issueID'];
        }
        returnProcess($guid, $_GET['return'], $editLink, null);
    }
    
    $issueGateway = $container->get(IssueGateway::class);
    $techGroupGateway = $container->get(TechGroupGateway::class);
    $technicianGateway = $container->get(TechnicianGateway::class);
    $userGateway = $container->get(UserGateway::class);
    $settingsGateway = $container->get(SettingGateway::class);
 
    $technician = $technicianGateway->getTechnicianByPersonID($gibbonPersonID);
    $isTechnician = $technician->isNotEmpty();
    $techGroup = $techGroupGateway->getByID($isTechnician ? $technician->fetch()['groupID'] : ''); 
    $departmentID = $_GET['departmentID'] ?? $techGroup['departmentID'] ?? NULL;
    $fullAccess = $techGroupGateway->getPermissionValue($gibbonPersonID, 'fullAccess');
       
    $criteria = $issueGateway->newQueryCriteria(true)
        ->searchBy($issueGateway->getSearchableColumns(), $_GET['search'] ?? '')
        ->filterBy('departmentID', $departmentID)
        ->sortBy('status', 'ASC')
        ->sortBy('issueID', 'DESC')
        ->fromPOST();
    
    //Set up Relation data
    $relations = [];

    if ($techGroupGateway->getPermissionValue($gibbonPersonID, 'viewIssue')) {
        $relations[] = 'All';
        $relation = $relation ?? 'All';
    }

    if ($isTechnician) {
        $relations[] = 'My Assigned';
        $relation = $relation ?? 'My Assigned';
    }
    
    $relations[] = 'My Issues';

    if (!in_array($relation, $relations)) {
        $relation = 'My Issues';
    }

    //Search Form
    $form = Form::create('searchForm', $gibbon->session->get('absoluteURL') . '/index.php', 'get');
    $form->setFactory(DatabaseFormFactory::create($pdo));

    $form->addHiddenValue('q', '/modules/' . $moduleName . '/issues_view.php');
    $form->addHiddenValue('address', $gibbon->session->get('address'));

    $form->setClass('noIntBorder fullWidth standardForm');
    $form->setTitle(__('Search & Filter'));

    $row = $form->addRow();
        $row->addLabel('search', __('Search'))
            ->description(__('Issue ID, Name or Description.'));
        $row->addTextField('search')
            ->setValue($criteria->getSearchText());
    
    if (count($relations) > 1) {
        $row = $form->addRow();
            $row->addLabel('relation', __('Relation'));
            $row->addSelect('relation')
                ->fromArray($relations)
                ->selected($relation);
    }

    $row = $form->addRow();
        $row->addLabel('year', __('Year Filter'));
        $row->addSelectSchoolYear('year', 'All')
            ->selected($year);
    
    $row = $form->addRow();
        $row->addSearchSubmit($gibbon->session, __('Clear Filters'));

    echo $form->getOutput();      
    
    $issues = $issueGateway->queryIssues($criteria, $year, $gibbonPersonID, $relation);

    $mode = 'owner';

    if ($techGroupGateway->getPermissionValue($gibbonPersonID, 'viewIssue')) {
        $mode = 'all';
    } else if ($isTechnician) {
        $mode = 'tech';
    }
    
    $table = DataTable::createPaginated('issues', $criteria);
    $table->setTitle('Issues');
    
    //FILTERS START    
    $statusFilter = [
        'status:Unassigned' => __('Status').': '.__('Unassigned'),
        'status:Pending'    => __('Status').': '.__('Pending'),
        'status:Resolved'   => __('Status').': '.__('Resolved')
    ];

    if ($isTechnician) {
        switch($techGroupGateway->getPermissionValue($gibbonPersonID, 'viewIssueStatus')) {
            case 'UP':
                unset($statusFilter['status:Resolved']);
                break;

            case 'PR':
                unset($statusFilter['status:Unassigned']);
                break;

            case 'Pending':
                $statusFilter = array();
                break;
        }   
    }

    $table->addMetaData('filterOptions', $statusFilter);


    $simpleCategories = $settingsGateway->getSettingByScope($moduleName, 'simpleCategories');
    if ($simpleCategories) {
        $categoryFilters = explodeTrim($settingsGateway->getSettingByScope($moduleName, 'issueCategory'));
        foreach  ($categoryFilters as $category) {
            $table->addMetaData('filterOptions', [
                'category:'.$category => __('Category').': '.$category,
            ]);
        }
    } else {
        if (!$isTechnician || ($isTechnician && $techGroup['departmentID'] == null) || $fullAccess) {
            $departmentGateway = $container->get(DepartmentGateway::class);
            $departments = $departmentGateway->selectDepartments()->toDataSet();

            foreach ($departments as $department) {
                $table->addMetaData('filterOptions', [
                    'departmentID:' . $department['departmentID'] => __('Department') . ': ' . $department['departmentName'],
                ]);
            }
        } else {
            $subcategoryGateway = $container->get(SubcategoryGateway::class);
            $subcategoryCriteria = $subcategoryGateway->newQueryCriteria(true)
                ->filterBy('departmentID', $techGroup['departmentID'])
                ->sortBy(['departmentName', 'subcategoryName']);

            $subcategories = $subcategoryGateway->querySubcategories($subcategoryCriteria);
            foreach ($subcategories as $subcategory) {
                $table->addMetaData('filterOptions', [
                    'subcategoryID:' . $subcategory['subcategoryID'] => __('Subcategory') . ': ' . $subcategory['departmentName'] . ' - ' . $subcategory['subcategoryName'],
                ]);
            }
        }
    }

    $priorityFilters = explodeTrim($settingsGateway->getSettingByScope($moduleName, 'issuePriority', false));
    foreach  ($priorityFilters as $priority) {
        $table->addMetaData('filterOptions', [
            'priority:'.$priority => __('Priority').': '.$priority,
        ]);
    }
    //FILTERS END    
    
    //Row Modifiers
    $table->modifyRows(function($issue, $row) use ($gibbonPersonID, $techGroupGateway, $issueGateway, $mode, $techGroup) {
        if ($issue['status'] == 'Resolved') {
            $row->addClass('current');
        } else if ($issue['status'] == 'Unassigned') {
            $row->addClass('error');
        } else if ($issue['status'] == 'Pending') {
            $row->addClass('warning');
        }
        
        if ($mode == 'owner') {
            if ($issue['gibbonPersonID'] != $gibbonPersonID) {
                $row = null;
            }
        } else if ($mode == 'tech') {
            if ($issue['techPersonID'] != $gibbonPersonID && $issue['gibbonPersonID'] != $gibbonPersonID) {
                $viewIssueStatus = $techGroupGateway->getPermissionValue($gibbonPersonID, 'viewIssueStatus');

                if ($viewIssueStatus == 'PR' && $issue['status'] == 'Unassigned') {
                    $row = null;
                } else if ($viewIssueStatus == 'UP' && $issue['status'] == 'Resolved') {
                    $row = null;
                } else if ($viewIssueStatus == 'Pending' && $issue['status'] != 'Pending') {
                    $row = null;
                }

                if ($techGroup['departmentID'] != null && $issue['departmentID'] != $techGroup['departmentID']) {
                    $row = null;
                }
            }
        }

        return $row;
    });

    //Header Actions
    if (isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_create.php')) {
        $table->addHeaderAction('add', __('Create'))
            ->setURL('/modules/' . $moduleName . '/issues_create.php')
            ->displayLabel();
    }

    //Issue ID column
    $table->addColumn('issueID', __('Issue ID'))
            ->format(Format::using('number', ['issueID'])); 

    //Subject & Description Column
    $table->addColumn('issueName', __('Subject'))
          ->description(__('Description'))
          ->format(function ($issue) {
            return Format::bold($issue['issueName']) . '<br/>' . Format::small(Format::truncate(strip_tags($issue['description']), 50));
          });
          
    //Owner & Technician Column
    $table->addColumn('gibbonPersonID', __('Owner')) 
                ->description(__('Technician'))
                ->format(function ($row) use ($userGateway) {
                    $owner = $userGateway->getByID($row['gibbonPersonID']);
                    $output = Format::bold(Format::name($owner['title'], $owner['preferredName'], $owner['surname'], 'Staff')) . '<br/>';

                    $tech = $userGateway->getByID($row['techPersonID']);
                    if (!empty($tech)) {
                        $output .= Format::small(Format::name($tech['title'], $tech['preferredName'], $tech['surname'], 'Staff'));
                    }

                    return $output;
                });

    //Facility & Category Column
    $table->addColumn('facility', __('Facility')) 
        ->description(__('Category'))
        ->format(function ($row) use ($simpleCategories) {
            $facility = $row['facility'] ?? 'N/A';

            $category = $row['category'];
            if (!$simpleCategories && !empty($row['subcategoryName'])) {
                $category = $row['departmentName'] . ' - ' . $row['subcategoryName'];
            }

            return  __(Format::bold($facility) . '<br/>'. Format::small($category));
        });
    
    //Priority Column
    if (!empty($priorityFilters)) {
        $table->addColumn('priority', __($settingsGateway->getSettingByScope($moduleName, 'issuePriorityName')));
    }

    //Status & Date Column
    $table->addColumn('status', __('Status'))
          ->description(__('Date'))
          ->format(function ($issue) {
                return Format::bold(__($issue['status'])) . '<br/>' . Format::small(Format::date($issue['date']));
            });
   
    //Action Column 
    $table->addActionColumn()
            ->addParam('issueID')
            ->format(function ($issues, $actions) use ($gibbonPersonID, $moduleName, $fullAccess, $techGroupGateway, $issueGateway) {
                $isPersonsIssue = $issues['gibbonPersonID'] == $gibbonPersonID;
                $related = $issueGateway->isRelated($issues['issueID'], $gibbonPersonID) || $fullAccess;

                $actions->addAction('view', __('Open'))
                        ->setURL('/modules/' . $moduleName . '/issues_discussView.php');

                if ($issues['status'] != 'Resolved') {
                    if ($issues['technicianID'] == null) {
                        if (!$isPersonsIssue && $techGroupGateway->getPermissionValue($gibbonPersonID, 'acceptIssue')) {
                            $actions->addAction('accept', __('Accept'))
                                    ->directLink()
                                    ->setURL('/modules/' . $moduleName . '/issues_acceptProcess.php')
                                    ->setIcon('page_new');
                        }

                        if ($techGroupGateway->getPermissionValue($gibbonPersonID, 'assignIssue')) {
                            $actions->addAction('assign', __('Assign'))
                                    ->setURL('/modules/' . $moduleName . '/issues_assign.php')
                                    ->setIcon('attendance');
                        }
                    } else if ($techGroupGateway->getPermissionValue($gibbonPersonID, 'reassignIssue')) { 
                        $actions->addAction('assign', __('Reassign'))
                                ->setURL('/modules/' . $moduleName . '/issues_assign.php')
                                ->setIcon('attendance');
                    }
                    
                    if ($isPersonsIssue || ($related && $techGroupGateway->getPermissionValue($gibbonPersonID, 'resolveIssue'))) {
                        $actions->addAction('resolve', __('Resolve'))
                                ->directLink()
                                ->setURL('/modules/' . $moduleName . '/issues_resolveProcess.php')
                                ->setIcon('iconTick');
                    }
                } else {
                    if ($isPersonsIssue || ($related && $techGroupGateway->getPermissionValue($gibbonPersonID, 'reincarnateIssue'))) {
                        $actions->addAction('reincarnate', __('Reincarnate'))
                                ->directLink()
                                ->setURL('/modules/' . $moduleName . '/issues_reincarnateProcess.php')
                                ->setIcon('reincarnate');
                    }
                }
            });
    
    echo $table->render($issues);    
 
}
?>
