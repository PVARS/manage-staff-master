<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

//Initialization
$func_id = 'detail_admin';
$message = '';
$messageClass = '';
$iconClass = '';

session_start();

//Get param
$param = getParam();

//Connect DB
$con = openDB();


if ($param){

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
                        <i class="fas fa-folder-plus"></i>&nbspTạo tài khoản</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Tạo tài khoản</li>
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
                    <a href="list-users.php" class="btn btn-primary float-right mr-3" style="background-color: #17a2b8;" title="Danh sách người dùng">
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
                                <h3 class="card-title">Tạo tài khoản</h3>
                            </div>
                            <div class="card-body">
                                <label>Họ tên&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Họ tên" name="fullname" value="">
                                </div>
                                <label>Người tạo</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="" readonly name="createBy">
                                </div>
                                <label>Email&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text"  class="form-control" placeholder="Email" name="email" value="">
                                </div>
                                <label>Tên đăng nhập&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Tên đăng nhập" name="loginId" value="">
                                </div>
                                
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <input type="hidden" class="mode" name="mode" value="">
                                <input type="hidden" name="registFlg" value="1">
                                <input type="hidden" name="uid" value="">
                                <a href="" id="deleteUser" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                    &nbspXoá
                                </a>
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

/**
 * Validation data
 * @param $con
 * @param $func_id
 * @param $param
 * @return array
 */
//function validateData($con, $func_id, $param){
//    $dupLoginId = getCheckDupLogId($con, $func_id, $param);
//    $dupEmail   = getCheckDupEmail($con, $func_id, $param);
//
//    $mes = [
//        'chk_required'   => [],
//        'chk_format'     => [],
//        'chk_max_length' => []
//    ];
//
//    if (empty($param['fullname'])){
//        $mes['chk_required'][] = 'Vui lòng nhập họ tên.';
//    } elseif (mb_strlen($param['fullname']) > 254){
//        $mes['chk_max_length'][] = 'Họ tên phải bé hơn 254 ký tự.';
//    }
//
//    if ($param['role'] == 0){
//        $mes['chk_required'][] = 'Vui lòng chọn vai trò cho tài khoản.';
//    }
//
//    if (empty($param['email'])){
//        $mes['chk_required'][] = 'Vui lòng nhập email.';
//    } elseif (!preg_match('/^[\w\.\-_]+@[\w\.\-_]+\.\w+$/', $param['email'])){
//        $mes['chk_format'][] = 'Email không đúng định dạng. Ví dụ: abc@gmail.com';
//    } elseif (mb_strlen($param['email']) > 254 || mb_strlen($param['email']) < 6){
//        $mes['chk_max_length'][] = 'Email phải lớn hơn 6 ký tự và bé hơn 254 ký tự.';
//    }
//
//    if (empty($param['loginId'])){
//        $mes['chk_required'][] = 'Vui lòng nhập tên đăng nhập.';
//    } elseif (!preg_match('/^[0-9A-Za-z]/', $param['loginId']) || preg_match('/^(?=.*[@#\-_$%^&+=§!\?])/', $param['loginId'])){
//        $mes['chk_format'][] = 'Tên đăng nhập không được chứa kí tự đặc biệt.';
//    }
//    elseif (mb_strlen($param['loginId']) > 254 || mb_strlen($param['loginId']) < 6){
//        $mes['chk_max_length'][] = 'Tên đăng nhập phải hơn 6 ký tự và bé hơn 254 ký tự.';
//    }
//
//    if ($param['mode'] == 'new'){
//        if (empty($param['password'])){
//            $mes['chk_required'][] = 'Vui lòng nhập mật khẩu.';
//        } elseif (!preg_match('/^(?=.*[0-9A-Za-z])/', $param['password']) || !preg_match('/^(?=.*[@#\-_$%^&+=§!\?])/', $param['password'])){
//            $mes['chk_format'][] = 'Mật khẩu không đúng định dạng, phải có ít nhất 1 chữ hoặc số và ký tự đặc biệt.';
//        }
//        elseif (mb_strlen($param['password']) > 254 || mb_strlen($param['password']) < 6){
//            $mes['chk_max_length'][] = 'Mật khẩu phải lớn hơn 6 ký tự và bé hơn 254 ký tự.';
//        }
//    }
//
//    $msg = array_merge(
//        $mes['chk_required'],
//        $mes['chk_format'],
//        $mes['chk_max_length']
//    );
//
//    if ($param['mode'] == 'new'){
//        if (empty($msg)){
//            if (!empty($dupEmail)){
//                $msg[] = 'Email đã được sử dụng';
//            }
//
//            if (!empty($dupLoginId)){
//                $msg[] = 'Tên đăng nhập đã được sử dụng';
//            }
//        }
//    }
//    return $msg;
//}
?>