<?php
require_once("/usr/local/lib64/webtools/config/config.php");
require_once("./myfunctions.php");
?>

<?php

global $USER, $RUNAS_CMD, $RESTORE, $RESTOREMAIL, $RESTORE_TYPE, $SCRIPT_LOG_FACILITY;

// try and get some passed variables 
$searchterm = NULL;
$snapshot_path_arr = NULL;
if (isset($_POST['searchterm']) && $_POST['searchterm'] != "")
    $searchterm = $_POST['searchterm'];
else {
    echo "<br /><br />";
    echo "You forgot to enter a searchterm.  Try again.";
    exit;
}

if (isset($_POST['spath']) && $_POST['spath'] != "")
    $spath = $_POST['spath'];
else {
    echo "<br /><br />";
    echo "Could not find where to search for your data.";
    exit;
}

// search for term in ALL directories and files in $snapshot_path_arr
if (isset($spath)) {
    $snapshot_path_arr = unserialize(base64_decode($spath));
    $allpaths = implode(' ', $snapshot_path_arr);
    if ($RESTORE_TYPE == 'mail')
        $user_cmd = "$RESTOREMAIL -s '$searchterm' -f $SCRIPT_LOG_FACILITY -p '$allpaths'";
    else
        $user_cmd = "$RESTORE -s '$searchterm' -f $SCRIPT_LOG_FACILITY -p '$allpaths'";
    $exec_target = "$RUNAS_CMD $USER $user_cmd";
    unset($result);
    exec($exec_target, $result, $status);

    // $line looks like either...
    // d!2009-12-22 17:18:17.782555000 -0500!/rci/u4/.snapshot/nightly.0/brylon/Maildir/.some-sent-mail!(type=folder)
    // d!2010-12-13 15:04:15.113204000 -0500!/rci/u4/.snapshot/nightly.0/brylon/public_html/usr/local/lib64/webtools/
    // o!2006-09-06 11:11:32.538213000 -0400!/rci/u4/.snapshot/nightly.0/brylon/webtools/webtools.sh!(size=3088)
    $checkboxes = NULL;
    foreach ($result as $_ => $line) {
        // skip the 'Permission denied' lines IFF they occurred
        if (strpos($line, 'Permission denied') !== false)
            continue;

        // skip paths containing keyword 'RESTORE'
        if (strpos($line, 'RESTORE') !== false)
            continue;

        // build arrays of both directories and files
        $tmp_arr = explode('!', $line);
        $filetype = $tmp_arr[0];
        $mtime = $tmp_arr[1];
        $path = $tmp_arr[2];
        $misc = $tmp_arr[3];
        if ($filetype == 'd') {
            // add default of INBOX folder
            if ($RESTORE_TYPE == 'mail' && strcasecmp($searchterm, 'inbox') == 0)
                $path = $path . '/.';

            $checkboxes .= "<input type='checkbox' id='$path' class='checkboxWebtools' name='path_arr[]' value='$path'><label class='directory' for='$path'>$mtime $path $misc</label><br />";
        } else {
            $checkboxes .= "<input type='checkbox' id='$path' class='checkboxWebtools' name='path_arr[]' value='$path'><label class='file' for='$path'>$mtime $path $misc</label><br />";
        }
    }

    // show HTML
    if (isset($checkboxes)) {
        echo "<br /><br />";
        echo "Check the name of any data type you wish to restore and click the Submit button below.";
        echo "<br /><br />";
        echo "Note: Directories/Folders are listed in bold.";
        echo "<br /><br />";
        echo $checkboxes;
        echo "<p>";
        echo "<input type='button' class='buttonWebtools' name='submitbutton' value='Submit' onclick='submitForm()' />";
    } else {
        echo "<br /><br />";
        echo "No data found with name '$searchterm'.  Try again.";
    }
}

?>
