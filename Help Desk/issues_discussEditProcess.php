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

require_once '../../gibbon.php';

require_once './moduleFunctions.php';


$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/Help Desk/' ;

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_view.php')) {
    //Fail 0
      $URL .= 'issues_view.php&return=error0' ;
    header("Location: {$URL}");
    exit();
} else {

    if (isset($_GET['issueID'])) {
        $issueID = $_GET['issueID'];
        $URL .= 'issues_discussView.php&issueID=' . $issueID ;
    } else {
        $URL .= 'issues_view.php&return=error1' ;
        header("Location: {$URL}");
        exit();
    }

    if (!isPersonsIssue($connection2, $issueID, $gibbon->session->get('gibbonPersonID'))) {
        $URL .= 'issues_view.php&return=error0' ;
        header("Location: {$URL}");
        exit();
    }

    if (isset($_POST['privacySetting'])) {
        $privacySetting = $_POST['privacySetting'];
    } else {
        $URL .= '&return=error1' ;
        header("Location: {$URL}");
          exit();
    }

    try {
        $data = array('privacySetting' => $privacySetting);

        $issueGateway = $container->get(IssueGateway::class);
        $issueGateway->update($issueID, $data);
    } catch (PDOException $e) {
        $URL .= '&return=error2' ;
        header("Location: {$URL}");
        exit();
    }

      $URL .= '&return=success0' ;
    header("Location: {$URL}");
}
?>
