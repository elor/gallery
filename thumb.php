<?php

function createthumb($image)
{
	// read image size
	$img = imagecreatefromjpeg("$image");
	$width = imagesx($img);
	$height = imagesy($img);

  // scale down
  $thumb_width=300;
  $thumb_height=200;

  $factor = max($thumb_width / $width, $thumb_height / $height);

  $cull_width = $thumb_width / $factor;
  $cull_height = $thumb_height / $factor;

  $off_x = ($width - $cull_width) / 2;
  $off_y = ($height - $cull_height) / 4;
//  $off_y = 0;

	// temporäres Image erzeugen
	$tmp_img = imagecreatetruecolor($thumb_width, $thumb_height);

	// Bereich des alten Bildes in das neue Bild kopieren
	imagecopyresized($tmp_img, $img, 0, 0, $off_x, $off_y, $thumb_width, $thumb_height, $cull_width, $cull_height);

	// Thumbnail abspeichern
	imagejpeg($tmp_img, "thumbs/$image");
}

?>
