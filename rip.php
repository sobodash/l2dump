<?php

include ("src/subs.php");
include ("src/maketables.php");

$infile = $argv[1];
$offset = $argv[2];
if(substr($offset,0,2)=="0x") $offset = hexdec(substr($offset, 2));
$length = $argv[3];
if(substr($length,0,2)=="0x") $length = hexdec(substr($length, 2));
$oufile = $argv[4];

$fd = fopen($infile, "rb");
fseek($fd, $offset, SEEK_SET);
$fo = fopen($oufile, "w");

for($z=0; $z<$length; $z++) {
  $switch = hexdec(bin2hex(fread($fd, 1)));
  $code = hexdec(bin2hex(fread($fd, 1)));
  if($switch == 0x0    ) fputs($fo, $font0[$code]);
  elseif($switch == 0x1) fputs($fo, $font1[$code]);
  elseif($switch == 0x2) fputs($fo, $font2[$code]);
  elseif($switch == 0x3) fputs($fo, $font3[$code]);
  elseif($switch ==0xff) {
    if($code == 0xf4    ) fputs($fo, "{reboot}");
    elseif($code == 0xf7) {
      $tmp = hexdec(bin2hex(fread($fd, 1)));
      $tmp = hexdec(bin2hex(fread($fd, 1)));
      fputs($fo, $char_names[$tmp]);
      $charchar += (strlen($char_names[$tmp])/2)-1;
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
    elseif($code == 0xfe) { fputs($fo, "\r\n"); $charchar = -1; }
    elseif($code == 0xff) {
      fputs($fo, "{end}\r\n\r\n");
      $next_block++;
    }
  }
  $z++;
}

fclose($fo);
fclose($fd);

?>
