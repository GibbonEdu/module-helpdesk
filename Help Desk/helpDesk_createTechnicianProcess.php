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
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

require_once '../../gibbon.php';

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

    if (empty($person) || empty($group) || !$techGroupGateway->exists($group)) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        //Write to database
        $data = ['gibbonPersonID' => $person, 'groupID' => $group];

        $technicianGateway = $container->get(TechnicianGateway::class);

        if (!$technicianGateway->unique($data, ['gibbonPersonID'])) {
            $URL .= '&return=error7';
            header("Location: {$URL}");
            exit();
        }

        $technicianID = $technicianGateway->insert($data);
        if ($technicianID === false) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
            exit();
        }

        $logGateway = $container->get(LogGateway::class);
        $logGateway->addLog($gibbon->session->get('gibbonSchoolYearID'), 'Help Desk', $gibbon->session->get('gibbonPersonID'), 'Technician Added', ['gibbonPersonID' => $person, 'technicianID' => $technicianID]);

        //Success 0
        $URL .= "&technicianID=$technicianID&return=success0";
        header("Location: {$URL}");
        exit();
    }
}
?>
