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
class TechnicianGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'helpDeskTechnicians';
    private static $primaryKey = 'technicianID';
    private static $searchableColumns = [];

    public function selectTechnicians() {
        $data = array();
        $sql = 'SELECT helpDeskTechnicians.technicianID, helpDeskTechnicians.groupID, helpDeskTechGroups.groupName, gibbonPerson.gibbonPersonID, gibbonPerson.title, gibbonPerson.preferredName, gibbonPerson.surname, helpDeskTechGroups.departmentID
                FROM helpDeskTechnicians
                JOIN gibbonPerson ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID)
                JOIN helpDeskTechGroups ON (helpDeskTechnicians.groupID=helpDeskTechGroups.groupID)
                WHERE gibbonPerson.status="Full"
                ORDER BY helpDeskTechnicians.technicianID ASC';

        return $this->db()->select($sql, $data);
    }

    public function selectTechniciansByTechGroup($groupID) {
        $data = array('groupID' => $groupID);
        $sql = 'SELECT helpDeskTechnicians.technicianID, gibbonPerson.gibbonPersonID, gibbonPerson.title, gibbonPerson.preferredName, gibbonPerson.surname
                FROM helpDeskTechnicians
                JOIN gibbonPerson ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID)
                WHERE helpDeskTechnicians.groupID=:groupID
                AND gibbonPerson.status="Full"
                ORDER BY helpDeskTechnicians.technicianID ASC';

        return $this->db()->select($sql, $data);
    }

    public function getTechnician($technicianID) {
        $query = $this
            ->newQuery()
            ->from('helpDeskTechnicians')
            ->cols(['helpDeskTechnicians.technicianID', 'helpDeskTechnicians.gibbonPersonID', 'helpDeskTechnicians.groupID','gibbonPerson.title', 'gibbonPerson.surname', 'gibbonPerson.preferredName'])
            ->leftJoin('gibbonPerson', 'helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID')
            ->where('helpDeskTechnicians.technicianID = :technicianID')
            ->bindValue('technicianID', $technicianID);

        return $this->runSelect($query); 
    }

    public function getTechnicianByPersonID($gibbonPersonID) {
         $query = $this
            ->newQuery()
            ->from('helpDeskTechnicians')
            ->cols(['helpDeskTechnicians.technicianID', 'helpDeskTechnicians.gibbonPersonID', 'helpDeskTechnicians.groupID','gibbonPerson.title', 'gibbonPerson.surname', 'gibbonPerson.preferredName'])
            ->leftJoin('gibbonPerson', 'helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID')
            ->where('helpDeskTechnicians.gibbonPersonID = :gibbonPersonID')
            ->bindValue('gibbonPersonID', $gibbonPersonID);

        return $this->runSelect($query);
    }
}
