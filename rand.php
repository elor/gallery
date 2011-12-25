<?php
  
//  ini_set('display_errors', 0);

$num = 0;

mkdir("rand");

while (($num++) < 100)
{
  $height = rand(200, 1000);
  $width = rand(200, 1000);
  
	$tmp_img = imagecreatetruecolor($width, $height);
	imagejpeg($tmp_img, "rand/image$num.jpg");
}
?>
