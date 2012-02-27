<?php
require_once('/usr/local/lib64/webtools/config/config.php');
require_once("./myfunctions.php");
head();
right_boxes('news');
content();
?>

<?php

$name = getFullName($USER);
if (isset($name))
    $greeting = "Hello, $name.";
else
    $greeting = "Hello, $USER.";

// build all the snapshot directories found for this user
$snapshot_path_arr = buildAllSnapshotArr();

// if no snapshots found, exit and print
if (! is_array($snapshot_path_arr)) {
    print <<<HTML
    <div id='greeting'>$greeting</div>
    <h4>
    No snapshots found.  Need additional help? Locate the Contact us section in the footer of this page.
    </h4>
HTML;
    foot();
    exit;
}

// setup the dates and paths for HTML output
$options = NULL;
foreach ($snapshot_path_arr as $datetime => $snapshot_path) {
    $date = date($DATE_FORMAT, strtotime($datetime));
    $options .= "<option value='$snapshot_path'>$date</option>";
}

// encode and serialize the paths found
$serialized_snapshot_path = base64_encode(serialize($snapshot_path_arr));

?>

<div id='greeting'><?=$greeting?></div>
<p></p>
Choose either a search by term or a backup date to begin.
It may take time to find your restorable <?=$RESTORE_TYPE?> data, so please be patient.
<p></p>
<form id='myform' action='restore.php' method='post'>
<fieldset class='webtools'>
<legend class='webtools'>Search all restorable data:</legend>
    <input type='hidden' name='serialized_snapshot_path' value="<?=$serialized_snapshot_path?>" />
    <input type='text' id="searchbox" class='textWebtools' name="searchterm" value="" size="32" maxlength="128" onkeydown="if (event.keyCode == 13) document.getElementById('searchbutton').focus();" />
    <input type='button' id='searchbutton' class='buttonWebtools' value='Search' onclick="showSearch(searchterm.value, serialized_snapshot_path.value);" />
<div id='loader_for_search' style='display:none'></div>
<span id='showsearch'></span>
</fieldset>
<p></p>
<fieldset class='webtools'>
<legend class='webtools'>Show all restorable data from date:</legend>
    <select id="pathselect" class='webtools' name="path" onchange="showAll(this.value)">
    <option value=''>Select date</option>
    <?=$options?>
    </select>
<div id='loader_for_all' style='display:none'></div>
<span id='showall'></span>
</fieldset>
</form>

<?
foot();
?>
