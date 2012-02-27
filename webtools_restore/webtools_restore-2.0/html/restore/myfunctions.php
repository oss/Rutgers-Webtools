<?php

function buildAllSnapshotArr() {
    global $USER, $RUNAS_CMD, $RESTORE, $RESTORE_TYPE, $RESTORE_PATH, $SCRIPT_LOG_FACILITY;

    $path_arr = NULL;
    // Get the list of snapshot dates
    $user_cmd = "$RESTORE -a -f $SCRIPT_LOG_FACILITY -p $RESTORE_PATH";
    $exec_target = "$RUNAS_CMD $USER $user_cmd";
    unset($result);
    exec($exec_target, $result, $status);

    if ($status == 0) {
        foreach ($result as $_ => $line) {
            $tmp = preg_split("/\s+/", $line);
            // skip line(s) that don't look like directory listings
            if (count($tmp) < 10)
                continue;

            // figure out path to be used (ie, append /Maildir for restoremail)
            $path = $tmp[9];
            if ($RESTORE_TYPE == 'mail')
                $path = "$path/Maildir";

            // build array of snapshot paths
            if (runas_file_exists($path)) {
                $m = $tmp[5];
                $d = $tmp[6];
                $Y = $tmp[7];
                $path_arr["$Y-$m-$d"] = $path;
            }
        }
        return $path_arr;
    }
    return false;
}

function runas_file_exists($path) {
    global $USER, $RUNAS_CMD, $FIND;

    $user_cmd = "$FIND '$path' -maxdepth 0";
    $exec_target = "$RUNAS_CMD $USER $user_cmd";
    exec($exec_target, $result, $status);
    if ($status == 0)
        return true;

    return false;
}

?>
