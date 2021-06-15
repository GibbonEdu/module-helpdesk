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

use Gibbon\Comms\NotificationSender;
use Gibbon\Domain\System\LogGateway;
use Gibbon\Domain\System\NotificationGateway;
use Gibbon\Module\HelpDesk\Domain\IssueGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

//Bit of a cheat, but needed for gateway to work
$_POST['address'] = '/modules/Help Desk/issues_reincarnateProcess.php';

require_once '../../gibbon.php';

$URL = $session->get('absoluteURL') . '/index.php?q=/modules/' . $session->get('module');

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_view.php')) {
    //Fail 0
    $URL .= '/issues_view.php&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    //Proceed!
    $issueID = $_GET['issueID'] ?? '';

    $issueGateway = $container->get(IssueGateway::class);
    $issue = $issueGateway->getByID($issueID);
    if (empty($issueID) || empty($issue)) {
        $URL .= '/issues_view.php&return=error1';
        header("Location: {$URL}");
        exit();
    }

    $gibbonPersonID = $session->get('gibbonPersonID');

    $techGroupGateway = $container->get(TechGroupGateway::class);
    
    $related = $issueGateway->isRelated($issueID, $gibbonPersonID) || $techGroupGateway->getPermissionValue($gibbonPersonID, 'fullAccess');
    if (($issue['gibbonPersonID'] == $gibbonPersonID) || ($related && $techGroupGateway->getPermissionValue($gibbonPersonID, 'reincarnateIssue'))) {
        $URL .= "/issues_discussView.php&issueID=$issueID";

        $status = 'Pending';
        if ($issue['technicianID'] == null) {
            $status = 'Unassigned';
        }

        //Write to database
        if (!$issueGateway->update($issueID, ['status' => $status])) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
            exit();
        }


        //Send Notification
        $notificationGateway = $container->get(NotificationGateway::class);
        $notificationSender = new NotificationSender($notificationGateway, $session);

        $message = __('Issue #') . $issueID . ' (' . $issue['issueName'] . ') ' . __('has been reincarnated.');

        $personIDs = $issueGateway->getPeopleInvolved($issueID);

        foreach ($personIDs as $personID) {
            if ($personID != $gibbonPersonID) {
                $notificationSender->addNotification($personID, $message, 'Help Desk', '/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=' . $issueID);
            } 
        }
        
        $notificationSender->sendNotifications();

        //Log
        $array = ['issueID' => $issueID];

        $technicianGateway = $container->get(TechnicianGateway::class);
        $technician = $technicianGateway->getTechnicianByPersonID($gibbonPersonID);
        if ($technician->isNotEmpty()) {
            $array['technicianID'] = $technician->fetch()['technicianID'];
        }

        $logGateway = $container->get(LogGateway::class);
        $logGateway->addLog($session->get('gibbonSchoolYearID'), 'Help Desk', $gibbonPersonID, 'Issue Reincarnated', $array);

        //Success 0
        $URL .= '&return=success0';
        header("Location: {$URL}");
        exit();
    } else {
        $URL .= '/issues_view.php&return=error1';
        header("Location: {$URL}");
        exit();
    }
}
?>
