<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/common/MakeCommonHtml.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/CommonDAO.php');

/*! 공통 DAO Class */
class ProductDAO extends CommonDAO {

    function __construct() {
    }

    /////////////////종이
/**
     * @brief 상품 확정형 가격 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 중분류코드
     * $param["paper_mpcode"] = 카테고리 종이 맵핑코드
     * $param["print_mpcode"] = 카테고리 인쇄 맵핑코드
     * $param["stan_mpcode"] = 카테고리 규격 맵핑코드
     * $param["amt"] = 수량
     * $param["table_name"] = 가격 테이블명
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 신규가격
     */

function selectPrdtPlyPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $query  = "\n SELECT  new_price";
        $query .= "\n   FROM  %s";

        $query .= "\n  WHERE  cate_sortcode                    = %s";
        $query .= "\n    AND  cate_paper_mpcode                = %s";
        $query .= "\n    AND  cate_beforeside_print_mpcode     = %s";
        $query .= "\n    AND  cate_stan_mpcode                 = %s";
        $query .= "\n    AND  amt                             >= %s + 0";
        $query .= "\n    AND  new_price                       != 0";
        $query .= "\n  LIMIT  1";


        $query  = sprintf($query, $param["table_name"]
        , $param["cate_sortcode"]
        , $param["paper_mpcode"]
        , $param["print_mpcode"]
        , $param["stan_mpcode"]
        , $param["amt"]);

        $rs = $conn->Execute($query);
        return $rs->fields["new_price"];
    }

    function selectPricePerPaper($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $query  = "\n SELECT  *";
        $query .= "\n   FROM  after_tomson_price_per";
        $query .= "\n  WHERE amt                             >= %s + 0 ";


        $query  = sprintf($query
            , $param["amt"]);

        $rs = $conn->Execute($query);
        return $rs;
    }

    function selectTomsonStickerPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $query  = "\n SELECT %s as price, basic_price ";
        $query .= "\n   FROM  after_tomson_price";
        $query .= "\n  WHERE  size_start <= %s + 0";
        $query .= "\n    AND  size_end >= %s + 0";

        $query  = sprintf($query
            , $param["f_name"]
            , $param["check_area"]
            , $param["check_area"]);

        $rs = $conn->Execute($query);
        return $rs->fields["price"] + $rs->fields["basic_price"];
    }


/**
 * @brief 카테고리 후공정 맵핑코드/기준수량 검색
 *
 * @detail $param["cate_sortcode"] = 카테고리 분류코드
 * $param["after_name"] = 후공정명
 * $param["depth1"] = 후공정 depth1
 * $param["depth2"] = 후공정 depth2
 * $param["depth3"] = 후공정 depth3
 *
 * @param $conn  = connection identifier
 * @param $param = 검색조건 파라미터
 *
 * @return 검색결과
 */
function selectCateAfterInfo($conn, $param)
{
    if ($this->connectionCheck($conn) === false) {
        return false;
    }

    $param = $this->parameterArrayEscape($conn, $param);

    $query = "\n SELECT  B.mpcode";
    $query .= "\n        ,A.crtr_unit";
    $query .= "\n   FROM  prdt_after AS A";
    $query .= "\n        ,cate_after AS B";
    $query .= "\n  WHERE  A.prdt_after_seqno = B.prdt_after_seqno";
    $query .= "\n    AND  A.after_name = %s";
    $query .= "\n    AND  B.cate_sortcode = %s";
    if ($this->blankParameterCheck($param, "depth1")) {
        $query .= "\n    AND  A.depth1 = " . $param["depth1"];
    }
    if ($this->blankParameterCheck($param, "depth2")) {
        $query .= "\n    AND  A.depth2 = " . $param["depth2"];
    }
    if ($this->blankParameterCheck($param, "depth3")) {
        $query .= "\n    AND  A.depth3 = " . $param["depth3"];
    }

    $query = sprintf($query, $param["after_name"]
        , $param["cate_sortcode"]);

    return $conn->Execute($query);
}


function selectCatePrintMpcode($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  B.mpcode";

        $query .= "\n   FROM  prdt_print AS A";
        $query .= "\n        ,cate_print AS B";

        $query .= "\n  WHERE  A.prdt_print_seqno = B.prdt_print_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";
        $query .= "\n    AND  A.name = %s";
    /*
        if ($this->blankParameterCheck($param, "purp")) {
            $query .= "\n AND  A.purp_dvs = " . $param["purp"];
        }*/
        if ($this->blankParameterCheck($param, "side_dvs")) {
            $query .= "\n AND  A.side_dvs = " . $param["side_dvs"];
        }

        $query  = sprintf($query, $param["cate_sortcode"]
                                , $param["tmpt"]);

        return $conn->Execute($query);
    }


//////////////////////////옵션
/**
     * @brief 카테고리 옵션 맵핑코드/기준수량 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 분류코드
     * $param["opt_name"] = 후공정명
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCateOptInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  B.mpcode";
        $query .= "\n   FROM  prdt_opt AS A";
        $query .= "\n        ,cate_opt AS B";
        $query .= "\n  WHERE  A.prdt_opt_seqno = B.prdt_opt_seqno";
        $query .= "\n    AND  A.opt_name = %s";
        $query .= "\n    AND  B.cate_sortcode = %s";

        $query  = sprintf($query, $param["name"]
                                , $param["cate_sortcode"]);

        return $conn->Execute($query);
    }

/**
     * @brief 옵션 가격 검색
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCateOptPriceList($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  A.cate_opt_price_seqno AS price_seqno";
        $query .= "\n        ,A.amt";
        $query .= "\n        ,A.basic_price";
        $query .= "\n        ,A.sell_rate";
        $query .= "\n        ,A.sell_aplc_price";
        $query .= "\n        ,A.sell_price ";

        $query .= "\n   FROM  cate_opt_price AS A";

        $query .= "\n  WHERE  1 = 1";

        if ($this->blankParameterCheck($param, "opt_mpcode")) {
            $query .= "\n    AND  A.cate_opt_mpcode = ";
            $query .= $param["opt_mpcode"];
        }
        if ($this->blankParameterCheck($param, "sell_site")) {
            $query .= "\n    AND  A.cpn_admin_seqno   = ";
            $query .= $param["sell_site"];
        }
        if ($this->blankParameterCheck($param, "price_seqno")) {
            $query .= "\n    AND  A.cate_opt_price_seqno = ";
            $query .= $param["price_seqno"];
        }
		if ($this->blankParameterCheck($param, "amt")) {
            $query .= "\n    AND  A.amt >= ";
            $query .= $param["amt"];
			$query .= "\n    order by A.amt desc limit 1";
        }
        return $conn->Execute($query);
    }

///////////////////////////////// 후공정
 /**
     * @brief 후공정 가격 검색
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCateAftPriceList($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  A.cate_after_price_seqno AS price_seqno";
        $query .= "\n        ,A.amt";
        $query .= "\n        ,A.basic_price";
        $query .= "\n        ,A.sell_rate";
        $query .= "\n        ,A.sell_aplc_price";
        $query .= "\n        ,A.sell_price ";

        $query .= "\n   FROM  cate_after_price AS A";

        $query .= "\n  WHERE  1 = 1";

        if ($this->blankParameterCheck($param, "after_mpcode")) {
            $query .= "\n    AND  A.cate_after_mpcode = ";
            $query .= $param["after_mpcode"];
        }
        if ($this->blankParameterCheck($param, "sell_site")) {
            $query .= "\n    AND  A.cpn_admin_seqno   = ";
            $query .= $param["sell_site"];
        }
        if ($this->blankParameterCheck($param, "price_seqno")) {
            $query .= "\n    AND  A.cate_after_price_seqno = ";
            $query .= $param["price_seqno"];
        }
		if ($this->blankParameterCheck($param, "amt")) {
            $query .= "\n    AND  A.amt * 1 >= ";
            $query .= $param["amt"];
			$query .= "\n    order by A.amt * 1 asc limit 1";
        }

        return $conn->Execute($query);
    }

    /**
     * @brief 후공정, 박 가격 검색
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectAfterFoilPressPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    SELECT  A.price as sell_price";

        $query .= "\n      FROM  after_foil_press_price AS A";

        $query .= "\n     WHERE  A.cate_sortcode = %s";
        $query .= "\n       AND  A.after_name    = %s";
        $query .= "\n       AND  A.dvs           = %s";
        $query .= "\n       AND  A.amt * 1          >= %s";

        $query .= "\n    ORDER BY A.amt * 1 asc limit 1";

        $query  = sprintf($query, $param["cate_sortcode"]
            , $param["after_name"]
            , $param["dvs"]
            , $param["amt"]);

        return $conn->Execute($query);
    }

    function selectCatePaperHtml($conn, $cate_sortcode, &$price_info_arr) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        /* before adding booklet
        $temp = array();
        $temp["col"]    = " mpcode";
        $temp["col"]   .= ",name, dvs, color, basisweight";
        $temp["table"]  = "cate_paper";
        $temp["where"]["cate_sortcode"] = $cate_sortcode;
        $rs = $this->selectData($conn, $temp);
        $default = ProductDefaultSel::DEFAULT_SEL[$cate_sortcode]["paper"];
        return makeCatePaperOption($rs, $default, $price_info_arr);
        */

        $temp = array();
        $temp["col"]    = " mpcode";
        $temp["col"]   .= ",sort";
        $temp["col"]   .= ",TRIM(name)        AS name";
        $temp["col"]   .= ",TRIM(dvs)         AS dvs";
        $temp["col"]   .= ",TRIM(color)       AS color";
        $temp["col"]   .= ",TRIM(basisweight) AS basisweight";
        $temp["table"]  = "cate_paper";
        $temp["where"]["cate_sortcode"] = $cate_sortcode;

        $rs = $this->selectData($conn, $temp);

        $default = ProductDefaultSel::DEFAULT_SEL[$cate_sortcode]["paper"];

        return makeCatePaperOption($rs, $default, $price_info_arr);
    }

    function selectCatePrintTmptHtml($conn,
                                     $param,
                                     &$price_info_arr) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $default_arr = ProductDefaultSel::DEFAULT_SEL[$param["cate_sortcode"]];
        $default_print = $default_arr["print"];
        $default_purp  = $default_arr["print_purp"];

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  A.name";
        $query .= "\n        ,A.side_dvs";
        $query .= "\n        ,A.purp_dvs";
        $query .= "\n        ,B.mpcode";

        $query .= "\n   FROM  prdt_print AS A";
        $query .= "\n        ,cate_print AS B";

        $query .= "\n  WHERE  A.prdt_print_seqno = B.prdt_print_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";
        if ($this->blankParameterCheck($param, "purp_dvs")) {
            $query .= "\n    AND  A.purp_dvs      = " . $param["purp_dvs"];
        }

        $query  = sprintf($query, $param["cate_sortcode"]);

        $rs = $conn->Execute($query);
        return makeCatePrintOption($rs,
            $default_print,
            $default_purp,
            $price_info_arr);
    }

    function selectCateSizeHtml($conn,
                                $cate_sortcode,
                                &$price_info_arr,
                                $pos_flag = false) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $default = ProductDefaultSel::DEFAULT_SEL[$cate_sortcode]["size"];
        $pos_num_arr = 0;
        if ($pos_flag === true) {
            $pos_num_arr = PrdtDefaultInfo::POSITION_NUMBER[$cate_sortcode];
        }

        $cate_sortcode = $this->parameterEscape($conn, $cate_sortcode);

        $query  = "\n SELECT  A.name";
        $query .= "\n        ,A.work_wid_size";
        $query .= "\n        ,A.work_vert_size";
        $query .= "\n        ,A.cut_wid_size";
        $query .= "\n        ,A.cut_vert_size";
        $query .= "\n        ,A.tomson_wid_size";
        $query .= "\n        ,A.tomson_vert_size";
        $query .= "\n        ,A.design_wid_size";
        $query .= "\n        ,A.design_vert_size";
        $query .= "\n        ,A.affil";
        $query .= "\n        ,B.mpcode";

        $query .= "\n   FROM  prdt_stan AS A";
        $query .= "\n        ,cate_stan AS B";

        $query .= "\n  WHERE  A.prdt_stan_seqno = B.prdt_stan_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";

        $query  = sprintf($query, $cate_sortcode);

        $rs = $conn->Execute($query);

		return makeCateSizeOption($rs, $default, $pos_num_arr, $price_info_arr);
    }

    function selectCateOptHtml($conn, $cate_sortcode) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $cate_sortcode = $this->parameterEscape($conn, $cate_sortcode);

        $query  = "\n SELECT  A.opt_name AS name";
        $query .= "\n        ,B.mpcode";
        $query .= "\n        ,B.basic_yn";

        $query .= "\n   FROM  prdt_opt AS A";
        $query .= "\n        ,cate_opt AS B";

        $query .= "\n  WHERE  A.prdt_opt_seqno = B.prdt_opt_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";
        $query .="\n AND  exists (select 1 from cate_opt_price as C where B.mpcode = C.cate_opt_mpcode)";

        $query  = sprintf($query, $cate_sortcode);

        $rs = $conn->Execute($query);

        $info_arr = array();
        $html = makeCateOptUl($rs, $info_arr);

        return array("info_arr" => $info_arr,
            "html"     => $html);
    }

    function selectCateAmtHtml($conn, $param, &$price_info_arr) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"]    = "amt";
        $temp["table"]  = $param["table_name"];
        $temp["where"]["cate_sortcode"] = $param["cate_sortcode"];
        $temp["order"]  = "amt + 0";

        $rs = $this->distinctData($conn, $temp);
        $default =
            ProductDefaultSel::DEFAULT_SEL[$param["cate_sortcode"]]["amt"];

        return makeCateAmtOption($rs,
            $param["amt_unit"],
            $default,
            $price_info_arr);
    }

    function selectCatePrintPurpHtml($conn, $cate_sortcode) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $default = ProductDefaultSel::DEFAULT_SEL[$cate_sortcode]["print_purp"];

        $cate_sortcode = $this->parameterEscape($conn, $cate_sortcode);

        $query  = "\n SELECT  DISTINCT A.purp_dvs";

        $query .= "\n   FROM  prdt_print AS A";
        $query .= "\n        ,cate_print AS B";

        $query .= "\n  WHERE  A.prdt_print_seqno = B.prdt_print_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";

        $query  = sprintf($query, $cate_sortcode);

        $rs = $conn->Execute($query);

        $arr = array(
            "dvs" => "purp_dvs",
            "sel" => $default
        );

        return makeOptionHtml($rs, $arr);
    }

    function selectCateAfterHtml($conn, $cate_sortcode, $except_arr = array()) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $cate_sortcode = $this->parameterEscape($conn, $cate_sortcode);

        $query  = "\n SELECT  A.after_name AS name";
        $query .= "\n        ,B.basic_yn";

        $query .= "\n   FROM  prdt_after AS A";
        $query .= "\n        ,cate_after AS B";

        $query .= "\n  WHERE  A.prdt_after_seqno = B.prdt_after_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";

        $query  = sprintf($query, $cate_sortcode);

        $rs = $conn->Execute($query);

        $info_arr = array();
        $html = makeCateAftUl($rs, $info_arr, $except_arr);

        return array("info_arr" => $info_arr,
            "html"     => $html);
    }

    function selectCateAddOptInfoHtml($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $cate_sortcode = $this->parameterEscape($conn, $param["cate_sortcode"]);

        $query  = "\n SELECT  A.opt_name";
        $query .= "\n        ,A.depth1";
        $query .= "\n        ,A.depth2";
        $query .= "\n        ,A.depth3";
        $query .= "\n        ,B.mpcode";

        $query .= "\n   FROM  prdt_opt AS A";
        $query .= "\n        ,cate_opt AS B";

        $query .= "\n  WHERE  A.prdt_opt_seqno = B.prdt_opt_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";
        $query .= "\n    AND  B.basic_yn = 'N'";
        $query .= "\n    AND  A.opt_name IN (%s)";

        $query  = sprintf($query, $cate_sortcode, $param["opt_name"]);

        $rs = $conn->Execute($query);

        return makeCateAddOpt($rs, $param["opt_idx"]);
    }

/**
     * @brief 카테고리 후공정 정보 검색
     *
     * @param $conn  = connection identifier
     * @param $dvs   = 정보 구분
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCateAftInfo($conn, $dvs, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        if ($dvs === "AFTER_NAME") {
            $query  = "\n SELECT  DISTINCT after_name";
        } else if ($dvs === "DEPTH1") {
            $query  = "\n SELECT  DISTINCT depth1";
        } else if ($dvs === "DEPTH2") {
            $query  = "\n SELECT  DISTINCT depth2";
        } else if ($dvs === "DEPTH3") {
        $query  = "\n SELECT  DISTINCT depth3";
        //카운트
        } else if ($dvs === "COUNT") {
            $query  = "\nSELECT  COUNT(*) AS cnt";
        } else if ($dvs === "SEQ") {
            $query  = "\n SELECT  A.after_name";
            $query .= "\n        ,A.depth1";
            $query .= "\n        ,A.depth2";
            $query .= "\n        ,A.depth3";
            $query .= "\n        ,A.crtr_unit";
            $query .= "\n        ,B.mpcode";
            $query .= "\n        ,B.basic_yn";
            $query .= "\n        ,B.cate_after_seqno";
            $query .= "\n        ,A.crtr_unit";
        }

        $query .= "\n   FROM  prdt_after AS A";
        $query .= "\n        ,cate_after AS B";

        $query .= "\n  WHERE  A.prdt_after_seqno = B.prdt_after_seqno";

        if ($this->blankParameterCheck($param, "cate_sortcode")) {
            $query .= "\n    AND  B.cate_sortcode = ";
            $query .= $param["cate_sortcode"];
        }
        //카테고리 분류코드 LIKE
        if ($this->blankParameterCheck($param ,"cate_sortcode_like")) {
            $val = substr($param["cate_sortcode_like"], 1, -1);
            $query .= "\n   AND  cate_sortcode LIKE '" . $val. "%'";
        }
        if ($this->blankParameterCheck($param, "basic_yn")) {
            $query .= "\n    AND  B.basic_yn = ";
            $query .= $param["basic_yn"];
        }
        if ($this->blankParameterCheck($param, "after_name")) {
            $query .= "\n    AND  A.after_name = ";
            $query .= $param["after_name"];
        }
        if ($this->blankParameterCheck($param, "depth1")) {
            $query .= "\n    AND  A.depth1 = ";
            $query .= $param["depth1"];
        }
        if ($this->blankParameterCheck($param, "depth2")) {
            $query .= "\n    AND  A.depth2 = ";
            $query .= $param["depth2"];
        }
        if ($this->blankParameterCheck($param, "depth3")) {
            $query .= "\n    AND  A.depth3 = ";
            $query .= $param["depth3"];
        }
        if ($this->blankParameterCheck($param ,"seqno")) {
            $query .= "\n   AND  cate_after_seqno = $param[seqno]";
        }

        if ($dvs === "SEQ" || $dvs === "COUNT") {

            if ($this->blankParameterCheck($param ,"search_txt")) {
                $val = substr($param["search_txt"], 1, -1);
                $query .= "\n   AND  after_name LIKE '%" . $val . "%'";
            }

            if ($this->blankParameterCheck($param ,"sorting")) {
                $sorting = substr($param["sorting"], 1, -1);
                $query .= "\n ORDER BY " . $sorting;

                if ($this->blankParameterCheck($param ,"sorting_type")) {
                    $sorting_type = substr($param["sorting_type"], 1, -1);
                    $query .= " " . $sorting_type;
                }
            }

            if ($this->blankParameterCheck($param ,"s_num")) {
                $s_num = substr($param["s_num"], 1, -1);
            }

            if ($this->blankParameterCheck($param ,"list_num")) {
                $list_num = substr($param["list_num"], 1, -1);
            }

            if ($this->blankParameterCheck($param ,"s_num") &&
                $this->blankParameterCheck($param ,"list_num") &&
                $dvs != "COUNT") {
                $query .= "\nLIMIT ". $s_num . ", " . $list_num;
            }
        }

        return $conn->Execute($query);
    }

}
?>
