<?php
//path to directory to scan
$directory = '';
 
//get all image files with a .jpg extension.
$images = glob($directory . '*');
 
//print each file name
foreach($images as $image)
{
print $image."<br>";
}

?>
