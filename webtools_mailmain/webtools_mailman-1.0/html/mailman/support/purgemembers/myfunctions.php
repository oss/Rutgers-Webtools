<?php
/**
 * Process the form data (i.e. Run the mailman binary change_pw for this users list)
 *
 * @param array     $val_arr    Associative array of form fields
 */
function process_data($val_arr)
{
    global $USER, $RUNAS_CMD, $RUNAS_USER, $REMOVE_MEMBERS;

    $args = NULL;
    if (array_key_exists('nouserack', $val_arr)) {
        $args .= '-n ';
    } if (array_key_exists('noadminack', $val_arr)) {
        $args .= '-N ';
    }


    /* Remove the members for the list the user has chosen */
    $usercmd = "$REMOVE_MEMBERS -a $args {$val_arr['listname']}";
    $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
    exec($exec_target, $result, $status);
    if ($status == 0) {
        $http_referer = $_SERVER['HTTP_REFERER'];
        print <<<HTML
        Members for list {$val_arr['listname']} have been purged.
        <p>
        You may <a href="$http_referer">go back and purge more members</a> or visit <a href="/mailman/">RU Mailman</a>.
HTML;
    } else {
        print <<<HTML
        Failed purging members of list {$val_arr['listname']}, please contact 445-HELP.
        <p>
HTML;
    }

    /* Log the time of request, NetID, and listname */
    $ident = substr(dirname($_SERVER['PHP_SELF']), 1);
    $logger = &Log::factory('syslog', LOG_SYSLOG, $ident);
    $logger->log('netid=' . $USER . ', listname=' . $val_arr['listname'] . ', action=remove_members');

    return true;
}

?>
