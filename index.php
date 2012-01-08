<!DOCTYPE html>
<html lang="de">

<head>
<meta charset="utf-8" />
<title>Gallery</title>
<link rel="stylesheet" href="getsomestyle.css" />
</head>

<body>
<?php
  
  ini_set('display_errors', 0);
// Whatever you can see in this file is a cheap hack. I haven't worked with PHP
// for years, so if you're shaking your head while reading every single line,
// keep in mind that I don't care as long as it works.

  if ($_GET["p"]) {
// picture page

    $gallery = $_GET["g"];
    $picture = $_GET["p"];

    if (($handle = opendir($gallery))) {
//      echo "<h2>$gallery</h2>";

      $list = array();

      mkdir("thumbs");
      mkdir("thumbs/$gallery");
      
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
        echo "Diese Galerie ist leer.
        <br>
        <a href=\"/\">Zur Startseite</a>
        ";
        exit;
      }

      echo "
        <a class=\"go left\" href=\"?g=$gallery\">zur&uuml;ck</a>
      ";

      if ($id !== 0) {
        $tmp = $list[$id - 1];
        echo "
          <a class=\"go left\" href=\"/?g=$gallery&p=$tmp\">&lt;&lt;</a>
        ";
      }

      if ($id !== $max - 1) {
        $tmp = $list[$id + 1];
        echo "
          <a class=\"go right\" href=\"/?g=$gallery&p=$tmp\">&gt;&gt;</a>
        ";
      }

      echo "
        <div id=\"nav\">
      ";
      
      echo "<span>";

      $current = ($id === 0 ? "class=\"current\"" : "");
      echo "
        <a href=\"/?g=$gallery&p=$list[0]\" $current>1</a>
      ";

      if ($id > 3) {
        echo " ... ";
      }

//      echo "from: " . max(2, $id - 1) . "\n";
//      echo "to: " . min($max - 2, $id + 3) . "\n";
      for ($i = max(2, $id - 1) ; $i < min($max, $id + 4) ; ++$i) {
//        echo "$i < $id";
        $tmp = $list[$i - 1];
        $cur = ($i === $id + 1 ? "class=\"current\"" : "");
        echo "
          <a href=\"/?g=$gallery&p=$tmp\" $cur>$i</a>
        ";
      }

      
      if ($id < $max - 4) {
        echo " ... ";
      }

      
      $tmp = $list[$max - 1];
      $current = ($id === $max - 1 ? "class=\"current\"" : "");
      echo "
        <a href=\"/?g=$gallery&p=$tmp\" $current>$max</a>
      ";

      echo "</span></div>";

      echo "
      <a id=\"piclink\" href=\"/$gallery/$picture\"><img id=\"pic\" src=\"/$gallery/$picture\" />
      <div id=\"name\">$picture</div>
      </a>
      ";

    } else {
      echo "Galerie kann nicht gefunden werden.
      <br>
      <a href=\"/\">Zur Startseite</a>
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
        echo "<a href=\"/?g=$val\" $current>$val</a>";
      }
      
      closedir($handle);
    }

    echo "</div>";
    if (($gallery=$_GET["g"])) {
// gallery page
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
          echo "<a href=\"/?g=$gallery&p=$val\"><img src=\"thumbs/$gallery/$val\" alt=\"$val\" class=\"item\"/></a>";
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
      <img src=\"img.jpg\" />
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

