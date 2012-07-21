#!/usr/bin/php
<?php
include("/home/sites/www.blueprinthaus.org/account/include/database.php");

global $database;

$message ='';
//$now = new DateTime("now");
//$now->setTime(0,0,0);

$sql="select forum_name, max(llo) as llo, min(Joined) from users,toons where toons.users_id=users.id group by users.id order by llo";
$result=$database->query($sql);//mysql_query($sql,$con) or die(mysql_error());
$llo='';
while($row=mysql_fetch_array($result)) {
		$full = new DateTime($row['llo']);
		$day = $full->format('Y-m-d');
		$hour = $full->format('H:i:s');
		$llo .= $day.",".$hour."\n";
}

//$to = 'barney@telinformix.com';
//$subject = '[BPH] Prototype2';
//$body = $message;
//$headers = 'From: Shin@telinformix.com';
//mail($to, $subject, $body,$headers);
$f = fopen("/home/sites/www.blueprinthaus.org/llo.csv", "a"); 
fwrite($f, $llo); 
fclose($f);
//echo $llo;
?>
