<?php

function current_action($dbname) {
    global $USER, $RUNAS_USER, $RUNAS_CMD, $DBAUDIT, $AUDIT_DIR;

    /* Read the file */
    $usercmd = "$DBAUDIT $AUDIT_DIR/$dbname";
    $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
    exec($exec_target, $lines, $status);
   
    /* 
     * Example entry:
     * [04-Oct-2007-13:21:59],brylon,Save
     */

    if ($status == 0) {
        foreach($lines as $_ => $entry) {
            $tmp_arr = explode(',', $entry);
            $netid = $tmp_arr[1];
            $action = $tmp_arr[2];
            $entry_arr[$netid] = $action;
            $entry_arr_reversed[$action] = $netid;
        }
        if(count($entry_arr) > 1) {
            if($key = array_search('Save', $entry_arr)) {
                $info_arr = array($entry_arr[$key] => $key);
            } else {
                $info_arr = array('Delete' => $entry_arr_reversed['Delete']);
            }
        } else {
            $info_arr = array($entry_arr[$netid] => $netid);
        }
        return $info_arr;
    }

    return false;
}

function processForm($val_arr) {
    include_once('Mail.php');

    global $USER, $RUNAS_USER, $RUNAS_CMD, $FIND, $AF, $MF, $AUDIT_DIR;

    $error = false;
    $emailoutput = null;
    $htmloutput = null;

    // remove unnecessary elements
    unset($val_arr['page1']);

    /* Create/Append a file as database name */
    foreach($val_arr as $dbname => $action){

        /* Contents to be written */
        $ymdu = date("[d-M-Y-H:i:s]");
        $output = $ymdu . ',' . $USER . ',' . $val_arr[$dbname] . "\n";

        /* Does the file exist already */
        $usercmd = "$FIND $AUDIT_DIR/$dbname";
        $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
        exec($exec_target, $result, $status);

        /* Are we appending or creating? */
        if ($status == 0) {
            /* Append the file */
            $usercmd = "$AF -A $AUDIT_DIR/$dbname";
            $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
            _unipipeExec($exec_target, $output);
        } else {
            /* Create the file */
            $usercmd = "$MF -A $AUDIT_DIR/$dbname";
            $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
            _unipipeExec($exec_target, $output);
        }

        /* Log the time of request, NetID, dbname, input, and action */
        //$logger->log('netid=' . $USER . ', dbname=' . $dbname . ', radio=' . $action . ', action=auditdbs');

        $emailoutput .= $dbname . ' ' . $action . "\n";
        $htmloutput .= $dbname . ' ' . $action . "<br>";
    }

    // Email confirmation to user
    $servername = gethostbyaddr($_SERVER['SERVER_ADDR']);
    $currdate = date('m/d/Y, h:i:s A', time());
    $headers['From'] = 'help@rci.rutgers.edu';
    $headers['Subject'] = 'RCI Database Audit Summary';
    $fullname = getFullname($USER);
    $emailbody = "Thank you for responding to the RCI Database Audit.\n\n";
    $emailbody .= "You submitted the following actions be taken for the database(s) below:\n\n";
    $emailbody .= "$emailoutput\n";
    $emailbody .= "You may return to the RCI Database Audit webtool:\n\n"; 
    $emailbody .= "    {$_SERVER['HTTP_REFERER']}\n\n";

    //$recipients['To'] = "$USER@rci.rutgers.edu";
    $recipients['To'] = "brylon@jla.rutgers.edu";
    $mail_object = &Mail::factory('mail');
    $result = $mail_object->send($recipients, $headers, $emailbody);
    if (PEAR::isError($result)) {
        $error = true;
        echo "Error: Failed sending confirmation mail to {$recipients['To']}, please contact 732-445-HELP.";
    }

    if (!$error) {
        print <<<HTML
        Thank you, $fullname.
        <br/>
        <br/>
        You submitted the following actions be taken for the database(s) below:
        <br/>
        <br/>
        $htmloutput
        <br/>
        Confirmation mail has been sent to {$recipients['To']}.
HTML;
    }

    return;
}

function _unipipeExec($cmd, $args) {
  $fp = popen($cmd, 'w');
  fputs($fp, $args);
  pclose($fp);
}

?>
