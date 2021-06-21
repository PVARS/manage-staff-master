<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

//Initialization
$func_id = 'detail_admin';
$message = '';
$messageClass = '';
$iconClass = '';
$hrefBack = 'home.php';

session_start();

//Get param
$param = getParam();

//Connect DB
$con = openDB();

$mode = $param['mode'] ?? 'new';
$uid = $param['uid'] ?? '';

if (isset($param['dispFrom'])){
    if ($param['dispFrom'] == 'manage-admin') $hrefBack = 'manage-admin.php';
}

if ($param){
    if (isset($param['registFlg']) && $param['registFlg'] == 1){
        if (isset($param['mode']) && $param['mode'] == 'update'){
            updateAdmin($con, $param);
        }
        insertAdmin($con, $param);
    }
}

$fullName = $param['fullname'] ?? '';
$username = $param['username'] ?? '';
$password = $param['password'] ?? '';
$email = $param['email'] ?? '';
$position = $param['position'] ?? '';
$role = $param['role'] ?? '';
$gender = $param['gender'] ?? '';
$phone = $param['phone'] ?? '';
$birthday = $param['birthday'] ?? '';

if (isset($param['uid']) && !empty($param['uid'])){
    $dataAdmin = getAdminById($con, $param);
    $fullName = $dataAdmin['fullName'] ?? $param['fullname'] ?? '';
    $username = $dataAdmin['username'] ?? $param['username'] ?? '';
    $password = $dataAdmin['password'] ?? $param['password'] ?? '';
    $email = $dataAdmin['email'] ?? $param['email'] ?? '';
    $position = $dataAdmin['position'] ?? $param['position'] ?? '';
    $role = $dataAdmin['role'] ?? $param['role'] ?? '';
    $gender = $dataAdmin['gender'] ?? $param['gender'] ?? '';
    $phone = $dataAdmin['phone'] ?? $param['phone'] ?? '';
    $birthday = date('dd/mm/YYYY', $dataAdmin['birthday']) ?? $param['birthday'] ?? '';
}

$genderChecked = [];
$genderChecked[1] = '';
$genderChecked[2] = '';
$valueChecked = $param['gender'] ?? $gender;

if ($valueChecked == 1){
    $genderChecked[1] = 'checked="checked"';
} elseif ($valueChecked == 2)
    $genderChecked[2] = 'checked="checked"';

$htmlSelectPos = '';
$htmlSelectPos = getSelectPosition($con, $position);

$htmlSelectRole = '';
$htmlSelectRole = getSelectRole($con, $role);

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
                        <li class="breadcrumb-item"><a href="home.php">Trang chủ</a></li>
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
                                <h3 class="card-title">Tạo tài khoản</h3>
                            </div>
                            <div class="card-body">
                                <label>Họ tên</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Họ tên" name="fullName" value="{$fullName}">
                                </div>
                                
                                <label>Tên đăng nhập</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="{$username}" placeholder="Tên đăng nhập" name="username">
                                </div>
                                
                                <label>Mật khẩu&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="password"  class="form-control" placeholder="Mật khẩu" name="password" value="{$password}">
                                </div>
                                
                                <label>Email&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Email" name="email" value="{$email}">
                                </div>
                                
                                <label>Vị trí&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    {$htmlSelectPos}
                                </div>
                                
                                <label>Vai trò&nbsp<span class="badge badge-danger">Bắt buộc</span></label>
                                <div class="input-group mb-3">
                                    {$htmlSelectRole}
                                </div>
                                
                                <label>Giới tính</label>
                                <div class="input-group mb-3 ml-2">
                                    <div class="row">
                                        <div class="form-check mr-3">
                                            <input class="form-check-input" type="radio" name="gender" value="1" {$genderChecked[1]}>
                                            <label class="form-check-label">Nam</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" value="2" {$genderChecked[2]}>
                                            <label class="form-check-label">Nữ</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <label>Số điện thoại</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Số điện thoại" name="phone" value="{$phone}">
                                </div>
                                
                                <label>Ngày sinh</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" name="birthday" class="form-control" value="{$birthday}">
                                </div>
                                
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <input type="hidden" class="mode" name="mode" value="{$mode}">
                                <input type="hidden" name="registFlg" value="1">
                                <input type="hidden" name="uid" value="{$uid}">
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

function getAdminById($con, $param){
    $data = [];
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT fullName                      ";
    $sql .= "     , username                      ";
    $sql .= "     , password                      ";
    $sql .= "     , email                         ";
    $sql .= "     , gender                        ";
    $sql .= "     , position                      ";
    $sql .= "     , role                          ";
    $sql .= "     , phone                         ";
    $sql .= "     , birthday                      ";
    $sql .= "  FROM User                          ";
    $sql .= " WHERE id = ".$param['uid']."        ";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getAllAdmin) SQL Error：', $sql.print_r(TRUE));
    } else {
        $recCnt = mysqli_num_rows($query);
    }

    if ($recCnt != 0){
        $data = mysqli_fetch_assoc($query);
    }
    return $data;
}

function updateAdmin($con, $param){
    if (!empty($param['birthday'])){
        $birthday = strtotime($param['birthday']);
    } else $birthday = 'NULL';

    $sql = "";
    $sql .= "UPDATE User SET";
    $sql .= "       username = '".$param['username']."'                                             ";
    $sql .= "     , fullName = '".$param['fullName']."'                                             ";
    $sql .= "     , password = '".password_hash($param['password'], PASSWORD_DEFAULT)."'       ";
    $sql .= "     , email = '".$param['email']."'                                                   ";
    $sql .= "     , position = ".$param['position']."                                               ";
    $sql .= "     , role = ".$param['role']."                                                       ";
    $sql .= "     , gender = ".$param['gender']."                                                   ";
    $sql .= "     , phone = '".$param['phone']."'                                                   ";
    $sql .= "     , birthday = ".$birthday."                                                        ";
    $sql .= "     , modifyDate = ".strtotime(currentDateTime())."                                   ";
    $sql .= "     , modifyBy = ".$_SESSION['uid']."                                                 ";
    $sql .= " WHERE id = ".$param['uid']."                                                          ";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getAllAdmin) SQL Error：', $sql.print_r(TRUE));
    }

    $_SESSION['message'] = 'Cập nhật thành công';
    $_SESSION['messageClass'] = 'alert-success';
    $_SESSION['iconClass'] = 'fas fa-check';

    header('Location: manage-admin.php');
    exit();
}

function insertAdmin($con, $param){
    if (!empty($param['birthday'])){
        $birthday = strtotime($param['birthday']);
    } else $birthday = 'NULL';

    $sql = "";
    $sql .= "INSERT INTO User(                                                                ";
    $sql .= "            username                                                             ";
    $sql .= "          , fullName                                                             ";
    $sql .= "          , password                                                             ";
    $sql .= "          , email                                                                ";
    $sql .= "          , position                                                             ";
    $sql .= "          , role                                                                 ";
    $sql .= "          , gender                                                               ";
    $sql .= "          , phone                                                                ";
    $sql .= "          , birthday                                                             ";
    $sql .= "          , createDate                                                           ";
    $sql .= "          , createBy)                                                            ";
    $sql .= " VALUES(                                                                         ";
    $sql .= "         '".$param['username']."'                                                ";
    $sql .= "       , '".$param['fullName']."'                                                ";
    $sql .= "       , '".password_hash($param['password'], PASSWORD_DEFAULT)."'          ";
    $sql .= "       , '".$param['email']."'                                                   ";
    $sql .= "        , ".$param['position']."                                                 ";
    $sql .= "        , ".$param['role']."                                                     ";
    $sql .= "        , ".$param['gender']."                                                   ";
    $sql .= "       , '".$param['phone']."'                                                   ";
    $sql .= "        , ".$birthday."                                                          ";
    $sql .= "        , ".strtotime(currentDateTime())."                                       ";
    $sql .= "        , ".$_SESSION['uid'].")                                                  ";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getAllAdmin) SQL Error：', $sql.print_r(TRUE));
    }

    $_SESSION['message'] = 'Thêm thành công';
    $_SESSION['messageClass'] = 'alert-success';
    $_SESSION['iconClass'] = 'fas fa-check';

    header('Location: manage-admin.php');
    exit();
}

function getSelectPosition($con, $position){
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT id               ";
    $sql .= "     , namePosition     ";
    $sql .= "  FROM postion          ";
    $sql .= "  ORDER BY id ASC       ";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getSelectPosition) SQL Error：', $sql . print_r(true));
    } else {
        $recCnt = mysqli_num_rows($query);
    }

    $html = '<select class="custom-select" name="position">';
    $html .= '<option value="0">-- Chọn vị trí --</option>';
    if ($recCnt != 0){
        while ($row = mysqli_fetch_assoc($query)){
            $selected = '';
            if ($position == $row['id']){
                $selected = 'selected="selected"';
            }
            $html .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['namePosition'].'</option>';
        }
    }
    $html .= '</select>';
    return $html;
}

function getSelectRole($con, $role){
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT id               ";
    $sql .= "     , name             ";
    $sql .= "  FROM role             ";
    $sql .= "  ORDER BY id ASC       ";

    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getSelectPosition) SQL Error：', $sql . print_r(true));
    } else {
        $recCnt = mysqli_num_rows($query);
    }

    $html = '<select class="custom-select" name="role">';
    $html .= '<option value="0">-- Chọn vai trò --</option>';
    if ($recCnt != 0){
        while ($row = mysqli_fetch_assoc($query)){
            $selected = '';
            if ($role == $row['id']){
                $selected = 'selected="selected"';
            }
            $html .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['name'].'</option>';
        }
    }
    $html .= '</select>';
    return $html;
}
?>