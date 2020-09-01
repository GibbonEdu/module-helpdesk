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

    public function getIssueDiscussionByID($issueID) {
        $query = $this
            ->newSelect()
            ->cols(['helpDeskIssueDiscuss.*','helpDeskIssueDiscuss.timestamp AS type', 'gibbonPerson.title', 'gibbonPerson.surname', 'gibbonPerson.preferredName', 'gibbonPerson.image_240', 'gibbonPerson.username', 'gibbonPerson.email'])
            ->from('helpDeskIssueDiscuss')
            ->innerJoin('gibbonPerson', 'helpDeskIssueDiscuss.gibbonPersonID=gibbonPerson.gibbonPersonID')
            ->where('helpDeskIssueDiscuss.issueID = :issueID')
            ->bindValue('issueID', $issueID)
            ->orderBy(['helpDeskIssueDiscuss.timestamp']);

        $result = $this->runSelect($query);

        return $result;
    }



    
}
