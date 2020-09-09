<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Change settings');
$I->loginAsAdmin();


$I->amOnModulePage('Help Desk', 'helpDesk_settings.php');
$I->seeBreadcrumb('Manage Help Desk Settings');


$I->selectFromDropdown('resolvedIssuePrivacy', 1);
$I->fillField('issueCategory', 'Facilities,ICT');
$I->fillField('issuePriority', '1,2,3');
$I->fillField('issuePriorityName', 'Priority');
$I->click('Submit');
$I->seeSuccessMessage();


