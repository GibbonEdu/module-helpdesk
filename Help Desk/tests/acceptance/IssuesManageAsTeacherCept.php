<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Create, discuss, resolve, reincarnate, and resolve an issue as a teacher');
$I->loginAsTeacher();
$I->amOnModulePage('Help Desk', 'issues_view.php');

// Add ------------------------------------------------
$I->clickNavigation('Create');
$I->seeBreadcrumb('Create Issue');
$I->fillField('issueName', 'Test Issue');
$I->fillField('description', '<p>Test Description</p>');
$I->selectFromDropdown('category', 2);
$I->selectFromDropdown('priority', -1);
$I->click('Submit');

//Check if we get redirected correctly
$issueID = $I->grabValueFromURL('issueID');
$I->seeSuccessMessage();
$I->seeBreadcrumb('Create Issue');


// discussView Accept ------------------------------------------------
$I->click('Logout');
$I->loginAsAdmin();
$I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);
$I->seeBreadcrumb('Discuss Issue');

$I->click('Accept');
$I->seeSuccessMessage();
$I->seeBreadcrumb('Discuss Issue');


// discuss ------------------------------------------------
$I->click('Logout');
$I->loginAsTeacher();
$I->amOnModulePage('Help Desk', 'issues_discussPost.php', ['issueID' => $issueID]);
$I->seeBreadcrumb('Post Discuss');

$I->dontSee('Assign');
$I->dontSee('Accept');

$I->fillField('comment', '<p>Discuss Test</p>');
$I->click('Submit');
$I->seeSuccessMessage();


//Resolve ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);

$I->click('Resolve');
$I->seeSuccessMessage();
$I->seeBreadcrumb('Issues');

//Reincarnate ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);

$I->click('Reincarnate');
$I->seeSuccessMessage();
$I->seeBreadcrumb('Discuss Issue');

//Resolve ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);

$I->click('Resolve');
$I->seeSuccessMessage();
$I->seeBreadcrumb('Issues');
