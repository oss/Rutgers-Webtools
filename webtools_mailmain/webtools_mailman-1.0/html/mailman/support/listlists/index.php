<?php
require_once '/usr/lib64/webtools/config/config.php';
require_once 'PEAR.php';
require_once 'Mail.php';
require_once 'Log.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/Rutgers.php';
head("Support - List all lists", "keywords", "description");
?>

<?php

/* List ALL lists */
$usercmd = "$LIST_LISTS -b";
$exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
exec($exec_target, $result, $status);

if ($status == 0) {
    $count = count($result);
    $tmp = implode("\n", $result);
    $output = nl2br($tmp);
    print <<<HTML
    There are $count results:
    <p>
    $output
HTML;
} else {
    print <<<HTML
    Sorry, tried to list All lists but failed!
HTML;
}

/* Log the time of request, NetID, and listname */
$ident = substr(dirname($_SERVER['PHP_SELF']), 1);
$logger = &Log::factory('syslog', LOG_SYSLOG, $ident);
$logger->log('netid=' . $USER . ', action=list_lists, status=' . $status);

?>

<p>
Questions about listing All Mailman lists may be addressed to <a href='mailto:help@email.rutgers.edu'>help@email.rutgers.edu</a>.

<?php
foot(null, null, true);
?>
