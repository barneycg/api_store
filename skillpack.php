<?php
include("/home/sites/www.blueprinthaus.org/account/apicheck.php");
//include("/home/sites/www.blueprinthaus.org/account/conn.php");
include("/home/sites/www.blueprinthaus.org/account/include/database.php");
require_once '/opt/eve/ale/factory.php';

global $database;
$missing = 1;
$infile = $_GET["file"];
$toonname = $_GET["toon"];

$file_handle = fopen($infile, "r");
while (!feof($file_handle)) {
	$line = fgets($file_handle);
	$line = trim($line);
	if (!empty($line))
	{
		list($s,$l) = explode(":",$line);
		$skilllist[$s] = $l;
	}
}
fclose($file_handle);
//var_dump($skilllist);
//exit;
//$sskill = $argv[1];
//$slevel = $argv[2];

$keys_q = "select m.forum_name as fname, a.key_uid as key_uid, c.toon_id as toon_id, c.toon_name as tname from api_keys as a, toons as c, users as m where a.key_uid = c.key_uid and c.corp = 'Blueprint Haus' and m.id = a.users_id and c.toon_name = '".$toonname."' ORDER by m.forum_name,c.toon_name";
$keys_r = $database->query($keys_q);
//and c.name = 'Shin Chogan'
$count = 0;

// ***
// *** Get a list of valid keys and toons
// ***
//$connection = mysql_connect("127.0.0.1", "root", "FcMD7fA4") or die(mysql_error());
//mysql_select_db("bph", $connection) or die(mysql_error());
$last_rname = '';
echo "<table border='1'>\n<tr><th>Skill</th><th>Current Level</th><th>Required Level</th>\n";
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
				echo "<tr bgcolor='green'><td>".$sskill ."</td><td style='text-align: center;'>" .$level."</td><td style='text-align: center;'>".$slevel."</td></tr>\n";
				// *** and not already failed earlier
				/*if ($matched != 99)
				{
					// *** found
					if ($level > $slevel)
						$levels .= '<font color="blue">'.$level."</font>,";
					else
						$levels .= '<font color="green">'.$level."</font>,";
					$matched = 1;
					
				}*/
			}
			else
			{
				// ** not found
				/*$matched = 99;
				$levels .= $level.",";*/
				echo "<tr bgcolor='red'><td>".$sskill ."</td><td style='text-align: center;'>" .$level."</td><td style='text-align: center;'>".$slevel."</td></tr>\n";
			}
		}
		if ($skill_found == 0)
		{
			echo "<tr bgcolor='red'><td>".$sskill ."</td><td style='text-align: center;'>" .$level."</td><td style='text-align: center;'>".$slevel."</td></tr>\n";
		}
	}
	echo "</table>";
	
	echo "\n</body>\n</html>";
}
?>


