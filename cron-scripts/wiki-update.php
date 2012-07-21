#!/usr/bin/php
<?php

include("/home/sites/www.blueprinthaus.org/account/include/database.php");
global $database;
$sql98 = "SELECT id,email,forum_name FROM users where toon_count=0 and wiki=1";
$result98 = $database->query($sql98);//mysql_query($sql98,$con) or die(mysql_error());

while ($row98=mysql_fetch_array($result98)){
	$email=$row98['email'];
	$user_id=$row98['id'];
	$fname=$row98['forum_name'];
	echo "Deleting : $fname\n";
	exec("/home/sites/www.blueprinthaus.org/account/del-wiki.pl $email");
	$sql97 = "update users set wiki=0 where id=$user_id";
	$result97 = $database->query($sql97);//mysql_query($sql97,$con) or die(mysql_error());	
}

$sql99 = "SELECT id,email,forum_name FROM users where (toon_count>0 and wiki!=1) or (userlevel=4 and wiki!=1)";
$result99 = $database->query($sql99);//mysql_query($sql99,$con) or die(mysql_error());

while ($row99=mysql_fetch_array($result99)){
	$email=$row99['email'];
	$user_id=$row99['id'];
	$fname=$row99['forum_name'];
	echo "Adding : $fname\n";
	exec("/home/sites/www.blueprinthaus.org/account/add-wiki.pl $email");
    $sql15 = "UPDATE users SET wiki=1 WHERE id='$user_id'";
    $result15=$database->query($sql15);//mysql_query($sql15,$con) or die(mysql_error());

}
?>
