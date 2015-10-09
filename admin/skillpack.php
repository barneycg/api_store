<?php
//include("/home/sites/www.blueprinthaus.org/account/include/database.php");
include("../include/session.php");

$hdr = '<html><head><style type="text/css">
 td {
 padding-right:10px;
 }
</style></head>';
$page = "";

if ($_POST["form_id"] == "1")
{
	$s=$_POST["skill"]; 
	$l=$_POST["level"];
	$m=$_POST["miss"];
	$skilllist[$s] = $l;
	//$hdr .= "<b>$s:$l</b><br>";
}
else if ($_POST["form_id"] == "2")
{
	$skills = explode("\n",trim($_POST["skills"]));
	foreach ($skills as $skill)
	{
		list($s,$l) = explode(":",$skill);
		//$hdr .= "<b>$s:$l</b><br>";
		$skilllist[$s] = $l;
	}
	$m=$_POST["miss"];
}
else if ($_POST["form_id"] == "3")
{
	$clones['under 20M'] = 0;
	$clones['Mu'] = 0;
	$clones['Xi'] = 0;
	$clones['Omicron'] = 0;
	$clones['Pi'] = 0;
	$clones['Rho'] = 0;
	$clones['Sigma'] = 0;
	$clones['Tau'] = 0;
	$clones['Upsilon'] = 0;
	$clones['Phi'] = 0;
	$clones['Chi'] = 0;
	$clones['Psi'] = 0;
	$clones['Omega'] = 0;			
	
	$sql_sp_q = "select level from character_skills as a where a.skill_id = 0 ";
    $r_sp_q =$database->query($sql_sp_q); 
	
	//$skillpoints = $smcFunc['db_query']('', "select level from {db_prefix}tea_character_skills as a where a.skill_id = 0");
	
	while ($sp_r = mysql_fetch_array($r_sp_q))
	{
		$sp = $sp_r['level'];
		
		switch ($sp)
		{
			case ($sp < 20000000):
				$clones['under 20M'] += 1;
				break;
			case (($sp >= 20000000) && ($sp < 25600000)):
				$clones['Mu'] += 1;
				break; 
			case (($sp >= 25600000) && ($sp < 32800000)):
				$clones['Xi'] += 1;
				break;
			case (($sp >= 32800000) && ($sp < 42200000)):
				$clones['Omicron'] += 1;
				break;
			case (($sp >= 42200000) && ($sp < 54600000)):
				$clones['Pi'] += 1;
				break;
			case (($sp >= 54600000) && ($sp < 71000000)):
				$clones['Rho'] += 1;
				break;
			case (($sp >= 71000000) && ($sp < 92500000)):
				$clones['Sigma'] += 1;
				break;
			case (($sp >= 92500000) && ($sp < 120000000)):
				$clones['Tau'] += 1;
				break;
			case (($sp >= 120000000) && ($sp < 156000000)):
				$clones['Upsilon'] += 1;
				break;
			case (($sp >= 156000000) && ($sp < 203000000)):	
				$clones['Phi'] += 1;
				break;
			case (($sp >= 203000000) && ($sp < 264000000)):	
				$clones['Chi'] += 1;
				break;
			case (($sp >= 264000000) && ($sp < 343000000)):	
				$clones['Psi'] += 1;
				break;
			case (($sp >= 343000000) && ($sp < 450000000)):	
				$clones['Omega'] += 1;
				break;
		}
	}
	echo $hdr;
	echo "\n<body><h3>Clone Spread</h4>";
	?>
	<font size="5" color="#ff0000">
	<b>::::::::::::::::::::::::::::::::::::::::::::</b></font>
	<font size="4">Logged in as <b><? echo $session->username; ?></b></font><br><br>
	Back to [<a href="/account/index.php">Main Page</a>] [<a href="/account/admin/skill_form.php">Skill Form</a>]<br><br>
	<?
	echo "<table><tr><th>Type</th><th>Count</th></tr>\n";
	foreach ($clones as $name => $cnt)
	{
		echo "<tr><td>".$name."</td><td>".$cnt."</td></tr>\n";
	}
	echo "</body></html>";
	exit;
}
/*
Clone Grade Mu	20.0 M SP	1.340 M ISK
Clone Grade Nu	25.6 M SP	1.980 M ISK
Clone Grade Xi	32.8 M SP	2.990 M ISK
Clone Grade Omicron	42.2 M SP	4.700 M ISK
Clone Grade Pi	54.6 M SP	7.800 M ISK
Clone Grade Rho	71.0 M SP	13.00 M ISK
Clone Grade Sigma	92.5 M SP	20.00 M ISK
Clone Grade Tau	12.00 M SP	30.00 M ISK
Clone Grade Upsilon	156.0 M SP	45.00 M ISK
Clone Grade Phi	203.0 M SP	65.00 M ISK
Clone Grade Chi	264.0 M SP	90.00 M ISK
Clone Grade Psi	343.0 M SP	120.00 M ISK
Clone Grade Omega	450.0 M SP	150.00 M ISK
*/

$page = '<table>';
$total_q = "select count(distinct charid) as count from character_skills";
$total_r = $database->query($total_q);
$total_r_r = mysql_fetch_array($total_r);
$total_c = $total_r_r['count'];

$keys_q = "select m.forum_name as fname, a.key_uid as key_uid, c.toon_id as toon_id, c.toon_name as tname from api_keys as a, toons as c, users as m where a.key_uid = c.key_uid and c.corp = 'Blueprint Haus' and m.id = a.users_id ORDER by m.forum_name,c.toon_name";
$keys_r = $database->query($keys_q);
//and c.name = 'Shin Chogan'
$count = 0;
$pcount = 0;
$last_rname = '';

// ***
// *** Get a list of valid keys and toons
// ***
//$connection = mysql_connect("127.0.0.1", "root", "FcMD7fA4") or die(mysql_error());
//mysql_select_db("bph", $connection) or die(mysql_error());
while ($key = mysql_fetch_array($keys_r))
{
	//var_dump($key);
	//die;
	$matched = 0;
	//$users_id=$key['users_id'];
	$key_uid=$key['key_uid'];
	$tname=$key['tname'];
	$toon_id=$key['toon_id'];
	$corp='[BPH]';
	$rname=$key['fname'];
	
	// ***
	// *** For each skill submitted compare against list of skills
	// ***
	$levels="";
	foreach ($skilllist as $sskill => $slevel)
	{
		$skill_found = 0;
		$typeID="";
		$level=0;

		$sql1 = "SELECT typeID from invTypes where typeName='$sskill'";
		$result1 = $database->query($sql1);
			//$result2=$database->query($sql2);
		while ($row1=mysql_fetch_array($result1)){
				$typeID=$row1['typeID'];
		}
		if (empty($typeID))
		{
			$error[$sskill] = 1;
			continue;
		}
		else
			$error[$sskill] = 0;
		// ***
		// *** Get skills for toon
		// ***
		$tid = '';
		$lev = '';
		$skills_q = "select skill_id,level from character_skills as a where a.userid = '" . $key_uid . "' and a.charid = '" .$toon_id."' and a.skill_id = '".$typeID."' ORDER by a.skill_id";
		$skills_r = $database->query($skills_q);
		while ($row = mysql_fetch_array($skills_r))
		{
			$tid = $row['skill_id'];
			$lev = $row['level'];
		}
		
		if (!empty($tid) && empty($m))
		{
			$skill_found = 1;
			
			// ***
			// *** if greater level
			// ***
			$level = (int)$lev;
			$slevel = (int)$slevel;
			
			if ($level >= $slevel)
			{
				//echo $typeName ." : " .$level."<br>";
				// *** and not already failed earlier
				if ($matched != 99)
				{
					// *** found
					if ($level > $slevel)
						$levels .= '<font color="blue">'.$level."</font>,";
					else
						$levels .= '<font color="green">'.$level."</font>,";
					$matched = 1;
					
				}
			}
			else
			{
				// ** not found
				$matched = 99;
				$levels .= $level.",";
			}
		}
		elseif (!empty($tid) && !empty($m))
		{
			$skill_found = 1;
			
			// ***
			// *** if greater level
			// ***
			$level = (int)$lev;
			$slevel = (int)$slevel;
			
			if ($level < $slevel)
			{
				//echo $typeName ." : " .$level."<br>";
				// *** and not already failed earlier
				if ($matched != 99)
				{
					// *** found
					$levels .= '<font color="red">'.$level."</font>,";
					$matched = 1;
				}
			}
			else
			{
				// ** not found
				$matched = 99;
				$levels .= $level.",";
			}
		}
			
			
			
		if (($skill_found == 0) && empty($m))
		{
			$matched = 99;
			$levels .= $level.",";
			//break 1;
		}
		elseif (($skill_found == 0) && !empty($m))
		{
			$matched = 1;
			$levels .= '<font color="red">'.$level."</font>,";
		}
	}
		
	
		

	if ($matched == 1)
	{
		//if ($_POST["form_id"]=="2")
		//{
		//	echo $tname .$levels"<br>";
		//}
		//else
		$levels=rtrim($levels,",");
		if ($rname == $last_rname)
			$page .= '<tr><td></td><td> '.$tname ."</td><td>". $levels ."</td></tr>";
		else
		{
			$pcount += 1;
			$page .= '<tr><td>'. $rname ."</td><td>".$tname ."</td><td>". $levels ."</td></tr>";
			//echo $rname . ",[".$corp."] ".$tname . $levels ."<br>";
		}
			
		$count += 1;
		$last_rname = $rname;
	}	
}
$page .= "</table>";

$hdr .= '<body><h3>Skills Check Result</h4>
	<font size="5" color="#ff0000">
	<b>::::::::::::::::::::::::::::::::::::::::::::</b></font>
	<font size="4">Logged in as <b>'. $session->username.'</b></font><br><br>
	Back to [<a href="/account/index.php">Main Page</a>] [<a href="/account/admin/skill_form.php">Skill Form</a>]<br><br>';

foreach ($skilllist as $s => $l)
{
	if ($error[$s] == 1)
	{
		$hdr .= '<b><font color="red">'.$s.':'.$l.'</font></b><br>';
	}
	else
		$hdr .= "<b>$s:$l</b><br>";
}

$hdr .=  "<br>Total = ".$pcount."/".$count."/".$total_c."<br><br>";

echo $hdr . $page.'</body></html>';
?>
