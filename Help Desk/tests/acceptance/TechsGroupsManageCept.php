<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('add, edit and delete technician technician groups');
$I->loginAsAdmin();
$I->amOnModulePage('Help Desk', 'helpDesk_manageTechnicianGroup.php');

// Add ------------------------------------------------
$I->clickNavigation('Add');
$I->seeBreadcrumb('Create Technician Group');


$I->fillField('groupName', 'Test Group');
$I->click('Submit');

$I->seeSuccessMessage();


$groupID = $I->grabEditIDFromURL();

// Edit ------------------------------------------------
$I->amOnModulePage('Help Desk', 'helpDesk_editTechnicianGroup.php', array('groupID' => $groupID));
$I->seeBreadcrumb('Edit Technician Group');

$I->seeInField('groupName', 'Test Group');
$I->seeInField('viewIssue', 'Y');
$I->seeInField('assignIssue', 'N');
$I->seeInField('acceptIssue', 'Y');
$I->seeInField('resolveIssue', 'Y');
$I->seeInField('createIssueForOther', 'Y');
$I->seeInField('reassignIssue', 'N');
$I->seeInField('reincarnateIssue', 'N');
$I->seeInField('fullAccess', 'N');

$I->selectFromDropdown('viewIssueStatus', 1);
$I->click('Submit');
$I->seeSuccessMessage();

// Delete ------------------------------------------------
$I->amOnModulePage('Help Desk', 'helpDesk_technicianGroupDelete.php', array('groupID' => $groupID));

$I->click('Yes');
$I->seeSuccessMessage();
