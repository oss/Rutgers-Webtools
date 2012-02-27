<?php
require_once '/usr/lib64/webtools/config/config.php';
require_once 'myfunctions.php';
require_once 'PEAR.php';
require_once 'Mail.php';
require_once 'Log.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/Rutgers.php';
head("Support -  List configuration", "keywords", "description");
?>

<?php
/**
 * Setup the form
 */
$form = new HTML_QuickForm('myform', 'post');

$form->addElement('header', null, 'Enter the listname that you would like the configuration for.');
$form->addElement('static', null, '&nbsp;');

$form->addElement('text', 'listname', 'Listname (in lowercase AND do not include \'@email.rutgers.edu\'):', array('size' => 40, 'maxlength' => 127));
$form->addElement('static', null, '&nbsp;');

$form->addElement('submit', 'page1', 'List cfg');

/**
 * Define validation rules
 */

/* Trim all fields (in order to combat only spaces in the input fields) */
$form->applyFilter('__ALL__', 'trim');

/* Register some of my own rules */
$form->registerRule('rule_listnameExist', 'callback', 'listnameExist');

/* Require all fields */
$form->addRule('listname', 'Field required', 'required');
$form->addRule('listname', 'Listname does not exist on Mailman', 'rule_listnameExist');

/**
 * Do the actual validation and process upon success
 */
if ($form->validate()) {
    $form->freeze();
    $form->process('process_data', false);
} else {
    /* Show the form */
    $renderer =& new HTML_QuickForm_Renderer_Rutgers();
    $template_note = '<tr><td colspan="2">{label}</td></tr>';
    $renderer->setElementTemplate($template_note, 'note');
    $form->accept($renderer);
    echo $renderer->toHtml();
}
?>

<p>
Questions about a lists configuration may be addressed to <a href='mailto:help@email.rutgers.edu'>help@email.rutgers.edu</a>.

<?php
foot(null, null, true);
?>
