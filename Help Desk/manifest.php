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
$name           = 'Help Desk';
$description    = 'A virtual help desk module for Gibbon.';
$entryURL       = 'issues_view.php';
$type           = 'Additional';
$category       = 'Other';
$version        = '2.1.04';
$author         = 'Ray Clark, Ashton Power & Adrien Tremblay';
$url            = 'https://github.com/GibbonEdu/module-helpDesk';

//Module tables & gibbonSettings entries
$moduleTables[] = "CREATE TABLE `helpDeskIssue` (
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

$moduleTables[] = "CREATE TABLE `helpDeskIssueDiscuss` (
    `issueDiscussID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
    `issueID` int(12) unsigned zerofill NOT NULL,
    `comment` text NOT NULL,
    `timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
    `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
    PRIMARY KEY (`issueDiscussID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$moduleTables[] = "CREATE TABLE `helpDeskIssueNotes` (
    `issueNoteID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
    `issueID` int(12) unsigned zerofill NOT NULL,
    `note` text NOT NULL,
    `timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
    `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
    PRIMARY KEY (`issueNoteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


$moduleTables[] = "CREATE TABLE `helpDeskTechnicians` (
    `technicianID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
    `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
    `groupID` int(4) unsigned zerofill NOT NULL,
    PRIMARY KEY (`technicianID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$moduleTables[] = "CREATE TABLE `helpDeskTechGroups` (
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

$moduleTables[] = "CREATE TABLE `helpDeskDepartments` (
    `departmentID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
    `departmentName` varchar(55) NOT NULL,
    `departmentDesc` varchar(128) NOT NULL,
    PRIMARY KEY (`departmentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$moduleTables[] = "CREATE TABLE `helpDeskDepartmentPermissions` (
    `departmentPermissionsID` int(4) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
    `departmentID` int(4) UNSIGNED ZEROFILL NOT NULL,
    `gibbonRoleID` int(3) UNSIGNED ZEROFILL NOT NULL,
    PRIMARY KEY (`departmentPermissionsID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$moduleTables[] = "CREATE TABLE `helpDeskGroupDepartment` (
    `groupDepartmentID` int(4) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
    `groupID` int(4) UNSIGNED ZEROFILL NOT NULL,
    `departmentID` int(4) UNSIGNED ZEROFILL NOT NULL,
    PRIMARY KEY (`groupDepartmentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$moduleTables[] = "CREATE TABLE `helpDeskSubcategories` (
    `subcategoryID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
    `departmentID` int(4) unsigned zerofill NOT NULL,
    `subcategoryName` varchar(55) NOT NULL,
    PRIMARY KEY (`subcategoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$moduleTables[] = "CREATE TABLE `helpDeskReplyTemplate` (
    `helpDeskReplyTemplateID` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
    `name` varchar(30) NOT NULL,
    `body` text NOT NULL,
    PRIMARY KEY (`helpDeskReplyTemplateID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$moduleTables[] = "INSERT INTO `helpDeskTechGroups` (`groupID`, `groupName`, `viewIssue`, `viewIssueStatus`, `assignIssue`, `acceptIssue`, `resolveIssue`, `createIssueForOther`, `fullAccess`, `reassignIssue`, `reincarnateIssue`)
    VALUES
    (NULL, 'Head Technician', 1, 'All', 1, 1, 1, 1, 1, 1, 1),
    (NULL, 'Technician', 1, 'All', 0, 1, 1, 1, 0, 0, 1)";

$moduleTables[] = "INSERT INTO `gibbonSetting` (`gibbonSettingID`, `scope`, `name`, `nameDisplay`, `description`, `value`)
    VALUES
    (NULL, 'Help Desk', 'issuePriority', 'Issue Priority', 'Different priority levels for the issues.', ''),
    (NULL, 'Help Desk', 'issuePriorityName', 'Issue Priority Name', 'Different name for the Issue Priority', 'Priority'),
    (NULL, 'Help Desk', 'issueCategory', 'Issue Category', 'Different categories for the issues.', 'Network,Hardware,Software,Application'),
    (NULL, 'Help Desk', 'simpleCategories', 'Simple Categories', 'Whether to use Simple Categories or Not.', TRUE),
    (NULL, 'Help Desk', 'techNotes', 'Technician Notes', 'Whether technicians can leave notes on issues that only other technicians can see.', FALSE)";

//Action rows
//One array per action
$actionRows[] = [
    'name'                      => 'Create Issue', //The name of the action (appears to user in the right hand side module menu)
    'precedence'                => '0', //If it is a grouped action, the precedence controls which is highest action in group
    'category'                  => 'Issues', //Optional: subgroups for the right hand side module menu
    'description'               => 'Allows the user to submit an issue to be resolved by the help desk staff.', //Text description
    'URLList'                   => 'issues_create.php',
    'entryURL'                  => 'issues_create.php',
    'defaultPermissionAdmin'    => 'Y', //Default permission for built in role Admin
    'defaultPermissionTeacher'  => 'Y', //Default permission for built in role Teacher
    'defaultPermissionStudent'  => 'Y', //Default permission for built in role Student
    'defaultPermissionParent'   => 'N', //Default permission for built in role Parent
    'defaultPermissionSupport'  => 'Y', //Default permission for built in role Support
    'categoryPermissionStaff'   => 'Y', //Should this action be available to user roles in the Staff category?
    'categoryPermissionStudent' => 'Y', //Should this action be available to user roles in the Student category?
    'categoryPermissionParent'  => 'Y', //Should this action be available to user roles in the Parent category?
    'categoryPermissionOther'   => 'Y', //Should this action be available to user roles in the Other category?
];

$actionRows[] = [
    'name'                      => 'Issues',
    'precedence'                => '0',
    'category'                  => 'Issues',
    'description'               =>  'Gives the user access to the Issues section.',
    'URLList'                   => 'issues_view.php',
    'entryURL'                  => 'issues_view.php',
    'defaultPermissionAdmin'    => 'Y',
    'defaultPermissionTeacher'  => 'Y',
    'defaultPermissionStudent'  => 'Y',
    'defaultPermissionParent'   => 'N',
    'defaultPermissionSupport'  => 'Y',
    'categoryPermissionStaff'   => 'Y',
    'categoryPermissionStudent' => 'Y',
    'categoryPermissionParent'  => 'Y',
    'categoryPermissionOther'   => 'Y',
];

$actionRows[] = [
    'name'                      => 'Help Desk Settings',
    'precedence'                => '0',
    'category'                  => 'Admin',
    'description'               => 'Allows the user to edit the settings for the module.',
    'URLList'                   => 'helpDesk_settings.php',
    'entryURL'                  => 'helpDesk_settings.php',
    'defaultPermissionAdmin'    => 'Y',
    'defaultPermissionTeacher'  => 'N',
    'defaultPermissionStudent'  => 'N',
    'defaultPermissionParent'   => 'N',
    'defaultPermissionSupport'  => 'N',
    'categoryPermissionStaff'   => 'Y',
    'categoryPermissionStudent' => 'N',
    'categoryPermissionParent'  => 'N',
    'categoryPermissionOther'   => 'Y',
];

$actionRows[] = [
    'name'                      => 'Manage Technicians',
    'precedence'                => '0',
    'category'                  => 'Technician',
    'description'               => 'Allows the user to manage the Technicians.',
    'URLList'                   => 'helpDesk_manageTechnicians.php',
    'entryURL'                  => 'helpDesk_manageTechnicians.php',
    'defaultPermissionAdmin'    => 'Y',
    'defaultPermissionTeacher'  => 'N',
    'defaultPermissionStudent'  => 'N',
    'defaultPermissionParent'   => 'N',
    'defaultPermissionSupport'  => 'N',
    'categoryPermissionStaff'   => 'Y',
    'categoryPermissionStudent' => 'N',
    'categoryPermissionParent'  => 'N',
    'categoryPermissionOther'   => 'Y',
];

$actionRows[] = [
    'name'                      => 'Manage Technician Groups',
    'precedence'                => '0',
    'category'                  => 'Technician',
    'description'               => 'Allows the user to manage the Technicians Groups.',
    'URLList'                   => 'helpDesk_manageTechnicianGroup.php',
    'entryURL'                  => 'helpDesk_manageTechnicianGroup.php',
    'defaultPermissionAdmin'    => 'Y',
    'defaultPermissionTeacher'  => 'N',
    'defaultPermissionStudent'  => 'N',
    'defaultPermissionParent'   => 'N',
    'defaultPermissionSupport'  => 'N',
    'categoryPermissionStaff'   => 'Y',
    'categoryPermissionStudent' => 'N',
    'categoryPermissionParent'  => 'N',
    'categoryPermissionOther'   => 'Y',
];

$actionRows[] = [
    'name'                      => 'Help Desk Statistics',
    'precedence'                => '0',
    'category'                  => 'Admin',
    'description'               => 'Statistics for the Help Desk.',
    'URLList'                   => 'helpDesk_statistics.php',
    'entryURL'                  => 'helpDesk_statistics.php',
    'defaultPermissionAdmin'    => 'Y',
    'defaultPermissionTeacher'  => 'N',
    'defaultPermissionStudent'  => 'N',
    'defaultPermissionParent'   => 'N',
    'defaultPermissionSupport'  => 'N',
    'categoryPermissionStaff'   => 'Y',
    'categoryPermissionStudent' => 'N',
    'categoryPermissionParent'  => 'N',
    'categoryPermissionOther'   => 'Y',
];

$actionRows[] = [
    'name'                      => 'Manage Departments',
    'precedence'                => '0',
    'category'                  => 'Technician',
    'description'               => 'Allows the user to manage the Help Desk Departments.',
    'URLList'                   => 'helpDesk_manageDepartments.php',
    'entryURL'                  => 'helpDesk_manageDepartments.php',
    'defaultPermissionAdmin'    => 'Y',
    'defaultPermissionTeacher'  => 'N',
    'defaultPermissionStudent'  => 'N',
    'defaultPermissionParent'   => 'N',
    'defaultPermissionSupport'  => 'N',
    'categoryPermissionStaff'   => 'Y',
    'categoryPermissionStudent' => 'N',
    'categoryPermissionParent'  => 'N',
    'categoryPermissionOther'   => 'Y',
];

$actionRows[] = [
    'name'                      => 'Help Desk Reply Templates',
    'precedence'                => '0',
    'category'                  => 'Settings',
    'description'               => 'Manage Help Desk Reply Templates.',
    'URLList'                   => 'helpDesk_manageReplyTemplates.php',
    'entryURL'                  => 'helpDesk_manageReplyTemplates.php',
    'defaultPermissionAdmin'    => 'Y',
    'defaultPermissionTeacher'  => 'N',
    'defaultPermissionStudent'  => 'N',
    'defaultPermissionParent'   => 'N',
    'defaultPermissionSupport'  => 'N',
    'categoryPermissionStaff'   => 'Y',
    'categoryPermissionStudent' => 'N',
    'categoryPermissionParent'  => 'N',
    'categoryPermissionOther'   => 'N',
];
?>
