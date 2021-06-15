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

use Gibbon\Domain\System\LogGateway;
use Gibbon\Domain\User\UserGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

require_once '../../gibbon.php';

$URL = $session->get('absoluteURL') . '/index.php?q=/modules/' . $session->get('module') . '/helpDesk_createTechnician.php';

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicians.php')) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    //Proceed!
    $person = $_POST['person'] ?? '';
    $group = $_POST['group'] ?? '';

    $userGateway = $container->get(UserGateway::class);
    $techGroupGateway = $container->get(TechGroupGateway::class);

    //Check that person and group exist
    if (empty($person) || !$userGateway->exists($person)
        || empty($group) || !$techGroupGateway->exists($group)
    ) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        //Write to database
        $data = ['gibbonPersonID' => $person, 'groupID' => $group];

        $technicianGateway = $container->get(TechnicianGateway::class);

        //Check that user is not already a technician
        if (!$technicianGateway->unique($data, ['gibbonPersonID'])) {
            $URL .= '&return=error7';
            header("Location: {$URL}");
            exit();
        }

        //Insert the new technician
        $technicianID = $technicianGateway->insert($data);
        if ($technicianID === false) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
            exit();
        }

        //Log
        $logGateway = $container->get(LogGateway::class);
        $logGateway->addLog($session->get('gibbonSchoolYearID'), 'Help Desk', $session->get('gibbonPersonID'), 'Technician Added', ['gibbonPersonID' => $person, 'technicianID' => $technicianID]);

        //Success 0
        $URL .= "&technicianID=$technicianID&return=success0";
        header("Location: {$URL}");
        exit();
    }
}
?>
