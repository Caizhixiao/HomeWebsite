<?php
function isInBlackList($ip){
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
?>