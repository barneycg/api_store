#!/usr/bin/php
<?php
include("/home/sites/www.blueprinthaus.org/account/apicheck.php");
//include("/home/sites/www.blueprinthaus.org/account/conn.php");
include("/home/sites/www.blueprinthaus.org/account/include/database.php");
require_once '/opt/eve/ale/factory.php';

global $database;

$sql = "SELECT a.users_id as users_id,a.key_uid as key_uid,a.api_key as api_key, u.ignore_err as ignore_err, u.wiki as wiki FROM api_keys as a,users as u where a.users_id = u.id and (a.valid = 'new')";
$result=$database->query($sql);//mysql_query($sql,$con);

while ($row=mysql_fetch_array($result)){
	$users_id=$row['users_id'];
	$key_uid=$row['key_uid'];
	$api_key=$row['api_key'];
	apicheck($users_id,$key_uid,$api_key);
}

//mysql_close($con);
?>
