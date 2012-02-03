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
//  ini_set('display_errors', 0);

// Whatever you can see in this file is a cheap hack. I haven't worked with PHP
// for years, so if you're shaking your head while reading every single line,
// keep in mind that I don't care as long as it works.

  if ($_GET["p"]) {
// picture page

    $gallery = $_GET["g"];
    $gallery_esc = rawurlencode($gallery);
    $picture = $_GET["p"];
    $picture_esc = rawurlencode($picture);

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
        $tmp = rawurlencode($list[$id - 1]);
        echo "
          <a class=\"go left\" href=\"/?g=$gallery_esc&amp;p=$tmp\">&lt;&lt;</a>
        ";
      } else {
        echo "
          <a class=\"go left end\" >&lt;&lt;</a>
        ";
      }

      if ($id !== $max - 1) {
        $tmp = rawurlencode($list[$id + 1]);
        echo "
          <a class=\"go right\" href=\"/?g=$gallery_esc&amp;p=$tmp\">&gt;&gt;</a>
        ";
      } else {
        echo "
          <a class=\"go right end\" >&gt;&gt;</a>
        ";
      }

      echo "
        <div id=\"nav\">
      ";
      
      echo "<span>";

      $tmp = rawurlencode($list[0]);
      $current = ($id === 0 ? "class=\"current\"" : "");
      echo "
        <a href=\"/?g=$gallery_esc&amp;p=$tmp\" $current>1</a>
      ";

      if ($id > 2) {
        echo " ... ";
      }

//      echo "from: " . max(2, $id - 1) . "\n";
//      echo "to: " . min($max - 2, $id + 3) . "\n";
      for ($i = max(2, $id) ; $i < min($max, $id + 3) ; ++$i) {
//        echo "$i < $id";
        $tmp = rawurlencode($list[$i - 1]);
        $cur = ($i === $id + 1 ? "class=\"current\"" : "");
        echo "
          <a href=\"/?g=$gallery_esc&amp;p=$tmp\" $cur>$i</a>
        ";
      }

      
      if ($id < $max - 3) {
        echo " ... ";
      }

      
      $tmp = rawurlencode($list[$max - 1]);
      $current = ($id === $max - 1 ? "class=\"current\"" : "");
      echo "
        <a href=\"/?g=$gallery_esc&amp;p=$tmp\" $current>$max</a>
      ";

      echo "</span></div>";

      if ($exists) {
        $picture_name = htmlspecialchars(preg_replace("/\.jpg$/", "", $picture));
        echo "
        <div id=\"pic\">
        <a href=\"/$gallery_esc/$picture_esc\">
        <img src=\"/$gallery_esc/$picture_esc\" alt=\"$picture_name\" />
        <div id=\"name\">
        $picture_name</div>
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
        $val_esc = rawurlencode($val);
        echo "<a href=\"/?g=$val_esc\" $current>$val</a>";
      }
      
      closedir($handle);
    }

    echo "</div>";
    if (($gallery=$_GET["g"])) {
// gallery page
      $gallery_esc = rawurlencode($gallery);
      $gallery_html = htmlspecialchars($gallery);

      echo "<div id=\"container\">\n";

      include 'thumbs/make.php';

      if (($handle = opendir($gallery))) {
  //      echo "<h2>$gallery</h2>";

        $list = array();

        if (!is_dir("thumbs")) {
          mkdir("thumbs");
        }

        if (!is_dir("thumbs/$gallery")) {
          mkdir("thumbs/$gallery");
        }

// failsafe code for broken names
        if (($newname = $_GET["rename"])) {
          
          rename($gallery, $newname);
          echo "'$gallery' renamed to '$newname'";
          exit;
        }
        
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
          $val_esc = rawurlencode($val);
          $val_html = htmlspecialchars($val);
          echo "<a href=\"/?g=$gallery_esc&amp;p=$val_esc\"><img src=\"thumbs/$gallery_esc/$val_esc\" alt=\"$val_html\" class=\"item\"/></a>\n";
        }

      closedir($handle);

      } else {
        echo "Galerie existiert nicht.\n";
      }

      echo "<br class=\"clear\" />\n";
      echo "</div>\n";
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

