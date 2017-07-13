<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Common/DPrintingFactory.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Common/PrintoutInterface.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/BasicMaterials/Paper.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/BasicMaterials/Option.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/BasicMaterials/Afterprocess.php');

$factory = new DPrintingFactory();
$product = $factory->create($sortcode_b);

// 종이 정보 생성
$paper = $product->makePaperOption();
$template->reg("paper", $paper);

// 인쇄도수 정보 생성
$print_tmpt = $product->makePrintTmptOption();
$template->reg("print_tmpt", $print_tmpt);

// 인쇄방식 정보 생성
$print_purp = $product->makePrintPurpOption();
$template->reg("print_purp", $print_purp);

// 사이즈 정보 생성
$size = $product->makeSizeOption();
$template->reg("size", $size);

// 재단, 작업사이즈간 차이 정보 생성
$size_gap = " _gap%s";
$val = ProductInfoClass::SIZE_GAP[$sortcode_b];
$template->reg("size_gap", sprintf($size_gap, $val));

$add_opt = $product->makeOptOption();
$template->reg("add_opt", $add_opt);

// 기본후공정 내역에 표시할 정보 생성
/*
$basic_after = $after["info_arr"]["basic"];
$basic_after = $frontUtil->arr2delimStr($basic_after, '|');
$template->reg("basic_after", $basic_after);
*/

$add_after = $product->makeAfterOption();
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
$c_mileage = $sell_price * $cRs->fields['c_user_rate'] * 0.01;
$template->reg("c_mileage", number_format($c_mileage));
// 회원등급 할인 정보 생성
if ($member_grade === null) {
    $param["dscr"] = NO_LOGIN;
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


//


// 총무팀 요율
/*
$cRs = $dao->selectCpoint($conn,$param);
$template->reg("c_rate",$cRs->fields['c_rate']);
$template->reg("c_user_rate",$cRs->fields['c_user_rate']);
unset($param);
*/

// 기본 할인가격 계산
$sale_price = doubleval($sell_price - $price_info_arr["grade_sale"]);
$sale_price = $frontUtil->ceilVal($sale_price);
$template->reg("sale_price", number_format($sale_price));
$template->reg("default_price", $sell_price - $tax);
// 견적서 html 생성
$param["esti_print"] = $sell_price - $tax;
$param["esti_tax"]   = $tax;
$param["esti_sell_price"] = $sell_price;
$param["esti_sale_price"] = $sale_price;
$template->reg("quick_esti", getQuickEstimateHtml($param));
?>
