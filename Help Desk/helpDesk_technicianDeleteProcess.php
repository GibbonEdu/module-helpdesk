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

use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;
use Gibbon\Module\HelpDesk\Domain\IssueGateway;

require_once '../../gibbon.php';

require_once './moduleFunctions.php';

$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $gibbon->session->get('module') . '/helpDesk_manageTechnicians.php';

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicians.php')) {
    //Fail 0
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    //Proceed!
    $technicianID = $_GET['technicianID'] ?? '';
    if (empty($technicianID)) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        //Write to database
        try {
            $gibbonModuleID = getModuleIDFromName($connection2, 'Help Desk');
            if ($gibbonModuleID == null) {
                throw new PDOException('Invalid gibbonModuleID.');
            }

            $technicianGateway = $container->get(TechnicianGateway::class);

            if (!$technicianGateway->exists($technicianID)) {
                $URL .= '&return=error1';
                header("Location: {$URL}");
                exit();
            }

            //TODO: Maybe start a transaction?
            $gibbonPersonID = $technicianGateway->getByID($technicianID)['gibbonPersonID'];
            if (!$technicianGateway->delete($technicianID)) {
                throw new PDOException('Failed to Delete Technician');
            }

            //TODO: In the future, maybe add and option to transfer these issues to another tech.
            $issueGateway = $container->get(IssueGateway::class);

            //Set any pending issues assigned to technician to unassigned and unset the technician.
            $keyAndValues = array('technicianID' => $technicianID, 'status' => 'Pending');
            $data = array('technicianID' => null, 'status' => 'Unassigned');
            if (!$issueGateway->updateWhere($keyAndValues, $data)) {
                throw new PDOException('Failed to update pending issues.');
            }

            //Removed technician from any resolved issue.
            $keyAndValues['status'] = 'Resolved';
            unset($data['status']);
            if (!$issueGateway->updateWhere($keyAndValues, $data)) {
                throw new PDOException('Failed to update resolved issues.');
            }
        } catch (PDOException $e) {
            //Fail 2
            $URL .= '&return=error2';
            header("Location: {$URL}");
        }

        setLog($connection2, $gibbon->session->get('gibbonSchoolYearID'), $gibbonModuleID, $gibbon->session->get('gibbonPersonID'), 'Technician Removed', array('gibbonPersonID' => $gibbonPersonID), null);

        //Success 0
        $URL .= '&return=success0';
        header("Location: {$URL}");
    }
}
?>
