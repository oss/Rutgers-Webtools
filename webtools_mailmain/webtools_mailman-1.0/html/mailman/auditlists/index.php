<?php
require_once '/usr/lib64/webtools/config/config.php';
require_once 'myfunctions.php';
require_once 'myconfig.php';
require_once 'PEAR.php';
require_once 'Mail.php';
require_once 'Log.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/Rutgers.php';
head('Mailman list audit', 'keywords', 'description');
?>

<?php
// Generate this users allowable owner/member email addresses
$addresses = NULL;
foreach ($ALLOWED_DOMAINS_ARR as $domain)
    $addresses .= "^$USER@$domain$ ";

// Find those lists
$usercmd = "$FIND_MEMBER -w $addresses";
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
            if (in_array($domain, $ALLOWED_DOMAINS_ARR)) {
                $savelist = true;
            } else {
                $savelist = false;
            }
        } else {
            if ($savelist) {
                $pos = strpos($listname_etal, '(as owner)');
                if ($pos !== false) {
                    $listname = substr($listname_etal, 0, $pos-1);
                    if($ANNUAL_AUDIT) {
                        // show ALL lists for this owner
                        $list_arr[$listname] = $listname;
                    } else {
                        // ONLY show lists for this owner that did NOT comply in the last audit
                        if(in_array($listname, $LISTS_ARR)) {
                            $list_arr[$listname] = $listname;
                        }
                    }
                }
            }
        }
    }
    if (is_array($list_arr)) {
        $form = new HTML_QuickForm('myform', 'post');
        $s =& HTML_QuickForm::createElement('hiddenselect', 'listnames');
        $s->loadArray($list_arr);
        $s->setMultiple(true);
        $s->setSelected($list_arr);
        $form->addElement($s);
        foreach ($list_arr as $_ => $listname) {

            /* Setup vanilla radio buttons */
            $savetext = 'Save';
            $deletetext = 'Delete';

            if (is_readable("$AUDIT_DIR/$listname")) {
                $readable_arr[$listname] = $listname;

                /* Write current action and NetID associated */
                $info_arr = current_action($listname);

                /* Setup informative radio buttons */
                if(isset($info_arr['Save'])) {
                    $savetext = <<<HTML
                    <b>Save</b> (is current status as per <b>{$info_arr['Save']}</b> according to this <a href='/mailman/policy/index.php#webtool' class='nav_item_b'>policy</a>)
HTML;
                } else if(isset($info_arr['Delete'])) {
                    $deletetext = <<<HTML
                    <b>Delete</b> (is current status as per <b>{$info_arr['Delete']}</b> according to this <a href='/mailman/policy/index.php#webtool' class='nav_item_b'>policy</a>)
HTML;
                }
            }

            /* Create form for listnames requiring user toggles of either Save or Delete */
            $tmp_arr[] =& HTML_QuickForm::createElement('radio', "$listname", null, $savetext, 'Save');
            $tmp_arr[] =& HTML_QuickForm::createElement('radio', "$listname", null, $deletetext, 'Delete');
            $form->addGroup($tmp_arr, $listname, "$listname:", '<br />', false);
            $form->addElement('static', null, '&nbsp;');
            $tmp_arr = null;
            
            /* Require all fields */
            $form->addRule($listname, 'Field required', 'required');
        }

        /* Pass hidden listnames that ARE readable */
        $r =& HTML_QuickForm::createElement('hiddenselect', 'readable');
        $r->loadArray($readable_arr);
        $r->setMultiple(true);
        $r->setSelected($readable_arr);
        $form->addElement($r);

        $form->addElement('static', null, '&nbsp;');
        $form->addElement('submit', 'page1', 'Submit');

        if ($form->validate()) {
            $form->freeze();
            $form->process('process_data', false);
        } else {
            if($ANNUAL_AUDIT) {
                $note = "$USER was found to be an administrator of the list(s) below.";
            } else {
                $note = "$USER was found to be an administrator of the list(s) below that failed to comply with the last audit.";
            }
            print <<<HTML
            <p>
            As described in the
            <a href='/mailman/policy/' class='nav_item_b'>mailing list policies</a>,
            only someone with a valid Rutgers University NetID may be a list administrator.
            If, after creating the list, <b>you changed your list administrator address</b> from a
            netid@rutgers.edu address to another address, <b>your list may not turn up in this
            tool</b>.
            <p>
            Other addresses, including other Rutgers addresses, are not valid.
            You must change the administrator's address back to a valid one before
            this tool can find the list to renew it. As a starting point please navigate to the 
            <a href='/mailman/admins/' class='nav_item_b'>admin portal</a>. Any lists for which
            <b>NO</b> action was taken will be deleted.
            <p>
            <h4>
            $note
            For each list select either the Save or Delete option: 
            </h4>
HTML;

            /* Show the form */
            $renderer =& new HTML_QuickForm_Renderer_Rutgers();
            $template_note = '<tr><td colspan="2">{label}</td></tr>';
            $renderer->setElementTemplate($template_note, 'note');
            $form->accept($renderer);
            echo $renderer->toHtml();
        }
    } else {
        if($ANNUAL_AUDIT) {
            $note = "<b>Sorry, no lists administered by $USER were found!</b>";
        } else {
            $note = "<b>No lists administered by $USER failed to comply with the last audit. Make sure to re-read the paragraphs above because some list(s) you administer may not be turning up in this tool for the reasons explained above.</b>";
        }
        print <<<HTML
        <p>
        As described in the
        <a href='/mailman/policy/' class='nav_item_b'>mailing list policies</a>,
        only someone with a valid Rutgers University NetID may be a list administrator.
        If, after creating the list, <b>you changed your list administrator address</b> from a
        netid@rutgers.edu address to another address, <b>your list may not turn up in this
        tool</b>.
        <p>
        Other addresses, including other Rutgers addresses, are not valid.
        You must change the administrator's address back to a valid one before
        this tool can find the list to renew it.  As a starting point please navigate to the 
        <a href='/mailman/admins/' class='nav_item_b'>admin portal</a>.
        <p>
        $note
HTML;
    }
} else {
    if($ANNUAL_AUDIT) {
        $note = "<b>Sorry, no lists administered by $USER were found!</b>";
    } else {
        $note = "<b>No lists administered by $USER failed to comply with the last audit. Make sure to re-read the paragraphs above because some list(s) you administer may not be turning up in this tool for the reasons explained above.</b>";
    }
    print <<<HTML
    <p>
    As described in the
    <a href='/mailman/policy/' class='nav_item_b'>mailing list policies</a>,
    only someone with a valid Rutgers University NetID may be a list administrator.
    If, after creating the list, <b>you changed your list administrator address</b> from a
    netid@rutgers.edu address to another address, <b>your list may not turn up in this
    tool</b>.
    <p>
    Other addresses, including other Rutgers addresses, are not valid.
    You must change the administrator's address back to a valid one before
    this tool can find the list to renew it.  As a starting point please navigate to the
    <a href='/mailman/admins/' class='nav_item_b'>admin portal</a>.
    <p>
    $note
HTML;
}

?>

<p>
Questions about your Mailman lists may be addressed to <a href='mailto:help@email.rutgers.edu' class='nav_item_b'>help@email.rutgers.edu</a>.

<?php
foot(null, null, true);
?>
