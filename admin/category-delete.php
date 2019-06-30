<?php

// 根据客户端传递过来的 ID 删除对应数据

require_once '../functions.php';

if (empty($_GET['id'])) {
  exit('缺少必要参数');
}

$id = $_GET['id'];
// => '1 or 1 =1'
// sql 注入 

$rows = baixiu_execute("delete from categories where id in ('{$id}');");

header('Location: /admin/categories.php');