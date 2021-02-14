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
        $select = $this
            ->newSelect()
            ->from('helpDeskIssue')
            ->cols(['issueID', 'issueName'])
            ->where('technicianID = :technicianID')
            ->bindValue('technicianID', $technicianID)
            ->where('status = \'Pending\'')
            ->orderBy(['issueID']);
            
        return $this->runSelect($select);
    }

    public function getIssueByID($issueID) {
        $criteria = $this->newQueryCriteria(false)
            ->filterBy('issueID', $issueID);

        $results = $this->queryIssues($criteria);
        return $results->getRow(0);
    }      
    
    public function queryIssues($criteria, $gibbonSchoolYearID = null, $gibbonPersonID = null, $relation = null, $viewIssueStatus = null, $departmentID = null) {      
        $query = $this
            ->newQuery()
            ->from('helpDeskIssue')
            ->cols(['helpDeskIssue.*', 'techID.gibbonPersonID AS techPersonID', 'helpDeskDepartments.departmentName', 'helpDeskSubcategories.subcategoryName', 'helpDeskSubcategories.departmentID', 'gibbonSpace.name AS facility'])
            ->leftJoin('helpDeskTechnicians AS techID', 'helpDeskIssue.technicianID=techID.technicianID')
            ->leftJoin('helpDeskSubcategories', 'helpDeskIssue.subcategoryID=helpDeskSubcategories.subcategoryID')
            ->leftJoin('helpDeskDepartments', 'helpDeskSubcategories.departmentID=helpDeskDepartments.departmentID')
            ->leftJoin('gibbonSpace', 'helpDeskIssue.gibbonSpaceID=gibbonSpace.gibbonSpaceID');

        if (!empty($gibbonSchoolYearID)) {
            $query->where('helpDeskIssue.gibbonSchoolYearID = :year')
                ->bindValue('year', $gibbonSchoolYearID);
        }
        
        if ($relation == 'My Issues') {
            $query->where('helpDeskIssue.gibbonPersonID = :gibbonPersonID')
                ->bindValue('gibbonPersonID', $gibbonPersonID);
        } else {
            if ($viewIssueStatus == 'PR') {
                $query->where('helpDeskIssue.status <> Unassigned');
            } else if ($viewIssueStatus == 'UP') {
                $query->where('helpDeskIssue.status <> Resolved');
            } else if ($viewIssueStatus == 'Pending') {
                $query->where('helpDeskIssue.status = Pending');
            }

            if (!empty($departmentID)) {
                $query->where('helpDeskSubcategories.departmentID = :departmentID')
                    ->bindValue('departmentID', $departmentID); 
            }

            if ($relation == 'My Assigned') {
                $query->where('techID.gibbonPersonID=:techPersonID')
                    ->bindValue('techPersonID', $gibbonPersonID);
            }
        }

        $criteria->addFilterRules([
            'issueID' => function($query, $issueID) {
                return $query
                    ->where('helpDeskIssue.issueID = :issueID')
                    ->bindValue('issueID', $issueID);
            },
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
