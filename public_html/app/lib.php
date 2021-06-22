<?php
/**
 * Connect db
 * @return mysqli
 */
function openDB(){
    global $DB_CONNECT_PATH;

    require (dirname(__FILE__) . $DB_CONNECT_PATH);
    $connect = mysqli_connect($dsn['host'], $dsn['user'], $dsn['password'], $dsn['dbname']);
    if(!$connect){
        systemError('systemError(lib) Database connection error');
    }
    return $connect;
}

/**
 * Error page
 */
function systemErrorPrint(){
    echo <<<EOF
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8">
    <title>System Error</title>
    </head>
    <body id="systemError">
    <section id="main">
        <article id="login_form" class="module width_half">
            <header><h3>The system is paused</h3></header>
            <div class="module_content">
                <p>We apologize for the inconvenience. <br /> Excuse me, but please wait a little longer.</p>
            </div>
        </article>
    </section>
    
    </body>
    </html>
EOF;
}

/**
 * Notification error
 */
function systemError(){
    systemErrorPrint();
    exit();
}

/**
 * Eliminate full-width and half-width spaces
 * @param $str
 * @return string
 */
function trimBlank($str){
    $stringValue = $str;
    $stringValue=trim($stringValue);
    
    return $stringValue;
}

/**
 * Get param
 * @return array
 */
function getParam(){
    $param = array();
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $a = $_POST;
    }else{
        $a = $_GET;
    }
    foreach($a as $k => $v) {
        if (is_array($v)) {
            foreach($v as $k2 => $v2) {
                if(get_magic_quotes_gpc()) {
                    $v2 = stripslashes($v2);
                }
                $v2 = htmlspecialchars($v2,ENT_QUOTES);
                $v2 = trimBlank($v2);
                $param[$k][$k2] = $v2;
            }
        }else{
            if(get_magic_quotes_gpc()) {
                $v = stripslashes($v);
            }
            $v = htmlspecialchars($v,ENT_QUOTES);
            $v = trimBlank($v);
            $param[$k] = $v;
        }
    }
    return $param;
}

/**
 * check status account user
 * @param $db
 * @return mixed
 */
function checkStatusUser($db){
    $recCnt = 0;
    $status = [];

    $sql = "";
    $sql .= "SELECT lockFlg                      ";
    $sql .= "  FROM User                       ";
    $sql .= " WHERE id = '".$_SESSION['uid']."'    ";

    $query = mysqli_query($db, $sql);
    if (!$query){
        systemError('systemError(getDelDate) SQL Error：',$sql.print_r(TRUE));
    } else {
        $recCnt = mysqli_num_rows($query);
    }

    if ($recCnt != 0){
        $status = mysqli_fetch_assoc($query);
    }
    return $status;
}

function checkDupUsername($db, $username): array
{
    $recCnt = 0;
    $data = [];

    $sql = "";
    $sql .= "SELECT username                     ";
    $sql .= "  FROM User                         ";
    $sql .= " WHERE username= '".$username."'    ";

    $query = mysqli_query($db, $sql);
    if (!$query){
        systemError('systemError(checkDupUsername) SQL Error：', $sql . print_r(true));
    } else {
        $recCnt = mysqli_num_rows($query);
    }

    if ($recCnt != 0){
        $data = mysqli_fetch_assoc($query);
    }
    return $data;
}

/**
 * Duplicate email check
 * @param $db
 * @param $email
 * @return array
 */
function checkDupEmail($db, $email): array
{
    $recCnt = 0;
    $data = [];

    $sql = "";
    $sql .= "SELECT email                           ";
    $sql .= "  FROM User                            ";
    $sql .= " WHERE email= '".$email."'             ";

    $query = mysqli_query($db, $sql);
    if (!$query){
        systemError('systemError(checkDupUsername) SQL Error：', $sql . print_r(true));
    } else {
        $recCnt = mysqli_num_rows($query);
    }

    if ($recCnt != 0){
        $data = mysqli_fetch_assoc($query);
    }
    return $data;
}

/**
 * Duplicate email check by username
 * @param $db
 * @param $email
 * @param $username
 * @return array
 */
function checkDupEmailByUsername($db, $email, $username): array
{
    $recCnt = 0;
    $data = [];

    $sql = "";
    $sql .= "SELECT email                      ";
    $sql .= "  FROM User                       ";
    $sql .= " WHERE email = '".$email."'       ";
    $sql .= " AND username = '".$username."'   ";

    $query = mysqli_query($db, $sql);
    if (!$query){
        systemError('systemError(checkDupUsername) SQL Error：', $sql . print_r(true));
    } else {
        $recCnt = mysqli_num_rows($query);
    }

    if ($recCnt != 0){
        $data = mysqli_fetch_assoc($query);
    }
    return $data;
}

/**
 * Get Css of Menu
 * @param $role
 * @return array
 */
function getCssOfMenu($role): array
{
    $navs       = array();
    $requestURI = $_SERVER['REQUEST_URI'];

    // Is Dashboard click?
    if (strpos($requestURI, 'dashboard') !== false) {
        $navs['navLinkActive'] = 'nav-link-dashboard';
    }
    // Is Setting system click?
    else if (strpos($requestURI, 'setting-system') !== false) {
        $navs['navLinkActive'] = 'nav-link-setting-system';
    }
    // Is Accept post click?
    else if (strpos($requestURI, 'accept-post') !== false) {
        $navs['navLinkActive'] = 'nav-link-accept-post';
    }
    // Is New click? Click on Detail or List
    else if (strpos($requestURI, 'new') !== false) {
        $navs['navLinkOnlick'] = 'nav-link-new';
        if (strpos($requestURI, 'detail') !== false) {
            $navs['navLinkActive'] = 'nav-link-new-detail';
        } else if (strpos($requestURI, 'list') !== false) {
            $navs['navLinkActive'] = 'nav-link-new-list';
        }
    }

    // Is it an admin or a system?
    if ($role == 'admin' || $role == 'system') {
        // Is Category click? Click on Detail or List
        if (strpos($requestURI, 'categor') !== false) {
            $navs['navLinkOnlick'] = 'nav-link-category';
            if (strpos($requestURI, 'detail') !== false) {
                $navs['navLinkActive'] = 'nav-link-category-detail';
            } else if (strpos($requestURI, 'list') !== false) {
                $navs['navLinkActive'] = 'nav-link-categories-list';
            }
        }
    }

    // Is it a system?
    if ($role == 'system') {
        // Is User click? Click on Detail or List
        if (strpos($requestURI, 'user') !== false) {
            $navs['navLinkOnlick'] = 'nav-link-user';
            if (strpos($requestURI, 'detail') !== false) {
                $navs['navLinkActive'] = 'nav-link-user-detail';
            } else if (strpos($requestURI, 'list') !== false) {
                $navs['navLinkActive'] = 'nav-link-user-list';
            }
        }
    }

    return $navs;
}

/**
 * Get Datetime mpw
 * @return false|string
 */
function currentDateTime(){
    $datenow = '';
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $datenow = date("Y-m-d H:i:s");
    return $datenow;
}

/**
 * Get Day now
 * @return false|string
 */
function currentDate(){
    $datenow = '';
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $datenow = date("Y-m-d");
    return $datenow;
}

function sendEmail(){

}
?>
