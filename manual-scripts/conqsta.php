#!/usr/bin/php
<?php
require_once '/opt/eve/ale/factory.php';
include("/home/sites/www.blueprinthaus.org/account/include/database.php");

global $database;

$ale = AleFactory::getEVEOnline();
        //set user credentials, third parameter $characterID is also possible;

//$ale->setKey($key_uid, $api_key, $toon_id);
//var_dump($ale);
$csl=$ale->eve->ConquerableStationList();

foreach ($csl->result->outposts as $o)
{
	$sta = $o->attributes();

	$stationID = $sta['stationID'];
	$stationName = $sta['stationName'];
	$stationTypeID = $sta['stationTypeID'];
	$solarSystemID = $sta['solarSystemID'];
	$corporationID = $sta['corporationID'];
	$corporationName = $sta['corporationName'];
	//var_dump($sta);
	$sql="INSERT INTO ConqStations values($stationID,'$stationName',$stationTypeID,$solarSystemID,$corporationID,'$corporationName')";
	$result1 = $database->query($sql);
}
?>