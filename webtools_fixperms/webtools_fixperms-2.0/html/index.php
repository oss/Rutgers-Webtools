<?php
require_once('/usr/local/lib64/webtools/config/config.php');
head();
right_boxes('news');
content();
?>

<?php

global $USER;

// setup greeting
$name = getFullName($USER);
if (isset($name))
    $greeting = "Hello, $name.";
else
    $greeting = "Hello, $USER.";

$form = <<<HTML
<fieldset class='webtools'>
<legend class='webtools'>Fix Permissions:</legend>
To tighten up permissions.
<p></p>
<input type='button' id='fixbutton' class='buttonWebtools' value='Fix Permissions' onclick="showResult('fix', 'showfix', 'loader_for_fix');" />
<div id='loader_for_fix' style='display:none'></div>
<div id='showfix'></div>
</fieldset>

<fieldset class='webtools'>
<legend class='webtools'>List Files Affected:</legend>
List what changes would be made without doing them.
<br />
If you want to change permissions yourself, this will list what files you should look at.
<p></p>
<input type='button' id='listbutton' class='buttonWebtools' value='List Files Affected' onclick="showResult('list', 'showlist', 'loader_for_list');" />
<div id='loader_for_list' style='display:none'></div>
<span id='showlist'></span>
</fieldset>
HTML;

?>

<div id='greeting'><?=$greeting?></div>
<p></p>
This webtool allows you to tighten up permissions for your files.
After running this only you will be able to see and change files in your home directory and all subdirectories.
Read permissions in public_html and mysql related directories will not be changed.
<p></p>
<?=$form?>
<p></p>
Find out <a href="http://www.nbcs.rutgers.edu/unix_permissions/"> more information on reviewing and changing permissions</a>.

<?php
foot();
?>
