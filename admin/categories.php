<?php 

require_once '../functions.php';

baixiu_get_current_user();


function add_category () {
  if (empty($_POST['name']) || empty($_POST['slug'])) {
    $GLOBALS['message'] = '请完整填写表单';
    return;
  }

  // 接受并保存
  $name = $_POST['name'];
  $slug = $_POST['slug'];

 $rows = baixiu_execute("insert into categories values (null, '{$slug}', '{$name}');");

 $GLOBALS['message'] = $rows <= 0 ? '添加失败' : '添加成功';
}

function edit_category () {

  global $current_edit_category;
  // if (empty($_POST['name']) || empty($_POST['slug'])) {
  //   $GLOBALS['message'] = '请完整填写表单';
  //   return;
  // }

  // 接受并保存
  $id = $current_edit_category['id'];

  $name = empty($_POST['name']) ? $current_edit_category['name'] : $_POST['name'];
  $current_edit_category['name'] = $name;

  $slug = empty($_POST['slug']) ? $current_edit_category['slug'] : $_POST['slug'];
  $current_edit_category['slug'] = $slug;

 $rows = baixiu_execute("update categories set slug = '{$slug}', name = '{$name}' where id = '{$id}';");

 $GLOBALS['message'] = $rows <= 0 ? '编辑失败' : '编辑成功';
}


if (empty($_GET['id'])) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    add_category();
  }

} else {
    // 客户端通过 URL 传递了一个 ID
    // => 客户端是要来拿一个修改数据的表单
    // => 需要拿到用户想要修改的数据
$current_edit_category = baixiu_fetch_one('select * from categories where id = ' . $_GET['id']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    edit_category();
  }
}






$categories = baixiu_fetch_all('select * from categories;');
var_dump($categories);
// array(4) {
//   [0]=>
//   array(3) {
//     ["id"]=>
//     string(1) "1"
//     ["slug"]=>
//     string(13) "uncategorized"
//     ["name"]=>
//     string(9) "未分类"
//   }






 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
        <?php if (isset($message)): ?>
          <div class="alert alert-danger">
          <strong><?php echo $message ?></strong>
          </div> 
        <?php endif ?>
       
      <div class="row">
        <div class="col-md-4">
            <?php if (isset($current_edit_category)): ?>
              <form action="<?php echo $_SERVER['PHP_SELF'] ?>?id=<?php echo $current_edit_category['id']; ?>" method="post">
            <h2>编辑《<?php echo $current_edit_category['name']; ?>》</h2>
            
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $current_edit_category['name']; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit_category['slug']; ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-success" type="submit">保存</button>
              <a href="/admin/categories.php" class="btn btn-primary" type="submit">取消</a>
            </div>
          </form>
            <?php else: ?>

          <!-- ==================================== -->
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <h2>添加新分类目录</h2>
            
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        <?php endif; ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" style="display: none" href="/admin/category-delete" >批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $item): ?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'] ?>"></td>
                <td><?php echo $item['name'] ?></td>
                <td><?php echo $item['slug'] ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $item['id'] ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/category-delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
            <?php endforeach ?>
              
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php $current_page = 'categories'; ?>
<?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script>
  // 不要重复使用无意义的选择操作，应该采用变量去本地化
    $(function ($) {
      // 在表格中的任意一个 checkbox 选中状态变化时

      var $tbodyCheckboxs = $('tbody input')
      var $btnDelete = $('#btn_delete')

      var allCheckeds = []
      $tbodyCheckboxs.on('change', function () {
        // this.dateset['id']
        // console.log($(this).attr('data-id'))
        // console.log($(this).data('id'))
        var id = $(this).data('id')

        if ($(this).prop('checked')) {
          allCheckeds.include(id) || allCheckeds.push(id)
        } else {
          allCheckeds.splice(allCheckeds.indexOf(id), 1)
        }

        allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
        $btnDelete.prop('search', '?id=' + allCheckeds)
      })
      // # version 1 ============================
      // $tbodyCheckboxs.on('change', function () {
      //   // 有任意个 checkbox 选中就显示，反之隐藏
      //   var flag = false
      //   $tbodyCheckboxs.each(function (i,item) {

      //      //  attr 和 prop 的区别
      //      //  attr 访问的是 元素属性，就是HTML页面肉眼能看到的属性
      //      // prop 访问的是 元素对应的 DOM 对象的属性，比如 a 元素的里面的 DOM 对象还有很多 host属性，hostname属性等等我们看不到的
      //     // console.log($(item).prop('checked'))
      //     if ($(this).prop('checked')) {
      //       flag =true
      //     }
      //   })
      //   flag ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
      // })



      // 找一个合适的时机 做一件合适的事情
      // 全选和全不选============================
      $('thead input').on('change', function () {
          // 1. 获取当前选中状态
          var checked = $(this).prop('checked')
          // 2. 设置给标题中的每一个
          $tbodyCheckboxs.prop('checked', checked)
        })



    })
  </script>
</body>
</html>
