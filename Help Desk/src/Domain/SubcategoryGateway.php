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
class SubcategoryGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'helpDeskSubcategories';
    private static $primaryKey = 'subcategoryID';
    private static $searchableColumns = [];

    public function querySubcategories($criteria) {
        $query = $this
            ->newQuery()
            ->from('helpDeskSubcategories')
            ->cols(['subcategoryID', 'subcategoryName', 'helpDeskSubcategories.departmentID', 'departmentName', 'departmentDesc'])
            ->leftjoin('helpDeskDepartments', 'helpDeskSubcategories.departmentID=helpDeskDepartments.departmentID');

        $criteria->addFilterRules([
            'subcategoryID' => function ($query, $subcategoryID) {
                return $query
                    ->where('helpDeskSubcategories.subcategoryID = :subcategoryID')
                    ->bindValue('subcategoryID', $subcategoryID);
            },
            'departmentID' => function ($query, $departmentID) {
                return $query
                    ->where('helpDeskSubcategories.departmentID = :departmentID')
                    ->bindValue('departmentID', $departmentID);
            },
            'gibbonRoleID' => function ($query, $gibbonRoleID) {
                return $query
                    ->where('helpDeskDepartments.departmentID IN (SELECT departmentID FROM helpDeskDepartmentPermissions WHERE gibbonRoleID = :gibbonRoleID)')
                     ->bindValue('gibbonRoleID', $gibbonRoleID);
            },
            
            
        ]);

        return $this->runQuery($query, $criteria);
    }

    public function deleteSubcategory($subcategoryID) {
        $this->db()->beginTransaction();

        $query = $this
            ->newUpdate()
            ->table('helpDeskIssue')
            ->set('subcategoryID', NULL)
            ->where('subcategoryID = :subcategoryID')
            ->bindValue('subcategoryID', $subcategoryID);

        $this->runUpdate($query);

        if (!$this->db()->getQuerySuccess()) {
            $this->db()->rollBack();
            return false;
        }


        $this->delete($subcategoryID);

        if (!$this->db()->getQuerySuccess()) {
            $this->db()->rollBack();
            return false;
        }

        $this->db()->commit();
        return true;
    }
    
}
