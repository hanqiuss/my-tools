<?php
$pw  = hex2bin('');
$a   = [ 0x7E, 0x46, 0x40, 0x37, 0x25, 0x6d, 0x24, 0x7e];
$fc0 = 0x71;
$len = strlen($pw);
$pi  = array_map(fn($x)=>ord($x),str_split($pw));
$pi[0] = $pi[0] ^ $fc0;
while (count($pi)>count($a)) {
    $a=array_merge($a,$a);
}
$ret = '';
for ($i=0; $i < $len-1; $i++) { 
    $chr = ( $pi[$i+1] ^ $a[$i] ) - $pi[$i];
    $chr = $chr > 0 ? $chr : ( $chr + 255 );
    $ret .= chr($chr);
}
var_dump($ret);
