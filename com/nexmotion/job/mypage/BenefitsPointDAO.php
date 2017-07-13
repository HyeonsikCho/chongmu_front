<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MypageCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/BenefitsPointHTML.php');

class BenefitsPointDAO extends MypageCommonDAO {
 
    /**
     * @brief 포인트 내역 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectPointList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);

        if ($type === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n    SELECT  regi_date";
            $query .= "\n           ,point";
            $query .= "\n           ,rest_point";
            $query .= "\n           ,order_price";
            $query .= "\n           ,dvs";
            $query .= "\n           ,order_num";
        }
        $query .= "\n      FROM  member_point_history";
        $query .= "\n     WHERE  member_seqno = " . $param["seqno"];

        //구분
        if ($this->blankParameterCheck($param ,"dvs")) {

            $query .= "\n    AND  dvs = " . $param["dvs"];

        }

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {

            $from = substr($param["from"], 1, -1);

            $query .="\n     AND  regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {

            $to = substr($param["to"], 1, -1);

            $query .="\n     AND  regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($type == "SEQ") {

            $query .= "\n ORDER BY member_point_history_seqno DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;

        }

        return $conn->Execute($query);
    }



}
?>
