<?php

/**
 * List administrators current action according to our policy
 *
 * @param string     $listname    listname this user owns OR is part owner
 */
function current_action($listname)
{
    global $USER, $RUNAS_CMD, $RUNAS_USER, $AUDIT_DIR;

    /* Read the file */
    $lines = file("$AUDIT_DIR/$listname", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
   
    /* 
     * The Policy:
     * https://email.rutgers.edu/mailman/policy/index.php#webtool
     *
     * ---If there is one owner follow the most recent action chosen
     * ---If more than one owner save will be used over delete
     *
     * Example entry:
     * [04-Oct-2007-13:21:59],brylon,Save
     */

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


/**
 * Process the form data (i.e. write a file as listname with contents Date,NetID,Save/Delete)
 *
 * @param array     $val_arr    Associative array of form fields
 */
function process_data($val_arr)
{
    global $USER, $RUNAS_CMD, $RUNAS_USER, $MF, $AF, $AUDIT_DIR;
    $htmloutput = null;
    $emailbody = null;
    $http_referer = $_SERVER['HTTP_REFERER'];

    /* Setup mail envelope */
    $servername = gethostbyaddr($_SERVER['SERVER_ADDR']);
    $currdate = date('m/d/Y, h:i:s A', time());
    $headers['From'] = 'help@email.rutgers.edu';
    $body  = "Submitted by $USER\n";
    $body .= "Submitted on $currdate\n\n";
    $body .= "Submitted values:\n\n";
    $body .= " Listname\t\tAction\n";
    $body .= " --------\t\t------\n\n";

    /* Log the entry to syslog */
    $ident = substr(dirname($_SERVER['PHP_SELF']), 1);
    $logger = &Log::factory('syslog', LOG_SYSLOG, $ident);

    /* Create/Append a file as listname */
    foreach($val_arr['listnames'] as $_ => $listname){

        /* Contents to be written */
        $ymdu = date("[d-M-Y-H:i:s]");
        $output = $ymdu . ',' . $USER . ',' . $val_arr[$listname] . "\n";

        /* Does a file with this listname already exist? */
        if(in_array($listname, $val_arr['readable'])) {
            /* Append the file */
            $usercmd = "$AF -A $AUDIT_DIR/$listname";
            $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
            _unipipeExec($exec_target, $output);
        } else {
            /* Create the file */
            $usercmd = "$MF -A $AUDIT_DIR/$listname";
            $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
            _unipipeExec($exec_target, $output);
        }

        /* Log the time of request, NetID, listname, input, and action */
        $logger->log('netid=' . $USER . ', listname=' . $listname . ', radio=' . $val_arr[$listname] . ', action=auditlists');

        $htmloutput .= $listname . ' ' . $val_arr[$listname] . "<br>";
        $emailbody .= " $listname\t\t$val_arr[$listname]\n";

        /* Send mail to all the owners of $listname */
        $headers['Subject'] = "[$listname] Mailman audit submission results";
        $tmpbody = $body;
        $tmpbody .= " $listname\t\t$val_arr[$listname]\n";
        $tmpbody .= "\nTo change or view the current action for lists you administer, go to:\n";
        $tmpbody .= "$http_referer\n";
        $recipients['To'] = "$listname-owner@email.rutgers.edu";
        $mail_object = &Mail::factory('mail');
        $result = $mail_object->send($recipients, $headers, $tmpbody);
    }

    print <<<HTML
    <p>
    Thank you $USER,
    <p>
    You submitted the following actions be taken for the list(s) below:
    <br>
    $htmloutput
    <p>
HTML;

    /* Send mail with a summary of actions taken for all this owners lists */
    $headers['Subject'] = 'Summary: Mailman audit submission results';
    $body .= $emailbody;
    $body .= "\nTo change or view the current action for lists you administer, go to:\n";
    $body .= "$http_referer\n";
    $recipients['To'] = "$USER@rutgers.edu";
    $mail_object = &Mail::factory('mail');
    $result = $mail_object->send($recipients, $headers, $body);

    $email = str_replace(',', ' and', $recipients['To']);
    if (PEAR::isError($result)) {
        print <<<HTML
        <b>NOTE:</b>Failed sending confirmation mail to $email and possibly ALL the list(s) administrator(s), please contact 445-HELP.
        <p>
        You may <a href="$http_referer" class='nav_item_b'>go back and audit your lists again</a> or visit <a href='/mailman/' class='nav_item_b'>RU Mailman</a>.
HTML;
    } else {
        print <<<HTML
        Confirmation mail has been emailed to $email and ALL the list(s) administrator(s).
        <p>
        You may <a href="$http_referer" class='nav_item_b'>go back and audit your lists again</a> or visit <a href='/mailman/' class='nav_item_b'>RU Mailman</a>.
HTML;
    }
    return true;
}

function _unipipeExec($cmd, $args)
{
  $fp = popen($cmd, 'w');
  fputs($fp, $args);
  pclose($fp);
}

?>
