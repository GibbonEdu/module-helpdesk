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
    
<<<<<<< Updated upstream
    public function selectTechGroupsByID($groupID) {
        $data = array('groupID' => $groupID);
        $sql = "SELECT groupID, groupName, viewIssue, viewIssueStatus, assignIssue, acceptIssue, resolveIssue, createIssueForOther, fullAccess, reassignIssue, reincarnateIssue
                FROM helpDeskTechGroups
                WHERE groupID=:groupID
                ORDER BY groupID ASC";

        return $this->db()->select($sql, $data);
=======
    public function selectTechGroupByID($groupID) {
        $query = $this
            ->newQuery()
            ->from('helpDeskTechGroups')
            ->cols(['helpDeskTechGroups.*'])
            ->where('helpDeskTechGroups.groupID=:groupID')
            ->bindValue('groupID', $groupID);;
             
             return $result = $this->runSelect($query);
>>>>>>> Stashed changes
    }

    public function getPermissionValue($gibbonPersonID, $permission)
    {
        $query = $this
            ->newSelect()
            ->distinct()
            ->cols(['viewIssue, viewIssueStatus, assignIssue, resolveIssue, createIssueForOther, fullAccess, reassignIssue, reincarnateIssue'])
            ->from($this->getTableName())
            ->leftJoin('helpDeskTechnicians', 'helpDeskTechGroups.groupID=helpDeskTechnicians.groupID')
            ->where('helpDeskTechnicians.gibbonPersonID = :gibbonPersonID')
            ->bindValue('gibbonPersonID', $gibbonPersonID);

        $result = $this->runSelect($query);

        //If there isn't one unique row, deny all permissions
        if ($result->rowCount() != 1) {
            return false;
        }

        $row = $result->fetch();

        //Check for fullAccess permissions
        if ($row['fullAccess'] == true) {
            if ($permission == "viewIssueStatus") {
                return "All";
            } else {
                return true;
            }
        }

        //Return permission that was asked for
        return $row[$permission];
    }

}
