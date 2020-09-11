<?php
//USE;end TO SEPERATE SQL STATEMENTS. DON'T USE;end IN ANY OTHER PLACES!

$sql=array();
$count=0;

//v0.0.01
$sql[$count][0]="0.0.01";
$sql[$count][1]="-- First version, nothing to update";

//v0.0.02
$count++;
$sql[$count][0]="0.0.02";
$sql[$count][1]="";

//v0.1.00
$count++;
$sql[$count][0]="0.1.00";
$sql[$count][1]="
UPDATE gibbonAction SET name='Create Issue', URLList='issues_create.php', entryURL='issues_create.php' WHERE name='Submit Issue' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
INSERT INTO gibbonAction SET name='Create Issue_forOther', precedence='1', category='', description='Submits an IT related issue to be resolved by the help desk staff with an optional feature to create on the behalf of others.', URLList='issues_create.php', entryURL='issues_create.php', defaultPermissionAdmin='Y', defaultPermissionTeacher='Y', defaultPermissionStudent='Y', defaultPermissionParent='N', defaultPermissionSupport='Y', categoryPermissionStaff='Y', categoryPermissionStudent='Y', categoryPermissionParent='Y', categoryPermissionOther='N' WHERE name='Submit Issue' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
ALTER TABLE helpDeskIssue ADD createdByID int(12) unsigned zerofill NOT NULL;end";

//v0.1.01
$count++;
$sql[$count][0]="0.1.01";
$sql[$count][1]="
INSERT INTO gibbonAction SET name='Create Issue_forOther', precedence='1', category='', description='Submits an IT related issue to be resolved by the help desk staff with an optional feature to create on the behalf of others.', URLList='issues_create.php', entryURL='issues_create.php', defaultPermissionAdmin='Y', defaultPermissionTeacher='Y', defaultPermissionStudent='Y', defaultPermissionParent='N', defaultPermissionSupport='Y', categoryPermissionStaff='Y', categoryPermissionStudent='Y', categoryPermissionParent='Y', categoryPermissionOther='N', gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end";

//v0.1.02
$count++;
$sql[$count][0]="0.1.02";
$sql[$count][1]="
UPDATE gibbonAction SET URLList='issues_view.php, issues_discuss_view.php' WHERE name='View issues_All' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET URLList='issues_view.php, issues_assign.php, issues_discuss_view.php' WHERE name='View issues_All&Assign' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
ALTER TABLE helpDeskIssueDiscuss DROP COLUMN technicianPosted;end
ALTER TABLE helpDeskIssueDiscuss ADD gibbonPersonID int(10) unsigned zerofill NOT NULL;end";

//v0.1.03
$count++;
$sql[$count][0]="0.1.03";
$sql[$count][1]="";

//v0.1.04
$count++;
$sql[$count][0]="0.1.04";
$sql[$count][1]="";

//v0.1.05
$count++;
$sql[$count][0]="0.1.05";
$sql[$count][1]="";

//v0.2.00
$count++;
$sql[$count][0]="0.2.00";
$sql[$count][1]="";

//v0.2.01
$count++;
$sql[$count][0]="0.2.01";
$sql[$count][1]="";

//v0.2.02
$count++;
$sql[$count][0]="0.2.02";
$sql[$count][1]="";

//v0.3.00
$count++;
$sql[$count][0]="0.3.00";
$sql[$count][1]="
DELETE FROM gibbonAction WHERE name='View issues_All' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
DELETE FROM gibbonAction WHERE name='View issues_All&Assign' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET name='Issues', description='Shows issues depending on role/permissions.' WHERE name='View issues_Mine'AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
DELETE FROM gibbonAction WHERE name='Create Issue_forOther' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
CREATE TABLE `helpDeskTechGroups` (`groupID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT, `groupName` varchar(55) NOT NULL, `viewIssue` boolean DEFAULT 1, `viewIssueStatus` ENUM('All', 'UP', 'PR', 'Pending') DEFAULT 'All', `assignIssue` boolean DEFAULT 0, `acceptIssue` boolean DEFAULT 1, `resolveIssue` boolean DEFAULT 1, `createIssueForOther` boolean DEFAULT 1, `fullAccess` boolean DEFAULT 0, PRIMARY KEY (`groupID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;end
UPDATE gibbonAction SET URLList='helpDesk_settings.php', entryURL='helpDesk_settings.php' WHERE name='Help Desk Settings'AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET URLList='helpDesk_manageTechnicians.php', entryURL='helpDesk_manageTechnicians.php' WHERE name='Manage Technicians'AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
INSERT INTO gibbonAction SET name='Manage Technician Groups', precedence='0', category='', description='Manage Technician Groups.', URLList='helpDesk_manageTechnicianGroup.php', entryURL='helpDesk_manageTechnicianGroup.php', defaultPermissionAdmin='Y', defaultPermissionTeacher='N', defaultPermissionStudent='N', defaultPermissionParent='N', defaultPermissionSupport='N', categoryPermissionStaff='Y', categoryPermissionStudent='N', categoryPermissionParent='N', categoryPermissionOther='N', gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
ALTER TABLE helpDeskTechnicians ADD groupID int(4) unsigned zerofill NOT NULL;end
";

//v0.3.01
$count++;
$sql[$count][0]="0.3.01";
$sql[$count][1]="
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '1', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Help Desk' AND gibbonAction.name='Manage Technician Groups'));end
UPDATE gibbonAction SET URLList='helpDesk_manageTechnicians.php', entryURL='helpDesk_manageTechnicians.php' WHERE name='Manage Technician Groups' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
";

//v0.3.02
$count++;
$sql[$count][0]="0.3.02";
$sql[$count][1]="
UPDATE gibbonAction SET URLList='helpDesk_manageTechnicianGroup.php', entryURL='helpDesk_manageTechnicianGroup.php' WHERE name='Manage Technician Groups' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET URLList='helpDesk_manageTechnicians.php', entryURL='helpDesk_manageTechnicians.php' WHERE name='Manage Technician' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
";

//v0.3.03
$count++;
$sql[$count][0]="0.3.03";
$sql[$count][1]="
UPDATE gibbonAction SET URLList='helpDesk_manageTechnicians.php', entryURL='helpDesk_manageTechnicians.php' WHERE name='Manage Technicians' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
";

//v0.3.04
$count++;
$sql[$count][0]="0.3.04";
$sql[$count][1]="
UPDATE gibbonAction SET description='Allows the user to submit an issue to be resolved by the help desk staff.' WHERE name='Create Issue' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET description='Gives the user access to the Issues section' WHERE name='Issues' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET description='Allows the user to manage the Technicians.' WHERE name='Manage Technicians' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET description='Allows the user to manage the Technicians Groups.' WHERE name='Manage Technician Groups' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET description='Allows the user to edit the settings for the module.' WHERE name='Help Desk Settings' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonModule SET url='https://github.com/raynichc/helpdesk' WHERE name='Help Desk';end
";

//v0.3.05
$count++;
$sql[$count][0]="0.3.05";
$sql[$count][1]="";

//v0.3.10
$count++;
$sql[$count][0]="0.3.10";
$sql[$count][1]="
ALTER TABLE helpDeskTechGroups ADD reassignIssue boolean DEFAULT 0;end
";

//v0.3.11
$count++;
$sql[$count][0]="0.3.11";
$sql[$count][1]="";

//v0.3.12
$count++;
$sql[$count][0]="0.3.12";
$sql[$count][1]="";

//v0.3.13
$count++;
$sql[$count][0]="0.3.13";
$sql[$count][1]="";

//v0.3.14
$count++;
$sql[$count][0]="0.3.14";
$sql[$count][1]="";

//v0.3.15
$count++;
$sql[$count][0]="0.3.15";
$sql[$count][1]="";

//v0.3.16
$count++;
$sql[$count][0]="0.3.16";
$sql[$count][1]="
UPDATE gibbonAction SET categoryPermissionOther='Y' WHERE name='Create Issue' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET categoryPermissionOther='Y' WHERE name='Issues' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET categoryPermissionOther='Y' WHERE name='Manage Technicians' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET categoryPermissionOther='Y' WHERE name='Manage Technician Groups' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET categoryPermissionOther='Y' WHERE name='Help Desk Settings' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
";

//v0.3.17
$count++;
$sql[$count][0]="0.3.17";
$sql[$count][1]="";

//v0.4.00
$count++;
$sql[$count][0]="0.4.00";
$sql[$count][1]="
INSERT INTO `gibbonSetting` (`gibbonSystemSettingsID`, `scope`, `name`, `nameDisplay`, `description`, `value`)
VALUES
(NULL, 'Help Desk', 'resolvedIssuePrivacy', 'Default Resolved Issue Privacy', 'Default privacy setting for resolved issues.', 'Everyone');end
ALTER TABLE helpDeskIssue ADD `privacySetting` ENUM('Everyone', 'Related', 'Owner', 'No one') DEFAULT 'Everyone';end
UPDATE gibbonAction SET entrySidebar='N' WHERE name='Issues' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
ALTER TABLE helpDeskTechGroups ADD reincarnateIssue boolean DEFAULT 1;end
";

//v0.4.10
$count++;
$sql[$count][0]="0.4.10";
$sql[$count][1]="
ALTER TABLE helpDeskIssue ALTER privacySetting SET DEFAULT 'Related';end
UPDATE gibbonAction SET category='Issues' WHERE name='Create Issue' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET category='Issues' WHERE name='Issues' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET category='Settings' WHERE name='Manage Technicians' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET category='Settings' WHERE name='Manage Technician Groups' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET category='Settings' WHERE name='Help Desk Settings' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
";

//v0.4.20
$count++;
$sql[$count][0]="0.4.20";
$sql[$count][1]="
INSERT INTO gibbonAction SET name='Help Desk Statistics', precedence='0', category='Admin', description='Statistics for the Help Desk.', URLList='helpDesk_statistics.php', entryURL='helpDesk_statistics.php', defaultPermissionAdmin='Y', defaultPermissionTeacher='N', defaultPermissionStudent='N', defaultPermissionParent='N', defaultPermissionSupport='N', categoryPermissionStaff='Y', categoryPermissionStudent='N', categoryPermissionParent='N', categoryPermissionOther='N', gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '1', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Help Desk' AND gibbonAction.name='Help Desk Statistics'));end
UPDATE gibbonAction SET category='Admin' WHERE name='Help Desk Settings' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET category='Technician' WHERE name='Manage Technicians' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonAction SET category='Technician' WHERE name='Manage Technician Groups' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
UPDATE gibbonModule SET description='A virtual help desk module for Gibbon.' WHERE name='Help Desk';end
";

//v1.0.00
$count++;
$sql[$count][0]="1.0.00";
$sql[$count][1]="
";

//v1.0.01
$count++;
$sql[$count][0]="1.0.01";
$sql[$count][1]="
";

//v1.0.02
$count++;
$sql[$count][0]="1.0.02";
$sql[$count][1]="
";

//v1.1.00
$count++;
$sql[$count][0]="1.1.00";
$sql[$count][1]="
";

//v1.1.01
$count++;
$sql[$count][0]="1.1.01";
$sql[$count][1]="
";

//v1.1.02
$count++;
$sql[$count][0]="1.1.02";
$sql[$count][1]="
";

//v1.1.03
$count++;
$sql[$count][0]="1.1.03";
$sql[$count][1]="
";

//v1.1.04
$count++;
$sql[$count][0]="1.1.04";
$sql[$count][1]="
";

//v1.1.05
$count++;
$sql[$count][0]="1.1.05";
$sql[$count][1]="
";

//v1.2.00
$count++;
$sql[$count][0]="1.2.00";
$sql[$count][1]="
";

//v1.2.01
$count++;
$sql[$count][0]="1.2.01";
$sql[$count][1]="
UPDATE `gibbonModule` SET `author`='Ray Clark, Ashton Power & Adrien Tremblay' WHERE `name` = 'Help Desk';end
";

//v1.2.02
$count++;
$sql[$count][0]="1.2.02";
$sql[$count][1]="
UPDATE `gibbonModule` SET `author`='Ray Clark, Ashton Power & Adrien Tremblay' WHERE `name` = 'Help Desk';end
";