<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "top.php";
    $query = 'SELECT distinct fldBuilding, count(fldNumStudents), fldDays FROM tblSections where fldDays like "%F%" group by fldBuilding order by count(fldNumStudents) desc';
    $info2 = $thisDatabaseReader->select($query, "", 1, 1, 2, 0, false, false);
    echo count ($info2);
    print "<table>";
    $columns = 3;
    
    $highlight = 0; // used to highlight alternate rows
    foreach ($info2 as $rec) {
        $highlight++;
        if ($highlight % 2 != 0) {
            $style = ' odd ';
        } else {
            $style = ' even ';
        }
        print '<tr class="' . $style . '">';
        for ($i = 0; $i < $columns; $i++) {
            print '<td>' . $rec[$i] . '</td>';
        }
        print '</tr>';
    }
    // all done
    print '</table>';
    
include "footer.php";
?>

