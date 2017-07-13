<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MypageCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/OrderFavoriteHTML.php');

class OrderFavoriteDAO extends MypageCommonDAO {
 
    /**
     * @brief 관심상품 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectPrdtList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);

        if ($dvs === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n    SELECT  A.regi_date";
            $query .= "\n           ,A.order_detail";
            $query .= "\n           ,A.amt";
            $query .= "\n           ,A.count";
            $query .= "\n           ,A.expec_weight";
            $query .= "\n           ,A.interest_prdt_seqno";
            $query .= "\n           ,B.file_path";
            $query .= "\n           ,B.save_file_name";

        }
        $query .= "\n      FROM  interest_prdt A";
        $query .= "\n           ,cate_photo B";
        $query .= "\n     WHERE A.cate_sortcode = B.cate_sortcode";
        $query .= "\n       AND A.member_seqno = " . $param["seqno"];
        $query .= "\n       AND B.seq = '1'";

        //주문상세 검색
        if ($this->blankParameterCheck($param ,"order_detail")) {
            $detail = substr($param["order_detail"], 1, -1);
            $query .= "\n    AND  A.order_detail LIKE '%" . $detail . "%'";
        }

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {

            $from = substr($param["from"], 1, -1);

            $query .="\n     AND  A.regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {

            $to = substr($param["to"], 1, -1);

            $query .="\n     AND  A.regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($dvs == "SEQ") {

            $query .= "\n ORDER BY A.regi_date DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;

        }

        return $conn->Execute($query);
    }

    /**
     * @brief 관심상품에 해당하는 후공정 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectPrdtAfter($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  after_name";
        $query .= "\n           ,depth1";
        $query .= "\n           ,depth2";
        $query .= "\n           ,depth3";
        $query .= "\n      FROM  interest_prdt_after_history";
        $query .= "\n     WHERE  interest_prdt_seqno = ";
        $query .= $param["prdt_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 관심상품을 정보 SELECT
     *        위한 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectPrdt($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT";
        $query .= "\n            member_seqno";
        $query .= "\n           ,order_detail";
        $query .= "\n           ,mono_yn";
        $query .= "\n           ,req_cont";
        $query .= "\n           ,title";
        $query .= "\n           ,stan_name";
        $query .= "\n           ,amt";
        $query .= "\n           ,count";
        $query .= "\n           ,expec_weight";
        $query .= "\n           ,amt_unit_dvs";
        $query .= "\n           ,sum_way";
        $query .= "\n           ,memo";
        $query .= "\n           ,owncompany_img_use_yn";
        $query .= "\n           ,pay_way";
        $query .= "\n           ,cate_sortcode";
        $query .= "\n           ,after_use_yn";
        $query .= "\n           ,opt_use_yn";
        $query .= "\n           ,print_tmpt_name";
        $query .= "\n           ,cpn_admin_seqno";
        $query .= "\n      FROM  interest_prdt";
        $query .= "\n     WHERE  interest_prdt_seqno = ";
        $query .= $param["prdt_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /** 
     * @brief 관심상품을 장바구니에 INSERT
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"]["컬럼명"] = "데이터" (다중)<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */ 
    function insertShb($conn, $result) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $query  = "\n INSERT INTO order_common (";
        $query .= "\n             member_seqno";
        $query .= "\n            ,order_detail";
        $query .= "\n            ,mono_yn";
        $query .= "\n            ,req_cont";
        $query .= "\n            ,title";
        $query .= "\n            ,stan_name";
        $query .= "\n            ,amt";
        $query .= "\n            ,count";
        $query .= "\n            ,expec_weight";
        $query .= "\n            ,amt_unit_dvs";
        $query .= "\n            ,sum_way";
        $query .= "\n            ,memo";
        $query .= "\n            ,owncompany_img_use_yn";
        $query .= "\n            ,pay_way";
        $query .= "\n            ,cate_sortcode";
        $query .= "\n            ,after_use_yn";
        $query .= "\n            ,opt_use_yn";
        $query .= "\n            ,print_tmpt_name";
        $query .= "\n            ,cpn_admin_seqno";
        $query .= "\n            ,order_regi_date";
        $query .= "\n            ,order_state";
        $query .= "\n) VALUES (";
        $query .= "\n           '" . $result->fields["member_seqno"] . "'";
        $query .= "\n          ,'" . $result->fields["order_detail"] . "'";
        $query .= "\n          ,'" . $result->fields["mono_yn"] . "'";
        $query .= "\n          ,'" . $result->fields["req_cont"] . "'";
        $query .= "\n          ,'" . $result->fields["title"] . "'";
        $query .= "\n          ,'" . $result->fields["stan_name"] . "'";
        $query .= "\n          ,'" . $result->fields["amt"] . "'";
        $query .= "\n          ,'" . $result->fields["count"] . "'";
        $query .= "\n          ,'" . $result->fields["expec_weight"] . "'";
        $query .= "\n          ,'" . $result->fields["amt_unit_dvs"] . "'";
        $query .= "\n          ,'" . $result->fields["sum_way"] . "'";
        $query .= "\n          ,'" . $result->fields["memo"] . "'";
        $query .= "\n          ,'" . $result->fields["owncompany_img_use_yn"];
        $query .= "'";
        $query .= "\n          ,'" . $result->fields["pay_way"] . "'";
        $query .= "\n          ,'" . $result->fields["cate_sortcode"] . "'";
        $query .= "\n          ,'" . $result->fields["after_use_yn"] . "'";
        $query .= "\n          ,'" . $result->fields["opt_use_yn"] . "'";
        $query .= "\n          ,'" . $result->fields["print_tmpt_name"] . "'";
        $query .= "\n          ,'" . $result->fields["cpn_admin_seqno"] . "'";
        $query .= "\n          ,'" . date("Y-m-d H:i:s", time()) . "'";
        $query .= "\n          ,'110'";
        $query .= "\n )";

        $resultSet = $conn->Execute($query);

        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 관심상품 후공정 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectPrdtAfterSet($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  basic_yn";
        $query .= "\n           ,depth1";
        $query .= "\n           ,depth2";
        $query .= "\n           ,depth3";
        $query .= "\n           ,price";
        $query .= "\n           ,after_name";
        $query .= "\n           ,seq";
        $query .= "\n           ,detail";
        $query .= "\n      FROM  interest_prdt_after_history";
        $query .= "\n     WHERE  interest_prdt_seqno = ";
        $query .= $param["prdt_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 장바구니에 관심상품 옵션 정보 INSERT
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertShbAfter($conn, $result, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $check = TRUE;

        while ($result && !$result->EOF) {

            $query  = "\n INSERT INTO ";
            $query .= "\n order_after_history (";
            $query .= "\n                       basic_yn";
            $query .= "\n                      ,depth1";
            $query .= "\n                      ,depth2";
            $query .= "\n                      ,depth3";
            $query .= "\n                      ,price";
            $query .= "\n                      ,after_name";
            $query .= "\n                      ,seq";
            $query .= "\n                      ,detail";
            $query .= "\n                      ,order_common_seqno";
            $query .= "\n) VALUES (";
            $query .= "\n            '" . $result->fields["basic_yn"] . "'";
            $query .= "\n           ,'" . $result->fields["depth1"] . "'";
            $query .= "\n           ,'" . $result->fields["depth2"] . "'";
            $query .= "\n           ,'" . $result->fields["depth3"] . "'";
            $query .= "\n           ,'" . $result->fields["price"] . "'";
            $query .= "\n           ,'" . $result->fields["after_name"] . "'";
            $query .= "\n           ,'" . $result->fields["seq"] . "'";
            $query .= "\n           ,'" . $result->fields["detail"] . "'";
            $query .= "\n           , " . $param["shb_seqno"];
            $query .= "\n )";


            $resultSet = $conn->Execute($query);
            if ($resultSet === FALSE) {
                $check = FALSE;
            }

            $result->moveNext();
        }

        return $check;

    }
 
    /**
     * @brief 관심상품 옵션 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectPrdtOptSet($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  basic_yn";
        $query .= "\n           ,depth1";
        $query .= "\n           ,depth2";
        $query .= "\n           ,depth3";
        $query .= "\n           ,price";
        $query .= "\n           ,opt_name";
        $query .= "\n      FROM  interest_prdt_opt_history";
        $query .= "\n     WHERE  interest_prdt_seqno = ";
        $query .= $param["prdt_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 장바구니에 관심상품 옵션 INSERT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertShbOpt($conn, $result, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $check = TRUE;

        while ($result && !$result->EOF) {

            $query  = "\n INSERT INTO ";
            $query .= "\n order_opt_history (";
            $query .= "\n                       basic_yn";
            $query .= "\n                      ,depth1";
            $query .= "\n                      ,depth2";
            $query .= "\n                      ,depth3";
            $query .= "\n                      ,price";
            $query .= "\n                      ,opt_name";
            $query .= "\n                      ,order_common_seqno";
            $query .= "\n) VALUES (";
            $query .= "\n            '" . $result->fields["basic_yn"] . "'";
            $query .= "\n           ,'" . $result->fields["depth1"] . "'";
            $query .= "\n           ,'" . $result->fields["depth2"] . "'";
            $query .= "\n           ,'" . $result->fields["depth3"] . "'";
            $query .= "\n           ,'" . $result->fields["price"] . "'";
            $query .= "\n           ,'" . $result->fields["opt_name"] . "'";
            $query .= "\n           , " . $param["shb_seqno"];
            $query .= "\n )";

            $resultSet = $conn->Execute($query);
            if ($resultSet === FALSE) {
                $check = FALSE;
            }

            $result->moveNext();
        }

        return $check;

    }
 
    /**
     * @brief 관심상품 주문상세 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectPrdtDetailSet($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  cate_print_mpcode";
        $query .= "\n           ,cate_paper_mpcode";
        $query .= "\n           ,typ";
        $query .= "\n           ,page_amt";
        $query .= "\n           ,spc_dscr";
        $query .= "\n           ,detail_num";
        $query .= "\n           ,order_detail_num";
        $query .= "\n           ,work_size_wid";
        $query .= "\n           ,work_size_vert";
        $query .= "\n           ,cut_size_wid";
        $query .= "\n           ,cut_size_vert";
        $query .= "\n           ,tomson_size_wid";
        $query .= "\n           ,tomson_size_vert";
        $query .= "\n      FROM  interest_prdt_detail";
        $query .= "\n     WHERE  interest_prdt_seqno = ";
        $query .= $param["prdt_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 거 주문상세 INSERT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertShbDetail($conn, $result, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $check = TRUE;

        while ($result && !$result->EOF) {

            $query  = "\n INSERT INTO order_detail (";
            $query .= "\n                          cate_print_mpcode";
            $query .= "\n                         ,typ";
            $query .= "\n                         ,page_amt";
            $query .= "\n                         ,cate_paper_mpcode";
            $query .= "\n                         ,spc_dscr";
            $query .= "\n                         ,detail_num";
            $query .= "\n                         ,work_size_wid";
            $query .= "\n                         ,work_size_vert";
            $query .= "\n                         ,cut_size_wid";
            $query .= "\n                         ,cut_size_vert";
            $query .= "\n                         ,tomson_size_wid";
            $query .= "\n                         ,tomson_size_vert";
            $query .= "\n                         ,order_common_seqno";
            $query .= "\n) VALUES (";
            $query .= "\n          '" . $result->fields["cate_print_mpcode"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["typ"] . "'";
            $query .= "\n         ,'" . $result->fields["page_amt"] . "'";
            $query .= "\n         ,'" . $result->fields["cate_paper_mpcode"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["spc_dscr"] . "'";
            $query .= "\n         ,'" . $result->fields["detail_num"] . "'";
            $query .= "\n         ,'" . $result->fields["work_size_wid"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["work_size_vert"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["cut_size_wid"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["cut_size_vert"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["tomson_size_wid"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["tomson_size_vert"];
            $query .= "'";
            $query .= "\n         , " . $param["shb_seqno"];
            $query .= "\n )";

            $resultSet = $conn->Execute($query);
            if ($resultSet === FALSE) {
                $check = FALSE;
            }

            $result->moveNext();
        }

        return $check;

    }



}

?>
