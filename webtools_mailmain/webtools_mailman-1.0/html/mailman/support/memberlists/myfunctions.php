<?php
/**
 * Process the form data (i.e. Run the mailman binary  for this list)
 *
 * @param array     $val_arr    Associative array of form fields
 */
function process_data($val_arr)
{
    global $USER, $RUNAS_CMD, $RUNAS_USER, $FIND_MEMBER;

    /* Find ALL lists this email address is a member of */
    $email = $val_arr['email'];
    $usercmd = "$FIND_MEMBER $email";
    $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
    exec($exec_target, $result, $status);

    /**
     * Results look like....
     * brylon@jla.rutgers.edu found in:
     *   uhr_email2
     *   uhr_email3
     *   rci_ici
     *   mailman_admin_forum
     *   campus_systems
     *   css_staff
     */
    if ($status == 0) {
        $thelists = null;
        foreach ($result as $_ => $listname_etal) {
            $listname_etal = trim($listname_etal);
            if ($pos = strpos($listname_etal, '@')){
                $addr = $listname_etal;
                $list_arr[] = "";
                $list_arr[] = "<b>$addr</b>";
            } else {
                $list_arr[] = "<a href='/mailman/listinfo/$listname_etal'>$listname_etal</a>";
            }
        }
        if (is_array($list_arr)){
            $thelists = implode('<br>', $list_arr);
            print <<<HTML
            <p>
            Below are all the lists $email is subscribed to (Click on a list name to visit the info page for that list.):
            <p>
            $thelists
HTML;
        } else {
            print <<<HTML
            Sorry, $email is not subscribed to any list!
HTML;
        }
    } else {
        print <<<HTML
        Sorry, $email is not subscribed to any list!
HTML;
    }

    /* Log the time of request, NetID, and listname */
    $ident = substr(dirname($_SERVER['PHP_SELF']), 1);
    $logger = &Log::factory('syslog', LOG_SYSLOG, $ident);
    $logger->log('email=' . $email . ', action=find_member_not_owner, status=' . $status);

    return true;
}

?>
