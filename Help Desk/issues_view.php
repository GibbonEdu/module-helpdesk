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
use Gibbon\Module\HelpDesk\Domain\IssueGateway;

//Module includes
require_once __DIR__ . '/moduleFunctions.php';

$page->breadcrumbs->add(__('Issues'));

if (isModuleAccessible($guid, $connection2) == false) {
    //Acess denied
    $page->addError('You do not have access to this action.');
} else {

    if (isset($_GET['return'])) {
        $editLink = null;
        if (isset($_GET['issueID'])) {
            $editLink = $_SESSION[$guid]["absoluteURL"] . '/index.php?q=/modules/' . $_SESSION[$guid]['module'] . '/issues_discussView.php&issueID=' . $_GET['issueID'];
        }
        returnProcess($guid, $_GET['return'], $editLink, null);
    }

    $highestAction = getHighestGroupedAction($guid, $_GET["q"], $connection2) ;
    if ($highestAction == false) {
        $page->addError(__('The highest grouped action cannot be determined.'));
        exit();
    }

    $settings = getHelpDeskSettings($connection2);
    $priorityFilters = array("All");
    $priorityName = null;
    $categoryFilters = array("All");

    while ($row = $settings->fetch()) {
        if ($row["name"] == "issuePriority") {
            foreach (explode(",", $row["value"]) as $type) {
                if ($type != "") {
                    array_push($priorityFilters, $type);
                }
            }
        } else if ($row["name"] == "issuePriorityName") {
            $priorityName = $row["value"];
        } else if ($row["name"] == "issueCategory") {
            foreach (explode(",", $row["value"]) as $type) {
                if ($type != "") {
                    array_push($categoryFilters, $type);
                }
            }
        }
    }

    $renderPriority = count($priorityFilters)>1;
    $renderCategory = count($categoryFilters)>1;

 
// $issue = isset($_GET['issue'])? $_GET['issue'] : '';
// $status = isset($_GET['status'])? $_GET['status'] : '';
// $category = isset($_GET['category'])? $_GET['category'] : '';
// $priority = isset($_GET['priority'])? $_GET['priority'] : '';
// $issueID = isset($_GET['issueID'])? $_GET['issueID'] : '';
// $year = isset($_GET['year'])? $_GET['year'] : '';

// TODO: THE ORIGINAL FILTER DIDN'T WORK SO IN THEORY THIS ONE SHOULD WORK JUST GOTTA FIX... ALL THE STUFF ABOVE
//     $form = Form::create('filter', $_SESSION[$guid]['absoluteURL'].'/index.php', 'get');
//     $form->setClass('noIntBorder fullWidth standardForm');
//     $form->setTitle(__('Filter'));
//     $form->setFactory(DatabaseFormFactory::create($pdo));
//     $form->addHiddenValue('q', '/modules/'.$_SESSION[$guid]['module'].'/issues_view.php');
//     $form->addHiddenValue('address', $_SESSION[$guid]['address']);
// 
//     if (count($issueFilters)>1) {  
//         $row = $form->addRow();
//             $row->addLabel('issue', __('Issue Filter'));
//             $row->addSelect('issue')->fromArray($issueFilters)->selected($issue)->required();
//     }
// 
//     $row = $form->addRow();
//         $row->addLabel('status', __('Status Filter'));
//         $row->addSelect('status')->fromArray($statusFilters)->selected($status)->required();
//             
//     if (count($categoryFilters)>1) {  
//         $row = $form->addRow();
//             $row->addLabel('category', __('Category Filter'));
//             $row->addSelect('category')->fromArray($categoryFilters)->selected($category)->required();
//     }
//     if ($renderPriority) {
//         $row = $form->addRow();
//             $row->addLabel('priority', __('Priority Filter'));
//             $row->addSelect('priority')->fromArray($priorityFilters)->selected($priority)->required();
//     }
//     
//     $row = $form->addRow();
//         $row->addLabel('issueID', __('Issue ID Filter'));
//         $row->addTextField('issueID')->setValue($issueID);
//     
//     $row = $form->addRow();
//         $row->addLabel('year', __('Year Filter'));
//         $row->addSelectSchoolYear('year', 'All')->selected($year);
//     
//     
//     $row = $form->addRow();
//         $row->addSearchSubmit($gibbon->session, __('Clear Filters'));
// 
//     echo $form->getOutput();
    

    //TODO: Filters
    $issueGateway = $container->get(IssueGateway::class);
    $criteria = $issueGateway->newQueryCriteria(true)
        ->sortBy('issueID')
        ->fromPOST();
        
    $issues = $issueGateway->queryIssues($criteria);
    $table = DataTable::createPaginated('issues', $criteria);
    
    if (getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssue")) {
        $table->addMetaData('filterOptions', ['issue:All'    => __('Issues').': '.__('All')]);
    }
    if (isTechnician($connection2, $_SESSION[$guid]["gibbonPersonID"])) {
        $table->addMetaData('filterOptions', ['issue:My Working'    => __('Issues').': '.__('My Working')]);
    }
    $table->addMetaData('filterOptions', ['issue:My Issues'    => __('Issues').': '.__('My Issues')]);
    if (isTechnician($connection2, $_SESSION[$guid]["gibbonPersonID"])) {
        if (getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssueStatus")=="All") {        
            $table->addMetaData('filterOptions', [
                'status:Unassigned'           => __('Status').': '.__('Unassigned'),
                'status:Pending'          => __('Status').': '.__('Pending'),
                'status:Resolved'           => __('Status').': '.__('Resolved')
            ]);
        } else if (getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssueStatus")=="UP") {            
            $table->addMetaData('filterOptions', [
                'status:Unassigned'           => __('Status').': '.__('Unassigned'),
                'status:Pending'          => __('Status').': '.__('Pending')
            ]);            
        } else if (getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssueStatus")=="PR") {                    
            $table->addMetaData('filterOptions', [
                'status:Pending'          => __('Status').': '.__('Pending'),
                'status:Resolved'           => __('Status').': '.__('Resolved')
            ]);
        }
    } else {
        $table->addMetaData('filterOptions', [
                'status:Unassigned'           => __('Status').': '.__('Unassigned'),
                'status:Pending'          => __('Status').': '.__('Pending'),
                'status:Resolved'           => __('Status').': '.__('Resolved')
            ]);
    }

    $categoryOptions = array_filter(array_map('trim', explode(',', getSettingByScope($connection2, $_SESSION[$guid]['module'], 'issueCategory', false))));
    if (count($categoryOptions) > 0) {
        //$table->addMetaData('filterOptions', []);
    }

    $priorityOptions = array_filter(array_map('trim', explode(',', getSettingByScope($connection2, $_SESSION[$guid]['module'], 'issuePriority', false))));
    
    $table->setTitle("Issues");

    $table->addHeaderAction('add', __("Create"))
            ->setURL("/modules/" . $_SESSION[$guid]["module"] . "/issues_create.php")
            ->displayLabel();

    $table->addColumn('issueID', __("Issue ID")); 
    $table->addColumn('issueName', __('Name'))
          ->description(__('Description'))
          ->format(function ($issue) {
            return '<strong>' . __($issue['issueName']) . '</strong><br/><small><i>' . __($issue['description']) . '</i></small>';
          });
    $table->addColumn('gibbonPersonID', __('Owner')) 
                ->description(__('Category'))
                ->format(function ($row) use ($connection2) {
                    $owner = getPersonName($connection2, $row['gibbonPersonID']);
                    return Format::name($owner['title'], $owner['preferredName'], $owner['surname'], 'Staff') . '<br/><small><i>'. __($row['category']) . '</i></small>';
                });
    $table->addColumn('technicianID', __('Technician'))
                ->format(function ($row) use ($connection2) {
                    $tech = getPersonName($connection2, $row['techPersonID']);
                    return Format::name($tech['title'], $tech['preferredName'], $tech['surname'], 'Staff');
                });         
    $table->addColumn('status', __('Status'))
          ->description(__('Date'))
          ->format(function ($issue) {
            return '<strong>' . __($issue['status']) . '</strong><br/><small><i>' . Format::date($issue['date']) . '</i></small>';
            });
    //TODO: implement if functions for different cases and such.... eurgh
    $table->addActionColumn()
            ->addParam('issueID')
            ->format(function ($issues, $actions) use ($guid) {
                $actions->addAction('view', __("Open"))
                        ->setURL("/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php");
                $actions->addAction('edit', __("Edit"))
                        ->setURL("/modules/" . $_SESSION[$guid]["module"] . "/issues_discussEdit.php");
                $actions->addAction('assign', __("Assign"))
                        ->setURL("/modules/" . $_SESSION[$guid]["module"] . "/issues_assign.php")
                        ->setIcon('attendance');
                        
            });
     
    echo $table->render($issues);    
 
}
?>
