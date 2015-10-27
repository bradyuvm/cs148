$query = 'SELECT distinct fldBuilding, count(fldNumStudents), fldDays from tblSections where fldDays like "%W%" group by fldBuilding order by count(fldNumStudents) desc';
