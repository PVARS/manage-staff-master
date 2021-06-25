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

$isStatus = checkStatusUser($con);
if ($isStatus['lockFlg'] == 1){
    header('Location: error-page.php');
    exit();
}

$fullName = $param['fullName'] ?? '';
$username = $param['username'] ?? '';
$numberPhone = $param['numberPhone'] ?? '';
$email = $param['email'] ?? '';
$status = $param['status'] ?? '';
$selected = '';
if (!empty($status)) $selected = 'selected';

$htmlDataMember = '';
$htmlDataMember = showDataMember($con, $param);

if ($param){
    $mes = validation($param);

    if (empty($mes)) $htmlDataMember;
    if (isset($param['uid'])) lock($con, $param);

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
    
    $(".editUser").on("click", function(e) {
        e.preventDefault();
        var message = "Chuyển đến màn hình chỉnh sửa. Bạn chắc chứ?";
        var form = $(this).closest("form");
        sweetConfirm(3, message, function(result) {
            if (result){
                form.submit();
            }
        });
    });

    $(".checkIn").on("click", function(e) {
        e.preventDefault();
        var message = "Chuyển đến màn hình thông tin điểm danh. Bạn chắc chứ?";
        var form = $(this).closest("form");
        sweetConfirm(7, message, function(result) {
            if (result){
                form.submit();
            }
        });
    });
    
    $(".lock").on("click", function(e) {
        e.preventDefault();
        var message = "Quản trị viên này sẽ bị khóa. Bạn chắc chứ";
        var form = $(this).closest("form");
        sweetConfirm(2, message, function(result) {
            if (result){
                $('.mode').val('unlock');
                form.submit();
            }
        });
    });
    
    $(".unlock").on("click", function(e) {
        e.preventDefault();
        var message = "Quản trị viên này sẽ mở khóa. Bạn chắc chứ";
        var form = $(this).closest("form");
        sweetConfirm(2, message, function(result) {
            if (result){
                $('.mode').val('lock');
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

//Conntent
echo <<<EOF
<div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">
                                <i class="fas fa-list-ul"></i>&nbspDanh thành viên</h1>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Trang chủ</a></li>
                                <li class="breadcrumb-item active">Danh thành viên</li>
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
                                                <input type="text" class="form-control" name="fullName" value="{$fullName}" placeholder="Họ tên">
                                              </div>
                                            </div>
                                            <div class="col-sm-6">
                                              <div class="form-group">
                                                <label>Tên đăng nhập</label>
                                                <input type="text" class="form-control" name="username" value="{$username}" placeholder="Tên đăng nhập">
                                              </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-sm-6">
                                              <!-- text input -->
                                              <div class="form-group">
                                                <label>Số điện thoại</label>
                                                <input type="text" class="form-control" name="numberPhone" value="{$numberPhone}" placeholder="Số điện thoại">
                                              </div>
                                            </div>
                                            <div class="col-sm-6">
                                              <div class="form-group">
                                                <label>Email</label>
                                                <input type="text" class="form-control" name="email" value="{$email}" placeholder="Email">
                                              </div>
                                            </div>
                                        </div>
                                        
                                        <label>Trạng thái</label>
                                        <div class="input-group mb-3">
                                            <select class="custom-select" name="status">
                                                <option value="0" {$selected}>Đang hoạt động</option>
                                                <option value="1" {$selected}>Vô hiệu hóa</option>
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
                                        <th colspan="3" class="text-center" style="width: 15px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$htmlDataMember}
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
function validation($param){
    $mes = [];
    if (!empty($param['fullName']) && mb_strlen($param['fullName']) > 200){
        $mes[] = 'Họ tên phải bé hơn 200 ký tự';
    }

    if (!empty($param['username']) && mb_strlen($param['username']) > 100){
        $mes[] = 'Tên đăng nhập phải bé hơn 100 ký tự';
    }

    if (!empty($param['numberPhone']) && !is_numeric($param['numberPhone'])){
        $mes[] = 'Số điện thoại chỉ được nhập ký tự số';
    }
    return $mes;
}

/**
 * @param $con
 * @param $param
 * @return string
 */
function showDataMember($con, $param): string
{
    $mysql = [];
    $recCnt = 0;
    $cnt = 0;

    if (!empty($param['fullName'])){
        $mysql[] = "AND fullName LIKE '%".$param['fullName']."%'   ";
    }

    if (!empty($param['username'])){
        $mysql[] = "AND username LIKE '%".$param['username']."%'   ";
    }

    if (!empty($param['numberPhone'])){
        $mysql[] = "AND phone LIKE '%".$param['numberPhone']."%'   ";
    }

    if (!empty($param['email'])){
        $mysql[] = "AND email LIKE '%".$param['email']."%'         ";
    }

    if (!empty($param['dateFrom'])){
        $mysql[] = "AND createDate >= ".$param['dateFrom']."       ";
    }

    if (!empty($param['dateTo'])){
        $mysql[] = "AND createDate <= ".$param['dateFrom']."       ";
    }

    if (!empty($param['status'])){
        $mysql[] = "AND lockFlg = ".$param['status']."             ";
    }

    $wheresql = join('', $mysql);

    $sql = " SELECT id                         ";
    $sql .= "     , username                   ";
    $sql .= "     , fullName                   ";
    $sql .= "     , role                       ";
    $sql .= "     , position                   ";
    $sql .= "     , gender                     ";
    $sql .= "     , email                      ";
    $sql .= "     , phone                      ";
    $sql .= "     , birthday                   ";
    $sql .= "     , lockFlg                    ";
    $sql .= "  FROM User                       ";
    $sql .= "  WHERE createDate IS NOT NULL    ";
    $sql .= "  AND role = 3                    ";
    $sql .= $wheresql;
    $sql .= " ORDER BY lockFlg ASC             ";
    $sql .= "     , createDate DESC            ";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getAllMember) SQL Error：', $sql.print_r(TRUE));
    } else
        $recCnt = mysqli_num_rows($query);

    $html = '';
    if ($recCnt != 0){
        $mode = $param['mode'] ?? '';
        while ($row = mysqli_fetch_assoc($query)){
            $cnt++;
            if ($row['lockFlg'] == 0){
                $iconStatus = 'fa-lock-open';
                $classLock = 'unlock';
                $classBg = 'btn-success';
            } else {
                $iconStatus = 'fa-lock';
                $classLock = 'lock';
                $classBg = 'btn-danger';
            }

            if ($_SESSION['uid'] == $row['id']){
                $htmlBtnLock = <<< EOF
                    <button class="btn {$classBg} btn-sm" disabled="disabled"><i class="fas {$iconStatus}"></i></button>
EOF;
            } else {
                $htmlBtnLock = <<< EOF
                    <form action="{$_SERVER['SCRIPT_NAME']}" method="POST">
                        <input type="hidden" name="uid" value="{$row['id']}">
                        <input type="hidden" class="mode" name="mode" value="{$mode}">
                        <button class="btn {$classBg} btn-sm {$classLock}"><i class="fas {$iconStatus}"></i></button>
                    </form>
EOF;
            }

            $html.= <<< EOF
                <tr>
                    <td style="text-align: center; width: 5%;">{$cnt}</td>
                    <td style="width: 20%;">{$row['fullName']}</td>
                    <td style="width: 20%;">{$row['username']}</td>
                    <td style="text-align: center; width: 20%;">{$row['phone']}</td>
                    <td style="text-align: center; width: 5%;">
                        <form action="checkinOut.php" method="POST">
                            <input type="hidden" name="fullName" value="{$row['fullName']}">
                            <input type="hidden" name="uidCheckin" value="{$row['id']}">
                            <button class="btn btn-primary btn-sm checkIn"><i class="fas fa-eye"></i></button>
                        </form>
                    </td>
                    <td style="text-align: center; width: 5%;">
                        <form action="detail-member.php" method="POST">
                            <input type="hidden" name="uid" value="{$row['id']}">
                            <input type="hidden" name="mode" value="update">
                            <input type="hidden" name="dispFrom" value="manage-member">
                            <button class="btn btn-primary btn-sm editUser"><i class="fas fa-edit"></i></button>
                        </form>
                    </td>
                    <td style="text-align: center; width: 5%;">
                        {$htmlBtnLock}
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

/**
 * @param $con
 * @param $param
 */
function lock($con, $param){
    if ($param['mode'] == 'lock'){
        $status = 1;
    } else $status = 0;

    $sql = " UPDATE User SET                  ";
    $sql .= "       lockFlg = ".$status."     ";
    $sql .= " WHERE id = ".$param['uid']."    ";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getAllMember) SQL Error：', $sql.print_r(TRUE));
    }
    header('Location: manage-member.php');
}
?>

