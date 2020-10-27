<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Create and manage an issue as a normal Technician using simple and complex categories, checking permissions');
$I->loginAsTech();
$I->amOnModulePage('Help Desk', 'issues_view.php');

// Add ------------------------------------------------
$I->createIssueOnBehalf();
$issueID = $I->grabValueFromURL('issueID');
$I->checkTechPermissions($issueID);

// discussView Accept ------------------------------------------------
$I->acceptIssue($issueID);

// discuss ------------------------------------------------
$I->discussIssue($issueID);


//Resolve ------------------------------------------------
$I->resolveIssue($issueID);

//Reincarnate ------------------------------------------------
$I->reincarnateIssue($issueID);

//Resolve ------------------------------------------------
$I->resolveIssue($issueID);

//Test from view
$I->reincarnateIssueFromView($issueID);
$I->amOnModulePage('Help Desk', 'issues_view.php');
$I->checkTechPermissionsFromView($issueID);
$I->resolveIssueFromView($issueID);


//check with simple categories
$I->changetoSimpleCategory();
$I->loginAsTech();
$I->amOnModulePage('Help Desk', 'issues_view.php');

// Add ------------------------------------------------
$I->createIssueOnBehalfSimple();
$issueID = $I->grabValueFromURL('issueID');

// discussView Accept ------------------------------------------------
$I->acceptIssue($issueID);

// discuss ------------------------------------------------
$I->discussIssue($issueID);


// discussView Assign ------------------------------------------------
$I->dontSee('Reassign');

//Resolve ------------------------------------------------
$I->resolveIssue($issueID);

//Reincarnate ------------------------------------------------
$I->reincarnateIssue($issueID);

//Resolve ------------------------------------------------
$I->resolveIssue($issueID);

$I->changetoComplexCategory();
