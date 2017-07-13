<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MypageCommonDAO.php');
//include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/ClaimWriteHTML.php');

class ClaimWriteDAO extends MypageCommonDAO {
 
    /**
     * @brief 주문상세 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderDetail($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $query  = "\n    SELECT  A.order_regi_date";
        $query .= "\n           ,A.order_num";
        $query .= "\n           ,A.title      AS print_title";
        $query .= "\n           ,A.order_detail";
        $query .= "\n           ,A.amt";
        $query .= "\n           ,A.count";
        $query .= "\n           ,A.pay_price";
        $query .= "\n           ,A.order_common_seqno";
        $query .= "\n           ,A.grade_sale_price";
        $query .= "\n           ,A.event_price";
        $query .= "\n           ,A.cp_price";
        $query .= "\n           ,A.expec_weight";
        $query .= "\n           ,A.dlvr_way";
        $query .= "\n           ,A.receipt_dvs";
        $query .= "\n           ,B.file_path";
        $query .= "\n           ,B.save_file_name";
        $query .= "\n           ,C.cate_name";
        $query .= "\n      FROM  cate C";
        $query .= "\n           ,order_common A";
        $query .= "\n      LEFT  OUTER JOIN order_file B";
        $query .= "\n        ON  A.order_common_seqno = B.order_common_seqno";
        $query .= "\n     WHERE  A.cate_sortcode = C.sortcode";
        $query .= "\n       AND  A.member_seqno = " . $param["member_seqno"];
        $query .= "\n       AND  A.order_common_seqno = ";
        $query .= $param["order_seqno"];
 
        return $conn->Execute($query);
    }
 
    /**
     * @brief 클레임댓글 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectClaimComment($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $query  = "\n    SELECT  comment";
        $query .= "\n           ,regi_date";
        $query .= "\n           ,cust_yn";
        $query .= "\n      FROM  order_claim_comment";
        $query .= "\n     WHERE  order_claim_seqno = ";
        $query .= $param["claim_seqno"];
        $query .= "\n  ORDER BY regi_date ASC";
 
        return $conn->Execute($query);

    }

}

?>
