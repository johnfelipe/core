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

@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Timetable Admin/courseEnrolment_manage_byPerson_edit_delete.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Check if school year specified
	$gibbonCourseClassID=$_GET["gibbonCourseClassID"] ;
	$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"] ;
	$gibbonPersonID=$_GET["gibbonPersonID"] ;
	$type=$_GET["type"] ;
	$allUsers=$_GET["allUsers"] ;
	$search=$_GET["search"] ;
		
	if ($gibbonPersonID=="" OR $gibbonCourseClassID=="" OR $gibbonSchoolYearID=="") {
		print "<div class='error'>" ;
			print _("You have not specified one or more required parameters.") ;
		print "</div>" ;
	}
	else {
		try {
			$data=array("gibbonCourseClassID"=>$gibbonCourseClassID, "gibbonPersonID"=>$gibbonPersonID); 
			$sql="SELECT role, gibbonPerson.preferredName, gibbonPerson.surname, gibbonPerson.gibbonPersonID, gibbonCourseClass.gibbonCourseClassID, gibbonCourseClass.name, gibbonCourseClass.nameShort, gibbonCourse.gibbonCourseID, gibbonCourse.name AS courseName, gibbonCourse.nameShort as courseNameShort, gibbonCourse.description AS courseDescription, gibbonCourse.gibbonSchoolYearID, gibbonSchoolYear.name as yearName FROM gibbonPerson, gibbonCourseClass, gibbonCourseClassPerson,gibbonCourse, gibbonSchoolYear WHERE gibbonPerson.gibbonPersonID=gibbonCourseClassPerson.gibbonPersonID AND gibbonCourseClassPerson.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID AND gibbonCourse.gibbonCourseID=gibbonCourseClass.gibbonCourseID AND gibbonCourse.gibbonSchoolYearID=gibbonSchoolYear.gibbonSchoolYearID AND gibbonCourseClass.gibbonCourseClassID=:gibbonCourseClassID AND gibbonPerson.gibbonPersonID=:gibbonPersonID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		
		if ($result->rowCount()!=1) {
			print "<div class='error'>" ;
				print _("The specified record cannot be found.") ;
			print "</div>" ;
		}
		else {
			//Let's go!
			$row=$result->fetch() ;
			print "<div class='trail'>" ;
			print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/courseEnrolment_manage_byPerson.php&gibbonSchoolYearID=" . $_GET["gibbonSchoolYearID"] . "&allUsers=$allUsers'>" . _('Enrolment by Person') . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/courseEnrolment_manage_byPerson_edit.php&gibbonCourseClassID=" . $_GET["gibbonCourseClassID"] . "&type=" . $_GET["type"] . "&gibbonSchoolYearID=" . $_GET["gibbonSchoolYearID"] . "&gibbonPersonID=" . $_GET["gibbonPersonID"] . "&allUsers=$allUsers'>" . $row["preferredName"] . " " . $row["surname"] . "</a> > </div><div class='trailEnd'>" . _('Delete Participant') . "</div>" ; 
			print "</div>" ;
			
			if (isset($_GET["deleteReturn"])) { $deleteReturn=$_GET["deleteReturn"] ; } else { $deleteReturn="" ; }
			$deleteReturnMessage="" ;
			$class="error" ;
			if (!($deleteReturn=="")) {
				if ($deleteReturn=="fail0") {
					$deleteReturnMessage=_("Your request failed because you do not have access to this action.") ;	
				}
				else if ($deleteReturn=="fail1") {
					$deleteReturnMessage=_("Your request failed because your inputs were invalid.") ;	
				}
				else if ($deleteReturn=="fail2") {
					$deleteReturnMessage=_("Your request failed due to a database error.") ;	
				}
				else if ($deleteReturn=="fail3") {
					$deleteReturnMessage=_("Your request failed because your inputs were invalid.") ;	
				}
				print "<div class='$class'>" ;
					print $deleteReturnMessage;
				print "</div>" ;
			} 
	
			print "<div class='linkTop'>" ;
				if ($search!="") {
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Timetable Admin/courseEnrolment_manage_byPerson_edit.php&gibbonCourseClassID=$gibbonCourseClassID&gibbonPersonID=$gibbonPersonID&allUsers=$allUsers&search=$search&gibbonSchoolYearID=$gibbonSchoolYearID&type=$type'>" . _('Back') . "</a>" ;
				}
			print "</div>" ;
			?>
			<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/courseEnrolment_manage_byPerson_edit_deleteProcess.php?gibbonCourseClassID=$gibbonCourseClassID&type=$type&gibbonSchoolYearID=$gibbonSchoolYearID&gibbonPersonID=$gibbonPersonID&allUsers=$allUsers&search=$search" ?>">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
					<tr>
						<td> 
							<b><?php print _('Are you sure you want to delete this record?') ; ?></b><br/>
							<span style="font-size: 90%; color: #cc0000"><i><?php print _('This operation cannot be undone, and may lead to loss of vital data in your system. PROCEED WITH CAUTION!') ; ?></i></span>
						</td>
						<td class="right">
							
						</td>
					</tr>
					<tr>
						<td> 
							<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
							<input type="submit" value="<?php print _('Yes') ; ?>">
						</td>
						<td class="right">
							
						</td>
					</tr>
				</table>
			</form>
			<?php
		}
	}
}
?>