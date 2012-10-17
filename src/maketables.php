<?php

// Bank 1
  $fd = fopen ("tbl/tbl00.txt", "rb");
  $fddump = fread ($fd, filesize ("tbl/tbl00.txt"));
  fclose ($fd);
  $k=0;
  for ($i = 0; $i < strlen($fddump); $i = $i+2) {
    $font0[$k] = substr($fddump, $i, 2);
    $k++;
  }

// Bank 2
  $fd = fopen ("tbl/tbl01.txt", "rb");
  $fddump = fread ($fd, filesize ("tbl/tbl01.txt"));
  fclose ($fd);
  $k=0;
  for ($i = 0; $i < strlen($fddump); $i = $i+2) {
    $font1[$k] = substr($fddump, $i, 2);
    $k++;
  }

// Bank 3
  $fd = fopen ("tbl/tbl02.txt", "rb");
  $fddump = fread ($fd, filesize ("tbl/tbl02.txt"));
  fclose ($fd);
  $k=0;
  for ($i = 0; $i < strlen($fddump); $i = $i+2) {
    $font2[$k] = substr($fddump, $i, 2);
    $k++;
  }

// Bank 4
  $fd = fopen ("tbl/tbl03.txt", "rb");
  $fddump = fread ($fd, filesize ("tbl/tbl02.txt"));
  fclose ($fd);
  $k=0;
  for ($i = 0; $i < strlen($fddump); $i = $i+2) {
    $font3[$k] = substr($fddump, $i, 2);
    $k++;
  }

// Names
  $fd = fopen ("tbl/names.txt", "rb");
  $fddump = fread ($fd, filesize ("tbl/names.txt"));
  fclose ($fd);
  $names = split ("\r\n", $fddump);
  for ($l = 0; $l < count ($names); $l++) {
    $char_names[$l] = trim($names[$l]);
  }

?>
