<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Create, discuss, resolve, reincarnate, and resolve an issue as a teacher (complex and simple categories)');
$I->loginAsTeacher();
$I->amOnModulePage('Help Desk', 'issues_view.php');

// Add ------------------------------------------------
$I->createIssueForMyself();
$issueID = $I->grabValueFromURL('issueID');

// discussView Accept ------------------------------------------------
$I->click('Logout');
$I->loginAsAdmin();
$I->acceptIssue($issueID);


// discuss ------------------------------------------------
$I->click('Logout');
$I->loginAsTeacher();
$I->discussIssue($issueID);

//Resolve ------------------------------------------------
$I->resolveIssue($issueID);

//Reincarnate ------------------------------------------------
$I->reincarnateIssue($issueID);

//Resolve ------------------------------------------------
$I->resolveIssue($issueID);

//Test from view
$I->reincarnateIssueFromView($issueID);
$I->resolveIssueFromView($issueID);

//check with simple categories
$I->click('Logout');
$I->loginAsAdmin();

$I->amOnModulePage('Help Desk', 'helpDesk_settings.php');
$I->checkOption('simpleCategories');
$I->click('Submit');

$I->click('Logout');
$I->loginAsTeacher();
$I->amOnModulePage('Help Desk', 'issues_view.php');

// Add ------------------------------------------------
$I->createIssueForMyselfSimple();
$issueID = $I->grabValueFromURL('issueID');

// discussView Accept ------------------------------------------------
$I->click('Logout');
$I->loginAsAdmin();
$I->acceptIssue($issueID);


// discuss ------------------------------------------------
$I->click('Logout');
$I->loginAsTeacher();
$I->discussIssue($issueID);


//Resolve ------------------------------------------------
$I->resolveIssue($issueID);

//Reincarnate ------------------------------------------------
$I->reincarnateIssue($issueID);

//Resolve ------------------------------------------------
$I->resolveIssue($issueID);

//go back to complex categories
$I->click('Logout');
$I->loginAsAdmin();

$I->amOnModulePage('Help Desk', 'helpDesk_settings.php');
$I->uncheckOption('simpleCategories');
$I->click('Submit');
