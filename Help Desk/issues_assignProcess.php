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

use Gibbon\Module\HelpDesk\Domain\IssueGateway;

require_once '../../gibbon.php';

require_once './moduleFunctions.php';

$URL = $_SESSION[$guid]['absoluteURL'] . '/index.php?q=/modules/' . $_SESSION[$guid]['module'];

if (isset($_GET['permission'])) {
    $permission = $_GET['permission'];
} else {
    $URL .= '/issues_view.php&return=error1';
    header("Location: {$URL}";
    exit();
}

if (!isActionAccessible($guid, $connection2, '/modules/' . $_SESSION[$guid]['module'] . '/issues_view.php') || !getPermissionValue($connection2, $_SESSION[$guid]['gibbonPersonID'], $permission)) {
    //Fail 0
    $URL .= '/issues_view.php&return=error0';
    header("Location: {$URL}";
    exit();
} else {
    if (isset($_GET['issueID'])) {
        $issueID = $_GET['issueID'];
    } else {
        $URL .= '/issues_view.php&return=error1';
        header("Location: {$URL}";
        exit();
    }

    // Proceed!
    if (isset($_POST['technician'])) {
        $technicianID = $_POST['technician'];
    } else {
        $URL .= '/issues_assign.php&issueID=$issueID&return=error1';
        header("Location: {$URL}";
        exit();
    }

    if ($technicianID == null || $technicianID == '') {
        $URL .= '/issues_assign.php&issueID=$issueID&return=error1';
        header("Location: {$URL}";
        exit();
    }

    try {
        $gibbonModuleID = getModuleIDFromName($connection2, 'Help Desk');
        if ($gibbonModuleID == null) {
            throw new PDOException('Invalid gibbonModuleID.');
        }
        $data = array('technicianID' => $technicianID, 'status' => 'Pending');

        $issueGateway = $container->get(IssueGateway::class);
        $issueGateway->update($issueID, $data);
    } catch (PDOException $e) {
        $URL .= '/issues_assign.php&issueID=$issueID&technicianID=$technicianID&return=error2' ;
        header("Location: {$URL}";
        exit();
    }

    $row = getIssue($connection2, $issueID);

    $assign = 'assigned';
    if ($permission == 'reassignIssue') {
        $assign = 'reassigned';
    }
    $tech = getTechWorkingOnIssue($connection2, $issueID);
    $message  = $tech['preferredName'] . ' ' . $tech['surname'];
    $message .= ' has been $assign';
    $message .= ' Issue #';
    $message .= $issueID;
    $message .= ' (' . $row['issueName'] . ').';

    $personIDs = getPeopleInvolved($connection2, $issueID);

    foreach($personIDs as $personID) {
        if ($personID != $_SESSION[$guid]['gibbonPersonID']) {
            setNotification($connection2, $guid, $personID, $message, 'Help Desk', '/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=' . $issueID);
        } 
    }    

    setLog($connection2, $_SESSION[$guid]['gibbonSchoolYearID'], $gibbonModuleID, $_SESSION[$guid]['gibbonPersonID'], 'Technician Assigned', array('issueID' => $issueID, 'technicainID'=>$technicianID), null);

    $URL .= '/issues_view.php&return=success0' ;
    header("Location: {$URL}";
}
?>
