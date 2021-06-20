<?php
    require_once ('config.php');
    require_once ('lib.php');
    session_start();
    $con = openDB();
    $uid = 1;
    $dateNow = currentDate();
    echo $dateNow; exit();
    $sql = "SELECT * FROM checkinout WHERE uid = $uid And Where checkin = $dateNow";
    $resuilt = mysqli_query($con, $sql);
    if ($resuilt){
        systemError('systemError() SQL Error：',$sql.print_r(TRUE));
    } else {
        $cnt = mysqli_num_rows($resuilt);
        echo $cnt; exit();
    }

    if($resuilt){
        echo 'true'; exit();
    }else{
        echo 'flase'; exit();
    }
    $arr = [];
    if($resuilt->num_rows >0){
        while($row = $resuilt->fetch_assoc()){
            $arr[] = $row;
        }
    }


    // if(){
    //     $sql = "INSERT INTO user(email, pass, username) values('$getemail','$getpass','$getname')";
    // }else{
    //     $sql = "update product set title = '$gettitle',price = '$getprice',image = '$getimage' where id='$getid'";
    // }
    $query = mysqli_query($con, $sql);
    $date = currentDateTime();
    echo strtotime($date); exit();
    echo $_GET['uid']; exit();
    if(empty($_SESSION['id']) && isset($_SESSION['id']) ){

    }

?>