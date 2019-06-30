<?php

// 封装大家公用的函数

session_start();

require_once 'config.php';
// 获取当前登录用户信息，如果没有获取则自动跳转到登陆页面

// echo function_exits('fn')   判断PHP内置有没有这个函数
function baixiu_get_current_user () {
  if (empty($_SESSION['current_login_user'])) {
    // 没有当前登录用户信息，意味着没有登陆
    // header('Location: /admin/login.php');
    // exit();  // 没有必要再执行之后的代码
    
  }

  return $_SESSION['current_login_user'];
}

// 数据库查询语法，全部，临时目录
// 得到的是一个挺多东西的临时文件，里面有count ，int之类的
function baixiu_fetch_all ($sql) {
  $conn = mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);

  if (!$conn) {
    exit('连接失败');
  }

  $query = mysqli_query($conn,$sql);
  if (!$query) {
    // 查询失败
    return false;
  }

  $result = array();
  
  while ($row = mysqli_fetch_assoc($query)) {
    $result[] = $row;
  }

  return $result;
}

// 数据库查询语法，查询并拿到一条
function baixiu_fetch_one ($sql) {
  $res = baixiu_fetch_all($sql);
  return isset($res[0]) ? $res[0] : null;
}


// 执行一个增删改语句
function baixiu_execute ($sql) {
  $conn = mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);

  if (!$conn) {
    exit('连接失败');
  }

  $query = mysqli_query($conn,$sql);
  if (!$query) {
    // 查询失败
    return false;
  }

  // 获取受影响行数
  $affected_rows = mysqli_affected_rows($conn);

  mysqli_close($conn);

  return $affected_rows;
}


function baixiu_pagination ($page, $total, $format, $visiable = 5) {
  // 计算起始页码
  // 当前页左侧有几个页码数，如果一共是 5 个，则左边是 2 个， 右边是2个
  $left = floor($visiable / 2);
  // 开始页码
  $begin = $page - $left;
  // 确保开始不能小于 1
  $begin = $begin < 1 ? 1 : $begin;
  // 结束页码
  $end = $begin + $visiable - 1;
  // 确保开始不能小于1
  $begin = $begin < 1 ? 1 : $begin;

  // 上一页--------    <  $begin大于1的时候就出现
  if ($page - 1 > 0) {
    printf('<li><a href="%s">&laquo;</a></li>', sprintf($format, $page - 1));
    // 上面这个 $format 是用户传的 /admin/posts.php?page=%d,,,,$d 是右边这个$page-1,得到一个数比如说是5，那么
    // sprintf = posts.php?page=5,
    // 同时上面这个数值再传回 $s，ok，上面的&laquo应该是一个特殊符号<
  }


  //省略号---------- ···
  if ($begin > 1) {
    print('<li class="disabled"><span>···</span></li>');
  }

  //数字页码------- 1 2 3 4 5
  for ($i = $begin; $i <= $end; $i++) { 
    // 经过以上的计算 $i 的类型可能是 float 类型，所以此处用 == 比较合适
    $activeClass = $i == $page ? ' class="active"' : '';
    printf('<li%s><a href="%s">%d</a></li>',$activeClass, sprintf($format, $i), $i);
  }



  // 省略号-------------···
  if ($end < $total) {
    print('<li class="disabled"><span>···</span></li>');
  }


  // 下一页----------------&raquo; ,,可能是>
  if ($page + 1 <= $total) {
    printf('<li><a href="%s">&raquo;</a></li>', sprintf($format, $page + 1));
  }
}