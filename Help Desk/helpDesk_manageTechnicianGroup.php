<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

use Gibbon\Tables\DataTable;
use Gibbon\Services\Format;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

$page->breadcrumbs->add(__('Manage Technician Groups'));

if (!isActionAccessible($guid, $connection2, '/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_manageTechnicianGroup.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, array("errorA" => "Cannot delete last technician group."));
    }
   
    $techGroupGateway = $container->get(TechGroupGateway::class);
    $technicianGateway = $container->get(TechnicianGateway::class);   

    $techGroupData = $techGroupGateway->selectTechGroups()->toDataSet();

    $formatTechnicianList = function($row) use ($technicianGateway) {
        $technicians = $technicianGateway->selectTechniciansByTechGroup($row['groupID'])->fetchAll();
        if (count($technicians) < 1) {
            return __("No one is currently in this group.");
        }
        return Format::nameList($technicians, 'Student', false, false);
    };

    $table = DataTable::create('techGroups');
    $table->setTitle("Technician Groups");

    $table->addHeaderAction('add', __("Create"))
            ->setURL("/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_createTechnicianGroup.php");

    $table->addColumn('groupName', __("Group Name"));

    $table->addColumn('techs', __("Technicians in group"))
            ->format($formatTechnicianList);

    $table->addActionColumn()
            ->addParam('groupID')
            ->format(function ($techGroup, $actions) use ($guid, $techGroupData) {
                $actions->addAction('edit', __("Edit"))
                        ->setURL("/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_editTechnicianGroup.php");

                if (count($techGroupData) > 1) {
                    $actions->addAction('delete', __("Delete"))
                            ->setURL("/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_technicianGroupDelete.php");
                }
            });

    echo $table->render($techGroupData);    
}
?>
