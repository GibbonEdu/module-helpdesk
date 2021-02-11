<?php
namespace Gibbon\Module\HelpDesk\Domain;

use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;

/**
 * Department Permissions Gateway
 *
 * @version v20
 * @since   v20
 */
class DepartmentPermissionsGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'helpDeskDepartmentPermissions';
    private static $primaryKey = 'departmentPermissionsID';
    private static $searchableColumns = [];
    
    public function queryDeptPerms($criteria) {
        $query = $this
            ->newQuery()
            ->from('helpDeskDepartmentPermissions')
            ->cols(['departmentPermissionsID', 'helpDeskDepartmentPermissions.departmentID', 'departmentName', 'gibbonRoleID'])
            ->leftJoin('helpDeskDepartments', 'helpDeskDepartmentPermissions.departmentID=helpDeskDepartments.departmentID');

        $criteria->addFilterRules([
            'departmentID' => function ($query, $departmentID) {
                return $query
                    ->where('helpDeskDepartmentPermissions.departmentID = :departmentID')
                    ->bindValue('departmentID', $departmentID);
            },
            'gibbonRoleID' => function($query, $gibbonRoleID) {
                return $query
                    ->where('helpDeskDepartmentPermissions.gibbonRoleID = :gibbonRoleID')
                    ->bindValue('gibbonRoleID', $gibbonRoleID);
            }
        ]);

        return $this->runQuery($query, $criteria);
    }


}
