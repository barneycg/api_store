<?
/**
 * apilist.php
 *
 * This is an example of the main page of a website. Here
 * users will be able to login. However, like on most sites
 * the login form doesn't just have to be on the main page,
 * but re-appear on subsequent pages, depending on whether
 * the user has logged in or not.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 26, 2004
 */
include("include/session.php");
global $database;
?>

<html>
<head>
<title>Blueprint Haus API Management</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> 
<script type="text/javascript" src="script.js"> </script> 
<style type="text/css"> 
<!-- 
.style1 {color: #FFFFFF} 
--> 
</style> 
</head> 


<body>


<?
/**
 * User has already logged in, so display relavent links, including
 * a link to the admin center if the user is an administrator.
 */
if($session->logged_in){
?>



<table width="775" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"> 
  <tr> 
    <td><hr size="1" noshade></td> 
  </tr> 
  <tr> 
    <td> 
      <form action="apiform.php" method="post"> 
        <table width="600" border="1" align="center" cellpadding="2" cellspacing="2"> 
          <tr> 
            <td><input name="topcheckbox" type="checkbox" class="check" id="topcheckbox" onClick="selectall();" value="ON"> 
Select All    </td> 
          </tr> 
          <tr> 
            <td><strong></strong></td> 
            <td><strong>ID</strong></td> 
            <td><strong>Verification Code</strong></td> 
            <td><strong>Status</strong></td>
            <td><strong>Update</strong></td> 
          </tr> 

<?

//include("conn.php");
$sql = "SELECT users_id,key_uid,api_key,valid FROM api_keys WHERE users_id = '$session->usrid'";
$result=$database->query($sql);//mysql_query($sql,$con);

//$dbarray = mysql_fetch_array($result);
while($row=mysql_fetch_array($result)) { 
?> 
          <tr> 
            <td><input name="<? echo $row['key_uid']; ?>" type="checkbox" class="check"></td> 
            <td><? echo $row['key_uid']; ?></td> 
            <td><? echo $row['api_key']; ?></td>
            <td><? echo $row['valid']; ?></td>
            <td><a href="<? echo "apiform.php?key_uid=".$row['key_uid']."&mode=update"; ?>">Update</a></td> 
          </tr> 
          <? } ?> 

        </table> 

		<button type="button" value="Delete" onclick="goDel();">Delete</button>
		<div align=right><input type="submit" name="mode" value="Add"></div>

    </form></td> 
  </tr> 
</table> 
<br>Back To [<a href="index.php">Main</a>]<br>
</body> 
</html>
<? } ?>
