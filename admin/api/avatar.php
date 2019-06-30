<?php
//连接上数据库
require_once '../../config.php';
// 根据用户邮箱获取用户头像

// 那边传过来一个邮箱，这里提供一个img地址回去
// email => image

// 1. 接受传递过来的邮箱
if (empty($_GET['email'])) {
  exit('缺少必要参数');
}
$email = $_GET['email'];

// 2. 查询相应的头像地址
$conn = mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);
if (!$conn) {
  exit('连接数据库失败');
}

$res = mysqli_query($conn,"select avatar from users where email = '{$email}' limit 1;");
if (!$res) {
  exit('查询失败');
}

$row = mysqli_fetch_assoc($res);
// 3. echo
// var_dump($row);
echo $row['avatar'];