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

@session_start() ;

include "./modules/Help Desk/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicianGroup.php") == FALSE) {
    //Acess denied
    print "<div class='error'>" ;
        print __($guid, "You do not have access to this action.") ;
    print "</div>" ;
} else {
    //Proceed!
    print "<div class='trail'>" ;
        print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __($guid, getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_manageTechnicianGroup.php'>" . __($guid, "Manage Technician Groups") . "</a> > </div><div class='trailEnd'>" . __($guid, 'Edit Technician Group') . "</div>" ;
    print "</div>" ;  
  
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    if (isset($_GET['groupID'])) {
        $groupID = $_GET['groupID'];
    }

    try {
        $data = array("groupID" => $groupID);
        $sql = "SELECT * FROM helpDeskTechGroups WHERE groupID = :groupID";
        $result = $connection2->prepare($sql);
        $result->execute($data);
        $row = $result->fetch();
    } catch (PDOException $e) {
    }

    print "<h3>";
        print "Permissons for Technician Group: " . $row["groupName"];
    print "</h3>";
    ?>
    <form method = "post" action = "<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/Help Desk/helpDesk_editTechnicianGroupProcess.php?groupID=$groupID" ?>">
        <table class='smallIntBorder' cellspacing='0' style="width: 100%">  
            <tr>
                <td style='width: 275px'>
                    <b>Group Name</b><br/>
                    <span style="font-size: 90%"><i></i></span>
                </td>
                <td class="right">
                    <input name="groupName" id="groupName" maxlength=100 value="<?php print $row["groupName"] ?>" type="text" data-minlength="1" style="width: 300px">
                    <script type="text/javascript">
                        var groupName=new LiveValidation('groupName');
                        groupName.add(Validate.Presence);
                    </script>
                </td>
            </tr>
            <tr>
                <td style='width: 275px'>
                    <b>Allow View All Issues</b><br/>
                    <span style="font-size: 90%"><i>Allow the technician to see all the issues instead of just their issues and the issues they working on.</i></span>
                </td>
                <td class="right">
                    <?php
                        $checked = '';
                        if ($row['viewIssue'] == TRUE) {
                            $checked = 'checked';
                        }
                        print "<input type='checkbox' name='viewIssue' id='viewIssue' $checked />" ;
                    ?>
                </td>
            </tr>
            <tr>
                <td style='width: 275px'>
                    <b>View Issues Status</b><br/>
                    <span style="font-size: 90%"><i>Choose what issue statuses the technicians can view.</i></span>
                </td>
                <td class="right">
                    <select name='viewIssueStatus' id='viewIssueStatus' style='width:302px'>
                    <?php
                            if ($row['viewIssueStatus'] == "All") {
                                print "<option selected value='All'>All</option>";
                            } else {
                                print "<option value='All'>All</option>";
                            }

                            if ($row['viewIssueStatus'] == "UP") {
                                print "<option selected value='UP'>Unassigned & Pending</option>";
                            } else { 
                                print "<option value='UP'>Unassigned & Pending</option>"; 
                            }

                            if ($row['viewIssueStatus'] == "PR") {
                                print "<option selected value='PR'>Pending & Resolved</option>";
                            } else { 
                                print "<option value='PR'>Pending & Resolved</option>";
                            }

                            if ($row['viewIssueStatus'] == "Pending") {
                                print "<option selected value='Pending'>Pending</option>";
                            } else { 
                                print "<option value='Pending'>Pending</option>";
                            }  
                    ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td style='width: 275px'>
                    <b>Allow Assign Issues</b><br/>
                    <span style="font-size: 90%"><i>Allow the technician to assign issues to other technicians.</i></span>
                </td>
                <td class="right">
                    <?php
                        $checked = '';
                        if ($row['assignIssue'] == TRUE) { $checked = 'checked'; }
                        print "<input type='checkbox' name='assignIssue' id='assignIssue' $checked />" ;
                    ?>
                </td>
            </tr>
            <tr>
                <td style='width: 275px'>
                    <b>Allow Accept Issues</b><br/>
                    <span style="font-size: 90%"><i>Allow the technician to accept issues to work on.</i></span>
                </td>
                <td class="right">
                    <?php
                        $checked = 'checked';
                        if ($row['acceptIssue'] == FALSE) { $checked = ''; }
                        print "<input type='checkbox' name='acceptIssue' id='acceptIssue' $checked />" ;
                    ?>
                </td>
            </tr>
            <tr>
                <td style='width: 275px'>
                    <b>Allow Resolve Issues</b><br/>
                    <span style="font-size: 90%"><i>Allow the technician to resolve an issue they are working on.</i></span>
                </td>
                <td class="right">
                    <?php
                        $checked = 'checked';
                        if ($row['resolveIssue'] == FALSE) { $checked = ''; }
                        print "<input type='checkbox' name='resolveIssue' id='resolveIssue' $checked />" ;
                    ?>
                </td>
            </tr>
            <tr>
                <td style='width: 275px'>
                    <b>Allow Create Issues For Other</b><br/>
                    <span style="font-size: 90%"><i>Allow the technician to create issues issues on behalf of others.</i></span>
                </td>
                <td class="right">
                    <?php
                        $checked = 'checked';
                        if ($row['createIssueForOther'] == FALSE) { $checked = ''; }
                        print "<input type='checkbox' name='createIssueForOther' id='createIssueForOther' $checked />" ;
                    ?>
                </td>
            </tr>
            <tr>
                <td style='width: 275px'>
                    <b>Reassign Issue</b><br/>
                    <span style="font-size: 90%"><i>This will allow the technician to reassign an issue to another technician.</i></span>
                </td>
                <td class="right">
                    <?php
                        $checked = '';
                        if ($row['reassignIssue'] == TRUE) { $checked = 'checked'; }
                        print "<input type='checkbox' name='reassignIssue' id='reassignIssue' $checked />" ;
                    ?>
                </td>
            </tr>
            <tr>
                <td style='width: 275px'>
                    <b>Reincarnate Issue</b><br/>
                    <span style="font-size: 90%"><i>This will allow the technician to bring back an issue that has been resolved.</i></span>
                </td>
                <td class="right">
                    <?php
                        $checked = 'checked';
                        if ($row['reincarnateIssue'] == FALSE) { $checked = ''; }
                        print "<input type='checkbox' name='reincarnateIssue' id='reincarnateIssue' $checked />" ;
                    ?>
                </td>
            </tr>
            <tr>
                <td style='width: 275px'>
                    <b>Full Access</b><br/>
                    <span style="font-size: 90%"><i>Enabling this will give the technician full access. This will override almost all the checks the system has in place. It will allow the technician to resolve any issues, work on issues they are not assigned to and all the other things listed above.</i></span>
                </td>
                <td class="right">
                    <?php
                        $checked = '';
                        if ($row['fullAccess'] == TRUE) { $checked = 'checked'; }
                        print "<input type='checkbox' name='fullAccess' id='fullAccess' $checked />" ;
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-size: 90%"><i>* <?php print __($guid, "denotes a required field") ; ?></i></span>
                </td>
                <td class="right">
                    <input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
                    <input type="submit" value="<?php print __($guid, "Submit") ; ?>">
                </td>
            </tr>
        </table>
    </form>
<?php
}
?>
