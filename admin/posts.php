<?php

require_once '../functions.php';

baixiu_get_current_user();


// 处理分页参数=====================================

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$page = $page < 1 ? 1 : $page;
$size = 6;
$total_count = (int)baixiu_fetch_one("
  select count(1) as count from posts
  inner join categories on posts.category_id = categories.id
  inner join users on posts.user_id = users.id;")['count'];
$total_pages = (int)ceil($total_count / $size);

$page = $page > $total_pages ? $total_pages : $page;


// 接受筛选参数==================================

$where = '1 = 1';
$search = '';


//分类筛选
if (isset($_GET['category']) && $_GET['category'] !== 'all') {
  $where .= ' and posts.category_id = ' . $_GET['category'];
  $search .= '&category=' . $_GET['category'];
}

if (isset($_GET['status']) && $_GET['status'] !== 'all') {
  $where .= " and posts.status = '{$_GET['status']}'";
  $search .= '&status=' . $_GET['status'];

}

// where => " 1=1 and postsss.category_id = 1 and posts.status = 'published'"
// search => "&category=1&status"



// 计算越过多少条
$offset = ($page - 1) * $size;


// 获取全部数据展示====================================
$posts = baixiu_fetch_all("select 
  posts.id,
  posts.title,
  posts.created,
  posts.status,
  categories.name as categories_name,
  users.nickname as users_nickname
from posts 
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where {$where}
order by posts.created desc
limit {$offset}, {$size};
");


$categories = baixiu_fetch_all("select * from categories");
// 处理分页页码===================================

// 求出最大页码
//  最大页数等于 数据总数 / 每页展示多少条
// 最大页数  $tatal_page = ceil($total / $size)



$visiables = 5;
// 计算最大和最小的展示的页码
$begin = $page - ($visiables - 1) / 2;
$end = $begin + $visiables - 1;

// 重点考虑合理性的问题
// begin > 0 end <= total_pages
// -2 -1 0 1 2    当pages=1的时候  begin=-1，end=3
$begin = $begin < 1 ? 1 : $begin;//确保begin不会小于1
$end = $begin + $visiables - 1; // 因为 1-2=-1   范围会在-1 0 1 2 3   ，但是begin=1了，end不能还是3 ，这样就成了 1 2 3 了

// 48 49 50 51 52 当pages=51的时候，begin=49，end=53
$end = $end > $total_pages ? $total_pages : $end;  //确保了 end 不会大于total_pages
$begin = $end - ($visiables - 1); // 因为

//-1 0 1 2 3，当peges=2的时候，begin先=0 ，然后小于1，就等于1，end就=5，但是最大页为3，所以end就=3，然后到上面这条代码，begin又-4变回-1，所以最后要把begin变回1
$begin = $begin < 1 ? 1 : $begin;





// 重点考虑合理性的问题
// begin > 0 and <= total_pages
// 计算页码开始
// $visiables = 5;
// $region = ($visiables - 1) / 2;  // 左右区间
// $begin = $page - $region;   // 开始页码
// $end = $begin + $visiables;  // 结束页码 +
// // 可能出现 $begin 和 $end 的不合理情况
// // $begin 必须 > 0
// // 确保 $begin 最下面为1
// if ($begin < 1) {
//   $begin = 1;
//   $end = $begin + $visiables;
// }

// // $end 必须 <= 最大页数
// if ($end > $total_pages + 1) {
//   // end 超出范围 
//   $end = $total_pages + 1;
//   $begin = $end - $visiables;
//   if ($begin < 1) {
//   $begin = 1;
//   }
// }

// 处理数据格式转换================================


/**
 * 转换状态显示
 * @param  string $status 英文单词
 * @return string         中文词语
 */
function convert_status ($status) {
  $dict = array (
    'published' => '已发布',
    'drafted' => '草稿',
    'trashed' => '回收站',
  );
  return isset($dict[$status]) ? $dict[$status] : '未知';
}

/**
 * 转换时间格式
 * @param  2014-04-24
 * @return 2014年04月24日
 */
function convert_date ($created) {
  // 2017-07-01 08:08:00  
  $timestamp = strtotime($created);
  return date('Y年m月d日<b\r>H:i:s', $timestamp);
}


// 查询数据库太多，打开很卡
// function get_category ($category_id) {
//   return baixiu_fetch_one("select name from categories where id = '{$category_id}';")['name'];
// }

// function get_user ($user_id) {
//   return baixiu_fetch_one("select nickname from users where id = '{$user_id}';")['nickname'];
// }




?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style=display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $item): ?>
              <option value="<?php echo $item['id']; ?>" <?php echo isset($_GET['category']) && $_GET['category'] == $item['id'] ? ' selected ' : ''; ?>><?php echo $item['name']; ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted"<?php echo isset($_GET['status']) && $_GET['status'] == 'drafted' ? ' selected ' : ''; ?>>草稿</option>
            <option value="published"<?php echo isset($_GET['status']) && $_GET['status'] == 'published' ? ' selected ' : ''; ?>>已发布</option>
            <option value="trashed"<?php echo isset($_GET['status']) && $_GET['status'] == 'trashed' ? ' selected ' : ''; ?>>回收站</option>
            foreach

          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
         <?php baixiu_pagination($page,$total_pages, '?page=%d' . $search); ?>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
            <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $item['title']; ?></td>
            <td><?php echo $item['users_nickname']; ?></td>
            <td><?php echo $item['categories_name']; ?></td>
            <td class="text-center"><?php echo convert_date($item['created']); ?></td>
            <!-- 一旦输出的判断或者转换逻辑过于复杂，不建议直接卸载混编位置 -->
            <td class="text-center"><?php echo convert_status($item['status']); ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/posts-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php $current_page = 'posts'; ?>
<?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
