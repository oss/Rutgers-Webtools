<?php

function been_here_before($dir=NULL) {
    global $USER, $RUNAS_USER, $RUNAS_CMD, $STF, $RETIREE_DIR;

    if (empty($dir))
        $dir = $RETIREE_DIR;

    // stat the file (ie, does it exist already?)
    $usercmd = "$STF -A $dir/$USER";
    $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
    exec($exec_target, $result, $status);
   
    // yes
    if ($status == 0)
        return true;

    // no
    return false;
}

function send_mail() {
    global $USER, $thisyear, $nextyear;

    include_once('Mail.php');

    // Email confirmation to user
    $servername = gethostbyaddr($_SERVER['SERVER_ADDR']);
    $currdate = date('m/d/Y, h:i:s A', time());
    $headers['From'] = 'help@rci.rutgers.edu';
    $headers['Subject'] = 'RCI Retiree Account Maintenance Summary';
    $fullname = getFullname($USER);
    $emailbody = "You have successfully renewed your account for the $thisyear-$nextyear academic year.\n";
    $emailbody .= "There is nothing more you need to do.\n";

    $recipients['To'] = "$USER@rci.rutgers.edu";
    $mail_object = &Mail::factory('mail');
    $result = $mail_object->send($recipients, $headers, $emailbody);
    if (PEAR::isError($result))
        return false;

    return true;
}

?>
