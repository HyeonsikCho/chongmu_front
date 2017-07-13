<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/CommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/product/ProductCommonHtml.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/define/product_default_sel.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/common_define/prdt_default_info.php');

class ProductCommonDAO extends CommonDAO {
    function __construct() {
    }

	function selectCpoint($conn,$param){
        if ($this->connectionCheck($conn) === false) {
            return false;
        }
		$sql = "select c_rate,c_user_rate from cate where sortcode=%s";
		$sql = sprintf($sql,$param['cate_sortcode']);

		$rs = $conn->Execute($sql);
		return $rs;
	}

    /**
     * @brief 카테고리 사진 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 분류코드
     * $param["seq"] = 사진 순서
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCatePhoto($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"]   = "file_path, save_file_name";
        $temp["table"] = "cate_photo";
        $temp["where"]["cate_sortcode"] = $param["cate_sortcode"];
        $temp["order"] = "seq ASC";

        return $this->selectData($conn, $temp);
    }

    /**
     * @brief 카테고리 배너 검색
     *
     * @param $conn          = connection identifier
     * @param $cate_sortcode = 검색조건 파라미터
     * @param $is_info       = 정보생성인지 이미지 출력인지 구분
     *
     * @return 검색결과
     */
    function selectCateBanner($conn, $cate_sortcode, $is_info = true) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $col = "";
        if ($is_info === true) {
            $col = "file_path, save_file_name, url_addr, target_yn";
        } else {
            $col = "file_path, save_file_name";
        }

        $temp = array();
        $temp["col"]   = $col;
        $temp["table"] = "cate_banner";
        $temp["where"]["cate_sortcode"] = $cate_sortcode;

        return $this->selectData($conn, $temp);
    }

    /**
     * @brief 카테고리 책자형 여부, 수량단위 검색
     *
     * @param $conn          = connection identifier
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return 낱장형 true / 책자형 false
     */
    function selectCateInfo($conn, $cate_sortcode) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"]   = "mono_dvs, amt_unit, tmpt_dvs";
        $temp["table"] = "cate";
        $temp["where"]["sortcode"] = $cate_sortcode;

        $rs = $this->selectData($conn, $temp);

        return $rs->fields;
    }

    /**
     * @brief 판매채널에 따른 테이블명을 반환
     *
     * @param $conn     = connection identifier
     * @param $mono_dvs = 확정형(0)/계산형(1) 구분
     * @param $seqno    = 회사 일련번호
     *
     * @return 가격 테이블명
     */
    function selectPriceTableName($conn, $mono_dvs, $seqno) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"]   = "price_tb_name";
        $temp["table"] = "cpn_admin";
        $temp["where"]["cpn_admin_seqno"] = $seqno;
        $rs = $this->selectData($conn, $temp);
        $table_name = explode('|', $rs->fields["price_tb_name"]);
        return $table_name[$mono_dvs];
    }

    /**
     * @brief 카테고리 종이정보 검색
     *
     * @param $conn            = connection identifier
     * @param $cate_sortcode   = 카테고리 분류코드
     * @param &$price_info_arr = 가격검색용 정보저장 배열
     *
     * @return option html
     */
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

    /**
     * @brief 카테고리 인쇄도수정보 검색
     *
     * @param $conn            = connection identifier
     * @param $param           = 검색조건 파라미터
     * @param &$price_info_arr = 가격검색용 정보저장 배열
     *
     * @return 면 구분별 html 배열
     */
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

    /**
     * @brief 카테고리 인쇄방식 검색
     *
     * @param $conn          = connection identifier
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return option html
     */
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

    /**
     * @brief 카테고리 사이즈정보 검색
     *
     * @param $conn           = connection identifier
     * @param $cate_sortcode  = 카테고리 분류코드
     * @param $price_info_arr = 가격검색용 정보저장 배열
     * @param $pos_flag       = 사이즈별 자리수 표시 여부
     *
     * @return option html
     */
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

    /**
     * @brief 카테고리 사이즈정보 검색
     *
     * @param $conn           = connection identifier
     * @param $cate_sortcode  = 카테고리 분류코드
     * @param $price_info_arr = 가격검색용 정보저장 배열
     * @param $pos_flag       = 사이즈별 자리수 표시 여부
     *
     * @return option html
     */
    function selectCateKindHtml($conn,
                                $cate_sortcode,
                                &$price_info_arr,
                                $pos_flag = false) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $default = ProductDefaultSel::DEFAULT_SEL[$cate_sortcode]["size_typ"];
        $pos_num_arr = 0;
        if ($pos_flag === true) {
            $pos_num_arr = PrdtDefaultInfo::POSITION_NUMBER[$cate_sortcode];
        }

        $cate_sortcode = $this->parameterEscape($conn, $cate_sortcode);

        $query  = "\n SELECT  distinct typ";

        $query .= "\n   FROM  prdt_stan AS A";
        $query .= "\n        ,cate_stan AS B";

        $query .= "\n  WHERE  A.prdt_stan_seqno = B.prdt_stan_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";

        $query  = sprintf($query, $cate_sortcode);

        $rs = $conn->Execute($query);

		return makeCateKindOption($rs, $default, $pos_num_arr, $price_info_arr);
    }


    /**
     * @brief 카테고리 수량정보 검색
     *
     * @detail $param["table_name"] = 가격 테이블명
     * @param["cate_sortcode"] = 카테고리 분류코드
     * @param["amt_unit"] = 수량단위
     *
     * @param $conn  = connection identifier
     * @param $param = 정보 배열
     * @param $price_info_arr = 가격검색용 정보저장 배열
     *
     * @return option html
     */
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

    /**
     * @brief 카테고리 포장방식 검색
     *
     * @param $conn          = connection identifier
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return option html
     */
    function selectCatePackWayHtml($conn, $cate_sortcode) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $cate_sortcode = $this->parameterEscape($conn, $cate_sortcode);

        $query  = "\n SELECT  A.pack_name";

        $query .= "\n   FROM  pack_way      AS A";
        $query .= "\n        ,cate_pack_way AS B";

        $query .= "\n  WHERE  A.pack_way_seqno = B.pack_way_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";

        $query  = sprintf($query, $cate_sortcode);

        $rs = $conn->Execute($query);

        return makeCatePackWayOption($rs);
    }

    /**
     * @brief 카테고리 옵션 검색
     *
     * @param $conn          = connection identifier
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return ul html
     */
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

    /**
     * @brief 카테고리 추가 옵션 검색
     *
     * @param $conn          = connection identifier
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return option html
     */
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
     * @brief 카테고리 후공정 검색
     *
     * @param $conn          = connection identifier
     * @param $cate_sortcode = 카테고리 분류코드
     * @param $except_arr    = 카테고리 분류코드
     *
     * @return ul html
     */
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

    /**
     * @brief 카테고리 추가 후공정 검색
     *
     * @param $conn          = connection identifier
     * @param $cate_sortcode = 카테고리 분류코드
     * @param $except_arr    = html 생성 제외 후공정
     *
     * @return option html
     */
    function selectCateAddAfterInfoHtml($conn, $param, $except_arr = array()) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $cate_sortcode = $this->parameterEscape($conn, $param["cate_sortcode"]);

        $query  = "\n SELECT  A.after_name";
        $query .= "\n        ,A.depth1";
        $query .= "\n        ,A.depth2";
        $query .= "\n        ,A.depth3";
        $query .= "\n        ,B.mpcode";

        $query .= "\n   FROM  prdt_after AS A";
        $query .= "\n        ,cate_after AS B";

        $query .= "\n  WHERE  A.prdt_after_seqno = B.prdt_after_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";
        $query .= "\n    AND  B.basic_yn = 'N'";
        $query .= "\n    AND  A.after_name IN (%s)";

        $query  = sprintf($query, $cate_sortcode, $param["after_name"]);
        $rs = $conn->Execute($query);

        return makeCateAddAfter($rs, $except_arr);
    }

    /**
     * @brief 카테고리 추가 후공정 검색
     *
     * @param $conn          = connection identifier
     * @param $cate_sortcode = 카테고리 분류코드
     * @param $except_arr    = html 생성 제외 후공정
     *
     * @return option html
     */
    function selectCateAddAfterInfo($conn, $param, $except_arr = array()) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $cate_sortcode = $this->parameterEscape($conn, $param["cate_sortcode"]);

        $query  = "\n SELECT  A.after_name";
        $query .= "\n        ,A.depth1";
        $query .= "\n        ,A.depth2";
        $query .= "\n        ,A.depth3";
        $query .= "\n        ,B.mpcode";

        $query .= "\n   FROM  prdt_after AS A";
        $query .= "\n        ,cate_after AS B";

        $query .= "\n  WHERE  A.prdt_after_seqno = B.prdt_after_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";
        $query .= "\n    AND  B.basic_yn = 'N'";
        $query .= "\n    AND  A.after_name IN (%s)";
        $query .= "\n    order by after_name, depth1";

        $query  = sprintf($query, $cate_sortcode, $param["after_name"]);
        return $conn->Execute($query);
    }


    /**
     * @brief 카테고리 추가 후공정 검색
     *
     * @param $conn   = connection identifier
     * @param $mpcode = 카테고리 종이 맵핑코드
     *
     * @return option html
     */
    function selectPaperDscr($conn, $mpcode) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }
        $temp = array();
        $temp["col"]   = "name, dvs";
        $temp["table"] = "cate_paper";
        $temp["where"]["mpcode"] = $mpcode;

        $rs = $this->selectData($conn, $temp);

        $name = $rs->fields["name"];
        $dvs  = $rs->fields["dvs"];
        unset($rs);
        unset($temp);

        $temp["col"]   = "paper_sense";
        $temp["table"] = "paper_dscr";
        $temp["where"]["name"] = $name;
        $temp["where"]["dvs"]  = $dvs;

        $rs = $this->selectData($conn, $temp);

        return $rs->fields["paper_sense"];
    }

    /**
     * @brief 회원 등급별 할인 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 분류코드
     * $param["grade"] = 회원 등급
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     * @param &$price_info_arr = 가격검색용 정보저장 배열
     *
     * @return option html
     */
    function selectGradeSalePriceHtml($conn, $param, &$price_info_arr) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"]   = "rate";
        $temp["table"] = "grade_sale_price";
        $temp["where"]["cate_sortcode"] = $param["cate_sortcode"];
        $temp["where"]["grade"]         = $param["grade"];
//$conn->debug = 1;
        $rs = $this->selectData($conn, $temp);

        $rate = $rs->fields["rate"];
        if (empty($rate) === true) {
            $rate = 0;
        }

        $arr = array(
            "rate"  => $rate,
            "grade" => $param["grade"],
            "price" => $param["sell_price"]
        );

        return makeGradeSaleDl($arr, $price_info_arr);
    }

    /**
     * @brief 회원 등급별 할인 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 중분류코드
     * $param["member_seqno"] = 회원 일련번호
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return option html
     */
    function selectCpCount($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n SELECT  count(1) AS cp_count";

        $query .= "\n   FROM  member_cp AS A";
        $query .= "\n        ,cp        AS B";

        $query .= "\n  WHERE  A.cp_seqno = B.cp_seqno";
        $query .= "\n    AND  A.member_seqno = %s";
        $query .= "\n    AND  B.cate_sortcode = %s";
        $query .= "\n    AND  now() <= B.public_extinct_date";

        $query  = sprintf($query, $param["member_seqno"]
                                , $param["cate_sortcode"]);

        $rs = $conn->Execute($query);

        return $rs->fields["cp_count"];
    }

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
        $temp = array();
        $temp["col"]   = "new_price";
        $temp["table"] = $param["table_name"];
        $temp["where"]["cate_sortcode"]     = $param["cate_sortcode"];
        $temp["where"]["cate_paper_mpcode"] = $param["paper_mpcode"];
        $temp["where"]["cate_beforeside_print_mpcode"] = $param["print_mpcode"];
        $temp["where"]["cate_stan_mpcode"]  = $param["stan_mpcode"];
        $temp["where"]["amt"]               = $param["amt"];
		$rs = $this->selectData($conn, $temp);
        return $rs->fields["new_price"];
    }

    /**
     * @brief 상품 계산형 가격 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 중분류코드
     * $param["paper_mpcode"] = 카테고리 종이 맵핑코드
     * $param["bef_print_mpcode"] = 카테고리 전면 인쇄 맵핑코드
     * $param["bef_add_print_mpcode"] = 카테고리 전면 추가 인쇄 맵핑코드
     * $param["aft_print_mpcode"] = 카테고리 후면 인쇄 맵핑코드
     * $param["aft_add_print_mpcode"] = 카테고리 후면 추가 인쇄 맵핑코드
     * $param["stan_mpcode"] = 카테고리 규격 맵핑코드
     * $param["amt"] = 수량
     * $param["page"] = 페이지 수
     * $param["page_dvs"] = 페이지 구분(표지, 내지...)
     * $param["page_detail"] = 페이지 상세(날개...)
     * $param["table_name"] = 가격 테이블명
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 신규가격
     */
    function selectPrdtCalcPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"]  = " paper_price";
        $temp["col"] .= ",print_price";
        $temp["col"] .= ",output_price";
        $temp["col"] .= ",sum_price";
        $temp["table"] = $param["table_name"];
        $temp["where"]["cate_sortcode"]     = $param["cate_sortcode"];
        $temp["where"]["cate_paper_mpcode"] = $param["paper_mpcode"];
        $temp["where"]["cate_beforeside_print_mpcode"] = $param["bef_print_mpcode"];
        $temp["where"]["cate_beforeside_add_print_mpcode"] = $param["bef_add_print_mpcode"];
        $temp["where"]["cate_aftside_print_mpcode"] = $param["aft_print_mpcode"];
        $temp["where"]["cate_aftside_add_print_mpcode"] = $param["aft_add_print_mpcode"];
        $temp["where"]["cate_stan_mpcode"]  = $param["stan_mpcode"];
        $temp["where"]["amt"]               = $param["amt"];
        $temp["where"]["page"]              = $param["page"];
        $temp["where"]["page_dvs"]          = $param["page_dvs"];
		$temp["where"]["affil"]          = $param["affil"];
        if ($this->blankParameterCheck($param, "page_detail")) {
            $temp["where"]["page_detail"]       = $param["page_detail"];
        }

        $rs = $this->selectData($conn, $temp);

        return $rs->fields;
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
    function selectCateAfterInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  B.mpcode";
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

        $query  = sprintf($query, $param["after_name"]
                                , $param["cate_sortcode"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 카테고리 후공정 하위 depth 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 분류코드
     * $param["after_name"] = 후공정명
     * $param["depth1"] = 후공정 depth1
     * $param["depth2"] = 후공정 depth2
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     * @param $flag  = 맵핑코드 검색여부
     *
     * @return option html
     */
    function selectCateAfterDepthHtml($conn, $param, $flag = true) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "";

        if ($this->blankParameterCheck($param, "after_name")) {
            $query  = "\n SELECT  A.depth1 AS lower_depth";
        }
        if ($this->blankParameterCheck($param, "depth1")) {
            $query  = "\n SELECT  A.depth2 AS lower_depth";
        }
        if ($this->blankParameterCheck($param, "depth2")) {
            $query  = "\n SELECT  A.depth3 AS lower_depth";
        }
        if ($flag === true) {
            $query .= "\n        ,B.mpcode";
        }
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

        $query  = sprintf($query, $param["after_name"]
                                , $param["cate_sortcode"]);

        $rs = $conn->Execute($query);

        $arr = array(
            "val" => "mpcode",
            "dvs" => "lower_depth"
        );

        return makeOptionHtml($rs, $arr);
    }

    /**
     * @brief 카테고리 후공정 가격 검색
     *
     * @detail $param["mpcode"] = 카테고리 후공정 맵핑코드
     * $param["sell_site"] = 판매채널
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCateAfterPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $except_arr = array("mpcode" => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = "\n SELECT  A.amt";
        $query .= "\n        ,A.sell_price";
        $query .= "\n        ,A.cate_after_mpcode AS mpcode";
        $query .= "\n   FROM  cate_after_price AS A";
        $query .= "\n  WHERE  A.cate_after_mpcode IN (%s)";
        $query .= "\n    AND  A.cpn_admin_seqno = %s";

        $query  = sprintf($query, $param["mpcode"]
                                , $param["sell_site"]);

        return $conn->Execute($query);
    }

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
     * @brief 카테고리 옵션 가격 검색
     *
     * @detail $param["mpcode"] = 카테고리 후공정 맵핑코드
     * $param["sell_site"] = 판매채널
     *
     * @param $conn   = connection identifier
     * @param $mpcode = 카테고리 후공정 맵핑코드
     *
     * @return 검색결과
     */
    function selectCateOptPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $except_arr = array("mpcode" => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = "\n SELECT  A.amt";
        $query .= "\n        ,A.sell_price";
        $query .= "\n        ,A.cate_opt_mpcode AS mpcode";
        $query .= "\n   FROM  cate_opt_price AS A";
        $query .= "\n  WHERE  A.cate_opt_mpcode IN (%s)";
        $query .= "\n    AND  A.cpn_admin_seqno = %s";

        $query  = sprintf($query, $param["mpcode"]
                                , $param["sell_site"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 카테고리 옵션 가격 검색 단일
     *
     * @detail $param["cate_sortcode"] = 카테고리 분류코드
     * $param["basic_yn"] = 기본여부
     * $param["sell_site"] = 판매채널
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCateOptSinglePrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n SELECT  B.sell_price";

        $query .= "\n   FROM  cate_opt       AS A";
        $query .= "\n        ,cate_opt_price AS B";

        $query .= "\n  WHERE  A.mpcode = B.cate_opt_mpcode";
        $query .= "\n    AND  A.cate_sortcode   = %s";
        $query .= "\n    AND  A.basic_yn        = %s";
        $query .= "\n    AND  B.cpn_admin_seqno = %s";
        $query .= "\n  LIMIT  1";

        $query  = sprintf($query, $param["cate_sortcode"]
                                , $param["basic_yn"]
                                , $param["sell_site"]);

        $rs = $conn->Execute($query);

        return $rs->fields["sell_price"];
    }

    /**
     * @brief 카테고리 후공정 가격 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 분류코드
     * $param["basic_yn"] = 기본여부
     * $param["sell_site"] = 판매채널
     * $param["except_after"] = 검색제외 후공정명
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCateAfterSinglePrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  B.sell_price";

        $query .= "\n      FROM  prdt_after       AS A";
        $query .= "\n           ,cate_after       AS B";
        $query .= "\n           ,cate_after_price AS C";

        $query .= "\n     WHERE  A.prdt_after_seqno = B.prdt_after_seqno";
        $query .= "\n       AND  B.mpcode = C.cate_after_mpcode";
        if ($this->blankParameterCheck($param, "except_after")) {
            $query .= "\n       AND  A.after_name != " . $param["except_after"];
        }
        $query .= "\n       AND  B.cate_sortcode   = %s";
        $query .= "\n       AND  B.basic_yn        = %s";
        $query .= "\n       AND  C.cpn_admin_seqno = %s";
        $query .= "\n       AND  (%s + 0) <= C.amt";
        $query .= "\n  ORDER BY  C.amt ASC";
        $query .= "\n     LIMIT  1";

        $query  = sprintf($query, $param["cate_sortcode"]
                                , $param["basic_yn"]
                                , $param["sell_site"]
                                , $param["amt"]);

        $rs = $conn->Execute($query);

        // 해당하는 수량이 없을경우 제일 마지막 수량 판매가격 반환
        if ($rs->EOF) {
            $query .= "\n    SELECT  B.sell_price";

            $query .= "\n      FROM  cate_after       AS A";
            $query .= "\n           ,cate_after_price AS B";

            $query .= "\n     WHERE  A.mpcode = B.cate_after_mpcode";
            $query .= "\n       AND  A.cate_sortcode   = %s";
            $query .= "\n       AND  A.basic_yn        = %s";
            $query .= "\n       AND  B.cpn_admin_seqno = %s";
            $query .= "\n  ORDER BY  B.amt ASC";
            $query .= "\n     LIMIT  1";

            $query  = sprintf($query, $param["cate_sortcode"]
                                    , $param["basic_yn"]
                                    , $param["sell_site"]);

            $rs = $conn->Execute($query);
        }

        $ret = $rs->fields["sell_price"];

        if ($rs->EOF) {
            $ret = 0;
        }

        return $ret;
    }

    /**
     * @brief 종이 수량 기준단위 검색
     *
     * @param $conn   = connection identifier
     * @param $mpcode = 카테고리 종이 맵핑코드
     * @param $col    = 상품종이에서 검색할 필드
     *
     * @return 종이 기준단위
     */
    function selectPrdtPaperInfo($conn, $mpcode, $col) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $rs = $this->selectCatePaperInfo($conn, $mpcode);

        $search_check = sprintf("%s|%s|%s|%s", $rs["name"]
                                             , $rs["dvs"]
                                             , $rs["color"]
                                             , $rs["basisweight"]);

        unset($temp);

        $temp["col"]   = $col;
        $temp["table"] = "prdt_paper";
        $temp["where"]["search_check"] = $search_check;

        $rs = $this->selectData($conn, $temp);

        return $rs->fields;
    }

    /**
     * @brief 종이 가격 검색
     *
     * @detail $param["sell_site"] = 판매채널
     * @param["mpcode"] = 상품 종이 맵핑코드
     *
     * @param $conn  = connection identifier
     * @param $param = 검색정보 파라미터
     *
     * @return 종이 판매가격
     */
    function selectPaperPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"]   = "sell_price";
        $temp["table"] = "prdt_paper_price";
        $temp["where"]["cpn_admin_seqno"]   = $param["sell_site"];
        $temp["where"]["prdt_paper_mpcode"] = $param["mpcode"];

        $rs = $this->selectData($conn, $temp);

        return $rs->fields["sell_price"];
    }

    /**
     * @brief 인쇄 가격 검색
     *
     * @detail $param["sell_site"] = 판매채널
     * @param["mpcode"] = 상품 종이 맵핑코드
     *
     * @param $conn  = connection identifier
     * @param $param = 검색정보 파라미터
     *
     * @return 검색결과
     */
    function selectPrintPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $except_arr = array("amt" => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = "\n    SELECT  sell_price";
        $query .= "\n      FROM  prdt_print_price";
        $query .= "\n     WHERE  cpn_admin_seqno = %s";
        $query .= "\n       AND  prdt_print_info_mpcode = %s";
        $query .= "\n       AND  %s <= (amt + 0)";
        $query .= "\n  ORDER BY  amt ASC";
        $query .= "\n     LIMIT  1";

        $query  = sprintf($query, $param["sell_site"]
                                , $param["mpcode"]
                                , $param["amt"]);

        $rs = $conn->Execute($query);

        // 해당하는 수량이 없을경우 제일 마지막 수량 판매가격 반환
        if ($rs->EOF) {
            $query  = "\n    SELECT  sell_price";
            $query .= "\n      FROM  prdt_print_price";
            $query .= "\n     WHERE  cpn_admin_seqno = %s";
            $query .= "\n       AND  prdt_print_info_mpcode = %s";
            $query .= "\n  ORDER BY  amt DESC";
            $query .= "\n     LIMIT  1";

            $query  = sprintf($query, $param["sell_site"]
                                    , $param["mpcode"]);

            $rs = $conn->Execute($query);
        }

        return $rs->fields["sell_price"];
    }

    /**
     * @brief 출력 가격 검색
     *
     * @detail $param["sell_site"] = 판매채널
     * @param["mpcode"] = 상품 종이 맵핑코드
     *
     * @param $conn  = connection identifier
     * @param $param = 검색정보 파라미터
     *
     * @return 출력 판매가격
     */
    function selectOutputPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    SELECT  sell_price";
        $query .= "\n      FROM  prdt_stan_price";
        $query .= "\n     WHERE  cpn_admin_seqno = %s";
        $query .= "\n       AND  prdt_output_info_mpcode = %s";
        $query .= "\n       AND  board_amt = '1'";

        $query  = sprintf($query, $param["sell_site"]
                                , $param["mpcode"]);

        $rs = $conn->Execute($query);

        return $rs->fields["sell_price"];
    }

    /**
     * @brief 비규격 사이즈 인쇄가격 계산정보 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 분류코드
     * @param["mpcode"] = 상품 인쇄 맵핑코드
     *
     * @param $conn  = connection identifier
     * @param $param = 검색정보 파라미터
     *
     * @return 상품 인쇄 정보
     */
    function selectPrdtPrintInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $cate_sortcode_mid = substr($param["cate_sortcode"], 0, 6);
        $cate_sortcode_mid = $this->parameterEscape($conn, $cate_sortcode_mid);

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    SELECT  A.tot_tmpt";
        $query .= "\n           ,A.output_board_amt";
        $query .= "\n           ,B.crtr_unit";
        $query .= "\n           ,B.mpcode AS prdt_mpcode";

        $query .= "\n      FROM  prdt_print      AS A";
        $query .= "\n           ,prdt_print_info AS B";
        $query .= "\n           ,cate_print      AS C";

        $query .= "\n     WHERE  A.prdt_print_seqno = C.prdt_print_seqno";
        $query .= "\n       AND  A.print_name       = B.print_name";
        $query .= "\n       AND  A.purp_dvs         = B.purp_dvs";
        $query .= "\n       AND  B.cate_sortcode    = %s";
        $query .= "\n       AND  C.cate_sortcode    = %s";
        $query .= "\n       AND  C.mpcode           = %s";

        $query  = sprintf($query, $cate_sortcode_mid
                                , $param["cate_sortcode"]
                                , $param["mpcode"]);

        $rs = $conn->Execute($query);

        return $rs->fields;
    }

    /**
     * @brief 비규격 사이즈 출력가격 계산정보 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 분류코드
     * @param["mpcode"] = 상품 규격 맵핑코드
     *
     * @param $conn  = connection identifier
     * @param $param = 검색정보 파라미터
     *
     * @return 상품 인쇄 정보
     */
    function selectPrdtOutputMpcode($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  B.mpcode";

        $query .= "\n   FROM  prdt_stan          AS A";
        $query .= "\n        ,prdt_output_info   AS B";
        $query .= "\n        ,cate_stan          AS C";

        $query .= "\n  WHERE  A.prdt_stan_seqno  = C.prdt_stan_seqno";
        $query .= "\n    AND  A.output_name      = B.output_name";
        $query .= "\n    AND  A.output_board_dvs = B.output_board_dvs";
        $query .= "\n    AND  C.cate_sortcode    = %s";
        $query .= "\n    AND  C.mpcode           = %s";

        $query  = sprintf($query, $param["cate_sortcode"]
                                , $param["mpcode"]);

        $rs = $conn->Execute($query);

        return $rs->fields["mpcode"];
    }

	/**
     * @brief 하위 카테고리 분류코드 중 가장 작은 값 검색
     *
     * @param $conn = connection identifier
     * @param $sortcode = 상위 카테고리 분류코드
     *
     * @return 하위 카테고리 분류코드
     */
    function selectCateSortcode($conn, $high_sortcode) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"] = "MIN(A.sortcode) AS sortcode";
        $temp["table"] = "cate AS A";
        $temp["where"]["A.high_sortcode"] = $high_sortcode;

        $rs = $this->selectData($conn, $temp);

        return $rs->fields["sortcode"];
    }

	/**********************************************
	*** 메인페이지에 보여줄 상품정보 가져오기 ****
    ***********************************************/
	function selectPrdtForMainpage($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  B.mpcode";

        $query .= "\n   FROM  prdt_stan          AS A";
        $query .= "\n        ,prdt_output_info   AS B";
        $query .= "\n        ,cate_stan          AS C";

        $query .= "\n  WHERE  A.prdt_stan_seqno  = C.prdt_stan_seqno";
        $query .= "\n    AND  A.output_name      = B.output_name";
        $query .= "\n    AND  A.output_board_dvs = B.output_board_dvs";
        $query .= "\n    AND  C.cate_sortcode    = %s";
        $query .= "\n    AND  C.mpcode           = %s";

        $query  = sprintf($query, $param["cate_sortcode"]
                                , $param["mpcode"]);

        $rs = $conn->Execute($query);

        return $rs->fields["mpcode"];
    }

    /**
     * @brief 주문페이지 견적서 팝업에서 정보검색용으로 사용
     *
     * @param $conn  = connection identifier
     * @param $seqno = 회사 일련번호
     *
     * @return 가격 테이블명
     */
    function selectCpnInfo($conn, $seqno) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $seqno = $this->parameterEscape($conn, $seqno);

        $query  = "\n SELECT  sell_site";
        $query .= "\n        ,repre_name";
        $query .= "\n        ,repre_num";
        $query .= "\n        ,addr";
        $query .= "\n        ,addr_detail";
        $query .= "\n   FROM  cpn_admin ";
        $query .= "\n  WHERE  cpn_admin_seqno = %s ";

        $query  = sprintf($query, $seqno);

        $rs = $conn->Execute($query);

        return $rs->fields;
    }

    /**
     * @brief 주문페이지 견적서 팝업에서 정보검색용으로 사용
     *
     * @param $conn  = connection identifier
     * @param $seqno = 회사 일련번호
     *
     * @return 가격 테이블명
     */
    function selectJCOrderInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterEscape($conn, $param);

        $query  = "\n SELECT  sell_site";
        $query .= "\n        ,repre_name";
        $query .= "\n        ,repre_num";
        $query .= "\n        ,addr";
        $query .= "\n        ,addr_detail";
        $query .= "\n   FROM  cpn_admin ";
        $query .= "\n  WHERE  cpn_admin_seqno = %s ";

        $query  = sprintf($query, $seqno);

        $rs = $conn->Execute($query);

        return $rs->fields;
    }
}
?>
