<?
/**
 * Recruiter.php
 *
 * This is the Admin Center page. Only administrators
 * are allowed to view this page. This page displays the
 * database table of users and banned users. Admins can
 * choose to delete specific users, delete inactive users,
 * ban users, update user levels, etc.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 26, 2004
 */
include("../include/session.php");

/**
 * displayUsers - Displays the users database table in
 * a nicely formatted html table.
 */
function displayUsers(){
   global $database;
   $q = "SELECT username,userlevel,email,forum_name,timestamp,reg_ip_address,last_ip_address,30plus,toon_count "
       ."FROM ".TBL_USERS." WHERE username != 'admin' ORDER BY userlevel DESC,username";
   $result = $database->query($q);
   $q2 = "SELECT count(*) as active_users from " . TBL_USERS . " WHERE toon_count>0";
   $result2 = $database->query($q2);
   $q3 = "SELECT count(*) as bph_toons from toons,users WHERE corp = 'Blueprint Haus' AND users.id=users_id and toon_count>0";
   $result3 = $database->query($q3);
   $q4 = "SELECT count(*) as non_bph_toons from toons,users WHERE corp != 'Blueprint Haus' AND users.id=users_id and toon_count>0";
   $result4 = $database->query($q4);
   $q5 = "SELECT count(*) as total_toons from toons,users WHERE users.id=users_id and toon_count>0";
   $result5 = $database->query($q5);
   
   $total_users = mysql_result($result2,0,"active_users");
   $total_bph = mysql_result($result3,0,"bph_toons");
   $total_non_bph = mysql_result($result4,0,"non_bph_toons");
   $total_toons = mysql_result($result5,0,"total_toons");
   
   
   /* Error occurred, return given name by default */
   $num_rows = mysql_numrows($result);
   if(!$result || ($num_rows < 0)){
      echo "Error displaying info";
      return;
   }
   if($num_rows == 0){
      echo "Database table empty";
      return;
   }
   
   echo "<table align=\"left\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";
   echo "<tr><td><b>Total number of members</b></td><td><b>Total BPH Toons</b></td><td><b>Total non-BPH Toons</b></td><td><b>Total Toons</b></td></tr>\n";
   echo "<tr><td>$total_users</td><td>$total_bph</td><td>$total_non_bph</td><td>$total_toons</td></tr></table>";
   
   echo "<table align=\"left\" border=\"0\" cellspacing=\"5\" cellpadding=\"5\">\n<tr><td><br></td></tr><tr><td><br></td></tr></table>";
   
   /* Display table contents */
   echo "<table align=\"left\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";
   echo "<tr><td><b>Username</b></td><td><b>Forum Name</b></td><td><b>Level<b></td><td><b>Toon Count (BPH)</b></td><td><b>30 Plus</b></td><td><b>Registering IP</b></td><td><b>Last IP</b></td></tr>\n";
   for($i=0; $i<$num_rows; $i++){
      $uname  = mysql_result($result,$i,"username");
      $forum_name = mysql_result($result,$i,"forum_name");
      switch (mysql_result($result,$i,"userlevel"))
      {
      	case 1:
      		$ulevel = 'New Account';
      		break;
      	case 2:
      		$ulevel = 'Former Member';
      		break;
      	case 3:
      		$ulevel = 'Remove From Forum';
      		break;
      	case 4:
      		$ulevel = 'Current Member';
      		break;
      	case 5:
      		$ulevel = 'Recruiter';
      		break;
		case 6:
			$ulevel = 'Dummy';
			break;
      	case 9:
      		$ulevel = 'Admin';
      		break;
      }
      //$ulevel = mysql_result($result,$i,"userlevel");
      $email  = mysql_result($result,$i,"email");
      $time   = mysql_result($result,$i,"timestamp");
      $lastip = mysql_result($result,$i,"last_ip_address");
      $regip = mysql_result($result,$i,"reg_ip_address");
      $toon_count = mysql_result($result,$i,"toon_count");
	  if (mysql_result($result,$i,"30plus")==1)
	  {
	  		$thirtyplus='<span style="align: centre;">&#10003;</span>';
	  }
	  else
	  {
	  		$thirtyplus='';
	  }
	  	
      echo "<tr><td><a href='https://www.blueprinthaus.org/account/admin/view_user.php?user=$uname'>$uname</a></td><td>$forum_name</td><td>$ulevel</td><td align=center >$toon_count</td><td align=center >$thirtyplus</td><td>$regip</td><td>$lastip</td></tr>\n";
   }
   echo "</table><br>\n";
}


/**
 * User not an administrator, redirect to main page
 * automatically.
 */
if(!$session->isRecruiter()){
   header("Location: ../index.php");
}
else{
/**
 * Administrator is viewing page, so display all
 * forms.
 */
?>
<html>
<title>BPH Recruiter Center</title>
<body>
<h1>Recruiter Center</h1>
<font size="5" color="#ff0000">
<b>::::::::::::::::::::::::::::::::::::::::::::</b></font>
<font size="4">Logged in as <b><? echo $session->username; ?></b></font><br><br>
Back to [<a href="../index.php">Main Page</a>]<br><br>
<?
if($form->num_errors > 0){
   echo "<font size=\"4\" color=\"#ff0000\">"
       ."!*** Error with request, please fix</font><br><br>";
}
?>
<table align="left" border="0" cellspacing="5" cellpadding="5">
<tr><td>
<?
/**
 * Display Users Table
 */
?>
<h3>BPH Members</h3>
<?
displayUsers();
?>
</td></tr>
<tr>
<td>
<br>
</form>
</td>
</tr>
</table>
</body>
</html>
<?
}
?>

