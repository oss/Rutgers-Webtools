<?php
require_once '/usr/lib64/webtools/config/config.php';
require_once 'PEAR.php';
require_once 'Mail.php';
require_once 'Log.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/Rutgers.php';
head("What lists do I own?", "keywords", "description");
?>

<?php
/**
 * The "Big 7" list that the $USER must use as the owner address
 * for this tool to work correctly.
 */
$domain_arr = array('eden.rutgers.edu', 'rci.rutgers.edu',
                    'pegasus.rutgers.edu', 'andromeda.rutgers.edu',
                    'clam.rutgers.edu', 'crab.rutgers.edu',
                    'rutgers.edu');

/* Find ALL lists this NetID is an owner of */
$usercmd = "$FIND_MEMBER -w '^$USER@'";
$exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
exec($exec_target, $result, $status);

/**
 * Results look like....
 * brylon@rutgers.edu found in:
 *    child_lit (as owner)
 *    aps_job_postings (as owner)
 *    brylon-test (as owner)
 */
if ($status == 0) {
    // Figure out all lists owned by $USER containing a Big 7 email address
    $thelists = null;
    $savelist = false;
    foreach ($result as $_ => $listname_etal) {
        $listname_etal = trim($listname_etal);
        if ($pos = strpos($listname_etal, '@')){
            $domain = substr($listname_etal, $pos+1);
            $domain = str_replace(' found in:', '', $domain);
            if (in_array($domain, $domain_arr)) {
                $savelist = true;
            } else {
                $savelist = false;
            }
        } else {
            if ($savelist) {
                $pos = strpos($listname_etal, '(as owner)');
                if ($pos !== false) {
                    $listname = substr($listname_etal, 0, $pos-1);
                    $list_arr[$listname] = "<a href='/mailman/admin/$listname'>$listname</a>";
                }
            }
        }
    }
    if (is_array($list_arr)) {
        $thelists = implode('<br/>', $list_arr);
        print <<<HTML
        <p>
        <b>NOTE:</b> This tool will ONLY fully work if the list owner's email address is of the form
        NetID@rutgers.edu OR NetID@ one of the following:
        <p>
        rci.rutgers.edu
        <br/>
        eden.rutgers.edu
        <br/>
        pegasus.rutgers.edu
        <br/>
        andromeda.rutgers.edu
        <br/>
        crab.rutgers.edu
        <br/>
        clam.rutgers.edu
        <p>
        Below are all the lists owned by $USER (Click on a list name to visit the configuration pages for that list.):
        <p>
        $thelists
HTML;
    } else {
       print <<<HTML
       Sorry, no lists owned by $USER were found!
HTML;
    }
} else {
    print <<<HTML
    Sorry, no lists owned by $USER were found!
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
    $logger->log('netid=' . $USER . ', action=find_member, status=' . $status);
?>

<p>
Questions about your Mailman lists may be addressed to <a href='mailto:help@email.rutgers.edu'>help@email.rutgers.edu</a>.

<?php
foot(null, null, true);
?>
