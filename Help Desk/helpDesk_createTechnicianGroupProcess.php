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

require_once '../../gibbon.php';

require_once './moduleFunctions.php';

$URL = $_SESSION[$guid]['absoluteURL'] . '/index.php?q=/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_createTechnicianGroup.php';

if (!isActionAccessible($guid, $connection2, '/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_manageTechnicianGroup.php')) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    if (isset($_POST['groupName'])) {
        $groupName = $_POST['groupName'];
    }

    if ($groupName == '') {
        $URL .= '&return=error1';
        header("Location: {$URL}");
    } else {
        //Write to database

        try {
            $gibbonModuleID = getModuleIDFromName($connection2, 'Help Desk');
            if ($gibbonModuleID == null) {
                throw new PDOException('Invalid gibbonModuleID.');
            }

            $data = array('groupName' => $groupName);

            $techGroupGateway = $container->get(TechGroupGateway::class);

            if (!$techGroupGateway->unique($data, ['groupName'])) {
                $URL .= '&return=error7';
                header("Location: {$URL}");
                exit();
            }

            $techGroupGateway->insert($data);
        } catch (PDOException $e) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
            exit();
        }

        $groupID = $connection2->lastInsertId();
        setLog($connection2, $_SESSION[$guid]['gibbonSchoolYearID'], $gibbonModuleID, $_SESSION[$guid]['gibbonPersonID'], 'Technician Group Added', array('groupID'=>$groupID), null);

        //Success 0
        $URL .= '&groupID=$groupID&return=success0';
        header("Location: {$URL}");
    }
}
?>
