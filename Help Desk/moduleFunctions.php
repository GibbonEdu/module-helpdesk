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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

use Gibbon\Domain\User\RoleGateway;
use Gibbon\Domain\DataSet;
use Psr\Container\ContainerInterface;

function explodeTrim($commaSeperatedString) {
    //This could, in theory, be made for effiicent, however, I don't care to do so.
    return array_filter(array_map('trim', explode(',', $commaSeperatedString)));
}

function getRoles(ContainerInterface $container) {
	$roleGateway = $container->get(RoleGateway::class);
    $criteria = $roleGateway->newQueryCriteria()
        ->sortBy(['gibbonRole.name']);

    return array_reduce($roleGateway->queryRoles($criteria)->toArray(), function ($group, $role) {
        $group[$role['gibbonRoleID']] = __($role['name']) . ' (' . __($role['category']) . ')';
        return $group; 
    }, []);
}

function statsOverview(DataSet $logs) {
    //Count each log entry
	$items = array_count_values($logs->getColumn('title'));

    //Sort by the title of the entry
    ksort($items);

    //Map the associative array to be displayed in the table
    array_walk($items, function (&$value, $key) {
        $value = ['name' => $key, 'value' => $value];
    });

    return $items;
}

?>
