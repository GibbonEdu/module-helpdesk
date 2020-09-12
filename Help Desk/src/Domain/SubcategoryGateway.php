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
            ->cols(['subcategoryID', 'subcategoryName', 'departmentID', 'departmentName', 'departmentDesc'])
            ->leftjoin('helpDeskDepartments', 'helpDeskSubcategories.departmentID=helpDeskSubcategories.departmentID');

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
        ]);

        return $this->runQuery($query, $criteria);
    }
    
}
