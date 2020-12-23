--
-- Data for table `gibbonPerson`
--
SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

--- User: testingadmin       7SSbB9FZN24Q
INSERT INTO `gibbonPerson` (`title`, `surname`, `firstName`, `preferredName`, `officialName`, `nameInCharacters`, `username`, `password`, `passwordStrong`, `passwordStrongSalt`, `passwordForceReset`, `status`, `canLogin`, `gibbonRoleIDPrimary`, `gibbonRoleIDAll`) VALUES
('Ms. ', 'TestUser', 'Admin', 'Admin', 'Admin TestUser', '', 'testingadmin', '', '015261d879c7fc2789d19b9193d189364baac34a98561fa205cd5f37b313cdb0', '/aBcEHKLnNpPrsStTUyz47', 'N', 'Full', 'Y', 001, '001,002,003,004,006') ON DUPLICATE KEY UPDATE lastTimestamp=NOW(), `passwordStrong`=VALUES(passwordStrong), `passwordStrongSalt`=VALUES(passwordStrongSalt), failCount=0;

INSERT INTO `gibbonStaff` (`gibbonPersonID`, `type`, `jobTitle`) VALUES ((SELECT `gibbonPersonID` FROM `gibbonPerson` WHERE `username`='testingadmin' LIMIT 1), 'Support', 'Test Admin') ON DUPLICATE KEY UPDATE `jobTitle`='Test Admin';

--- User: testingteacher     m86GVNLH7DbV
INSERT INTO `gibbonPerson` (`title`, `surname`, `firstName`, `preferredName`, `officialName`, `nameInCharacters`, `username`, `password`, `passwordStrong`, `passwordStrongSalt`, `passwordForceReset`, `status`, `canLogin`, `gibbonRoleIDPrimary`, `gibbonRoleIDAll`) VALUES
('Mr. ', 'TestUser', 'Teacher', 'Teacher', 'Teacher TestUser Admin', '', 'testingteacher', '', '3ea8c6a760d223038a96c900994410950057390a0e4a48a39e760d50cab68040', '.aBdegGhlNoqRxzZ012369', 'N', 'Full', 'Y', 002, '002') ON DUPLICATE KEY UPDATE lastTimestamp=NOW(), `passwordStrong`=VALUES(passwordStrong), `passwordStrongSalt`=VALUES(passwordStrongSalt), failCount=0;

--- User: testingteacher2     m86GVNLH7DbV
INSERT INTO `gibbonPerson` (`title`, `surname`, `firstName`, `preferredName`, `officialName`, `nameInCharacters`, `username`, `password`, `passwordStrong`, `passwordStrongSalt`, `passwordForceReset`, `status`, `canLogin`, `gibbonRoleIDPrimary`, `gibbonRoleIDAll`) VALUES
('Mr. ', 'TestUser2', 'Teacher2', 'Teacher2', 'Teacher TestUser Admin2', '', 'testingteacher2', '', '3ea8c6a760d223038a96c900994410950057390a0e4a48a39e760d50cab68040', '.aBdegGhlNoqRxzZ012369', 'N', 'Full', 'Y', 002, '002') ON DUPLICATE KEY UPDATE lastTimestamp=NOW(), `passwordStrong`=VALUES(passwordStrong), `passwordStrongSalt`=VALUES(passwordStrongSalt), failCount=0;

--- User: testingstudent     WKLm9ELHLJL5
INSERT INTO `gibbonPerson` (`title`, `surname`, `firstName`, `preferredName`, `officialName`, `nameInCharacters`, `username`, `password`, `passwordStrong`, `passwordStrongSalt`, `passwordForceReset`, `status`, `canLogin`, `gibbonRoleIDPrimary`, `gibbonRoleIDAll`) VALUES
('Ms. ', 'TestUser', 'Student', 'Student', 'Student TestUser', '', 'testingstudent', '', '90ad15af1a18f0fcf9192997c93dda3731fa6d379bd25005711b659470fe0243', './aefFGhHIPrRTuvVwxz47', 'N', 'Full', 'Y', 003, '003') ON DUPLICATE KEY UPDATE lastTimestamp=NOW(), `passwordStrong`=VALUES(passwordStrong), `passwordStrongSalt`=VALUES(passwordStrongSalt), failCount=0;

--- User: testingparent      UVSf5t7epNa7
INSERT INTO `gibbonPerson` (`title`, `surname`, `firstName`, `preferredName`, `officialName`, `nameInCharacters`, `username`, `password`, `passwordStrong`, `passwordStrongSalt`, `passwordForceReset`, `status`, `canLogin`, `gibbonRoleIDPrimary`, `gibbonRoleIDAll`) VALUES
('Ms. ', 'TestUser', 'Parent', 'Parent', 'Parent TestUser', '', 'testingparent', '', '5562ca7b5775ed5678c3523035ec347da2b7e26b0f5bb5ad1d4ac6a9af0274d6', '/bceFHiIKmnOQRstVwxXZ2', 'N', 'Full', 'Y', 004, '004') ON DUPLICATE KEY UPDATE lastTimestamp=NOW(), `passwordStrong`=VALUES(passwordStrong), `passwordStrongSalt`=VALUES(passwordStrongSalt), failCount=0;

--- User: testingsupport     84BNQAQfNyKa
INSERT INTO `gibbonPerson` (`title`, `surname`, `firstName`, `preferredName`, `officialName`, `nameInCharacters`, `username`, `password`, `passwordStrong`, `passwordStrongSalt`, `passwordForceReset`, `status`, `canLogin`, `gibbonRoleIDPrimary`, `gibbonRoleIDAll`) VALUES
('Ms. ', 'TestUser', 'Support', 'Support', 'Support TestUser', '', 'testingsupport', '', 'bd0688e5ca1c86f1f03417556ed53b9b1deed66bd4a0e75f660efb0ba0cb4671', 'aCdHikKlnpPqRstvXyY026', 'N', 'Full', 'Y', 006, '006') ON DUPLICATE KEY UPDATE lastTimestamp=NOW(), `passwordStrong`=VALUES(passwordStrong), `passwordStrongSalt`=VALUES(passwordStrongSalt), failCount=0;


--- Create test users for Helpdesk Testing
--- User: testingheadtech       7SSbB9FZN24Q
INSERT INTO `gibbonPerson` (`title`, `surname`, `firstName`, `preferredName`, `officialName`, `nameInCharacters`, `username`, `password`, `passwordStrong`, `passwordStrongSalt`, `passwordForceReset`, `status`, `canLogin`, `gibbonRoleIDPrimary`, `gibbonRoleIDAll`) VALUES
('Ms. ', 'TestUser', 'HeadTech', 'HeadTech', 'HeadTech TestUser', '', 'testingheadtech', '', '015261d879c7fc2789d19b9193d189364baac34a98561fa205cd5f37b313cdb0', '/aBcEHKLnNpPrsStTUyz47', 'N', 'Full', 'Y', 006, ',006') ON DUPLICATE KEY UPDATE lastTimestamp=NOW(), `passwordStrong`=VALUES(passwordStrong), `passwordStrongSalt`=VALUES(passwordStrongSalt), failCount=0;

INSERT INTO `helpDeskTechnicians` (`gibbonPersonID`, `groupID`) VALUES ((SELECT `gibbonPersonID` FROM `gibbonPerson` WHERE `username`='testingheadtech' LIMIT 1), '0001') ON DUPLICATE KEY UPDATE `groupID`='0001';

--- User: testingtech       7SSbB9FZN24Q
INSERT INTO `gibbonPerson` (`title`, `surname`, `firstName`, `preferredName`, `officialName`, `nameInCharacters`, `username`, `password`, `passwordStrong`, `passwordStrongSalt`, `passwordForceReset`, `status`, `canLogin`, `gibbonRoleIDPrimary`, `gibbonRoleIDAll`) VALUES
('Ms. ', 'TestUser', 'Tech', 'Tech', 'Tech TestUser', '', 'testingtech', '', '015261d879c7fc2789d19b9193d189364baac34a98561fa205cd5f37b313cdb0', '/aBcEHKLnNpPrsStTUyz47', 'N', 'Full', 'Y', 006, ',006') ON DUPLICATE KEY UPDATE lastTimestamp=NOW(), `passwordStrong`=VALUES(passwordStrong), `passwordStrongSalt`=VALUES(passwordStrongSalt), failCount=0;

INSERT INTO `helpDeskTechnicians` (`gibbonPersonID`, `groupID`) VALUES ((SELECT `gibbonPersonID` FROM `gibbonPerson` WHERE `username`='testingtech' LIMIT 1), '0002') ON DUPLICATE KEY UPDATE `groupID`='0002';


