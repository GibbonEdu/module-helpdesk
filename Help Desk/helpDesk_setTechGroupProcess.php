<?php
/*
Gibbon: the flexible, open school platform
Founded by Ross Parker at ICHK Secondary. Built by Ross Parker, Sandra Kuipers and the Gibbon community (https://gibbonedu.org/about/)
Copyright © 2010, Gibbon Foundation
Gibbon™, Gibbon Education Ltd. (Hong Kong)

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

$URL = $session->get('absoluteURL') . '/index.php?q=/modules/' . $session->get('module');

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicians.php')) {
    $URL .= '/issues_view.php&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    //Proceed!
    $technicianID = $_GET['technicianID'] ?? '';
    $group = $_POST['group'] ?? '';

    $techGroupGateway = $container->get(TechGroupGateway::class);
    $technicianGateway = $container->get(TechnicianGateway::class);

    if (empty($technicianID) || !$technicianGateway->exists($technicianID) || empty($group) || !$techGroupGateway->exists($group)) {
        $URL .= '/helpDesk_setTechGroup.php&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        //Write to database
        if (!$technicianGateway->update($technicianID, ['groupID' => $group])) {
            $URL .= '/helpDesk_setTechGroup.php&return=error2';
            header("Location: {$URL}");
            exit();
        }

        $logGateway = $container->get(LogGateway::class);
        $logGateway->addLog($session->get('gibbonSchoolYearID'), 'Help Desk', $session->get('gibbonPersonID'), 'Technician Group Set', ['technicianID' => $technicianID, 'groupID' => $group]);

        //Success 0
        $URL .= '/helpDesk_manageTechnicians.php&return=success0';
        header("Location: {$URL}");
        exit();
    }
}
?>
