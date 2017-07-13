<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");

// 종이 정보 생성
$paper = $dao->selectCatePaperHtml($conn, $sortcode_b, $price_info_arr);
$template->reg("paper", $paper);

// 인쇄도수 정보 생성
$param["cate_sortcode"] = $sortcode_b;

$print_tmpt = $dao->selectCatePrintTmptHtml($conn,
                                            $param,
                                            $price_info_arr);

$print_tmpt = $print_tmpt["단면"] . $print_tmpt["양면"];
$template->reg("print_tmpt", $print_tmpt);
unset($param);

// 인쇄방식 정보 생성
$print_purp = $dao->selectCatePrintPurpHtml($conn, $sortcode_b);
$template->reg("print_purp", $print_purp);

// 사이즈 정보 생성
$size = $dao->selectCateSizeHtml($conn, $sortcode_b, $price_info_arr);
$template->reg("size", $size);
$template->reg("def_stan_mpcode", $price_info_arr["stan_mpcode"]);
$template->reg("def_cut_wid"    , $price_info_arr["def_cut_wid"]);
$template->reg("def_cut_vert"   , $price_info_arr["def_cut_vert"]);

// 재단, 작업사이즈간 차이 정보 생성
$size_gap = " _gap%s";
$val = ProductInfoClass::SIZE_GAP[$sortcode_b];
$template->reg("size_gap", sprintf($size_gap, $val));

// 옵션 체크박스 생성
$opt = $dao->selectCateOptHtml($conn, $sortcode_b);
$template->reg("opt", $opt["html"]);

// 옵션 가격 레이어 생성
$add_opt = $opt["info_arr"]["name"];
$add_opt = $dao->parameterArrayEscape($conn, $add_opt);
$add_opt = $frontUtil->arr2delimStr($add_opt);

$param["cate_sortcode"] = $sortcode_b;
$param["opt_name"]      = $add_opt;
$param["opt_idx"]       = $opt["info_arr"]["idx"];
$add_opt = $dao->selectCateAddOptInfoHtml($conn, $param);
unset($param);
$template->reg("add_opt", $add_opt);

// 후공정 체크박스 생성
$after = $dao->selectCateAfterHtml($conn, $sortcode_b);
$template->reg("after", $after["html"]);

// 기본후공정 내역에 표시할 정보 생성
$basic_after = $after["info_arr"]["basic"];
$basic_after = $frontUtil->arr2delimStr($basic_after, '|');
$template->reg("basic_after", $basic_after);

// 추가 후공정 가격 레이어 생성
$add_after = $after["info_arr"]["add"];
$add_after = $dao->parameterArrayEscape($conn, $add_after);
$add_after = $frontUtil->arr2delimStr($add_after);
$template->reg("after", $after["html"]);

$param["cate_sortcode"] = $sortcode_b;
$param["after_name"]    = $add_after;
$add_after = $dao->selectCateAddAfterInfoHtml($conn, $param);
unset($param);
$template->reg("add_after", $add_after);

// 카테고리 독판여부, 수량단위 검색
$cate_info_arr = $dao->selectCateInfo($conn, $sortcode_b);
$mono_dvs = $cate_info_arr["mono_dvs"];
$amt_unit = $cate_info_arr["amt_unit"];
unset($cate_info_arr);
$template->reg("mono_dvs", makeMonoDvsOption($mono_dvs));

// 지질느낌 검색
$paper_sense = $dao->selectPaperDscr($conn, $price_info_arr["paper_mpcode"]);
$template->reg("paper_sense", $paper_sense);

// 수량 정보 생성
$mono_dvs = ($mono_dvs === '1' || $mono_dvs === '2') ? '0' : '1';
$price_tb = $dao->selectPriceTableName($conn, $mono_dvs, $sell_site);
$param["table_name"]    = $price_tb;
$param["cate_sortcode"] = $sortcode_b;
$param["amt_unit"]      = $amt_unit;
$amt = $dao->selectCateAmtHtml($conn, $param, $price_info_arr);
unset($param);
$template->reg("amt", $amt);
$template->reg("amt_unit", $amt_unit);

// 기준가격(정상판매가) 검색, 부가세 계산
$price_info_arr["table_name"] = $price_tb;
$sell_price = doubleval($dao->selectPrdtPlyPrice($conn, $price_info_arr));
$sell_price = $frontUtil->ceilVal($sell_price);
$tax = $sell_price * TAX_RATE;
$tax = $frontUtil->ceilVal($tax);
$sell_price += $tax;
$template->reg("sell_price", number_format($sell_price));

// 회원등급 할인 정보 생성
if ($member_grade === null) {
    //$param["dscr"] = NO_LOGIN;
    $grade_sale = makeGradeSaleDl($param, $price_info_arr);
} else {
    $param["cate_sortcode"] = $sortcode_b;
    $param["grade"]         = $member_grade;
    $param["sell_price"]    = $sell_price;
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
$sale_price = doubleval($sell_price - $price_info_arr["grade_sale"]);
$sale_price = $frontUtil->ceilVal($sale_price);
$template->reg("sale_price", number_format($sale_price));

// 견적서 html 생성
$param["esti_print"] = $sell_price - $tax;
$param["esti_tax"]   = $tax;
$param["esti_sell_price"] = $sell_price;
$param["esti_sale_price"] = $sale_price;
$template->reg("quick_esti", getQuickEstimateHtml($param));
?>
