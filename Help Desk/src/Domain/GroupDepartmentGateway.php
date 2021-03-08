<?php
namespace Gibbon\Module\HelpDesk\Domain;

use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;

/**
 * Tech Group Department Gateway
 *
 * @version v22
 * @since   v22
 */
class GroupDepartmentGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'helpDeskGroupDepartment';
    private static $primaryKey = 'groupDepartmentID';
    private static $searchableColumns = [];

    public function selectGroupDepartments($groupID) {
        $select = $this
            ->newSelect()
            ->from('helpDeskGroupDepartment')
            ->cols(['groupDepartmentID, groupID, helpDeskGroupDepartment.departmentID, departmentName'])
            ->leftJoin('helpDeskDepartments', 'helpDeskGroupDepartment.departmentID = helpDeskDepartments.departmentID')
            ->where('groupID = :groupID')
            ->bindValue('groupID', $groupID);

        return $this->runSelect($select);
    }

}
