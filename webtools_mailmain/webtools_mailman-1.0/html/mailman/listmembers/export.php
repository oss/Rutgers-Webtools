<?php
/* TODO - Turn this into PEAR HTTP_Header code */

header('Content-Type: application/CSV');
header('Content-Disposition: attachment; filename="members.csv"');

require_once '/usr/lib64/webtools/config/config.php';

global $RUNAS_CMD, $RUNAS_USER, $LIST_MEMBERS;

/* TODO - Just pass the $result array */
if (array_key_exists('listname', $_POST)) {
    $listname = $_POST['listname'];
}

/* Retrieve this lists configuration */
$usercmd = "$LIST_MEMBERS -f $listname";
$exec_target = "$RUNAS_CMD $RUNAS_USER $usercmd";
exec($exec_target, $result, $status);
if ($status == 0) {
    foreach($result as $_ => $value) {
        $lchev = strpos($value, '<');
        if ($lchev !== false) {
            $rchev = strpos($value, '>');
            $name = substr($value, 0, $lchev-1);
            $email = substr($value, $lchev+1, $rchev-$lchev-1);
            $name = trim($name);
            $email = trim($email);
            echo $name . "," . $email . "\n";
        } else {
            $email = trim($value);
            echo "," . $email . "\n";
        }
    }
}

?>
