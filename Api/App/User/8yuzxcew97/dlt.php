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
    
    $filteredString = str_replace(array('', " "), '', $filteredString);  
    
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
$med_id = $_POST["medid"];
if (filterString($userid) != $userid || filterString($token) != $token || filterString($accessToken) != $accessToken || filterString($med_id) != $med_id){
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
$success = 0;
$uid = '';
$acc = '';
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
                    $success = 1;
                    $uid = $row["uid"];
                    $acc = $row["access"];
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
if ($success == 0) {
    return ;
}
if ($acc == 'user'){
    echo '{"code":-200,"text":"权限不足!"}';
    return ;
}
$servername = "localhost";
$username = "home_caizhixiao";
$password = "zkfc8wdswf5Dn64x";
$dbname = "home_caizhixiao";
$oqid = '';
header('Access-Control-Allow-Origin: *');
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // 设置 PDO 错误模式为异常
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    // 预处理 SQL 并绑定参数
    $stmt = $conn->prepare("INSERT INTO OperateLog (OperateID, UID, AppID ,Access ,OperateTime, Details) 
    VALUES (:oid, :uid, :aid, :acc, :opt, :det)");
    $oid = genCode(5).time().uniqid().genCode(5);
    $oqid = $oid;
    $aid = '8yuzxcew97';
    $ca = $acc.',admin';
    $det = '删除药品'.$med_id;
    $stmt->bindParam(':oid', $oid);
    $stmt->bindParam(':uid', $userid);
    $stmt->bindParam(':aid', $aid);
    $stmt->bindParam(':acc', $ca);
    $stmt->bindParam(':opt', time());
    $stmt->bindParam(':det', $det);
    $stmt->execute();
}
catch(PDOException $e)
{
    echo '{"code":400,"text":"数据缺失!"}';
    return ;
}
$conn = null;
$servername = "localhost";
$username = "home_caizhixiao";
$password = "zkfc8wdswf5Dn64x";
$dbname = "home_caizhixiao";
$con=mysqli_connect($servername,$username,$password,$dbname);
// 检测连接
if (mysqli_connect_errno())
{
    echo  '{"code":404,"text":"网络错误!"}';
}

mysqli_query($con,"DELETE FROM Medicine WHERE MedicineID='$med_id'");

mysqli_close($con);
echo '{"code":200,"text":"success"}';
?>