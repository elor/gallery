<!DOCTYPE html>
<html lang="de">

<head>
<meta charset="utf-8" />
<title>Gallery</title>
<link rel="stylesheet" href="getsomestyle.css" />
</head>

<body>
<div id="menu">
<?php
// Whatever you can see in this file is a cheap hack. I haven't worked with PHP
// for years, so if you're shaking your head while reading every single line,
// keep in mind that I don't care as long as it works.

  if (($handle = opendir('.'))) {
    while ( !!($entry = readdir($handle) )) {
      if (is_dir($entry) && $entry !== '..' && $entry !== '.') {
        echo "<a href=\"http://www.5engine.de/?g=$entry\">$entry</a><br />";
      }
    }
    closedir($handle);
  }

?>
</div>

<?php
  if (($gallery = $_GET['g'])) {
    if (($handle = opendir($gallery))) {
      echo "<div class=\"container\">\n";
      echo "<h2>$gallery</h2>";

      while ( !!($entry = readdir($handle) )) {
        if (is_file("$gallery/$entry")) {
          echo "<div class=\"item\"><a href=\"http://www.5engine.de/$gallery/$entry\">$entry</a></div>";
        }
      }

      echo "</div>";
    } else {
      echo "gallery doesn't exist.";
    }
  } else {
    echo "Please select a gallery from the menu to your right.";
  }
?>

</html>

