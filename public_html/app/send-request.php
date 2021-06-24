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

$isStatus = checkStatusUser($con);
if ($isStatus['lockFlg'] == 1){
    header('Location: error-page.php');
    exit();
}

$titleEmail = $param['titleEmail'] ?? '';
$mailTo = $param['mailTo'] ?? '';
$content = $param['content'] ?? '';

if ($param){
    $mes = [];

    if (empty($titleEmail)){
        $mes[] = 'Tiêu đề không được để trống.';
    }

    if (empty($mailTo)){
        $mes[] = 'Vui lòng nhập địa chỉ email gửi đến.';
    } elseif (!preg_match('/^[\w\.\-_]+@[\w\.\-_]+\.\w+$/', $mailTo)){
        $mes[] = 'Email không đúng định dạng. Ví dụ: abc@gmail.com.';
    }

    if (empty($content)){
        $mes[] = 'Vui lòng nhập nội dung.';
    }

    if (empty($mes)){
        sendMail($param);
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
                        <i class="fas fa-envelope-open-text"></i>&nbspGửi Email</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="home.php">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Gửi Email</li>
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
                    <form action="{$_SERVER['SCRIPT_NAME']}" method="POST" id="form-edit">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Gửi Email</h3>
                            </div>
                            <div class="card-body">
                                <label>Tiêu đề email&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Tiêu đề email" name="titleEmail" value="{$titleEmail}" autocomplete="off">
                                </div>
                                
                                <label>Gửi đến&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="{$mailTo}" placeholder="Gửi đến" name="mailTo" autocomplete="off">
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
                                    <i class="fas fa-paper-plane"></i>
                                    &nbspGửi đi
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

/**
 * @throws \PHPMailer\PHPMailer\Exception
 */
function sendMail($param)
{
    // SETTING PHPMAIL
    $mail = new PHPMailer();
    $mail->IsHTML(true);
    $mail->IsSMTP();
    $mail->addAddress($param['mailTo']);
    $mail->Subject    = $param['titleEmail'];

    $mail->FromName   = 'BAP System';
    $mail->From       = 'vupv@bap.jp';
    $mail->Username   = 'vupv@bap.jp';
    $mail->Password   = 'phanvu2000';
    $mail->CharSet    = 'utf-8';
    $mail->Host       = 'ssl://smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->Body       = html_entity_decode($param['content'], ENT_QUOTES, 'UTF-8');

    if ($mail->send()) {
        $_SESSION['message'] = 'Email đã được gửi đến '.$param['mailTo'];
        $_SESSION['messageClass'] = 'alert-success';
        $_SESSION['iconClass'] = 'fas fa-check';

        header('location: send-request.php');
        exit();
    } else {
        $mail->isError();
    }
}
?>
