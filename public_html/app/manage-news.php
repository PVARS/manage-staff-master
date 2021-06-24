<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

//Initialization
$funcId = 'manage-news';
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

$title = $param['title'] ?? '';
$createBy = $param['createBy'] ?? '';
$keyword = $param['keyword'] ?? '';
$dateFrom = $param['dateFrom'] ?? '';
$dateTo = $param['dateTo'] ?? '';

$htmlDataNews = '';
$htmlDataNews = showDataNews($con, $param);

if ($param){
    $mes = [];

    if (!empty($createBy) && mb_strlen($createBy) > 200){
        $mes[]= 'Vui lòng nhập tên người đăng không được quá 200 ký tự.';
    }

    if (!empty($param['dateFrom'])){
        if (strtotime($param['dateFrom']) == false){
            $mes[] = 'Vui lòng nhập ngày đến định dạng.';
        }
    }

    if (!empty($param['dateTo'])){
        if (strtotime($param['dateTo']) == false){
            $mes[] = 'Vui lòng nhập ngày từ định dạng.';
        }
    }

    if (empty($mes)){
        $htmlDataNews;
    }

    if (isset($param['mode']) && $param['mode'] == 'delete'){
        deleteNews($con, $param);
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
    $("#datepickerTo").datepicker({
        dateFormat: 'dd-mm-yy'
    });
    
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
    
    $(".editNews").on("click", function(e) {
        e.preventDefault();
        var message = "Chuyển đến màn hình chỉnh sửa. Bạn chắc chứ?";
        var form = $(this).closest("form");
        sweetConfirm(3, message, function(result) {
            if (result){
                form.submit();
            }
        });
    });
    
    $(".deleteNews").on("click", function(e) {
        e.preventDefault();
        var message = "Bài viết này sẽ bị xoá. Bạn chắc chứ?";
        var form = $(this).closest("form");
        sweetConfirm(1, message, function(result) {
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

//Conntent
echo <<<EOF
<div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">
                                <i class="fas fa-newspaper"></i>&nbspDanh sách bài viết</h1>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.html">Trang chủ</a></li>
                                <li class="breadcrumb-item active">Danh sách bài viết</li>
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
                                        <h3 class="card-title">Danh sách bài viết</h3>
                                    </div>
                                    <div class="card-body">
                                        <label>Tiêu đề</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" name="title" placeholder="Tiêu đề" value="{$title}">
                                        </div>

                                        <label>Người đăng</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" name="createBy" placeholder="Người đăng" value="{$createBy}">
                                        </div>

                                        <label>Từ khoá</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" name="keyword" placeholder="Nhập từ khoá" value="{$keyword}">
                                        </div>

                                        <label>Thời gian</label>
                                        <div class="row">
                                            <div class="input-group mb-6 col-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="text" id="datepicker" placeholder="10-05-2021" name="dateFrom" class="form-control" value="{$dateFrom}" autocomplete="off">
                                            </div>
                                            <span><b>~</b></span>
                                            <div class="input-group mb-6 col-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="text" id="datepickerTo" placeholder="10-05-2021" name="dateTo" class="form-control" value="{$dateTo}" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary float-right" style="background-color: #17a2b8;">
                                          <i class="fa fa-search"></i>
                                          &nbspTìm kiếm
                                        </button>
                                        <a href="" id="btnClear">
                                            <button type="reset" class="btn btn-default">
                                            <i class="fas fa-eraser fa-fw"></i>
                                            Xoá
                                          </button>
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
                                        <th style="width: 5%;" class="text-th text-center">STT</th>
                                        <th style="width: 55%;" class="text-th text-center">Tiêu đề</th>
                                        <th style="width: 20%;" class="text-th text-center">Người đăng</th>
                                        <th style="text-align: center; width: 20%;" class="text-th text-center">Ngày đăng</th>
                                        <th colspan="2"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$htmlDataNews}
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

/**
 * Show data news
 * @param $con
 * @param $param
 * @return string
 */
function showDataNews($con, $param): string
{
    $mysql = [];
    $recCnt = 0;

    if (!empty($param['title'])){
        $mysql[] = "AND News.title LIKE '%".$param['title']."%'                    ";
    }

    if (!empty($param['createBy'])){
        $mysql[] = "AND User.fullName LIKE '%".$param['createBy']."%'              ";
    }

    if (!empty($param['keyword'])){
        $mysql[] = "AND News.content LIKE '%".$param['keyword']."%'                ";
    }

    if (!empty($param['dateFrom'])){
        $mysql[] = "AND unix_timestamp(DATE(from_unixtime(News.createDate))) >= ".strtotime($param['dateFrom'])."       ";
    }

    if (!empty($param['dateTo'])){
        $mysql[] = "AND unix_timestamp(DATE(from_unixtime(News.createDate))) <= ".strtotime($param['dateTo'])."         ";
    }

    $wheresql = join('', $mysql);

    $sql = "";
    $sql .= "SELECT News.title                        ";
    $sql .= "     , News.id                           ";
    $sql .= "     , News.createDate                   ";
    $sql .= "     , User.fullName                     ";
    $sql .= "  FROM News                              ";
    $sql .= "  INNER JOIN User                        ";
    $sql .= "    ON News.createBy = User.id           ";
    $sql .= " WHERE News.createDate IS NOT NULL       ";
    $sql .= $wheresql;
    $sql .= " ORDER BY News.createDate DESC           ";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getAllMember) SQL Error：', $sql.print_r(TRUE));
    } else {
        $recCnt = mysqli_num_rows($query);
    }

    $html = '';
    if ($recCnt != 0){
        $index = 0;
        while ($row = mysqli_fetch_assoc($query)){
            $index++;
            $createDate = date('d/m/Y - H:i:s', $row['createDate']);
            $html .= <<< EOF
                <tr>
                   <td style="width: 5%;" class="text-center">{$index}</td>
                   <td style="width: 45%;">{$row['title']}</td>
                   <td style="width: 20%;" class="text-center">{$row['fullName']}</td>
                   <td style="text-align: center; width: 20%;" class="text-center">{$createDate}</td>
                   <td style="text-align: center; width: 10%;">
                       <form action="detail-news.php" method="POST">
                            <input type="hidden" name="mode" value="update">
                            <input type="hidden" name="dispFrom" value="manage-news">
                            <input type="hidden" name="nid" value="{$row['id']}">
                            <button class="btn btn-primary btn-sm editNews"><i class="fas fa-edit"></i></button>
                        </form>
                   </td>
                   <td style="text-align: center; width: 10%;">
                       <form action="{$_SERVER['SCRIPT_NAME']}" method="POST">
                            <input type="hidden" name="mode" value="delete">
                            <input type="hidden" name="nid" value="{$row['id']}">
                            <button class="btn btn-danger btn-sm deleteNews"><i class="fas fa-trash"></i></button>
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

/**
 * delete a news
 * @param $con
 * @param $param
 */
function deleteNews($con, $param){
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

