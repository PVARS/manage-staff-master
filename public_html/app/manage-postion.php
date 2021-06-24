<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

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

if ($_SESSION['role'] == 3){
    header('location: not-found.php');
    exit();
}

$isStatus = checkStatusUser($con);
if ($isStatus['lockFlg'] == 1){
    header('Location: error-page.php');
    exit();
}

$htmlDataPostion = '';
$htmlDataPostion = getAllPosition($con,$param);

if ($param){
    $mes = [];

    if(isset($param['pid'])){
        lock($con, $param);
    }
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

    $(".lock").on("click", function(e) {
        e.preventDefault();
        var message = "Vị trí này sẽ bị khóa. Bạn chắc chứ ??";
        var form = $(this).closest("form");
        $('.mode').val('unLock');
        sweetConfirm(2, message, function(result) {
            if (result){
                
                form.submit();
            }
        });
    });
    
    $(".unLock").on("click", function(e) {
        e.preventDefault();
        var message = "Vị trí này sẽ bị khóa. Bạn chắc chứ ??";
        var form = $(this).closest("form");
        $('.mode').val('lock');
        sweetConfirm(2, message, function(result) {
            if (result){
                form.submit();
            }
        });
    });

    $(".editPostion").on("click", function(e) {
        e.preventDefault();
        var message = "Chuyển đến màn hình chỉnh sửa. Bạn chắc chứ ??";
        var form = $(this).closest("form");
        $('.mode').val('lock');
        sweetConfirm(3, message, function(result) {
            if (result){
                form.submit();
            }
        });
    });
    $(".table").paginate({
        rows: 5,           // Set number of rows per page. Default: 5
        position: "top",   // Set position of pager. Default: "bottom"
        jqueryui: false,   // Allows using jQueryUI theme for pager buttons. Default: false
        showIfLess: false, // Don't show pager if table has only one page. Default: true
        numOfPages: 5
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
$search = $param['position'] ?? '';
//Conntent
echo <<<EOF
<div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">
                                <i class="fas fa-list-ul"></i>&nbspQuản lí vị trí</h1>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Trang chủ</a></li>
                                <li class="breadcrumb-item active">Danh sách vị trí</li>
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
                            <form action="{$_SERVER['SCRIPT_NAME']}" method="POST">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Tìm kiếm</h3>
                                    </div>
                                    <div class="card-body">
                                        <label>Tên vị trí</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" name="position" value="{$search}" placeholder="Tên vị trí">
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
                                        <th style="text-align: center; width: 10%;" class="text-th">STT</th>
                                        <th style="text-align: center; width: 40%;" class="text-th">Vị trí</th>
                                        <th style="text-align: center; width: 40%;" class="text-th">Người tạo</th>
                                        <th colspan="3" class="text-center" style="width: 15px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$htmlDataPostion}
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

function getAllPosition($con,$param){
    $data = [];
    $mysql = [];
    $cnt = 0;
    $recCnt = 0;

    if(!empty($param['position'])){
        $mysql[] = " WHERE Postion.namePosition LIKE '%".$param['position']."%' ";
    }

    $wheresql = join('', $mysql);

    $sql = "";
    $sql .= "SELECT Postion.*";
    $sql .= "     , User.fullName";
    $sql .= " FROM Postion";
    $sql .= " INNER JOIN User";
    $sql .= "   ON User.id = Postion.createBy";
    $sql .= $wheresql;
    $sql .= " ORDER BY Postion.lockFlg DESC";
    $sql .= "     , Postion.createDate DESC";
    $query = mysqli_query($con, $sql);


    if (!$query){
        systemError('systemError(getAllPosition) SQL Error：', $sql.print_r(TRUE));
    } else{
        $recCnt = mysqli_num_rows($query);
    }

    $html = '';
    if($recCnt != 0){
        $mode = $param['mode'] ?? '';
        while($row = mysqli_fetch_assoc($query)){
            $cnt++;
            if($row['lockFlg'] == 0){
                $classBg = 'btn-success';
                $classStatus = 'unLock';
                $checklock = 'fa-lock-open';
            } else{
                $classBg = 'btn-danger';
                $classStatus = 'lock';
                $checklock = 'fa-lock';
            }

            $html.= <<< EOF
                <tr>
                    <td style="text-align: center; width: 5%;">{$cnt}</td>
                    <td style="width: 40%;">{$row['namePosition']}</td>
                    <td style="width: 40%;">{$row['fullName']}</td>
                    <td style="text-align: center; width: 5%;">
                        <form action="detail-postion.php" method="POST">
                            <input type="hidden" name="pid" value="{$row['id']}">
                            <input type="hidden" name="mode" value="update">
                            <input type="hidden" name="dispFrom" value="manage-postion">
                            <button class="btn btn-primary btn-sm editPostion"><i class="fas fa-edit"></i></button>
                        </form>
                    </td>
                    <td style="text-align: center; width: 5%;">
                        <form action="{$_SERVER['SCRIPT_NAME']}" method="POST">
                            <input type="hidden" name="pid" value="{$row['id']}">
                            <input type="hidden" class="mode" name="mode" value="{$mode}">
                            <button class="btn {$classBg} btn-sm {$classStatus}"><i class="fas {$checklock}"></i></button>
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
function lock($con, $param){
    if($param['mode'] == 'lock'){
        $sql = "";
        $sql .= "UPDATE Postion SET ";
        $sql .= " lockFlg = 1";
        $sql .= " WHERE id = ".$param['pid']." ";
    } else {
        $sql = "";
        $sql .= "UPDATE Postion SET ";
        $sql .= " lockFlg = 0";
        $sql .= " WHERE id = ".$param['pid']." ";
    }

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(lock) SQL Error：', $sql.print_r(TRUE));
    }
    header('location: manage-postion.php');
    exit();
}
?>

