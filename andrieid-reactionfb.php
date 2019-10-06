<?php
session_start();
$baomat = strpos($_SERVER['HTTP_REFERER'],'siblog.net'); // sửa siblog.net lại domain blog của bạn.
if($_SERVER['HTTP_REFERER'] && $baomat > 0){
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
if($_GET['id']){
$id_post = $_GET['id'];
$act = $_GET['act'];
$unact = $_GET['unact'];
$host = "localhost";
$username = ""; //phần này tạo database sẽ có và nhập vào thôi.
$password = ""; // nhập...
$dbname = ""; // nhập...
$connect = mysqli_connect($host, $username, $password, $dbname);
if(mysqli_connect_errno()){
echo "Failed to connect to MySQL: ".mysqli_connect_error();
}
mysqli_query($connect,"SET NAMES utf8");
mysqli_query($connect,"CREATE TABLE IF NOT EXISTS `Poster`(
`id` bigint(11) NOT NULL AUTO_INCREMENT,
`LIKE` int(11) NOT NULL,
`LOVE` int(11) NOT NULL,
`HAHA` int(11) NOT NULL,
`WOW` int(11) NOT NULL,
`SAD` int(11) NOT NULL,
`ANGRY` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
");
$check = mysqli_num_rows(mysqli_query($connect,"SELECT id FROM Poster WHERE id = '{$id_post}'"));
if($check == 0){
//API lần đầu sử dụng hệ thống
mysqli_query($connect,"INSERT INTO Poster SET
`id` = '".$id_post."',
`LIKE` = '0',
`LOVE` = '0',
`HAHA` = '0',
`WOW` = '0',
`SAD` = '0',
`ANGRY` = '0'");
}else{
$get_sql = mysqli_query($connect,"SELECT * FROM Poster WHERE id = '{$id_post}'");
$data = mysqli_fetch_array($get_sql, MYSQLI_ASSOC);
//tăng like
if($act && ($act == 'LIKE' || $act == 'LOVE' || $act == 'HAHA' || $act == 'WOW' || $act == 'SAD' || $act == 'ANGRY'))
mysqli_query($connect,"UPDATE Poster SET `$act` = '".($data[$act]+1)."' WHERE `id` = '".$id_post."'");
if($unact && ($unact == 'LIKE' || $unact == 'LOVE' || $unact == 'HAHA' || $unact == 'WOW' || $unact == 'SAD' || $unact == 'ANGRY') && $data[$unact] > 0)
mysqli_query($connect,"UPDATE Poster SET `$unact` = '".($data[$unact]-1)."' WHERE `id` = '".$id_post."'");
$get_react = mysqli_query($connect,"SELECT * FROM Poster WHERE id = '{$id_post}'");
$react = mysqli_fetch_array($get_react, MYSQLI_ASSOC);
$count = $react['LIKE']+$react['LOVE']+$react['HAHA']+$react['WOW']+$react['SAD']+$react['ANGRY'];
//get top
$get_api = array(
"LIKE" => (int)$react['LIKE'],
"LOVE" => (int)$react['LOVE'],
"HAHA" => (int)$react['HAHA'],
"WOW" => (int)$react['WOW'],
"SAD" => (int)$react['SAD'],
"ANGRY" => (int)$react['ANGRY']);
arsort($get_api);
$dem = 0;
$danhsach[] = '';
foreach($get_api as $name => $value) {
if($dem < 3 && $value > 0)
$danhsach[$dem] = $name;
$dem++;
}
//end top
if($act !== 'post' && $_SESSION[$id_post] = true){
$api = array(
"reaction" => "$act",
"Reaction" => $get_api,
"Top" => $danhsach,
"count" => $count,
"success" => true);
}
if($act == 'post'){
$_SESSION[$id_post] = true;
$api = array(
"Reaction" => $get_api,
"Top" => $danhsach,
"count" => $count,
"success" => true);
}
}
}else{
$api = ["success" => false];
}
die(json_encode($api));
}else{
die('xảy ra lỗi !');
}
?>
