<?
/********************************************************************
***** 프로 젝트 : 총무팀
***** 개  발  자 : 김성진
***** 수  정  일 : 2016.05.12
********************************************************************/

/********************************************************************
***** 라이브러리 인클루드
********************************************************************/

include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MypageCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/OrderAllHTML.php');

class OrderAllDAO extends MypageCommonDAO {

    /**
     * @brief 주문공통 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */

	 /********************************************************************
	***** 주문 데이터 가져오기
	********************************************************************/

	 function getOrderList($conn,$param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

		$param = $this->parameterArrayEscape($conn, $param);
		$dvs = substr($param["dvs"], 1, -1);

        if ($dvs == "COUNT") {
            $sql  = "SELECT count(*) AS cnt FROM (SELECT a.order_no ";
		} else {
			$sql = "SELECT  order_etime, MIN(b.title) as title, MAX(order_status), prd_status, SUM(IF(b.cate_sortcode = '003001001', 0, 1)) AS o_cnt, ";
			$sql .= "a.order_no, b.prd_detail_no, MAX(b.title) AS title, MAX(e.cate_name) AS cate_name, ";
			$sql .= "GROUP_CONCAT(DISTINCT CONCAT(i.origin_file_name, '_', i.dvs) ORDER BY i.dvs ASC SEPARATOR '/') AS img_name, ";
			$sql .= "GROUP_CONCAT(DISTINCT i.dvs ORDER BY i.dvs ASC SEPARATOR '/') AS img_dvs, ";
			$sql .= "MAX(CONCAT(f.name, ' ', f.color, ' ', f.basisweight)) AS paper_name, ";
			$sql .= "MAX(h.name) AS print_name, MAX(b.prd_amount) AS prd_amount, MAX(b.prd_count) AS prd_count, ";
			$sql .= "GROUP_CONCAT(DISTINCT c.opt_name ORDER BY c.seq ASC SEPARATOR '/') AS opt_name, ";
			$sql .= "GROUP_CONCAT(DISTINCT d.after_name ORDER BY d.seq ASC SEPARATOR '/') AS after_name, ";
			$sql .= "SUM(DISTINCT d.amnt) AS after_amnt, SUM(DISTINCT c.amnt) AS opt_amnt, ";
			$sql .= "b.prd_amnt AS prd_amnt, (b.prd_amnt + SUM(c.amnt) + SUM(d.amnt)) AS tot_amnt, ";
			$sql .= "(b.prd_amnt + SUM(c.amnt) + SUM(d.amnt)) * MAX(e.c_rate * 0.01) AS c_amnt, ";
			$sql .= "MAX(e.c_rate), MAX(i.file_path) AS file_path, MAX(i.save_file_name) AS save_file_name ";
		}

		$sql .= "FROM order_master AS a ";
		$sql .= "INNER JOIN order_prdlist AS b on (a.order_no = b.order_no) ";
		$sql .= "LEFT JOIN order_opt_history AS c on (b.order_no = c.order_no AND b.prd_detail_no = c.prd_detail_no) ";
		$sql .= "LEFT JOIN order_after_history AS d on (b.order_no = d.order_no AND b.prd_detail_no = d.prd_detail_no) ";
		$sql .= "LEFT JOIN order_img AS i on (b.order_no = i.order_no AND b.prd_detail_no = i.prd_detail_no) ";
		$sql .= "INNER JOIN cate AS e on (b.cate_sortcode = e.sortcode) ";
		$sql .= "INNER JOIN cate_paper AS f on (b.cate_sortcode = f.cate_sortcode AND b.cate_paper_mpcode = f.mpcode)  ";
		$sql .= "INNER JOIN cate_print AS g on (b.cate_sortcode = g.cate_sortcode AND b.cate_print_mpcode = g.mpcode) ";
		$sql .= "INNER JOIN prdt_print AS h on (g.prdt_print_seqno = h.prdt_print_seqno) ";
		$sql .= "WHERE user_id = ".$param['user_id']." ";

		if ($this->blankParameterCheck($param ,"title")) {
			$title = substr($param["title"], 1, -1);
			$sql .= "AND  b.title LIKE '%".$title."%' ";
		}

		if ($this->blankParameterCheck($param ,"state")) {
			$sql .= "AND  b.prd_status = '".$param['state']."' ";
		}

		if ($this->blankParameterCheck($param ,"from")) {
			$from = substr($param["from"], 1, -1);
			$sql .= "AND a.order_etime >= '" .$from;
			$sql .= " 00:00:00' ";
		}

		if ($this->blankParameterCheck($param ,"to")) {
			$to = substr($param["to"], 1, -1);
			$sql .="AND a.order_etime <= '" . $to;
			$sql .=" 23:59:59' ";
		}

		$sql .= "GROUP BY b.order_no, b.prd_detail_no ORDER BY a.order_no DESC ";

		if (!$dvs) {
			$param["s_num"] = str_replace('\'','',$param["s_num"]);
			$param["list_num"] = str_replace('\'','',$param["list_num"]);

			$sql .= "LIMIT ". $param["s_num"]. ", " . $param["list_num"];
		}

		if ($dvs == "COUNT") {
			$sql .= " ) AS cnt ";
		}

		return $conn->Execute($sql);
	 }


    function selectOrderList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);

        if ($dvs === "COUNT") {
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
            $query .= "\n           ,C.file_path";
            $query .= "\n           ,C.save_file_name";
        }
        $query .= "\n      FROM  order_common A";
        $query .= "\n      LEFT  OUTER JOIN order_file C";
        $query .= "\n        ON  A.order_common_seqno = C.order_common_seqno";
        $query .= "\n     WHERE  A.member_seqno = " . $param["seqno"];

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

        //상태
        if ($this->blankParameterCheck($param ,"state")) {

            $state = substr($param["state"], 1, -1);
            $state_dvs = substr($param["state_dvs"], 1, -1);

            if ($state_dvs == "N") {

                $query .= "\n    AND  A.order_state = '" . $state . "'";

            } else if ($state_dvs == "P") {

                $query .= "\n    AND  A.order_state >= '" . $state . "'";

            } else {

                $query .= "\n    AND  A.order_state >= " . $state;
                $query .= "\n    AND  A.order_state < " . ($state + 100);

            }

        }

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

        if ($dvs == "SEQ") {

            $query .= "\n ORDER BY order_common_seqno DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;

        }

        return $conn->Execute($query);
    }

    /**
     * @brief 주문취소시 UPDATE
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateOrderState($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $query  = "\n    UPDATE  order_common";
        $query .= "\n       SET  order_state = 120";
        $query .= "\n           ,del_yn = 'Y'";
        $query .= "\n           ,eraser = " . $param["member_name"];
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .=  $param["order_seqno"];

        $result = $conn->Execute($query);

        if ($result === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 회원선입금 UPDATE(주문취소/ 재주문)
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateMemberPrepay($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);

        $query  = "\n    UPDATE  member";

        if ($type == "lack") {

            $query .= "\n       SET  prepay_price = 0";
            $query .= "\n           ,order_lack_price = ";
            $query .= $param["price"];

        } else {

            $query .= "\n       SET  prepay_price = " . $param["price"];

        }

        $query .= "\n     WHERE  member_seqno = " . $param["member_seqno"];

        $result = $conn->Execute($query);

        if ($result === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 회원 이름 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectMemberName($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $query .= "\n    SELECT  member_name";
        $query .= "\n      FROM  member";
        $query .= "\n     WHERE  member_seqno = " . $param["member_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 회원 선입금 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectMemberPrepay($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $query .= "\n    SELECT  prepay_price";
        $query .= "\n           ,member_name";
        $query .= "\n      FROM  member";
        $query .= "\n     WHERE  member_seqno = " . $param["member_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }


    /**
     * @brief 주문에 해당하는 후공정 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderAfter($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  after_name";
        $query .= "\n           ,depth1";
        $query .= "\n           ,depth2";
        $query .= "\n           ,depth3";
        $query .= "\n      FROM  order_after_history";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 주문취소를 위한 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  pay_price";
        $query .= "\n           ,member_seqno";
        $query .= "\n           ,order_state";
        $query .= "\n      FROM  order_common";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 재주문을 위한 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderRow($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT";
        $query .= "\n            order_num";
        $query .= "\n           ,order_state";
        $query .= "\n           ,oper_sys";
        $query .= "\n           ,pro";
        $query .= "\n           ,pro_ver";
        $query .= "\n           ,req_cont";
        $query .= "\n           ,basic_price";
        $query .= "\n           ,grade_sale_price";
        $query .= "\n           ,event_price";
        $query .= "\n           ,use_point_price";
        $query .= "\n           ,sell_price";
        $query .= "\n           ,cp_price";
        $query .= "\n           ,pay_price";
        $query .= "\n           ,order_regi_date";
        $query .= "\n           ,stan_name";
        $query .= "\n           ,amt";
        $query .= "\n           ,amt_unit_dvs";
        $query .= "\n           ,sum_way";
        $query .= "\n           ,member_seqno";
        $query .= "\n           ,mono_yn";
        $query .= "\n           ,claim_yn";
        $query .= "\n           ,order_detail";
        $query .= "\n           ,title";
        $query .= "\n           ,expec_weight";
        $query .= "\n           ,count";
        $query .= "\n           ,bun_group";
        $query .= "\n           ,receipt_regi_date";
        $query .= "\n           ,memo";
        $query .= "\n           ,cpn_admin_seqno";
        $query .= "\n           ,del_yn";
        $query .= "\n           ,point_use_yn";
        $query .= "\n           ,owncompany_img_use_yn";
        $query .= "\n           ,pay_way";
        $query .= "\n           ,cate_sortcode";
        $query .= "\n           ,after_use_yn";
        $query .= "\n           ,opt_use_yn";
        $query .= "\n           ,print_tmpt_name";
        $query .= "\n           ,prdt_basic_info";
        $query .= "\n           ,prdt_add_info";
        $query .= "\n           ,prdt_price_info";
        $query .= "\n           ,bun_yn";
        $query .= "\n           ,prdt_pay_info";
        $query .= "\n           ,dlvr_way";
        $query .= "\n           ,dlvr_pay_way";
        $query .= "\n           ,dlvr_price";
        $query .= "\n           ,add_after_price";
        $query .= "\n           ,add_opt_price";
        $query .= "\n           ,expenevid_req_yn";
        $query .= "\n           ,expenevid_dvs";
        $query .= "\n           ,expenevid_num";
        $query .= "\n           ,event_yn";
        $query .= "\n      FROM  order_common";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }


    /**
     * @brief 데이터 삽입 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"]["컬럼명"] = "데이터" (다중)<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */
    function insertReorder($conn, $result, $param) {

        $param = $this->parameterArrayEscape($conn, $param);

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $query  = "\n INSERT INTO order_common (";
        $query .= "\n                          order_num";
        $query .= "\n                         ,order_state";
        $query .= "\n                         ,oper_sys";
        $query .= "\n                         ,pro";
        $query .= "\n                         ,pro_ver";
        $query .= "\n                         ,req_cont";
        $query .= "\n                         ,basic_price";
        $query .= "\n                         ,grade_sale_price";
        $query .= "\n                         ,event_price";
        $query .= "\n                         ,use_point_price";
        $query .= "\n                         ,sell_price";
        $query .= "\n                         ,cp_price";
        $query .= "\n                         ,pay_price";
        $query .= "\n                         ,order_regi_date";
        $query .= "\n                         ,stan_name";
        $query .= "\n                         ,amt";
        $query .= "\n                         ,amt_unit_dvs";
        $query .= "\n                         ,sum_way";
        $query .= "\n                         ,member_seqno";
        $query .= "\n                         ,mono_yn";
        $query .= "\n                         ,claim_yn";
        $query .= "\n                         ,order_detail";
        $query .= "\n                         ,title";
        $query .= "\n                         ,expec_weight";
        $query .= "\n                         ,count";
        $query .= "\n                         ,bun_group";
        $query .= "\n                         ,memo";
        $query .= "\n                         ,cpn_admin_seqno";
        $query .= "\n                         ,del_yn";
        $query .= "\n                         ,point_use_yn";
        $query .= "\n                         ,owncompany_img_use_yn";
        $query .= "\n                         ,pay_way";
        $query .= "\n                         ,cate_sortcode";
        $query .= "\n                         ,after_use_yn";
        $query .= "\n                         ,opt_use_yn";
        $query .= "\n                         ,print_tmpt_name";
        $query .= "\n                         ,prdt_basic_info";
        $query .= "\n                         ,prdt_add_info";
        $query .= "\n                         ,prdt_price_info";
        $query .= "\n                         ,bun_yn";
        $query .= "\n                         ,prdt_pay_info";
        $query .= "\n                         ,dlvr_way";
        $query .= "\n                         ,dlvr_pay_way";
        $query .= "\n                         ,dlvr_price";
        $query .= "\n                         ,add_after_price";
        $query .= "\n                         ,add_opt_price";
        $query .= "\n                         ,expenevid_req_yn";
        $query .= "\n                         ,expenevid_dvs";
        $query .= "\n                         ,expenevid_num";
        $query .= "\n                         ,event_yn";
        $query .= "\n) VALUES (";
        $query .= "\n           '" . $result->fields["order_num"] . "'";
        $query .= "\n          ," . $param["state"];
        $query .= "\n          ,'" . $result->fields["oper_sys"] . "'";
        $query .= "\n          ,'" . $result->fields["pro"] . "'";
        $query .= "\n          ,'" . $result->fields["pro_ver"] . "'";
        $query .= "\n          ,'" . $result->fields["req_cont"] . "'";
        $query .= "\n          ,'" . $result->fields["basic_price"] . "'";
        $query .= "\n          ,'0'";
        $query .= "\n          ,'0'";
        $query .= "\n          ,'0'";
        $query .= "\n          ,'" . $result->fields["sell_price"] . "'";
        $query .= "\n          ,'0'";
        $query .= "\n          ," . $param["pay_price"];
        $query .= "\n          ,'" . date("Y-m-d H:i:s", time()) . "'";
        $query .= "\n          ,'" . $result->fields["stan_name"] . "'";
        $query .= "\n          ,'" . $result->fields["amt"] . "'";
        $query .= "\n          ,'" . $result->fields["amt_unit_dvs"] . "'";
        $query .= "\n          ,'" . $result->fields["sum_way"] . "'";
        $query .= "\n          ,'" . $result->fields["member_seqno"] . "'";
        $query .= "\n          ,'" . $result->fields["mono_yn"] . "'";
        $query .= "\n          ,'N'";
        $query .= "\n          ,'" . $result->fields["order_detail"] . "'";
        $query .= "\n          ,'" . $result->fields["title"] . "'";
        $query .= "\n          ,'" . $result->fields["expec_weight"] . "'";
        $query .= "\n          ,'" . $result->fields["count"] . "'";
        $query .= "\n          ,'" . $result->fields["bun_group"] . "'";
        $query .= "\n          ,'" . $result->fields["memo"] . "'";
        $query .= "\n          ,'" . $result->fields["cpn_admin_seqno"] . "'";
        $query .= "\n          ,'N'";
        $query .= "\n          ,'N'";
        $query .= "\n          ,'" . $result->fields["owncompany_img_use_yn"];
        $query .= "'";
        $query .= "\n          ,'" . $result->fields["pay_way"] . "'";
        $query .= "\n          ,'" . $result->fields["cate_sortcode"] . "'";
        $query .= "\n          ,'" . $result->fields["after_use_yn"] . "'";
        $query .= "\n          ,'" . $result->fields["opt_use_yn"] . "'";
        $query .= "\n          ,'" . $result->fields["print_tmpt_name"] . "'";
        $query .= "\n          ,'" . $result->fields["prdt_basic_info"] . "'";
        $query .= "\n          ,'" . $result->fields["prdt_add_info"] . "'";
        $query .= "\n          ,'" . $result->fields["prdt_price_info"] . "'";
        $query .= "\n          ,'" . $result->fields["bun_yn"] . "'";
        $query .= "\n          ,'" . $result->fields["prdt_pay_info"] . "'";
        $query .= "\n          ,'" . $result->fields["dlvr_way"] . "'";
        $query .= "\n          ,'" . $result->fields["dlvr_pay_way"] . "'";
        $query .= "\n          ,'" . $result->fields["dlvr_price"] . "'";
        $query .= "\n          ,'" . $result->fields["add_after_price"] . "'";
        $query .= "\n          ,'" . $result->fields["add_opt_price"] . "'";
        $query .= "\n          ,'N'";
        $query .= "\n          ,'" . $result->fields["expenevid_dvs"] . "'";
        $query .= "\n          ,'" . $result->fields["expenevid_num"] . "'";
        $query .= "\n          ,'N'";
        $query .= "\n )";

        $resultSet = $conn->Execute($query);

        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 주문파일을 위한 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderFileSet($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  dvs";
        $query .= "\n           ,file_path";
        $query .= "\n           ,save_file_name";
        $query .= "\n           ,origin_file_name";
        $query .= "\n           ,size";
        $query .= "\n      FROM  order_file";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 재주문 주문파일을 INSERT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertOrderFile($conn, $result, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $check = TRUE;

        while ($result && !$result->EOF) {

            $query  = "\n INSERT INTO order_file (";
            $query .= "\n                          dvs";
            $query .= "\n                         ,file_path";
            $query .= "\n                         ,save_file_name";
            $query .= "\n                         ,origin_file_name";
            $query .= "\n                         ,size";
            $query .= "\n                         ,order_common_seqno";
            $query .= "\n) VALUES (";
            $query .= "\n            '" . $result->fields["dvs"] . "'";
            $query .= "\n           ,'" . $result->fields["file_path"] . "'";
            $query .= "\n           ,'" . $result->fields["save_file_name"];
            $query .= "'";
            $query .= "\n           ,'" . $result->fields["origin_file_name"];
            $query .= "'";
            $query .= "\n           ,'" . $result->fields["size"] . "'";
            $query .= "\n           , " . $param["reorder_seqno"];
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
     * @brief 주문상세를 위한 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderDetailSet($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  cate_print_mpcode";
        $query .= "\n           ,typ";
        $query .= "\n           ,page_amt";
        $query .= "\n           ,cate_paper_mpcode";
        $query .= "\n           ,spc_dscr";
        $query .= "\n           ,detail_num";
        $query .= "\n           ,work_size_wid";
        $query .= "\n           ,work_size_vert";
        $query .= "\n           ,cut_size_wid";
        $query .= "\n           ,cut_size_vert";
        $query .= "\n           ,tomson_size_wid";
        $query .= "\n           ,tomson_size_vert";
        $query .= "\n      FROM  order_detail";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 재주문 주문상세 INSERT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertOrderDetail($conn, $result, $param) {
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
            $query .= "\n'";
            $query .= "\n         ,'" . $result->fields["typ"] . "'";
            $query .= "\n         ,'" . $result->fields["page_amt"] . "'";
            $query .= "\n         ,'" . $result->fields["cate_paper_mpcode"];
            $query .= "\n'";
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
            $query .= "\n         , " . $param["reorder_seqno"];
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
     * @brief 재주문을 위한 후공정 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderAfterSet($conn, $param) {
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
        $query .= "\n      FROM  order_after_history";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 재주문 후공정 INSERT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertOrderAfter($conn, $result, $param) {
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
            $query .= "\n           , " . $param["reorder_seqno"];
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
     * @brief 재주문을 위한 옵션 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderOptSet($conn, $param) {
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
        $query .= "\n      FROM  order_opt_history";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 재주문 옵션 INSERT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertOrderOpt($conn, $result, $param) {
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
            $query .= "\n           , " . $param["reorder_seqno"];
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
     * @brief 주문 메모 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderMemo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  memo";
        $query .= "\n      FROM  order_common";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 주문메모 UPDATE
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateOrderMemo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $query  = "\n    UPDATE  order_common";
        $query .= "\n       SET  memo = " . $param["memo"];
        $query .= "\n     WHERE  order_common_seqno = " . $param["order_seqno"];

        $result = $conn->Execute($query);

        if ($result === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 회원 등급 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectMemberInfo($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  grade";
        $query .= "\n           ,prepay_price";
        $query .= "\n           ,order_lack_price";
        $query .= "\n           ,member_typ";
        $query .= "\n      FROM  member";
        $query .= "\n     WHERE  member_seqno = ";
        $query .= $param["member_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 등급별 할인율 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectGradeRate($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  sales_sale_rate";
        $query .= "\n      FROM  member_grade_policy";
        $query .= "\n     WHERE  grade = ";
        $query .= $param["grade"];

        $result = $conn->Execute($query);

        return $result;

    }


    /**
     * @brief 시안보기 select
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectDraft($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);


        $query = "SELECT  order_no,prd_detail_no,file_path,save_file_name
					FROM  order_img
					WHERE  order_no=%s
					and prd_detail_no=%s
					and dvs='3'
					and type='admin'";
        $query = sprintf($query,$param['order_no'],$param['prd_detail_no']);

        $result = $conn->Execute($query);

        return makeDraftHTML($result);

    }


	function updateDraft($conn,$param){
        if ($this->connectionCheck($conn) === false) {
            return false;
        }




			$upsql = "update order_prdlist set prd_status='390'
						where order_no='".$param['order_no']."'
						and prd_detail_no='".$param['prd_detail_no']."'";


			$upsql2 = "update order_img set draft_confirm='".$param['draft_chk']."'";

			if($param['draft_comment']){
				$upsql2 .=",draft_comment='".$param['draft_comment']."'";
			}
			$upsql2 .="where order_no='".$param['order_no']."'
						and prd_detail_no='".$param['prd_detail_no']."'";
			$rs = array();

			$conn->StartTrans();
			if($param['draft_chk'] == '1'){
				$rs[] = $conn->Execute($upsql);
			}
			$rs[] = $conn->Execute($upsql2);
			$conn->CompleteTrans();
			if(in_array(false,$rs)){
				return false;
			}else{
				return true;
			}


	}

}
?>
