<?
	include("include/session.php");
	global $database;
?>


<html> 
<head> 
<title>Blueprint Haus API Form</title> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> 
</head> 

<body> 
<table width="775" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"> 
  <tr> 
    <td> </td> 
  </tr> 
  <tr> 
    <td>      <table width="700" border="0" align="center" cellpadding="2" cellspacing="2"> 
        <tr> 
          <td> </td> 
        </tr> 
        <tr> 
          <td> 
          <?
		  if (!empty($_POST)){ 
          $mode=$_POST['mode'];
			}
			else {
			$mode="upgrade";
			}
          if($mode=="Add") { 
          ?> 
		  <p style="color:red"><b>You can get your api <a target=_blank href="https://community.eveonline.com/support/api-key/CreatePredefined?accessMask=60162076">here</a> (opens in a new tab and may I suggest ticking the "No Expiry" option)</b></p>
		<hr>
          <form name="form1" method="post" action="apiformsubmit.php?mode=add"> 
            <table width="500" border="1" align="center" cellpadding="2" cellspacing="2"> 
              <tr> 
                <td><strong>Add New API Key</strong></td> 
                <td> </td> 
              </tr> 
              <tr> 
                <td>ID</td> 
                <td><input name="key_uid" type="text" id="key_uid"></td> 
              </tr> 
              <tr> 
                <td>Verification Code</td> 
                <td><input name="api_key" type="text" id="api_key"></td> 
              </tr> 
              <tr> 
                <td>
				<input type="hidden" name="users_id" value="<? echo $session->usrid; ?>">
				<input type="submit" name="Submit" value="Save Data"></td> 
                <td> </td> 
              </tr> 
            </table> 
          </form> 
          <?        
          } else  { 
              //include("conn.php"); 
            $id=$_GET["key_uid"]; 
            $sql="select users_id,key_uid,api_key from api_keys where key_uid='$id'"; 
            $result=$database->query($sql);
            //$result=mysql_query($sql,$con) or die(mysql_error()); 
            while($row=mysql_fetch_array($result)) { 
                //$id=$row['key_uid']; 
                $key_uid=$row['key_uid']; 
                $api_key=$row['api_key']; 
            } 
			//mysql_close($con);
        ?> 
        <form name="form1" method="post" action="apiformsubmit.php?mode=update"> 
            <table width="500" border="1" align="center" cellpadding="2" cellspacing="2"> 
              <tr> 
                <td><strong>Update API Key </strong></td> 
                <td><input type="hidden" name="key_uid" value="<? echo $key_uid; ?>"> 
                   </td>
              </tr> 
              <tr> 
                <td>ID</td> 
                <td><input name="keyid" type="text" id="key_uid" value="<? echo $key_uid; ?>" disabled=1></td> 
              </tr> 
              <tr> 
                <td>Verification Code</td> 
                <td><input name="api_key" type="text" id="api_key" value="<? echo $api_key; ?>"></td> 
              </tr> 
              <tr>
              	<td>
              	<input type="hidden" name="users_id" value="<? echo $session->usrid; ?>">
                <input type="submit" name="Submit" value="Update Data"></td> 
                <td> </td> 
              </tr> 
            </table> 
          </form> 
        
        <?    
            
          } 
          ?> 
          
          </td> 
        </tr> 
        <tr> 
          <td> </td> 
        </tr> 
    </table></td> 
  </tr> 
</table> 
</body> 
</html>
