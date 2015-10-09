#!/usr/bin/php5
<?php
include("/home/sites/www.blueprinthaus.org/account/apicheck.php");
//include("/home/sites/www.blueprinthaus.org/account/conn.php");
include("/home/sites/www.blueprinthaus.org/account/include/database.php");
require_once '/opt/eve/ale/factory.php';

global $database;

$infile = $argv[1];
$toonname = $argv[2];
$missing = 1;

//var_dump($infile);

$file_handle = fopen($infile, "r");
while (!feof($file_handle)) {
	$line = fgets($file_handle);
	$line = trim($line);
	//echo $line;
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

$sqla = "SELECT a.users_id as users_id,a.key_uid as key_uid,a.api_key as api_key, t.toon_id as toon_id, u.forum_name as fname, t.toon_name as tname FROM api_keys as a,users as u,toons as t where a.users_id = u.id and a.key_uid=t.key_uid and t.corp='Blueprint Haus' ORDER BY `a`.`users_id` ASC"; 
$sqlt = "SELECT a.users_id as users_id,a.key_uid as key_uid,a.api_key as api_key, t.toon_id as toon_id, u.forum_name as fname, t.toon_name as tname FROM api_keys as a,users as u,toons as t where a.users_id = u.id and a.key_uid=t.key_uid and t.corp='Blueprint Haus' and t.toon_name = '" . $toonname . "' ORDER BY `a`.`users_id` ASC";

if (empty($toonname))
{
	$sql = $sqla;
}
else
{
	$sql = $sqlt;
}
$result=$database->query($sql);//mysql_query($sql,$con);
$left=array();
$message='';
while ($row=mysql_fetch_array($result)){
	echo "1\n";
	$matched = 0;
	$users_id=$row['users_id'];
	$key_uid=$row['key_uid'];
	$api_key=$row['api_key'];
	$fname=$row['fname'];
	$tname=$row['tname'];
	$toon_id=$row['toon_id'];
	$short = array();
	try {
        $ale = AleFactory::getEVEOnline();
        //set user credentials, third parameter $characterID is also possible;

        $ale->setKey($key_uid, $api_key, $toon_id);
		//var_dump($ale);
		$skillsheet=$ale->char->CharacterSheet();
		foreach ($skilllist as $sskill => $slevel)
		{
			var_dump($sskill);
			var_dump($slevel);
			$skill_found = 0;
			foreach ($skillsheet->result->skills as $skill)
			{
				$typeID = $skill->typeID;
				$level = $skill->level;

				$sql2 = "SELECT typeName from invTypes where typeID='$typeID'";
				$result2=$database->query($sql2);
				while ($row2=mysql_fetch_array($result2)){
					$typeName=$row2['typeName'];
				}
				
				if (strcasecmp($typeName,$sskill) == 0)
				{
					$skill_found = 1;
					//echo $tname . " : " . $typeID . " : " . $level ."\n";
					if (($level >= $slevel))
					{
						//echo $tname . " : " . $sskill . " : " . $level ."\n";
						if ($matched != 99)
						{
							$matched = 1;
						}
						$short[$sskill] = $slevel . ":" . $level;
					}
					else
					{
						$matched = 99;
						$short[$sskill] = $slevel . ":" . $level;
						//continue 3;
					}
				}
			}
			if ($skill_found == 0)
			{
				$short[$sskill] = $slevel . ":" . 0;
			}
		}
	}
	catch (Exception $e)
	{
		var_dump($e);
	}

	if (($missing ==  1))
	{
		echo "<h3>".$tname ."</h3><br>";
		echo "<table border=1><tr><th>Skill</th><th>Required level</th><th>Current Level</th></tr>\n";
		foreach ($short as $short_skill => $sslevel)
		{
			list($req,$cur) = explode(":",$sslevel);
			if ($cur >= $req)
				$bg = "green";
			else
				$bg = "red";
			echo "<tr bgcolor=".$bg."><td>" . $short_skill . "</td><td align=center>" . $req . "</td><td align=center>". $cur . "</td></tr>\n";
		} 
	}
}

