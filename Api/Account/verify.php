<?php
require_once('getBlackList.php');
require_once('../DevelopSettings.php');
if (ReadDevelopSettings()->CORS_policy == "false"){
    header('Access-Control-Allow-Origin: *');
}
$ip = '';
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
} else {
    $ip = 'Unknown';
}
if (isInBlackList($ip)){
    echo '{"code":0,"text":"IP被封禁!"}';
    exit;
}
function filterString($input) {  
    // 移除特殊字符  
    $filteredString = strip_tags($input);  
  
    // 移除引号  
    $filteredString = str_replace(array('"', "'", "`"), '', $filteredString);  
  
    // 移除分号和换行符  
    $filteredString = str_replace(array(';', "\n"), '', $filteredString);  
    
    $filteredString = str_replace(array(',', " "), '', $filteredString);  
    
    $filteredString = trim($filteredString);
    
    $filteredString = (get_magic_quotes_gpc()) ? $filteredString : addslashes($filteredString);
  
    return $filteredString;  
}
class AES {
    private $key;
    private $iv;
 
    public function __construct($key, $iv) {
        $this->key = $key;
        $this->iv = $iv;
    }
 
    public function encrypt($data) {
        $encrypted = openssl_encrypt($data, 'AES-128-ECB', $this->key, OPENSSL_RAW_DATA);
        return base64_encode($encrypted);
    }
 
    public function decrypt($encryptedData) {
        $decrypted = openssl_decrypt(base64_decode($encryptedData), 'AES-128-ECB', $this->key, OPENSSL_RAW_DATA);
        return $decrypted;
    }
 
    public function encryptCBC($data) {
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $this->iv);
        return base64_encode($encrypted);
    }
 
    public function decryptCBC($encryptedData) {
        $decrypted = openssl_decrypt(base64_decode($encryptedData), 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $this->iv);
        return $decrypted;
    }

}
$userid = $_POST["userid"];
$token = $_POST["token"];
$accessToken = $_POST["accessToken"];
if (filterString($userid) != $userid || filterString($token) != $token || filterString($accessToken) != $accessToken ){
    echo '{"code":-403,"text":"含非法字符或字符段!"}';
    return ;
}


// 创建连接
$conn = new mysqli("localhost", "home_caizhixiao", "zkfc8wdswf5Dn64x", "home_caizhixiao");
// Check connection
if ($conn->connect_error) {
    echo '{"code":404,"text":"网络错误!"}';
} 
 
$sql = "SELECT uid, username, pwd ,access ,dec_pwd FROM users";
$result = $conn->query($sql);
$find = 0;
if ($result->num_rows > 0) {
    // 输出数据
    while($row = $result->fetch_assoc()) {
        if ($userid == $row["uid"]){
            $find = 1;
            $key = $row["dec_pwd"];
            $iv = strrev($row["dec_pwd"]);
            $aes = new AES($key,$iv);
            $access = $aes -> decrypt($accessToken);
            $decToken = $aes -> decrypt($token);
            if ($decToken == $row["uid"].'-'.$row["username"].'-'.$row["pwd"]){
                if ($access == $row["access"]){
                    echo '{"code":200,"text":"成功!"}';
                }
                else{
                    echo '{"code":-200,"text":"accessToken是非法的!"}';
                }
            }
            else{
                echo '{"code":-200,"text":"token是非法的!"}';
            }
        }
    }
}
if (!$find){
    echo '{"code":-200,"text":"UID是非法的!"}';
}
$conn->close();

?>