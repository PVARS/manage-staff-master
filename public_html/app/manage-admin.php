<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');
require_once ('reponsitory/AdminRepository.php');

//Initialization
$funcId = 'manage-postion';
$message = '';
$messageClass = '';
$iconClass = '';

session_start();

//Get param
$param = getParam();

//Connect DB
$con = openDB();

if (!isset($_SESSION['uid']) || empty($_SESSION)){
    header('location: login.php');
    exit();
}

$adminRepo = new AdminRepository();
$dataAdmin = $adminRepo->getAllAdmin();

$htmlDataAdmin = '';
$htmlDataAdmin = showDataAmin($dataAdmin);

if ($param){
    $mes = [];

    $message = join('<br>', $mes);
    if (strlen($message)) {
        $messageClass = 'alert-danger';
        $iconClass = 'fas fa-ban';
    }
}

//Message HTML
if (isset($_SESSION['message']) && strlen($_SESSION['message'])) {
    $message .= $_SESSION['message'];
    $messageClass .= $_SESSION['messageClass'];
    $iconClass .= $_SESSION['iconClass'];
    $_SESSION['message'] = '';
    $_SESSION['messageClass'] = '';
    $_SESSION['iconClass'] = '';
}
$messageHtml = '';
if (strlen($message)) {
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
$(function() {
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
                                <i class="fas fa-list-ul"></i>&nbspDanh sách quản trị viên</h1>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Trang chủ</a></li>
                                <li class="breadcrumb-item active">Danh sách quản trị viên</li>
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
                        <div class="card-body">
                            {$messageHtml}
                            <form action="" method="POST">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Tìm kiếm</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                              <!-- text input -->
                                              <div class="form-group">
                                                <label>Họ tên</label>
                                                <input type="text" class="form-control" name="fullName" value="" placeholder="Họ tên">
                                              </div>
                                            </div>
                                            <div class="col-sm-6">
                                              <div class="form-group">
                                                <label>Tên đăng nhập</label>
                                                <input type="text" class="form-control" name="username" value="" placeholder="Tên đăng nhập">
                                              </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-sm-6">
                                              <!-- text input -->
                                              <div class="form-group">
                                                <label>Số điện thoại</label>
                                                <input type="text" class="form-control" name="numberPhone" value="" placeholder="Số điện thoại">
                                              </div>
                                            </div>
                                            <div class="col-sm-6">
                                              <div class="form-group">
                                                <label>Email</label>
                                                <input type="text" class="form-control" name="email" value="" placeholder="Email">
                                              </div>
                                            </div>
                                        </div>
                                        
                                        <label>Trạng thái</label>
                                        <div class="input-group mb-3">
                                            <select class="custom-select" name="admin">
                                                <option value="0">-- Vui lòng chọn --</option>
                                                <option value="2">Tin Chuyển Nhượng</option>
                                                <option value="3">Phân Tích</option>
                                                <option value="4">Tản Mạn</option>
                                                <option value="1">Tin Arsenal</option>
                                            </select>
                                        </div>

                                        <label>Thời gian</label>
                                        <div class="row">
                                            <div class="input-group mb-6 col-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="date" name="dateFrom" class="form-control" value="">
                                            </div>
                                            <span><b>~</b></span>
                                            <div class="input-group mb-6 col-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="date" name="dateTo" class="form-control" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <input type="hidden" name="registFlg" value="1">
                                        <button type="submit" class="btn btn-primary float-right" style="background-color: #17a2b8;">
                                          <i class="fa fa-search"></i>
                                          &nbspTìm kiếm
                                        </button>
                                        <a class="btn btn-default" id="btnClear">
                                            <i class="fas fa-eraser fa-fw"></i>
                                            Xoá
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.row -->
                    <div class="row">
                        
                        <div class="card-body table-responsive">
                            <table class="table table-hover text-nowrap table-bordered" style="background-color: #FFFFFF;">
                                <thead style="background-color: #17A2B8;">
                                    <tr>
                                        <th style="text-align: center; width: 5%;" class="text-th">STT</th>
                                        <th style="text-align: center; width: 20%;" class="text-th">Họ tên</th>
                                        <th style="text-align: center; width: 20%;" class="text-th">Tên đăng nhập</th>
                                        <th style="text-align: center; width: 20%;" class="text-th">Số điện thoại</th>
                                        <th style="text-align: center; width: 20%;" class="text-th">Trạng thái</th>
                                        <th colspan="2" class="text-center" style="width: 15px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$htmlDataAdmin}
                                </tbody>
                            </table>
                        </div>
                    </div>
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

function showDataAmin($dataAdmin){
    $html = '';
    if (!empty($dataAdmin)){
        foreach ($dataAdmin as $k => $value){
            if ($value['lockFlg'] == 0){
                $value['lockFlg'] = '<i class="fas fa-check-circle"></i>';
            } else $value['lockFlg'] = '<i class="fas fa-ban"></i>';
            $index = $k+1;
            $html.= <<< EOF
                <tr>
                    <td style="text-align: center; width: 5%;">{$index}</td>
                    <td style="width: 20%;">{$value['fullName']}</td>
                    <td style="width: 20%;">{$value['username']}</td>
                    <td style="text-align: center; width: 20%;">{$value['phone']}</td>
                    <td style="text-align: center; width: 20%;">{$value['lockFlg']}</td>
                    <td style="text-align: center; width: 5%;">
                        <form action="detail-admin.php" method="POST">
                            <input type="hidden" name="uid" value="{$value['id']}">
                            <button class="btn btn-primary btn-sm editUser"><i class="fas fa-edit"></i></button>
                        </form>
                    </td>
                    <td style="text-align: center; width: 5%;">
                        <form action="{$_SERVER['SCRIPT_NAME']}" method="POST">
                            <input type="hidden" name="uid" value="{$value['id']}">
                            <button class="btn btn-danger btn-sm deleteUser"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
EOF;
        }
    } else {
        $html .= <<< EOF
            <tr>
                <td colspan = 6>
                    <h3 class="card-title">
                        <i class="fas fa-bullseye fa-fw" style="color: red"></i>
                        Không có dữ liệu
                    </h3>
                </td>
            </tr>
EOF;
    }
    return $html;
}
?>

