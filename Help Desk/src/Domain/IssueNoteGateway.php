<?php
namespace Gibbon\Module\HelpDesk\Domain;

use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;

/**
 * Issue Note Gateway
 *
 * @version v22
 * @since   v22
 */
class IssueNoteGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'helpDeskIssueNotes';
    private static $primaryKey = 'issueNoteID';
    private static $searchableColumns = [];

    public function getIssueNotesByID($issueID) {
        $query = $this
            ->newSelect()
            ->cols(['helpDeskIssueNotes.note as comment', 'helpDeskIssueNotes.timestamp', 'helpDeskIssueNotes.gibbonPersonID', 'gibbonPerson.title', 'gibbonPerson.surname', 'gibbonPerson.preferredName', 'gibbonPerson.image_240', 'gibbonPerson.username', 'gibbonPerson.email', '"Commented " AS action'])
            ->from('helpDeskIssueNotes')
            ->innerJoin('gibbonPerson', 'helpDeskIssueNotes.gibbonPersonID=gibbonPerson.gibbonPersonID')
            ->where('helpDeskIssueNotes.issueID = :issueID')
            ->bindValue('issueID', $issueID);

        $result = $this->runSelect($query);

        return $result;
    }
}
