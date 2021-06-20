<?php
    require_once ('config.php');
    require_once ('lib.php');
    session_start();
    $con = openDB();
    //$uid =  $_SESSION['id'];
    $uid =  1;
    $sql = "SELECT * FROM checkinout WHERE uid = $uid";
    $resuilt = mysqli_query($con, $sql);
    $arr = [];
    if($resuilt->num_rows >0){
        while($row = $resuilt->fetch_assoc()){
            $arr[] = $row;
        }
    }
   

?>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
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
                    &nbspCheckin -checkout in here 
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
                                <th style="text-align: center; width: 20%;" class="text-th">Checkin at</th>
                                <th style="text-align: center; width: 20%;" class="text-th">Checkout at</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(isset($arr)){
                                    foreach($arr as $key=>$value){
                            ?>
                                <tr>
                                    <td>1</td>
                                    <td><?php echo date("Y-m-d H:i:s", 1624190085);?></td>
                                    <td><?php echo date("Y-m-d H:i:s", $value['checkout']);?></td>
                                </tr>
                            <?php
                                    }
                                }
                            ?>
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

<script>
    $(document).ready(function(){
        $('.check').click(function(){
            console.log("abc");
            $.ajax({
                method: "GET",
                url: "CheckinOut_Ajax.php",
                data: {uid: 1},
                success:function(response){
                    alert(response);
                }
            })
        })
    })
</script>


