<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

//Initialization
$funcId = 'manage-postion';
$message = '';
$messageClass = '';
$iconClass = '';
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

if (isset($param['dispFrom'])){
    if ($param['dispFrom'] == 'manage-postion') $hrefBack = 'manage-postion.php';
}

$title = 'Tạo vị trí';
$namePostion = $param['namePostion'] ?? '';
$mode = $param['mode'] ?? 'new';
$pid = $param['pid'] ?? '';

if(isset($param['pid']) && !empty($param['pid'])){
    $dataPostion = getPositionById($con,$param);
    $namePostion = $dataPostion['namePosition'] ?? '';
    $title = 'Cập nhật vị trí ';
}

if ($param){
    if(isset($param['registFlg']) && $param['registFlg'] == 1){
        $mes = [];
        
        if(empty($param['namePostion'])){
            $mes[] = "Vui lòng nhập tên vị trí";
        } elseif(mb_strlen($param['namePostion']) >100 ){
            $mes[] = "Tên vị trí phải nhỏ hơn 100 kí tự";
        }
        

        if(empty($mes)){
            if($mode == 'update'){
                updatePosition($con,$param);
            }
            addPosition($con,$param);
        }
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
                        <i class="fas fa-folder-plus"></i>&nbsp{$title}</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Trang chủ</a></li>
                        <li class="breadcrumb-item active">{$title}</li>
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
                    <form action="{$_SERVER['SCRIPT_NAME']}" method="POST" id="form-edit">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">{$title}</h3>
                            </div>
                            <div class="card-body">
                                <label>Tên vị trí&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Tên vị trí" name="namePostion" value="{$namePostion}">
                                </div>
                                <label>Người tạo</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="{$_SESSION['username']}" readonly name="createBy">
                                </div>
                                
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <input type="hidden" class="mode" name="mode" value="{$mode}">
                                <input type="hidden" name="registFlg" value="1">
                                <input type="hidden" name="pid" value="{$pid}">
                                <button type="submit" class="btn btn-primary float-right" style="background-color: #17a2b8;">
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

function getPositionById($con,$param){
    $data = [];
    $cnt = 0;
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT *";
    $sql .= "     FROM Postion";
    $sql .= " WHERE id = ".$param['pid']." ";
    

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getAllPosition) SQL Error：', $sql.print_r(TRUE));
    } else {
        $recCnt = mysqli_num_rows($query);
    }

    if($recCnt != 0){
        $data = mysqli_fetch_assoc($query);
    }
    return $data;
}
function addPosition($con,$param){
    $recCnt = 0;
    $dateTime = strtotime(currentDateTime());


    $sql = "";
    $sql .= "INSERT INTO Postion                        ";
    $sql .= "           (namePosition                   ";
    $sql .= "          , createDate                     ";
    $sql .= "          , createBy                       ";
    $sql .= "          , lockFlg)                       ";
    $sql .= " VALUES (                                  ";
    $sql .= "        '" .$param['namePostion']."'       ";
    $sql .= "        , ".$dateTime."                    ";
    $sql .= "       , ".$_SESSION['uid']."              ";
    $sql .= "         , 0)                              ";
    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getAllPosition) SQL Error：', $sql.print_r(TRUE));
    }


    $_SESSION['message'] = 'Thêm thành công';
    $_SESSION['messageClass'] = 'alert-success';
    $_SESSION['iconClass'] = 'fas fa-check';

    header('Location: manage-postion.php');
    exit();

}
function updatePosition($con, $param){
    $recCnt = 0;
    $dateTime = strtotime(currentDateTime());
    $sql = "";
    $sql .= "UPDATE Postion SET ";
    $sql .= "     namePosition = '".$param['namePostion']."'";
    $sql .= "     ,modifyDate = ".$dateTime."";
    $sql .= "     ,modifyBy = ".$_SESSION['uid']."";
    $sql .= " WHERE id = ".$param['pid']." ";
    
    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getAllPosition) SQL Error：', $sql.print_r(TRUE));
    }

    $_SESSION['message'] = 'Cập nhật thành công';
    $_SESSION['messageClass'] = 'alert-success';
    $_SESSION['iconClass'] = 'fas fa-check';

    header('Location: manage-postion.php');
    exit();
}

