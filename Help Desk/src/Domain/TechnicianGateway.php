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
        $query = $this
            ->newSelect()
            ->from('helpDeskTechnicians')
            ->cols(['helpDeskTechnicians.technicianID', 'helpDeskTechnicians.groupID', 'helpDeskTechGroups.groupName', 'gibbonPerson.gibbonPersonID', 'gibbonPerson.title', 'gibbonPerson.preferredName', 'gibbonPerson.surname', 'helpDeskTechGroups.departmentID'])
            ->leftJoin('gibbonPerson', 'gibbonPerson.gibbonPersonID=helpDeskTechnicians.gibbonPersonID')
            ->leftJoin('helpDeskTechGroups', 'helpDeskTechGroups.groupID=helpDeskTechnicians.groupID')
            ->where('gibbonPerson.status="Full"')
            ->orderBy(['helpDeskTechnicians.technicianID']);

        return $this->runSelect($query);
    }

    public function selectTechniciansByTechGroup($groupID) {
        $query = $this
            ->newSelect()
            ->from('helpDeskTechnicians')
            ->cols(['helpDeskTechnicians.technicianID', 'gibbonPerson.gibbonPersonID', 'gibbonPerson.title', 'gibbonPerson.preferredName', 'gibbonPerson.surname'])
            ->leftJoin('gibbonPerson', 'gibbonPerson.gibbonPersonID=helpDeskTechnicians.gibbonPersonID')
            ->where('helpDeskTechnicians.groupID = :groupID')
            ->bindValue('groupID', $groupID)
            ->where('gibbonPerson.status = "Full"')
            ->orderBy(['helpDeskTechnicians.technicianID']);

        return $this->runSelect($query);
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
