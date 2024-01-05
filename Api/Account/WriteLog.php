<?php
function write_log($name,$text){
    $f=fopen($name,'a');
    $text = $text."\n";
    if ($f){
        fwrite($f,$text);
        fclose($f);
        return 1;
    }else{
        return 0;
    }
}
?>