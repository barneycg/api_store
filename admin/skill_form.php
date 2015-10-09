<?php
include("../include/session.php");
if(!$session->isRecruiter()){
   header("Location: ../index.php");
}
else{

?>
<html> 
<head> 
<title>LAWN Skill Checking Form</title> 

</head> 

<body>
<h2>BPH Memberlist</h2>
<font size="5" color="#ff0000">
<b>::::::::::::::::::::::::::::::::::::::::::::</b></font>
<font size="4">Logged in as <b><? echo $session->username; ?></b></font><br><br>
Back to [<a href="/account/index.php">Main Page</a>]<br><br>

<table width="775" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"> 
	<tr> 
		<td>
			<form name="form1" method="post" action="skillpack.php"> 
			<table width="500" border="1" align="center" cellpadding="2" cellspacing="2"> 
				<tr> 
					<td colspan="2"><strong>Find Specific Skill</strong></td> 
				</tr> 
				<tr> 
					<td>Skill Name</td> 
					<td><input size="65" name="skill" type="text" id="skill"></td> 
				</tr> 
				<tr> 
					<td>Level</td> 
					<td><input size="65" name="level" type="text" id="level"></td> 
				</tr> 
				<tr>
					<td>Missing</td>
					<td><input type="checkbox" name="miss" id="miss"></td>
				<tr> 
					<td colspan="2"><input type="hidden" name="form_id" value="1"><input type="submit" name="Submit" value="Submit"></td> 
				</tr> 
			</table> 
			</form> 
		</td>
	</tr>
	<tr>
		<td>
			<table width="500" border="0" align="center" cellpadding="2" cellspacing="2">
				<tr>
					<td>Enter skills and levels 1 per line in the format :</td>
				</tr>
				<tr>
					<td>SkillName:Level</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<form name="form2" method="post" action="skillpack.php">
			<table width="500" border="1" align="center" cellpadding="2" cellspacing="2">
				<tr>
					<td colspan="2"><strong>Search for Skillset</strong></td>
				</tr>
				<tr>
					<td>Skill List</td>
					<td><textarea name="skills"  cols="50" rows="10" id="skills"></textarea></td>
				</tr>
				<tr>
					<td>Missing</td>
					<td><input type="checkbox" name="miss" id="miss"></td>
				<tr>
				<tr>
					<td colspan="2"><input type="hidden" name="form_id" value="2"><input type="submit" name="Submit" value="Submit"></td>
				</tr>
			</table>
			</form>
		</td> 
	</tr>
	<tr>
		<form name="form2" method="post" action="skillpack.php">
		<table width="500" border="1" align="center" cellpadding="2" cellspacing="2">
			<tr>
				<td>Clone level spread</td>
				<td><input type="hidden" name="form_id" value="3"><input type="submit" name="Submit" value="Submit"></td>
			</tr>
		</table>
	</tr>
</form>
</body>
</html>
<?
};
?>
