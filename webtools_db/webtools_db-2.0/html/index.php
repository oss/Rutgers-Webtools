<?php
require_once('/usr/local/lib64/webtools/config/config.php');
head();
right_boxes('news');
content();
?>

<?php
global $USER, $RUNAS_USER, $RUNAS_CMD, $DB, $DEFAULT_PRIV_TYPE;

// setup greeting
$name = getFullName($USER);
if (isset($name))
    $greeting = "Hello, $name.";
else
    $greeting = "Hello, $USER.";

// list any active/pending databases for user
$usercmd = "$DB -l $USER";
$exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
exec($exec_target, $lines, $status);
$hasActive = false;
if (isset($status) && $status == 0) {
    foreach($lines as $_ => $value) {
        if (preg_match('/^GRANT USAGE ON/', $value)) {
            // ignore password hash
            continue;
        } else if (preg_match("/^GRANT (.*) ON `([a-zA-Z0-9_]+)`\..* TO '([a-zA-Z0-9_]+)'@'[a-zA-Z\.\%]+'/", $value, $matches)) {
            // SELECT, INSERT, UPDATE, DELETE
            $priv_type           = $matches[1];
            // netid_test
            $priv_level          = $matches[2];
            // netid
            $user_specification  = $matches[3];

            // determine active vs pending by db name prefix of $netid_ AND db owner matching this netid or not
            if (preg_match("/^${USER}_/", $priv_level)) {
                if (preg_match("/^${USER}$/", $user_specification)) {
                    $db_arr[$priv_level][] = 'Active';
                    $hasActive = true;
                } else {
                    $db_arr[$priv_level][] = 'Pending';
                }

                // do privs match default privs?
                $priv_arr = preg_split('/[,][\s]/', $priv_type);
                $priv_arr = array_map('strtolower', $priv_arr);
                $default_priv_arr = array_map('strtolower', $DEFAULT_PRIV_TYPE);
                $t = array_diff($default_priv_arr, $priv_arr);
                if (empty($t))
                    $priv_type = 'Default';

                // add privileges to database array
                $db_arr[$priv_level][] = $priv_type;
            }
        }
    }
}

// present the current (editable) data (if any exists)
$divs = NULL;
if (isset($db_arr) && is_array($db_arr)) {
    // count number of databases attributed to this user
    $divs = <<<HTML
    <span id='span_add'>
    <div class='container-header'>
      <div class='float'>
        <p class='image'>&nbsp;</p>
      </div>
      <div class='float'>
        <p id='dheader' class='database'>Database</p>
      </div>
      <div class='float'>
        <p id='sheader' class='status'>Status</p>
      </div>
      <div class='float'>
        <p id='pheader' class='privs'>Privileges</p>
      </div>
    </div>
    </span>
    <span id='span_add_entry'></span>
HTML;
    $unum = 1;
    foreach ($db_arr as $database => $tmparr) {
        $status = $tmparr[0];
        $privs = ucwords(strtolower($tmparr[1]));
        // add hidden field populating current dbnames and create div entry
        //<p class='image'><a href='javascript:;' onclick="deleteEntry('entry$unum', '$database')"><img src='/css/newimages/delete.png' alt='Delete database $database' title='Delete database $database' /></a></p>
        $divs .=<<<HTML
            <div id='entry$unum'>
              <input type='hidden' name='current_dbnames[]' value='$database'>
              <div class='container'>
                <div class='float'>
                  <p class='image'></p>
                </div>
                <div class='float'>
                  <p class='database'>$database</p>
                </div>
                <div class='float'>
                  <p class='status'>$status</p>
                </div>
                <div class='float'>
                  <p class='privs'>$privs</p>
                </div>
              </div>
            </div>
HTML;
        // up the user (ie, entry) num by 1
        $unum++;
    }
} else {
    $divs = "<span id='span_add'>Request some above...</span>";
}

// If there are no current databases, we don't need to show the password related buttons
if ($hasActive) {
    $pw_divs .=<<<HTML
        <input type='button' id='showpwbutton' class='buttonWebtools' value='Show Password' title='Show Database Password' onclick="showResult('showpw', 'showpwresult', 'loader_for_showpw')" />
        <div id='db_note2'><b>Note:</b> You only need to know the password for commandline MySQL access (<b>use cautiously</b>)</div>
        <p></p>
        <span id='loader_for_showpw' style='display:none'></span>
        <div id='showpwresult'></div>
        <br />
        <input type='button' id='resetpwbutton' class='buttonWebtools' value='Reset Password' title='Reset Database Password' onclick="showResult('resetpw', 'resetpwresult', 'loader_for_resetpw')" />
        <p></p>
        <span id='loader_for_resetpw' style='display:none'></span>
        <div id='resetpwresult'></div>
HTML;
} else {
    $pw_divs = "You don't have one yet...";
}

?>

<div id='greeting'><?=$greeting?></div>
<p></p>
<fieldset class='webtools'>
<span id='showpending'></span>
<legend class='webtools'>Request New Database:</legend>
<label id='label_dbname' for='dbname'>Database Name:</label>
<input id='dbname' size='25' maxlength='100' name='dbname' type='text' />
<p></p>
<div id='db_showprivs'>
<a href='#' onclick="return toggle('db_hideprivs', 'db_showprivs');">
Configure Default Privileges (show)
</a>
</div>

<div id='db_hideprivs'>
<a href='#' onclick="return toggle('db_showprivs', 'db_hideprivs');">
Configure Default Privileges (hide)
</a>
<p><p/>
<div class='db_leftprivs'>
<label><input type='checkbox' id='db_selectpriv' name='db_privs' value='select' checked='checked' /> Select</label>
</div>
<div class='db_rightprivs'>
<label><input type='checkbox' id='db_locktablespriv' name='db_privs' value='lock tables' checked='checked' /> Lock Tables</label>
</div>
<div class='db_leftprivs'>
<label><input type='checkbox' id='db_insertpriv' name='db_privs' value='insert' checked='checked' /> Insert</label>
</div>
<div class='db_rightprivs'>
<label><input type='checkbox' id='db_createviewpriv' name='db_privs' value='create view' checked='checked' /> Create View</label>
</div>
<div class='db_leftprivs'>
<label><input type='checkbox' id='db_updatepriv' name='db_privs' value='update' checked='checked' /> Update</label>
</div>
<div class='db_rightprivs'>
<label><input type='checkbox' id='db_showviewpriv' name='db_privs' value='show view' checked='checked' /> Show View</label>
</div>
<div class='db_leftprivs'>
<label><input type='checkbox' id='db_deletepriv' name='db_privs' value='delete' checked='checked' /> Delete</label>
</div>
<div class='db_rightprivs'>
<label><input type='checkbox' id='db_createroutinepriv' name='db_privs' value='create routine' checked='checked' /> Create Routine</label>
</div>
<div class='db_leftprivs'>
<label><input type='checkbox' id='db_createpriv' name='db_privs' value='create' checked='checked' /> Create</label>
</div>
<div class='db_rightprivs'>
<label><input type='checkbox' id='db_alterroutinepriv' name='db_privs' value='alter routine' checked='checked' /> Alter Routine</label>
</div>
<div class='db_leftprivs'>
<label><input type='checkbox' id='db_droppriv' name='db_privs' value='drop' checked='checked' /> Drop</label>
</div>
<div class='db_rightprivs'>
<label><input type='checkbox' id='db_executepriv' name='db_privs' value='execute' checked='checked' /> Execute</label>
</div>
<div class='db_leftprivs'>
<label><input type='checkbox' id='db_indexpriv' name='db_privs' value='index' checked='checked' /> Index</label>
</div>
<div class='db_rightprivs'>
<label><input type='checkbox' id='db_eventpriv' name='db_privs' value='event' checked='checked' /> Event</label>
</div>
<div class='db_leftprivs'>
<label><input type='checkbox' id='db_alterpriv' name='db_privs' value='alter' checked='checked' /> Alter</label>
</div>
<div class='db_rightprivs'>
<label><input type='checkbox' id='db_triggerpriv' name='db_privs' value='trigger' checked='checked' /> Trigger</label>
</div>
</div>
<p></p>
<input type='button' id='db_requestbutton' class='buttonWebtools' value='Submit Request' title='Request New Database' onclick="addPendingEntry('<?=$USER?>', document.getElementById('dbname').value, document.getElementsByName('db_privs'), document.getElementsByName('current_dbnames[]'))" />
</fieldset>
<p></p>
<fieldset class='webtools'>
<legend class='webtools'>Current Database(s):</legend>
<?=$divs?>
</fieldset>
<p></p>
<fieldset class='webtools'>
<legend class='webtools'>Manage Database Password:</legend>
<?=$pw_divs?>
</fieldset>

<?php
foot();
?>
