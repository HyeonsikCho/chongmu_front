<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductNcDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_lib/CommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/prdt_default_info.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/product_default_sel.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$commonUtil = new CommonUtil();
$frontUtil = new FrontCommonUtil();
$dao = new ProductCommonDAO();
$fb = new FormBean();

$sell_site = $fb->session("sell_site");

$fb = $fb->getForm();

$dvs  = $fb["dvs"];
$flag = $fb["flag"];

$cate_sortcode = $fb["cate_sortcode"];
$stan_mpcode   = $fb["stan_mpcode"];
$def_stan_name = $fb["def_stan_name"];
$amt           = $fb["amt"];
$amt_unit      = $fb["amt_unit"];
$manu_pos_num  = intval($fb["manu_pos_num"]);
// 표지
$cover_paper_mpcode = $fb["cover_paper_mpcode"];
$cover_page_info    = explode('!', $fb["cover_page_info"]);
$cover_page         = $cover_page_info[0];
$cover_page_detail  = $cover_page_info[1];
$cover_page_dvs     = "표지";
// 내지1
$inner1_paper_mpcode = $fb["inner1_paper_mpcode"];
$inner1_page_info    = explode('!', $fb["inner1_page_info"]);
$inner1_page         = $inner1_page_info[0];
$inner1_page_detail  = $inner1_page_info[1];
$inner1_page_dvs     = "내지";
// 내지2
$inner2_paper_mpcode = $fb["inner2_paper_mpcode"];
$inner2_page_info    = explode('!', $fb["inner2_page_info"]);
$inner2_page         = $inner2_page_info[0];
$inner2_page_detail  = $inner2_page_info[1];
$inner2_page_dvs     = "내지";
// 내지3
$inner3_paper_mpcode = $fb["inner3_paper_mpcode"];
$inner3_page_info    = explode('!', $fb["inner3_page_info"]);
$inner3_page         = $inner3_page_info[0];
$inner3_page_detail  = $inner3_page_info[1];
$inner3_page_dvs     = "내지";

$def_pos_num = PrdtDefaultInfo::POSITION_NUMBER[$cate_sortcode][$def_stan_name];
$pos_num = $def_pos_num / $manu_pos_num;

// 가격 저장 변수
$cover_paper_price  = 0;
$cover_print_price  = 0;
$cover_output_price = 0;
$cover_sum_price    = 0;

$inner1_paper_price  = 0;
$inner1_print_price  = 0;
$inner1_output_price = 0;
$inner1_sum_price    = 0;

$inner2_paper_price  = 0;
$inner2_print_price  = 0;
$inner2_output_price = 0;
$inner2_sum_price    = 0;

$inner3_paper_price  = 0;
$inner3_print_price  = 0;
$inner3_output_price = 0;
$inner3_sum_price    = 0;

$param = array();

//표지
if ($dvs === "all" || $dvs === "cover") {
    // 종이 기준단위, 맵핑코드 검색
    $paper_info = $dao->selectPrdtPaperInfo($conn,
                                            $cover_paper_mpcode,
                                            "crtr_unit, mpcode");
    $paper_crtr_unit = $paper_info["crtr_unit"];

    // 여분지 계산
    $extra_paper_amt_arr = PrdtDefaultInfo::EXTRA_PAPER_AMT;

    $bef_print_name     = $fb["cover_bef_print_name"];
    $bef_add_print_name = $fb["cover_bef_add_print_name"];
    $aft_print_name     = $fb["cover_aft_print_name"];
    $aft_add_print_name = $fb["cover_aft_add_print_name"];

    $extra_paper_amt = $extra_paper_amt_arr[$bef_print_name];
    if ($flag === 'N') {
        $extra_paper_amt += $extra_paper_amt_arr[$bef_add_print_name];
        $extra_paper_amt += $extra_paper_amt_arr[$aft_print_name];
        $extra_paper_amt += $extra_paper_amt_arr[$aft_add_print_name];
    }

    if ($paper_crtr_unit === 'R') {
        $extra_paper_amt /= 500;
    }

    // 표지 실제인쇄수량 검색
    $param["amt"]       = $amt;
    $param["pos_num"]   = $pos_num;
    $param["page_num"]  = $cover_page;
    $param["amt_unit"]  = $amt_unit;
    $param["crtr_unit"] = $paper_crtr_unit;

    $real_paper_amt = $commonUtil->getPaperRealPrintAmt($param);
    $calc_paper_amt = $real_paper_amt + $extra_paper_amt;
    //$calc_paper_amt = ceil($calc_paper_amt);

    // 종이 가격 계산
    unset($param);
    $param["sell_site"] = $sell_site;
    $param["mpcode"]    = $paper_info["mpcode"];

    $cover_paper_price  = $dao->selectPaperPrice($conn, $param);
    $cover_paper_price  = intval($cover_paper_price);
    $cover_paper_price *= $calc_paper_amt;

    // 인쇄, 출력 가격 계산
    // 인쇄 도수명 맵핑코드로 변환
    $print_mpcode_arr = $frontUtil->getPrintMpcode($conn, $dao, $fb, "cover");
    $print_bef_mpcode = $print_mpcode_arr["bef"];
    $print_aft_mpcode = $print_mpcode_arr["aft"];
    $print_bef_add_mpcode = $print_mpcode_arr["bef_add"];
    $print_aft_add_mpcode = $print_mpcode_arr["aft_add"];

    // 카테고리 분류코드, 규격 맵핑코드로
    // 출력 상품 맵핑코드 검색
    $param["cate_sortcode"] = $cate_sortcode;
    $param["mpcode"] = $stan_mpcode;
    $output_prdt_mpcode = $dao->selectPrdtOutputMpcode($conn, $param);

    if ($flag === 'N') {
        // 책자형 일 때 계산로직

        // 카테고리 분류코드, 인쇄 맵핑코드로
        // 총도수랑 출력판 수, 기준단위, 인쇄 상품 맵핑코드 검색
        $param["mpcode"] = $print_bef_mpcode;
        $print_bef_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $param["mpcode"] = $print_aft_mpcode;
        $print_aft_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $param["mpcode"] = $print_bef_add_mpcode;
        $print_bef_add_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $param["mpcode"] = $print_aft_add_mpcode;
        $print_aft_add_rs = $dao->selectPrdtPrintInfo($conn, $param);

        // 인쇄 가격 계산
        $print_bef_tot_tmpt = $print_bef_rs["tot_tmpt"];
        $print_aft_tot_tmpt = $print_aft_rs["tot_tmpt"];
        $print_bef_add_tot_tmpt = $print_bef_add_rs["tot_tmpt"];
        $print_aft_add_tot_tmpt = $print_aft_add_rs["tot_tmpt"];

        $print_crtr_unit = $print_bef_rs["crtr_unit"];
        $print_add_crtr_unit = $print_bef_add_rs["crtr_unit"];

        $print_bef_prdt_mpcode = $print_bef_rs["prdt_mpcode"];
        $print_aft_prdt_mpcode = $print_aft_rs["prdt_mpcode"];
        $print_bef_add_prdt_mpcode = $print_bef_add_rs["prdt_mpcode"];
        $print_aft_add_prdt_mpcode = $print_aft_add_rs["prdt_mpcode"];

        // 전면, 후면 도수 둘 다 0도가 아닐 때
        if ($print_aft_tot_tmpt !== '0' && $print_bef_tot_tmpt !== '0') {

            unset($param);
            // 인쇄대수별 종이수량 재계산용
            $param["amt"]             = $amt;
            $param["amt_unit"]        = $amt_unit;
            $param["paper_crtr_unit"] = $paper_crtr_unit;
            // 인쇄가격 계산용
            $param["pos_num"]         = $pos_num;
            $param["bef_tot_tmpt"]    = intval($print_bef_tot_tmpt);
            $param["aft_tot_tmpt"]    = intval($print_aft_tot_tmpt);
            $param["page_num"]        = $cover_page;
            $param["crtr_unit"]       = $print_crtr_unit;
            $param["bef_mpcode"]      = $print_bef_prdt_mpcode;
            $param["aft_mpcode"]      = $print_aft_prdt_mpcode;
            $param["sell_site"]       = $sell_site;

            $cover_print_price = calcBookletPrintPrice($conn,
                                                       $dao,
                                                       $temp);
        }
        // 추가도수 인쇄가격 계산용
        // 전면, 후면 추가도수 둘 다 0도가 아닐 때
        if ($print_aft_add_tot_tmpt !== '0' && $print_bef_add_tot_tmpt !== '0') {

            unset($param);
            // 인쇄대수별 종이수량 재계산용
            $param["amt"]             = $amt;
            $param["amt_unit"]        = $amt_unit;
            $param["paper_crtr_unit"] = $paper_crtr_unit;
            // 인쇄가격 계산용
            $param["pos_num"]         = $pos_num;
            $param["bef_tot_tmpt"]    = intval($print_bef_add_tot_tmpt);
            $param["aft_tot_tmpt"]    = intval($print_aft_add_tot_tmpt);
            $param["page_num"]        = $cover_page;
            $param["crtr_unit"]       = $print_add_crtr_unit;
            $param["bef_mpcode"]      = $print_bef_add_prdt_mpcode;
            $param["aft_mpcode"]      = $print_aft_add_prdt_mpcode;
            $param["sell_site"]       = $sell_site;

            $cover_print_price += calcBookletPrintPrice($conn,
                                                        $dao,
                                                        $temp);
        }

        // 출력 가격 계산
        $output_bef_board_amt = $print_bef_rs["output_board_amt"];
        $output_aft_board_amt = $print_aft_rs["output_board_amt"];
        $output_bef_add_board_amt = $print_bef_add_rs["output_board_amt"];
        $output_aft_add_board_amt = $print_aft_add_rs["output_board_amt"];

        unset($temp);
        $temp["pos_num"]   = $pos_num;
        $temp["page_num"]  = $page_num;
        $temp["board_amt"] = $output_bef_board_amt +
                             $output_aft_board_amt +
                             $output_bef_add_board_amt +
                             $output_aft_add_board_amt;
        $temp["bef_tmpt"]  = $bef_print_tmpt;
        $temp["aft_tmpt"]  = $aft_print_tmpt;
        $temp["mpcode"]    = $output_prdt_mpcode;
        $temp["sell_site"] = $sell_site;

        $cover_output_price = $this->calcBookletOutputPrice($conn,
                                                            $dao,
                                                            $temp);
    } else {
        // 낱장형 일 때 가격계산 로직

        // 인쇄 가격 계산
        $param["mpcode"] = $print_bef_mpcode;
        $print_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $print_tot_tmpt = $print_rs["tot_tmpt"];
        $print_crtr_unit = $print_rs["crtr_unit"];
        $print_prdt_mpcode = $print_rs["prdt_mpcode"];

        $print_bef_mpcode = $print_mpcode_arr["bef"];
        unset($param);
        $param["tot_tmpt"]       = intval($print_tot_tmpt);
        $param["page_num"]       = 2;
        $param["crtr_unit"]      = $print_crtr_unit;
        $param["mpcode"]         = $print_prdt_mpcode;
        $param["real_paper_amt"] = $real_paper_amt;
        $param["sell_site"]      = $sell_site;

        $cover_print_price = calcSheetPrintPrice($conn,
                                                 $dao,
                                                 $param);

        // 출력 가격 계산
        $output_board_amt = $print_rs["output_board_amt"];
        unset($param);
        $param["board_amt"] = $output_board_amt;
        $param["mpcode"]    = $output_prdt_mpcode;
        $param["sell_site"] = $sell_site;

        $cover_output_price = calcOutputPrice($conn,
                                              $dao,
                                              $param);
    }

    $cover_sum_price = $cover_paper_price +
                       $cover_print_price +
                       $cover_output_price;
}

// 내지1
if ($dvs === "all" || $dvs === "inner1") {
    // 종이 기준단위, 맵핑코드 검색
    $paper_info = $dao->selectPrdtPaperInfo($conn,
                                            $inner1_paper_mpcode,
                                            "crtr_unit, mpcode");
    $paper_crtr_unit = $paper_info["crtr_unit"];

    // 여분지 계산
    $extra_paper_amt_arr = PrdtDefaultInfo::EXTRA_PAPER_AMT;

    $bef_print_name     = $fb["inner1_bef_print_name"];
    $bef_add_print_name = $fb["inner1_bef_add_print_name"];
    $aft_print_name     = $fb["inner1_aft_print_name"];
    $aft_add_print_name = $fb["inner1_aft_add_print_name"];

    $extra_paper_amt = $extra_paper_amt_arr[$bef_print_name];
    if ($flag === 'N') {
        $extra_paper_amt += $extra_paper_amt_arr[$bef_add_print_name];
        $extra_paper_amt += $extra_paper_amt_arr[$aft_print_name];
        $extra_paper_amt += $extra_paper_amt_arr[$aft_add_print_name];
    }

    if ($paper_crtr_unit === 'R') {
        $extra_paper_amt /= 500;
    }

    // 표지 실제인쇄수량 검색
    $param["amt"]       = $amt;
    $param["pos_num"]   = $pos_num;
    $param["page_num"]  = $inner1_page;
    $param["amt_unit"]  = $amt_unit;
    $param["crtr_unit"] = $paper_crtr_unit;

    $real_paper_amt = $commonUtil->getPaperRealPrintAmt($param);
    $calc_paper_amt = $real_paper_amt + $extra_paper_amt;
    //$calc_paper_amt = ceil($calc_paper_amt);

    // 종이 가격 계산
    unset($param);
    $param["sell_site"] = $sell_site;
    $param["mpcode"]    = $paper_info["mpcode"];

    $inner1_paper_price  = $dao->selectPaperPrice($conn, $param);
    $inner1_paper_price  = intval($inner1_paper_price);
    $inner1_paper_price *= $calc_paper_amt;

    // 인쇄, 출력 가격 계산
    // 인쇄 도수명 맵핑코드로 변환
    $print_mpcode_arr = $frontUtil->getPrintMpcode($conn, $dao, $fb, "inner1");
    $print_bef_mpcode = $print_mpcode_arr["bef"];
    $print_aft_mpcode = $print_mpcode_arr["aft"];
    $print_bef_add_mpcode = $print_mpcode_arr["bef_add"];
    $print_aft_add_mpcode = $print_mpcode_arr["aft_add"];

    // 카테고리 분류코드, 규격 맵핑코드로
    // 출력 상품 맵핑코드 검색
    $param["cate_sortcode"] = $cate_sortcode;
    $param["mpcode"] = $stan_mpcode;
    $output_prdt_mpcode = $dao->selectPrdtOutputMpcode($conn, $param);

    if ($flag === 'N') {
        // 책자형 일 때 계산로직

        // 카테고리 분류코드, 인쇄 맵핑코드로
        // 총도수랑 출력판 수, 기준단위, 인쇄 상품 맵핑코드 검색
        $param["mpcode"] = $print_bef_mpcode;
        $print_bef_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $param["mpcode"] = $print_aft_mpcode;
        $print_aft_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $param["mpcode"] = $print_bef_add_mpcode;
        $print_bef_add_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $param["mpcode"] = $print_aft_add_mpcode;
        $print_aft_add_rs = $dao->selectPrdtPrintInfo($conn, $param);

        // 인쇄 가격 계산
        $print_bef_tot_tmpt = $print_bef_rs["tot_tmpt"];
        $print_aft_tot_tmpt = $print_aft_rs["tot_tmpt"];
        $print_bef_add_tot_tmpt = $print_bef_add_rs["tot_tmpt"];
        $print_aft_add_tot_tmpt = $print_aft_add_rs["tot_tmpt"];

        $print_crtr_unit = $print_bef_rs["crtr_unit"];
        $print_add_crtr_unit = $print_bef_add_rs["crtr_unit"];

        $print_bef_prdt_mpcode = $print_bef_rs["prdt_mpcode"];
        $print_aft_prdt_mpcode = $print_aft_rs["prdt_mpcode"];
        $print_bef_add_prdt_mpcode = $print_bef_add_rs["prdt_mpcode"];
        $print_aft_add_prdt_mpcode = $print_aft_add_rs["prdt_mpcode"];

        // 전면, 후면 도수 둘 다 0도가 아닐 때
        if ($inner1_page !== 0 &&
                $print_aft_tot_tmpt !== '0' &&
                $print_bef_tot_tmpt !== '0') {

            unset($param);
            // 인쇄대수별 종이수량 재계산용
            $param["amt"]             = $amt;
            $param["amt_unit"]        = $amt_unit;
            $param["paper_crtr_unit"] = $paper_crtr_unit;
            // 인쇄가격 계산용
            $param["pos_num"]         = $pos_num;
            $param["bef_tot_tmpt"]    = intval($print_bef_tot_tmpt);
            $param["aft_tot_tmpt"]    = intval($print_aft_tot_tmpt);
            $param["page_num"]        = $inner1_page;
            $param["crtr_unit"]       = $print_crtr_unit;
            $param["bef_mpcode"]      = $print_bef_prdt_mpcode;
            $param["aft_mpcode"]      = $print_aft_prdt_mpcode;
            $param["sell_site"]       = $sell_site;

            $inner1_print_price = calcBookletPrintPrice($conn,
                                                        $dao,
                                                        $temp);
        }
        // 추가도수 인쇄가격 계산용
        // 전면, 후면 추가도수 둘 다 0도가 아닐 때
        if ($inner1_page !== 0 &&
                $print_aft_add_tot_tmpt !== '0' &&
                $print_bef_add_tot_tmpt !== '0') {

            unset($param);
            // 인쇄대수별 종이수량 재계산용
            $param["amt"]             = $amt;
            $param["amt_unit"]        = $amt_unit;
            $param["paper_crtr_unit"] = $paper_crtr_unit;
            // 인쇄가격 계산용
            $param["pos_num"]         = $pos_num;
            $param["bef_tot_tmpt"]    = intval($print_bef_add_tot_tmpt);
            $param["aft_tot_tmpt"]    = intval($print_aft_add_tot_tmpt);
            $param["page_num"]        = $inner1_page;
            $param["crtr_unit"]       = $print_add_crtr_unit;
            $param["bef_mpcode"]      = $print_bef_add_prdt_mpcode;
            $param["aft_mpcode"]      = $print_aft_add_prdt_mpcode;
            $param["sell_site"]       = $sell_site;

            $inner1_print_price += calcBookletPrintPrice($conn,
                                                         $dao,
                                                         $temp);
        }

        // 출력 가격 계산
        if ($inner1_page !== 0) {
            $output_bef_board_amt = $print_bef_rs["output_board_amt"];
            $output_aft_board_amt = $print_aft_rs["output_board_amt"];
            $output_bef_add_board_amt = $print_bef_add_rs["output_board_amt"];
            $output_aft_add_board_amt = $print_aft_add_rs["output_board_amt"];

            unset($temp);
            $temp["pos_num"]   = $pos_num;
            $temp["page_num"]  = $page_num;
            $temp["board_amt"] = $output_bef_board_amt +
                                 $output_aft_board_amt +
                                 $output_bef_add_board_amt +
                                 $output_aft_add_board_amt;
            $temp["bef_tmpt"]  = $bef_print_tmpt;
            $temp["aft_tmpt"]  = $aft_print_tmpt;
            $temp["mpcode"]    = $output_prdt_mpcode;
            $temp["sell_site"] = $sell_site;

            $inner1_output_price = $this->calcBookletOutputPrice($conn,
                                                                 $dao,
                                                                 $temp);
        }
    } else {
        // 낱장형 일 때 가격계산 로직

        // 인쇄 가격 계산
        $param["mpcode"] = $print_bef_mpcode;
        $print_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $print_tot_tmpt = $print_rs["tot_tmpt"];
        $print_crtr_unit = $print_rs["crtr_unit"];
        $print_prdt_mpcode = $print_rs["prdt_mpcode"];

        $print_bef_mpcode = $print_mpcode_arr["bef"];
        unset($param);
        $param["tot_tmpt"]       = intval($print_tot_tmpt);
        $param["page_num"]       = 2;
        $param["crtr_unit"]      = $print_crtr_unit;
        $param["mpcode"]         = $print_prdt_mpcode;
        $param["real_paper_amt"] = $real_paper_amt;
        $param["sell_site"]      = $sell_site;

        $inner1_print_price = calcSheetPrintPrice($conn,
                                                  $dao,
                                                  $param);

        // 출력 가격 계산
        $output_board_amt = $print_rs["output_board_amt"];
        unset($param);
        $param["board_amt"] = $output_board_amt;
        $param["mpcode"]    = $output_prdt_mpcode;
        $param["sell_site"] = $sell_site;

        $inner1_output_price = calcOutputPrice($conn,
                                               $dao,
                                               $param);
    }

    $inner1_sum_price = $inner1_paper_price +
                        $inner1_print_price +
                        $inner1_output_price;
}

// 내지2
if ($dvs === "all" || $dvs === "inner2") {
    // 종이 기준단위, 맵핑코드 검색
    $paper_info = $dao->selectPrdtPaperInfo($conn,
                                            $inner2_paper_mpcode,
                                            "crtr_unit, mpcode");
    $paper_crtr_unit = $paper_info["crtr_unit"];

    // 여분지 계산
    $extra_paper_amt_arr = PrdtDefaultInfo::EXTRA_PAPER_AMT;

    $bef_print_name     = $fb["inner2_bef_print_name"];
    $bef_add_print_name = $fb["inner2_bef_add_print_name"];
    $aft_print_name     = $fb["inner2_aft_print_name"];
    $aft_add_print_name = $fb["inner2_aft_add_print_name"];

    $extra_paper_amt = $extra_paper_amt_arr[$bef_print_name];
    if ($flag === 'N') {
        $extra_paper_amt += $extra_paper_amt_arr[$bef_add_print_name];
        $extra_paper_amt += $extra_paper_amt_arr[$aft_print_name];
        $extra_paper_amt += $extra_paper_amt_arr[$aft_add_print_name];
    }

    if ($paper_crtr_unit === 'R') {
        $extra_paper_amt /= 500;
    }

    // 표지 실제인쇄수량 검색
    $param["amt"]       = $amt;
    $param["pos_num"]   = $pos_num;
    $param["page_num"]  = $inner2_page;
    $param["amt_unit"]  = $amt_unit;
    $param["crtr_unit"] = $paper_crtr_unit;

    $real_paper_amt = $commonUtil->getPaperRealPrintAmt($param);
    $calc_paper_amt = $real_paper_amt + $extra_paper_amt;
    //$calc_paper_amt = ceil($calc_paper_amt);

    // 종이 가격 계산
    unset($param);
    $param["sell_site"] = $sell_site;
    $param["mpcode"]    = $paper_info["mpcode"];

    $inner2_paper_price  = $dao->selectPaperPrice($conn, $param);
    $inner2_paper_price  = intval($inner2_paper_price);
    $inner2_paper_price *= $calc_paper_amt;

    // 인쇄, 출력 가격 계산
    // 인쇄 도수명 맵핑코드로 변환
    $print_mpcode_arr = $frontUtil->getPrintMpcode($conn, $dao, $fb, "inner2");
    $print_bef_mpcode = $print_mpcode_arr["bef"];
    $print_aft_mpcode = $print_mpcode_arr["aft"];
    $print_bef_add_mpcode = $print_mpcode_arr["bef_add"];
    $print_aft_add_mpcode = $print_mpcode_arr["aft_add"];

    // 카테고리 분류코드, 규격 맵핑코드로
    // 출력 상품 맵핑코드 검색
    $param["cate_sortcode"] = $cate_sortcode;
    $param["mpcode"] = $stan_mpcode;
    $output_prdt_mpcode = $dao->selectPrdtOutputMpcode($conn, $param);

    if ($flag === 'N') {
        // 책자형 일 때 계산로직

        // 카테고리 분류코드, 인쇄 맵핑코드로
        // 총도수랑 출력판 수, 기준단위, 인쇄 상품 맵핑코드 검색
        $param["mpcode"] = $print_bef_mpcode;
        $print_bef_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $param["mpcode"] = $print_aft_mpcode;
        $print_aft_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $param["mpcode"] = $print_bef_add_mpcode;
        $print_bef_add_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $param["mpcode"] = $print_aft_add_mpcode;
        $print_aft_add_rs = $dao->selectPrdtPrintInfo($conn, $param);

        // 인쇄 가격 계산
        $print_bef_tot_tmpt = $print_bef_rs["tot_tmpt"];
        $print_aft_tot_tmpt = $print_aft_rs["tot_tmpt"];
        $print_bef_add_tot_tmpt = $print_bef_add_rs["tot_tmpt"];
        $print_aft_add_tot_tmpt = $print_aft_add_rs["tot_tmpt"];

        $print_crtr_unit = $print_bef_rs["crtr_unit"];
        $print_add_crtr_unit = $print_bef_add_rs["crtr_unit"];

        $print_bef_prdt_mpcode = $print_bef_rs["prdt_mpcode"];
        $print_aft_prdt_mpcode = $print_aft_rs["prdt_mpcode"];
        $print_bef_add_prdt_mpcode = $print_bef_add_rs["prdt_mpcode"];
        $print_aft_add_prdt_mpcode = $print_aft_add_rs["prdt_mpcode"];

        // 전면, 후면 도수 둘 다 0도가 아닐 때
        if ($inner2_page !== 0 &&
                $print_aft_tot_tmpt !== '0' &&
                $print_bef_tot_tmpt !== '0') {

            unset($param);
            // 인쇄대수별 종이수량 재계산용
            $param["amt"]             = $amt;
            $param["amt_unit"]        = $amt_unit;
            $param["paper_crtr_unit"] = $paper_crtr_unit;
            // 인쇄가격 계산용
            $param["pos_num"]         = $pos_num;
            $param["bef_tot_tmpt"]    = intval($print_bef_tot_tmpt);
            $param["aft_tot_tmpt"]    = intval($print_aft_tot_tmpt);
            $param["page_num"]        = $inner2_page;
            $param["crtr_unit"]       = $print_crtr_unit;
            $param["bef_mpcode"]      = $print_bef_prdt_mpcode;
            $param["aft_mpcode"]      = $print_aft_prdt_mpcode;
            $param["sell_site"]       = $sell_site;

            $inner2_print_price = calcBookletPrintPrice($conn,
                                                        $dao,
                                                        $temp);
        }
        // 추가도수 인쇄가격 계산용
        // 전면, 후면 추가도수 둘 다 0도가 아닐 때
        if ($inner2_page !== 0 &&
                $print_aft_add_tot_tmpt !== '0' &&
                $print_bef_add_tot_tmpt !== '0') {

            unset($param);
            // 인쇄대수별 종이수량 재계산용
            $param["amt"]             = $amt;
            $param["amt_unit"]        = $amt_unit;
            $param["paper_crtr_unit"] = $paper_crtr_unit;
            // 인쇄가격 계산용
            $param["pos_num"]         = $pos_num;
            $param["bef_tot_tmpt"]    = intval($print_bef_add_tot_tmpt);
            $param["aft_tot_tmpt"]    = intval($print_aft_add_tot_tmpt);
            $param["page_num"]        = $inner2_page;
            $param["crtr_unit"]       = $print_add_crtr_unit;
            $param["bef_mpcode"]      = $print_bef_add_prdt_mpcode;
            $param["aft_mpcode"]      = $print_aft_add_prdt_mpcode;
            $param["sell_site"]       = $sell_site;

            $inner2_print_price += calcBookletPrintPrice($conn,
                                                         $dao,
                                                         $temp);
        }

        // 출력 가격 계산
        if ($inner2_page !== 0) {
            $output_bef_board_amt = $print_bef_rs["output_board_amt"];
            $output_aft_board_amt = $print_aft_rs["output_board_amt"];
            $output_bef_add_board_amt = $print_bef_add_rs["output_board_amt"];
            $output_aft_add_board_amt = $print_aft_add_rs["output_board_amt"];

            unset($temp);
            $temp["pos_num"]   = $pos_num;
            $temp["page_num"]  = $page_num;
            $temp["board_amt"] = $output_bef_board_amt +
                                 $output_aft_board_amt +
                                 $output_bef_add_board_amt +
                                 $output_aft_add_board_amt;
            $temp["bef_tmpt"]  = $bef_print_tmpt;
            $temp["aft_tmpt"]  = $aft_print_tmpt;
            $temp["mpcode"]    = $output_prdt_mpcode;
            $temp["sell_site"] = $sell_site;

            $inner2_output_price = $this->calcBookletOutputPrice($conn,
                                                                 $dao,
                                                                 $temp);
        }
    } else {
        // 낱장형 일 때 가격계산 로직

        // 인쇄 가격 계산
        $param["mpcode"] = $print_bef_mpcode;
        $print_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $print_tot_tmpt = $print_rs["tot_tmpt"];
        $print_crtr_unit = $print_rs["crtr_unit"];
        $print_prdt_mpcode = $print_rs["prdt_mpcode"];

        $print_bef_mpcode = $print_mpcode_arr["bef"];
        unset($param);
        $param["tot_tmpt"]       = intval($print_tot_tmpt);
        $param["page_num"]       = 2;
        $param["crtr_unit"]      = $print_crtr_unit;
        $param["mpcode"]         = $print_prdt_mpcode;
        $param["real_paper_amt"] = $real_paper_amt;
        $param["sell_site"]      = $sell_site;

        $inner2_print_price = calcSheetPrintPrice($conn,
                                                  $dao,
                                                  $param);

        // 출력 가격 계산
        $output_board_amt = $print_rs["output_board_amt"];
        unset($param);
        $param["board_amt"] = $output_board_amt;
        $param["mpcode"]    = $output_prdt_mpcode;
        $param["sell_site"] = $sell_site;

        $inner2_output_price = calcOutputPrice($conn,
                                               $dao,
                                               $param);
    }

    $inner2_sum_price = $inner2_paper_price +
                        $inner2_print_price +
                        $inner2_output_price;
}

// 내지3
if ($dvs === "all" || $dvs === "inner3") {
    // 종이 기준단위, 맵핑코드 검색
    $paper_info = $dao->selectPrdtPaperInfo($conn,
                                            $inner3_paper_mpcode,
                                            "crtr_unit, mpcode");
    $paper_crtr_unit = $paper_info["crtr_unit"];

    // 여분지 계산
    $extra_paper_amt_arr = PrdtDefaultInfo::EXTRA_PAPER_AMT;

    $bef_print_name     = $fb["inner3_bef_print_name"];
    $bef_add_print_name = $fb["inner3_bef_add_print_name"];
    $aft_print_name     = $fb["inner3_aft_print_name"];
    $aft_add_print_name = $fb["inner3_aft_add_print_name"];

    $extra_paper_amt = $extra_paper_amt_arr[$bef_print_name];
    if ($flag === 'N') {
        $extra_paper_amt += $extra_paper_amt_arr[$bef_add_print_name];
        $extra_paper_amt += $extra_paper_amt_arr[$aft_print_name];
        $extra_paper_amt += $extra_paper_amt_arr[$aft_add_print_name];
    }

    if ($paper_crtr_unit === 'R') {
        $extra_paper_amt /= 500;
    }

    // 표지 실제인쇄수량 검색
    $param["amt"]       = $amt;
    $param["pos_num"]   = $pos_num;
    $param["page_num"]  = $inner3_page;
    $param["amt_unit"]  = $amt_unit;
    $param["crtr_unit"] = $paper_crtr_unit;

    $real_paper_amt = $commonUtil->getPaperRealPrintAmt($param);
    $calc_paper_amt = $real_paper_amt + $extra_paper_amt;
    //$calc_paper_amt = ceil($calc_paper_amt);

    // 종이 가격 계산
    unset($param);
    $param["sell_site"] = $sell_site;
    $param["mpcode"]    = $paper_info["mpcode"];

    $inner3_paper_price  = $dao->selectPaperPrice($conn, $param);
    $inner3_paper_price  = intval($inner3_paper_price);
    $inner3_paper_price *= $calc_paper_amt;

    // 인쇄, 출력 가격 계산
    // 인쇄 도수명 맵핑코드로 변환
    $print_mpcode_arr = $frontUtil->getPrintMpcode($conn, $dao, $fb, "inner3");
    $print_bef_mpcode = $print_mpcode_arr["bef"];
    $print_aft_mpcode = $print_mpcode_arr["aft"];
    $print_bef_add_mpcode = $print_mpcode_arr["bef_add"];
    $print_aft_add_mpcode = $print_mpcode_arr["aft_add"];

    // 카테고리 분류코드, 규격 맵핑코드로
    // 출력 상품 맵핑코드 검색
    $param["cate_sortcode"] = $cate_sortcode;
    $param["mpcode"] = $stan_mpcode;
    $output_prdt_mpcode = $dao->selectPrdtOutputMpcode($conn, $param);

    if ($flag === 'N') {
        // 책자형 일 때 계산로직

        // 카테고리 분류코드, 인쇄 맵핑코드로
        // 총도수랑 출력판 수, 기준단위, 인쇄 상품 맵핑코드 검색
        $param["mpcode"] = $print_bef_mpcode;
        $print_bef_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $param["mpcode"] = $print_aft_mpcode;
        $print_aft_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $param["mpcode"] = $print_bef_add_mpcode;
        $print_bef_add_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $param["mpcode"] = $print_aft_add_mpcode;
        $print_aft_add_rs = $dao->selectPrdtPrintInfo($conn, $param);

        // 인쇄 가격 계산
        $print_bef_tot_tmpt = $print_bef_rs["tot_tmpt"];
        $print_aft_tot_tmpt = $print_aft_rs["tot_tmpt"];
        $print_bef_add_tot_tmpt = $print_bef_add_rs["tot_tmpt"];
        $print_aft_add_tot_tmpt = $print_aft_add_rs["tot_tmpt"];

        $print_crtr_unit = $print_bef_rs["crtr_unit"];
        $print_add_crtr_unit = $print_bef_add_rs["crtr_unit"];

        $print_bef_prdt_mpcode = $print_bef_rs["prdt_mpcode"];
        $print_aft_prdt_mpcode = $print_aft_rs["prdt_mpcode"];
        $print_bef_add_prdt_mpcode = $print_bef_add_rs["prdt_mpcode"];
        $print_aft_add_prdt_mpcode = $print_aft_add_rs["prdt_mpcode"];

        // 전면, 후면 도수 둘 다 0도가 아닐 때
        if ($inner3_page !== 0 &&
                $print_aft_tot_tmpt !== '0' &&
                $print_bef_tot_tmpt !== '0') {

            unset($param);
            // 인쇄대수별 종이수량 재계산용
            $param["amt"]             = $amt;
            $param["amt_unit"]        = $amt_unit;
            $param["paper_crtr_unit"] = $paper_crtr_unit;
            // 인쇄가격 계산용
            $param["pos_num"]         = $pos_num;
            $param["bef_tot_tmpt"]    = intval($print_bef_tot_tmpt);
            $param["aft_tot_tmpt"]    = intval($print_aft_tot_tmpt);
            $param["page_num"]        = $inner3_page;
            $param["crtr_unit"]       = $print_crtr_unit;
            $param["bef_mpcode"]      = $print_bef_prdt_mpcode;
            $param["aft_mpcode"]      = $print_aft_prdt_mpcode;
            $param["sell_site"]       = $sell_site;

            $inner3_print_price = calcBookletPrintPrice($conn,
                                                        $dao,
                                                        $temp);
        }
        // 추가도수 인쇄가격 계산용
        // 전면, 후면 추가도수 둘 다 0도가 아닐 때
        if ($inner3_page !== 0 &&
                $print_aft_add_tot_tmpt !== '0' &&
                $print_bef_add_tot_tmpt !== '0') {

            unset($param);
            // 인쇄대수별 종이수량 재계산용
            $param["amt"]             = $amt;
            $param["amt_unit"]        = $amt_unit;
            $param["paper_crtr_unit"] = $paper_crtr_unit;
            // 인쇄가격 계산용
            $param["pos_num"]         = $pos_num;
            $param["bef_tot_tmpt"]    = intval($print_bef_add_tot_tmpt);
            $param["aft_tot_tmpt"]    = intval($print_aft_add_tot_tmpt);
            $param["page_num"]        = $inner3_page;
            $param["crtr_unit"]       = $print_add_crtr_unit;
            $param["bef_mpcode"]      = $print_bef_add_prdt_mpcode;
            $param["aft_mpcode"]      = $print_aft_add_prdt_mpcode;
            $param["sell_site"]       = $sell_site;

            $inner3_print_price += calcBookletPrintPrice($conn,
                                                         $dao,
                                                         $temp);
        }

        // 출력 가격 계산
        if ($inner3_page !== 0) {
            $output_bef_board_amt = $print_bef_rs["output_board_amt"];
            $output_aft_board_amt = $print_aft_rs["output_board_amt"];
            $output_bef_add_board_amt = $print_bef_add_rs["output_board_amt"];
            $output_aft_add_board_amt = $print_aft_add_rs["output_board_amt"];

            unset($temp);
            $temp["pos_num"]   = $pos_num;
            $temp["page_num"]  = $page_num;
            $temp["board_amt"] = $output_bef_board_amt +
                                 $output_aft_board_amt +
                                 $output_bef_add_board_amt +
                                 $output_aft_add_board_amt;
            $temp["bef_tmpt"]  = $bef_print_tmpt;
            $temp["aft_tmpt"]  = $aft_print_tmpt;
            $temp["mpcode"]    = $output_prdt_mpcode;
            $temp["sell_site"] = $sell_site;

            $inner3_output_price = $this->calcBookletOutputPrice($conn,
                                                                 $dao,
                                                                 $temp);
        }
    } else {
        // 낱장형 일 때 가격계산 로직

        // 인쇄 가격 계산
        $param["mpcode"] = $print_bef_mpcode;
        $print_rs = $dao->selectPrdtPrintInfo($conn, $param);

        $print_tot_tmpt = $print_rs["tot_tmpt"];
        $print_crtr_unit = $print_rs["crtr_unit"];
        $print_prdt_mpcode = $print_rs["prdt_mpcode"];

        $print_bef_mpcode = $print_mpcode_arr["bef"];
        unset($param);
        $param["tot_tmpt"]       = intval($print_tot_tmpt);
        $param["page_num"]       = 2;
        $param["crtr_unit"]      = $print_crtr_unit;
        $param["mpcode"]         = $print_prdt_mpcode;
        $param["real_paper_amt"] = $real_paper_amt;
        $param["sell_site"]      = $sell_site;

        $inner3_print_price = calcSheetPrintPrice($conn,
                                                  $dao,
                                                  $param);

        // 출력 가격 계산
        $output_board_amt = $print_rs["output_board_amt"];
        unset($param);
        $param["board_amt"] = $output_board_amt;
        $param["mpcode"]    = $output_prdt_mpcode;
        $param["sell_site"] = $sell_site;

        $inner3_output_price = calcOutputPrice($conn,
                                               $dao,
                                               $param);
    }

    $inner3_sum_price = $inner3_paper_price +
                        $inner3_print_price +
                        $inner3_output_price;
}

$inner  = '{';
$inner .= " \"paper\"  : \"%s\",";
$inner .= " \"print\"  : \"%s\",";
$inner .= " \"output\" : \"%s\",";
$inner .= " \"price\"  : \"%s\"";
$inner .= '}';

$outer  = '{';
if ($dvs ==="all") {
    $outer .= " \"cover\"   : %s,";
    $outer .= " \"inner1\"  : %s,";
    $outer .= " \"inner2\"  : %s,";
    $outer .= " \"inner3\"  : %s";

    $outer  = sprintf($outer, $inner, $inner, $inner, $inner);
    $outer  = sprintf($outer, $cover_paper_price
                            , $cover_print_price
                            , $cover_output_price
                            , $cover_sum_price
                            , $inner1_paper_price
                            , $inner1_print_price
                            , $inner1_output_price
                            , $inner1_sum_price
                            , $inner2_paper_price
                            , $inner2_print_price
                            , $inner2_output_price
                            , $inner2_sum_price
                            , $inner3_paper_price
                            , $inner3_print_price
                            , $inner3_output_price
                            , $inner3_sum_price);
} else if ($dvs === "cover") {
    $outer .= " \"cover\"   : %s";

    $outer  = sprintf($outer, $inner);
    $outer  = sprintf($outer, $cover_paper_price
                            , $cover_print_price
                            , $cover_output_price
                            , $cover_sum_price);
} else if ($dvs === "inner1") {
    $outer .= " \"inner1\"  : %s";

    $outer  = sprintf($outer, $inner);
    $outer  = sprintf($outer, $inner1_paper_price
                            , $inner1_print_price
                            , $inner1_output_price
                            , $inner1_sum_price);
} else if ($dvs === "inner2") {
    $outer .= " \"inner2\"  : %s";

    $outer  = sprintf($outer, $inner);
    $outer  = sprintf($outer, $inner2_paper_price
                            , $inner2_print_price
                            , $inner2_output_price
                            , $inner2_sum_price);
} else if ($dvs === "inner3") {
    $outer .= " \"inner3\"  : %s";

    $outer  = sprintf($outer, $inner);
    $outer  = sprintf($outer, $inner3_paper_price
                            , $inner3_print_price
                            , $inner3_output_price
                            , $inner3_sum_price);
}
$outer .= '}';

echo $outer;

$conn->Close();

/******************************************************************************
 * 계산로직 함수 부분
 * nimda/engine/common/CalcPriceUtil.php 에 존재하는 함수 복사
 * 수정될 경우 동시 수정 필요
 *****************************************************************************/

/**
 * @brief 낱장형 인쇄 가격 계산
 *
 * @detail $info["tot_tmpt"] = 총도수
 * $info["page_num"] = 페이지수
 * $info["mpcode"] = 인쇄 맵핑코드
 * $info["crtr_unit"] = 기준 단위
 * $info["real_paper_amt"] = 종이 실제 수량
 * $info["sell_site"] = 판매채널
 *
 * @detail 낱장형 인쇄물에서 인쇄 대수는 1대로 계산한다
 *
 * @param $conn  = connection identifier
 * @param $dao   = 가격 검색용 dao
 * @param $param = 가격검색에 필요한 정보배열
 *
 * @return 인쇄 가격
 */
function calcSheetPrintPrice($conn, $dao, $info) {
    $page_num       = doubleval($info["page_num"]);
    $crtr_unit      = $info["crtr_unit"];
    $real_paper_amt = $info["real_paper_amt"];

    // 종이 수량단위와 인쇄 수량단위가 틀릴경우
    if ($crtr_unit !== "R") {
        $real_paper_amt *= 500;
    }

    $param = array();
    $param["sell_site"] = $info["sell_site"];
    $param["mpcode"]    = $info["mpcode"];
    $param["amt"]       = $real_paper_amt;
    $param["tot_tmpt"]  = $info["tot_tmpt"];
    $param["page_num"]  = $page_num;

    return getPrintSellPrice($conn, $dao, $param);
}

/**
 * @brief 책자형 인쇄 가격 계산
 *
 * @detail $info["aft_tot_tmpt"] = 전면 총도수
 * $info["bef_tot_tmpt"] = 후면 총도수
 * $info["page_num"] = 페이지수
 * $info["aft_mpcode"] = 전면 인쇄 맵핑코드
 * $info["bef_mpcode"] = 후면 인쇄 맵핑코드
 * $info["crtr_unit"] = 기준 단위
 * $info["real_paper_amt"] = 종이 실제 수량
 * $info["sell_site"] = 판매채널
 *
 * @detail 책자형 인쇄는 낱장형 인쇄와 다르게 출력과 똑같이 
 * 인쇄 대수가 적용된다.
 *
 * 홍각기 / 돈땡에 따라서 페이지가 분할되고 분할된 페이지에 따른
 * 종이 수량을 계산해서 종이 가격을 별도로 계산해서 합친다.
 *
 * @param $conn  = connection identifier
 * @param $dao   = 가격 검색용 dao
 * @param $param = 가격검색에 필요한 정보배열
 *
 * @return 인쇄 가격
 */
function calcBookletPrintPrice($conn, $dao, $info) {
    $pos_num   = doubleval($info["pos_num"]);
    $page_num  = doubleval($info["page_num"]);
    $crtr_unit = $info["crtr_unit"];

    // 인쇄 대수로부터 대수별 페이지 계산
    // 인쇄 대수 계산
    $calc_info = getMachineCount($page_num, $pos_num);
    $calc_info["pos_num"] = $pos_num;

    $hong_count = $calc_info["hong"];

    // 대수별 페이지 계산
    $calc_info = getPrintBookletPageNum($calc_info);

    $param = array();
    $param["pos_num"]   = $pos_num;
    $param["amt"]       = $info["amt"];
    $param["amt_unit"]  = $info["amt_unit"];
    $param["crtr_unit"] = $info["paper_crtr_unit"];

    // 홍각기 종이수량
    $param["page_num"] = $calc_info["hong_page_num"];
    $hong_paper_amt = getPaperRealPrintAmt($param);
    // 1/2 돈땡 종이수량
    $param["page_num"] = $calc_info["don_h_page_num"];
    $don_h_paper_amt = getPaperRealPrintAmt($param);
    // 1/4 돈땡 종이수량
    $param["page_num"] = $calc_info["don_q_page_num"];
    $don_q_paper_amt = getPaperRealPrintAmt($param);
    // 1/8 돈땡 종이수량
    $param["page_num"] = $calc_info["don_e_page_num"];
    $don_e_paper_amt = getPaperRealPrintAmt($param);

    // 종이 수량단위와 인쇄 수량단위가 틀릴경우
    if ($crtr_unit !== "R") {
        $hong_paper_amt  *= 500;
        $don_h_paper_amt *= 500;
        $don_q_paper_amt *= 500;
        $don_e_paper_amt *= 500;
    }

    unset($calc_info);
    unset($param["amt_unit"]);
    unset($param["crtr_unit"]);

    $param["sell_site"] = $info["sell_site"];

    // 전면도수 가격
    $param["tot_tmpt"] = $info["bef_tot_tmpt"];
    $param["mpcode"]   = $info["bef_mpcode"];
    // 홍각기 인쇄가격
    $param["amt"] = $hong_paper_amt;
    $bef_hong_price  = getPrintSellPrice($conn, $dao, $param);
    $bef_hong_price *= $hong_count;
    // 1/2 돈땡 인쇄가격
    $param["amt"] = $don_h_paper_amt;
    $bef_don_h_price = getPrintSellPrice($conn, $dao, $param);
    // 1/4 돈땡 인쇄가격
    $param["amt"] = $don_q_paper_amt;
    $bef_don_q_price = getPrintSellPrice($conn, $dao, $param);
    // 1/8 돈땡 인쇄가격
    $param["amt"] = $don_e_paper_amt;
    $bef_don_e_price = getPrintSellPrice($conn, $dao, $param);

    $bef_print_price_sum = $bef_hong_price +
                           $bef_don_h_price +
                           $bef_don_q_price +
                           $bef_don_e_price;

    // 후면도수 가격
    $param["tot_tmpt"] = $info["aft_tot_tmpt"];
    $param["mpcode"]   = $info["aft_mpcode"];
    // 홍각기 인쇄가격
    $param["amt"] = $hong_paper_amt;
    $aft_hong_price  = getPrintSellPrice($conn, $dao, $param);
    $aft_hong_price *= $hong_count;
    // 1/2 돈땡 인쇄가격
    $param["amt"] = $don_h_paper_amt;
    $aft_don_h_price = getPrintSellPrice($conn, $dao, $param);
    // 1/4 돈땡 인쇄가격
    $param["amt"] = $don_q_paper_amt;
    $aft_don_q_price = getPrintSellPrice($conn, $dao, $param);
    // 1/8 돈땡 인쇄가격
    $param["amt"] = $don_e_paper_amt;
    $aft_don_e_price = getPrintSellPrice($conn, $dao, $param);

    $aft_print_price_sum = $aft_hong_price +
                           $aft_don_h_price +
                           $aft_don_q_price +
                           $aft_don_e_price;

    $ret = $bef_print_price_sum + $aft_print_price_sum;

    return $ret;
}

/**
 * @brief 인쇄 책자형 홍각기/돈땡별 페이지수 계산
 *
 * @detail $info["hong"] = 홍각기 대수
 * $info["don"] = 돈땡 대수
 * $info["pos_num"] = 자리수
 *
 * @param $info = 정보배열
 *
 * @return 인쇄 가격
 */
function getPrintBookletPageNum($info) {
    $hong_count = $info["hong"];
    $don_count  = $info["don"];
    $pos_num    = $info["pos_num"];

    // 기본 페이지수
    $def_page_num = $pos_num * 2;

    // 홍각기 페이지수
    $hong_page_num = $hong_count * $def_page_num;
    // 1/2 돈땡 페이지수
    $don_h_page_num = 0;
    // 1/4 돈땡 페이지수
    $don_q_page_num = 0;
    // 1/8 돈땡 페이지수
    $don_e_page_num = 0;

    switch ($don_count) {
        case HALF_QUARTER_EIGHTH :
            // 1/2(8p), 1/4(4p), 1/8(2p) 돈땡
            $don_h_page_num = $def_page_num / 2;
            $don_q_page_num = $def_page_num / 4;
            $don_e_page_num = $def_page_num / 8;

            break;
        case HALF_QUARTER :
            // 1/2, 1/4 돈땡
            $don_h_page_num = $def_page_num / 2;
            $don_q_page_num = $def_page_num / 4;

            break;
        case HALF_EIGHTH :
            // 1/2, 1/8 돈땡
            $don_h_page_num = $def_page_num / 2;
            $don_e_page_num = $def_page_num / 8;

            break;
        case HALF :
            // 1/2 돈땡
            $don_h_page_num = $def_page_num / 2;

            break;
        case QUARTER_EIGHTH :
            // 1/4, 1/8 돈땡
            $don_q_page_num = $def_page_num / 4;
            $don_e_page_num = $def_page_num / 8;

            break;
        case QUARTER :
            // 1/4 돈땡
            $don_q_page_num = $def_page_num / 4;

            break;
        case EIGHTH :
            // 1/8 돈땡
            $don_e_page_num = $def_page_num / 8;

            break;
    }

    return array(
        "hong_page_num"  => $hong_page_num,
        "don_h_page_num" => $don_h_page_num,
        "don_q_page_num" => $don_q_page_num,
        "don_e_page_num" => $don_e_page_num
    );
}

/**
 * @brief 인쇄 가격 계산
 *
 * @detail 실제 계산 공식은 아래와 같다
 *  - 인쇄 기계 대수 공식 =
 *      소수점 올림{$사용자가 입력한 페이지 수$ / ($규격의 자리수$ * 2)}
 *  - 인쇄 가격 공식 =
 *      ($전체 도수$ * $인쇄 대수$ * 소수점 올림{$인쇄 연수$}) * $인쇄 단가$
 *
 * @param $conn  = connection identifier
 * @param $dao   = 가격 검색용 dao
 * @param $param = 가격검색에 필요한 정보배열
 *
 * @return 인쇄 가격
 */
function getPrintSellPrice($conn, $dao, $param) {
    $page_num = $param["page_num"];
    $tot_tmpt = $param["tot_tmpt"];
    $amt      = $param["amt"];
    
    if ($amt == 0) {
        return 0;
    }

    $sell_price = $dao->selectPrintPrice($conn, $param);
    
    $price = $tot_tmpt * ceil($amt) * $sell_price;

    return intval($price);
}

/**
 * @brief 낱장형 출력 가격 계산
 *
 * @detail $info["page_num"] = 페이지수
 * $info["board_amt"] = 출력판 수량
 * $info["aft_tmpt"] = 전면도수
 * $info["bef_tmpt"] = 후면도수
 * $info["mpcode"] = 여분지 수량
 * $info["sell_site"] = 판매채널
 *
 * @param $conn  = connection identifier
 * @param $dao   = 가격 검색용 dao
 * @param $info = 가격검색에 필요한 정보배열
 *
 * @return 출력 가격
 */
function calcOutputPrice($conn, $dao, $info) {
    $sell_site = $info["sell_site"];

    $board_amt = $info["board_amt"];
    $mpcode    = $info["mpcode"];

    $param = array();
    $param["sell_site"] = $sell_site;
    $param["mpcode"]    = $mpcode;

    $sell_price = $dao->selectOutputPrice($conn, $param);

    $price = $board_amt * intval($sell_price);

    return $price;
}

/**
 * @brief 출력 가격 계산
 *
 * @detail $info["page_num"] = 페이지수
 * $info["board_amt"] = 출력판 수량
 * $info["aft_tmpt"] = 전면도수
 * $info["bef_tmpt"] = 후면도수
 * $info["mpcode"] = 여분지 수량
 * $info["sell_site"] = 판매채널
 *
 * @detail 실제 계산 공식은 아래와 같다
 *  - 출력 기계 대수 공식 =
 *      $사용자가 입력한 페이지 수$ / ($규격의 자리수$ * 2)
 *  - 출력판수 산출 공식 =
 *      ($홍각기 대 수$ * $판수$) + ($돈땡 대 수$ + ($판수$ / 2))
 *  - 인쇄 가격 공식 =
 *      $전체 출력판 수$ * $출력판당 가격$
 *
 * @detail 홍각기/돈땡 구분법은 전/후면 도수로 구분한다
 *  - 홍각기 => 전면도수 != 후면도수
 *  - 돈땡   => 전면도수 == 후면도수
 *
 * @param $conn  = connection identifier
 * @param $dao   = 가격 검색용 dao
 * @param $info = 가격검색에 필요한 정보배열
 *
 * @return 출력 가격
 */
function calcBookletOutputPrice($conn, $dao, $info) {
    $sell_site = $info["sell_site"];

    $pos_num   = doubleval($info["pos_num"]);
    $page_num  = doubleval($info["page_num"]);
    $board_amt = $info["board_amt"];
    $bef_tmpt  = $info["bef_tmpt"];
    $aft_tmpt  = $info["aft_tmpt"];
    $mpcode    = $info["mpcode"];

    $count_arr  = getMachineCount($page_num, $pos_num);
    $hong_count = $count_arr["hong"];
    $don_count  = $count_arr["don"];

    switch ($don_count) {
        case HALF_QUARTER_EIGHTH :
            // 1/2, 1/4, 1/8 돈땡
            $don_count = 3;
            break;
        case HALF_QUARTER :
            // 1/2, 1/4 돈땡
            $don_count = 2;
            break;
        case HALF_EIGHTH :
            // 1/2, 1/8 돈땡
            $don_count = 2;
            break;
        case HALF :
            // 1/2 돈땡
            $don_count = 1;
            break;
        case QUARTER_EIGHTH :
            // 1/4, 1/8 돈땡
            $don_count = 2;
            break;
        case QUARTER :
            // 1/4 돈땡
            $don_count = 1;
            break;
        case EIGHTH :
            // 1/8 돈땡
            $don_count = 1;
            break;
    }

    // 돈땡 판수 계산용, 각 도수가 1도보다 커야 돈땡 가능
    if (('1' < $bef_tmpt) && 
            ('1' < $aft_tmpt) &&
            ($bef_tmpt === $aft_tmpt)) {
        $board_count = $don_count * ($board_amt >> 1);
    } else {
        if ((('1' === $bef_tmpt) ||
                ('1' === $aft_tmpt)) &&
                $don_count !== 0) {
            $don_count = 0;
            $hong_count++;
        }

        $board_count = $don_count * $board_amt;
    }


    $board_count += $hong_count * $board_amt;

    $param = array();
    $param["sell_site"] = $sell_site;
    $param["mpcode"]    = $mpcode;

    $sell_price = $dao->selectOutputPrice($conn, $param);

    $price = $board_count * intval($sell_price);

    return $price;
}

/**
 * @brief 인쇄/출력 기계 대수 반환
 *
 * @param $page_num = 페이지 수
 * @param $pos_num  = 자리 수
 *
 * @return $ret["hone"] = 홍각기 상수값
 * $ret["don"] = 돈땡 상수값
 */
function getMachineCount($page_num, $pos_num) {
    $count = strval($page_num / ($pos_num * 2.0));
    $count = explode('.', $count);
    // 홍각기 대수
    $hong_count = intval($count[0]);
    // 돈땡 대수
    $don_count = intval($count[1]);

    return array(
        "hong" => $hong_count,
        "don"  => $don_count
    );
}
?>
