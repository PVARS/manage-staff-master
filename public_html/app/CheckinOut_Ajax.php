<?php
    require_once ('config.php');
    require_once ('lib.php');
    session_start();
    $con = openDB();
    $uid = $_SESSION['uid'];
    $dateNow = strtotime(currentDate());
    $dateTimeNow = strtotime(currentDateTime());
    $sql = "SELECT * FROM CheckInOut WHERE uid = $uid And DateCheck = $dateNow";
    $query = mysqli_query($con, $sql);
    $recCnt = 0;

    if (!$query){
        systemError('systemError(getSelectPosition) SQL Error：', $sql . print_r(true));
    } else {
        $recCnt = mysqli_num_rows($query);
    }

    if ($recCnt != 0){
        $sql1 = "UPDATE CheckInOut SET checkout = '$dateTimeNow' where uid ='$uid'";
    }else{
        $sql1 = "INSERT INTO CheckInOut(checkin, checkout, uid,DateCheck) values('$dateTimeNow','$dateTimeNow','$uid','$dateNow')";
    }
    $query1 = mysqli_query($con,$sql1);
    $_SESSION['message'] = 'Điểm danh thành công';
    $_SESSION['messageClass'] = 'alert-success';
    $_SESSION['iconClass'] = 'fas fa-check';

?>