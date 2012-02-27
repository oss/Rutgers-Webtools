<?php

/**
 * Validates an email address
 *
 * @param array    $val_arr    Array of all elements w/ non-NULL values
 */
function isValidEmail($val_arr)
{
    /* remove all the owners array elements equal to FALSE */
    $tmp_arr = array_filter($val_arr['owners']);

    /* Code snippets taken directly from PEAR ~pear/HTML/Quickform/Rule/Email.php and hacked a bit */
    $regex = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';

    foreach ($tmp_arr as $_ => $email) {
        if (preg_match($regex, $email)) {
            $tokens = explode('@', $email);
            if (!checkdnsrr($tokens[1], 'MX') && !checkdnsrr($tokens[1], 'A'))
                return array('owners' => "Email address '$email' is invalid");
        } else {
            return array('owners' => "Email address '$email' is invalid");
        }
    }

    return true;
}

/**
 * Check ALL elements in array are unique
 *
 * @param array    $val_arr    Array to ensure ALL elements are unique
 */
function areUnique($val_arr)
{
    /* remove all the elements equal to FALSE */
    $tmp_arr = array_filter($val_arr);

    /* determine how many elements there are left */
    $cnt = count($tmp_arr);

    /* are all the elements unique? */
    if (count(array_unique($tmp_arr)) != $cnt)
        return false;

    return true;
}

/**
 * Determine whether the listname has been deactivated and thus not avail for 8 weeks
 *
 * @param string    $listname    listname to check
 */
function isDeactivatedListname($listname)
{
    global $MM_DEACTIVATED_LISTS_DIR;
    if (file_exists("$MM_DEACTIVATED_LISTS_DIR/$listname"))
        return false;

    return true;
}

/**
 * Determine whether the listname exists in Mailman
 *
 * @param string    $listname    listname to check
 */
function listnameExist($listname)
{
    global $MM_LISTS_DIR;
    if (!file_exists("$MM_LISTS_DIR/$listname"))
        return false;

    return true;
}

/**
 * Determine whether the listname is available in Mailman
 *
 * @param string    $listname    listname to check
 */
function listnameAvailable($listname)
{
    global $MM_LISTS_DIR;
    if (file_exists("$MM_LISTS_DIR/$listname"))
        return false;

    return true;
}

?>
