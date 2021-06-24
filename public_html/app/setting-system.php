<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

$message = '';
$messageClass = '';
$iconClass = '';

session_start();

//Get param
$param = getParam();

//Connect DB
$con = openDB();

$appInf = getApps($con);

if ($param){
    if (isset($param['registFlg']) && $param['registFlg'] == 1){
        $mes = validation($param);

        if (empty($mes)){
            updateApps($con, $param, $appInf);
        }

        $message = join('<br>', $mes);
        if (strlen($message)) {
            $messageClass = 'alert-danger';
            $iconClass = 'fas fa-ban';
            $error = 1;
        }
    }
}


if (!empty($appInf)){
    $fromName       = $param['fromname'] ?? $appInf['nameEmail'];
    $username       = $param['username'] ?? $appInf['mailFrom'];
    $password       = $param['password'] ?? $appInf['password'];
    $charset        = $param['charset'] ?? $appInf['charset'];
    $host           = $param['host'] ?? $appInf['host'];
    $smtpAuth       = $param['smtpauth'] ?? $appInf['smtpAuth'];
    $smtpSecure     = $param['smtpsecure'] ?? $appInf['smptSecure'];
    $port           = $param['port'] ?? $appInf['port'];
} else {
    $fromName       = $param['fromname'] ?? '';
    $username       = $param['username'] ?? '';
    $password       = $param['password'] ?? '';
    $charset        = $param['charset'] ?? '';
    $host           = $param['host'] ?? '';
    $smtpAuth       = $param['smtpauth'] ?? '';
    $smtpSecure     = $param['smtpsecure'] ?? '';
    $port           = $param['port'] ?? '';
}

$authChecked = [];
$authChecked[0] = '';
$authChecked[1] = '';
$valueChecked = $param['gender'] ?? $smtpAuth;

if ($valueChecked == 0){
    $authChecked[0] = 'checked="checked"';
} elseif ($valueChecked == 1)
    $authChecked[1] = 'checked="checked"';

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
$titleHTML = 'Cài đặt hệ thống';
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
                    <h1 class="m-0"><i class="fas fa-cog"></i>&nbsp{$titleHTML}</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Cài đặt hệ thống</li>
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
                    <a href="home.php" class="btn btn-primary float-right mr-3" style="background-color: #17a2b8;" title="Danh sách người dùng">
                        <i class="fas fa-backward"></i>
                        &nbspTrở lại
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="card-body">
                    {$messageHtml}
                    <form action="{$_SERVER['SCRIPT_NAME']}" method="POST">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Cài đặt</h3>
                            </div>
                            <div class="card-body">                                                        
                                <label>Tên email&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Tên email" name="fromname" value="{$fromName}">
                                </div>
                            
                                <label>Email&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Email" name="username" value="{$username}">
                                </div>
                                <label>Mật khẩu&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="password" class="form-control" placeholder="Mật khẩu" name="password" value="{$password}">
                                </div>
                                <label>Charset&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Charset" name="charset" value="{$charset}">
                                </div>
                                
                                <label>Host&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Host" name="host" value="{$host}">
                                </div>
                                
                                <label>SMTP Auth&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3 ml-2">
                                    <div class="row">
                                        <div class="form-check mr-3">
                                            <input class="form-check-input" type="radio" name="smtpauth" value="0" {$authChecked[0]}>
                                            <label class="form-check-label">True</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="smtpauth" value="1" {$authChecked[1]}>
                                            <label class="form-check-label">False</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <label>SMTP Secure&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="SMTP Secure" name="smtpsecure" value="{$smtpSecure}">
                                </div>
                                
                                <label>Port&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Port" name="port" value="{$port}">
                                </div>
                                
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <input type="hidden" name="registFlg" value="1">
                                <input type="hidden" name="mode" value="">
                                <button type="submit" class="btn btn-primary float-right" style="background-color: #17a2b8;">
                                  <i class="fas fa-cog"></i>
                                  &nbspLưu
                                </button>
                            </div>
                        </div>
                    </form>
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
 * validation data
 * @param $param
 * @return array
 */
function validation($param): array
{
    $mes = [
        'chk_required'   => [],
        'chk_format'     => [],
        'chk_max_length' => []
    ];

    if (empty($param['fromname'])){
        $mes['chk_required'][] = 'Vui lòng nhập tên email.';
    } elseif (mb_strlen($param['fromname']) > 100){
        $mes['chk_max_length'][] = 'Vui lòng nhập tên email dưới 100 ký tự.';
    }

    if (empty($param['username'])){
        $mes['chk_required'][] = 'Vui lòng nhập email.';
    } elseif (!preg_match('/^[\w\.\-_]+@[\w\.\-_]+\.\w+$/', $param['username'])){
        $mes['chk_format'][] = 'Email không đúng định dạng. Ví dụ: abc@gmail.com.';
    } elseif (mb_strlen($param['username']) > 200 || mb_strlen($param['username']) < 6){
        $mes['chk_max_length'][] = 'Email phải lớn hơn 6 ký tự và bé hơn 200 ký tự.';
    }

    if (empty($param['password'])){
        $mes['chk_required'][] = 'Vui lòng nhập mật khẩu.';
    } elseif (mb_strlen($param['password']) < 6 || mb_strlen($param['password']) > 50) {
        $mes['chk_max_length'][] = 'Mật khẩu phải lớn hơn 6 bé hơn 50 ký tự.';
    }

    if (empty($param['charset'])){
        $mes['chk_required'][] = 'Vui lòng nhập charset.';
    } elseif (mb_strlen($param['charset']) > 10) {
        $mes['chk_max_length'][] = 'Charset phải bé hơn 10 ký tự.';
    }

    if (empty($param['host'])){
        $mes['chk_required'][] = 'Vui lòng nhập host.';
    } elseif (mb_strlen($param['host']) > 100) {
        $mes['chk_max_length'][] = 'Host phải bé hơn 100 ký tự.';
    }

    if (empty($param['smtpsecure'])){
        $mes['chk_required'][] = 'Vui lòng nhập SMTP Secure.';
    } elseif (mb_strlen($param['smtpsecure']) > 10) {
        $mes['chk_max_length'][] = 'SMTP Secure phải bé hơn 10 ký tự.';
    }

    if (empty($param['port'])){
        $mes['chk_required'][] = 'Vui lòng nhập tên port';
    } elseif (!is_numeric($param['port'])){
        $mes['chk_format'][] = 'Vui lòng nhập port bằng số';
    }

    $msg = array_merge(
        $mes['chk_required'],
        $mes['chk_format'],
        $mes['chk_max_length'],
    );
    return $msg;
}

/**
 * get apps inf
 * @param $con
 * @return array|false|string[]|null
 */
function getApps($con){
    $data = [];
    $recCnt = 0;

    $sql = "SELECT*FROM Apps WHERE appsId = 1";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getApps) SQL Error：', $sql.print_r(TRUE));
    } else {
        $recCnt = mysqli_num_rows($query);
    }

    if ($recCnt != 0){
        $data = mysqli_fetch_assoc($query);
    }
    return $data;
}

/**
 * update & insert inf app
 * @param $con
 * @param $param
 * @param $appInf
 */
function updateApps($con, $param, $appInf){
    if (!empty($appInf)){
        $sql = "";
        $sql .= "UPDATE Apps SET                                ";
        $sql .= "  nameEmail = '".$param['fromname']."'         ";
        $sql .= ", mailFrom = '".$param['username']."'          ";
        $sql .= ", username = '".$param['username']."'          ";
        $sql .= ", password = '".$param['password']."'          ";
        $sql .= ", charset = '".$param['charset']."'            ";
        $sql .= ", host = '".$param['host']."'                  ";
        $sql .= ", smtpAuth = ".$param['smtpauth']."            ";
        $sql .= ", smptSecure = '".$param['smtpsecure']."'      ";
        $sql .= ", port = ".$param['port']."                    ";
        $sql .= " WHERE appsId = 1                              ";
    } else {
        $sql = "";
        $sql .= "INSERT INTO Apps(                               ";
        $sql .= "  appsId                                        ";
        $sql .= ", nameEmail                                     ";
        $sql .= ", mailFrom                                      ";
        $sql .= ", username                                      ";
        $sql .= ", password                                      ";
        $sql .= ", charset                                       ";
        $sql .= ", host                                          ";
        $sql .= ", smtpAuth                                      ";
        $sql .= ", smptSecure                                    ";
        $sql .= ", port)                                         ";
        $sql .= " VALUES(                                        ";
        $sql .= " 1                                              ";
        $sql .= ", '".$param['fromname']."'                      ";
        $sql .= ", '".$param['username']."'                      ";
        $sql .= ", '".$param['username']."'                      ";
        $sql .= ", '".$param['password']."'                      ";
        $sql .= ", '".$param['charset']."'                       ";
        $sql .= ", '".$param['host']."'                          ";
        $sql .= ", ".$param['smtpauth']."                        ";
        $sql .= ", '".$param['smtpsecure']."'                    ";
        $sql .= ", ".$param['port'].")                           ";
    }

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(updateApps) SQL Error：', $sql.print_r(TRUE));
    }

    $_SESSION['message'] = 'Cập nhật thành công';
    $_SESSION['messageClass'] = 'alert-success';
    $_SESSION['iconClass'] = 'fas fa-check';

    header('Location: setting-system.php');
    exit();
}
?>
