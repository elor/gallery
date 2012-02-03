<!DOCTYPE html>
<html lang="de">

<head>
<meta charset="utf-8" />
<?php
// Extract gallery and pic names from query string
  $page = "";
  $pic = "";
  $query = $_SERVER["QUERY_STRING"];

  $components = explode('/', $query);
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

