<?php
include "top.php";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
//
// SECTION: 1a.
// variables for the classroom purposes to help find errors.
$update = false;
$debug = false;

if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}

if ($debug)
    print "<p>DEBUG MODE IS ON</p>";

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
if (isset($_GET["id"])) {
    $pmkUserId = (int) htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");

    $query = 'SELECT fldFirstName, fldLastName, fldEmail';
    $query .= 'FROM tblRegister WHERE pmkUserId = ?';

    $results = $thisDatabase->select($query, array($pmkUserId), 1, 0, 0, 0, false, false);

     $firstName = $results[0]["fldFirstName"];
     $lastName = $results[0]["fldLastName"];
     $email = $results[0]["fldEmail"];
  
} else {
    $pmkUserId = -1;
    $firstName = "";
    $lastName = "";
    $email = "";
}


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$firstNameERROR = false;
$lastNameERROR = false;
$emailERROR = false;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();

// array used to hold form values that will be written to a CSV file
$data = array();
$dataEntered = false;
$mailed=false;
// have we mailed the information to the user?
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
//
if (isset($_POST["btnSubmit"])) {

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2a Security
    // 
    if (!securityCheck($path_parts, $yourURL, true)) {
        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported</p>";
        die($msg);
    }
    
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2b Sanitize (clean) data 
    // remove any potential JavaScript or html code from users input on the
    // form. Note it is best to follow the same order as declared in section 1c.

     $pmkUserId = (int) htmlentities($_POST["hidUserId"], ENT_QUOTES, "UTF-8");
    if ($pmkUserId > 0) {
        $update = true;
    }
    
    $firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");
    $data[] = $firstName;

    $lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");
    $data[] = $lastName;
    
    $email = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL);
    $data[] = $email;


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2c Validation
    //
    // Validation section. Check each value for possible errors, empty or
    // not what we expect. You will need an IF block for each element you will
    // check (see above section 1c and 1d). The if blocks should also be in the
    // order that the elements appear on your form so that the error messages
    // will be in the order they appear. errorMsg will be displayed on the form
    // see section 3b. The error flag ($emailERROR) will be used in section 3c.

    if ($firstName == "") {
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
    
    if ($email == "") {
        $errorMsg[] = "Please enter your email address";
        $emailERROR = true;
    } elseif (!verifyEmail($email)) {
        $errorMsg[] = "Your email address appears to be incorrect.";
        $emailERROR = true;
    }


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2d Process Form - Passed Validation
    //
    // Process for when the form passes validation (the errorMsg array is empty)
    //
    if (!$errorMsg) {
        if ($debug)
            print "<p>Form is valid</p>";

        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2e Save Data to database

         $dataEntered = false;
        try {
            $thisDatabase->db->beginTransaction();

            if ($update) {
                $query = 'UPDATE tblRegister SET ';
            } else {
                $query = 'INSERT INTO tblRegister SET ';
            }

            
            $query .= 'fldFirstName = ?, ';
            $query .= 'fldLastName = ?, ';
            $query .= 'fldEmail = ?, ';

            if ($update) {
                $query .= 'WHERE pmkUserId = ?';
                $data[] = $pmkUserId;

                if ($_SERVER["REMOTE_USER"] == 'bcorbier') {
                    $results = $thisDatabase->update($query, $data, 1, 0, 0, 0, false, false);
                }
            } else {
                if ($_SERVER["REMOTE_USER"] == 'bcorbier'){
                    $results = $thisDatabase->insert($query, $data);
                    $primaryKey = $thisDatabase->lastInsert();
                    if ($debug) {
                        print "<p>pmk= " . $primaryKey;
                    }
                }
            }

             // all sql statements are done so lets commit to our changes
            //if($_SERVER["REMOTE_USER"]=='rerickso'){
            $dataEntered = $thisDatabase->db->commit();
            // }else{
            //     $thisDatabase->db->rollback();
            // }
            if ($debug)
                print "<p>transaction complete ";
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            if ($debug)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
        }
    } // end form is valid
} // ends if form was submitted.
        
        
        
        // This block saves the data to a CSV file.

       // $fileExt = ".csv";

        //$myFileName = "data/registration";

        //$filename = $myFileName . $fileExt;

        //if ($debug)
          //  print "\n\n<p>filename is " . $filename;

        // now we just open the file for append
        //$file = fopen($filename, 'a');

        // write the forms informations
        //fputcsv($file, $dataRecord);

        // close the file
        //fclose($file);

        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2f Create message
        //
        // build a message to display on the screen in section 3a and to mail
        // to the person filling out the form (section 2g).

 
          //  
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
        // SECTION: 2g Mail to user
        //
        // Process for mailing a message which contains the forms data
        // the message was built in section 2f.
        $to = $email; // the person who filled out the form
        $cc = "";
        $bcc = "";
        $from = "WRONG site <noreply@yoursite.com>";

        // subject of mail should make sense to your form
        $todaysDate = strftime("%x");
        $subject = "Research Study: " . $todaysDate;

        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);
        
     // end form is valid
    
 // ends if form was submitted.

//#############################################################################
//
// SECTION 3 Display Form
//
?>

<article id="main">

    <?php
    echo '<div class="container">';
    //####################################
    //
    // SECTION 3a.
    //
    // 
    // 
    // 
    // If its the first time coming to the form or there are errors we are going
    // to display the form.
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
        print "<h1>Your Request has ";

        if (!$mailed) {
            print "not ";
        }

        print "been processed</h1>";

        print "<p>A copy of this message has ";
        if (!$mailed) {
            print "not ";
        }
        print "been sent</p>";
        print "<p>To: " . $email . "</p>";
        print "<p>Mail Message:</p>";
        print $message;
    } else {


        //####################################
        //
        // SECTION 3b Error Messages
        //
        // display any error messages before we print out the form

        if ($errorMsg) {
            print '<div id="errors">';
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
                <legend>Sign Up Today</legend>
                <p>Stay up-to-date on current BPL news.</p>

                <fieldset class="wrapperTwo">
                    <legend>Please complete the following form</legend>

                    <input type="hidden" id="hidUserId" name="hidUserId"
                       value="<?php print $pmkUserId; ?>"
                       >
                    
                    <fieldset class="contact">
                        <legend>Contact Information</legend>
                        <label for="txtFirstName" class="required">First Name
                            <input type="text" id="txtFirstName" name="txtFirstName"
                                   value="<?php print $firstName; ?>"
                                   tabindex="100" maxlength="45" placeholder="Enter your first name"
                                   <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   autofocus>
                        </label>
                        
                        <label for="txtLastName" class="required">Last Name
                            <input type="text" id="txtLastName" name="txtLastName"
                                   value="<?php print $lastName; ?>"
                                   tabindex="100" maxlength="45" placeholder="Enter your last name"
                                   <?php if ($lastNameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   autofocus>
                        </label>
                        
                        <label for="txtEmail" class="required">Email
                            <input type="text" id="txtEmail" name="txtEmail"
                                   value="<?php print $email; ?>"
                                   tabindex="120" maxlength="45" placeholder="Enter a valid email address"
                                   <?php if ($emailERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()" 
                                   >
                        </label>
                        
                        <fieldset  class="listbox">	
                        <label for="lstteam">Favorite Team</label>
                            <select id="lstteam" 
                                    name="lstteam" 
                                    tabindex="300" >
                                <option value="Arsenal" selected >Arsenal</option>
        
                             <option value="Aston Villa">Aston Villa</option>
        
                             <option value="Bournemouth">Bournemouth</option>
                             
                             <option value="Chelsea">Chelsea</option>
        
                             <option value="Crystal Palace">Crystal Palace</option>
        
                             <option value="Everton">Everton</option>
                             
                             <option value="Leicester City">Leicester City</option>
        
                             <option value="Liverpool">Liverpool</option>
        
                             <option value="Manchester City">Manchester City</option>
                             
                             <option value="Manchester United">Manchester United</option>
        
                             <option value="Newcastle United">Newcastle United</option>
        
                             <option value="Norwich City">Norwich City</option>
                             
                             <option value="Southampton">Southampton</option>
        
                             <option value="Stoke City">Stoke City</option>
        
                             <option value="Sunderland">Sunderland</option>
                             
                             <option value="Swansea City">Swansea City</option>
        
                             <option value="Tottenham Hotspur">Tottenham Hotspur</option>
        
                             <option value="Watford">Watford</option>
                             
                             <option value="West Bromwich Albion">West Bromwich Albion</option>
                            
                             <option value="West Ham United">West Ham United</option>
                             
                            </select>
                            </fieldset>
                        
                            <fieldset class="checkbox">
                                <legend>Select the following (check all that apply):</legend>
                                <label for="chkupdates"><input type="checkbox" 
                                       id="chkupdates" 
                                       name="chkupdates" 
                                       value="2">Do you want to receive matchday updates?
                               </label>
                               <label for="chknews"><input type="checkbox" 
                                                id="chknews" 
                                                name="chknews" 
                                                value="3">Do you want to receive BPL news?
                               </label>
                               <label for="chkteamnews"><input type="checkbox" 
                                                id="chkteamnews" 
                                                name="chteamknews" 
                                                value="4">Do you want to receive your favorite team's news?
                               </label>
                            </fieldset>
                        
                            <fieldset class="radio">
                                 <legend>How many points is a win?:</legend>

                                 <label for="radanswer1">
                                      <input type="radio" 
                                             id="radanswer1" 
                                             name="radpoints" 
                                             value="2">0
                               </label>

                               <label for="radanswer2">
                                      <input type="radio" 
                                               id="radanswer2" 
                                             name="radpoints" 
                                            value="3">1
                               </label>

                               <label for="radanswer3">
                                      <input type="radio" 
                                               id="radanswer3" 
                                             name="radpoints" 
                                            value="3">3
                               </label>  
                                 
                            </fieldset>
                        
                    </fieldset> <!-- ends contact -->
                    
                </fieldset> <!-- ends wrapper Two -->
                
                <fieldset class="buttons">
                    <legend></legend>
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Submit" tabindex="900" class="button">
                </fieldset> <!-- ends buttons -->
                
            </fieldset> <!-- Ends Wrapper -->
        </form>

    <?php
    } // end body submit
    ?>

</article>

<?php include "footer.php"; ?>
</article>
</body>
</html>
