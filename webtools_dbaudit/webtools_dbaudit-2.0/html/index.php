<?php
require_once('/usr/local/lib64/webtools/config/config.php');
require_once('myfunctions.php');
require_once('HTML/QuickForm.php');
head();
right_boxes('news');
content();
?>

<?php

global $USER, $RUNAS_USER, $RUNAS_CMD, $DBAUDIT, $DB_FILE;

$name = getFullName($USER);
if (isset($name))
    $greeting = "Hello, $name.";
else
    $greeting = "Hello, $USER.";

/* Find any databases matching this user */
$list_arr = NULL;
$usercmd = "$DBAUDIT $DB_FILE";
$exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
exec($exec_target, $lines, $status);
if ($status == 0) {
    foreach ($lines as $_ => $dbname) {
        if (($pos = strpos($dbname, $USER.'_')) !== false && $pos == 0)
            $list_arr[] = $dbname;
    }
}

/* Punt iff no databases. */
if (!isset($list_arr)) {
    print <<<HTML
    <div id='greeting'>$greeting</div>
    <h4>
    No databases found.  Need additional help? Locate the Contact us section in the footer of this page.
    </h4>
HTML;
} else {
    $form = new HTML_QuickForm('myform', 'post');
    $info_arr = NULL;
    foreach ($list_arr as $_ => $dbname) {

        /* Setup vanilla radio buttons */
        $savetext = 'Save';
        $deletetext = 'Delete';

        /* Write current action and NetID associated */
        $info_arr = current_action($dbname);

        /* Setup informative radio buttons */
        if(isset($info_arr['Save'])) {
            $savetext = <<<HTML
            <b>Save</b> (is current status as per <b>{$info_arr['Save']}</b>)
HTML;
        } else if(isset($info_arr['Delete'])) {
            $deletetext = <<<HTML
            <b>Delete</b> (is current status as per <b>{$info_arr['Delete']}</b>)
HTML;
        }

        /* Create form for dbname requiring user toggles of either Save or Delete */
        $tmp_arr[] =& HTML_QuickForm::createElement('radio', "$dbname", null, $savetext, 'Save');
        $tmp_arr[] =& HTML_QuickForm::createElement('radio', "$dbname", null, $deletetext, 'Delete');
        $form->addGroup($tmp_arr, $dbname, "$dbname:", '<br />', false);
        $form->addElement('static', null, '&nbsp;');
        $tmp_arr = null;

        /* Require all fields */
        $form->addRule($dbname, 'Field required', 'required');
    }

    $form->addElement('static', null, '&nbsp;');
    $form->addElement('submit', 'page1', 'Submit');

    if ($form->validate()) {
        $form->freeze();
        $form->process('processForm', false);
    } else {
        print <<<HTML
        <div id='greeting'>$greeting</div>
        <h4>
        For each database select either the Save or Delete option:
        </h4>
HTML;

        /* Show the form */
        $form->display();
    }
}

?>

<?php
foot();
?>
