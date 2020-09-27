<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Create and manage an issue as a Teacher/Owner using simple and complex categories, checking permissions');
$I->loginAsTeacher();
$I->amOnModulePage('Help Desk', 'issues_view.php');

// Add ------------------------------------------------
$I->createIssueForMyself();
$issueID = $I->grabValueFromURL('issueID');
$I->checkTeacherPermissions();

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
$I->checkTeacherPermissionsFromView($issueID);
$I->resolveIssueFromView($issueID);

//check with simple categories
$I->changetoSimpleCategory();
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
$I->changetoComplexCategory();
