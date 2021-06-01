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
use Gibbon\Module\HelpDesk\Domain\IssueDiscussGateway;
use Gibbon\Module\HelpDesk\Domain\IssueGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

require_once '../../gibbon.php';

$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $gibbon->session->get('module');

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_view.php')) {
    $URL .= '/issues_view.php&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    $issueID = $_GET['issueID'] ?? '';

    $issueGateway = $container->get(IssueGateway::class);
    $issue = $issueGateway->getByID($issueID);

    if (empty($issueID) || empty($issue)) {
        $URL .= '/issues_view.php&return=error1';
        header("Location: {$URL}");
        exit();
    }

    $gibbonPersonID = $gibbon->session->get('gibbonPersonID');

    $techGroupGateway = $container->get(TechGroupGateway::class);

    if ($issueGateway->isRelated($issueID, $gibbonPersonID) || $techGroupGateway->getPermissionValue($gibbonPersonID, 'fullAccess')) {
      //Proceed!
        $URL .= "/issues_discussView.php&issueID=$issueID";

        if ($issue['status'] != 'Pending') {
            $URL .= '&return=error0';
            header("Location: {$URL}");
            exit();
        }

        $comment = $_POST['comment'] ?? '';

        if (empty($comment)) {
            $URL .= '&return=error1';
            header("Location: {$URL}");
            exit();
        }

        $issueDiscussGateway = $container->get(IssueDiscussGateway::class);

        $issueDiscussID = $issueDiscussGateway->insert([
            'issueID' => $issueID,
            'comment' => $comment,
            'timestamp' => date('Y-m-d H:i:s'),
            'gibbonPersonID' => $gibbonPersonID
        ]);
        
        if ($issueDiscussID === false) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
            exit();
        }
       
        $technicianGateway = $container->get(TechnicianGateway::class);
        $technician = $technicianGateway->getTechnicianByPersonID($gibbonPersonID);

        $isTech = $technician->isNotEmpty() && ($issue['gibbonPersonID'] != $gibbonPersonID);

        //Send Notification
        $notificationGateway = $container->get(NotificationGateway::class);
        $notificationSender = new NotificationSender($notificationGateway, $gibbon->session); 

        $message = __('A new message has been added to Issue #') . $issueID . ' (' . $issue['issueName'] . ').';

        $personIDs = $issueGateway->getPeopleInvolved($issueID);

        $notificationGateway = $container->get(NotificationGateway::class);
        $notificationSender = new NotificationSender($notificationGateway, $gibbon->session);
 
        foreach ($personIDs as $personID) {
            if ($personID != $gibbonPersonID) {
                $notificationSender->addNotification($personID, $message, 'Help Desk', $absoluteURL . '/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=' . $issueID);
            } 
        }

        $notificationSender->sendNotifications();

        //Log
        $array = ['issueDiscussID' => $issueDiscussID];

        if ($isTech) {
            $array['technicianID'] = $technician->fetch()['technicianID'];
        } 

        $logGateway = $container->get(LogGateway::class);
        $logGateway->addLog($gibbon->session->get('gibbonSchoolYearID'), 'Help Desk', $gibbonPersonID, 'Discussion Posted', $array);

        $URL .= '&return=success0';
        header("Location: {$URL}");
        exit();
    } else {
        //Fail 0 aka No permission
        $URL .= '/issues_view.php&return=error0';
        header("Location: {$URL}");
        exit();
    }
}
?>
