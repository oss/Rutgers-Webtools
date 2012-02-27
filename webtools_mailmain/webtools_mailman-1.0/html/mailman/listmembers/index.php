<?php
require_once '/usr/lib64/webtools/config/config.php';
require_once 'myfunctions.php';
require_once 'PEAR.php';
require_once 'Mail.php';
require_once 'Log.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/Rutgers.php';
head("Who are the members of my lists?", "keywords", "description");
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
/**
 * Setup the form
 */
$form = new HTML_QuickForm('myform', 'post');

$form->addElement('header', null, 'Choose a list that you would like to see the members.');

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
$foundalist = false;
if ($status == 0) {
    // Figure out all lists owned by $USER containing a Big 7 email address
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
                $foundalist = true;
                $pos = strpos($listname_etal, '(as owner)');
                if ($pos !== false){
                    $listname = substr($listname_etal, 0, $pos-1);
                    $list_arr[$listname] = $listname;
                }
            }
        }
    }
} 


$s =& HTML_QuickForm::createElement('select', 'listname');
$s->addOption('Select one..', null);
$s->loadArray($list_arr);
$s->setLabel('Select a listname:');
$form->addElement('static', null, '&nbsp;');
$form->addElement($s);
$form->addElement('static', null, '&nbsp;');

$form->addElement('submit', 'page1', 'Submit');

/**
 * Define validation rules
 */

/* Require all fields */
$form->addRule('listname', 'Field required', 'required');

/**
 * Do the actual validation and process upon success
 */
if ($form->validate()) {
    $form->freeze();
    $form->process('process_data', false);
} else {
    if ($foundalist) {
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
HTML;

        /* Show the form */
        $renderer =& new HTML_QuickForm_Renderer_Rutgers();
        $template_note = '<tr><td colspan="2">{label}</td></tr>';
        $renderer->setElementTemplate($template_note, 'note');
        $form->accept($renderer);
        echo $renderer->toHtml();
    } else {
        print <<<HTML
        Sorry, no lists owned by $USER were found!
HTML;
    }
}
?>

<p>
Questions about Mailman list members may be addressed to <a href='mailto:help@email.rutgers.edu'>help@email.rutgers.edu</a>.

<?php
foot(null, null, true);
?>
