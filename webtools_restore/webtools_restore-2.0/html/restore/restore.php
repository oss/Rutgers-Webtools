<?php
require_once('/usr/local/lib64/webtools/config/config.php');
require_once('./myfunctions.php');
require_once('Log.php');
head();
right_boxes('news');
content();
?>

<?php

global $USER, $RUNAS_CMD, $RESTORE_TYPE, $RESTORE, $RESTOREMAIL, $SCRIPT_LOG_FACILITY, $WEB_LOG_FACILITY;

$path_arr = NULL;
if (isset($_POST['path_arr']))
    $path_arr = $_POST['path_arr'];

// restore each directory or file
$success_arr = NULL;
$failure_arr = NULL;
$addmsg = NULL;
$totalMB = 0;
foreach ($path_arr as $_ => $path) {
    if ($RESTORE_TYPE == 'mail') {
        $user_cmd = "$RESTOREMAIL -f $SCRIPT_LOG_FACILITY -p '$path'";
        $html = "Use your favorite mail program to look for folder(s) by the name of:";
        $path = 'RESTORE' . basename($path);
    } else {
        $user_cmd = "$RESTORE -f $SCRIPT_LOG_FACILITY -p '$path'";
        $html = "The following data was restored to:";
        $path = "~$USER/RESTORE/" . basename($path);
    }
    $exec_target = "$RUNAS_CMD $USER $user_cmd";
    $result = NULL;
    exec($exec_target, $result, $status);

    // note: $result[0] contains the total MB or error message if one exists
    if ($status == 0) {
        $success_arr[] = $path;
        if (isset($result[0])) {
            $tmp_arr = explode(':', $result[0]);
            if (isset($tmp_arr[2]))
                $tmp2_arr = preg_split("/[\s]+/", $tmp_arr[2]);
            if (isset($tmp2_arr[0]))
                $totalMB += $tmp2_arr[0];
        } else {
            $addmsg = '(inaccurate)';
        }
    }
    else {
        if (isset($result[0]))
            $failure_arr[] = "$path -- $result[0]";
        else
            $failure_arr[] = "$path -- error not returned";
    }

}

// setup logger
$tool = basename(dirname($_SERVER['PHP_SELF']));
$logger = &Log::factory('syslog', $WEB_LOG_FACILITY, 'webtool');

$http_referer = $_SERVER['HTTP_REFERER'];
if (isset($failure_arr)) {

    // log failure summary
    $count = count($failure_arr);
    $logger->log("FAILURE:$tool:$USER:n/a MB NOT restored from $count sources", PEAR_LOG_INFO);

    // output HTML
    $failure = implode('<br />', $failure_arr);
    print <<<HTML
    Sorry the following data was NOT restored to:
    <br /><br />
    $failure
    <br /><br />
    You may <a href="$http_referer">go back and try again</a> OR contact the Support Center at 732-445-HELP (4357).
    <br /><br />
HTML;
}
if (isset($success_arr)) {

    // log successful summary of total MB restored and from how many sources
    $count = count($success_arr);
    $logger->log("SUCCESS:$tool:$USER:$totalMB MB restored from $count sources $addmsg", PEAR_LOG_INFO);

    // output HTML
    $success = implode('<br />', $success_arr);
    print <<<HTML
    $html
    <br /><br />
    $success
    <br /><br />
    You may <a href="$http_referer">go back and restore more data</a>.
    <br /><br />
HTML;
}

?>

<?php
foot();
?>
