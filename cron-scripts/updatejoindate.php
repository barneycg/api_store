#!/usr/bin/php
<?php
require_once '/opt/eve/ale/factory.php';

$db_name="bph";// Database name 
$con = mysql_connect("localhost","root","FcMD7fA4");
if (!$con)
{
  die('Could not connect: ' . mysql_error());
}

$db=mysql_select_db($db_name,$con) or die("I Couldn't select your database");


function get_api_chars() {
	try {
		$ale = AleFactory::getEVEOnline();
		//set user credentials, third parameter $characterID is also possible;
		$key_uid=1926;
		$api_key='lOd36ABXOwaSPt48r2EMXOIHZ1apQyDn0yUowBvwQFpeu4xNX5oQbej8JI9MgqlI';
		$characterID=1073671514;
	
		$extended = 1;
        $params['extended']=1;
	
		$ale->setKey($key_uid, $api_key, $characterID);
		//all errors are handled by exceptions

		$member_trac = $ale->corp->MemberTracking($params);
		//var_dump($member_trac);
		//Get Join dates

		foreach ($member_trac->result->members as $membert)
		{
			$member_attr = $membert->attributes();
			$name = strtolower($member_attr["name"]);
			$logonDateTime = $member_attr["startDateTime"];

			$chars["$name"]['joined']=new DateTime($membert->startDateTime);
			$chars["$name"]['joined']->setTime(0,0,0);
		}
		//var_dump($chars);
		return $chars;
	}
	//and finally, we should handle exceptions
	catch (Exception $e) {
		echo $e->getMessage();
	}
}

$a = get_api_chars();

$keys = array_keys($a);

foreach ($keys as $key)
{
	$joined = $a["$key"]['joined']->format('Y-m-d H:i:s');
	if ($a["$key"]['joined'])
	{
		$sql="update toons set joined='$joined' where toon_name=\"$key\"";
   		$result=mysql_query($sql,$con) or die(mysql_error());
	}
}

?>
