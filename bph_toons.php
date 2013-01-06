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
include("include/session.php");

/**
 * displayUsers - Displays the users database table in
 * a nicely formatted html table.
 */
function displayUsers(){
	global $database;
	$sql="select forum_name,toon_name,corp from users,toons where toons.users_id = users.id and corp='Blueprint Haus' order by forum_name";
	$result=$database->query($sql);
	$mlist = array();
	$max_alts=0;
	while($row=mysql_fetch_array($result)) {
		$fname = $row['forum_name'];
        $tname = $row['toon_name'];
		$cname = $row['corp'];
        if (!array_key_exists($fname, $mlist))
        {
            $mlist[$fname] = array();
        }
		$tinfo=array("$tname","$cname");

        array_push($mlist[$fname],$tinfo);
	}
	//var_dump($mlist);
	foreach (array_keys($mlist) as $maxtoons) {
		$count = count($mlist[$maxtoons]);
		if ($count > $max_alts) {
			$max_alts=$count;
		}
	}

	//$message = "<html><body><table border=1 frame=void cellpadding=10 rules=groups><COLGROUP></COLGROUP><th id=\"fname\">Forum Name</th><th colspan=$max_alts id=\"tname\">Toons</th><COLGROUP></COLGROUP>\n";
	$message = "<html><body><table style=\"border-collapse:collapse; font-size:14px; text-align:left;\" cellpadding=5 ><COLGROUP style=\"border-color:black;border-style: none solid none none\" ></COLGROUP><colgroup span=$max_alts></colgroup><tr style=\"border-color:black;border-style: none none solid none\"><th id=\"fname\">Forum Name</th><th  colspan=$max_alts id=\"tname\">Toons</th></tr>\n";

	foreach (array_keys($mlist) as $member) {
		$message .= "<tr><td header=\"fname\">$member</td>";
		//$line = $member." : ";
		natcasesort($mlist[$member]);
		foreach ($mlist[$member] as $toon) {
			$message .= "<td header=\"tname\">$toon[0]</td>";
		}
		$message .= "</tr>\n";
	}
	$message .= "</table></body></html>";
	echo $message;
}


/**
 * User not in BPH, redirect to main page
 * automatically.
 */
if(!$session->isBPH()){
   header("Location: index.php");
}
else{
/**
 * Administrator is viewing page, so display all
 * forms.
 */
?>
<html>
<title>BPH Memberlist</title>
<body>
<h2>BPH Memberlist</h2>
<font size="5" color="#ff0000">
<b>::::::::::::::::::::::::::::::::::::::::::::</b></font>
<font size="4">Logged in as <b><? echo $session->username; ?></b></font><br><br>
Back to [<a href="index.php">Main Page</a>]<br><br>
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
<!-- <h3>BPH Members</h3> -->
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

