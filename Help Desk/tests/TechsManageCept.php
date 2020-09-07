<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('add, edit and delete technicians');
$I->loginAsAdmin();
$I->amOnModulePage('Help Desk', 'helpDesk_manageTechnicians.php');

// Add ------------------------------------------------
$I->clickNavigation('Add');
$I->seeBreadcrumb('Create Technician');


$I->selectFromDropdown('person', 1);
$I->selectFromDropdown('group', 1);
$I->click('Submit');
$I->seeSuccessMessage();


$technicianID = $I->grabEditIDFromURL();

// Edit ------------------------------------------------
$I->amOnModulePage('Help Desk', 'helpDesk_setTechGroup.php', array('technicianID' => $technicianID));
$I->seeBreadcrumb('Edit Technician');

//TODO: I currently do not see in form fields

$I->selectFromDropdown('group', 2);
$I->click('Submit');
$I->seeSuccessMessage();

// Delete ------------------------------------------------
$I->amOnModulePage('Help Desk', 'helpDesk_technicianDelete.php', array('technicianID' => $technicianID));

$I->click('Yes');
$I->seeSuccessMessage();
