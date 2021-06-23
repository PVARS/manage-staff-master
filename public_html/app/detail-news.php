<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');
require_once ('plugins/PHPMailer/PHPMailer.php');
require_once ('plugins/PHPMailer/SMTP.php');
require_once ('plugins/PHPMailer/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;

//Initialization
$func_id = 'send_request';
$message = '';
$messageClass = '';
$iconClass = '';
$error = 0;
$readonly = '';

session_start();

//Get param
$param = getParam();

//Connect DB
$con = openDB();

if (!isset($_SESSION['uid']) || empty($_SESSION)){
    header('location: login.php');
    exit();
}

$titleEmail = $param['titleEmail'] ?? '';
$mailTo = $param['mailTo'] ?? '';
$content = $param['content'] ?? '';

if ($param){
    if (isset($param['registFlg']) && $param['registFlg'] == 1){
        $mes = [];

        $targetDir = 'uploads/';
        $targetFile = $targetDir.basename($_FILES['thumbnail']['name']);
        $allowUpload = true;
        $extensionFile = pathinfo($targetFile, PATHINFO_EXTENSION);
        $allowtypes = ['jpg', 'png', 'jpeg', 'gif'];

        $checkImage = getimagesize($_FILES["thumbnail"]["tmp_name"]);
        if ($checkImage != true){
            $allowUpload = false;
            $mes[] = 'Chỉ được uploads hình ảnh.';
        } elseif(!in_array($extensionFile, $allowtypes)){
            $allowUpload = false;
            $mes[] = 'Chỉ uploads hình ảnh có đuôi JPG, PNG, JPEG, GIF.';
        }

        if (empty($mes) && $allowUpload){
            if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $targetFile)){
                var_dump(basename($_FILES["thumbnail"]["name"]));
            }
        }
    }

    $message = join('</br>', $mes);
    if (strlen($message)){
        $messageClass = 'alert-danger';
        $iconClass = 'fas fa-ban';
    }
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
$scriptHTML = '';

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
            </div>
            <div class="row">
                <div class="card-body">
                    {$messageHtml}
                    <form action="{$_SERVER['SCRIPT_NAME']}" method="POST" id="form-edit" enctype="multipart/form-data">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Thêm bài viết</h3>
                            </div>
                            <div class="card-body">
                                <label>Tiêu đề&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Tiêu đề email" name="titleEmail" value="{$titleEmail}" autocomplete="off">
                                </div>
                                
                                <label>Thumbnail</label>
                                <div class="input-group mb-3">
                                    <div class="custom-file">
<!--                                        <input type="file" class="custom-file-input" name="thumbnail" value="">-->
<!--                                        <label class="custom-file-label" for="customFile"></label>-->
                                        <input type="file" name="thumbnail" id="thumbnail" value="111">
                                    </div>
                                </div>
                                
                                <label>Nội dung&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <textarea id="summernote" name="content">{$content}</textarea>
                                
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <input type="hidden" class="mode" name="mode" value="">
                                <input type="hidden" name="registFlg" value="1">
                                <input type="hidden" name="uid" value="">
                                <button type="submit" class="btn btn-primary float-right" id="saveUser" style="background-color: #17a2b8;">
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

?>
