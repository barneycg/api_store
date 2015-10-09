<?php 
include("/home/sites/www.blueprinthaus.org/account/include/database.php");
global $database;
$recsno=$_GET["recsno"]; 
$data=trim($recsno); 
$ex=explode(" ",$data); 
$size=sizeof($ex); 
for($i=0;$i<$size;$i++) { 
    $key_uid=trim($ex[$i]); 
    $sql1="SELECT users.id as id,users.forum_name as forum_name,users.email as email FROM users,api_keys WHERE api_keys.key_uid='$key_uid' AND users.id=api_keys.users_id";
    $result1=$database->query($sql1);//mysql_query($sql1,$con) or die(mysql_error());
    $row1=mysql_fetch_array($result1);
    $fname=$row1['forum_name'];
	$users_id=$row1['id'];
	$email=$row1['email'];
    $sql="delete from api_keys where key_uid='$key_uid'"; 
    $result=$database->query($sql);//mysql_query($sql,$con) or die(mysql_error()); 
	$sql2="delete from toons where key_uid='$key_uid'";
    $result2=$database->query($sql2);//mysql_query($sql2,$con) or die(mysql_error());
    $sql13= "SELECT count(toon_name) as count FROM toons WHERE toons.users_id='$users_id' and toons.corp = 'Blueprint Haus'";
	$result13=$database->query($sql13);//mysql_query($sql13,$con) or die(mysql_error());
	$row13=mysql_fetch_array($result13);
	$count=$row13['count'];
	$sql14 = "UPDATE users SET toon_count=$count where id='$users_id'";
	$result14 = $database->query($sql14);//mysql_query($sql14,$con) or die(mysql_error());
	if ($count==0)
	{
		$sql3 = "UPDATE users SET userlevel=2 WHERE id='$users_id'";
		$result3=$database->query($sql13);//mysql_query($sql3,$con) or die(mysql_error());
		exec("/home/sites/www.blueprinthaus.org/account/del-wiki.pl $email");
		$message = $users_id . " : " . $fname . " has no toons in corp\n";
		$to = 'barney@telinformix.com';
		$subject = '[BPH] No Active Toons';
		$body = $message;
		$headers = 'From: Shin@telinformix.com';
		//mail($to, $subject, $body,$headers);
	}
	
} 
//mysql_close($con);
header("location: apilist.php"); 
?>

