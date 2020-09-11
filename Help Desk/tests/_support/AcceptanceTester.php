<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    protected $breadcrumbEnd = '.trailEnd';

   /**
    * Define custom actions here
    */

    public function login($name, $password)
    {
        $I = $this;

        $I->amOnPage('/');
        $I->submitForm('form[id=loginForm]', [
            'username' => $name,
            'password' => $password
        ]);
    }

    public function loginAsAdmin()
    {
        $this->login('testingadmin', '7SSbB9FZN24Q');
    }

    public function loginAsTeacher()
    {
        $this->login('testingteacher', 'm86GVNLH7DbV');
    }

    public function loginAsStudent()
    {
        $this->login('testingstudent', 'WKLm9ELHLJL5');
    }

    public function loginAsParent()
    {
        $this->login('testingparent', 'UVSf5t7epNa7');
    }

    public function loginAsSupport()
    {
        $this->login('testingsupport', '84BNQAQfNyKa');
    }

    public function clickNavigation($text)
    {
        return $this->click($text, '.linkTop a');
    }

    public function seeBreadcrumb($text)
    {
        return $this->see($text, $this->breadcrumbEnd);
    }

    public function seeSuccessMessage($text = 'Your request was completed successfully.')
    {
        return $this->see($text, '.success');
    }

    public function seeErrorMessage($text = '')
    {
        return $this->see($text, '.error');
    }

    public function seeWarningMessage($text = '')
    {
        return $this->see($text, '.warning');
    }

    public function grabValueFromURL($param)
    {
        return $this->grabFromCurrentUrl('/'.$param.'=([^=&\s]+)/');
    }

    public function grabEditIDFromURL()
    {
        return $this->grabFromCurrentUrl('/editID=(\d+)/');
    }

    public function selectFromDropdown($selector, $n)
    {
        $n = intval($n);

        if ($n < 0) {
            $option = $this->grabTextFrom('#content select[name='.$selector.'] option:nth-last-of-type('.abs($n).')');
        } else {
            $option = $this->grabTextFrom('#content select[name='.$selector.'] option:nth-of-type('.$n.')');
        }

        $this->selectOption('#content #'.$selector, $option);
    }

    public function amOnModulePage($module, $page, $params = null)
    {
        if (mb_stripos($page, '.php') === false) {
            $page .= '.php';
        }

        $url = sprintf('/index.php?q=/modules/%1$s/%2$s', $module, $page);

        if (!empty($params)) {
            $url .= '&'.http_build_query($params);
        }

        return $this->amOnPage($url);
    }
    
    public function createIssueForMyself()
    {
         $I = $this;
        $I->clickNavigation('Create');
        $I->seeBreadcrumb('Create Issue');
        $I->fillField('issueName', 'Test Issue');
        $I->fillField('description', '<p>Test Description</p>');
        $I->selectFromDropdown('category', 2);
        $I->selectFromDropdown('priority', -1);
        $I->click('Submit');
        $I->seeSuccessMessage();
        $I->seeBreadcrumb('Create Issue');
    }
    
    public function createIssueOnBehalf()
    {
        $I = $this;
        $I->clickNavigation('Create');
        $I->seeBreadcrumb('Create Issue');
        $I->fillField('issueName', 'Test Issue');
        $I->fillField('description', '<p>Test Description</p>');
        $I->selectFromDropdown('category', 2);
        $I->selectFromDropdown('createFor', -1); 
        $I->selectFromDropdown('priority', -1);
        $I->click('Submit');
        $I->seeSuccessMessage();
        $I->seeBreadcrumb('Create Issue');
    }
    public function acceptIssue($issueID)
    {
        $I = $this;
        $I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);
        $I->seeBreadcrumb('Discuss Issue');

        $I->see('Test Issue');
        $I->see('Test Description');

        $I->click('Accept');
        $I->seeSuccessMessage();
        $I->seeBreadcrumb('Discuss Issue');
    }
    
    public function assignIssue($issueID)
    {
        $I = $this;
        $I->amOnModulePage('Help Desk', 'issues_assign.php', ['issueID' => $issueID]);
        $I->seeBreadcrumb('Reassign Issue');

        $I->selectFromDropdown('technician', 2);
        $I->click('Submit');
        $I->seeSuccessMessage();
        $I->seeBreadcrumb('Discuss Issue');
    }
    
    public function discussIssue($issueID)
    {
        $I = $this;
        $I->amOnModulePage('Help Desk', 'issues_discussPost.php', ['issueID' => $issueID]);
        $I->seeBreadcrumb('Post Discuss');

        $I->dontSee('Assign');
        $I->dontSee('Accept');

        $I->fillField('comment', '<p>Discuss Test</p>');
        $I->click('Submit');
        $I->seeSuccessMessage();
        $I->seeBreadcrumb('Discuss Issue');
    }
    
    public function resolveIssue($issueID)
    {
        $I = $this;
        $I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);
        $I->click('Resolve');
        $I->seeSuccessMessage();
        $I->seeBreadcrumb('Issues');
    }
    
    public function reincarnateIssue($issueID)
    {
        $I = $this;
        $I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);
        $I->click('Reincarnate');
        $I->seeSuccessMessage();
        $I->seeBreadcrumb('Discuss Issue');
    }
    
}
