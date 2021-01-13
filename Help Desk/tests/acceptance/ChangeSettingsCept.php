<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Change and check settings');
$I->loginAsAdmin();


$I->amOnModulePage('Help Desk', 'helpDesk_settings.php');
$I->seeBreadcrumb('Manage Help Desk Settings');


$newFormValues = array(
            'issueCategory' => 'Facilities,ICT',
            'issuePriority' => '1,2,3',
            'issuePriorityName' => 'Priority',
        );
$I->uncheckOption('simpleCategories');
$I->submitForm('#helpDeskSettings', $newFormValues, 'Submit');
$I->seeSuccessMessage();

$I->seeInFormFields('#helpDeskSettings', $newFormValues);

