<?php
require_once("/usr/local/lib64/webtools/config/config.php");
?>

<?php

global $USER, $RUNAS_USER, $RUNAS_CMD, $STF, $TOUCH, $SMB_DIR;

// try and get some passed variables 
$action = NULL;
if (isset($_POST['action']) && $_POST['action'] != "")
    $action = $_POST['action'];
else {
    echo "<br /><br />";
    echo "No action specified.  Try again.";
    exit;
}

if (isset($action)) {
    // so we show off our loader ;)
    sleep(2);

    // double check user action file not written already (should never happen in theory)
    $usercmd = "$STF -A $SMB_DIR/$USER-$action";
    $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
    $status = NULL;
    exec($exec_target, $result, $status);
    // let the user know their misfortune of this (mini) black swan event
    if (isset($status) && $status == 0) {
        print <<<HTML
        <p></p>
        <span class='error'>Error: Please report this to your local Campus Computing Help Desk.</span>
HTML;
        exit;
    }

    // write file as $USER-$action (eg, brylon-add)
    $usercmd = "$TOUCH $SMB_DIR/$USER-$action";
    $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
    $status = NULL;
    exec($exec_target, $result, $status);
    if(isset($status) && $status == 0){
        // uppercase $CLUSTER
        $cluster = strtoupper($CLUSTER);

        // html out results
        switch ($action){
                case 'add':
                    print <<<HTML
                    <p></p>
                    <span class='success'>A new password has been generated for your $cluster drive account and a confirmation email has been sent containing that password.</span>
HTML;
                    break;
                case 'disable':
                    print <<<HTML
                    <p></p>
                    <span class='success'>Your $cluster drive account has been disabled and a confirmation email has been sent.</span>
HTML;
                    break;
                case 'enable':
                    print <<<HTML
                    <p></p>
                    <span class='success'>Your $cluster drive account has been enabled and a confirmation email has been sent.</span>
HTML;
                    break;
        }
    }
}

?>
