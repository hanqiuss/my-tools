<?php
function objToArray($obj){
    if(!is_object($obj))return false;
    $str = var_export($obj, true);
    $index = strpos($str,'__set_state(array(');
    if(!$index)return false;
    $index += 12;
    $str = 'return ' . substr($str, $index,-1) . ';';
    return eval($str);
}
