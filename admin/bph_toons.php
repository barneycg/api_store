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
	$sql="select forum_name,toon_name,corp,key_uid,director,dalt,secure from users,toons where toons.users_id = users.id and corp='Blueprint Haus' order by forum_name,toon_name DESC";
	$result=$database->query($sql);
	$mlist = array();
	$max_alts=0;
	while($row=mysql_fetch_array($result)) {
		$fname = $row['forum_name'];
        $tname = $row['toon_name'];
		$cname = $row['corp'];
		$key_uid = $row['key_uid'];
		$director = $row['director'];
		$secure = $row['secure'];
		$dalt = $row['dalt'];
		$sql1 = "select full_api from api_keys where key_uid = $key_uid";
		$result1=$database->query($sql1);
		$row1=mysql_fetch_array($result1);
		$full = $row1['full_api'];
        if (!array_key_exists($fname, $mlist))
        {
            $mlist[$fname] = array();
        }
		$tinfo=array("$tname","$cname","$director","$dalt","$secure","$full");

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
	$members = array_keys($mlist);
	foreach ($members as $member) {
		$message .= "<tr><td header=\"fname\" valign=\"top\">$member</td>";
		//$line = $member." : ";
		//natcasesort($mlist[$member]);
		foreach ($mlist[$member] as $toon) {
			//$message .= "<td header=\"tname\">$toon[0]</td>";
			if ($toon[5])
				$full_api = "Full API";
			else
				$full_api = "";
			if ($toon[2])
				$message .= "<td header=\"tname\" valign=\"top\">$toon[0]<br><span style=\"color:red\">Director</span><br><span style=\"color:green\">$full_api</span></td>";
			elseif ($toon[3])
				$message .= "<td header=\"tname\" valign=\"top\">$toon[0]<br><span style=\"color:red\">Director Alt</span><br><span style=\"color:green\">$full_api</span></td>";
			elseif ($toon[4])
				$message .= "<td header=\"tname\" valign=\"top\">$toon[0]<br><span style=\"color:red\">Secure Access</span><br><span style=\"color:green\">$full_api</span></td>";
			else
				$message .= "<td header=\"tname\" valign=\"top\">$toon[0]<br><span style=\"color:green\">$full_api</span></td>";
		}
		$message .= "</tr>\n";
	}
	$message .= "</table></body></html>";
	echo $message;
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
Back to [<a href="recruiter.php">Recruiter Admin</a>]<br><br>
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

