<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductNcDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$frontUtil = new FrontCommonUtil();
$dao = new ProductNcDAO();
$fb = new FormBean();

$sell_site = $fb->session("sell_site");

$fb = $fb->getForm();

$dvs      = $fb["dvs"];
$mono_yn  = $fb["mono_yn"];
$tmpt_dvs = $fb["tmpt_dvs"];
$affil    = $fb["affil"];

$cate_sortcode = $fb["cate_sortcode"];
$stan_mpcode   = $fb["stan_mpcode"];
$amt           = $fb["amt"];
// 내지
$inner_paper_mpcode = $fb["inner_paper_mpcode"];
$inner_page_info    = explode('!', $fb["inner_page_info"]);
$inner_page         = $inner_page_info[0];
$inner_page_detail  = $inner_page_info[1];
$inner_page_dvs     = "내지";

$price_tb = $dao->selectPriceTableName($conn, $mono_yn, $sell_site);

$param = array();
$param["table_name"]    = $price_tb;
$param["cate_sortcode"] = $cate_sortcode;
$param["stan_mpcode"]   = $stan_mpcode;
$param["amt"]           = $amt;
$param["affil"]         = $affil;

//$conn->debug = 1;

// 인쇄 맵핑코드 검색
// 도수구분에 따라서 변경
if ($tmpt_dvs === '0') {
    $print_name   = $fb["inner_bef_print_name"];
    $print_purp   = $fb["inner_print_purp"];

    $param["cate_sortcode"] = $cate_sortcode;
    $param["name"]          = $print_name;
    $param["purp_dvs"]      = $print_purp;

    $print_mpcode = $dao->selectCatePrintMpcode($conn, $param);

    $param["bef_print_mpcode"]     = $print_mpcode;
    $param["bef_add_print_mpcode"] = '0';
    $param["aft_print_mpcode"]     = '0';
    $param["aft_add_print_mpcode"] = '0';
} else {
    $print_mpcode_arr = $frontUtil->getPrintMpcode($conn, $dao, $fb, "inner");

    $param["bef_print_mpcode"]     = $print_mpcode_arr["bef"];
    $param["bef_add_print_mpcode"] = $print_mpcode_arr["bef_add"];
    $param["aft_print_mpcode"]     = $print_mpcode_arr["aft"];
    $param["aft_add_print_mpcode"] = $print_mpcode_arr["aft_add"];
}

// 내지1 가격 검색
$param["paper_mpcode"] = $inner_paper_mpcode;
$param["page"]         = $inner_page;
$param["page_dvs"]     = $inner_page_dvs;
$param["page_detail"]  = $inner_page_detail;

$inner_rs = $dao->selectPrdtCalcPrice($conn, $param);

$price_json  = '{';
$price_json .= " \"paper\"  : \"%s\",";
$price_json .= " \"print\"  : \"%s\",";
$price_json .= " \"output\" : \"%s\",";
$price_json .= " \"price\"  : \"%s\"";
$price_json .= '}';

$outer  = '{';
$outer .= " \"inner\"  : %s";

$outer  = sprintf($outer, $price_json);
$outer  = sprintf($outer, $frontUtil->ceilVal($inner_rs["paper_price"])
                        , $frontUtil->ceilVal($inner_rs["print_price"])
                        , $frontUtil->ceilVal($inner_rs["output_price"])
                        , $frontUtil->ceilVal($inner_rs["sum_price"]));
$outer .= '}';

echo $outer;

$conn->Close();
?>
