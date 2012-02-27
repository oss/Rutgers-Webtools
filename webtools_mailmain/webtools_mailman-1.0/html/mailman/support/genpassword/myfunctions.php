<?php
/**
 * Process the form data (i.e. Run the mailman binary change_pw for this users list)
 *
 * @param array     $val_arr    Associative array of form fields
 */
function process_data($val_arr)
{
    global $USER, $RUNAS_CMD, $RUNAS_USER, $CHANGE_PW;

    /* Change the password for the list the user has chosen */
    $usercmd = "$CHANGE_PW -l {$val_arr['listname']}";
    $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
    exec($exec_target, $result, $status);
    if ($status == 0) {
        $http_referer = $_SERVER['HTTP_REFERER'];
        print <<<HTML
        A new password has been emailed to the owners of list {$val_arr['listname']}.
        <p>
        You may <a href="$http_referer">go back and change another password</a> or visit <a href="/mailman/">RU Mailman</a>.
HTML;
    } else {
        print <<<HTML
        Failed sending a new password to the owners of list {$val_arr['listname']}, please contact 445-HELP.
        <p>
HTML;
    }

    /* Log the time of request, NetID, and listname */
    $ident = substr(dirname($_SERVER['PHP_SELF']), 1);
    $logger = &Log::factory('syslog', LOG_SYSLOG, $ident);
    $logger->log('netid=' . $USER . ', listname=' . $val_arr['listname'] . ', action=change_pw');

    return true;
}

?>
