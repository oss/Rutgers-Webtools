<?php
/**
 * Determine whether the checkboxes valid to process
 *
 * @param string    $val_arr    Associative array of form fields
 */
function validateCheckboxes($val_arr)
{
    if (array_key_exists('mod', $val_arr)) {
        if (array_key_exists('regular', $val_arr) ||
           array_key_exists('digest', $val_arr) ||
           array_key_exists('nomail', $val_arr) || 
           array_key_exists('invalid', $val_arr)) {
            return array('mod' => 'Ignores -r, -d, -n, -i (so uncheck all that apply)');
        }
    }
    else if (array_key_exists('invalid', $val_arr)) {
        if (array_key_exists('regular', $val_arr) ||
           array_key_exists('digest', $val_arr) ||
           array_key_exists('nomail', $val_arr)) {
            return array('invalid' => 'Ignores -r, -d, -n (so uncheck all that apply)');
        }
    }
    else if (array_key_exists('nomail', $val_arr)) {
        if (array_key_exists('regular', $val_arr) ||
           array_key_exists('digest', $val_arr)) {
            return array('nomail' => 'Ignores -r, -d (so uncheck all that apply)');
        }
    }
    return true;
}

/**
 * Process the form data (i.e. Run the mailman binary config_list for this list)
 *
 * @param array     $val_arr    Associative array of form fields
 */
function process_data($val_arr)
{
    global $USER, $RUNAS_CMD, $RUNAS_USER, $LIST_MEMBERS, $DUMPDB, $MM_LISTS_DIR;

    $http_referer = $_SERVER['HTTP_REFERER'];

    if (array_key_exists('mod', $val_arr)) {
        /* Retrieve this lists configuration */
        $usercmd = "$DUMPDB $MM_LISTS_DIR/" . $val_arr['listname'] . "/config.pck"; 
        $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
        exec($exec_target, $result, $status);
        if ($status == 0) {
            foreach ($result as $index => $value) {
                if (strpos($value, 'user_options')) {
                    $startkey = $index;
                } else if (strpos($value, 'usernames')) {
                    $endkey = $index;
                    break;
                }
            }

            $tmp_arr = array_slice($result, $startkey, $endkey-$startkey);
            $totalcount = count($tmp_arr);

            // Handle the first entry different b/c of the extra 'user_options:' text
            $tmp2_arr = explode('{', $tmp_arr[0]);
            $tmp_arr[0] = $tmp2_arr[1];

            // Handle the last entry different b/c of the "}" at the end
            $tmp2_arr = explode('}', $tmp_arr[$totalcount-1]);
            $tmp_arr[$totalcount-1] = $tmp2_arr[0];

            // Do we want moderated OR unmoderated members?
            if (strcmp($val_arr['mod'], 'on') == 0) {
                $mod = true;
                $type = 'moderated';
            } else {
                $mod = false;
                $type = 'unmoderated';
            }

            // Create a new array of the following format [email address] -> useroptions (ie, some number)
            $output = NULL;
            $member_count = 0;
            foreach ($tmp_arr as $_ => $value) {
                $tmp3_arr = explode(':', trim($value));
                $tmp4_arr = explode(',', $tmp3_arr[1]);

                $bin = strrev(decbin($tmp4_arr[0]));
                // The moderation bit is set if the 8th bit is a 1.
                if (isset($bin[7]) && $bin[7] == 1) {
                    if ($mod) {
                        $member_count++;
                        $output .= $tmp3_arr[0] . '<br>';
                    }
                } else {
                    if (! $mod) {
                        $member_count++;
                        $output .= $tmp3_arr[0] . '<br>';
                    }
                }
            }

            if (isset($output)) {
                print <<<HTML
                There are $member_count $type member results:
                <p>
                $output
                <p>
                You may <a href="$http_referer">go back and lookup another lists moderated members </a> or visit <a href="/mailman/">RU Mailman</a>.
HTML;
            } else {
                print <<<HTML
                No $type members for {$val_arr['listname']}.
                <p>
                You may <a href="$http_referer">go back and lookup another lists moderated members </a> or visit <a href="/mailman/">RU Mailman</a>.
HTML;
            }
        } else {
            print <<<HTML
            Failed showing the lists moderated members for {$val_arr['listname']}, please contact 445-HELP.
            <p>
HTML;
        }

        /* Log the time of request, NetID, and listname */
        $ident = substr(dirname($_SERVER['PHP_SELF']), 1);
        $logger = &Log::factory('syslog', LOG_SYSLOG, $ident);
        $logger->log('netid=' . $USER . ', listname=' . $val_arr['listname'] . ', action=dumpdb, status=' . $status);

    } else {
        $args = NULL;
        if (array_key_exists('regular', $val_arr)) {
            $args .= '-r ';
        } if (array_key_exists('digest', $val_arr)) {
            $args .= '-d ';
        } if (array_key_exists('nomail', $val_arr)) {
            $args .= '-n ';
        } if (array_key_exists('invalid', $val_arr)) {
            $args .= '-i ';
        }

        /* Retrieve this lists configuration */
        $usercmd = "$LIST_MEMBERS -f $args" . $val_arr['listname'];
        $exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
        exec($exec_target, $result, $status);
        if ($status == 0) {
            $count = count($result);
            $tmp = implode("\n", $result);
            $tmp = htmlspecialchars($tmp);
            $output = nl2br($tmp);
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

        /* Log the time of request, NetID, and listname */
        $ident = substr(dirname($_SERVER['PHP_SELF']), 1);
        $logger = &Log::factory('syslog', LOG_SYSLOG, $ident);
        $logger->log('netid=' . $USER . ', listname=' . $val_arr['listname'] . ', action=list_members, status=' . $status);
    }

    return true;
}

?>
