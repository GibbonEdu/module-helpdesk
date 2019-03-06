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

//Module includes
include __DIR__ . '/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_view.php") == false || !relatedToIssue($connection2, $_GET["issueID"], $_SESSION[$guid]["gibbonPersonID"])) {
    //Acess denied
    $page->addError('You do not have access to this action.');
} else {
    $page->breadcrumbs->add(__("Discuss Issue"), 'issues_discussView.php', ['issueID' => $issueID]);
    $page->breadcrumbs->add(__('Post Discuss'));
?>
    <form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_discussPostProccess.php?issueID="  . $_GET["issueID"]?>">
        <table class='smallIntBorder' cellspacing='0' style="width: 100%">
            <tr>
                <td colspan=2>
                    <b>
                        <?php print __('Comment') ?>
                    </b><br/>
                    <?php print getEditor($guid, true, "comment", "", 5, true, true, false); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-size: 90%"><i>* <?php print __("denotes a required field") ; ?></i></span>
                </td>
                <td class="right">
                    <input type="submit" value="<?php print __("Submit") ; ?>">
                </td>
            </tr>
        </table>
    </form>
<?php
}
?>
