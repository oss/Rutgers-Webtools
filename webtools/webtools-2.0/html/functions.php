<?php

function getFullname($netid, $cluster=NULL) {
    global $CLUSTER;

    if (empty($cluster))
        $cluster = $CLUSTER;

    $dn = "dc=$cluster,dc=rutgers,dc=edu";
    $ds = ldap_connect("ldap://ldap.nbcs.rutgers.edu") or die("!");
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3) or die("Version 3 required");
    ldap_start_tls($ds) or die("Couldn't set encryption");
    $bind = ldap_bind($ds) or die ("Error in ldap_bind");
    $res = ldap_search($ds, $dn, "uid=$netid", array("cn")) or die ("Error in ldap_search.");
    $entries = ldap_get_entries($ds, $res) or die ("Boink");
    if (isset($entries[0]['cn'][0])) {
        $fullname = $entries[0]['cn'][0];
        if (strpos($fullname, '&') !== false)
            $fullname = str_replace('&', $netid, $fullname);

        return ucwords(strtolower($fullname));
    }
    return NULL;
}

function getHomeDir($netid, $cluster=NULL) {
    global $CLUSTER;

    if (empty($cluster))
        $cluster = $CLUSTER;

    $dn = "dc=$cluster,dc=rutgers,dc=edu";
    $ds = ldap_connect("ldap://ldap.nbcs.rutgers.edu") or die("!");
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3) or die("Version 3 required");
    ldap_start_tls($ds) or die("Couldn't set encryption");
    $bind = ldap_bind($ds) or die ("Error in ldap_bind");
    $res = ldap_search($ds, $dn, "uid=$netid", array("homedirectory")) or die ("Error in ldap_search.");
    $entries = ldap_get_entries($ds, $res) or die ("Boink");
    if (isset($entries[0]['homedirectory'][0]))
        return $entries[0]['homedirectory'][0];

    return NULL;
}

?>
