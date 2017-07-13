<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MypageCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/ClaimSelectHTML.php');

class ClaimSelectDAO extends MypageCommonDAO {
 
    /**
     * @brief 클레임공통 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);

        if ($type === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n    SELECT  A.order_regi_date";
            $query .= "\n           ,A.order_num";
            $query .= "\n           ,A.title";
            $query .= "\n           ,A.order_detail";
            $query .= "\n           ,A.amt";
            $query .= "\n           ,A.count";
            $query .= "\n           ,A.pay_price";
            $query .= "\n           ,A.order_state";
            $query .= "\n           ,A.order_common_seqno";
            $query .= "\n           ,A.grade_sale_price";
            $query .= "\n           ,A.event_price";
            $query .= "\n           ,A.cp_price";
            $query .= "\n           ,A.expec_weight";
            $query .= "\n           ,A.dlvr_way";
            $query .= "\n           ,A.receipt_dvs";
            $query .= "\n           ,B.file_path";
            $query .= "\n           ,B.save_file_name";
        }
        $query .= "\n      FROM  order_common A";
        $query .= "\n      LEFT  OUTER JOIN order_file B";
        $query .= "\n        ON  A.order_common_seqno = B.order_common_seqno";
        $query .= "\n     WHERE  A.member_seqno = " . $param["seqno"];
        $query .= "\n       AND  A.order_state = '020'";

        //인쇄물제목 검색
        if ($this->blankParameterCheck($param ,"title")) {
            $title = substr($param["title"], 1, -1);
            $query .= "\n    AND  A.title LIKE '%" . $title . "%'";
        }

        //배송종류
        if ($this->blankParameterCheck($param ,"dlvr_way")) {
            $search_txt = substr($param["dlvr_way"], 1, -1);
            $query .= "\n    AND  A.dlvr_way = " . $param["dlvr_way"];
        }

        $start_range = substr($param["start_range"], 1, -1);
        $query .= "\n       AND  A.dlvr_finish_date >='" . $start_range;
        $query .=" 00:00:00'";

        $end_range = substr($param["end_range"], 1, -1);
        $query .= "\n       AND  A.dlvr_finish_date <='" . $end_range;
        $query .=" 23:59:59'";

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {

            $from = substr($param["from"], 1, -1);

            $query .="\n     AND  A.order_regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {

            $to = substr($param["to"], 1, -1);

            $query .="\n     AND  A.order_regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($type == "SEQ") {

            $query .= "\n ORDER BY A.order_common_seqno DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;

        }

        return $conn->Execute($query);
    }

    /**
     * @brief 클레임 갯수 COUNT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function countClaimList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);

        $query  = "\n    SELECT  COUNT(*) AS cnt";
        $query .= "\n      FROM  order_common A";
        $query .= "\n           ,order_claim B";
        $query .= "\n     WHERE  A.order_common_seqno = B.order_common_seqno";
        $query .= "\n       AND  A.order_common_seqno = " . $param["order_seqno"];

        return $conn->Execute($query);
    }

}
?>
