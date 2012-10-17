<?php

function getpointers($in_file) {
  $fd = fopen($in_file, "rb");
  $fddump = rtrim(fread($fd, filesize($in_file)));
  return (split("\r\n", $fddump));
}  

?>
