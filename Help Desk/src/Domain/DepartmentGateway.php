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
class DepartmentGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'helpDeskDepartments';
    private static $primaryKey = 'departmentID';
    private static $searchableColumns = [];
    
    public function selectDepartments() {
        $select = $this
            ->newSelect()
            ->from('helpDeskDepartments')
            ->cols(['departmentID', 'departmentName', 'departmentDesc'])
            ->orderBy(['departmentID']);

        return $this->runSelect($select);
    }
    
    public function deleteDepartment($departmentID) {
        $this->db()->beginTransaction();

        //Update issues to remove subcategories to be deleted
        $query = $this
            ->newUpdate() 
            ->table('helpDeskIssue')
            ->set('subcategoryID', NULL)
            ->where('subcategoryID IN (SELECT subcategoryID FROM helpDeskSubcategories WHERE departmentID = :departmentID)')
            ->bindValue('departmentID', $departmentID);

        $this->runUpdate($query);

        if (!$this->db()->getQuerySuccess()) {
            $this->db()->rollBack();
            return false;
        }

        //Delete subcategories
        $query = $this
            ->newDelete()
            ->from('helpDeskSubcategories')
            ->where('departmentID = :departmentID')
            ->bindValue('departmentID', $departmentID);

        $this->runDelete($query);

        if (!$this->db()->getQuerySuccess()) {
            $this->db()->rollBack();
            return false;
        }

        //Delete group departments
        $query = $this
            ->newDelete()
            ->from('helpDeskGroupDepartment')
            ->where('departmentID = :departmentID')
            ->bindValue('departmentID', $departmentID);

        $this->runDelete($query);

        if (!$this->db()->getQuerySuccess()) {
            $this->db()->rollBack();
            return false;
        }

        $query = $this
            ->newDelete()
            ->from('helpDeskDepartmentPermissions')
            ->where('departmentID = :departmentID')
            ->bindValue('departmentID', $departmentID);

        $this->runDelete($query);

        if (!$this->db()->getQuerySuccess()) {
            $this->db()->rollBack();
            return false;
        }

        //Delete Department
        $this->delete($departmentID);

        if (!$this->db()->getQuerySuccess()) {
            $this->db()->rollBack();
            return false;
        }


        $this->db()->commit();
        return true;
    }
}
