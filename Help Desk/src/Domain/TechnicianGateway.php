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
            ->cols(['helpDeskTechnicians.technicianID', 'helpDeskTechnicians.groupID', 'helpDeskTechGroups.groupName', 'gibbonPerson.gibbonPersonID', 'gibbonPerson.title', 'gibbonPerson.preferredName', 'gibbonPerson.surname'])
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

    public function deleteTechnician($technicianID, $newTechnicianID) {
        $this->db()->beginTransaction();

        //If there is no new tech, reset the Pending Issues
        if (empty($newTechnicianID)) {
            $newTechnicianID = NULL;

            $query = $this
                ->newUpdate()
                ->table('helpDeskIssue')
                ->set('technicianID', $newTechnicianID)
                ->set('status', '"Unassigned"')
                ->where('technicianID = :technicianID')
                ->bindValue('technicianID', $technicianID)
                ->where('status = "Pending"');

            $this->runUpdate($query);

            if (!$this->db()->getQuerySuccess()) {
                $this->db()->rollBack();
                return false;
            }
        }

        //Change over the assigned issues
        $query = $this
            ->newUpdate()
            ->table('helpDeskIssue')
            ->set('technicianID', $newTechnicianID)
            ->where('technicianID = :technicianID')
            ->bindValue('technicianID', $technicianID);

        $this->runUpdate($query);

        if (!$this->db()->getQuerySuccess()) {
            $this->db()->rollBack();
            return false;
        }

        //Delete the tech
        $this->delete($technicianID);

        if (!$this->db()->getQuerySuccess()) {
            $this->db()->rollBack();
            return false;
        }

        $this->db()->commit();
        return true;
    }
}
