<?php
require_once('/usr/local/lib64/webtools/config/config.php');
require_once('myfunctions.php');
head();
right_boxes('news');
content();
?>

<?php

global $USER, $RUNAS_CMD, $RUNAS_USER, $RETIREE, $RETIREE_DIR, $INVALID_RETIREE_DIR;

$name = getFullName($USER);
if (isset($name))
    $greeting = "Hello, $name.";
else
    $greeting = "Hello, $USER.";

print <<<HTML
<div id='greeting'>$greeting</div>
HTML;

// figure out this year and next
$thisyear=date('Y');
$nextyear=date('Y', strtotime('+1 years'));
global $thisyear, $nextyear;

// has the retiree been here before (are they paranoid?)
if (been_here_before($RETIREE_DIR)) {
    // congratulate the retiree again
    print <<<HTML
    <h2>Congratulations again.</h2>
    You have <b>already</b> successfully renewed your account for the $thisyear-$nextyear academic year.
    There is nothing more you need to do.
HTML;
    foot();
    exit;
}

// has the invalid retiree (facstaff) been here before (are they paranoid?)
if (been_here_before($INVALID_RETIREE_DIR)) {
    // thank the facstaff again
    print <<<HTML
    <h2>Thank you again for logging in.</h2>
    Your NetID has been collected from your previous login this year.
    There is no further action you need take at this time. If you are
    confirmed as a Retiree, your account will be renewed for the next academic
    year.  If Human Resources shows you have not retired under the proper
    conditions, we will contact you again with further instructions.
HTML;
    foot();
    exit;
}

// figure out and process what should be done if we've made it this far
$usercmd = "$RETIREE $RETIREE_DIR $INVALID_RETIREE_DIR $USER {$_SERVER['REMOTE_ADDR']}";
$exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
exec($exec_target, $result, $status);
if (isset($status) && $status == 0) {
    // send confirmation email to the retiree AND
    $sentmail = send_mail();
    $note = NULL;
    if ($sentmail)
        $note = "A confirmation mail has been sent to you.";

    // congratulate the retiree
    print <<<HTML
    <h2>Congratulations.</h2>
    You have successfully renewed your account for the $thisyear-$nextyear academic year.
    $note
    There is nothing more you need to do.
HTML;
} else if (isset($status) && $status == 1) {
    // is facstaff and not a retiree
    print <<<HTML
    <h2>Thank you for logging in.</h2>
    Your NetID has been collected, and your name will be sent to the
    Human Resources office for verification of your Retiree status.
    There is no further action you need take at this time.  If you are confirmed
    as a Retiree, your account will be renewed for the next academic year.  If
    Human Resources shows you have not retired under the proper conditions, we
    will contact you again with further instructions.
HTML;
} else if (isset($status) && $status == 2) {
    // is guest and not a retiree
    print <<<HTML
    <h2>Sorry</h2>
    You are not listed among the retirees approved by the University Human
    Resources office, so you may not use this tool to renew your RCI account.
    Please follow the Guest renewal procedure previously described to you. In
    addition, you may read the "Retiree" section of the
    <a href="http://www.rci.rutgers.edu/renew.html#keep">renewal instructions</a>
    to learn how to apply for approved retiree status for next year.
HTML;
} else if (isset($status) && $status == 3) {
    // is other and not a retiree
    print <<<HTML
    <h2>Sorry.</h2>
    Please write to <a href='mailto:help@rci.rutgers.edu'>help@rci.rutgers.edu</a>
    or call the OIT Help Desk at 732-445-4357 if you have any questions about your account.
HTML;
} else {
    // report error to retiree
    print <<<HTML
    <h2>Error.</h2>
    There was an issue with this webtool, please write to
    <a href='mailto:help@rci.rutgers.edu'>help@rci.rutgers.edu</a>
    or call the OIT Help Desk at 732-445-4357.
HTML;
}
?>

<?php
foot();
?>
