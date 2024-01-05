<?php
require_once('../DevelopSettings.php');
if (ReadDevelopSettings()->CORS_policy == "false"){
    header('Access-Control-Allow-Origin: *');
}
function LgSuccess($id,$ip){
    $con=mysqli_connect("localhost", "home_caizhixiao", "zkfc8wdswf5Dn64x", "home_caizhixiao");
// 检测连接
    $ti=time();
    mysqli_query($con,"UPDATE UserSafety SET LastLoginIp='$ip', LastLoginTime='$ti', WrongTimes=0, Blocked='[none]'
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
function addBlackList($ip){
$servername = "localhost";
$username = "home_caizhixiao";
$password = "zkfc8wdswf5Dn64x";
$dbname = "home_caizhixiao";

$conn = new mysqli($servername, $username, $password, $dbname);
$BID = genCode(10).uniqid();
$sql = "INSERT INTO BlackList (BanID, IP)
VALUES ('$BID','$ip')";
if ($conn->query($sql) === TRUE){
    echo '{"code":200,"text":"success"}';
}else{
    echo '{"code":-200,"text":"failed"}';
}
$conn->close();
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
$BlockID = $_POST["BlockID"];
$type = $_POST["RequestType"];
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
if ($type == "refused"){
    addBlackList($ip);
    $con=mysqli_connect("localhost", "home_caizhixiao", "zkfc8wdswf5Dn64x", "home_caizhixiao");
    mysqli_query($con,"UPDATE UserSafety SET Blocked='[none]'
    WHERE userid='$userid'");
    mysqli_close($con);
}
else{
    LgSuccess($userid,$ip);
    echo '{"code":200,"text":"approved_success"}';
}

?>