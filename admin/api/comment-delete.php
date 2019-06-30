<?php

// 根据客户端传递过来的 ID 删除对应数据

require_once '../../functions.php';

if (empty($_GET['id'])) {
  exit('缺少必要参数');
}

$id = $_GET['id'];
// => '1 or 1 =1'
// sql 注入 

$rows = baixiu_execute("delete from comments
  where id in ('{$id}');");

// 之前是页码请求，返回网页
// header('Location: ' . $_SERVER['HTTP_REFERER']);

// 现在是 AJAX 请求，直接返回数据
header('Content-Type: application/json');

$json = json_encode($rows > 0);

echo $json;