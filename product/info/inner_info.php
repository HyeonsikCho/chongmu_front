<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");

/*
 * $price_info_arr, $param은 product/info/common_info.php에 있음
 */
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_lib/CommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/prdt_default_info.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/product_default_sel.php");

$commonUtil = new CommonUtil();

//1 기본 선택 정보 배열
$default_sel_arr = ProductDefaultSel::DEFAULT_SEL[$sortcode_b];
$param["cate_sortcode"] = $sortcode_b;

$cRs = $dao->selectCpoint($conn,$param);
$template->reg("c_rate",$cRs->fields['c_rate']);
$template->reg("c_user_rate",$cRs->fields['c_user_rate']);

//2 사이즈 정보 생성
$size = $dao->selectCateSizeHtml($conn, $sortcode_b, $price_info_arr, true);
$template->reg("size", $size);

//2-1 사이즈 자리수 정보 생성
$pos_num = PrdtDefaultInfo::POSITION_NUMBER[$sortcode_b];
$pos_num = $pos_num[$default_sel_arr["size"]];
$template->reg("pos_num", $pos_num);

//3 재단, 작업사이즈간 차이 정보 생성
$size_gap = " _gap%s";
$val = ProductInfoClass::SIZE_GAP[$sortcode_b];
$template->reg("size_gap", sprintf($size_gap, $val));

//4-1 제본 depth1 정보 생성
$param["cate_sortcode"] = $sortcode_b;

$binding_depth1 = $dao->selectBindingHtml($conn,
                                          "depth1",
                                          $param,
                                          $price_info_arr);
$template->reg("binding_depth1", $binding_depth1);

//4-2 제본 depth2 정보 생성
$param["depth1"] = $price_info_arr["binding_depth1"];

$binding_depth2 = $dao->selectBindingHtml($conn,
                                          "depth2",
                                          $param,
                                          $price_info_arr);

unset($param);
$template->reg("binding_depth2", $binding_depth2);

//5 수량 정보 생성
$amt_arr = PrdtDefaultInfo::AMT[$sortcode_b];
$amt_arr_count = count($amt_arr);
$amt = "";
$amt_default = $default_sel_arr["amt"];
for ($i = 0; $i < $amt_arr_count; $i++) {
    $val = $amt_arr[$i];
    $attr = "";
    if ($val === $amt_default) {
        $attr = "selected=\"selected\"";
        $price_info_arr["amt"] = $val;
    }
    $amt .= option($val, number_format(doubleval($val)), $attr);
}
$template->reg("amt", $amt);

//6 종이 정보 생성
$paper = $dao->selectCatePaperHtml($conn, $sortcode_b, $price_info_arr);
$template->reg("paper", $paper["info"]);
$template->reg("paper_sort", $paper["sort"]);

//7 도수 정보 생성
$param["cate_sortcode"] = $sortcode_b;

$print_tmpt = $dao->selectCatePrintTmptHtml($conn,
                                            $param,
                                            $price_info_arr);
$print_tmpt = $print_tmpt["단면"] . $print_tmpt["양면"];
$template->reg("print_tmpt", $print_tmpt);
unset($param);

//9 내지 페이지 정보 생성
$page_arr = PrdtDefaultInfo::PAGE_INFO[$sortcode_b]["내지"];
$page_arr_count = count($page_arr);
$page     = "";
$page_default = $default_sel_arr["inner"];
for ($i = 0; $i < $page_arr_count; $i++) {
    $val = $page_arr[$i];
    $dvs = number_format(doubleval($val)) . 'p';
    $attr = "";
    if ($dvs === $page_default) {
        $attr = "selected=\"selected\"";
        $price_info_arr["page"]["내지"]        = $val;
        $price_info_arr["page_detail"]["내지"] = '';
    }
    $page .= option($val, $dvs, $attr);
}
$template->reg("page_inner"    , $page);

//10 카테고리 독판여부, 수량단위 검색
$cate_info_arr = $dao->selectCateInfo($conn, $sortcode_b);
$mono_dvs = $cate_info_arr["mono_dvs"];
$amt_unit = $cate_info_arr["amt_unit"];
$tmpt_dvs = $cate_info_arr["tmpt_dvs"];
unset($cate_info_arr);
$template->reg("mono_dvs", makeMonoDvsOption($mono_dvs));
$template->reg("amt_unit", $amt_unit);
$template->reg("tmpt_dvs", $tmpt_dvs);

//12 지질느낌 검색
$paper_sense = $dao->selectPaperDscr($conn, $price_info_arr["paper_mpcode"]);
$template->reg("paper_sense", $paper_sense);

//13 인쇄방식 정보 생성
$print_purp = $dao->selectCatePrintPurpHtml($conn, $sortcode_b);
$template->reg("print_purp", $print_purp);

//14-1 옵션 정보 생성
$opt = $dao->selectCateOptHtml($conn, $sortcode_b);

$template->reg("opt", $opt["html"]);

//14-2 옵션 가격 레이어 생성
$add_opt = $opt["info_arr"]["name"];
$add_opt = $dao->parameterArrayEscape($conn, $add_opt);
$add_opt = $frontUtil->arr2delimStr($add_opt);

$param["cate_sortcode"] = $sortcode_b;
$param["opt_name"]      = $add_opt;
$param["opt_idx"]       = $opt["info_arr"]["idx"];

$add_opt = $dao->selectCateAddOptInfoHtml($conn, $param);
unset($param);
$template->reg("add_opt", $add_opt);

//15-1 후공정 체크박스 생성
$except_arr = array("제본" => true);

$after = $dao->selectCateAfterHtml($conn, $sortcode_b, $except_arr);
$template->reg("after", $after["html"]);

//8 기본 후공정 내역에 표시할 정보 생성
$template->reg("basic_after", '');
if (empty($after["info_arr"]["basic"]) === false) {
    $basic_after = $after["info_arr"]["basic"];
    $basic_after = $frontUtil->arr2delimStr($basic_after, '|');
    $template->reg("basic_after", $basic_after);
}

//15-2 추가 후공정 가격 레이어 생성
$add_after = $after["info_arr"]["add"];
$add_after = $dao->parameterArrayEscape($conn, $add_after);
$add_after = $frontUtil->arr2delimStr($add_after);

$param["cate_sortcode"] = $sortcode_b;
$param["after_name"]    = $add_after;
$add_after = $dao->selectCateAddAfterInfoHtml($conn, $param);
unset($param);
$template->reg("add_after", $add_after);

//16-1 가격 테이블 검색
$mono_dvs = ($mono_dvs === '1' || $mono_dvs === '2') ? '0' : '1';
$price_tb = $dao->selectPriceTableName($conn, $mono_dvs, $sell_site);

//16-2 가격 검색용 공통 검색파라미터 생성
$param["table_name"]           = $price_tb;
$param["cate_sortcode"]        = $price_info_arr["cate_sortcode"];
$param["paper_mpcode"]         = $price_info_arr["paper_mpcode"];
$param["bef_print_mpcode"]     = $price_info_arr["print_mpcode"];
$param["bef_add_print_mpcode"] = '0';
$param["aft_print_mpcode"]     = '0';
$param["aft_add_print_mpcode"] = '0';
$param["stan_mpcode"]          = $price_info_arr["stan_mpcode"];
$param["amt"]                  = $price_info_arr["amt"];

//17 내지 가격 검색
$param["page"]        = $price_info_arr["page"]["내지"];
$param["page_dvs"]    = "내지";
$param["page_detail"] = $price_info_arr["page_detail"]["내지"];
$param["affil"]       = $price_info_arr["affil"];

$price_rs = $dao->selectPrdtCalcPrice($conn, $param);

$inner_paper_price  = $frontUtil->ceilVal($price_rs["paper_price"]);
$inner_print_price  = $frontUtil->ceilVal($price_rs["print_price"]);
$inner_output_price = $frontUtil->ceilVal($price_rs["output_price"]);
$inner_sum_price    = $frontUtil->ceilVal($price_rs["sum_price"]);

$template->reg("paper_inner_price" , $inner_paper_price);
$template->reg("print_inner_price" , $inner_print_price);
$template->reg("output_inner_price", $inner_output_price);
$template->reg("inner_price"       , $inner_sum_price);

$paper_price  = $inner_paper_price;
$print_price  = $inner_print_price;
$output_price = $inner_output_price;
$sum_price    = $inner_sum_price;

//18 제본 가격 검색용 맵핑코드 검색
$param["cate_sortcode"] = $sortcode_b;
$param["after_name"] = "제본";
$param["depth1"] = $price_info_arr["binding_depth1"];
$param["depth2"] = $price_info_arr["binding_depth2"];

$binding_rs = $dao->selectCateAfterInfo($conn, $param);

$binding_mpcode = $binding_rs->fields["mpcode"];

unset($binding_rs);
unset($param);

//19-1 제본가격 검색용 종이수량 공통 정보 생성
$param["amt"]     = $price_info_arr["amt"];
$param["pos_num"] = $pos_num;
$param["amt_unit"]  = $amt_unit;
$param["crtr_unit"] = $dao->selectPrdtPaperInfo($conn,
                                                $price_info_arr["paper_mpcode"],
                                                "crtr_unit")["crtr_unit"];

//19-2 제본가격 검색용 내지1 종이수량 계산
$param["page_num"]  = $price_info_arr["page"]["내지"];

$paper_amt_inner = $commonUtil->getPaperRealPrintAmt($param);

unset($param);

//20 내지1 제본 가격 계산
$param["sell_site"] = $sell_site;
$param["mpcode"]    = $binding_mpcode;
$param["amt"]       = $paper_amt_inner;
$binding_price_inner = $dao->selectBindingPrice($conn, $param);

$binding_price = intval($binding_price_inner);

//21 기본 옵션 가격 검색
$param["cate_sortcode"] = $sortcode_b;
$param["basic_yn"]      = 'Y';
$param["sell_site"]     = $sell_site;

$opt_price = intval($dao->selectCateOptSinglePrice($conn, $param));
$template->reg("opt_default_price", $opt_price);

//15-3 추가 후공정(=제본) 가격 검색
$param["except_arr"]    = "제본";
// 내지
$param["amt"] = $paper_amt_inner;
$after_price = $dao->selectCateAfterSinglePrice($conn, $param);
$inner_after_basic_price = intval($after_price);

$after_price = $inner_after_basic_price;
$template->reg("after_default_price", $after_price);

$sum_price += $binding_price + $opt_price + $after_price;

unset($param);

$tax = $sum_price * TAX_RATE;
$sum_price += $tax;

unset($param);
$template->reg("sell_price", number_format($sum_price));
$c_mileage = $sum_price * $cRs->fields['c_user_rate'] * 0.01;
$template->reg("c_mileage", number_format($c_mileage));

// 회원등급 할인 정보 생성
if ($member_grade === null) {
    $param["dscr"] = NO_LOGIN;
    $grade_sale = makeGradeSaleDl($param, $price_info_arr);
} else {
    $param["cate_sortcode"] = $sortcode_b;
    $param["grade"]         = $member_grade;
    $param["sell_price"]    = $sum_price;
    $grade_sale = $dao->selectGradeSalePriceHtml($conn,
                                                 $param,
                                                 $price_info_arr);
}

unset($param);
$template->reg("grade_sale", $grade_sale);

// 이벤트 할인 정보 생성
$param["dscr"]  = NO_EVENT;
$template->reg("event_sale", makeEventSaleDl($param));
unset($param);

// 기본 할인가격 계산
$sale_price = doubleval($sum_price - $price_info_arr["grade_sale"]);
$template->reg("sale_price", number_format($sale_price));

// 견적서 html 생성
$param["esti_paper"]  = $paper_price;
$param["esti_output"] = $output_price;
$param["esti_print"]  = $print_price;
$param["esti_after"]  = $binding_price + $after_price;
$param["esti_opt"]    = $opt_price;
$param["esti_tax"]    = $tax;
$param["esti_sell_price"] = $sum_price;
$param["esti_sale_price"] = $sale_price;
$template->reg("quick_esti", getQuickEstimateHtml($param));
?>
