<pre>
<?php
// Whatever you can see in this file is a cheap hack. I haven't worked with PHP
// for years, so if you're shaking your head while reading every single line,
// keep in mind that I don't care as long as it works.

  if (($handle = opendir('.'))) {
    $list = array();
    while ( !!($entry = readdir($handle) )) {
      if (is_dir($entry) && $entry !== '..' && $entry !== '.' && $entry !== 'thumbs') {
        $list[] = $entry;
      }
    }
    
    sort($list);

    echo "Galerien:\n";
    foreach ($list as $val) {
      echo " $val\n";
    }
    
    closedir($handle);
  } else {
    echo "Fehler: Kann lokales Verzeichnis nicht auf Galerien durchsuchen\n";
  }

  echo "Teste Galerien auf fehlende Thumbnails...\n";

  foreach ($list as $gallery) {
    echo "\nTeste $gallery:\n";

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
        echo " $val\n";
      }

    closedir($handle);
    } else {
      echo " Fehler: Galerie existiert ploetzlich nicht mehr! Bitte den Verzeichnisbaum manuell ueberpruefen.\n";
    }
  }

  echo "\nFertig.\n
  Fehlende Thumbnails werden beim Ansehen einer Galerie automatisch erstellt,\n
  sofern moeglich. Bisher wird nur .jpg unterstuetzt. Andere Formate koennen\n
  oftmals nicht umgewandelt werden.\n";

?>
</pre>

