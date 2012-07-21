<?
/**
 * UserInfo.php
 *
 * This page is for users to view their account information
 * with a link added for them to edit the information.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 26, 2004
 */
include("../include/session.php");
//include("../include/database.php");
//conn.php");

global $database;
?>

<html>
<title>BPH User Info</title>
<body>

<?
/* Requested Username error checking */
$req_user = trim($_GET['user']);
if(!$req_user || strlen($req_user) == 0 ||
   !eregi("^([0-9a-z_])+$", $req_user) ||
   !$database->usernameTaken($req_user)){
   die("Username not registered");
}

/* Logged in user viewing own account */
if((strcmp($session->username,$req_user) == 0)||(strcmp($session->userlevel,'5') == 0)||(strcmp($session->userlevel,'9') == 0)){
   echo "<h1>Member Toons</h1>";

/* Display requested user information */
$req_user_info = $database->getUserInfo($req_user);

/* Username */
echo "<b>Username: ".$req_user_info['username']."</b><br>";

/* Forum Name */

echo "<b>Forum Name:</b> ".$req_user_info['forum_name']."<br>";

/**
 * Note: when you add your own fields to the users table
 * to hold more information, like homepage, location, etc.
 * they can be easily accessed by the user info array.
 *
 * $session->user_info['location']; (for logged in users)
 *
 * ..and for this page,
 *
 * $req_user_info['location']; (for any user)
 */

echo "<hr><u><h3>Toons</h3></u>";
$users_id=$req_user_info['id'];
$sql="SELECT t.toon_name,t.corp,t.key_uid,t.toon_id,a.api_key FROM toons as t,api_keys as a WHERE t.users_id=$users_id and a.key_uid=t.key_uid ";
$result=mysql_query($sql,$database->connection) or die(mysql_error());
?>
<table border="1" cellpadding="3">
<tr><th>Toon Name</th><th>Corp</th>
<?
while ($row=mysql_fetch_array($result)){
	$tname=$row['toon_name'];
	$corp=$row['corp'];
	$charid=$row['toon_id'];
	$vcode=$row['api_key'];
	$keyid=$row['key_uid'];
	
	echo "<tr><td><a target=_blank href='https://gate.eveonline.com/Profile/$tname'>$tname</a></td><td>$corp</td><td><a target=_blank href='https://www.blueprinthaus.org/jackknife/index.php?usid=".$keyid."&apik=".$vcode."&chid=". $charid . "'>skills</a></td></tr>";
}
?>
</table>
<?

//mysql_close($con);

/* Link back to main */
echo "<br>Back To [<a href=\"recruiter.php\">Recruiter Admin</a>]<br>";
}
else
{
echo "<h1>You are not authorised to view this page</h1>";
}
?>

</body>
</html>
