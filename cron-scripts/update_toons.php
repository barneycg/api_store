#!/usr/bin/php
<?php
include("/home/sites/www.blueprinthaus.org/account/apicheck.php");
//include("/home/sites/www.blueprinthaus.org/account/conn.php");
include("/home/sites/www.blueprinthaus.org/account/include/database.php");
require_once '/opt/eve/ale.new/factory.php';

global $database;

$sql = "SELECT a.users_id as users_id,a.key_uid as key_uid,a.api_key as api_key, u.ignore_err as ignore_err, u.wiki as wiki,u.forum_name as fname,u.userlevel as userlevel FROM api_keys as a,users as u where a.users_id = u.id and (a.valid = 'outcorp' or a.valid = 'incorp') and (u.userlevel=5 or u.userlevel=4 or u.userlevel=3 or u.userlevel=2 or u.userlevel=1)";
$result=$database->query($sql);//mysql_query($sql,$con);
$left=array();
$message='';
while ($row=mysql_fetch_array($result)){
	$users_id=$row['users_id'];
	$key_uid=$row['key_uid'];
	$api_key=$row['api_key'];
	$fname=$row['fname'];
	$ignore_err=$row['ignore_err'];
	$userlvl=$row['userlevel'];
	$checked = apicheck($users_id,$key_uid,$api_key);
	if ($checked==0)
	{
		if (!array_key_exists($fname, $left)) {
			$left[$fname]=array();
			$left[$fname]['userlvl']=$userlvl;
		}
		array_push($left[$fname],0);
	}
	if ($checked==1)
	{
		if (!array_key_exists($fname, $left)) {
			$left[$fname]=array();
			$left[$fname]['userlvl']=$userlvl;
		}
		array_push($left[$fname],1);
	}
}

foreach (array_keys($left) as $member){
	//var_dump($left);
	$userlvl=$left[$member]['userlvl'];
	if (!in_array(1,$left[$member]))
	{
		if (($userlvl == 4)||($userlvl==5)||($userlvl==3))
		{
			//echo $member ."\n";
			$sql1 = "update users set userlevel=3 where forum_name = '$member'";
			$result1=$database->query($sql1);//mysql_query($sql1,$con);
			//$message .= $member."\n";
		}
	}
}

$sql2 = "SELECT u.forum_name as fname FROM users as u where u.userlevel=3 AND ignore_err=0";
$result2=$database->query($sql2);//mysql_query($sql,$con);
while ($row2=mysql_fetch_array($result2))
{
	$message .= $row2['fname']."\n";
}
//var_dump($left);

$to = 'barney@telinformix.com';
$subject = '[BPH] Remove forum access';
$body = $message;
$headers = 'From: Shin@telinformix.com';
mail($to, $subject, $body,$headers);
//mysql_close($con);
?>
