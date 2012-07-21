<?php

function apicheck($key_uid,$api_key)
{

	try {
		$ale = AleFactory::getEVEOnline();
		//set user credentials, third parameter $characterID is also possible;
				
		$ale->setCredentials($key_uid, $api_key);
		//all errors are handled by exceptions

		$characters=$ale->account->APIKeyInfo();
		var_dump($characters);
	}
    catch (Exception $e) {
            echo $key_uid." : ".$e->getMessage()."\n";
    }


}
?>
