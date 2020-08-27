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
class TechGroupGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'helpDeskTechGroups';
    private static $primaryKey = 'groupID';
    private static $searchableColumns = [];

    public function selectTechGroups() {
        $data = array();
        $sql = "SELECT groupID, groupName, viewIssue, viewIssueStatus, assignIssue, acceptIssue, resolveIssue, createIssueForOther, fullAccess, reassignIssue, reincarnateIssue
                FROM helpDeskTechGroups
                ORDER BY groupID ASC";

        return $this->db()->select($sql, $data);
    }

}
