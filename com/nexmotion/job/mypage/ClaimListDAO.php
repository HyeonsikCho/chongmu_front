<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MypageCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/ClaimListHTML.php');

class ClaimListDAO extends MypageCommonDAO {
 
    /**
     * @brief 클레임공통 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectClaimList($conn, $param) {

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
            $query .= "\n           ,A.title      AS print_title";
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
            $query .= "\n           ,B.title";
            $query .= "\n           ,B.dvs";
            $query .= "\n           ,B.regi_date";
            $query .= "\n           ,B.state";
            $query .= "\n           ,B.order_claim_seqno";
            $query .= "\n           ,C.file_path";
            $query .= "\n           ,C.save_file_name";
        }
        $query .= "\n      FROM  order_common A";
        $query .= "\n           ,order_claim B";
        $query .= "\n      LEFT  OUTER JOIN order_file C";
        $query .= "\n        ON  B.order_common_seqno = C.order_common_seqno";
        $query .= "\n     WHERE  A.order_common_seqno = B.order_common_seqno";
        $query .= "\n       AND  A.member_seqno = " . $param["seqno"];

        //인쇄물제목 검색
        if ($this->blankParameterCheck($param ,"title")) {
            $title = substr($param["title"], 1, -1);
            $query .= "\n    AND  B.title LIKE '%" . $title . "%'";
        }

        //상태
        if ($this->blankParameterCheck($param ,"state")) {

            $query .= "\n    AND  B.state = " . $param["state"];

        }

        //사고유형
        if ($this->blankParameterCheck($param ,"dvs")) {

            $query .= "\n    AND  B.dvs = " . $param["dvs"];

        }

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {

            $from = substr($param["from"], 1, -1);

            $query .="\n     AND  B.regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {

            $to = substr($param["to"], 1, -1);

            $query .="\n     AND  B.regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($type == "SEQ") {

            $query .= "\n ORDER BY B.order_claim_seqno DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;

        }

        return $conn->Execute($query);
    }


}
?>
