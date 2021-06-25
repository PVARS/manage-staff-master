<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

session_start();
$con = openDB();

if ($isStatus['lockFlg'] == 1){
    header('Location: error-page.php');
    exit();
}

$nid = $_GET['nid'];
if (!is_numeric($nid)){
    header('Location: not-found.php');
    exit();
}
//-----------------------------------------------------------
// HTML
//-----------------------------------------------------------
$titleHTML = '';
$cssHTML = '';
$scriptHTML = '';
$htmlNews = '';
$htmlNews = showDetailNews($con,$nid);

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
        {$htmlNews}
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
function showDetailNews($con,$nid){
    $recCnt = 0;
    $html = "";
    $sql = "";
    $sql .= "SELECT News.*";
    $sql .= "     , User.fullName";
    $sql .= " FROM News";
    $sql .= " INNER JOIN User";
    $sql .= "   ON User.id = News.createBy";
    $sql .= " WHERE News.id = ".$nid."    ";


    $query = mysqli_query($con, $sql);
    if (!$query){
        systemError('systemError(getAllPosition) SQL Error：', $sql.print_r(TRUE));
    } else {
        $recCnt = mysqli_num_rows($query);
    }

    if ($recCnt != 0){
        while ($row = mysqli_fetch_assoc($query)){
            $date = date('d/m/Y',$row['createDate']);
            $dateTime = date('H:i:s',$row['createDate']);
            if(empty($row['thumbnail'])){
                $image = "https://i.pinimg.com/736x/dc/5f/50/dc5f502fffc9a064bca7a7be5c8771b2.jpg";
            } else{
                $image = $row['thumbnail'];
            }
            $content = html_entity_decode($row['content']);
            $html.= <<< EOF
            <div class="container jumbotron mt-5">
                <h1 class="mb-4"><b>{$row['title']}</b></h1>
                <p>{$row['fullName']} - {$date} vào lúc {$dateTime}</p>
                <img class="card-img-top" src="{$image}">
                <div class="mt-5">
                {$content}
                </div>
            </div>

EOF;
        }
    } else {
        header('Location: not-found.php');
        exit();
    }
    return $html;
}

?>
