<?php

include ("src/subs.php");
include ("src/maketables.php");

$fd = fopen("langriss.bin", "rb");

$filename = array("v_prologues.txt", "v_conditions.txt");
$offset   = array(0x9cf7c, 0x98d7a);
$rawdata  = array(0x9b2fc, 0x986c6);
$wrapwidth= array(0, 16);

for($i=0; $i<count($offset); $i++) {
  $pointers = array();
  $tables = array();
  
  fseek($fd, $offset[$i], SEEK_SET);
  $m=0;
  $pointers[$m] = hexdec(bin2hex(fread($fd, 4)));
  $m++;
  while(ftell($fd) < $pointers[0]) {
    $pointers[$m] = hexdec(bin2hex(fread($fd, 4)));
    $m++;
  }
  
  fseek($fd, $rawdata[$i], SEEK_SET);
  $m=0;
  $tables[$m] = hexdec(bin2hex(fread($fd, 4)));
  $m++;
  while(ftell($fd) < $tables[0]) {
    $tables[$m] = hexdec(bin2hex(fread($fd, 4)));
    $m++;
  }
  
  $output = array();
  
  $fo = fopen("text/".$filename[$i], "w");
  
  for($k=0; $k<count($pointers); $k++) {
  	
    fseek($fd, $tables[$k], SEEK_SET);
    $bank_font = array();
    for($h=0; $h<256; $h++) {
      $bank_font[$h] = fread($fd, 2);
    }
    //die(print bin2hex($bank_font[0]));
    
    fseek($fd, $pointers[$k], SEEK_SET);
    $next_block = 0;
    while($next_block == 0) {
      $switch = fread($fd, 1);
      if(hexdec(bin2hex($switch)) != 0xff ) $output[$k] .= $bank_font[hexdec(bin2hex(fread($fd, 1)))];
      else {
        $code = fread($fd, 1);
        if(hexdec(bin2hex($code)) == 0xff ) {
          $output[$k] .= $switch . $code;
          $next_block++;
        }
        elseif(hexdec(bin2hex($code)) == 0xf7 || hexdec(bin2hex($code)) == 0xf9) {
          $output[$k] .= $switch . $code . fread($fd, 2);
        }
        else $output[$k] .= $switch . $code;
      }
    }
    unset($temp, $line_ptr, $next_block);
  }

  for($k=0; $k<count($output); $k++) {
    $temp = $output[$k];
    $charchar=0;
    for($z=0; $z<strlen($temp); $z++) {
      $switch = hexdec(bin2hex($temp[$z]));
      $code = hexdec(bin2hex($temp[$z+1]));
      if($switch == 0x0    ) fputs($fo, $font0[$code]);
      elseif($switch == 0x1) fputs($fo, $font1[$code]);
      elseif($switch == 0x2) fputs($fo, $font2[$code]);
      elseif($switch == 0x3) fputs($fo, $font3[$code]);
      elseif($switch ==0xff) {
        if($code == 0xf4    ) fputs($fo, "{reboot}");
        elseif($code == 0xf7) {
          $tmp = hexdec(bin2hex($temp[$z+3]));
          fputs($fo, $char_names[$tmp]);
          $charchar += (strlen($char_names[$tmp])/2)-1;
          $z+=2;
          unset($tmp);
        }
        elseif($code == 0xf9) {
          $tmp = hexdec(bin2hex($temp[$z+3]));
          fputs($fo, "{portrait:$tmp}");
          $z+=2;
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
      
      $charchar++;
      if($charchar>0 && $wrapwidth[$i]>0) if($charchar % $wrapwidth[$i] == 0) fputs($fo, "\r\n");
    }
  }
  fclose($fo);
}

?>