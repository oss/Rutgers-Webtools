<?php

/**
 * Process the form data (i.e. Run the mailman binary list_members for this users list)
 *
 * @param array     $val_arr    Associative array of form fields
 */
function process_data($val_arr)
{
    global $USER, $RUNAS_CMD, $RUNAS_USER, $LIST_MEMBERS;

    /* Retrieve this lists configuration */
    $usercmd = "$LIST_MEMBERS -f " . $val_arr['listname'];
    $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
    exec($exec_target, $result, $status);
    if ($status == 0) {
        $http_referer = $_SERVER['HTTP_REFERER'];
        $count = count($result);
        $tmp = implode("\n", $result);
        $tmp = htmlspecialchars($tmp);
        $output = nl2br($tmp);
        /* Allows exporting of list members to CSV file */
        $form2 = new HTML_QuickForm('myform2', 'post', 'export.php', null, array('enctype' => 'multipart/form-data'));
        $form2->addElement('hidden', 'listname', "$val_arr[listname]");
        $form2->addElement('static', 'fyi', "Export this lists members:");
        $form2->addElement('submit', 'export', 'Export to CSV File');
        $form2->display();
        print <<<HTML
        There are $count results:
        <p>
        $output
        <p>
        You may <a href="$http_referer">go back and lookup another lists members </a> or visit <a href="/mailman/">RU Mailman</a>.
HTML;
    } else {
        print <<<HTML
        Failed showing the lists members for {$val_arr['listname']}, please contact 445-HELP.
        <p>
HTML;
    }

    /* Include the survey */
    $webtool = basename(dirname($_SERVER['SCRIPT_NAME']));
    print <<<HTML
    <p>
    <br/>
    Please help us improve our Mailman webtools by taking our <a href="/mailman/survey/webtools/index.php?webtool=$webtool">Mailman webtool survey</a>.
HTML;

    /* Log the time of request, NetID, and listname */
    $ident = substr(dirname($_SERVER['PHP_SELF']), 1);
    $logger = &Log::factory('syslog', LOG_SYSLOG, $ident);
    $logger->log('netid=' . $USER . ', listname=' . $val_arr['listname'] . ', action=list_members, status=' . $status);

    return true;
}

?>
