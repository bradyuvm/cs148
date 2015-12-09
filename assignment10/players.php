<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "top.php";
$admin = true;

    $query = 'SELECT tblTeams.fldName, fldFirstName, fldLastName, fldAge, fldNationality, fldPosition, fldNumber FROM `tblTeamPlayer`                                            
        Join tblTeams on `pmkTeamId`= `fnkTeamId`
        Join tblPlayers on `pmkPlayerId`= `fnkPlayerId`
        WHERE `fnkTeamId`= 2
        ';
    $info2 = $thisDatabaseReader->select($query, "", 1, 0, 0, 0, false, false);
  
    
    echo '<div class = "container">';
    echo '<div class = "jumbotron">';
    
    echo '<table class="table table-hover">';
    echo "<th> Team </th>";
    echo "<th> First Name </th>";
    echo "<th> Last Name </th>";
    echo "<th> Age </th>";
    echo "<th> Nationality </th>";
    echo "<th> Position </th>";
    echo "<th> Number </th>";
 
    $columns = 7;
   
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
    

    
//include "footer.php";
?>
