<?php

include ("src/subs.php");
include ("src/maketables.php");

$fd = fopen("langriss.bin", "rb");

$filename = array("classes8n.txt", "items8n.txt", "names8n.txt");
$offset   = array(0x5e6d6, 0x60364, 0x618e8);
$offend   = array(0x5e94a, 0x603fc, 0x61abc);

for($i=0; $i<count($offset); $i++) {
  $pointers = array();
  fseek($fd, $offset[$i], SEEK_SET);
  $m=0;
  $pointers[$m] = hexdec(bin2hex(fread($fd, 4)));
  $m++;
  while(ftell($fd) < $offend[$i]) {
    $pointers[$m] = hexdec(bin2hex(fread($fd, 4)));
    $m++;
  }
  
  $fo = fopen("text/".$filename[$i], "w");
  for($k=0; $k<count($pointers); $k++) {
    fseek($fd, $pointers[$k], SEEK_SET);
    $next_block = 0;
    while($next_block == 0) {
      $switch = fread($fd, 1);
      if(hexdec(bin2hex($switch)) == 0xff) {
        fputs($fo, "{end}\r\n");
        $next_block++;
      }
      else fputs($fo, $switch);
    }
    unset($temp, $line_ptr, $next_block);
  }
  fclose($fo);
}

?>