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
include("include/session.php");
//include("conn.php");
global $database;
?>

<html>
<title>BPH User Info</title>
<body>

<?
/* Requested Username error checking */
$req_user = trim($_GET['user']);
if(!$req_user || strlen($req_user) == 0 ||
   !eregi("^([0-9a-z])+$", $req_user) ||
   !$database->usernameTaken($req_user)){
   die("Username not registered");
}

/* Logged in user viewing own account */
if((strcmp($session->username,$req_user) == 0)||(strcmp($session->username,'admin') == 0)){
   echo "<h1>My Account</h1>";

/* Display requested user information */
$req_user_info = $database->getUserInfo($req_user);

/* Username */
echo "<b>Username: ".$req_user_info['username']."</b><br>";

/* Email */
echo "<b>Email:</b> ".$req_user_info['email']."<br>";

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

echo "<hr><u><h3>BPH Characters</h3></u>";
$users_id=$req_user_info['id'];
$sql="SELECT t.toon_name,t.corp,t.key_uid,t.toon_id,a.api_key FROM toons as t,api_keys as a WHERE t.users_id=$users_id and a.key_uid=t.key_uid and t.corp ='Blueprint Haus'";
//$sql="SELECT toon_name FROM toons WHERE users_id=$users_id and corp = 'Blueprint Haus'";
$result=$database->query($sql);//mysql_query($sql,$con) or die(mysql_error());
while ($row=mysql_fetch_array($result)){
//echo $row['toon_name']. "<br>";

$tname = $row['toon_name'];
$tname=$row['toon_name'];
//$corp=$row['corp'];
$charid=$row['toon_id'];
$vcode=$row['api_key'];
$keyid=$row['key_uid'];

echo "<a target=_blank href='https://www.blueprinthaus.org/jackknife/index.php?usid=".$keyid."&apik=".$vcode."&chid=". $charid . "'>$tname</a><br>";

}

/* If logged in user viewing own account, give link to edit */
   echo "<hr><a href=\"useredit.php\">Edit Account Information</a><br>";

/* Link back to main */
echo "<br>Back To [<a href=\"index.php\">Main</a>]<br>";
}
else
{
echo "<h1>You are not authorised to view this page</h1>";
}
?>

</body>
</html>
