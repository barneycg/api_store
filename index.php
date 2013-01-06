<?
/**
 * Main.php
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
?>

<html>
<title>Blueprint Haus Account Management</title>
<body>
<!-- <a href="javascript:(function(){var%20aff%20=%20'getoffmylawal-21';%20if%20(!document.getElementById('ASIN'))%20{alert('Can\'t%20find%20the%20product%20ID');%20return;}%20location%20=%20'http://www.amazon.co.uk/dp/'%20+%20document.getElementById('ASIN').value%20+%20'/?tag='%20+%20aff;})()" onclick="return false;" >TEST</a> -->
<table>
<tr><td>


<?
/**
 * User has already logged in, so display relavent links, including
 * a link to the admin center if the user is an administrator.
 */
if($session->logged_in){
   echo "<h1>Logged In</h1>";
   echo "Welcome <b>$session->username</b>, you are logged in. <br><br>"
       ."[<a href=\"userinfo.php?user=$session->username\">My Account</a>] &nbsp;&nbsp;"
       ."[<a href=\"useredit.php\">Edit Account</a>] &nbsp;&nbsp;"
       ."[<a href=\"apilist.php\">Api's</a>] &nbsp;&nbsp;";
       
   if($session->isBPH()){	
	   echo "[<a href=\"bph_toons.php\">BPH Toon's</a>] &nbsp;&nbsp;";
   }
   if($session->isAdmin()){
      echo "[<a href=\"admin/admin.php\">Admin Center</a>] &nbsp;&nbsp;";
   }
   if($session->isRecruiter()){
      echo "[<a href=\"admin/recruiter.php\">Recruiter Center</a>] &nbsp;&nbsp;";
   }
   echo "[<a href=\"process.php\">Logout</a>]";
}
else{
?>

<h1>Login</h1>
<?
/**
 * User not logged in, display the login form.
 * If user has already tried to login, but errors were
 * found, display the total number of errors.
 * If errors occurred, they will be displayed.
 */
if($form->num_errors > 0){
   echo "<font size=\"2\" color=\"#ff0000\">".$form->num_errors." error(s) found</font>";
}
?>
<form action="process.php" method="POST">
<table align="left" border="0" cellspacing="0" cellpadding="3">
<tr><td>Username:</td><td><input type="text" name="user" maxlength="30" value="<? echo $form->value("user"); ?>"></td><td><? echo $form->error("user"); ?></td></tr>
<tr><td>Password:</td><td><input type="password" name="pass" maxlength="30" value="<? echo $form->value("pass"); ?>"></td><td><? echo $form->error("pass"); ?></td></tr>
<tr><td colspan="2" align="left"><input type="checkbox" name="remember" <? if($form->value("remember") != ""){ echo "checked"; } ?>>
<font size="2">Remember me next time &nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="sublogin" value="1">
<input type="submit" value="Login"></td></tr>
<tr><td colspan="2" align="left"><br><font size="2">[<a href="forgotpass.php">Forgot Password?</a>]</font></td><td align="right"></td></tr>
<tr><td colspan="2" align="left"><br>Not registered? <a href="register.php">Sign-Up!</a></td></tr>
<tr><td colspan="2" align="left"><br><a href="orderpizza.html">Order Pizza for the Directors</a></td></tr>
<tr><span id="wtb-ew-v1" style="width: 369px; display:inline-block"><script src="https://www.blueprinthaus.org/account/clock_widget.js?h=0&cn=Eve+Time&bc=8BA1BB&wt=c2"></script><i><a target="_blank" href="http://www.worldtimebuddy.com/">Convert time zones</a> with worldtimebuddy.com</i><noscript><a href="http://www.worldtimebuddy.com/">Convert time zones</a> with worldtimebuddy.com</noscript><script>window[wtb_event_widgets.pop()].init()</script></span></tr>
</table>
</form>

<?
}
?>


</td></tr>
</table>


</body>
</html>
