<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Create, edit, and delete a technician');
$I->loginAsAdmin();
$I->amOnModulePage('Help Desk', 'helpDesk_manageTechnicians.php');

// Add ------------------------------------------------
$I->clickNavigation('Add');
$I->seeBreadcrumb('Create Technician');


$I->selectFromDropdown('person', 2);
$I->selectFromDropdown('group', 2);
$I->click('Submit');
$I->seeSuccessMessage();

//TODO: technicianID does not get posted
$technicianID = $I->grabValueFromURL('technicianID');

// Edit ------------------------------------------------
$I->amOnModulePage('Help Desk', 'helpDesk_setTechGroup.php', array('technicianID' => $technicianID));
$I->seeBreadcrumb('Edit Technician');

//TODO: I currently do not see in dropdowns

$I->selectFromDropdown('group', 2);
$I->click('Submit');
$I->seeSuccessMessage();

//Delete ------------------------------------------------
$I->amOnModulePage('Help Desk', 'helpDesk_technicianDelete.php', array('technicianID' => $technicianID));

$I->click('Yes');
$I->seeSuccessMessage();
