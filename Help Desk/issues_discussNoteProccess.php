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

use Gibbon\Domain\System\SettingGateway;
use Gibbon\Module\HelpDesk\Domain\IssueGateway;
use Gibbon\Module\HelpDesk\Domain\IssueNoteGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

require_once '../../gibbon.php';

$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $gibbon->session->get('module');

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_view.php')) {
    $URL .= '/issues_view.php&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    $issueID = $_POST['issueID'] ?? '';

    $issueGateway = $container->get(IssueGateway::class);
    $issue = $issueGateway->getByID($issueID);

    if (empty($issue)) {
        $URL .= '/issues_view.php&return=error1';
        header("Location: {$URL}");
        exit();
    }

    $gibbonPersonID = $gibbon->session->get('gibbonPersonID');

    $technicianGateway = $container->get(TechnicianGateway::class);
    $techGroupGateway = $container->get(TechGroupGateway::class);
    $settingGateway = $container->get(SettingGateway::class);

    $technician = $technicianGateway->getTechnicianByPersonID($gibbonPersonID);

    if ($technician->isNotEmpty() //Is tech
        && !($gibbonPersonID == $issue['gibbonPersonID']) //Not owner
        && ($issueGateway->isRelated($issueID, $gibbonPersonID) || $techGroupGateway->getPermissionValue($gibbonPersonID, 'fullAccess')) //Has access
        && $settingGateway->getSettingByScope('Help Desk', 'techNotes') //Setting is enabled
    ) {
      //Proceed!
        $URL .= "/issues_discussView.php&issueID=$issueID";

        $note = $_POST['techNote'] ?? '';

        if (empty($note)) {
            $URL .= '&return=error1';
            header("Location: {$URL}");
            exit();
        }

        try {
            $gibbonModuleID = getModuleIDFromName($connection2, 'Help Desk');
            if ($gibbonModuleID == null) {
                throw new PDOException('Invalid gibbonModuleID.');
            }

            $issueNoteGateway = $container->get(IssueNoteGateway::class);

            $issueNoteID = $issueNoteGateway->insert([
                'issueID' => $issueID,
                'note' => $note,
                'timestamp' => date('Y-m-d H:i:s'),
                'gibbonPersonID' => $gibbonPersonID
            ]);
            
            if ($issueNoteID === false) {
                throw new PDOException('Could not insert note.');
            }
        } catch (PDOException $e) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
            exit();
        }

        $URL .= '&return=success0';
        header("Location: {$URL}");
        exit();
    } else {
        //Fail 0 aka No permission
        $URL .= '/issues_view.php&return=error0';
        header("Location: {$URL}");
        exit();
    }
}
?>
