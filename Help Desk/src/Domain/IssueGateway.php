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
class IssueGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'helpDeskIssue';
    private static $primaryKey = 'issueID';
    private static $searchableColumns = ['issueID', 'issueName', 'description'];

    public function selectIssueByTechnician($technicianID) {
        $data = array('technicianID' => $technicianID);
        $sql = "SELECT issueID, gibbonPersonID, issueName, description, date, status, category, priority, gibbonSchoolYearID, createdByID, privacySetting
                FROM helpDeskIssue
                WHERE technicianID=:technicianID
                ORDER BY issueID ASC";

        return $this->db()->select($sql, $data);
    }


     public function queryIssues($criteria) {      
        $query = $this
            ->newQuery()
            ->from('helpDeskIssue')
            ->cols(['helpDeskIssue.*', 'techID.gibbonPersonID AS techPersonID'])
            ->leftJoin('helpDeskTechnicians AS techID', 'helpDeskIssue.technicianID=techID.technicianID');

        $criteria->addFilterRules([
            'status' => function ($query, $status) {
                return $query
                    ->where('helpDeskIssue.status = :status')
                    ->bindValue('status', $status);
            },
            'category' => function ($query, $category) {
                return $query
                    ->where('helpDeskIssue.category = :category')
                    ->bindValue('category', $category);
            },
            'priority' => function ($query, $priority) {
                return $query
                    ->where('helpDeskIssue.priority = :priority')
                    ->bindValue('priority', $priority);
            },
            'year' => function($query, $year) {
                return $query
                    ->where('helpDeskIssue.gibbonSchoolYearID = :year')
                    ->bindValue('year', $year);
            }
        ]);

       return $this->runQuery($query, $criteria);
    }
}
