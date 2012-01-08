<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8" />
<title>Thumbnail checker</title>
</head>

<body>
<p>
<?php
  if (($handle = opendir('.'))) {
    $list = array();
    while ( !!($entry = readdir($handle) )) {
      if (is_dir($entry) && $entry !== '..' && $entry !== '.' && $entry !== 'thumbs') {
        $list[] = $entry;
      }
    }
    
    sort($list);

    echo "Galerien:<br>";
    foreach ($list as $val) {
      $val_html = htmlspecialchars($val);
      echo " $val_html<br>";
    }
    
    closedir($handle);
  } else {
    echo "Fehler: Kann lokales Verzeichnis nicht auf Galerien durchsuchen<br>";
  }

  echo "<br>Teste Galerien auf fehlende Thumbnails...<br>";

  foreach ($list as $gallery) {
    $val_html = htmlspecialchars($gallery);
    echo "<br>Teste $val_html:<br>";

    if (($handle = opendir($gallery))) {
      $files = array();

      while ( !!($entry = readdir($handle) )) {
        if (is_file("$gallery/$entry")) {
          if (!file_exists("thumbs/$gallery/$entry")) {
            $files[] = $entry;
          }
        }
      }

      sort($files);

      foreach ($files as $val) {
        if ($val === " .jpg" || $val === ".jpg") {
          rename ("$gallery/$val", "$gallery/restored$val");
          continue;
        }
        $val_html = htmlspecialchars($val);
        echo " $val_html<br>";
      }

    closedir($handle);
    } else {
      echo " Fehler: Galerie existiert ploetzlich nicht mehr! Bitte den Verzeichnisbaum manuell ueberpruefen.<br>";
    }
  }

  echo "<br>Fertig.<br>
  Fehlende Thumbnails werden beim Ansehen einer Galerie automatisch erstellt,<br>
  sofern moeglich. Bisher wird nur .jpg unterstuetzt. Andere Formate koennen<br>
  oftmals nicht umgewandelt werden.<br>";

?>
</p>
</body>

</html>

