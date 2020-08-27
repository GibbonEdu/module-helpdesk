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
    private static $primaryKey = 'technicianID';
    private static $searchableColumns = [];

    public function selectIssueByTechnician($technicianID) {
        $data = array('technicianID' => $technicianID);
        $sql = "SELECT issueID, gibbonPersonID, issueName, description, date, status, category, priority, gibbonSchoolYearID, createdByID, privacySetting
                FROM helpDeskIssue
                WHERE technicianID=:technicianID
                ORDER BY issueID ASC";

        return $this->db()->select($sql, $data);
    }
}
