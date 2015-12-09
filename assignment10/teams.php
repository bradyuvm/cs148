<?php
/* %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
 * the purpose of this page is to display a list of poets, admin can edit
 * 
 * Written By: Robert Erickson robert.erickson@uvm.edu
 */
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%

include "top.php";
$admin = true;
print "<article>";
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// prepare the sql statement
$orderBy = "ORDER BY fldName";

$query  = "SELECT pmkTeamId, fldName ";
$query .= "FROM tblTeams " . $orderBy;
//print $query;

if ($debug)
    print "<p>sql " . $query;

$teams = $thisDatabaseReader->select($query, "", 0, 1, 0, 0, false, false);

echo '<div class = "container">';
echo '<div class = "jumbotron">';

if ($debug) {
    print "<pre>";
    print_r($teams);
    print "</pre>";
}

// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// print out the results
print "<ol>\n";

foreach ($teams as $team) {

    print "<li>";
   /* if ($admin) {
        print '<a href="form.php?id=' . $team["pmkTeamId"] . '">[Edit]</a> ';
    }*/
    print $team['fldName'] . "</li>\n";
}
print "</ol>\n";
print "</article>";
include "footer.php";
?>