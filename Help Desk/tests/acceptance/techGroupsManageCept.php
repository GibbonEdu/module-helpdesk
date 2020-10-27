<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Create, Edit and Delete Technician Group');
$I->loginAsAdmin();
$I->amOnModulePage('Help Desk', 'helpDesk_manageTechnicianGroup.php');

// Add ------------------------------------------------
$I->clickNavigation('Add');
$I->seeBreadcrumb('Create Technician Group');

$I->fillField('groupName', 'Test Group');
$I->click('Submit');

$I->seeSuccessMessage();

$groupID = $I->grabValueFromURL('groupID');

// Edit ------------------------------------------------
$I->amOnModulePage('Help Desk', 'helpDesk_editTechnicianGroup.php', array('groupID' => $groupID));
$I->seeBreadcrumb('Edit Technician Group');

$I->seeInField('groupName', 'Test Group');
$I->seeInField('viewIssue', '1');
$I->seeInField('assignIssue', '');
$I->seeInField('acceptIssue', '1');
$I->seeInField('resolveIssue', '1');
$I->seeInField('createIssueForOther', '1');
$I->seeInField('reassignIssue', '');
$I->seeInField('reincarnateIssue', '1');
$I->seeInField('fullAccess', '');
$I->selectFromDropdown('viewIssueStatus', 1);
$I->selectFromDropdown('departmentID', 1);
$I->click('Submit');
$I->seeSuccessMessage();

// Delete ------------------------------------------------
$I->amOnModulePage('Help Desk', 'helpDesk_technicianGroupDelete.php', array('groupID' => $groupID));
$I->selectFromDropdown('group', 2);
$I->click('Submit');
$I->seeSuccessMessage();
