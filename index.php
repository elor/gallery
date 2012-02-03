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

// Extraction done

  $title = ($page ? $page : "Startseite");
  echo "<title>Thomas B&ouml;ttcher Chemnitz - $title</title>";
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

    if (($handle = opendir($page))) {

      $list = array();

      while ( !!($entry = readdir($handle) )) {
        if (is_file("$page/$entry")) {
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
        <a class=\"go left\" href=\"/?$page_esc\">zur&uuml;ck</a>
      ";

      if ($id !== 0) {
        $tmp = rawurlencode($list[$id - 1]);
        echo "
          <a class=\"go left\" href=\"/?$page_esc/$tmp\">&lt;&lt;</a>
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
        $picture_name = htmlspecialchars(preg_replace("/\.jpg$/", "", $pic));
        echo "
        <div id=\"pic\">
        <a href=\"/$page_esc/$pic_esc\">
        <img src=\"/$page_esc/$pic_esc\" alt=\"$picture_name\" />
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

      if (($handle = opendir($page))) {
  //      echo "<h2>$gallery</h2>";

        $list = array();

        if (!is_dir("thumbs")) {
          mkdir("thumbs");
        }

        if (!is_dir("thumbs/$page")) {
          mkdir("thumbs/$page");
        }

// failsafe code for broken names
        if (($newname = $_GET["rename"])) {
          
          rename($page, $newname);
          echo "'$page' renamed to '$newname'";
          exit;
        }
        
        while ( !!($entry = readdir($handle) )) {
          if (is_file("$page/$entry")) {
            if (!file_exists("thumbs/$page/$entry")) {
              createthumb("$page/$entry");
            }

            if (file_exists("thumbs/$page/$entry")) {
              $list[] = $entry;
            }
          }
        }

        sort($list);

        foreach ($list as $val) {
          $val_esc = rawurlencode($val);
          $val_html = htmlspecialchars($val);
          echo "<a href=\"/?$page_esc/$val_esc\"><img src=\"thumbs/$page_esc/$val_esc\" alt=\"$val_html\" class=\"item\"/></a>\n";
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
      <img src=\"Boettch.jpg\" alt=\"Thomas B&ouml;ttcher\" />
      <p>
Schon als Kind vom Bilder- u. Fotovirus infiziert, hat dieses Medium für mich nichts an Bedeutung und Reiz verloren. Früher mit den verschiedensten manuellen Analogkameras unterwegs, jetzt  digital, wodurch sich völlig neue Möglichkeiten eröffnen. Ständig fasziniert von Lichtstimmungen, Farbkombinationen und spannenden Details, immer bestrebt all diese herrlichen Entdeckungen im Bild fest zu halten. Jedes gute Motiv erzählt seine eigene Geschichte, die sich häufig nur dem aufmerksamen Auge erschließt. Durch eine gezielte Bearbeitung bzw. Entwicklung wird dieses Geheimnis auch für Andere sichtbar.
      </p>
      <p>
      <span class=\"contact\">Kontakt:</span>
      <span class=\"mail\">
&#98;&#108;&#97;&#117;&#98;&#97;&#101;&#114;&#54;&#53;<span></span>&#64;&#119;&#101;&#98;&#46;&#100;&#101;
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

