<?php

include("include/session.php");
global $database;
//include("conn.php");
include("apicheck.php");
require_once '/opt/eve/ale.new/factory.php';

$mode=$_GET["mode"]; 
if($mode=="add") 
{ 
	$key_uid=$_POST["key_uid"]; 
	$api_key=$_POST["api_key"]; 
	$users_id=$_POST["users_id"];
	$sql="insert into api_keys(id,users_id,key_uid,api_key,valid) values(NULL,'$users_id','$key_uid','$api_key','new')"; 
    $result=$database->query($sql);//mysql_query($sql,$con) or die(mysql_error()); 
	apicheck($users_id,$key_uid,$api_key);
    //mysql_close($con);
    header("location: apilist.php");    
}
elseif($mode=="update") 
{ 
	$key_uid=$_POST["key_uid"]; 
    $api_key=$_POST["api_key"];
	$users_id=$_POST["users_id"];
    $id=$_POST["id"]; 
    $sql="update api_keys set key_uid='$key_uid',api_key='$api_key',valid='updated' where id='$id'"; 
    //echo $sql; 
    $result=$database->query($sql);//mysql_query($sql,$con) or die(mysql_error()); 
	apicheck($users_id,$key_uid,$api_key);
    //mysql_close($con);
    header("location: apilist.php");
} 
?>
