<?php

$upn = $_REQUEST['username'].'@amiu0.amiu.genova.it';
$attributes = ['displayname'];
$filter = "(&(objectClass=user)(objectCategory=person)(userPrincipalName=".ldap_escape($upn, null, LDAP_ESCAPE_FILTER)."))";
$baseDn = "DC=abc,DC=xyz";
$results = ldap_search($ldap, $baseDn, $filter, $attributes);
$info = ldap_get_entries($ldap, $results);

// This is what you're looking for...
var_dump($info[0]['displayname'][0]);


?>
