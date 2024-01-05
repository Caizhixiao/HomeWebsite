<?php
function ReadVersionControler($var,$var_father){
    if ($handle = fopen('/www/wwwroot/home/VersionControler.settings', "r")) {
        $line = "";
        while (($line = fgets($handle)) !== false) {
            fclose($handle);
	    if ($var != "all")
	    {
	      if ($var_father == "null") return json_decode($line)->$var;
	      else return json_decode($line)->$var_father->$var;
	    }
	    else return json_decode($line);
        }
    }
    else {
        return -1;
    }
}
echo json_encode(ReadVersionControler($_GET["var"],$_GET["var_father"]));
?>
