<?php
require_once('/usr/local/lib64/webtools/config/config.php');
head();
right_boxes('news');
content();
?>

<?php

global $USER, $RUNAS_CMD, $VMAIL;

// setup greeting
$name = getFullName($USER);
if (isset($name))
    $greeting = "Hello, $name.";
else
    $greeting = "Hello, $USER.";

// gather any vmail usernames already defined
$usercmd = "$VMAIL -l";
$exec_target = "$RUNAS_CMD $USER $usercmd";
exec($exec_target, $lines, $status);
if (isset($status) && $status == 0) {
    foreach ($lines as $_ => $line) {
        $tmp_arr = explode(':', $line);

        // retrieve old vmail username
        $oldvusername = str_replace('.qmail-', '', $tmp_arr[0]);

        // retrieve old vmail username forwarding addresses
        $address_arr = array_slice($tmp_arr, 1);
        $vmail_arr[$oldvusername] = array_filter($address_arr, 'trim');
    }
}

// present the current (editable) data (if any exists)
$divs = NULL;
if (isset($vmail_arr) && is_array($vmail_arr)) {
    $divs = <<<HTML
    <span id='span_add'>
    <div class='container-header'>
      <div class='float'>
        <p class='image'>&nbsp;</p>
      </div>
      <div class='float'>
        <p id='uheader' class='username'>Username</p>
      </div>
      <div class='float'>
        <p id='aheader' class='address'>Address</p>
      </div>
    </div>
    </span>
    <span id='span_add_entry'></span>
HTML;
    $unum = 1;
    $anum = 1;
    foreach ($vmail_arr as $username => $address_arr) {
        $divs .=<<<HTML
            <div id='entry$unum'>
              <div class='container'>
                <div class='float'>
                  <p class='image'><a href='javascript:;' onclick="deleteEntry('entry$unum', '$username')"><img src='/css/newimages/delete.png' alt='Delete virtual user $username' title='Delete virtual user $username' /></a></p>
                </div>
                <div class='float'>
                  <p class='username'>$username</p>
                </div>
                <div class='addressfloat'>
                  <p class='address'>&nbsp;</p>
                </div>
              </div>
                <div id='showaddressresult$unum' class='deleteaddress'></div>
HTML;
        foreach ($address_arr as $_ => $address) {
            $divs .=<<<HTML
                <div id='div_entry$unum-address$anum' class='container'>
                  <div class='float'>
                    <p class='image'>&nbsp;</p>
                  </div>
                  <div class='float'>
                    <p class='username'>&nbsp;</p>
                  </div>
                  <div>
                    <p class='address'>
                      <input id='entry$unum-address$anum' size='25' maxlength='100' name='entry$unum-address$anum' type='text' value='$address' readonly='readonly' />
                      <a href='javascript:;' onclick="deleteAddress('div_entry$unum-address$anum', 'showaddressresult$unum', '$username', '$address')"><img src='/css/newimages/delete.png' alt='Delete address $address' title='Delete address $address' /></a>
                    </p>
                  </div>
                </div>
HTML;
            // up the address num by 1
            $anum++;
        }

        $divs .=<<<HTML
              <span id='span_entry$unum-newaddress'></span>
              <div id='div_entry$unum-newaddress-parent'>
                <div id='div_entry$unum-newaddress' class='container'>
                  <div class='float'>
                    <p class='image'>&nbsp;</p>
                  </div>
                  <div class='float'>
                    <p class='username'>&nbsp;</p>
                  </div>
                  <div>
                    <p class='address'>
                      <span id='entry$unum-newaddress-error'></span>
                      <input id='entry$unum-newaddress' size='25' maxlength='100' name='entry$unum-newaddress' type='text' />
                      <a href='javascript:;' onclick="addnewAddress('$unum', '$username', document.getElementById('entry$unum-newaddress').value)"><img src='/css/newimages/add.png' alt='Add address' title='Add address' /></a>
                    </p>
                  </div>
                </div>
              </div>
            </div>
HTML;

        // up the user (ie, entry) num by 1
        $unum++;
    }
} else {
    $divs = "<span id='span_add'>Add some above...</span>";
}

?>

<div id='greeting'><?=$greeting?></div>
<p></p>
<fieldset class='webtools'>
<legend class='webtools'>Add Virtual Mail User:</legend>
<span id='shownew'></span>
<label id='label_username' for='username'>Username:</label>
<input id='username' size='25' maxlength='100' name='username' type='text' />
<p></p>
<label id='label_address' for='address'>Address:</label><input id='address' size='25' name='address' maxlength='100' type='text' />
<p></p>
<input type='button' id='addbutton' class='buttonWebtools' value='Add' title='Add Virtual Mail User' onclick="addnewEntry(document.getElementById('username').value, document.getElementById('address').value)" />
</fieldset>
<fieldset class='webtools'>
<legend class='webtools'>Edit Virtual Mail User:</legend>
<?=$divs?>
</fieldset>

<?php
foot();
?>
