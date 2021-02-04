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
$_POST['address'] = '/modules/Help Desk/issues_acceptProcess.php';

require_once '../../gibbon.php';

require_once './moduleFunctions.php';

$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $gibbon->session->get('module');

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

    if (empty($issueID) || empty($issue) || $issue['technicianID'] != null) {
        //Fail 3
        $URL .= '/issues_view.php&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        $gibbonPersonID = $gibbon->session->get('gibbonPersonID');

        $techGroupGateway = $container->get(TechGroupGateway::class);

        $technicianGateway = $container->get(TechnicianGateway::class);
        $technician = $technicianGateway->getTechnicianByPersonID($gibbonPersonID);

        if ($technician->isNotEmpty() && $techGroupGateway->getPermissionValue($gibbonPersonID, 'acceptIssue')) {
            $URL .= '/issues_discussView.php&issueID=' . $issueID;  
    
            //Write to database
            $technicianID = $technician->fetch()['technicianID'];
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
                //Fail 2
                $URL .= '&return=error2';
                header("Location: {$URL}");
                exit();
            }

            setNotification($connection2, $guid, $issue['gibbonPersonID'], 'A technician has started working on your isuse.', 'Help Desk', '/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=' . $issueID);

            $array = array('issueID' => $issueID, 'technicianID' => $technicianID);

            setLog($connection2, $gibbon->session->get('gibbonSchoolYearID'), $gibbonModuleID, $gibbonPersonID, 'Issue Accepted', $array, null);

            //Success 1 aka Accepted
            $URL .= '&return=success0';
            header("Location: {$URL}");
            exit();
        } else {
            $URL .= '/issues_view.php&return=error0';
            header("Location: {$URL}");
            exit();
        }
    }
}
?>
