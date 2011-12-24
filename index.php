<!DOCTYPE html>
<html lang="de">

<head>
<meta charset="utf-8" />
<title>Gallery</title>
<link rel="stylesheet" href="getsomestyle.css" />
</head>

<?php
  ini_set('display_errors', 0);
?>

<body>
<div id="menu">
<a href="http://5engine.de/">Home</a><br />
<h4>Galerien</h4>
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

    foreach ($list as $val) {
      echo "<a href=\"http://www.5engine.de/?g=$val\">$val</a><br />";
    }
    
    closedir($handle);
  }

?>
</div>

<div id="container">

<?php
  include 'thumb.php';

  if (($gallery = $_GET['g'])) {
    if (($handle = opendir($gallery))) {
      echo "<h2>$gallery</h2>";

      $list = array();

      mkdir("thumbs");
      mkdir("thumbs/$gallery");
      
      while ( !!($entry = readdir($handle) )) {
        if (is_file("$gallery/$entry")) {
          if (!file_exists("thumbs/$gallery/$entry")) {
            createthumb("$gallery/$entry");
          }

          if (file_exists("thumbs/$gallery/$entry")) {
            $list[] = $entry;
          }
        }
      }

      sort($list);

      foreach ($list as $val) {
        echo "<a href=\"http://www.5engine.de/$gallery/$val\"><img src=\"thumbs/$gallery/$val\" alt=\"$val\" class=\"item\"/></a>";
      }

    } else {
      echo "gallery doesn't exist.";
    }
  } else {
    echo "Please select a gallery from the menu to your right.";
  }
?>

</div>

</body>

</html>

