<?php

include ("src/subs.php");
include ("src/maketables.php");

$fd = fopen("langriss.bin", "rb");

for($i=0; $i<32; $i++) {
  $pointers = getpointers("pnt/sc".str_pad($i, 2, "0", STR_PAD_LEFT).".pnt");
  $fo = fopen("text/sc" . str_pad($i, 2, "0", STR_PAD_LEFT) . ".txt", "w");
  for($k=0; $k<count($pointers); $k++) {
    fseek($fd, $pointers[$k]-3, SEEK_SET);
    $temp = hexdec(bin2hex(fread($fd, 1)));
    fputs($fo, $char_names[$temp] . $char_names[0]);
    fseek($fd, $pointers[$k], SEEK_SET);
    $line_ptr = hexdec(bin2hex(fread($fd, 4)));
    fseek($fd, $line_ptr, SEEK_SET);
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