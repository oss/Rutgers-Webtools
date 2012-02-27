<?php
/**
 * Process the form data (i.e. Run the mailman binary  for this list)
 *
 * @param array     $val_arr    Associative array of form fields
 */
function process_data($val_arr)
{
    global $USER, $RUNAS_CMD, $RUNAS_USER, $FIND_MEMBER;

    /**
     * The "Big 7" list that the $USER must use as the owner address
     * for this tool to work correctly.
     */
    $domain_arr = array('eden.rutgers.edu', 'rci.rutgers.edu',
                        'pegasus.rutgers.edu', 'andromeda.rutgers.edu',
                        'clam.rutgers.edu', 'crab.rutgers.edu',
                        'rutgers.edu');

    /* Find ALL lists this NetID is an owner of */
    $netid = $val_arr['netid'];
    $usercmd = "$FIND_MEMBER -w '^$netid@'";
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
        // Figure out all lists owned by $netid containing a Big 7 email address
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
                if ($savelist){
                    $pos = strpos($listname_etal, '(as owner)');
                    if ($pos !== false){
                        $listname = substr($listname_etal, 0, $pos-1);
                        $list_arr[$listname] = "<input type='submit' name='listname' value='$listname'>";
                    }
                }
            }
        }
        if (is_array($list_arr)){
            $thelists = implode('<br/>', $list_arr);
            print <<<HTML
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
            Below are all the lists owned by $netid:
            <p>
            <form action='/mailman/webtools/support/listconfig/index.php' method='post' name='myform'>
            $thelists
            </form>
HTML;
        } else {
            print <<<HTML
            Sorry, no lists owned by $netid were found!
HTML;
        }
    } else {
        print <<<HTML
        Sorry, no lists owned by $netid were found!
HTML;
    }

    /* Log the time of request, NetID, and listname */
    $ident = substr(dirname($_SERVER['PHP_SELF']), 1);
    $logger = &Log::factory('syslog', LOG_SYSLOG, $ident);
    $logger->log('netid=' . $USER . ', action=find_member, status=' . $status);

    return true;
}

?>
