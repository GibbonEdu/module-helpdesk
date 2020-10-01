<?php
//This doesnt check for the specific issueID but flushing all the notifications should mean that the test is semiaccurate each time it runs
$I = new AcceptanceTester($scenario);
$I->wantTo('Check for notification presence');

$I->loginAsTech();
$I->amOnPage('/index.php?q=notifications.php');
$I->see("A new issue has been added (Test Issue).", "//td[contains(text(),'Help Desk')]//..");
$I->click('Delete All Notifications');

$I->click('Logout');
$I->loginAsHeadTech();
$I->amOnPage('/index.php?q=notifications.php');
$I->see("A new issue has been added (Test Issue).", "//td[contains(text(),'Help Desk')]//..");
$I->click('Delete All Notifications');


$I->click('Logout');
$I->loginAsTeacher();
$I->amOnPage('/index.php?q=notifications.php');
$I->see("A new message has been added to Issue");
$I->see("A technician has started working on your isuse.", "//td[contains(text(),'Help Desk')]//..");
$I->click('Delete All Notifications');
