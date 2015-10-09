#!/usr/bin/php
<?php
//include("include/session.php");
require_once '/opt/eve/ale/factory.php';
include("/home/sites/www.blueprinthaus.org/account/include/database.php");
global $database;
function apicheck($key_uid,$api_key,$charid)
{
	global $database;
	$incorp = 0;
	$director = 0;
	$dalt = 0;
	$secure = 0;
	
	try {
		$ale = AleFactory::getEVEOnline();
		//set user credentials, third parameter $characterID is also possible;
				
		$ale->setKey($key_uid, $api_key,$charid);
		//all errors are handled by exceptions

		//  Get ignore err value 
		//$sql1 = "SELECT ignore_err,userlevel,email,forum_name FROM users where id='$users_id'";
		//$result1=$database->query($sql1);//mysql_query($sql1,$con) or die(mysql_error());
		//$row1=mysql_fetch_array($result1);
		//$ignore_err=$row1['ignore_err'];
		//$userlvl = $row1['userlevel'];
		//$email= $row1['email'];
		//$fname= $row1['forum_name'];
		

		$characters=$ale->account->APIKeyInfo();
		$attr = $characters->result->key->attributes();
		$cs_xml = $ale->char->CharacterSheet();
		$titles = array();
		foreach ($cs_xml->result->corporationTitles as $title)
		{
			array_push($titles,$title->titleName);
			//var_dump($title->titleName);
		}
		
		if(($key = array_search('First Draft', $titles)) !== false) {
    		unset($titles[$key]);
		}
		if(($key = array_search('Blueprint', $titles)) !== false) {
    		unset($titles[$key]);
		}
		if(($key = array_search('Prototype', $titles)) !== false) {
    		unset($titles[$key]);
		}
		if(($key = array_search('Trader', $titles)) !== false) {
    		unset($titles[$key]);
		}
		if(($key = array_search('Recruiter', $titles)) !== false) {
    		unset($titles[$key]);
		}
		if(($key = array_search('Comms Officer', $titles)) !== false) {
    		unset($titles[$key]);
		}
		if(($key = array_search('-2', $titles)) !== false) {
    		unset($titles[$key]);
		}
		//if(($key = array_search('555', $titles)) !== false) {
    	//	unset($titles[$key]);
		//}
		//var_dump($titles);
		//$values = array(0,1);
		//$result = array();
		//for ($i = 1; $i <= 30; $i++) {
	//		$val = $values[$i]*2;
//			array_push($values,$val);
//		}

		$accessMask = (int)$attr['accessMask'];
		$result['access'] = array(
					'Wallet Transactions' 	 => ($accessMask & 4194304)  > 0,
					'Wallet Journal' 		 => ($accessMask & 2097152) > 0,
					'Market Orders' 			 => ($accessMask & 4096) > 0,
					'Account Balance' 		 => ($accessMask & 1) > 0,
					'Notification Texts' 	 => ($accessMask & 32768) > 0,
					'Notifications' 		 => ($accessMask & 16384) > 0,
					'Mail Messages' 			 => ($accessMask & 2048) > 0,
					'Mailing Lists' 			 => ($accessMask & 1024) > 0,
					'Mail Bodies' 			 => ($accessMask & 512) > 0,
					'Contact Notifications' 	 => ($accessMask & 32) > 0,
					'Contact List' 			 => ($accessMask & 16) > 0,
					'Contracts' 			 => ($accessMask & 67108864) > 0,
					'Account Status' 		 => ($accessMask & 33554432) > 0,
					'Character Info' 		 => ($accessMask & 16777216) > 0,
					'Upcoming Calendar Events' => ($accessMask & 1048576) > 0,
					'Skill Queue' 			 => ($accessMask & 262144) > 0,
					'Skill In Training' 		 => ($accessMask & 131072) > 0,
					'Character Sheet' 		 => ($accessMask & 8) > 0,
					'Calendar Event Attendees' => ($accessMask & 4) > 0,
					'Asset List' 			 => ($accessMask & 2) > 0,
					'Character Info' 		 => ($accessMask & 8388608) > 0,
					'Standings'				 => ($accessMask & 524288) > 0,
					'Medals' 				 => ($accessMask & 8192) > 0,
					'KillLog' 				 => ($accessMask & 256) > 0,
					'Fac War Stats' 			 => ($accessMask & 64) > 0,
					'Research' 				 => ($accessMask & 65536) > 0,
					'Industry Jobs' 			 => ($accessMask & 128) > 0
				);
		
		//var_dump($titles);
		$tmp =$result['access']['Asset List'];
		//var_dump($tmp); 
		if (!$result['access']['Asset List'] && !empty($titles))
		{
			return true;
		}
		else
			return false;
	}
	catch (Exception $e) {
		var_dump($e);
	}
}

$sql = "SELECT a.users_id as users_id,a.key_uid as key_uid,a.api_key as api_key, t.toon_id as toon_id, u.forum_name as fname, t.toon_name as tname FROM api_keys as a,users as u,toons as t where a.users_id = u.id and a.key_uid=t.key_uid and t.corp='Blueprint Haus' ORDER BY `a`.`users_id` ASC"; 
//$sql = "SELECT a.users_id as users_id,a.key_uid as key_uid,a.api_key as api_key, t.toon_id as toon_id, u.forum_name as fname, t.toon_name as tname FROM api_keys as a,users as u,toons as t where a.users_id = u.id and a.key_uid=t.key_uid ORDER BY `a`.`users_id` ASC"; 

$result=$database->query($sql);//mysql_query($sql,$con);

while ($row=mysql_fetch_array($result)){
	$users_id=$row['users_id'];
	$key_uid=$row['key_uid'];
	$api_key=$row['api_key'];
	$fname=$row['fname'];
	$tname=$row['tname'];
	$toon_id=$row['toon_id'];

	$cak = apicheck($key_uid,$api_key,$toon_id);
	//$cak = apicheck(3146,'pqBE4RIfBnEsHpwUfKCy9MVH6oPXmQhIbyOgO6PdukPAvy8nyABtaxWXDksAsrDq',1573225853);
	if ($cak)
	{
		echo "$fname : $tname\n";
	}
}