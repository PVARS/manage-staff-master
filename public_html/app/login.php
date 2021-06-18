<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

//Initialization
$funcId      = 'login';
$message      = '';
$messageClass = '';
$iconClass    = '';

session_start();

//Connect DB
$con = openDB();

//Get param
$param = getParam();

$username = $param['username'] ?? '';
$password = $param['password'] ?? '';

$isLogin = login($con, $param, $funcId);

if ($param){
    if (isset($param['registFlg']) && $param['registFlg'] == 1){
        $mes = [];
        if (empty($username)){
            $mes[] = 'Vui lòng nhập tên đăng nhập';
        }

        if (empty($password)){
            $mes[] = 'Vui lòng nhập mật khẩu';
        }

        if (empty($mes)){
            if ($isLogin){
                $_SESSION['username'] = $isLogin['username'];
                $_SESSION['password'] = $isLogin['role'];
                $_SESSION['role'] = $isLogin['role'];

                header('location: manage-postion.php');
                exit();
            } else
                $mes[] = 'Tên đăng nhập hoặc mật khẩu không đúng';
        }

        $message = join('<br>', $mes);
        if (strlen($message)) {
            $messageClass = 'alert-danger';
            $iconClass = 'fas fa-ban';
        }
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
<body class="hold-transition login-page" id="{$funcId}">
    <div class="login-box">
    {$messageHtml}
EOF;

//Conntent
echo <<<EOF
<div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="login.php" class="h1"><b>Đăng Nhập</a>
            </div>
            <div class="card-body">
                <form action="{$_SERVER['SCRIPT_NAME']}" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" value="{$username}" autocomplete="off">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Mật khẩu" value="{$password}" autocomplete="off">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-7">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">Lưu đăng nhập</label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-5">
                            <input type="hidden" name="registFlg" value="1">
                            <button type="submit" name="login" class="btn btn-primary btn-block">Đăng nhập</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.card-body -->
        </div>
EOF;

//Meta JS
include ($TEMP_APP_METAJS_PATH);
echo <<<EOF
    </div>
</body>
</html>
EOF;

function login($con, $param, $funcId){
    $cnt = 0;

    $sql = "";
    $sql .= "SELECT username                                ";
    $sql .= "     , password                                ";
    $sql .= "     , role                                    ";
    $sql .= "  FROM User                                    ";
    $sql .= " WHERE username = '".$param['username']."'       ";
    $sql .= "   AND password = '".$param['password']."'       ";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError('.$funcId.') SQL Error：',$sql.print_r(TRUE));
    } else {
        $cnt = mysqli_num_rows($query);
    }

    if ($cnt != 0){
        $data = mysqli_fetch_assoc($query);
    }
    return $data;
}
?>