$query = 'SELECT distinct fldBuilding, count(fldNumStudents), fldDays FROM tblSections where fldDays like "%F%" group by fldBuilding order by count(fldNumStudents) desc';
