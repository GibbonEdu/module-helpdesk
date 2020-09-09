<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('create, accept, assign, resolve, and reincarnate');
$I->loginAsAdmin();
$I->amOnModulePage('Help Desk', 'issues_view.php');

// Add ------------------------------------------------
$I->clickNavigation('create');
$I->seeBreadcrumb('Create Issue');
$I->fillField('issueName', 'Test Issue');
$I->fillField('description', '<p>Test Description</p>');
$I->selectFromDropdown('category', 2);
$I->selectFromDropdown('priority', 2);
$I->selectFromDropdown('privacySettings', 2);
$I->submitForm('#content form', $addFormValues, 'Submit');
$I->seeSuccessMessage();


$issueID = $I->grabEditIDFromURL();

// discussView ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_discussView.php', array('issueID' => $issueID));
$I->seeBreadcrumb('Discuss Issue');

$I->seeInField('issueName', 'Test Issue');
$I->seeInField('description', '<p>Test Description</p>');

$I->selectFromDropdown('group', 2);
$I->click('Submit');
$I->seeSuccessMessage();

// Delete ------------------------------------------------
$I->amOnModulePage('Help Desk', 'helpDesk_technicianDelete.php', array('issueID' => $issueID));

$I->click('Yes');
$I->seeSuccessMessage();
