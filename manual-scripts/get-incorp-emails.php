#!/usr/bin/php
<?php

include("/home/sites/www.blueprinthaus.org/account/include/database.php");
global $database;



$sql97 = "SELECT id,username,email,forum_name,userlevel FROM users";
$result97 = mysql_query($sql97,$conn) or die(mysql_error());

while ($row97=mysql_fetch_array($result97)){
	$users_id=$row97['id'];
	$sql96 = "SELECT count(*) as count from toons,users WHERE toons.users_id=users.id and users.id=$users_id";
	$result96 = mysql_query($sql96,$conn) or die(mysql_error()); 
	$row96=mysql_fetch_array($result96);
	$count=$row96['count'];
	$sql95 = "UPDATE users SET toon_count=$count where id=$users_id";
	$result95 = mysql_query($sql95,$conn) or die(mysql_error());
}

$sql98 = "SELECT email FROM users WHERE toon_count>0 and poss_spy!=1 order by email";
$result98 = mysql_query($sql98,$conn) or die(mysql_error());

while ($row98=mysql_fetch_array($result98)){
	echo $row98['email']."\n";
}

mysql_close($conn);

?>
