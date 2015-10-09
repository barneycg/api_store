<?php

include("check.php");
require_once '/opt/eve/ale/factory.php';

	$key_uid=$_POST["key_uid"]; 
	$api_key=$_POST["api_key"];
	apicheck($key_uid,$api_key);
?>
