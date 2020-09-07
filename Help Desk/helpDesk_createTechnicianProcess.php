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

use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

require_once '../../gibbon.php';

require_once './moduleFunctions.php';

$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $gibbon->session->get('module') . '/helpDesk_createTechnician.php';

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicians.php')) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    //Proceed!
    $person = $_POST['person'] ?? '';
    $group = $_POST['group'] ?? '';

    $techGroupGateway = $container->get(TechGroupGateway::class);

    if (empty($person) || empty($groupID) || !$techGroupGateway->exists($group)) {
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

            $data = array('gibbonPersonID' => $person, 'groupID' => $group);

            $technicianGateway = $container->get(TechnicianGateway::class);

            if (!$technicianGateway->unique($data, ['gibbonPersonID'])) {
                $URL .= '&return=error7';
                header("Location: {$URL}");
                exit();
            }

            $technicianID = $technicianGateway->insert($data);
            if ($technicianID === false) {
                throw new PDOException('Could not insert technician.');
            }
        } catch (PDOException $e) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
            exit();
        }

        $array = array('gibbonPersonID' => $person, 'technicianID' => $technicianID);
        setLog($connection2, $gibbon->session->get('gibbonSchoolYearID'), $gibbonModuleID, $gibbon->session->get('gibbonPersonID'), 'Technician Added', $array, null);

        //Success 0
        $URL .= '&return=success0';
        header("Location: {$URL}");
        exit();
    }
}
?>
