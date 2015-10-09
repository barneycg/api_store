#!/usr/bin/php
<?php
require_once '/opt/eve/ale/factory.php';
include("/home/sites/www.blueprinthaus.org/account/include/database.php");
global $database;

$config = parse_ini_file("/home/sites/www.blueprinthaus.org/account/settings.ini", true);

function get_api_chars() {
	global $config;
	
	try {
		$ale = AleFactory::getEVEOnline();
        //set user credentials, third parameter $characterID is also possible;
        $key_uid=$config['eve_api_key']['keyid'];
        $api_key=$config['eve_api_key']['vcode'];
        $characterID=$config['eve_api_key']['charid'];
		$extended = 1;


        $ale->setKey($key_uid, $api_key, $characterID);
		$params['extended']=1;

		//all errors are handled by exceptions

		$member_trac = $ale->corp->MemberTracking($params);
		//var_dump($member_trac);

		//Get LLO dates

		foreach ($member_trac->result->members as $membert)
		{
			$name = $membert->name;
			$chars["$name"]['llo']=new DateTime($membert->logonDateTime);
			$chars["$name"]['joined']=new DateTime($membert->startDateTime);
			//$chars["$name"]['llo']->setTime(0,0,0);
		}
		
		return $chars;
	}
	//and finally, we should handle exceptions
	catch (Exception $e) {
		echo $e->getMessage();
	}
}

$message ='';
$now = new DateTime("now");
//$now->setTime(0,0,0);

//$w = get_wiki_chars();
$a = get_api_chars();

$alts = $a;
//$alts=id_alts($w,$a);

//uksort($alts,'strnatcasecmp');
$keys = array_keys($alts);

$message="";

//echo "\n=====================================\n";

foreach ($keys as $key)
{
	$alt_names="";
	$ignore=0;
	$interval = $now->diff($alts["$key"]['llo']);
	$delta = $interval->format('%R');
	$llo = $alts["$key"]['llo']->format('Y-m-d H:i:s');
	$joined = $a["$key"]['joined']->format('Y-m-d H:i:s');
	
	if ($alts["$key"]['llo'])
	{
		$sql="update toons set llo='$llo' where toon_name=\"$key\"";
    	$result=$database->query($sql);//mysql_query($sql,$con) or die(mysql_error());
	}
	
	if ($a["$key"]['joined'])
	{
		$sql="update toons set joined='$joined' where toon_name=\"$key\"";
   		$result=$database->query($sql);//mysql_query($sql,$con) or die(mysql_error());
	}

	if ($delta == '-')
	{
		//var_dump($key);
	}	
}


//mysql_close($con);
//$to = 'barney@telinformix.com';
//$subject = '[BPH] Prototype2';
//$body = $message;
//$headers = 'From: Shin@telinformix.com';
//mail($to, $subject, $body,$headers);



?>
