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
    private static $searchableColumns = [];

    public function selectIssueByTechnician($technicianID) {
        $data = array('technicianID' => $technicianID);
        $sql = "SELECT issueID, gibbonPersonID, issueName, description, date, status, category, priority, gibbonSchoolYearID, createdByID, privacySetting
                FROM helpDeskIssue
                WHERE technicianID=:technicianID
                ORDER BY issueID ASC";

        return $this->db()->select($sql, $data);
    }


     public function selectIssues() {
//  TODO: MAKE THIS WORK        
//      $query = $this
//             ->newQuery()
//             ->from('helpDeskIssue')
//             ->cols(['helpDeskIssue.*', 'techID.gibbonPersonID AS techPersonID'])
//             ->leftJoin('helpDeskTechnicians AS techID', 'helpDeskIssue.technicianID=techID.technicianID');
//             
//     $query->union()
//             ->from('helpDeskIssue')
//             ->cols(['helpDeskIssue.*', 'techID.gibbonPersonID AS techPersonID'])
//             ->where('helpDeskIssue.technicianID IS NULL');
//
//        return $this->runQuery($query, $criteria);
        $data = array();
        $sql = "( 
                SELECT  helpDeskIssue.*, techID.gibbonPersonID AS techPersonID FROM helpDeskIssue 
                LEFT JOIN helpDeskTechnicians AS techID ON helpDeskIssue.technicianID=techID.technicianID
                ) UNION ( 
                SELECT  helpDeskIssue.*, helpDeskIssue.technicianID as techPersonID FROM helpDeskIssue WHERE helpDeskIssue.technicianID IS NULL
                )
                ORDER BY issueID ASC";

        return $this->db()->select($sql, $data);
    }
}
