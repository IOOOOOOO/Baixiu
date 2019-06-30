<?php

require_once '../functions.php';

baixiu_get_current_user();


 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style=display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right">
       
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <!-- <tr class="danger">
            <td class="text-center"><input type="checkbox"></td>
            <td>大大</td>
            <td>楼主好人，顶一个</td>
            <td>《Hello world》</td>
            <td>2016/10/07</td>
            <td>未批准</td>
            <td class="text-center">
              <a href="post-add.php" class="btn btn-info btn-xs">批准</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr> -->

        </tbody>
      </table>
    </div>
  </div>
<?php $current_page = 'comments'; ?>
 <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script id="comments_tmpl" type="text/x-jsrender">
    {{for suibian}}
      <tr {{if status == 'held'}} class="warning"
      {{else status == 'rejected'}} class="danger"
      {{/if}}
      data-id="{{:id}}">
      <td class="text-center"><input type="checkbox"></td>
        <td>{{:author}}</td>
        <td>{{:content}}</td>
        <td>{{:post_title}}</td>
        <td>{{:created}}</td>
        <td>{{:status}}</td>
        <td class="text-center">
          {{if status == 'held'}}
          <a href="post-add.php" class="btn btn-info btn-xs">批准</a>
          <a href="post-add.php" class="btn btn-warning btn-xs">拒绝</a>
          {{/if}}
          <a href="javascript:;" class="btn btn-danger btn-xs btn-delete">删除</a>
        </td>
      </tr>
    {{/for}}
  </script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>

  <script>

  $(document)
   .ajaxStart(function () {
     NProgress.start()
   })
   .ajaxStop(function () {
     NProgress.done()
   })
   

    var current_page = 1
    function loadPageData (page) { 
       // 发送 AJAX 请求获取列表所需数据
      
      $.getJSON('/admin/api/comments.php', { page: page}, function (res) {
          if (page > res.total_pages) {
            loadPageData(res.total_pages)
            return
          }
        $('.pagination').twbsPagination('destroy')
        $('.pagination').twbsPagination({
              first: '&laquo;',
              last: '&raquo;',
              prev: '&lt;',
              next: '&gt;',
              startPage: page,
              totalPages: res.total_pages,
              visiablePages: 5,
              initiateStartPageClick: false,
              // 下面是给页码注册点击事件
              onPageClick: function (e,page) {
                loadPageData(page)
              }

            })
      // 拿过来的 res 现在是一个数组了
      // 请求发送出去啦，不知道什么什么时候回来，回来的话就执行下面的代码，这样就不用等了
        console.log(res)
        var html = $('#comments_tmpl').render({
          suibian: res.comments
        })
        
        $('tbody').html(html).fadeIn()
      // 将数据渲染到页面上
        current_page = page
      })
    }

    loadPageData(current_page)



    $('tbody').on('click', '.btn-delete', function (){
      // 删除单条数据的时机
      // 1. 拿到需要删除的数据 ID
      var id = $(this).parent().parent().data('id')
      // 2. 发送一个 AJAX 请求 告诉服务端要删除哪一条具体的数据
      $.get('/admin/api/comment-delete.php', {id:id}, function (res) {
        if (!res) return
          loadPageData(current_page)
      })
      // 3. 根据服务端返回的删除是否成功决定是否再界面上移除这个元素
    })
    

  </script>
  <script>NProgress.done()</script>
</body>
</html>
