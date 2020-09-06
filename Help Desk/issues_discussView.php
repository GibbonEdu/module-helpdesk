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

use Gibbon\Tables\DataTable;
use Gibbon\Tables\Action;
use Gibbon\Services\Format;
use Gibbon\Module\HelpDesk\Domain\IssueDiscussGateway;
use Gibbon\Domain\DataSet;
use Gibbon\Domain\System\DiscussionGateway;
use Gibbon\View\View;

require_once __DIR__ . '/moduleFunctions.php';

$allowed = relatedToIssue($connection2, $_GET['issueID'], $_SESSION[$guid]['gibbonPersonID']);
if ((!hasTechnicianAssigned($connection2, $_GET['issueID']) && isTechnician($connection2, $_SESSION[$guid]['gibbonPersonID'])) || getPermissionValue($connection2, $_SESSION[$guid]['gibbonPersonID'], 'fullAccess')) {
    $allowed = true;
}

if (!isModuleAccessible($guid, $connection2) || !$allowed) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
    exit();
} else {
    $page->breadcrumbs->add(__('Discuss Issue'));
    
    $issueID = $_GET['issueID'] ;
    $data = array('issueID' => $issueID) ;

    try {
        $sql = 'SELECT helpDeskIssue.* , surname , preferredName , title FROM helpDeskIssue JOIN gibbonPerson ON (helpDeskIssue.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE issueID=:issueID ' ;
        $result=$connection2->prepare($sql);
        $result->execute($data);

        $sql2 = 'SELECT helpDeskTechnicians.*, surname , title, preferredName, helpDeskIssue.createdByID, helpDeskIssue.status AS issueStatus, privacySetting FROM helpDeskIssue JOIN helpDeskTechnicians ON (helpDeskIssue.technicianID=helpDeskTechnicians.technicianID) JOIN gibbonPerson ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE issueID=:issueID ' ;
        $result2 = $connection2->prepare($sql2);
        $result2->execute($data);
        $array2 = $result2->fetch();

        $sql3 = 'SELECT issueDiscussID, comment, timestamp, gibbonPersonID FROM helpDeskIssueDiscuss WHERE issueID=:issueID ORDER BY timestamp ASC' ;
        $result3 = $connection2->prepare($sql3);
        $result3->execute($data);

        $sql4 = 'SELECT surname , preferredName , title FROM helpDeskIssue JOIN gibbonPerson ON (helpDeskIssue.createdByID=gibbonPerson.gibbonPersonID) WHERE issueID=:issueID';
        $result4 = $connection2->prepare($sql4);
        $result4->execute($data);
        $row4 = $result4->fetch();
    } catch (PDOException $e) {
    }

    $privacySetting = $array2['privacySetting'];
    if ($array2['issueStatus']=='Resolved' && !getPermissionValue($connection2, $_SESSION[$guid]['gibbonPersonID'], 'fullAccess')) {
        if ($privacySetting == 'No one') {
            $page->addError(__('You do not have access to this action.'));
            exit();
        } else if ($privacySetting == 'Related' && !relatedToIssue($connection2, $issueID, $_SESSION[$guid]['gibbonPersonID'])) {
            $page->addError(__('You do not have access to this action.'));
            exit();
        }
        else if ($privacySetting == 'Owner' && !isPersonsIssue($connection2, $issueID, $_SESSION[$guid]['gibbonPersonID'])) {
            $page->addError(__('You do not have access to this action.'));
            exit();
        }
    }

    if (!isset($array2['gibbonPersonID'])) {
        $technicianName = 'Unassigned' ;
    } else {
        $technicianName = formatName($array2['title'] , $array2['preferredName'] , $array2['surname'] , 'Student', false, false);
    }

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    if (!isset($array2['technicianID'])) {
        $array2['technicianID'] = null;
    }

    if (technicianExists($connection2, $array2['technicianID']) && !isPersonsIssue($connection2, $issueID, $_SESSION[$guid]['gibbonPersonID']) && !getPermissionValue($connection2, $_SESSION[$guid]['gibbonPersonID'], 'resolveIssue')) {
        if (!($array2['technicianID'] == getTechnicianID($connection2, $_SESSION[$guid]['gibbonPersonID']))) {
            $page->addError(__('You do not have access to this action.'));
            exit();
        }
    }

    $issueDiscussID = null;
    if (isset($_GET['issueDiscussID'])) {
        $issueDiscussID = $_GET['issueDiscussID'];
    }

    $row = $result->fetch();

    $createdByShow = $row['createdByID'] != $row['gibbonPersonID'];

    $date2 = dateConvertBack($guid, $row['date']);
    if ($date2 == '30/11/-0001') {
        $date2 = 'No date';
    }

    $studentName = formatName($row['title'] , $row['preferredName'] , $row['surname'] , 'Student', false, false);

    $detailsData = array(
        'issueID' => $issueID,
        'owner' => $studentName,
        'technician' => $technicianName,
        'date' => $date2,
        'privacySetting' => $row['privacySetting']
    );

    $tdWidth = count($detailsData);
    if ($createdByShow) {
        $tdWidth++;
    }
    $tdWidth = 100 / $tdWidth;
    $tdWidth .= '%';

    $table = DataTable::createDetails('details');
    $table->setTitle($row['issueName']);

    $table->addColumn('issueID', __('ID'))
            ->width($tdWidth);

    $table->addColumn('owner', __('Owner'))
            ->width($tdWidth);

    $table->addColumn('technician', __('Technician'))
            ->width($tdWidth);

    $table->addColumn('date', __('Date'))
            ->width($tdWidth);

    if ($createdByShow) {
        $detailsData['createdBy'] = formatName($row4['title'] , $row4['preferredName'] , $row4['surname'] , 'Student', false, false);
        $table->addColumn('createdBy', __('Created By'))
            ->width($tdWidth);
    }

    $table->addColumn('privacySetting', __('Privacy'))
            ->width($tdWidth)
            ->format(function($row) use ($connection2, $guid) {
                if (isPersonsIssue($connection2, $row['issueID'], $_SESSION[$guid]["gibbonPersonID"]) || getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "fullAccess")) {
                    print '<a href="' . $_SESSION[$guid]['absoluteURL'] . '/index.php?q=/modules/' . $_SESSION[$guid]['module'] . '/issues_discussEdit.php&issueID='. $row['issueID'] . '">' .  __($row['privacySetting']) . '</a>';
                } else {
                    print $row['privacySetting'];
                }
            });

    echo $table->render([$detailsData]);

    //Description Table
    $table = DataTable::createDetails('description');
    $table->setTitle(__('Description'));

    if ($array2['technicianID'] == null && (!relatedToIssue($connection2, $_GET['issueID'], $_SESSION[$guid]['gibbonPersonID']) || getPermissionValue($connection2, $_SESSION[$guid]['gibbonPersonID'], 'fullAccess'))) {
         if (getPermissionValue($connection2, $_SESSION[$guid]['gibbonPersonID'], 'acceptIssue') && !isPersonsIssue($connection2, $issueID, $_SESSION[$guid]['gibbonPersonID'])) {
            $table->addHeaderAction('accept', __('Accept'))
                    ->setIcon('page_new')
                    ->directLink()
                    ->setURL('/modules/' . $_SESSION[$guid]['module'] . '/issues_acceptProcess.php')
                    ->addParam('issueID', $issueID);
        }
        if (getPermissionValue($connection2, $_SESSION[$guid]['gibbonPersonID'], 'assignIssue')) {
            $table->addHeaderAction('assign', __('Assign'))
                    ->setIcon('attendance')
                    ->modalWindow()
                    ->setURL('/modules/' . $_SESSION[$guid]['module'] . '/issues_assign.php')
                    ->addParam('issueID', $issueID);
          }
    }

    $table->addColumn('description')
            ->width('100%');

    echo $table->render([$row]);

    if ($array2['technicianID'] != null) {
            $IssueDiscussGateway = $container->get(IssueDiscussGateway::class);
            $logs = $IssueDiscussGateway->getIssueDiscussionByID($issueID)->fetchAll();

            echo $page->fetchFromTemplate('ui/discussion.twig.html', [
                'title' => __('Comments'),
                'discussion' => $logs
            ]); 
            
            //Again a bit of a cheat, we'll see how this goes.
            $headerActions = array();

            $action = new Action('refresh', __('Refresh'));
            $action->setIcon('refresh')
                    ->setURL('/modules/' . $_SESSION[$guid]['module'] . '/issues_discussView.php')
                    ->addParam('issueID', $issueID);
            $headerActions[] = $action;

            $action = new Action('add', __('Add'));
            $action->modalWindow()
                    ->setURL('/modules/' . $_SESSION[$guid]['module'] . '/issues_discussPost.php')
                    ->addParam('issueID', $issueID);

            $headerActions[] = $action;

            if (getPermissionValue($connection2, $_SESSION[$guid]['gibbonPersonID'], 'resolveIssue') || isPersonsIssue($connection2, $issueID, $_SESSION[$guid]['gibbonPersonID'])) {
                $action = new Action('resolve', __('Resolve'));
                $action->setIcon('iconTick')
                        ->directLink()
                        ->setURL('/modules/' . $_SESSION[$guid]['module'] . '/issues_resolveProcess.php')
                        ->addParam('issueID', $issueID);

                $headerActions[] = $action;
            }

            echo '<div class="linkTop">';
                foreach ($headerActions as $action) {
                    echo $action->getOutput();
                }
            echo '</div>';
    }
}
?>
