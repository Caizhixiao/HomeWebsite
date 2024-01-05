<?php
require_once('../DevelopSettings.php');
require_once('getBlackList.php');
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
if (ReadDevelopSettings()->CORS_policy == "false"){
    header('Access-Control-Allow-Origin: *');
}
function filterString($input) {  
    // 移除特殊字符  
    $filteredString = strip_tags($input);  
  
    // 移除引号  
    $filteredString = str_replace(array('"', "'", "`"), '', $filteredString);  
  
    // 移除分号和换行符  
    $filteredString = str_replace(array(';', "\n"), '', $filteredString);  
    
    $filteredString = str_replace(array('', " "), '', $filteredString);  
     $filteredString = str_replace(array(',', " "), '', $filteredString);  
    
    $filteredString = trim($filteredString);
    
    $filteredString = (get_magic_quotes_gpc()) ? $filteredString : addslashes($filteredString);
  
    return $filteredString;  
}
$BlockID = $_GET["ID"];
if (filterString($BlockID) != $BlockID){
    echo '{"code":-403,"text":"含非法字符或字符段!"}';
    return ;
}
$servername = "localhost";
$username = "home_caizhixiao";
$password = "zkfc8wdswf5Dn64x";
$dbname = "home_caizhixiao";
 
// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    echo '{"code":-404,"text":"网络错误!"}';
} 
 
$sql = "SELECT userid, email, LastLoginIp ,LastLoginTime ,WrongTimes,Blocked FROM UserSafety";
$result = $conn->query($sql);
$findID = 0;
$userid = '';
$ip = '';
    // 输出数据
while($row = $result->fetch_assoc()) {
    if ($row["Blocked"] == "[none]") continue;
    $ban_info = explode(",",$row["Blocked"]);
    if ($BlockID == $ban_info[0]){
        $findID = 1;
        $userid = $row["userid"];
        $ip = $ban_info[1];
    }
}

$conn->close();
if ($findID == 1){
    echo '{"code":200,"text":"询问存在"}';
}else{
    echo '{"code":-200,"text":"询问不存在"}';
}
?>