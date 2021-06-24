<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

//Initialization
$func_id = 'send_request';
$message = '';
$messageClass = '';
$iconClass = '';
$error = 0;
$readonly = '';
$clearSessionJs = 0;
$hrefBack = 'home.php';

session_start();

//Get param
$param = getParam();

//Connect DB
$con = openDB();

if (!isset($_SESSION['uid']) || empty($_SESSION)){
    header('location: login.php');
    exit();
}

if ($_SESSION['role'] == 3){
    header('location: not-found.php');
    exit();
}

$isStatus = checkStatusUser($con);
if ($isStatus['lockFlg'] == 1){
    header('Location: error-page.php');
    exit();
}

$thumbnail = 'Chọn file';
$image = $param['image'] ?? '';
$mode = $param['mode'] ?? 'new';
$nid = $param['nid'] ?? '';

if (isset($param['dispFrom'])){
    if ($param['dispFrom'] == 'manage-news') $hrefBack = 'manage-news.php';
}

if ($param) {
    if (isset($param['registFlg']) && $param['registFlg'] == 1) {
        $mes = [];

        if (isset($param['mode']) && $param['mode'] == 'delete'){
            deleteNews($con, $param);
        }

        $targetDir = 'uploads';
        if (!file_exists('uploads')) {
            mkdir($targetDir, 0777, true);
        }
        $targetFile = $targetDir . '/' . basename($_FILES['thumbnail']['name']);
        $allowUpload = true;
        $extensionFile = pathinfo($targetFile, PATHINFO_EXTENSION);
        $allowtypes = ['jpg', 'png', 'jpeg', 'gif'];

        if (isset($_FILES['thumbnail']['name']) && !empty($_FILES['thumbnail']['name'])) {
            $checkImage = getimagesize($_FILES["thumbnail"]["tmp_name"]);
            if ($checkImage != true) {
                $allowUpload = false;
                $mes[] = 'Chỉ được uploads hình ảnh.';
            } elseif (!in_array($extensionFile, $allowtypes)) {
                $allowUpload = false;
                $mes[] = 'Chỉ uploads hình ảnh có đuôi JPG, PNG, JPEG, GIF.';
            }

            if ($_FILES['thumbnail']['size'] > 26214400) {
                $allowUpload = false;
                $mes[] = 'Ảnh không được quá 25MB.';
            }
        }

        if (empty($mes) && $allowUpload == true) {
            if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $targetFile)) {
                if ($mode == 'new') {
                    $isSuccess = insertNews($con, $param, $targetFile);
                    if ($isSuccess) {
                        $_SESSION['message'] = 'Thêm thành công';
                        $_SESSION['messageClass'] = 'alert-success';
                        $_SESSION['iconClass'] = 'fas fa-check';

                        header('Location: manage-news.php');
                        exit();
                    }

                }
            } else {
                $error = 1;
            }

            if ($mode == 'update') {
                $isSuccess = updateNews($con, $param);
                if ($isSuccess) {
                    $_SESSION['message'] = 'Cập nhật thành công';
                    $_SESSION['messageClass'] = 'alert-success';
                    $_SESSION['iconClass'] = 'fas fa-check';

                    header('Location: manage-news.php');
                    exit();
                }
            }
        }
        $message = join('</br>', $mes);
        if (strlen($message)){
            $messageClass = 'alert-danger';
            $iconClass = 'fas fa-ban';
        }
    }
}

$htmlDeleteNews = '';
if (isset($param['nid']) && !empty($param['nid'])){
    $dataNews = getNewsById($con, $param);
    $image = str_replace('uploads/', '', $dataNews['thumbnail']);
    $title = $param['title'] ?? $dataNews['title'];
    $thumbnail = $param['thumbnail'] ?? $image;
    $content = $param['content'] ?? $dataNews['content'];

    $htmlDeleteNews .= <<<EOF
        <a href="javascript:void(0)" class="btn btn-danger" id="btnDelete" title="Xóa bài">
            <i class="fas fa-trash"></i> Xóa
        </a>
EOF;
} else {
    $title = $param['title'] ?? '';
    $thumbnail = $param['thumbnail'] ?? '';
    $content = $param['content'] ?? '';
}

//Message HTML
if(isset($_SESSION['message']) && strlen($_SESSION['message'])){
    $message      .= $_SESSION['message'];
    $messageClass .= $_SESSION['messageClass'];
    $iconClass    .= $_SESSION['iconClass'];
    $_SESSION['message']      = '';
    $_SESSION['messageClass'] = '';
    $_SESSION['iconClass']    = '';
}
$messageHtml  = '';
if(strlen($message)){
    $messageHtml = <<< EOF
        <div class="alert {$messageClass} alert-dismissible">
            <div class="row">
                <div class="icon">
                    <i class="{$iconClass}"></i>
                </div>
                <div class="col-10">
                    {$message}
                </div>
            </div>
        </div>
EOF;
}

//-----------------------------------------------------------
// HTML
//-----------------------------------------------------------
$titleHTML = '';
$cssHTML = '';
$scriptHTML = <<< EOF
<script>
$(document).ready(function (){
    $("#btnClear").on("click", function(e) {
        e.preventDefault();
        var message = "Đặt màn hình tìm kiếm về trạng thái ban đầu?";
        var that = $(this)[0];
        sweetConfirm(1, message, function(result) {
            if (result){
                window.location.href = that.href;
            }
        });
    });
    
    $("#btnDelete").on("click", function(e) {
        e.preventDefault();
        var message = "Bài viết này sẽ bị xoá. Bạn chắc chứ?";
        var form = $(this).closest('form');
        sweetConfirm(1, message, function(result) {
            if (result){
                $('.mode').val('delete');
                form.submit();
            }
        });
    });
    
    if ('{$mode}' == 'update'){
        $('.thumbnail').text('{$thumbnail}');
        $('.image').val('{$thumbnail}');
    }
    
    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('.thumbnail').text(fileName);
        $('.valueImage').val(fileName);   
    });
    
    var valueImage = $('.valueImage').val();
    $('.thumbnail').text(valueImage);
    
    if ({$error} == 1){
        Swal.fire({
            position: 'center',
            icon: 'warning',
            title: 'Upload hình ảnh không thành công',
            showConfirmButton: true,
            timer: 5000
        });
    }
    
    $('#saveNews').on('click', function(e) {
      var mes = [];
      var title = $('.title').val();
      if (!title){
          $('#message').show();
          mes.push('Vui lòng nhập tiêu đề bài viết.'+'</br>');
      }
      
      var valueMess = mes.toString().replace(',', '');
      if (valueMess){
          $('.message').remove();
          $('#showMess').show();
          $('<span class = "message">'+valueMess+'</span>').appendTo('#message');
          $("html, body").animate({ scrollTop: 0 }, "slow");
      }
      
      if (mes.length === 0){
          $('#form-edit').submit();
      }
    })
});
</script>
EOF;

echo <<<EOF
<!DOCTYPE html>
<html>
<head>
EOF;

//Meta CSS
include ($TEMP_APP_META_PATH);

echo <<<EOF
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
EOF;

//Preloader
//include ($TEMP_APP_PRELOADER_PATH);

//Header
include ($TEMP_APP_HEADER_PATH);

//Menu
include ($TEMP_APP_MENU_MOD_PATH);

//Conntent
echo <<<EOF
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-plus"></i>&nbspThêm bài viết</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="home.php">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Thêm bài viết</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a href="{$hrefBack}" class="btn btn-primary float-right mr-3" style="background-color: #17a2b8;" title="Danh sách người dùng">
                        <i class="fas fa-backward"></i>
                        &nbspTrở lại
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="card-body">
                    {$messageHtml}
                    <div class="alert alert-danger alert-dismissible" id="showMess" style="display: none">
                        <div class="row">
                            <div class="icon">
                                <i class="fas fa-ban"></i>
                            </div>
                            <div class="col-10" id="message">
                            </div>
                        </div>
                    </div>
                    <form action="{$_SERVER['SCRIPT_NAME']}" method="POST" id="form-edit" enctype="multipart/form-data">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Thêm bài viết</h3>
                            </div>
                            <div class="card-body">
                                <label>Tiêu đề&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control title" placeholder="Tiêu đề" name="title" value="{$title}" autocomplete="off">
                                </div>
                                
                                <label>Thumbnail</label>
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="thumbnail" id="thumbnail">
                                        <label class="custom-file-label thumbnail" for="thumbnail"></label>
                                        <input type="hidden" class="valueImage" name="image" value="{$image}">
                                    </div>
                                </div>
                                
                                <label>Nội dung</label>
                                <textarea id="summernote" name="content">{$content}</textarea>
                                
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <input type="hidden" class="mode" name="mode" value="{$mode}">
                                <input type="hidden" name="registFlg" value="1">
                                <input type="hidden" name="nid" value="{$nid}">
                                {$htmlDeleteNews}
                                <button type="button" class="btn btn-primary float-right" id="saveNews" style="background-color: #17a2b8;">
                                    <i class="fas fa-save"></i>
                                    &nbspLưu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.row -->
            <!-- /.row (main row) -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
EOF;

//Footer
include ($TEMP_APP_FOOTER_PATH);
//Meta JS
include ($TEMP_APP_METAJS_PATH);
echo <<<EOF
    </div>
</body>
</html>
EOF;

function getNewsById($con, $param){
    $data = [];
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT title                         ";
    $sql .= "     , thumbnail                     ";
    $sql .= "     , content                       ";
    $sql .= "  FROM News                          ";
    $sql .= " WHERE id = ".$param['nid']."        ";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getAllPosition) SQL Error：', $sql.print_r(TRUE));
    } else {
        $recCnt = mysqli_num_rows($query);
    }

    if ($recCnt != 0){
        $data = mysqli_fetch_assoc($query);
    }
    return $data;
}

function insertNews($con, $param, $targetFile){
    $sql = "";
    $sql .= "INSERT INTO News(                          ";
    $sql .= "  title                                    ";
    $sql .= ", thumbnail                                ";
    $sql .= ", content                                  ";
    $sql .= ", createDate                               ";
    $sql .= ", createBy)                                ";
    $sql .= "  VALUES(                                  ";
    $sql .= " '".$param['title']."'                     ";
    $sql .= ", '".$targetFile."'                        ";
    $sql .= ", '".$param['content']."'                  ";
    $sql .= ", ".strtotime(currentDateTime())."         ";
    $sql .= ", ".$_SESSION['uid'].")                    ";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getAllPosition) SQL Error：', $sql.print_r(TRUE));
        return false;
    }
    return true;
}

function updateNews($con, $param){
    $sql = "";
    $sql .= "UPDATE News SET                                     ";
    $sql .= "  title = '".$param['title']."'                     ";
    $sql .= ", thumbnail = 'uploads/".$param['image']."'         ";
    $sql .= ", content = '".$param['content']."'                 ";
    $sql .= ", modifyDate = ".strtotime(currentDateTime())."     ";
    $sql .= ", modifyBy = ".$_SESSION['uid']."                   ";
    $sql .= "  WHERE id = ".$param['nid']."                      ";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getAllPosition) SQL Error：', $sql.print_r(TRUE));
        return false;
    }
    return true;
}

function deleteNews($con, $param){
    $recCnt = 0;

    $sql = "";
    $sql .= "DELETE FROM News WHERE id = ".$param['nid']." ";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(deleteNews) SQL Error：', $sql.print_r(TRUE));
    }

    $_SESSION['message'] = 'Xoá thành công';
    $_SESSION['messageClass'] = 'alert-success';
    $_SESSION['iconClass'] = 'fas fa-check';

    header('Location: manage-news.php');
    exit();
}
?>
