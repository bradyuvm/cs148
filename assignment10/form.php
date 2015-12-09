<?php


include "top.php";
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
$update = false;

// SECTION: 1a.
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.
$yourURL = $domain . $phpSelf;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables
//
// Initialize variables one for each form element
// in the order they appear on the form
$email = "youremail@uvm.edu";


if (isset($_GET["id"])) {
    $pmkTeamId = (int) htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");

    $query = 'SELECT fldWin, fldTie, fldLoss, fldgf, fldga, fldgd, fldPoints ';
    $query .= 'FROM tblRecord JOIN tblTeams on tblTeams.pmkTeamId = tblRecord.pmkTeamId WHERE pmkTeamId = ?';

    
    $results = $thisDatabase->select($query, array($pmkTeamId), 1, 0, 0, 0, false, false);

    $wins = $results[0]["fldWin"];
    $ties = $results[0]["fldTie"];
    $loss = $results[0]["fldLoss"];
    $goalsFor = $results[0]["fldgf"];
    $goalsAgainst = $results[0]["fldga"];
    $goalDiff = $results[0]["fldgd"];
    $points = $results[0]["fldPoints"];
} else {
    $pmkTeamId = -1;
    $wins = "";
    $ties = "";
    $loss = "";
    $goalsFor = "";
    $goalsAgainst = "";
    $goalDiff = "";
    $points = "";
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$emailERROR = false;

$winsERROR = false;
$tiesERROR = false;
$lossERROR = false;
$goalsForERROR = false;
$goalsAgainstERROR = false;
$goalDiffERROR = false;
$pointsERROR = false;
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();

// used for building email message to be sent and displayed
$mailed = false;
$messageA = "";
$messageB = "";
$messageC = "";

$errorMsg = array();
$data = array();
$dataEntered = false;

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
//
if (isset($_POST["btnSubmit"])) {
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2a Security
//
        if (!securityCheck(true)) {
      $msg = "<p>Sorry you cannot access this page. ";
      $msg.= "Security breach detected and reported</p>";
      die($msg);
      }
     
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2b Sanitize (clean) data
// remove any potential JavaScript or html code from users input on the
// form. Note it is best to follow the same order as declared in section 1c.
     $email = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL); 
      
      $pmkTeamId = (int) htmlentities($_POST["hidTeamId"], ENT_QUOTES, "UTF-8");
    if ($pmkTeamId > 0) {
        $update = true;
    }
    // I am not putting the ID in the $data array at this time

    $wins = htmlentities($_POST["txtWins"], ENT_QUOTES, "UTF-8");
    $data[] = $firstName;

    $ties = htmlentities($_POST["txtTies"], ENT_QUOTES, "UTF-8");
    $data[] = $lastName;

    $loss = htmlentities($_POST["txtLoss"], ENT_QUOTES, "UTF-8");
    $data[] = $loss;
    
    $goalsFor = htmlentities($_POST["txtFor"], ENT_QUOTES, "UTF-8");
    $data[] = $goalsFor;
    
    $goalsAgainst = htmlentities($_POST["txtDiff"], ENT_QUOTES, "UTF-8");
    $data[] = $goalsAgainst;
    
    $goalDiff = htmlentities($_POST["txtDiff"], ENT_QUOTES, "UTF-8");
    $data[] = $goalDiff;
    
    $points = htmlentities($_POST["txtPoints"], ENT_QUOTES, "UTF-8");
    $data[] = $points;

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2c Validation
//

    if ($email == "") {
        $errorMsg[] = "Please enter your email address";
        $emailERROR = true;
    } elseif (!verifyEmail($email)) {
        $errorMsg[] = "Your email address appears to be incorrect.";
        $emailERROR = true;
    }
    
    /*if ($firstName == "") {
        $errorMsg[] = "Please enter your first name";
        $firstNameERROR = true;
    } elseif (!verifyAlphaNum($firstName)) {
        $errorMsg[] = "Your first name appears to have extra character.";
        $firstNameERROR = true;
    }

    if ($lastName == "") {
        $errorMsg[] = "Please enter your last name";
        $lastNameERROR = true;
    } elseif (!verifyAlphaNum($lastName)) {
        $errorMsg[] = "Your last name appears to have extra character.";
        $lastNameERROR = true;
    }

    if ($birthday == "") {
        $errorMsg[] = "Please enter the poets birthday";
        $birthdayERROR = true;
    }// should check to make sure its the correct date format*/
    if ($wins == "") {
        $errorMsg[] = "Please enter total wins";
        $winsERROR = true;
    }
    
    if ($ties == "") {
        $errorMsg[] = "Please enter total draws";
        $tiesERROR = true;
    }
    
    if ($loss == "") {
        $errorMsg[] = "Please enter total losses";
        $lossERROR = true;
    }
    
    if ($goalsFor == "") {
        $errorMsg[] = "Please enter total goals for";
        $goalsForERROR = true;
    }
    
    if ($goalsAgainst == "") {
        $errorMsg[] = "Please enter total goals against";
        $goalsAgainstERROR = true;
    }
    
    if ($goalDiff == "") {
        $errorMsg[] = "Please enter goal differential";
        $goalDiffERROR = true;
    }
    
    if ($points == "") {
        $errorMsg[] = "Please enter total points";
        $pointsERROR = true;
    }
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2d Process Form - Passed Validation
//
// Process for when the form passes validation (the errorMsg array is empty)
//
    if (!$errorMsg) {
        if ($debug) {
            print "<p>Form is valid</p>";
        }

        // write out message
        $message = '<h2>Your information.</h2>';

        foreach ($_POST as $key => $value) {

            $message .= "<p>";

            $camelCase = preg_split('/(?=[A-Z])/', substr($key, 3));

            foreach ($camelCase as $one) {
                $message .= $one . " ";
            }
            $message .= " = " . htmlentities($value, ENT_QUOTES, "UTF-8") . "</p>";
        }    
        
        
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2e Save Data
//

        $dataEntered = false;
        try {
            $thisDatabaseWriter->db->beginTransaction();

            if ($update) {
                $query = 'UPDATE tblRecord SET ';
            } else {
                $query = 'INSERT INTO tblRecord SET ';
            }

            $query .= 'fldWin = ?, ';
            $query =  'fldTie = ? ';
            $query =  'fldLoss = ?, ';
            $query =  'fldgf = ?, ';
            $query =  'fldga = ?, ';
            $query =  'fldgd = ?, ';
            $query =  'fldPoints = ? ';
            

            if ($update) {
                $query .= 'WHERE pmkTeamId = ?';
                $data[] = $pmkTeamId;

                if ($_SERVER["REMOTE_USER"] == 'bcorbier') {
                    $results = $thisDatabaseWriter->update($query, $data, 1, 0, 0, 0, false, false);
                }
            } else {
                if ($_SERVER["REMOTE_USER"] == 'bcorbier'){
                    $results = $thisDatabaseWriter->insert($query, $data);
                    $primaryKey = $thisDatabaseWriter->lastInsert();
                    if ($debug) {
                        print "<p>pmk= " . $primaryKey;
                    }
                }
            }

            // all sql statements are done so lets commit to our changes
            //if($_SERVER["REMOTE_USER"]=='rerickso'){
            $dataEntered = $thisDatabaseWriter->db->commit();
            // }else{
            //     $thisDatabase->db->rollback();
            // }
            if ($debug)
                print "<p>transaction complete ";
        } catch (PDOExecption $e) {
            $thisDatabaseWriter->db->rollback();
            if ($debug)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
        }
    } // end form is valid
} // ends if form was submitted.
//#############################################################################
//
// SECTION 3 Display Form
//


?>
<article id="main">
    <?php
//####################################
//
// SECTION 3a.
//
//
//
//
// If its the first time coming to the form or there are errors we are going
// to display the form.
    if ($dataEntered) { // closing of if marked with: end body submit
        print "<h1>Record Saved</h1> ";
    } else {
//####################################
//
// SECTION 3b Error Messages
//
// display any error messages before we print out the form
        if ($errorMsg) {
            print '<div id="errors">';
            print '<h1>Your form has the following mistakes</h1>';

            print "<ol>\n";
            foreach ($errorMsg as $err) {
                print "<li>" . $err . "</li>\n";
            }
            print "</ol>\n";
            print '</div>';
        }
//####################################
//
// SECTION 3c html Form
//
        /* Display the HTML form. note that the action is to this same page. $phpSelf
          is defined in top.php
          NOTE the line:
          value="<?php print $email; ?>
          this makes the form sticky by displaying either the initial default value (line 35)
          or the value they typed in (line 84)
          NOTE this line:
          <?php if($emailERROR) print 'class="mistake"'; ?>
          this prints out a css class so that we can highlight the background etc. to
          make it stand out that a mistake happened here.
         */
        ?>
        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmRegister">
            <fieldset class="wrapper">
                <legend>Edit Teams</legend>

                <input type="hidden" id="hidTeamId" name="hidTeamId"
                       value="<?php print $pmkTeamId; ?>"
                       >

                <label for="txtWin" class="required">Total Wins
                    <input type="text" id="txtFirstName" name="txtWin"
                           value="<?php print $wins; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter total wins"
    <?php if ($winERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           autofocus>
                </label>

                <label for="txtTies" class="required">Total Draws
                    <input type="text" id="txtTies" name="txtTies"
                           value="<?php print $ties; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter total ties"
    <?php if ($tiesERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           autofocus>
                </label>
                
                <label for="txtLoss" class="required">Total Losses
                    <input type="text" id="txtLoss" name="txtLoss"
                           value="<?php print $loss; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter total losses"
    <?php if ($lossERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           autofocus>
                </label>
                               
            </fieldset> <!-- ends contact -->
            </fieldset> <!-- ends wrapper Two -->
            <fieldset class="buttons">
                <legend></legend>
                <input type="submit" id="btnSubmit" name="btnSubmit" value="Save" tabindex="900" class="button">
            </fieldset> <!-- ends buttons -->
            </fieldset> <!-- Ends Wrapper -->
        </form>
        <?php
    } // end body submit
    ?>
</article>

<?php
include "footer.php";
if ($debug)
    print "<p>END OF PROCESSING</p>";
?>

</body>
</html>