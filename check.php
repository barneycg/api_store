<?php

function apicheck($key_uid,$api_key)
{

	try {
		$ale = AleFactory::getEVEOnline();
		//set user credentials, third parameter $characterID is also possible;
				
		$ale->setKey($key_uid, $api_key);
		//all errors are handled by exceptions

		$characters=$ale->account->APIKeyInfo();
		//var_dump($characters);

		$characters=$ale->account->APIKeyInfo();
        $attr = $characters->result->key->attributes();
        $values = array(0,1);
        $result = array();
        for ($i = 1; $i <= 30; $i++) {
            $val = $values[$i]*2;
            array_push($values,$val);
        }

        $test = $attr['accessMask'];
		echo "Key type : " .$attr['type']."<br>";
		echo "Access Mask : ".$test."<br>";

        foreach ($values as $value) {
            array_push($result,$value & $test);
        }

        if ( ( $result[3] !=0 && $result[4] !=0 && $result[5] !=0 && $result[18] !=0 && $result[19] !=0 && $result[21] !=0 && $result[24] !=0 && $result[25] !=0 && $result[26] !=0 ))
        {
			echo "Access Mask includes BPH required options<br>";
		}
		else
			echo "Access Mask does not includes BPH required options<br>";
		
		foreach ($characters->result->key->characters as $character)
		{
			//var_dump($character);
			echo $character->characterID . " : ".$character->characterName . " : " . $character->corporationName; 
			echo "<br>";
		}
		var_dump($characters);
	}
    catch (Exception $e) {
            echo $key_uid." : ".$e->getMessage()."\n";
    }


}
?>
