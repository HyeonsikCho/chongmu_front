<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MypageCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/ClaimViewHTML.php');

class ClaimViewDAO extends MypageCommonDAO {
 
    /**
     * @brief 클레임상세 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectClaimDetail($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n  SELECT  T1.*";
        $query .= "\n         ,C.name";
        $query .= "\n         ,D.file_path";
        $query .= "\n         ,D.save_file_name";
        $query .= "\n    FROM (";
        $query .= "\n          SELECT  A.order_regi_date";
        $query .= "\n                 ,A.order_num";
        $query .= "\n                 ,A.title      AS print_title";
        $query .= "\n                 ,A.order_detail";
        $query .= "\n                 ,A.amt";
        $query .= "\n                 ,A.count";
        $query .= "\n                 ,A.pay_price";
        $query .= "\n                 ,A.order_common_seqno";
        $query .= "\n                 ,A.grade_sale_price";
        $query .= "\n                 ,A.event_price";
        $query .= "\n                 ,A.cp_price";
        $query .= "\n                 ,A.expec_weight";
        $query .= "\n                 ,A.dlvr_way";
        $query .= "\n                 ,A.receipt_dvs";
        $query .= "\n                 ,B.title";
        $query .= "\n                 ,B.dvs";
        $query .= "\n                 ,B.regi_date";
        $query .= "\n                 ,B.state";
        $query .= "\n                 ,B.occur_price";
        $query .= "\n                 ,B.cust_burden_price";
        $query .= "\n                 ,B.outsource_burden_price";
        $query .= "\n                 ,B.sample_file_path";
        $query .= "\n                 ,B.sample_origin_file_name";
        $query .= "\n                 ,B.sample_save_file_name";
        $query .= "\n                 ,B.cust_cont";
        $query .= "\n                 ,B.order_claim_seqno";
        $query .= "\n                 ,B.mng_cont";
        $query .= "\n                 ,B.empl_seqno";
        $query .= "\n            FROM  order_common A";
        $query .= "\n                 ,order_claim B";
        $query .= "\n           WHERE  A.order_common_seqno =";
        $query .= " B.order_common_seqno";
        $query .= "\n             AND  B.order_claim_seqno = ";
        $query .= $param["claim_seqno"];
        $query .= "\n             AND  A.member_seqno = ";
        $query .= $param["member_seqno"];
        $query .= "\n           ) T1";
        $query .= "\n      LEFT  OUTER JOIN empl C";
        $query .= "\n        ON  T1.empl_seqno = C.empl_seqno";
        $query .= "\n      LEFT  OUTER JOIN order_file D";
        $query .= "\n        ON  T1.order_common_seqno = D.order_common_seqno";

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
