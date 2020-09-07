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

use Gibbon\Services\Format;
use Gibbon\Module\HelpDesk\Domain\IssueGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;

require_once '../../gibbon.php';

require_once './moduleFunctions.php';

$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $gibbon->session->get('module');

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_view.php')) {
    //Fail 0
    $URL .= '/issues_view.php&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    $permission = $_GET['permission'] ?? '';

    if (empty($permission) || !in_array($permission, ['assignIssue', 'reassignIssue'])) {
        $URL .= '/issues_view.php&return=error1';
        header("Location: {$URL}");
        exit();
    }
    $gibbonPersonID = $gibbon->session->get('gibbonPersonID');

    $techGroupGateway = $container->get(TechGroupGateway::class);
    if (!$techGroupGateway->getPermissionValue($gibbonPersonID, $permission)) {
        $URL .= '/issues_view.php&return=error0';
        header("Location: {$URL}");
        exit();
    }

    $issueID = $_GET['issueID'] ?? '';
    
    $issueGateway = $container->get(IssueGateway::class);
    $issue = $issueGateway->getByID($issueID);

    if (empty($issueID) || empty($issue)) {
        $URL .= '/issues_view.php&return=error1';
        header("Location: {$URL}");
        exit();
    }

    $technicianID = $_POST['technician'] ?? '';
    if (empty($technicianID)) {
        $URL .= "/issues_assign.php&issueID=$issueID&return=error1";
        header("Location: {$URL}");
        exit();
    }

    $technicianGateway = $container->get(TechnicianGateway::class);
    $technician = $technicianGateway->getTechnician($technicianID);

    if ($technician->isEmpty()) {
        $URL .= "/issues_assign.php&issueID=$issueID&return=error1";
        header("Location: {$URL}");
        exit();
    }
    $technician = $technician->fetch();

    try {
        $gibbonModuleID = getModuleIDFromName($connection2, 'Help Desk');
        if ($gibbonModuleID == null) {
            throw new PDOException('Invalid gibbonModuleID.');
        }
        
        $data = array('technicianID' => $technicianID, 'status' => 'Pending');

        if (!$issueGateway->update($issueID, $data)) {
            throw new PDOException('Could not update issue.');
        }
    } catch (PDOException $e) {
        $URL .= "/issues_assign.php&issueID=$issueID&technicianID=$technicianID&return=error2";
        header("Location: {$URL}");
        exit();
    }

    $assign = 'assigned';
    if ($permission == 'reassignIssue') {
        $assign = 'reassigned';
    }

    $message  = Format::name($technician['title'], $technician['preferredName'], $technician['surname'], 'Student');
    $message .= " has been $assign";
    $message .= ' Issue #';
    $message .= $issueID;
    $message .= ' (' . $issue['issueName'] . ').';

    $personIDs = $issueGateway->getPeopleInvolved($issueID);

    foreach($personIDs as $personID) {
        if ($personID != $gibbonPersonID) {
            setNotification($connection2, $guid, $personID, $message, 'Help Desk', '/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=' . $issueID);
        } 
    }    

    $array = array('issueID' => $issueID, 'technicainID' => $technicianID);

    setLog($connection2, $gibbon->session->get('gibbonSchoolYearID'), $gibbonModuleID, $gibbonPersonID, 'Technician Assigned', $array, null);

    $URL .= '/issues_view.php&return=success0';
    header("Location: {$URL}");
    exit();
}
?>
