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

require_once '../../gibbon.php';

require_once './moduleFunctions.php';

$URL = $_SESSION[$guid]['absoluteURL'] . '/index.php?q=/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_setTechGroup.php';

if (!isActionAccessible($guid, $connection2, '/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_manageTechnicians.php')) {
    $URL .= '&return=fail0';
    header("Location: {$URL}");
    exit();
} else {
    //Proceed!
    $technicianID = $_GET['technicianID'] ?? '';
    $group = $_POST['group'] ?? '';

    if (empty($technicianID) || empty($group)) {
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
                //TODO: Change redirect?
                $URL .= '&return=error1';
                header("Location: {$URL}");
                exit();
            } else {
                $data = array('groupID' => $group);
                $technicianGateway->update($technicianID, $data);
            }
        }
        catch (PDOException $e) {
            $URL .= '&return=fail2';
            header("Location: {$URL}");
            exit();
        }

        setLog($connection2, $_SESSION[$guid]['gibbonSchoolYearID'], $gibbonModuleID, $_SESSION[$guid]['gibbonPersonID'], 'Technician Group Set', array('technicianID' => $technicianID, 'groupID' => $group), null);

        //Success 0
        $URL = $_SESSION[$guid]['absoluteURL'] . '/index.php?q=/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_manageTechnicians.php&return=success0';
        header("Location: {$URL}");
        exit();
    }
}
?>
