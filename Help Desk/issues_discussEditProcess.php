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

require_once '../../gibbon.php';

require_once './moduleFunctions.php';

$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/Help Desk';

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_view.php')) {
    $URL .= '/issues_view.php&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    $issueID = $_GET['issueID'] ?? '';

    $issueGateway = $container->get(IssueGateway::class);
    $issue = $issueGateway->getByID($issueID);

    if (empty($issueID) || empty($issue)) {
        $URL .= '/issues_view.php&return=error1';
        header("Location: {$URL}");
        exit();
    }

    $gibbonPersonID =  $gibbon->session->get('gibbonPersonID');
    $techGroupGateway = $container->get(TechGroupGateway::class);

    if ($issue['gibbonPersonID'] != $gibbonPersonID && !$techGroupGateway->getPermissionValue($gibbonPersonID, 'fullAccess')) {
        $URL .= "/issues_discussView.php&issueID=$issueID&return=error0";
        header("Location: {$URL}");
        exit();
    }

    $privacySetting = $_POST['privacySetting'] ?? '';

    if (!in_array($privacySetting, privacyOptions())) {
        $URL .= "/issues_discussEdit.php&issueID=$issueID&return=error1";
        header("Location: {$URL}");
        exit();
    }

    try {
        $data = array('privacySetting' => $privacySetting);
        
        if (!$issueGateway->update($issueID, $data)) {
            throw new PDOException('Could not update issue.');
        }
    } catch (PDOException $e) {
        $URL .= "/issues_discussEdit.php&issueID=$issueID&return=error2";
        header("Location: {$URL}");
        exit();
    }

    $URL .= "/issues_discussView.php&issueID=$issueID&return=success0";
    header("Location: {$URL}");
    exit();
}
?>
