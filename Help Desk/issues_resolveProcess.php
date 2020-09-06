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
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

//Bit of a cheat, but needed for gateway to work
$_POST['address'] = '/modules/Help Desk/issues_resolveProcess.php';

require_once '../../gibbon.php';

require_once './moduleFunctions.php';

$URL = $_SESSION[$guid]['absoluteURL'] . '/index.php?q=/modules/' . $_SESSION[$guid]['module'] . '/issues_view.php';

$techGroupGateway = $container->get(TechGroupGateway::class);

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_view.php') || !$techGroupGateway->getPermissionValue($gibbon->session->get('gibbonPersonID'), 'resolveIssue')) {
    //Fail 0
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    //Proceed!
    $issueID = $_GET['issueID'] ?? '';

    if (empty($issueID)){
        //Fail 3
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        if (relatedToIssue($connection2, $issueID, $_SESSION[$guid]['gibbonPersonID']) || $techGroupGateway->getPermissionValue($gibbon->session->get('gibbonPersonID'), 'resolveIssue')) {
            //Write to database
            $issueGateway = $container->get(IssueGateway::class);
            try {
                $gibbonModuleID = getModuleIDFromName($connection2, 'Help Desk');
                if ($gibbonModuleID == null) {
                    throw new PDOException('Invalid gibbonModuleID.');
                }

                $data = array('status' => 'Resolved');
                
                if (!$issueGateway->update($issueID, $data)) {
                    throw new PDOException('Failed to update issue.');
                }
            } catch (PDOException $e) {
                $URL .= '&return=error2';
                header("Location: {$URL}");
                exit();
            }

            $row = $issueGateway->getByID($issueID);

            $message = 'Issue #';
            $message .= $issueID;
            $message .= ' (' . $row['issueName'] . ') has been resolved.';

            $personIDs = getPeopleInvolved($connection2, $issueID);

            foreach ($personIDs as $personID) {
                if ($personID != $_SESSION[$guid]['gibbonPersonID']) {
                    setNotification($connection2, $guid, $personID, $message, 'Help Desk', '/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=' . $issueID);
                } 
            }

            $array['issueID'] = $issueID;

            $technicianGateway = $container->get(TechnicianGatway::class);
            $technician = $technicianGateway->selectBy('gibbonPersonID' => $gibbon->session->get('gibbonPersonID'));
            if ($technician->isNotEmpty()) {
                $array['technicianID'] = $technician->fetch()['technicianID'];
            }

            setLog($connection2, $gibbon->session->get('gibbonSchoolYearID'), $gibbonModuleID, $gibbon->session->get('gibbonPersonID'), 'Issue Resolved', $array, null);

            //Success 0
            $URL .= '&return=success0';
            header("Location: {$URL}");
            exit();
        } else {
            $URL .= '&return=error0';
            header("Location: {$URL}");
            exit();
        }
    }
}
?>
