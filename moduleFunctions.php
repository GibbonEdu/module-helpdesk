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


function isTechnician($gibbonPersonID, $connection2){
  try {
    $data=array("gibbonPersonID"=> $gibbonPersonID);
    $sql="SELECT * FROM helpDeskTechnicians WHERE gibbonPersonID=:gibbonPersonID";
    $result=$connection2->prepare($sql);
    $result->execute($data);
  }
  catch(PDOException $e) {
	   print $e;
  }

  return ($result->rowCount()==1);
}

function getTechnicianID($gibbonPersonID, $connection2){
  try {
    $data=array("gibbonPersonID"=> $gibbonPersonID);
    $sql="SELECT * FROM helpDeskTechnicians WHERE helpDeskTechnicians.gibbonPersonID=:gibbonPersonID ";
    $result=$connection2->prepare($sql);
    $result->execute($data);
  }
  catch(PDOException $e) {
	   print $e;
  }
  $id = null;
  if($result->rowCount()==1){
  	$array = $result->fetch();
  	$id = (int)$array["technicianID"];
  }
  return $id;
}

function hasTechnicianAssigned($issueID, $connection2)
{
  try {
    $data=array("issueID"=> $issueID);
    $sql="SELECT * FROM helpDeskIssue WHERE helpDeskIssue.issueID=:issueID ";
    $result=$connection2->prepare($sql);
    $result->execute($data);
  }
  catch(PDOException $e) {
	print $e;
  }
  $array = $result->fetchAll();
  $id = $array[0]["technicianID"];
  return ($id != null);
}

function getAllTechnicians($connection2, $returnName=FALSE)
{
  try {
    $data=array();
    $sql="SELECT helpDeskTechnicians.*, surname, preferredName
	FROM helpDeskTechnicians
	JOIN gibbonPerson ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID)
	WHERE status='Full'";
    $result=$connection2->prepare($sql);
    $result->execute($data);
  }
  catch(PDOException $e) {
	print $e;
  }

  if($returnName)
  {
  	$array = array();
	while($row=$result->fetch()){
	  array_push($array, ($row["preferredName"]. " " .$row["surname"]));
	}
	return $array;
  }
  else
  {
    $array = $result->fetchAll();
    return $array;
  }
}

function getTechnicianIDViaName($connection2, $name)
{
  try {
    $data=array();
    $sql="SELECT helpDeskTechnicians.*, surname, preferredName
	FROM helpDeskTechnicians
	JOIN gibbonPerson ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID)
	WHERE status='Full'";
    $result=$connection2->prepare($sql);
    $result->execute($data);
  }
  catch(PDOException $e) {
	print $e;
  }
  while($row=$result->fetch()){
	  $name2 = $row["preferredName"]." ".$row["surname"];
	  if($name == $name2){
	  	return (int)$row["technicianID"];
	  }
  }
  return null;
}

function technicianExists($connection2, $technicianID)
{
  try {
    $data=array("technicianID"=> $technicianID);
    $sql="SELECT * FROM helpDeskTechnicians WHERE technicianID=:technicianID";
    $result=$connection2->prepare($sql);
    $result->execute($data);
  }
  catch(PDOException $e) {
	   print $e;
  }

  return ($result->rowCount()==1);
}

function technicianExistsFromPersonID($connection2, $gibbonPersonID) {
  try {
    $data=array("gibbonPersonID"=> $gibbonPersonID);
    $sql="SELECT * FROM helpDeskTechnicians WHERE gibbonPersonID=:gibbonPersonID";
    $result=$connection2->prepare($sql);
    $result->execute($data);
  }
  catch(PDOException $e) {
    print $e;
  }

  return ($result->rowCount()==1);
}

?>
