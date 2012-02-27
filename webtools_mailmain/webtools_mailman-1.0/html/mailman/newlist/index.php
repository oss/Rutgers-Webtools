<?php
require_once '/usr/lib64/webtools/config/config.php';
require_once 'myfunctions.php';
require_once 'myconfig.php';
require_once 'PEAR.php';
require_once 'Mail.php';
require_once 'Log.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/Rutgers.php';
head("Create a new Mailman list", "keywords", "description");
?>

<?php
/**
 * Setup the form
 */
$form = new HTML_QuickForm('myform', 'post');

$form->addElement('static', null, 'Owner address:', "$USER@rutgers.edu");
$form->addElement('hidden', 'initialowner', "$USER@rutgers.edu");
$form->addElement('static', null, '&nbsp;');

$form->addElement('text', 'listname', "Listname: (MUST contain an underscore, i.e. '_')", array('size' => 40, 'maxlength' => 127));
$form->addElement('static', null, '&nbsp;');

$tmp_arr[] =& HTML_QuickForm::createElement('radio', 'subscribe_policy', null, 'Email confirmation required', 'Email confirmation required');
$tmp_arr[] =& HTML_QuickForm::createElement('radio', 'subscribe_policy', null, 'Require list administrator approval for subscriptions', 'Require list administrator approval for subscriptions');
$tmp_arr[] =& HTML_QuickForm::createElement('radio', 'subscribe_policy', null, 'Both confirm and approve <b>(Recommended)</b>', 'Both confirm and approve');
$form->addGroup($tmp_arr, 'subscribe_policy', "What steps are required for subscription:", '<br />', false);
$form->addElement('static', null, '&nbsp;');

$tmp_arr1[] =& HTML_QuickForm::createElement('radio', 'private_roster', null, 'Anyone', 'Anyone');
$tmp_arr1[] =& HTML_QuickForm::createElement('radio', 'private_roster', null, 'List members', 'List members');
$tmp_arr1[] =& HTML_QuickForm::createElement('radio', 'private_roster', null, 'List admin only <b>(Recommended)</b>', 'List admin only');
$form->addGroup($tmp_arr1, 'private_roster', "Who can view subscription list:", '<br />', false);
$form->addElement('static', null, '&nbsp;');

$tmp_arr2[] =& HTML_QuickForm::createElement('radio', 'advertised', null, 'No', 'No');
$tmp_arr2[] =& HTML_QuickForm::createElement('radio', 'advertised', null, 'Yes', 'Yes');
$form->addGroup($tmp_arr2, 'advertised', "Advertise this list when people ask what lists are on this machine:", '<br />', false);
$form->addElement('static', null, '&nbsp;');

$tmp_arr3[] =& HTML_QuickForm::createElement('radio', 'default_member_moderation', null, 'No', 'No');
$tmp_arr3[] =& HTML_QuickForm::createElement('radio', 'default_member_moderation', null, 'Yes <b>(Recommended)</b>', 'Yes');
$form->addGroup($tmp_arr3, 'default_member_moderation', "Do you want your list to be moderated:", '<br />', false);
$form->addElement('static', null, '&nbsp;');

$tmp_arr4[] =& HTML_QuickForm::createElement('radio', 'generic_nonmember_action', null, 'Accept', 'Accept');
$tmp_arr4[] =& HTML_QuickForm::createElement('radio', 'generic_nonmember_action', null, 'Hold (Not valid for unmoderated lists)', 'Hold');
$tmp_arr4[] =& HTML_QuickForm::createElement('radio', 'generic_nonmember_action', null, 'Reject <b>(Recommended)</b>', 'Reject');
$tmp_arr4[] =& HTML_QuickForm::createElement('radio', 'generic_nonmember_action', null, 'Discard', 'Discard');
$form->addGroup($tmp_arr4, 'generic_nonmember_action', "Action to take for postings from non-members:", '<br />', false);

/*
$form->addElement('static', 'note', "Note: The owner address below will receive a piece of mail confirming the request for this new list.");
for ($i=1; $i<3; $i++) {
    $tmp_arr5[] =& HTML_QuickForm::createElement('text', "$i", '', array('size' => 40, 'maxlength' => 127));
}
*/

$form->addElement('static', null, '&nbsp;');
$form->addElement('submit', 'page1', 'Submit');

/**
 * Define validation rules
 */

/* Trim all fields (in order to combat only spaces in the input fields) */
$form->applyFilter('__ALL__', 'trim');
$form->applyFilter('listname', 'strtolower');

/* Register some of my own rules */
$form->registerRule('rule_listnameAvailable', 'callback', 'listnameAvailable');
$form->registerRule('rule_isDeactivatedListname', 'callback', 'isDeactivatedListname');
$form->registerRule('rule_isModeratorOn', 'callback', 'isModeratorOn');
$form->registerRule('rule_isValidEmail', 'callback', 'isValidEmail');
$form->registerRule('rule_areUnique', 'callback', 'areUnique');

/* Require all fields */
$form->addRule('listname', 'Field required', 'required');
$form->addRule('listname', 'Already exists on Mailman', 'rule_listnameAvailable');
$form->addRule('listname', 'A deactivated list with that name exists', 'rule_isDeactivatedListname');
$form->addRule('listname', 'Underscore required', 'regex', '/^[a-zA-Z0-9]+(_){1}[a-zA-Z0-9]+((_)?[a-zA-Z0-9]+)*$/');
$form->addRule('listname', 'Must be between 3 and 80 characters', 'rangelength', array(3, 80));
$form->addRule('subscribe_policy', 'Field required', 'required');
$form->addRule('private_roster', 'Field required', 'required');
$form->addRule('advertised', 'Field required', 'required');
$form->addRule('default_member_moderation', 'Field required', 'required');
$form->addFormRule('isModeratorOn');
$form->addRule('generic_nonmember_action', 'Field required', 'required');

/**
 * Do the actual validation and process upon success
 */
if ($form->validate()) {
    $form->freeze();
    $form->process('process_data', false);
} else {
    print <<<HTML
Please use this form to create a new Mailman mailing list. The list will
be created with some basic attributes. There are many other list attributes
which are optional and can be added later.  Once the list has been
successfully created, the list's primary owner (the NetID of the person
filling out this form) will receive a confirmation message, with
instructions on how to maintain and modify the list. Online documentation
can be viewed and downloaded in a variety of formats from the
<a href='/mailman/'>Rutgers Mailman home</a>. 
More information about  <a href='/mailman/policy/'>
mailing list services</a> is also available. 
<p>
If any of the questions below are unclear, please 
<a href='help.html'>visit our help page</a>.
<p>
HTML;

    /* Show the form */
    $renderer =& new HTML_QuickForm_Renderer_Rutgers();
    $template_note = '<tr><td colspan="2">{label}</td></tr>';
    $renderer->setElementTemplate($template_note, 'note');
    $form->accept($renderer);
    echo $renderer->toHtml();
}
?>

<p>
Questions about list creation may be addressed to <a href='mailto:help@email.rutgers.edu'>help@email.rutgers.edu</a>.

<?php
foot(null, null, true);
?>
