<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "top.php";
$admin = true;

    $query = 'SELECT tblTeams.fldName, fldWin, fldTie, fldLoss, fldgf, fldga, fldgd, fldPoints, tblTeams.pmkTeamId FROM tblRecord JOIN tblTeams on tblTeams.pmkTeamId = tblRecord.pmkTeamId ORDER BY fldPoints Desc, fldgd Desc';
    $info2 = $thisDatabaseReader->select($query, "", 0, 1, 0, 0, false, false);
    echo '<div class = "container">';
    
    echo '<div class = "jumbotron">';
    
    echo '<table class="table table-hover">';
    echo "<th> Team </th>";
    echo "<th> Wins </th>";
    echo "<th> Draws </th>";
    echo "<th> Losses </th>";
    echo "<th> Goals For </th>";
    echo "<th> Goals Against </th>";
    echo "<th> Goal Difference </th>";
    echo "<th> Points </th>";
    
    $columns = 8;
   
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
            
            print '<td> ' . $rec[$i] . '</td>';
        }
        print '</tr>';
    }
    // all done
    
       
    print '</table>';
    echo '</div>';
    
    
include "footer.php";
?>
