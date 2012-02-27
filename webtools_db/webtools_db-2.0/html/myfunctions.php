<?php
require_once("/usr/local/lib64/webtools/config/config.php");
?>

<?php

// Generate a random password of 3 character classes (note: omit letter 'l' and number 1 and letter 'O' and number 0)
function genpassword() {
    $uc = "ABCDEFGHIJKLMNPQRSTUVWXYZ";
    $lc = "abcdefghijkmnopqrstuvwxyz";
    $c = $uc . $lc;
    $n = "23456789";
    $c_length = strlen($c) - 1;
    $n_length = strlen($n) - 1;
    $password = null;
    for ($i=0; $i <= 7; $i++) {
        if (mt_rand(0,3))
            $password .= $c[mt_rand(0, $c_length)];
        else
            $password .= $n[mt_rand(0, $n_length)];
    }

    // paranoia check that we have at least 1 of each character class
    if (ctype_lower($password))
        $password = substr_replace($password, $uc[mt_rand(0, strlen($uc)-1)] . $n[mt_rand(0, $n_length)], mt_rand(0,6), 2);
    if (ctype_upper($password))
        $password = substr_replace($password, $lc[mt_rand(0, strlen($lc)-1)] . $n[mt_rand(0, $n_length)], mt_rand(0,6), 2);
    if (ctype_digit($password))
        $password = substr_replace($password, $uc[mt_rand(0, strlen($uc)-1)] . $lc[mt_rand(0, strlen($lc)-1)], mt_rand(0,6), 2);
    if (ctype_alpha($password))
        $password = substr_replace($password, $n[mt_rand(0, $n_length)], mt_rand(0,7), 1);
    return $password;
}

?>
