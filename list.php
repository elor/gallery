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
  if ($handle = opendir('.')) {
    while ( !!($entry = readdir($handle) )) {
      if (is_dir($entry) && $entry !== '..' && $entry !== '.') {
        echo "$entry\n";
      }
    }
    closedir($handle);
  }
  ?>
</div>

<div class="container">
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div class="item"></div>
  <div id="foot"></div>
</div>
</body>

</html>

