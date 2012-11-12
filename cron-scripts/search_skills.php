#!/usr/bin/php
<?php
include("/home/sites/www.blueprinthaus.org/account/apicheck.php");
//include("/home/sites/www.blueprinthaus.org/account/conn.php");
include("/home/sites/www.blueprinthaus.org/account/include/database.php");
require_once '/opt/eve/ale.new/factory.php';

global $database;

$sskill = $argv[1];
$slevel = $argv[2];

$sql = "SELECT a.users_id as users_id,a.key_uid as key_uid,a.api_key as api_key, t.toon_id as toon_id, u.forum_name as fname, t.toon_name as tname FROM api_keys as a,users as u,toons as t where a.users_id = u.id and a.key_uid=t.key_uid and t.corp='Blueprint Haus' ORDER BY `a`.`users_id` ASC"; 
$result=$database->query($sql);//mysql_query($sql,$con);
$left=array();
$message='';
while ($row=mysql_fetch_array($result)){
	$users_id=$row['users_id'];
	$key_uid=$row['key_uid'];
	$api_key=$row['api_key'];
	$fname=$row['fname'];
	$tname=$row['tname'];
	$toon_id=$row['toon_id'];
	try {
        $ale = AleFactory::getEVEOnline();
        //set user credentials, third parameter $characterID is also possible;

        $ale->setCredentials($key_uid, $api_key, $toon_id);
		$skillsheet=$ale->char->CharacterSheet();

		foreach ($skillsheet->result->skills as $skill)
		{
			$typeID = $skill->typeID;
			$level = $skill->level;

			$sql2 = "SELECT typeName from invTypes where typeID='$typeID'";
			$result2=$database->query($sql2);
			while ($row2=mysql_fetch_array($result2)){
				$typeName=$row2['typeName'];
			}
			if (($level >= $slevel) && ($typeName === $sskill))
			{
				echo $fname . "\n";
			}
		}
	}
	catch (Exception $e)
	{
	}
}

