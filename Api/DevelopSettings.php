<?php
function ReadDevelopSettings(){
    if ($handle = fopen('/www/wwwroot/home/Api/develop.settings', "r")) {
        $line = "";
        while (($line = fgets($handle)) !== false) {
            fclose($handle);
            return json_decode($line);
        }
    } 
    else {
        return -1;
    }
}
?>
