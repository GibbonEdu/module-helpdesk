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

//Bit of a cheat, but needed for gateway to work
$_POST['address'] = '/modules/Help Desk/issues_acceptProcess.php';

include '../../gibbon.php';

include './moduleFunctions.php' ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]['timezone']);

$URL = $_SESSION[$guid]['absoluteURL'] . '/index.php?q=/modules/Help Desk/issues_view.php' ;

if (isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_view.php') == false) {
    //Fail 0
    $URL = $URL . '&return=error0' ;
    header("Location: {$URL}");
} else {
    //Proceed!
    $issueID = $_GET['issueID'];
    if ($issueID == '' || hasTechnicianAssigned($connection2, $issueID)) {
        //Fail 3
        $URL = $URL . '&return=error1' ;
        header("Location: {$URL}");
    } else {
        if (isTechnician($connection2, $_SESSION[$guid]['gibbonPersonID']) && getPermissionValue($connection2, $_SESSION[$guid]['gibbonPersonID'], 'acceptIssue')) {
            $technicianID = getTechnicianID($connection2, $_SESSION[$guid]['gibbonPersonID']);

            //Write to database
            try {
                $gibbonModuleID = getModuleIDFromName($connection2, 'Help Desk');
                if ($gibbonModuleID == null) {
                    throw new PDOException('Invalid gibbonModuleID.');
                }

                $data = array('technicianID' => $technicianID, 'status' => 'Pending');

                $issueGateway = $container->get(IssueGateway::class);
                $issueGateway->update($issueID, $data);
            } catch (PDOException $e) {
                //Fail 2
                $URL = $URL . '&return=error2' ;
                header("Location: {$URL}");
                exit();
            }

            setNotification($connection2, $guid, getOwnerOfIssue($connection2, $issueID)['gibbonPersonID'], 'A technician has started working on your isuse.', 'Help Desk', '/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=' . $issueID);
            setLog($connection2, $_SESSION[$guid]['gibbonSchoolYearID'], $gibbonModuleID, $_SESSION[$guid]['gibbonPersonID'], 'Issue Accepted', array('issueID' => $issueID, 'technicianID'=>$technicianID), null);

            //Success 1 aka Accepted
            $URL = $URL . '&issueID=$issueID&return=success0' ;
            header("Location: {$URL}");
        } else {
            $URL = $URL . '&return=error0' ;
            header("Location: {$URL}");
        }
    }
}
?>
