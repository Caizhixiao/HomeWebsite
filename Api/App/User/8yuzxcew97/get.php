<?php
require_once('getBlackList.php');
require_once('../../../DevelopSettings.php');
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
$names = $_POST["names"];
$nameid_arr = [];
if (filterString($userid) != $userid || filterString($token) != $token || filterString($accessToken) != $accessToken ){
    echo '{"code":-403,"text":"含非法字符或字符段!"}';
    return ;
}


// 创建连接
$conn = new mysqli("localhost", "home_caizhixiao", "zkfc8wdswf5Dn64x", "home_caizhixiao");
// Check connection
if ($conn->connect_error) {
    echo '{"code":404,"text":"网络错误!"}';
    return ;
} 
 
$sql = "SELECT uid, username, pwd ,access ,dec_pwd FROM users";
$result = $conn->query($sql);
$find = 0;
$success = 0;
if ($result->num_rows > 0) {
    // 输出数据
    while($row = $result->fetch_assoc()) {
        if (substr_count($names,$row["username"]) != 0){
            array_push($nameid_arr,$row["uid"]);
        }
        if ($userid == $row["uid"]){
            $find = 1;
            $key = $row["dec_pwd"];
            $iv = strrev($row["dec_pwd"]);
            $aes = new AES($key,$iv);
            $access = $aes -> decrypt($accessToken);
            $decToken = $aes -> decrypt($token);
            if ($decToken == $row["uid"].'-'.$row["username"].'-'.$row["pwd"]){
                if ($access == $row["access"]){
                    $success = 1;
                }
                else{
                    echo '{"code":-200,"text":"accessToken是非法的!"}';
                    return ;
                }
            }
            else{
                echo '{"code":-200,"text":"token是非法的!"}';
                return ;
            }
        }
    }
}
if (!$find){
    echo '{"code":-200,"text":"UID是非法的!"}';
    return ;
}
$conn->close();
if ($success == 0) return ;


$conn = new mysqli("localhost", "home_caizhixiao", "zkfc8wdswf5Dn64x", "home_caizhixiao");
// Check connection
if ($conn->connect_error) {
    echo '{"code":404,"text":"网络错误!"}';
    return ;
} 
 
$sql = "SELECT MedicineID, CreatorID, LastUpdateTime ,BelongTo ,Name, EndBy, DailyUse,Room,Stat,Description,UpdateLog FROM Medicine";
$result = $conn->query($sql);
$resultr = [];
if ($result->num_rows > 0) {
    // 输出数据
    while($row = $result->fetch_assoc()) {
        if (in_array($row["BelongTo"], $nameid_arr)){
            $jso = [
                "id" => $row["MedicineID"],
                "creator" => $row["CreatorID"],
                "LastUpdateTime" => $row["LastUpdateTime"],
                "BelongTo" => $row["BelongTo"],
                "name" => $row["Name"],
                "endby" => $row["EndBy"],
                "usage" => $row["DailyUse"],
                "room" => $row["Room"],
                "stat" => $row["Stat"],
                "description" => $row["Description"],
                "UpdateLog" => $row["UpdateLog"]
            ];
           array_push($resultr,$jso);
        }
    }
}
$conn->close();
$ret = ["code" => 200, "result" => $resultr];
echo json_encode($ret);
return ;
?>