<?php

include ("src/subs.php");
include ("src/maketables.php");

$fd = fopen("langriss.bin", "rb");

$filename = array("items.txt", "system.txt", "magic.txt", "units.txt", "names.txt", "credits.txt");
$offset   = array(0x1066, 0x82a92, 0x82ba2, 0x82d1e, 0x9722c, 0xa332a);

for($i=0; $i<count($offset); $i++) {
  $pointers = array();
  fseek($fd, $offset[$i], SEEK_SET);
  $m=0;
  $pointers[$m] = hexdec(bin2hex(fread($fd, 4)));
  $m++;
  while(ftell($fd) < $pointers[0]) {
    $pointers[$m] = hexdec(bin2hex(fread($fd, 4)));
    $m++;
  }
  
  $fo = fopen("text/".$filename[$i], "w");
  for($k=0; $k<count($pointers); $k++) {
    fseek($fd, $pointers[$k], SEEK_SET);
    $next_block = 0;
    while($next_block == 0) {
      $switch = hexdec(bin2hex(fread($fd, 1)));
      if($switch == 0x0    ) fputs($fo, $font0[hexdec(bin2hex(fread($fd, 1)))]);
      elseif($switch == 0x1) fputs($fo, $font1[hexdec(bin2hex(fread($fd, 1)))]);
      elseif($switch == 0x2) fputs($fo, $font2[hexdec(bin2hex(fread($fd, 1)))]);
      elseif($switch == 0x3) fputs($fo, $font3[hexdec(bin2hex(fread($fd, 1)))]);
      elseif($switch ==0xff) {
        $code = hexdec(bin2hex(fread($fd, 1)));
        if($code == 0xf4    ) fputs($fo, "{reboot}");
        elseif($code == 0xf7) {
          $tmp = hexdec(bin2hex(fread($fd, 1)));
          $tmp = hexdec(bin2hex(fread($fd, 1)));
          fputs($fo, $char_names[$tmp]);
          unset($tmp);
        }
        elseif($code == 0xf9) {
          $tmp = hexdec(bin2hex(fread($fd, 1)));
          $tmp = hexdec(bin2hex(fread($fd, 1)));
          fputs($fo, "{portrait:$tmp}");
          unset($tmp);
        }
        elseif($code == 0xfb) fputs($fo, "{fast_clear}\r\n");
        elseif($code == 0xfc) {
          fputs($fo, "{fast_end}\r\n\r\n");
          $next_block++;
        }
        elseif($code == 0xfd) fputs($fo, "{clear}\r\n");
        elseif($code == 0xfe) fputs($fo, "\r\n");
        elseif($code == 0xff) {
          fputs($fo, "{end}\r\n\r\n");
          $next_block++;
        }
      }
    }
    unset($temp, $line_ptr, $next_block);
  }
  fclose($fo);
}

?>