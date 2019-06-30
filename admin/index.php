<?php 


/// 校验数据当前访问用户的箱子（session）有没有登陆的登陆标识
// session_start();

// if (empty($_SESSION['current_login_user'])) {
//   // 没有当前登陆信息，意味着没有登陆
//   header('Location: /admin/login.php');
// } 
require_once '../functions.php';

// 判断用户是否登录一定是最先去做
baixiu_get_current_user();

// 获取界面所需要的数据
// 重复的操作一定封装起来

$comments_count = baixiu_fetch_all('select count(1) from comments;');
var_dump($comments_count);
/*
array(1) {
  [0]=>
  array(1) {
    ["count(1)"]=>
    string(3) "507"
  }
}
*/
$posts_count = baixiu_fetch_one('select count(1) as num from posts;')['num'];
$comments_count = baixiu_fetch_one('select count(1) as num from comments;')['num'];
$posts_count_published = baixiu_fetch_one("select count(1) as num from posts where status = 'published';")['num'];

/*
array(1) {
  ["num"]=>
  string(4) "1004"
}
*/
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <script src="/static/assets/vendors/chart/chart.js"></script>
  
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $posts_count ?></strong>篇文章（<strong>2</strong>篇草稿）</li>
              <li class="list-group-item"><strong>6</strong>个分类</li>
              <li class="list-group-item"><strong>5</strong>条评论（<strong>1</strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4">
          <canvas id="myChart"></canvas>
        </div>
        <div class="col-md-4"></div>
      </div>
    </div>

  </div>
<?php $current_page = 'index'; ?>
<?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'line',

    // The data for our dataset
    data: {
        labels: ["文章", "评论", "已发表的文章", , ],
        datasets: [{
            label: "数据整理",
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: [<?php echo $posts_count ?>, <?php echo $comments_count ?>, <?php echo $posts_count_published ?>],
        }]
    },

    // Configuration options go here
    options: {}
});
  </script>
</body>
</html>
