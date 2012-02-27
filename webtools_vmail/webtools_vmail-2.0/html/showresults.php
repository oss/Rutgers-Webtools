<?php
require_once("/usr/local/lib64/webtools/config/config.php");
require_once("./myfunctions.php");
?>

<?php

global $USER, $RUNAS_CMD, $VMAIL;

// try and get some passed variables 
$action = NULL;
if (isset($_POST['action']) && $_POST['action'] != "") {
    $action = $_POST['action'];

    // determine the action to take
    switch ($action){
        case 'new':
            if (isset($_POST['username']) && isset($_POST['address'])) {
                $username = $_POST['username'];
                $address = $_POST['address'];

                // validate the address
                if (! isvalidUsername($username) || ! isvalidAddress($username, $address))
                    exit;
                
                // create the new vmail user file and add the address
                $usercmd = "$VMAIL -i -a $address $username";
                $exec_target = "$RUNAS_CMD $USER $usercmd";
                exec($exec_target, $lines, $status);
                if (isset($status) && $status == 0)
                    exit;
                break;
            }
        case 'addaddress':
            if (isset($_POST['username']) && isset($_POST['address'])) {
                $username = $_POST['username'];
                $address = $_POST['address'];

                // validate the address
                if (! isvalidAddress($username, $address))
                    exit;
       
                // add the address to vmail user file
                $usercmd = "$VMAIL -i -a $address $username";
                $exec_target = "$RUNAS_CMD $USER $usercmd";
                exec($exec_target, $lines, $status);
                if (isset($status) && $status == 0)
                    exit;
                break;
            }
        case 'deleteuser':
            if (isset($_POST['username'])) {
                $username = $_POST['username'];
       
                // remove the vmail user file
                $usercmd = "$VMAIL -D $username";
                $exec_target = "$RUNAS_CMD $USER $usercmd";
                exec($exec_target, $lines, $status);
                if (isset($status) && $status == 0)
                    exit;
                break;
            }
        case 'deleteaddress':
            if (isset($_POST['username']) && isset($_POST['address'])) {
                $username = $_POST['username'];
                $address = $_POST['address'];
       
                // remove the address from vmail user file
                $usercmd = "$VMAIL -d -a $address $username";
                $exec_target = "$RUNAS_CMD $USER $usercmd";
                exec($exec_target, $lines, $status);
                if (isset($status) && $status == 0)
                    exit;
                break;
            }
    }
}

// error out IFF we've made it this far
echo "<span name='error' class='error'>Error: Please report this to your local Campus Computing Help Desk.</span><br />";

?>
