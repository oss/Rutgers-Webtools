<?php
require_once("/usr/local/lib64/webtools/config/config.php");
?>

<?php

// Validate the forwarding email address
function isvalidAddress($username, $faddress) {
    require_once("Validate.php");

    global $USER, $RUNAS_CMD, $VMAIL;

    if (! Validate::email($faddress, array('check_domain' => 'true'))) {
        echo "<span name='error' class='error'>Invalid forwarding address</span><br />";
        return false;
    }

    // does this address already exist?
    $usercmd = "$VMAIL -s -a $faddress $username";
    $exec_target = "$RUNAS_CMD $USER $usercmd";
    exec($exec_target, $lines, $status);
    if (isset($status) && $status == 0) {
        echo "<span name='error' class='error'>Invalid forwarding address (already exists)</span><br />";
        return false;
    }

    return true;
}

// Validate the username
function isvalidUsername($username) {
    global $USER, $RUNAS_CMD, $VMAIL, $VMAIL_USERNAMES_DISALLOW_ARRAY;

    // gather any vmail usernames already defined
    $oldvusername_arr = NULL;
    $usercmd = "$VMAIL -l";
    $exec_target = "$RUNAS_CMD $USER $usercmd";
    exec($exec_target, $lines, $status);
    if (isset($status) && $status == 0) {
        foreach ($lines as $_ => $line) {
            $tmp_arr = explode(':', $line);

            // retrieve old vmail username
            $oldvusername = str_replace('.qmail-', '', $tmp_arr[0]);
            $oldvusername_arr[] = $oldvusername;
        }
    }

    // test the username against various rules
    if (!ctype_alnum(str_replace(array('-', '_', '.'), '', $username))) {
        echo "<span name='error' class='error'>Invalid username (alphanumeric, dash(-), underscore(_), and dot(.) allowed)</span><br />";
        return false;
    } else if (in_array($username, $VMAIL_USERNAMES_DISALLOW_ARRAY)) {
        echo "<span name='error' class='error'>Invalid username ('$username' not allowed)</span><br />";
        return false;
    } else if (in_array($username, $oldvusername_arr)) {
        echo "<span name='error' class='error'>Invalid username ('$username' already in use)</span><br />";
        return false;
    }
    return true;
}

?>
