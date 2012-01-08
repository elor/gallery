<!DOCTYPE html>
<html lang="de">

<head>
<meta charset="utf-8" />
<?php
  $page = $_GET["g"];
  $page = ($page ? $page : "Startseite");
  echo "<title>Thomas B&ouml;ttcher Chemnitz - $page</title>";
?>

<link rel="stylesheet" href="getsomestyle.css" />
</head>

<body>
<?php
  
  ini_set('display_errors', 0);
// Whatever you can see in this file is a cheap hack. I haven't worked with PHP
// for years, so if you're shaking your head while reading every single line,
// keep in mind that I don't care as long as it works.

  function replaceSpace($in) {
    return str_replace(" ", "%20", $in);
  }

//  echo replaceSpace('abc asdc ');

  if ($_GET["p"]) {
// picture page

    $gallery = $_GET["g"];
    $gallery_esc = replaceSpace($gallery);
    $picture = $_GET["p"];
    $picture_esc = replaceSpace($picture);

    if (($handle = opendir($gallery))) {

      $list = array();

      while ( !!($entry = readdir($handle) )) {
        if (is_file("$gallery/$entry")) {
          $list[] = $entry;
        }
      }

      sort($list);

      $id = array_search($picture, $list);

//      echo "id: $id \n";
      
      $exists = !($id === false);
    
      $max = count($list);

      if (!$max) {
        echo "
        <p>Diese Galerie ist leer.
        <br>
        <a href=\"/\"> &gt;&gt; Zur Startseite</a>
        </p>
        ";
        exit;
      }

      echo "
        <a class=\"go left\" href=\"/?g=$gallery_esc\">zur&uuml;ck</a>
      ";

      if ($id !== 0) {
        $tmp = replaceSpace($list[$id - 1]);
        echo "
          <a class=\"go left\" href=\"/?g=$gallery_esc&amp;p=$tmp\">&lt;&lt;</a>
        ";
      }

      if ($id !== $max - 1) {
        $tmp = replaceSpace($list[$id + 1]);
        echo "
          <a class=\"go right\" href=\"/?g=$gallery_esc&amp;p=$tmp\">&gt;&gt;</a>
        ";
      }

      echo "
        <div id=\"nav\">
      ";
      
      echo "<span>";

      $tmp = replaceSpace($list[0]);
      $current = ($id === 0 ? "class=\"current\"" : "");
      echo "
        <a href=\"/?g=$gallery_esc&amp;p=$tmp\" $current>1</a>
      ";

      if ($id > 3) {
        echo " ... ";
      }

//      echo "from: " . max(2, $id - 1) . "\n";
//      echo "to: " . min($max - 2, $id + 3) . "\n";
      for ($i = max(2, $id - 1) ; $i < min($max, $id + 4) ; ++$i) {
//        echo "$i < $id";
        $tmp = replaceSpace($list[$i - 1]);
        $cur = ($i === $id + 1 ? "class=\"current\"" : "");
        echo "
          <a href=\"/?g=$gallery_esc&amp;p=$tmp\" $cur>$i</a>
        ";
      }

      
      if ($id < $max - 4) {
        echo " ... ";
      }

      
      $tmp = replaceSpace($list[$max - 1]);
      $current = ($id === $max - 1 ? "class=\"current\"" : "");
      echo "
        <a href=\"/?g=$gallery_esc&amp;p=$tmp\" $current>$max</a>
      ";

      echo "</span></div>";

      if ($exists) {
        echo "
        <div id=\"pic\">
        <a href=\"/$gallery_esc/$picture_esc\"><img src=\"/$gallery_esc/$picture_esc\" alt=\"$picture\" />
        <div id=\"name\">$picture</div>
        </a>
        </div>
        ";
      } else {
        echo "
        <div id=\"pic\">
        Das Bild '$picture' existiert nicht innerhalb der Galerie '$gallery'.
        </div>
        ";
      }

    } else {
      echo "
      <p>Die Galerie '$gallery' ist nicht verf&uuml;gbar.
      <br>
      <a href=\"/\"> &gt;&gt; Zur Startseite</a>
      </p>
      ";
      exit;
    }
    
  } else {
// gallery or home/contact page
    echo "<div id=\"menu\">";

    if (($handle = opendir('.'))) {
      $list = array();
      while ( !!($entry = readdir($handle) )) {
        if (is_dir($entry) && $entry[0] !== '.' && $entry !== "thumbs") {
          $list[] = $entry;
        }
      }
      
      sort($list);

      $cur = $_GET["g"];

      $current = (!$cur ? "class=\"current\"" : "");

      echo "<a href=\"/\" $current>Startseite</a><br>";

      foreach ($list as $val) {
        $current = ($cur === $val ? "class=\"current\"" : "");
        $val_esc = replaceSpace($val);
        echo "<a href=\"/?g=$val_esc\" $current>$val</a>";
      }
      
      closedir($handle);
    }

    echo "</div>";
    if (($gallery=$_GET["g"])) {
// gallery page
      $gallery_esc = replaceSpace($gallery);

      echo "<div id=\"container\">";

      include 'thumb.php';

      if (($handle = opendir($gallery))) {
  //      echo "<h2>$gallery</h2>";

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
          $val_esc = replaceSpace($val);
          echo "<a href=\"/?g=$gallery_esc&amp;p=$val_esc\"><img src=\"thumbs/$gallery_esc/$val_esc\" alt=\"$val\" class=\"item\"/></a>";
        }

      closedir($handle);

      } else {
        echo "Galerie existiert nicht.";
      }

      echo "<br class=\"clear\" />";
      echo "</div>";
    } else {
 // home/contact page
      echo "
      <div id=\"home\">
      <h2>Thomas B&ouml;ttcher <span>Chemnitz</span></h2>
      <img src=\"img.jpg\" alt=\"Thomas B&ouml;ttcher\" />
      <p>
Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
      </p>
      </div>
      ";
    }
  }

?>

</body>

</html>

