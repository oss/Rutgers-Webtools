<?php
require_once("/usr/local/lib64/webtools/config/config.php");
?>

<?php

global $USER, $RUNAS_CMD, $FIXPERMS;

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
    // determine the action to take
    switch ($action){
            case 'fix':
                $usercmd = "$FIXPERMS -X";
                $exec_target = "$RUNAS_CMD $USER $usercmd";
                exec($exec_target, $result, $status);
                if(isset($status) && $status == 0){

                    // show results
                    print <<<HTML
                    <p></p>
                    <span class='success'>Permissions fixed.</span>
HTML;
                    exit;
                }
                break;
            case 'list':
                $usercmd = "$FIXPERMS";
                $exec_target = "$RUNAS_CMD $USER $usercmd";
                exec($exec_target, $result, $status);
                if(isset($status) && $status == 0){
                    if(isset($result) && is_array($result))
                        $htmlout = "Count " . count($result) . ":<br />" . implode('<br />', $result);
                    else
                        $htmlout = "<span class='success'>No permissions to fix.</span>";
               
                    // show results
                    print <<<HTML
                    <p></p>
                    $htmlout
HTML;
                    exit;
                }
                break;
    }
}

// error out IFF we've made it this far
print <<<HTML
<p></p>
<span class='error'>Error: Please report this to your local Campus Computing Help Desk.</span>
HTML;

?>
