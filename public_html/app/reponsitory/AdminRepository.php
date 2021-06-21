<?php
require_once ('D:\xampp\htdocs\project\manage-staff-master\public_html\app\config.php');
require_once ('D:\xampp\htdocs\project\manage-staff-master\public_html\app\lib.php');

class AdminRepository
{
    private $con;
    private $param;

    public function __construct()
    {
        $this->param = getParam();
        $this->con = openDB();
    }

    public function getAllAdmin(){
        $data = [];
        $recCnt = 0;

        $sql = "";
        $sql .= "SELECT id";
        $sql .= "     , username";
        $sql .= "     , fullName";
        $sql .= "     , role";
        $sql .= "     , position";
        $sql .= "     , gender";
        $sql .= "     , email";
        $sql .= "     , phone";
        $sql .= "     , birthday";
        $sql .= "     , lockFlg";
        $sql .= "  FROM user";
        $sql .= " ORDER BY lockFlg DESC";
        $sql .= "     , createDate DESC";

        $query = mysqli_query($this->con, $sql);
        if (!$query){
            systemError('systemError(getAllAdmin) SQL Error：', $sql.print_r(TRUE));
        } else
            $recCnt = mysqli_num_rows($query);

        if ($recCnt != 0){
            $data = mysqli_fetch_all($query, MYSQLI_ASSOC);
        }
        return $data;
    }

    function searchInfAdmin(){

    }
}