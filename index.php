<!DOCTYPE html>
<html lang="de">

<head>
<meta charset="utf-8" />
<?php
// Extract gallery and pic names from query string
  $page = "";
  $pic = "";
  $query = $_SERVER["QUERY_STRING"];

  $components = explode('&', $query);
  $components = explode('/', $components[0]);
  switch (count($components)) {
    case 0:
      break;
    case 2:
      $pic = rawurldecode($components[1]);
    default:
      $page = rawurldecode($components[0]);
  }

// re-encode the url. sloppy server and stuff...
  $page_esc = rawurlencode($page);
  $pic_esc = rawurlencode($pic);

  $page_html = htmlspecialchars($page);
  $pic_html = htmlspecialchars($pic);

  $page_ansi = iconv('UTF-8', 'ISO-8859-1//IGNORE', $page);
  $pic_ansi = iconv('UTF-8', 'ISO-8859-1//IGNORE', $pic);

  $page_ansi_esc = rawurlencode($page_ansi);
  $pic_ansi_esc = rawurlencode($pic_ansi);

// Extraction done

  $title = ($page ? $page : "Startseite");
  echo "<title>Thomas Böttcher Chemnitz - $title</title>";
?>

<link rel="stylesheet" href="getsomestyle.css" />
</head>

<body>
<?php
//  ini_set('display_errors', 0);

// Whatever you can see in this file is a cheap hack. I haven't worked with PHP
// for years, so if you're shaking your head while reading every single line,
// keep in mind that I don't care as long as it works.

  if ($pic) {
// picture page

    if (($handle = opendir($page_ansi))) {

      $list = array();

      while ( !!($entry = readdir($handle) )) {
        if (is_file("$page_ansi/$entry")) {
          $list[] = $entry;
        }
      }

      sort($list);

      $id = array_search($pic, $list);

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
        <a class=\"go left\" href=\"/?$page_esc\">&Uuml;bersicht</a>
      ";

      if ($id !== 0) {
        $tmp = rawurlencode($list[$id - 1]);
        echo "
          <a class=\"go left\" href=\"/?$page_ansi_esc/$tmp\">&lt;&lt;</a>
        ";
      } else {
        echo "
          <a class=\"go left end\" >&lt;&lt;</a>
        ";
      }

      if ($id !== $max - 1) {
        $tmp = rawurlencode($list[$id + 1]);
        echo "
          <a class=\"go right\" href=\"/?$page_esc/$tmp\">&gt;&gt;</a>
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
        <a href=\"/?$page_esc/$tmp\" $current>1</a>
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
          <a href=\"/?$page_esc/$tmp\" $cur>$i</a>
        ";
      }

      
      if ($id < $max - 3) {
        echo " ... ";
      }

      
      $tmp = rawurlencode($list[$max - 1]);
      $current = ($id === $max - 1 ? "class=\"current\"" : "");
      echo "
        <a href=\"/?$page_esc/$tmp\" $current>$max</a>
      ";

      echo "</span></div>";

      if ($exists) {
        $pic_utf8 = iconv('ISO-8859-1', 'UTF-8//IGNORE', $pic);
        $picture_name = htmlspecialchars(preg_replace("/\.jpg$/", "", $pic_utf8));
        echo "
        <div id=\"pic\">
        <a href=\"/$page_ansi_esc/$pic_esc\">
        <img src=\"/$page_ansi_esc/$pic_esc\" alt=\"$picture_name\" />
        <div id=\"name\">
        $picture_name</div>
        </a>
        </div>
        ";
      } else {
        echo "
        <div id=\"pic\">
        Das Bild '$pic' existiert nicht innerhalb der Galerie '$page'.
        </div>
        ";
      }

    } else {
      echo "
      <p>Die Galerie '$page' ist nicht verf&uuml;gbar.
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
          $entry = iconv('ISO-8859-1', 'UTF-8//IGNORE', $entry);
          $list[] = $entry;
        }
      }
      
      sort($list);

      $current = (!$page ? "class=\"current\"" : "");

      echo "<a href=\"/\" $current>Startseite</a><br>";

      foreach ($list as $val) {
        $current = ($page === $val ? "class=\"current\"" : "");
        $val_esc = rawurlencode($val);
        echo "<a href=\"/?$val_esc\" $current>$val</a>";
      }
      
      closedir($handle);
    }

    echo "</div>";
    if ($page) {
// gallery page
      echo "<div id=\"container\">\n";

      include 'thumbs/make.php';

      if (($handle = opendir($page_ansi))) {
  //      echo "<h2>$gallery</h2>";

        $list = array();

        if (!is_dir("thumbs")) {
          mkdir("thumbs");
        }

        if (!is_dir("thumbs/$page_ansi")) {
          mkdir("thumbs/$page_ansi");
        }

// failsafe code for broken names
        if (($newname = $_GET["rename"])) {
          
          rename($page, $newname);
          echo "'$page' renamed to '$newname'";
          exit;
        }
        
        while ( !!($entry = readdir($handle) )) {
          if (is_file("$page_ansi/$entry")) {
            if (!file_exists("thumbs/$page_ansi/$entry")) {
              createthumb("$page_ansi/$entry");
            }

            if (file_exists("thumbs/$page_ansi/$entry")) {
              $list[] = $entry;
            }
          }
        }

        sort($list);

        foreach ($list as $val) {
          $val_utf8 = iconv('ISO-8859-1', 'UTF-8//IGNORE', $val);
          $val_esc = rawurlencode($val);
          $val_html = htmlspecialchars($val_utf8);
          echo "<a href=\"/?$page_esc/$val_esc\"><img src=\"thumbs/$page_ansi_esc/$val_esc\" alt=\"$val_html\" class=\"item\"/></a>\n";
        }

      closedir($handle);

      } else {
        echo "Galerie existiert nicht.\n";
      }

      echo "<br class=\"clear\" />\n";
      echo "</div>\n";
    } else {
 // home/contact page

      $hometext = file_get_contents('welcome.txt');
      $mailaddress = file_get_contents('contact.txt');

      echo "
      <div id=\"home\">
      <h2>Thomas B&ouml;ttcher <span>Chemnitz</span></h2>
      <img src=\"Boettch.jpg\" alt=\"Thomas B&ouml;ttcher\" />
      <p>
$hometext
      </p>
      <p>
      <span class=\"contact\">Kontakt:</span>
      <span class=\"mail\">
$mailaddress
      </span>
      </p>
<a rel=\"license\" href=\"http://creativecommons.org/licenses/by-nc/3.0/de/\"><img alt=\"Creative Commons License\" style=\"border-width:0\" src=\"http://i.creativecommons.org/l/by-nc/3.0/de/88x31.png\" /></a><br />This <span xmlns:dct=\"http://purl.org/dc/terms/\" href=\"http://purl.org/dc/dcmitype/StillImage\" rel=\"dct:type\">work</span> by <span xmlns:cc=\"http://creativecommons.org/ns#\" property=\"cc:attributionName\">Thomas Böttcher</span> is licensed under a <a rel=\"license\" href=\"http://creativecommons.org/licenses/by-nc/3.0/de/\">Creative Commons Attribution-NonCommercial 3.0 Germany License</a>.
      </div>
      ";
    }
  }

?>

<a id="license" rel="license" href="http://creativecommons.org/licenses/by-nc/3.0/de/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc/3.0/de/88x31.png" /></a>

</body>

</html>

