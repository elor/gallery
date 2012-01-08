<pre>
<?php
  if (($handle = opendir('.'))) {
    $list = array();
    while ( !!($dir = readdir($handle) )) {
      if (is_dir($dir) && $dir[0] !== '.') {
        
        if (($filehandle = opendir($dir))) {
          $list = array();
          while ( !!($file = readdir($filehandle) )) {
            if (!is_dir($file)) {
              echo("$dir/$file\n");
              unlink("$dir/$file");
            }
          }
        }

        echo("$dir\n");
        rmdir($dir);
      }
    }
  }
  
  exit;
?>
</pre>

