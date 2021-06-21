<?php
require_once ('config.php');
require_once ('lib.php');
class PostionReponsitory
{
    private $con;
    private $param;

    public function __construct()
    {
        $this->param = getParam();
        $this->con = openDB();
    }

    public function getAllPosition(){
        $data = [];
        $recCnt = 0;

        $sql = "";
        $sql .= "SELECT *";
        $sql .= "  FROM Postion";
        $sql .= " ORDER BY lockFlg DESC";
        $sql .= "     , createDate DESC";

        $query = mysqli_query($this->con, $sql);
        if (!$query){
            systemError('systemError(getAllPosition) SQL Error：', $sql.print_r(TRUE));
        } else
            $recCnt = mysqli_num_rows($query);

        if ($recCnt != 0){
            $data = mysqli_fetch_all($query, MYSQLI_ASSOC);
        }
        return $data;

    }

}

?>