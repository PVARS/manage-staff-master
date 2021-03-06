<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

session_start();
$con = openDB();

$isStatus = checkStatusUser($con);
if ($isStatus['lockFlg'] == 1){
    header('Location: error-page.php');
    exit();
}

//-----------------------------------------------------------
// HTML
//-----------------------------------------------------------
$titleHTML = '';
$cssHTML = <<<EOF
<style>
.text-limited{
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    font-weight: bold;
}
</style>
EOF;
$scriptHTML = '';
$htmlNew = "";
$htmlNew = showNew($con);
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
<div class="content-wrapper d-flex">
    <div class="row mt-5">
        {$htmlNew}
    </div>
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
function showNew($con)
{
    $sql = "";
    $sql .= "SELECT News.*";
    $sql .= "     , User.fullName";
    $sql .= " FROM News";
    $sql .= " INNER JOIN User";
    $sql .= "   ON User.id = News.createBy";
    $sql .= " ORDER BY News.createDate DESC";
    $query = mysqli_query($con, $sql);


    if (!$query){
        systemError('systemError(getNews) SQL Error：', $sql.print_r(TRUE));
    } else{
        $recCnt = mysqli_num_rows($query);
    }
    $stt = 0;
    $html = '';
    if($recCnt != 0){
        while($row = mysqli_fetch_assoc($query)){
            $stt++;
            $date = date('d/m/Y',$row['createDate']);
            $content = html_entity_decode($row['content']);

            if(empty($row['thumbnail'])){
                $image = "https://i.pinimg.com/736x/dc/5f/50/dc5f502fffc9a064bca7a7be5c8771b2.jpg";
            } else{
                $image = $row['thumbnail'];

            }
            
            $baseUrl = 'http://'.$_SERVER['HTTP_HOST'].'/manage-staff-master/public_html/app/news.php?nid='.$row['id'];

            $html.= <<< EOF
                <div class="col-md-4">
                    <div class="col-md-11" style="margin-left:5%">
                        <div class="card">
                            <img class="card-img-top" src="{$image}" alt="Card image cap" style="height: 300px;">
                            <div class="card-body">
                                <a href="{$baseUrl}"><h2 class="text-limited">{$row['title']}</h2></a>
                                <span>{$row['fullName']} - {$date}</span> <br>
                            </div>
                        </div>
                    </div>
                </div>
    
EOF;

        }
    }
    return $html;
}
?>
