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
        $query = $this
            ->newSelect()
            ->cols([
                'groupID','groupName',
                'viewIssue', 'viewIssueStatus',
                'assignIssue', 'acceptIssue', 'resolveIssue', 'createIssueForOther', 'fullAccess', 'reassignIssue', 'reincarnateIssue',
            ])
            ->from('helpDeskTechGroups')
            ->orderBy(['groupID']);

        return $this->runSelect($query);
    }

    public function getPermissionValue($gibbonPersonID, $permission)
    {
        $query = $this
            ->newSelect()
            ->distinct()
            ->cols(['viewIssue, viewIssueStatus, assignIssue, acceptIssue, resolveIssue, createIssueForOther, fullAccess, reassignIssue, reincarnateIssue'])
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
            if ($permission == 'viewIssueStatus') {
                return 'All';
            } else {
                return true;
            }
        }

        //Return permission that was asked for
        return $row[$permission];
    }

    public function deleteTechGroup($groupID, $newGroupID) {
        $this->db()->beginTransaction();

        $query = $this
            ->newUpdate()
            ->table('helpDeskTechnicians')
            ->set('groupID', $newGroupID)
            ->where('groupID = :groupID')
            ->bindValue('groupID', $groupID);

        $this->runUpdate($query);

        if (!$this->db()->getQuerySuccess()) {
            $this->db()->rollback();
            return false;
        }

        $this->delete($groupID);

        if (!$this->db()->getQuerySuccess()) {
            $this->db()->rollback();
            return false;
        }

        $this->db()->commit();
        return true;
    }

}
