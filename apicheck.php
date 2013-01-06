<?php
//include("include/session.php");

function apicheck($users_id,$key_uid,$api_key)
{
	global $database;
	$incorp = 0;

	try {
		$ale = AleFactory::getEVEOnline();
		//set user credentials, third parameter $characterID is also possible;
				
		$ale->setCredentials($key_uid, $api_key);
		//all errors are handled by exceptions

		//  Get ignore err value 
		$sql1 = "SELECT ignore_err,userlevel,email,forum_name FROM users where id='$users_id'";
		$result1=$database->query($sql1);//mysql_query($sql1,$con) or die(mysql_error());
		$row1=mysql_fetch_array($result1);
		$ignore_err=$row1['ignore_err'];
		$userlvl = $row1['userlevel'];
		$email= $row1['email'];
		$fname= $row1['forum_name'];
		

		$characters=$ale->account->APIKeyInfo();
		$attr = $characters->result->key->attributes();
		$values = array(0,1);
		$result = array();
		for ($i = 1; $i <= 30; $i++) {
			$val = $values[$i]*2;
			array_push($values,$val);
		}

		$test = $attr['accessMask'];
		foreach ($values as $value) {
			array_push($result,$value & $test);
		}

		if ( ($ignore_err=='1') || ( $result[3] !=0 && $result[4] !=0 && $result[5] !=0 && $result[18] !=0 && $result[19] !=0 && $result[21] !=0 && $result[24] !=0 && $result[25] !=0 && $result[26] !=0 ))
		{
			// TODO : only do insert if toon not there or update if the toons corp/users_id/key_uid has changed
			foreach ($characters->result->key->characters as $character)
			{
				if ($character->corporationName == "Blueprint Haus")
				{
					$sql2 = "REPLACE INTO toons(id,users_id,api_id,key_uid,toon_id,toon_name,corp) VALUES(NULL,'$users_id',0,'$key_uid','$character->characterID',\"$character->characterName\",\"$character->corporationName\")";
					$result2=$database->query($sql2);//mysql_query($sql2,$con) or die(mysql_error());
					$incorp = 1;
				}
				else
				{
					$sql6 = "REPLACE INTO toons(id,users_id,api_id,key_uid,toon_id,toon_name,corp) VALUES(NULL,'$users_id',0,'$key_uid','$character->characterID',\"$character->characterName\",\"$character->corporationName\")";
					//$sql6 = "DELETE FROM toons WHERE toon_id = $character->characterID";
					$result6=$database->query($sql6);//mysql_query($sql6,$con) or die(mysql_error());
				}
				if ($character->corporationName == "Senex Legio")
				{
					$sql99 = "UPDATE users set senex_alt = 1 where id=$users_id";
					$result99=$database->query($sql99);//mysql_query($sql99,$con) or die(mysql_error());
				}
			}

			if ($incorp == 1)
			{
				$sql9 = "UPDATE api_keys SET valid='incorp' where key_uid=$key_uid";
				$result9=$database->query($sql9);//mysql_query($sql9,$con) or die(mysql_error());
				$sql13= "SELECT count(*) as count from toons,users WHERE toons.users_id=users.id and users.id=$users_id and toons.corp = 'Blueprint Haus'";
				$result13=$database->query($sql13);//mysql_query($sql13,$con) or die(mysql_error());
				$row13=mysql_fetch_array($result13);
				$count=$row13['count'];
				$sql14 = "UPDATE users SET toon_count=$count where id=$users_id";
				$result14 = $database->query($sql14);//mysql_query($sql14,$con) or die(mysql_error());
				
				if (($userlvl != "9")&&($userlvl !="4")&&($userlvl !="5"))
				{
					$sql3 = "UPDATE users SET userlevel=4 WHERE id='$users_id'";
					$result3=$database->query($sql3);//mysql_query($sql3,$con) or die(mysql_error());
					exec("/home/sites/www.blueprinthaus.org/account/add-wiki.pl $email");
					$sql15 = "UPDATE users SET wiki=1 WHERE id='$users_id'";
					$result15=$database->query($sql15);//mysql_query($sql15,$con) or die(mysql_error());
					// send mail to notify to add forum account.
					$message = $fname . " has joined the corp\n";
					$to = 'barney@telinformix.com';
					$subject = '[BPH] Add forum access';
					$body = $message;
					$headers = 'From: Shin@telinformix.com';
					mail($to, $subject, $body,$headers);
				}
				return 1;
			}
			else
			{
				$sql7 = "UPDATE api_keys SET valid='outcorp' where key_uid=$key_uid";
				$result7=$database->query($sql7);//mysql_query($sql7,$con) or die(mysql_error());
				$sql13= "SELECT count(*) as count from toons,users WHERE toons.users_id=users.id and users.id=$users_id and toons.corp = 'Blueprint Haus'";
				$result13=$database->query($sql13);//mysql_query($sql13,$con) or die(mysql_error());
				$row13=mysql_fetch_array($result13);
				$count=$row13['count'];
				$sql14 = "UPDATE users SET toon_count=$count where id=$users_id";
				$result14 = $database->query($sql14);//mysql_query($sql14,$con) or die(mysql_error());
				// if tc =0 mail me to remove forums access.
				if ($count==0)
				{
				//	$message = $fname . " has left the corp\n";
				//	$to = 'barney@telinformix.com';
				//	$subject = '[BPH] Remove forum access';
				//	$body = $message;
				//	$headers = 'From: Shin@telinformix.com';
				//	mail($to, $subject, $body,$headers);
				//}
					if (($userlvl==4)||($userlvl==5))
					{
						$sql16 = "UPDATE users SET userlevel=3 where id=$users_id";
            			$result16 = $database->query($sql16);//mysql_query($sql16,$con) or die(mysql_error());
            		}
					return 0;	
				}
				return 1;
			}
		}
		else
		{	
			$sql10 = "UPDATE api_keys SET valid='type_err' where key_uid=$key_uid";
			$result10=$database->query($sql10);//mysql_query($sql10,$con) or die(mysql_error());
			if ($attr['type'] != "Account")
			{
				$sql11 = "UPDATE api_keys SET detail='character' where key_uid=$key_uid";
				$result11=$database->query($sql11);//mysql_query($sql11,$con) or die(mysql_error());
			} 
			$sql15 = "DELETE FROM toons WHERE key_uid= $key_uid";
            $result15=$database->query($sql15);//mysql_query($sql15,$con) or die(mysql_error());
			$sql13= "SELECT count(*) as count from toons,users WHERE toons.users_id=users.id and users.id=$users_id and toons.corp = 'Blueprint Haus'";
            $result13=$database->query($sql13);//mysql_query($sql13,$con) or die(mysql_error());
            $row13=mysql_fetch_array($result13);
            $count=$row13['count'];
            $sql14 = "UPDATE users SET toon_count=$count where id=$users_id";
            $result14 = $database->query($sql14);//mysql_query($sql14,$con) or die(mysql_error());
            // if tc =0 mail me to remove forums access.
            if ($count==0)
            {
                //$message = $fname . " has left the corp\n";
                //$to = 'barney@telinformix.com';
                //$subject = '[BPH] Remove forum access';
                //$body = $message;
                //$headers = 'From: Shin@telinformix.com';
                //mail($to, $subject, $body,$headers);
                if (($userlvl == 4)||($userlvl==5))
                {
                	$sql15 = "UPDATE users SET userlevel=3 where id=$users_id";
            		$result15 = $database->query($sql15);//mysql_query($sql15,$con) or die(mysql_error());
            	}
				return 0;
            }
			return 1;
		}
	}
	//and finally, we should handle exceptions
	catch (Exception $e) {
		if (($e->getMessage() == "Authentication failure.") || ($e->getMessage() == "Login denied by account status." ) || ($e->getMessage() == "Api call requires user credentials"))
		{
			$sql4 = "UPDATE api_keys SET valid='invalid' where key_uid=$key_uid";
			$result4=$database->query($sql4);//mysql_query($sql4,$con) or die(mysql_error());
			$sql5 = "DELETE FROM toons WHERE key_uid=$key_uid";
			$result5=$database->query($sql5);//mysql_query($sql5,$con) or die(mysql_error());
		}
		elseif (preg_match("/Key has expired/",$e->getMessage()))
		{
			$sql11 = "UPDATE api_keys SET valid='old' where key_uid=$key_uid";
			$result11=$database->query($sql11);//mysql_query($sql11,$con) or die(mysql_error());
			$sql12 = "DELETE FROM toons WHERE key_uid=$key_uid";
			$result12=$database->query($sql12);//mysql_query($sql12,$con) or die(mysql_error());
		}
		else
		{
			echo $key_uid." : " . $api_key . " : " . $e->getMessage() . "\n";
		}
		$sql16= "SELECT count(*) as count from toons,users WHERE toons.users_id=users.id and users.id=$users_id and toons.corp = 'Blueprint Haus'";
        $result16=$database->query($sql16);//mysql_query($sql16,$con) or die(mysql_error());
        $row16=mysql_fetch_array($result16);
        $count=$row16['count'];
        $sql17 = "UPDATE users SET toon_count=$count where id=$users_id";
        $result17 = $database->query($sql17);//mysql_query($sql17,$con) or die(mysql_error());
		return 0;
	}
}
?>
