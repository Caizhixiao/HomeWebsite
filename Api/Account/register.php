<?php
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
function genCode($num){
    $str = 'a b c d e f g h i j k l m n o p q r s t u v w x y z 1 2 3 4 5 6 7 8 9 0';
    $arr = explode(' ',$str);
    $res = '';
    $res = $res . $arr[rand(26,count($arr)-1)];
    for ($i = 2;$i <= $num; $i = $i + 1){
        $res = $res . $arr[rand(0,count($arr)-1)];
    }
    return $res;
}
$servername = "localhost";
$username = "home_caizhixiao";
$password = "zkfc8wdswf5Dn64x";
$dbname = "home_caizhixiao";
$name = $_POST["username"];
$pwd = $_POST["password"];
$access = $_POST["access"];
$key = $_POST["reg_key"];
header('Access-Control-Allow-Origin: *');
if (filterString($name) != $name || filterString($pwd) != $pwd || filterString($access) != $access || filterString($key) != $key){
    echo '{code:-403,text:"用户名或密码含非法字符或字符段!"}';
    return ;
}
if ($key != 'AbXn_X4z:7y_zHwZ3AkSC4phv-pMFu'){
    echo '{code:403,text:"Key字段缺失或Key是非法的"}';
    return ;
}
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // 设置 PDO 错误模式为异常
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    // 预处理 SQL 并绑定参数
    $stmt = $conn->prepare("INSERT INTO users (uid, username, pwd ,access ,dec_pwd) 
    VALUES (:uid, :username, :pwd, :access, :decpwd)");
    $uid = genCode(2).time()."_".uniqid().genCode(3);
    $dec_pwd = genCode(16);
    $stmt->bindParam(':uid', $uid);
    $stmt->bindParam(':username', $name);
    $stmt->bindParam(':pwd', md5($pwd));
    $stmt->bindParam(':access', $access);
    $stmt->bindParam(':decpwd', $dec_pwd);
    $stmt->execute();
    // 插入行
    echo '{code:200,text:"成功!"}';
}
catch(PDOException $e)
{
    echo '{code:400,text:"数据缺失!"}';
}
$conn = null;
?>