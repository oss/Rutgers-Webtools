<?php

function isModeratorOn($val_arr)
{
    if (isset($val_arr['default_member_moderation']) && strcmp($val_arr['default_member_moderation'], 'No') == 0) {
        if (isset($val_arr['generic_nonmember_action']) && strcmp($val_arr['generic_nonmember_action'], 'Hold') == 0) {
            return array('generic_nonmember_action' => 'Hold is not valid when your list is NOT moderated');
        }
    }
    return true;
}

/**
 * Process the form data (i.e. Email the criteria of the new list)
 *
 * @param array     $val_arr    Associative array of form fields
 */
function process_data($val_arr)
{
    global $USER, $MM_SUPPORT_EMAIL_ADDRESS;

    $http_referer = $_SERVER['HTTP_REFERER'];

    /* Generate a random password (note: omitted letter 'l' and number 1) */
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz023456789";
    $password = null;
    for ($i=0; $i <= 7; $i++) {
        $n = rand(0, 59);
        $password .= substr($chars, $n, 1);
    }

    /* Setup mail envelope */
    $servername = gethostbyaddr($_SERVER['SERVER_ADDR']);
    $currdate = date('m/d/Y, h:i:s A', time());
    $headers['From'] = 'mailman@email.rutgers.edu';
    $headers['Subject'] = 'Mailman request: ' . $val_arr['listname'];
    $body = "The following list creation information was submitted on:\n";
    $body .= "$currdate\n\n";
    $body .= "Create a Mailman Mailing List\n\n";
    $body .= "#Name of list:\n{$val_arr['listname']}\n";
    $body .= "#Initial list owner address:\n{$val_arr['initialowner']}\n";
    $body .= "#Initial list password:\n$password\n\n";
    $body .= "Privacy options...\n";
    $body .= "-Subscription rules:\n";
    $body .= "#Advertise this list when people ask what lists are on this machine:\n{$val_arr['advertised']}\n";
    $body .= "#What steps are required for subscription:\n{$val_arr['subscribe_policy']}\n";
    $body .= "#Who can view subscription list:\n{$val_arr['private_roster']}\n";
    $body .= "-Sender filters:\n";
    $body .= "#Do you want your list to be moderated:\n{$val_arr['default_member_moderation']}\n";
    $body .= "#Action to take for postings from non-members:\n{$val_arr['generic_nonmember_action']}\n\n";
    $body .= "List requests are generally completed within 2 business days.\n\n";
    $body .= "$http_referer\n";

    $body2 = "The following list creation information was submitted on:\n";
    $body2 .= "$currdate\n\n";
    $body2 .= "Owner address: {$val_arr['initialowner']}\n";
    $body2 .= "Listname: {$val_arr['listname']}\n";
    $body2 .= "What steps are required for subscription: {$val_arr['subscribe_policy']}\n";
    $body2 .= "Who can view subscription list: {$val_arr['private_roster']}\n";
    $body2 .= "Advertise this list when people ask what lists are on this machine: {$val_arr['advertised']}\n";
    $body2 .= "Do you want your list to be moderated: {$val_arr['default_member_moderation']}\n";
    $body2 .= "Action to take for postings from non-members: {$val_arr['generic_nonmember_action']}\n\n";
    $body2 .= "List requests are generally completed within 2 business days.\n\n";
    $body2 .= "$http_referer\n\n";
    $body2 .= "You can check if your Mailman list has been created by visiting:\n";
    $body2 .= "https://email.rutgers.edu/mailman/listinfo/{$val_arr['listname']}\n";

    $recipients['To'] = $MM_SUPPORT_EMAIL_ADDRESS;
    $recipients2['To'] = "$USER@rutgers.edu";
    $email = str_replace(',', ' and', $recipients['To']);

    /* Send mail */
    $mail_object = &Mail::factory('mail');
    $result = $mail_object->send($recipients, $headers, $body);
    $result2 = $mail_object->send($recipients2, $headers, $body2);
    if (PEAR::isError($result) || PEAR::isError($result2)) {
        print <<<HTML
        Failed sending confirmation mail to $email, please contact 445-HELP.
        <p>
HTML;
    } else {
        print <<<HTML
        Confirmation mail has been emailed to $email.
        <p>
        You may <a href="$http_referer">go back and request another list</a> or visit <a href="/mailman/">RU Mailman</a>.
HTML;
    }

    /* Include the survey */
    $webtool = basename(dirname($_SERVER['SCRIPT_NAME']));
    print <<<HTML
    <p>
    <br/>
    Please help us improve our Mailman webtools by taking our <a href="/mailman/survey/webtools/index.php?webtool=$webtool">Mailman webtool survey</a>.
HTML;

    /* Log the time of creation, netID, and listname */
    $ident = substr(dirname($_SERVER['PHP_SELF']), 1);
    $logger = &Log::factory('syslog', LOG_SYSLOG, $ident);
    $logger->log('netid=' . $USER . ', listname=' . $val_arr['listname'] . ', action=create');

    return true;
}

?>
