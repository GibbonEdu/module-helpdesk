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
        $data = array();
        $sql = "SELECT *
                FROM helpDeskDepartments
                ORDER BY departmentID ASC";

        return $this->db()->select($sql, $data);
    }
    
    public function deleteDepartment($departmentID) {
        //TODO: Transaction

        //Update issues to remove subcategories to be deleted
        $data = array('departmentID' => $departmentID);
        $sql = 'UPDATE helpDeskIssue
                LEFT JOIN helpDeskSubcategories ON (helpDeskIssue.subcategoryID = helpDeskSubcategories.subcategoryID)
                SET helpDeskIssue.subcategoryID = NULL
                WHERE helpDeskSubcategories.departmentID = :departmentID';

        $this->db()->update($sql, $data);

        if (!$this->db()->getQuerySuccess()) {
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
            return false;
        }

        $query = $this
            ->newUpdate()
            ->table('helpDeskTechGroups')
            ->set('departmentID', NULL)
            ->where('departmentID = :departmentID')
            ->bindValue('departmentID', $departmentID);

        $this->runUpdate($query);

        if (!$this->db()->getQuerySuccess()) {
            return false;
        }

        $this->delete($departmentID);

        //Delete Department
        return $this->db()->getQuerySuccess();
    }
}
