<?php
function ipBelong($ip){
    $request_url = 'https://searchplugin.csdn.net/api/v1/ip/get?ip=';
    $response = file_get_contents($request_url);
    $response = json_decode($response);
    return $response -> data -> address;
}
?>