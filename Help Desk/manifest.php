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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//Basic variables
$name="Help Desk";
$description="A virtual help desk module for Gibbon.";
$entryURL="issues_view.php";
$type="Additional";
$category="Other";
$version="2.0.02";
$author="Ray Clark, Ashton Power & Adrien Tremblay";
$url="https://github.com/GibbonEdu/module-helpDesk";

//Module tables & gibbonSettings entries
$tables = 0;

$moduleTables[$tables++]="CREATE TABLE `helpDeskIssue` (
    `issueID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
    `technicianID` int(4) unsigned zerofill DEFAULT NULL,
    `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
    `issueName` varchar(55) NOT NULL,
    `description` text NOT NULL,
    `date` date NOT NULL,
    `status` ENUM('Unassigned','Pending','Resolved') DEFAULT 'Unassigned',
    `category` varchar(100) DEFAULT NULL,
    `priority` varchar(100) DEFAULT NULL,
    `gibbonSchoolYearID` int(3) unsigned zerofill NOT NULL,
    `createdByID` int(12) unsigned zerofill NOT NULL,
    `subcategoryID` int(4) UNSIGNED ZEROFILL DEFAULT NULL,
    `gibbonSpaceID` int(5) UNSIGNED ZEROFILL DEFAULT NULL,
    PRIMARY KEY (`issueID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$moduleTables[$tables++]="CREATE TABLE `helpDeskIssueDiscuss` (
    `issueDiscussID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
    `issueID` int(12) unsigned zerofill NOT NULL,
    `comment` text NOT NULL,
    `timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
    `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
    PRIMARY KEY (`issueDiscussID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$moduleTables[$tables++]="CREATE TABLE `helpDeskIssueNotes` (
    `issueNoteID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
    `issueID` int(12) unsigned zerofill NOT NULL,
    `note` text NOT NULL,
    `timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
    `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
    PRIMARY KEY (`issueNoteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


$moduleTables[$tables++]="CREATE TABLE `helpDeskTechnicians` (
    `technicianID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
    `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
    `groupID` int(4) unsigned zerofill NOT NULL,
    PRIMARY KEY (`technicianID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$moduleTables[$tables++]="CREATE TABLE `helpDeskTechGroups` (
    `groupID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
    `groupName` varchar(55) NOT NULL,
    `viewIssue` boolean DEFAULT 1,
    `viewIssueStatus` ENUM('All', 'UP', 'PR', 'Pending') DEFAULT 'All',
    `assignIssue` boolean DEFAULT 0,
    `acceptIssue` boolean DEFAULT 1,
    `resolveIssue` boolean DEFAULT 1,
    `createIssueForOther` boolean DEFAULT 1,
    `fullAccess` boolean DEFAULT 0,
    `reassignIssue` boolean DEFAULT 0,
    `reincarnateIssue` boolean DEFAULT 1,
    PRIMARY KEY (`groupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$moduleTables[$tables++]="CREATE TABLE `helpDeskDepartments` (
    `departmentID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
    `departmentName` varchar(55) NOT NULL,
    `departmentDesc` varchar(128) NOT NULL,
    PRIMARY KEY (`departmentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$moduleTables[$tables++]="CREATE TABLE `helpDeskDepartmentPermissions` (
    `departmentPermissionsID` int(4) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
    `departmentID` int(4) UNSIGNED ZEROFILL NOT NULL,
    `gibbonRoleID` int(3) UNSIGNED ZEROFILL NOT NULL,
    PRIMARY KEY (`departmentPermissionsID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$moduleTables[$tables++]="CREATE TABLE `helpDeskGroupDepartment` (
    `groupDepartmentID` int(4) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
    `groupID` int(4) UNSIGNED ZEROFILL NOT NULL,
    `departmentID` int(4) UNSIGNED ZEROFILL NOT NULL,
    PRIMARY KEY (`groupDepartmentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$moduleTables[$tables++]="CREATE TABLE `helpDeskSubcategories` (
    `subcategoryID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
    `departmentID` int(4) unsigned zerofill NOT NULL,
    `subcategoryName` varchar(55) NOT NULL,
    PRIMARY KEY (`subcategoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$moduleTables[$tables++]="INSERT INTO `helpDeskTechGroups` (`groupID`, `groupName`, `viewIssue`, `viewIssueStatus`, `assignIssue`, `acceptIssue`, `resolveIssue`, `createIssueForOther`, `fullAccess`, `reassignIssue`, `reincarnateIssue`)
    VALUES
    (NULL, 'Head Technician', 1, 'All', 1, 1, 1, 1, 1, 1, 1),
    (NULL, 'Technician', 1, 'All', 0, 1, 1, 1, 0, 0, 1)";

$moduleTables[$tables++]="INSERT INTO `gibbonSetting` (`gibbonSettingID`, `scope`, `name`, `nameDisplay`, `description`, `value`)
    VALUES
    (NULL, 'Help Desk', 'issuePriority', 'Issue Priority', 'Different priority levels for the issues.', ''),
    (NULL, 'Help Desk', 'issuePriorityName', 'Issue Priority Name', 'Different name for the Issue Priority', 'Priority'),
    (NULL, 'Help Desk', 'issueCategory', 'Issue Category', 'Different categories for the issues.', 'Network,Hardware,Software,Application'),
    (NULL, 'Help Desk', 'simpleCategories', 'Simple Categories', 'Whether to use Simple Categories or Not.', TRUE),
    (NULL, 'Help Desk', 'techNotes', 'Technician Notes', 'Whether technicians can leave notes on issues that only other technicians can see.', FALSE)";

//Action rows
//One array per action
$actionCount = 0;

$actionRows[$actionCount]["name"]="Create Issue"; //The name of the action (appears to user in the right hand side module menu)
$actionRows[$actionCount]["precedence"]="0"; //If it is a grouped action, the precedence controls which is highest action in group
$actionRows[$actionCount]["category"]="Issues"; //Optional: subgroups for the right hand side module menu
$actionRows[$actionCount]["description"]="Allows the user to submit an issue to be resolved by the help desk staff."; //Text description
$actionRows[$actionCount]["URLList"]="issues_create.php";
$actionRows[$actionCount]["entryURL"]="issues_create.php";
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y"; //Default permission for built in role Admin
$actionRows[$actionCount]["defaultPermissionTeacher"]="Y"; //Default permission for built in role Teacher
$actionRows[$actionCount]["defaultPermissionStudent"]="Y"; //Default permission for built in role Student
$actionRows[$actionCount]["defaultPermissionParent"]="N"; //Default permission for built in role Parent
$actionRows[$actionCount]["defaultPermissionSupport"]="Y"; //Default permission for built in role Support
$actionRows[$actionCount]["categoryPermissionStaff"]="Y"; //Should this action be available to user roles in the Staff category?
$actionRows[$actionCount]["categoryPermissionStudent"]="Y"; //Should this action be available to user roles in the Student category?
$actionRows[$actionCount]["categoryPermissionParent"]="Y"; //Should this action be available to user roles in the Parent category?
$actionRows[$actionCount]["categoryPermissionOther"]="Y"; //Should this action be available to user roles in the Other category?

$actionCount++;
$actionRows[$actionCount]["name"]="Issues";
$actionRows[$actionCount]["precedence"]="0";
$actionRows[$actionCount]["category"]="Issues";
$actionRows[$actionCount]["description"]= "Gives the user access to the Issues section.";
$actionRows[$actionCount]["URLList"]="issues_view.php";
$actionRows[$actionCount]["entryURL"]="issues_view.php";
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y";
$actionRows[$actionCount]["defaultPermissionTeacher"]="Y";
$actionRows[$actionCount]["defaultPermissionStudent"]="Y";
$actionRows[$actionCount]["defaultPermissionParent"]="N";
$actionRows[$actionCount]["defaultPermissionSupport"]="Y";
$actionRows[$actionCount]["categoryPermissionStaff"]="Y";
$actionRows[$actionCount]["categoryPermissionStudent"]="Y";
$actionRows[$actionCount]["categoryPermissionParent"]="Y";
$actionRows[$actionCount]["categoryPermissionOther"]="Y";

$actionCount++;
$actionRows[$actionCount]["name"]="Help Desk Settings";
$actionRows[$actionCount]["precedence"]="0";
$actionRows[$actionCount]["category"]="Admin";
$actionRows[$actionCount]["description"]="Allows the user to edit the settings for the module.";
$actionRows[$actionCount]["URLList"]="helpDesk_settings.php";
$actionRows[$actionCount]["entryURL"]="helpDesk_settings.php";
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y";
$actionRows[$actionCount]["defaultPermissionTeacher"]="N";
$actionRows[$actionCount]["defaultPermissionStudent"]="N";
$actionRows[$actionCount]["defaultPermissionParent"]="N";
$actionRows[$actionCount]["defaultPermissionSupport"]="N";
$actionRows[$actionCount]["categoryPermissionStaff"]="Y";
$actionRows[$actionCount]["categoryPermissionStudent"]="N";
$actionRows[$actionCount]["categoryPermissionParent"]="N";
$actionRows[$actionCount]["categoryPermissionOther"]="Y";

$actionCount++;
$actionRows[$actionCount]["name"]="Manage Technicians";
$actionRows[$actionCount]["precedence"]="0";
$actionRows[$actionCount]["category"]="Technician";
$actionRows[$actionCount]["description"]="Allows the user to manage the Technicians.";
$actionRows[$actionCount]["URLList"]="helpDesk_manageTechnicians.php";
$actionRows[$actionCount]["entryURL"]="helpDesk_manageTechnicians.php";
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y";
$actionRows[$actionCount]["defaultPermissionTeacher"]="N";
$actionRows[$actionCount]["defaultPermissionStudent"]="N";
$actionRows[$actionCount]["defaultPermissionParent"]="N";
$actionRows[$actionCount]["defaultPermissionSupport"]="N";
$actionRows[$actionCount]["categoryPermissionStaff"]="Y";
$actionRows[$actionCount]["categoryPermissionStudent"]="N";
$actionRows[$actionCount]["categoryPermissionParent"]="N";
$actionRows[$actionCount]["categoryPermissionOther"]="Y";

$actionCount++;
$actionRows[$actionCount]["name"]="Manage Technician Groups";
$actionRows[$actionCount]["precedence"]="0";
$actionRows[$actionCount]["category"]="Technician";
$actionRows[$actionCount]["description"]="Allows the user to manage the Technicians Groups.";
$actionRows[$actionCount]["URLList"]="helpDesk_manageTechnicianGroup.php";
$actionRows[$actionCount]["entryURL"]="helpDesk_manageTechnicianGroup.php";
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y";
$actionRows[$actionCount]["defaultPermissionTeacher"]="N";
$actionRows[$actionCount]["defaultPermissionStudent"]="N";
$actionRows[$actionCount]["defaultPermissionParent"]="N";
$actionRows[$actionCount]["defaultPermissionSupport"]="N";
$actionRows[$actionCount]["categoryPermissionStaff"]="Y";
$actionRows[$actionCount]["categoryPermissionStudent"]="N";
$actionRows[$actionCount]["categoryPermissionParent"]="N";
$actionRows[$actionCount]["categoryPermissionOther"]="Y";

$actionCount++;
$actionRows[$actionCount]["name"]="Help Desk Statistics";
$actionRows[$actionCount]["precedence"]="0";
$actionRows[$actionCount]["category"]="Admin";
$actionRows[$actionCount]["description"]="Statistics for the Help Desk.";
$actionRows[$actionCount]["URLList"]="helpDesk_statistics.php";
$actionRows[$actionCount]["entryURL"]="helpDesk_statistics.php";
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y";
$actionRows[$actionCount]["defaultPermissionTeacher"]="N";
$actionRows[$actionCount]["defaultPermissionStudent"]="N";
$actionRows[$actionCount]["defaultPermissionParent"]="N";
$actionRows[$actionCount]["defaultPermissionSupport"]="N";
$actionRows[$actionCount]["categoryPermissionStaff"]="Y";
$actionRows[$actionCount]["categoryPermissionStudent"]="N";
$actionRows[$actionCount]["categoryPermissionParent"]="N";
$actionRows[$actionCount]["categoryPermissionOther"]="Y";

$actionCount++;
$actionRows[$actionCount]["name"]="Manage Departments";
$actionRows[$actionCount]["precedence"]="0";
$actionRows[$actionCount]["category"]="Technician";
$actionRows[$actionCount]["description"]="Allows the user to manage the Help Desk Departments.";
$actionRows[$actionCount]["URLList"]="helpDesk_manageDepartments.php";
$actionRows[$actionCount]["entryURL"]="helpDesk_manageDepartments.php";
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y";
$actionRows[$actionCount]["defaultPermissionTeacher"]="N";
$actionRows[$actionCount]["defaultPermissionStudent"]="N";
$actionRows[$actionCount]["defaultPermissionParent"]="N";
$actionRows[$actionCount]["defaultPermissionSupport"]="N";
$actionRows[$actionCount]["categoryPermissionStaff"]="Y";
$actionRows[$actionCount]["categoryPermissionStudent"]="N";
$actionRows[$actionCount]["categoryPermissionParent"]="N";
$actionRows[$actionCount]["categoryPermissionOther"]="Y";
?>
