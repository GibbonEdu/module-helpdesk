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

$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/Help Desk';

if (isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicianGroup.php')==false) {
    //Fail 0
    $URL .= '/issues_view.php&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    //Proceed!
    $URL .= '/helpDesk_manageTechnicianGroup.php';

    $techGroupGateway = $container->get(TechGroupGateway::class);

    if ($techGroupGateway->countAll() < 2) {
        $URL .= '&return=errorA';
        header("Location: {$URL}");
        exit();
    }

    $groupID = $_GET['groupID'] ?? '';
    if (empty($groupID) || !$techGroupGateway->exists($groupID)) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    }

    $newGroupID = $_POST['group'] ?? '';
    if (empty($newGroupID) || !$techGroupGateway->exists($newGroupID) || $groupID == $newGroupID) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    }

    if (!$techGroupGateway->deleteTechGroup($groupID, $newGroupID)) {
        $URL .= '&return=error2';
        header("Location: {$URL}");
        exit();
    }

    $gibbonModuleID = getModuleIDFromName($connection2, 'Help Desk');

    setLog($connection2, $gibbon->session->get('gibbonSchoolYearID'), $gibbonModuleID, $gibbon->session->get('gibbonPersonID'), 'Technician Group Removed', ['newGroupID' => $newGroupID], null);

    //Success 0
    $URL .= '&return=success0';
    header("Location: {$URL}");
    exit();
}
?>
