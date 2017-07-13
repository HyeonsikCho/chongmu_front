<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/ProductCommonDAO.php');

class ProductAdBookDAO extends ProductCommonDAO {
    function __construct() {
    }

    /**
     * @brief 책자 제본 html 생성
     *
     * @param $conn  = connection identifier
     * @param $dvs   = 가져올 정보 구분값
     * @param $param = 검색조건 파라미터
     * @param &$price_info_arr = 가격검색용 정보저장 배열
     *
     * @return option html
     */
    function selectBindingHtml($conn, $dvs, $param, &$price_info_arr) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT ";
        if ($dvs === "depth1") {
            $query .= "DISTINCT A.depth1";
        } else if ($dvs === "depth2") {
            $query .= " A.depth2";
            $query .= ",B.mpcode";
        }

        $query .= "\n   FROM  prdt_after AS A";
        $query .= "\n        ,cate_after AS B";

        $query .= "\n  WHERE  A.prdt_after_seqno = B.prdt_after_seqno";
        $query .= "\n    AND  A.after_name    = '제본'";
        $query .= "\n    AND  B.cate_sortcode = %s";
        if ($this->blankParameterCheck($param, "depth1")) {
            $query .= "\n    AND  A.depth1 = " . $param["depth1"];
        }

        $query  = sprintf($query, $param["cate_sortcode"]);

        $rs = $conn->Execute($query);

        $arr = array(
            "val" => "mpcode",
            "dvs" => $dvs
        );

        $price_info_arr["binding_" . $dvs] = $rs->fields[$dvs];

        return makeOptionHtml($rs, $arr);
    }

    /**
     * @brief 제본 가격 검색
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 종이 기준단위
     */
    function selectBindingPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    SELECT A.sell_price";
        $query .= "\n      FROM cate_after_price AS A";
        $query .= "\n     WHERE A.cate_after_mpcode = %s";
        $query .= "\n       AND A.cpn_admin_seqno   = %s";
        $query .= "\n       AND (%s + 0) <= A.amt";
        $query .= "\n  ORDER BY amt ASC";
        $query .= "\n     LIMIT 1";

        $query  = sprintf($query, $param["mpcode"]
                                , $param["sell_site"]
                                , $param["amt"]);

        $rs = $conn->Execute($query);

        // 해당하는 수량이 없을경우 제일 마지막 수량 판매가격 반환
        if ($rs->EOF) {
            $query  = "\n    SELECT A.sell_price";
            $query .= "\n      FROM cate_after_price AS A";
            $query .= "\n     WHERE A.cate_after_mpcode = %s";
            $query .= "\n       AND A.cpn_admin_seqno   = %s";
            $query .= "\n  ORDER BY amt DESC";
            $query .= "\n     LIMIT 1";

            $query  = sprintf($query, $param["mpcode"]
                                    , $param["sell_site"]);

            $rs = $conn->Execute($query);
        }

        return $rs->fields["sell_price"];
    }
}
?>
