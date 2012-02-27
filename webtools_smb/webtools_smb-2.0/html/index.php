<?php
require_once('/usr/local/lib64/webtools/config/config.php');
head();
right_boxes('news');
content();
?>

<?php

global $USER, $RUNAS_CMD, $STF, $CLUSTER;

// setup greeting
$name = getFullName($USER);
if (isset($name))
    $greeting = "Hello, $name.";
else
    $greeting = "Hello, $USER.";

// uppercase $CLUSTER
$cluster = strtoupper($CLUSTER);

/* Has user setup samba before? */

// well, does ~/$CLUSTER_drive exist?
$usercmd = "$STF {$cluster}_drive";
$exec_target = "$RUNAS_CMD $USER $usercmd";
exec($exec_target, $result, $status);

if (isset($status) && $status == 0) {
    // Yes
    $form = <<<HTML
    <fieldset class='webtools'>
    <legend class='webtools'>Generate New Password:</legend>
    If the Samba password is lost or forgotten generate a new one.
    The new password will be sent by email to the account owner.
    <p></p>
    <input type='button' id='addbutton' class='buttonWebtools' value='Generate Password' onclick="showResult('add', 'showadd', 'loader_for_add');" />
    <div id='loader_for_add' style='display:none'></div>
    <div id='showadd'></div>
    </fieldset>

    <fieldset class='webtools'>
    <legend class='webtools'>Disable Access:</legend>
    To temporarily disable Samba account.
    <p></p>
    <input type='button' id='disablebutton' class='buttonWebtools' value='Disable Access' onclick="showResult('disable', 'showdisable', 'loader_for_disable');" />
    <div id='loader_for_disable' style='display:none'></div>
    <span id='showdisable'></span>
    </fieldset>

    <fieldset class='webtools'>
    <legend class='webtools'>Enable Access:</legend>
    To enable a previously disabled Samba account.
    <p></p>
    <input type='button' id='enablebutton' class='buttonWebtools' value='Enable Access' onclick="showResult('enable', 'showenable', 'loader_for_enable');" />
    <div id='loader_for_enable' style='display:none'></div>
    <span id='showenable'></span>
    </fieldset>
HTML;
} else {
    // No
    $form = <<<HTML
    <fieldset class='webtools'>
    <legend class='webtools'>Setup Samba Access:</legend>
    To use Samba with a Rutgers account, click on the "Setup Samba Access" button.
    Access will be enabled and a Samba password will be automatically generated
    and sent by email to the account owner. The same holds true for departmental
    accounts.
    <p></p>
    <input type='button' id='setupbutton' class='buttonWebtools' value='Setup Samba Access' onclick="showResult('add', 'showsetup', 'loader_for_setup');" />
    <div id='loader_for_setup' style='display:none'></div>
    <span id='showsetup'></span>
    </fieldset>
HTML;
}

?>

<div id='greeting'><?=$greeting?></div>
<p></p>
Remote drive mapping with Samba allows access to portions of Rutgers
accounts as if they were physically connected to a user's local
computer. This allows easy access to files and web pages.
<br />
<br />
<?=$form?>
<br />
<br />
Find out <a href="http://www.nbcs.rutgers.edu/newdocs/samba"> more information about using Samba</a>.

<?php
foot();
?>
