<?php

/**
 * Process the form data (i.e. Run the mailman binary config_list for this list)
 *
 * @param array     $val_arr    Associative array of form fields
 */
function process_data($val_arr)
{
    global $USER, $RUNAS_CMD, $RUNAS_USER, $CONFIG_LIST;

    /* Retrieve this lists configuration */
    $usercmd = "$CONFIG_LIST -o - " . $val_arr['listname'];
    $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
    exec($exec_target, $result, $status);
    if ($status == 0) {
        $http_referer = $_SERVER['HTTP_REFERER'];
        $tmp = implode("\n", $result);
        $tmp = htmlspecialchars($tmp);
        $output = nl2br($tmp);
        print <<<HTML
        <p>
        $output
        <p>
        You may <a href="$http_referer">go back and lookup another list cfg</a> or visit <a href="/mailman/">RU Mailman</a>.
HTML;
    } else {
        print <<<HTML
        Failed showing the list configuration for {$val_arr['listname']}, please contact 445-HELP.
        <p>
HTML;
    }

    /* Log the time of request, NetID, and listname */
    $ident = substr(dirname($_SERVER['PHP_SELF']), 1);
    $logger = &Log::factory('syslog', LOG_SYSLOG, $ident);
    $logger->log('netid=' . $USER . ', listname=' . $val_arr['listname'] . ', action=config_list, status=' . $status);

    return true;
}

?>
