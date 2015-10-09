#!/usr/bin/php
<?php
include("/home/sites/www.blueprinthaus.org/account/apicheck.php");
//include("/home/sites/www.blueprinthaus.org/account/conn.php");
include("/home/sites/www.blueprinthaus.org/account/include/database.php");
require_once '/opt/eve/ale/factory.php';

global $database;

$sasset = $argv[1];

$sql = "SELECT a.users_id as users_id,a.key_uid as key_uid,a.api_key as api_key, t.toon_id as toon_id, u.forum_name as fname, t.toon_name as tname FROM api_keys as a,users as u,toons as t where a.users_id = u.id and a.key_uid=t.key_uid and t.corp='Blueprint Haus' ORDER BY `a`.`users_id` ASC"; 
//$sql = "SELECT a.users_id as users_id,a.key_uid as key_uid,a.api_key as api_key, t.toon_id as toon_id, u.forum_name as fname, t.toon_name as tname FROM api_keys as a,users as u,toons as t where a.users_id = u.id and a.key_uid=t.key_uid ORDER BY `a`.`users_id` ASC"; 

$result=$database->query($sql);//mysql_query($sql,$con);
$left=array();
$message='';

//$sql2 = "SELECT typeName from invTypes where typeID='$typeID'";
$sql2 = "SELECT typeID from invTypes where typeName='$sasset'";
$result2=$database->query($sql2);
while ($row2=mysql_fetch_array($result2)){
	$stypeID=$row2['typeID'];
}


function get_loc($locationID)
{
	global $database;
	$slocation ='';
	
	switch ($locationID)
	{
		case ($locationID >= 66000000 && $locationID <= 66999999) :
			//staStations.stationID
			$locationID = $locationID - 6000001;
			$ql_loc = "SELECT itemName from mapDenormalize as m, staStations as s where m.solarSystemID = s.solarSystemID and s.stationID = $locationID and m.itemName like '% - Star'";
			break;
		case ($locationID >= 67000000 && $locationID <= 67999999) :
			//ConqStations.stationID
			$locationID = $locationID - 6000000;
			$sql_loc = "SELECT itemName from mapDenormalize as m, staStations as s where m.solarSystemID = s.solarSystemID and s.stationID = $locationID and m.itemName like '% - Star'";
			break;
		case ($locationID >= 60014861 && $locationID <= 60014928) :
			//ConqStations.stationID
			$locationID = $locationID;
			$sql_loc = "SELECT itemName from mapDenormalize as m, staStations as s where m.solarSystemID = s.solarSystemID and s.stationID = $locationID and m.itemName like '% - Star'";
			break;
		case ($locationID >= 60000000 && $locationID <= 61000000) :
			//staStations.stationID
			$locationID = $locationID;
			$sql_loc = "SELECT itemName from mapDenormalize as m, staStations as s where m.solarSystemID = s.solarSystemID and s.stationID = $locationID and m.itemName like '% - Star'";
			break;
		case ($locationID >= 61000000) :
			//ConqStations.stationID
			$locationID = $locationID;
			$sql_loc = "SELECT itemName from mapDenormalize as m, staStations as s where m.solarSystemID = s.solarSystemID and s.stationID = $locationID and m.itemName like '% - Star'";
			break;
		default :
			//mapDenormalize.itemID
			$locationID = $locationID;
			$sql_loc = "SELECT itemName from mapDenormalize as m where m.itemID = $locationID and m.itemName like '% - Star'";
			break;
	}

	$result_loc=$database->query($sql_loc);
	while ($row_loc=mysql_fetch_array($result_loc)){
		$slocation=$row_loc['itemName'];
	}
	
	return $slocation;
}


while ($row=mysql_fetch_array($result)){
	$users_id=$row['users_id'];
	$key_uid=$row['key_uid'];
	$api_key=$row['api_key'];
	$fname=$row['fname'];
	$tname=$row['tname'];
	$toon_id=$row['toon_id'];
	try {
        $ale = AleFactory::getEVEOnline();
        //set user credentials, third parameter $characterID is also possible;

        $ale->setKey($key_uid, $api_key, $toon_id);
		//var_dump($ale);
		$assetlist = $ale->char->AssetList();
		//var_dump($assetlist->result);
		//die;
		foreach ($assetlist->result->assets as $assets)
		{
			//var_dump($assets->children());
			//die;
				$location = '';
				foreach ($assets->attributes() as $name=>$value)
				{
					if ($name == 'locationID')
					{
						$locationID=$value;
						$location = get_loc($locationID);
						//var_dump($locationID);
					}
					if (($name == 'typeID') && ($value === $stypeID))
					{
						echo $tname . " : Gen 0 : " . $sasset . " : " . $location . "\n";
					}
				}
				foreach ($assets->children() as $gen1)
				{
						//var_dump($gen1->row);
						//die;
					foreach ($gen1 as $row)
					{
						//var_dump($row);
						//die;
						foreach ($row->attributes() as $name =>  $value)
						{
							// stuff
							//if ($name == 'locationID')
							//{
							//	$locationID=$value;
							//	$location = get_loc($locationID);
							//}
							if (($name == 'typeID') && ($value === $stypeID))
								echo $tname . " : Gen 1 : " . $sasset . " : " . $location . "\n";
						}
					}
					foreach ($gen1->children() as $gen2)
					{
						foreach ($gen2->children() as $gen3)
						{
							foreach ($gen3 as $row)
							{
								foreach ($row->attributes() as $name => $value) 
								{
									// stuff
									if (($name == 'typeID') && ($value === $stypeID))
										echo $tname . " : Gen 3 : " . $sasset ."\n";
								}
							}
						}
					}
				}
		}
			
			/*die;
			$typeID = $asset->typeID;
			$locationID = $asset->locationID;
			$ol = $locationID;
			if (($typeID === $stypeID))
			{

				
				echo $tname . " : " . $slocation . "\n";
			}*/
		
	}
	catch (Exception $e)
	{
		echo "No Assets in Mask : ".$tname. "\n";
	}
}

