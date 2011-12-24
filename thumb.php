<?php

function createthumb($image)
{
	// read image size
	$img = imagecreatefromjpeg("$image");
	$width = imagesx($img);
	$height = imagesy($img);

  // scale down
  $size = 300;

/*
  $factor=1.0;

  if ($width <= $size && $height <= $size) {
    // just copy the picture
    // -> do nothing
  } else {
    $factor = $size / max($width, $height);
  }
*/
  $factor = min($size / $height, 1.0);

  $thumb_width = intval($factor * $width);
  $thumb_height = intval($factor * $height);

	// temporäres Image erzeugen
	$tmp_img = imagecreatetruecolor($thumb_width, $thumb_height);

	// Bereich des alten Bildes in das neue Bild kopieren
	imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);

	// Thumbnail abspeichern
  mkdir("thumbs");
	imagejpeg($tmp_img, "thumbs/$image");
}

?>
