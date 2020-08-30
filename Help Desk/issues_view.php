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

@session_start();

//Module includes
include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isModuleAccessible($guid, $connection2) == false) {
    //Acess denied
    $page->addError('You do not have access to this action.');
} else {
    $page->breadcrumbs->add(__('Issues'));

    if (isset($_GET['return'])) {
        $editLink = null;
        if (isset($_GET['issueID'])) {
            $editLink = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Deskissues_discussView.php&issueID=" . $_GET['issueID'];
        }
        returnProcess($guid, $_GET['return'], $editLink, null);
    }

    $highestAction = getHighestGroupedAction($guid, $_GET["q"], $connection2) ;
    if ($highestAction == false) {
        $page->addError(__('The highest grouped action cannot be determined.'));
        exit();
    }

    print "<h3>" ;
        print __("Filter") ;
    print "</h3>" ;


    $filter1=null ;
    $filter2=null ;
    $filter3=null ;
    $filter4=null ;
    $yearFilter=null;
    $IDFilter="" ;
    $whereYear="";
    $whereUsed=false;

    if (isset($_GET["filter1"])) {
        $filter = $_GET["filter1"] ;
    }
    if (isset($_GET["filter2"])) {
        $filter2 = $_GET["filter2"] ;
    }
    if (isset($_GET["filter3"])) {
        $filter3 = $_GET["filter3"] ;
    }
    if (isset($_GET["filter4"])) {
        $filter4 = $_GET["filter4"] ;
    }
    if (isset($_GET["yearFilter"])) {
        $yearFilter = $_GET["yearFilter"] ;
    }

    if (isset($_GET["IDFilter"])) {
        $IDFilter=intval($_GET["IDFilter"]) ;
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

    $issueFilters = array();
    if (getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssue")) {
        array_push($issueFilters, "All");
    }
    if (isTechnician($connection2, $_SESSION[$guid]["gibbonPersonID"])) {
        array_push($issueFilters, "My Working");
    }
    array_push($issueFilters, "My Issues");
    if (isTechnician($connection2, $_SESSION[$guid]["gibbonPersonID"])) {
        $statusFilters = array("Pending");
        if (getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssueStatus")=="All") {
            $statusFilters = array("All", "Unassigned", "Pending", "Resolved");
        } else if (getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssueStatus")=="UP") {
            $statusFilters = array("Unassigned and Pending", "Unassigned", "Pending");
        } else if (getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssueStatus")=="PR") {
            $statusFilters = array("Pending and Resolved", "Pending", "Resolved");
        }
    } else {
        $statusFilters = array("All", "Unassigned", "Pending", "Resolved");
    }


//TODO: THE ORIGINAL FILTER DIDN'T WORK SO IN THEORY THIS ONE SHOULD WORK JUST GOTTA FIX... ALL THE STUFF ABOVE
    $form = Form::create('search', $_SESSION[$guid]['absoluteURL'].'/index.php', 'get');
    $form->setFactory(DatabaseFormFactory::create($pdo));
    $form->addHiddenValue('q', '/modules/'.$_SESSION[$guid]['module'].'/issues_view.php');
    $form->addHiddenValue('address', $_SESSION[$guid]['address']);

    if (count($issueFilters)>1) {  
        $row = $form->addRow();
            $row->addLabel('filter1', __('Issue Filter'));
            $row->addSelect('filter1')->fromArray($issueFilters)->selected($option)->required();
    }

    $row = $form->addRow();
        $row->addLabel('filter2', __('Status Filter'));
        $row->addSelect('filter2')->fromArray($statusFilters)->selected($option)->required();
            
    if (count($categoryFilters)>1) {  
        $row = $form->addRow();
            $row->addLabel('filter3', __('Category Filter'));
            $row->addSelect('filter3')->fromArray($categoryFilters)->selected($option)->required();
    }
    if ($renderPriority) {
        $row = $form->addRow();
            $row->addLabel('filter4', __('Priority Filter'));
            $row->addSelect('filter4')->fromArray($priorityFilters)->selected($option)->required();
    }
    
    $row = $form->addRow();
        $row->addLabel('IDFilter', __('Issue ID Filter'));
        $row->addTextField('IDFilter')->setValue($option);
    
    $row = $form->addRow();
        $row->addLabel('yearFilter', __('Year Filter'));
        $row->addSelectSchoolYear('yearFilter', 'All')->selected($option);
    
    
    $row = $form->addRow();
        $row->addSearchSubmit($gibbon->session);

    echo $form->getOutput();
    

    
    $issueGateway = $container->get(IssueGateway::class);
   //TODO: add filter capability
    
    $table = DataTable::create('issues');
    $table->setTitle("Issues");

    $table->addHeaderAction('add', __("Create"))
            ->setURL("/modules/" . $_SESSION[$guid]["module"] . "/issues_create.php")
            ->displayLabel();

    $table->addColumn('issueID', __("Issue ID")); 
    $table->addColumn('issueName', __("Name")); //TODO: row within column, have a description
    $table->addColumn('gibbonPersonID', __('Owner'))
                ->format(function ($row) use ($connection2) {
                    $owner = getPersonName($connection2, $row['gibbonPersonID']);
                    return Format::name($owner['title'], $owner['preferredName'], $owner['surname'], 'Staff');
                });
    $table->addColumn('technicianID', __('Technician'))
                ->format(function ($row) use ($connection2) {
                    $tech = getPersonName($connection2, $row['techPersonID']);
                    return Format::name($tech['title'], $tech['preferredName'], $tech['surname'], 'Staff');
                });         
    $table->addColumn('status', __("Status"));
    //TODO: implement if functions for different cases and such.... eurgh
    $table->addActionColumn()
            ->addParam('issueID')
            ->format(function ($issues, $actions) use ($guid, $result) {
                $actions->addAction('view', __("Open"))
                        ->setURL("/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php");
                $actions->addAction('edit', __("Edit"))
                        ->setURL("/modules/" . $_SESSION[$guid]["module"] . "/issues_discussEdit.php");
                $actions->addAction('assign', __("Assign"))
                        ->setURL("/modules/" . $_SESSION[$guid]["module"] . "/issues_assign.php")->setIcon('attendance');;
                        
            });
     
    echo $table->render($issueGateway->selectIssues()->toDataSet());    
 
}
?>
