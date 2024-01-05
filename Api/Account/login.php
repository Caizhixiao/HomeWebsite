<?php
//var at="a b c d e f g h i j k l m n o p q r s t u v w x y z";var atsl=at.split(" ");var c="";for (var i=0;i<16;i++){c+=atsl[Math.floor(Math.random()*26)]}
require_once('ipBelong.php');
require_once 'vendor/autoload.php';
require_once('../DevelopSettings.php');
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
function LgSuccess($id,$ip){
    $con=mysqli_connect("localhost", "home_caizhixiao", "zkfc8wdswf5Dn64x", "home_caizhixiao");
// 检测连接
    $ti=time();
    mysqli_query($con,"UPDATE UserSafety SET LastLoginIp='$ip', LastLoginTime='$ti', WrongTimes=0
    WHERE userid='$id'");

    mysqli_close($con);
}
function LgFail($id){
    $con=mysqli_connect("localhost", "home_caizhixiao", "zkfc8wdswf5Dn64x", "home_caizhixiao");
// 检测连接
    $ti=time();
    mysqli_query($con,"UPDATE UserSafety SET WrongTimes=WrongTimes+1
    WHERE userid='$id'");

    mysqli_close($con);
}
function AccountBlock($userid,$banid,$ip){
    $con=mysqli_connect("localhost", "home_caizhixiao", "zkfc8wdswf5Dn64x", "home_caizhixiao");
// 检测连接
    $ti=time();
    $banstr = $banid . ',' . $ip;
    mysqli_query($con,"UPDATE UserSafety SET Blocked='$banstr'
    WHERE userid='$userid'");

    mysqli_close($con);
}
function findBannedIP($ip){
    $conn = new mysqli("localhost", "home_caizhixiao", "zkfc8wdswf5Dn64x", "home_caizhixiao");
    // Check connection
    if ($conn->connect_error) {
        return true;
    } 
     
    $sql = "SELECT BanID, IP FROM BlackList";
    $result = $conn->query($sql);
    $find = 0;
    $user_info = [];
    while($row = $result->fetch_assoc()) {
        if ($row["IP"] == $ip){
            return true;
        }
    }
    $conn->close();
    return false;
}
function LglogO($det){
$servername = "localhost";
$username = "home_caizhixiao";
$password = "zkfc8wdswf5Dn64x";
$dbname = "home_caizhixiao";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // 设置 PDO 错误模式为异常
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    // 预处理 SQL 并绑定参数
    $stmt = $conn->prepare("INSERT INTO OperateLog (OperateID, UID, AppID ,Access ,OperateTime, Details) 
    VALUES (:oid, :uid, :aid, :acc, :opt, :det)");
    $oid = genCode(5).time().uniqid().genCode(5);
    $ac = 'LOGIN';
    $stmt->bindParam(':oid', $oid);
    $stmt->bindParam(':uid', $ac);
    $stmt->bindParam(':aid', $ac);
    $stmt->bindParam(':acc', $ac);
    $stmt->bindParam(':opt', time());
    $stmt->bindParam(':det', $det);
    $stmt->execute();
}
catch(PDOException $e)
{
    echo '{"code":400,"text":"数据缺失!"}';
    return ;
}
}
$username = $_POST["username"];
$password = $_POST["password"];
if (filterString($username) != $username || filterString($password) != $password ){
    echo '{"code":-403,"text":"用户名或密码含非法字符或字符段!"}';
    return ;
}
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
$conn = new mysqli("localhost", "home_caizhixiao", "zkfc8wdswf5Dn64x", "home_caizhixiao");
// Check connection
if ($conn->connect_error) {
    echo '{"code":-403,"text":"网络错误!"}';
} 
 
$sql = "SELECT uid, username, pwd ,access ,dec_pwd FROM users";
$result = $conn->query($sql);
$find = 0;
$user_info = [];
if ($result->num_rows > 0) {
    // 输出数据
    while($row = $result->fetch_assoc()) {
        if ($username == $row["username"] && md5($password) == $row["pwd"]){
            $find = 1;
            $key = $row["dec_pwd"];
            $iv = strrev($row["dec_pwd"]);
            $aes = new AES($key,$iv);
            $user_info = [
                "token" => $aes->encrypt($row["uid"]."-".$row["username"]."-".$row["pwd"]),
                "userid" => $row["uid"],
                "accessToken" => $aes->encrypt($row["access"]),
                "code" => 200,
                "text" => "成功"
            ];
        }
        if ($username == $row["username"] && md5($password) != $row["pwd"]){
            LgFail($row["uid"]);
        }
    }
}

if (!$find){
    echo '{"code":-200,"text":"用户名或密码错误!"}';
}
$conn->close();
if (!$find){
    $log_str = $ip.'%'.$username.'-'.$password.'%fail';
    LglogO($log_str);
    exit;
}
$conn = new mysqli("localhost", "home_caizhixiao", "zkfc8wdswf5Dn64x", "home_caizhixiao");
// Check connection
if ($conn->connect_error) {
    echo '{"code":-403,"text":"网络错误!"}';
} 
 
$sql = "SELECT userid, email, LastLoginIp ,LastLoginTime ,WrongTimes,Blocked FROM UserSafety";
$result = $conn->query($sql);
$find = 0;
if ($result->num_rows > 0) {
    // 输出数据
    while($row = $result->fetch_assoc()) {
        if ($user_info["userid"] == $row["userid"]){
            if ($row["Blocked"] != "[none]"){
                echo '{"code":900,"text":"已发送邮件至'.substr($row["email"], 0, 3).'****@'.substr($row["email"],strpos($row["email"], '@')+1).'"}';
                LglogO($ip.'%'.$username.'-'.$password.'%fail');
                exit;
            }
            else{
                $res = ipBelong($ip);
                if ($ip == $row["LastLoginIp"] && $row["WrongTimes"] <= 10){
                    //放行
                    LgSuccess($user_info["userid"],$ip);
                    echo json_encode($user_info);
                    LglogO($ip.'%'.$username.'-'.$password.'%success');
                    exit;
                    
                }else{
                    $res1 = $res;
                    $res = explode(" ",$res);
                    if ($res[2] == "上海" && $row["WrongTimes"] <= 5){
                        //放行
                        LgSuccess($user_info["userid"],$ip);
                        echo json_encode($user_info);
                        LglogO($ip.'%'.$username.'-'.$password.'%success');
                        exit;
                    }
                    // 实例化PHPMailer类
                    $mail = new  PHPMailer\PHPMailer\PHPMailer();
                    // 设置SMTP服务器
                    $mail->isSMTP();
                    $mail->SMTPDebug = 2;
                    $mail->Host = 'smtp.163.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'homeca1@163.com';
                    $mail->Password = 'WIBOGSMOTRPFOBSS';
                    $mail->SMTPSecure = 'ssl';
                    $mail->SMTPDebug = false;
                    $mail->Port = 465;
                    // 设置发件人和收件人
                    $mail->setFrom('homeca1@163.com', '账户安全中心');
                    $mail->addAddress($row["email"], '');
                    // 设置邮件内容
                    $mail->Subject = '账户被锁定';
                    $iden = genCode(5).uniqid().genCode(5);
                    $refuesd = 'https://home.caizhixiao.com.cn/email_refused/'.$iden;
                    $accept = 'https://home.caizhixiao.com.cn/email_accepted/'.$iden;
                    $mail->CharSet = 'UTF-8';
                    $mail->ContentType='text/html';
                    $mail->isHTML(true);
                    $msg = '您的账户与'.date('Y年m月d日 H:i:s',time()).
                    "位于".$res1.'('.$ip.') 尝试登录';
                    $site_src='https://home.caizhixiao.com.cn/AskDecision/'.$iden.'?msg='.$msg;
                    $mail->Body = $msg.'<br/<br/><a href="'.$site_src.'">前去查看</a>';
                    ;
                    AccountBlock($row["userid"],$iden,$ip);
                    // 发送邮件
                    if (!$mail->send()) {
                        echo '{"code":909,"text":"邮件发送失败!"}';
                    } else {
                        echo '{"code":900,"text":"已发送邮件至'.substr($row["email"], 0, 3).'****@'.substr($row["email"],strpos($row["email"], '@')+1).'"}';
                    }
                    LglogO($ip.'%'.$username.'-'.$password.'%blocked');
                }
            }
        }
    }
}
?>