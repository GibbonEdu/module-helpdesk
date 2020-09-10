<?php
namespace Gibbon\Module\HelpDesk\Domain;

use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;

/**
 * Technician Gateway
 *
 * @version v20
 * @since   v20
 */
class IssueDiscussGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'helpDeskIssueDiscuss';
    private static $primaryKey = 'issueDiscussID';
    private static $searchableColumns = [];

    public function getIssueDiscussionByID($issueID, $isPersonsIssue) {
       if ($isPersonsIssue == FALSE) { 
        $query = $this
            ->newSelect()
            ->cols(['helpDeskIssueDiscuss.*', 'gibbonPerson.title', 'gibbonPerson.surname', 'gibbonPerson.preferredName', 'gibbonPerson.image_240', 'gibbonPerson.username', 'gibbonPerson.email', 'helpDeskTechnicians.technicianID', '"Owner" AS type', '"Commented " AS action'])
            ->from('helpDeskIssueDiscuss')
            ->innerJoin('gibbonPerson', 'helpDeskIssueDiscuss.gibbonPersonID=gibbonPerson.gibbonPersonID')
            ->leftJoin('helpDeskTechnicians', 'helpDeskIssueDiscuss.gibbonPersonID=helpDeskTechnicians.gibbonPersonID')
            ->where('helpDeskIssueDiscuss.issueID = :issueID')
            ->bindValue('issueID', $issueID);
        } else {
        $query = $this
            ->newSelect()
            ->cols(['helpDeskIssueDiscuss.*', 'gibbonPerson.title', 'gibbonPerson.surname', 'gibbonPerson.preferredName', 'gibbonPerson.image_240', 'gibbonPerson.username', 'gibbonPerson.email', 'helpDeskTechnicians.technicianID', '"Technician" AS type', '"Commented " AS action'])
            ->from('helpDeskIssueDiscuss')
            ->innerJoin('gibbonPerson', 'helpDeskIssueDiscuss.gibbonPersonID=gibbonPerson.gibbonPersonID')
            ->leftJoin('helpDeskTechnicians', 'helpDeskIssueDiscuss.gibbonPersonID=helpDeskTechnicians.gibbonPersonID')
            ->where('helpDeskIssueDiscuss.issueID = :issueID')
            ->bindValue('issueID', $issueID);
        }

        $result = $this->runSelect($query);

        return $result;
    }



    
}
