<?php
require_once("/usr/local/lib64/webtools/config/config.php");
require_once("./myfunctions.php");
?>

<?php
global $USER, $RUNAS_USER, $RUNAS_CMD, $DB, $PENDING_DIR, $DB_EMAIL_ADDRESS_FROM, $DB_EMAIL_ADDRESS_TO;

// try and get some passed variables 
$action = NULL;
if (isset($_POST['action']) && $_POST['action'] != "") {
    // determine the action to take
    $action = $_POST['action'];
    switch ($action){
        case 'pending':
            if (isset($_POST['dbname']) && isset($_POST['privs'])) {
                $dbname = $_POST['dbname'];
                $realdbname= $USER . '_' . $dbname;
                $privs_arr = unserialize($_POST['privs']);
                $privs = implode(',', $privs_arr);
                $current_dbnames_arr = unserialize($_POST['current_dbnames']);

                // validate the dbname
                // test the database name against various rules
                if (!ctype_alnum(str_replace(array('-'), '', $dbname))) {
                    echo "<span name='error' class='error'>Invalid Database Name (alphanumeric and dash(-) allowed)</span><br />";
                    exit;
                }
                if (in_array($realdbname, $current_dbnames_arr)) {
                    echo "<span name='error' class='error'>Invalid Database Name ('$realdbname' already in use by you)</span><br />";
                    exit;
                }

                // extra steps todo if user has NO previous pending or active databases
                $passline = NULL;
                $sourceline = NULL;
                if (count($current_dbnames_arr) == 0) {
                    // generate new mysql password for user
                    $pw = genpassword();

                    // set the passline
                    $passline = "SET @PASS='$pw';";

                    // set an additional sourceline
                    $sourceline = "#source $PENDING_DIR/batch/create-user.sql";
                }
                
                // create the sql file for the user
                $content =<<<EOF
SET @REALU='$USER';
SET @DB='$dbname';
SET @PRIVS='$privs';
SET @PENDU='rcimysql_pending';
$passline

$sourceline
#source $PENDING_DIR/batch/create-database.sql
#source $PENDING_DIR/batch/grant-privs-to-pending-user.sql
#source $PENDING_DIR/batch/grant-privs-to-user.sql
EOF;

                // write the users sql file (eg, NetID_dbname.sql)
                $usercmd = "$MF -A $PENDING_DIR/$realdbname.sql";
                $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
                $fp = popen($exec_target, 'w');
                fputs($fp, $content."\n");
                pclose($fp);

                // create pending database
                $usercmd = "$DB -c $dbname $USER";
                $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
                exec($exec_target, $lines, $status);
                if (isset($status) && $status == 0) {
                    // send mail about the new db request
                    include_once('Mail.php');
                    $headers['From'] = $DB_EMAIL_ADDRESS_FROM;
                    $headers['Subject'] = "RCI New DB Request ($realdbname)";
                    $recipients['To'] = $DB_EMAIL_ADDRESS_TO;
                    $emailbody = "RCI New DB Request ($realdbname)\n\n";
                    $emailbody .= "Assignee: RCI Root\n";
                    $mail_object = &Mail::factory('mail');
                    $result = $mail_object->send($recipients, $headers, $emailbody);
                    if (! PEAR::isError($result)) {
                        $msg = "New Database request ($realdbname) has been sent to the RCI administrators.  You will receive email when the request has been processed.";
                        echo "<span name='success' class='success'>$msg</span><br />";
                        exit;
                    }
                }
                break;
            }
        case 'delete':
            if (isset($_POST['dbname'])) {
                $dbname = $_POST['dbname'];

                // delete the db entry
                exit;
            }
        case 'showpw':
            // show users mysql password
            $usercmd = "$DB -s $USER";
            $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
            exec($exec_target, $lines, $status);
            if (isset($status) && $status == 0) {
                echo "<span name='success' class='success'>$lines[0]</span>";
                exit;
            }
            break;
        case 'resetpw':
            // send mail about resetting users mysql password
            include_once('Mail.php');
            $headers['From'] = $DB_EMAIL_ADDRESS_FROM;
            $headers['Subject'] = "RCI DB Reset Password Request ($USER)";
            $recipients['To'] = $DB_EMAIL_ADDRESS_TO;
            $emailbody = "RCI DB Reset Password Request ($USER)\n\n";
            $emailbody .= "Assignee: RCI Root\n";
            $mail_object = &Mail::factory('mail');
            $result = $mail_object->send($recipients, $headers, $emailbody);
            if (! PEAR::isError($result)) {
                $msg = 'Reset Password request has been sent to the RCI administrators.  You will receive email when the request has been processed.';
                echo "<span name='success' class='success'>$msg</span>";
                exit;
            }
            break;
    }
}

// error out IFF we've made it this far
echo "<span name='error' class='error'>Error: Please report this to your local Campus Computing Help Desk.</span><br />";

?>
