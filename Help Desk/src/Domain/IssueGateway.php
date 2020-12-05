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
class IssueGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'helpDeskIssue';
    private static $primaryKey = 'issueID';
    private static $searchableColumns = ['issueID', 'issueName', 'description'];

    public function selectActiveIssueByTechnician($technicianID) {
        $data = array('technicianID' => $technicianID);
        $sql = "SELECT issueID, gibbonPersonID, issueName, description, date, status, category, priority, gibbonSchoolYearID, createdByID
                FROM helpDeskIssue
                WHERE technicianID=:technicianID AND status='Pending'
                ORDER BY issueID ASC";

        return $this->db()->select($sql, $data);
    }
    
     public function queryIssues($criteria) {      
        $query = $this
            ->newQuery()
            ->from('helpDeskIssue')
            ->cols(['helpDeskIssue.*', 'techID.gibbonPersonID AS techPersonID', 'helpDeskDepartments.departmentName', 'helpDeskSubcategories.subcategoryName', 'helpDeskSubcategories.departmentID'])
            ->leftJoin('helpDeskTechnicians AS techID', 'helpDeskIssue.technicianID=techID.technicianID')
            ->leftJoin('helpDeskSubcategories', 'helpDeskIssue.subcategoryID=helpDeskSubcategories.subcategoryID')
            ->leftJoin('helpDeskDepartments', 'helpDeskSubcategories.departmentID=helpDeskDepartments.departmentID');

        $criteria->addFilterRules([
            'status' => function ($query, $status) {
                return $query
                    ->where('helpDeskIssue.status = :status')
                    ->bindValue('status', $status);
            },
            'category' => function ($query, $category) {
                return $query
                    ->where('helpDeskIssue.category = :category')
                    ->bindValue('category', $category);
            },
            'priority' => function ($query, $priority) {
                return $query
                    ->where('helpDeskIssue.priority = :priority')
                    ->bindValue('priority', $priority);
            },
            'year' => function($query, $year) {
                return $query
                    ->where('helpDeskIssue.gibbonSchoolYearID = :year')
                    ->bindValue('year', $year);
            },
            'subcategoryID' => function ($query, $subcategoryID) {
                return $query
                    ->where('helpDeskIssue.subcategoryID = :subcategoryID')
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
    
    public function isRelated($issueID, $gibbonPersonID) {
        $query = $this
            ->newQuery()
            ->from('helpDeskIssue')
            ->cols(['helpDeskIssue.gibbonPersonID', 'techID.gibbonPersonID AS techPersonID', 'helpDeskIssue.createdByID' ])
            ->leftJoin('helpDeskTechnicians AS techID', 'helpDeskIssue.technicianID=techID.technicianID')
            ->where('helpDeskIssue.issueID = :issueID')
            ->bindValue('issueID', $issueID);

        $issue = $this->runSelect($query);

        return $issue->isNotEmpty() ? in_array($gibbonPersonID, $issue->fetch()) : false;
    }

    //This can probably be simplfied, however, for now it works.
    public function getPeopleInvolved($issueID) {
        $people = array();

        $query = $this
            ->newQuery()
            ->from('helpDeskIssue')
            ->cols(['helpDeskIssue.gibbonPersonID', 'techID.gibbonPersonID AS techPersonID'])
            ->leftJoin('helpDeskTechnicians AS techID', 'helpDeskIssue.technicianID=techID.technicianID')
            ->where('helpDeskIssue.issueID = :issueID')
            ->bindValue('issueID', $issueID);

        $result = $this->runSelect($query);

        if ($result->isNotEmpty()) {
            foreach ($result->fetch() as $person) {
                if (!empty($person)) {
                    $people[] = $person;
                }
            }
        }

        $query = $this
            ->newQuery()
            ->distinct()
            ->from('helpDeskIssueDiscuss')
            ->cols(['helpDeskIssueDiscuss.gibbonPersonID', 'helpDeskTechGroups.fullAccess'])
            ->leftJoin('helpDeskTechnicians', 'helpDeskIssueDiscuss.gibbonPersonID=helpDeskTechnicians.gibbonPersonID')
            ->leftJoin('helpDeskTechGroups', 'helpDeskTechnicians.groupID=helpDeskTechGroups.groupID')
            ->where('helpDeskIssueDiscuss.issueID = :issueID')
            ->bindValue('issueID', $issueID)
            ->where('helpDeskTechGroups.fullAccess IS NOT null');
        
        $result = $this->runSelect($query);
        
        while ($person = $result->fetch()) {
            if (!in_array($person['gibbonPersonID'], $people) && $person['fullAccess']) {
                $people[] = $person['gibbonPersonID'];
            }
        }

        return $people;
    }

    
}
