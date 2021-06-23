<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

session_start();

$con = openDB();
$htmlShowData = '';
$htmlShowData = showData($con);

if (!isset($_SESSION['uid']) || empty($_SESSION)){
    header('location: login.php');
    exit();
}
$status = $param['email'] ?? '';
if ($param){
    // if (empty($mes)) $htmlShowData;

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
    $(document).ready(function(){
        $('.check').click(function(){
            console.log("abc");
            $.ajax({
                method: "GET",
                url: "CheckinOut_Ajax.php",
                data: {uid: 1},
                success:function(response){
                    location.reload();
                }
            })
        })
    })
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
            {$messageHtml}
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-search"></i>&nbspCheckin - checkOut </h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Checkin/out</li>
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
                    <button type="submit" class="btn btn-primary check" style="background-color: #17a2b8;width:300px;">
                    <i class="fas fa-calendar-check"></i>
                    &nbspĐiểm danh
                    </button>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="card-body table-responsive">
                    <table class="table table-hover text-nowrap table-bordered" style="background-color: #FFFFFF;">
                        <thead style="background-color: #17A2B8;">
                            <tr>
                                <th style="text-align: center; width: 5%;" class="text-th">STT</th>
                                <th style="text-align: center; width: 20%;" class="text-th">Ngày điểm danh</th>
                                <th style="text-align: center; width: 20%;" class="text-th">Thời gian đến </th>
                                <th style="text-align: center; width: 20%;" class="text-th">Thời gian về</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                            {$htmlShowData}
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

function showData($con)
{
    $uid =  $_SESSION['uid'];
    $recCnt = 0;
    $sql = "SELECT * FROM CheckInOut WHERE uid = ".$uid." ";
    
    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(showData) SQL Error：', $sql.print_r(TRUE));
    } else{
        $recCnt = mysqli_num_rows($query);
    }

    $html = '';
    $cnt = 0;
    if($recCnt != 0){
        while($row = mysqli_fetch_assoc($query)){
            $cnt++;
            $dateCheck = date("d-m-Y", $row['DateCheck']);
            $checkin = date("H:i:s", $row['checkin']);
            $checkout = date("H:i:s", $row['checkout']);
            if($checkin < "08:15:00"){
                $status = "color: #69aa46;";
            }else{
                $status = "color: red;";
            }
            if($checkout > "17:30:00"){
                $statusCheckout = "color: #69aa46;";
            }else{
                $statusCheckout = "color: red;";
            }
            $html.= <<< EOF
            <tr>
            <td style="text-align: center; width: 5%;" >{$cnt}</td>
            <td style="text-align: center; width: 20%;">{$dateCheck}</td>
            <td style="text-align: center; width: 20%;{$status}">{$checkin}</td>
            <td style="text-align: center; width: 20%;{$statusCheckout}">{$checkout}</td>
            </tr>
EOF;
        }
    }
    return $html;
    
}


?>
