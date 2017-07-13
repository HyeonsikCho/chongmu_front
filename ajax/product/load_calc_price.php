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

$dvs = $fb["dvs"];

$cate_sortcode = $fb["cate_sortcode"];
$stan_mpcode   = $fb["stan_mpcode"];
$amt           = $fb["amt"];
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

$price_tb = $dao->selectPriceTableName($conn, '1', $sell_site);

$param = array();
$param["table_name"]    = $price_tb;
$param["cate_sortcode"] = $cate_sortcode;
$param["stan_mpcode"]   = $stan_mpcode;
$param["amt"]           = $amt;

//$conn->debug = 1;
$cover_rs = null;
if ($dvs === "all" || $dvs === "cover") {
    $print_mpcode_arr = $frontUtil->getPrintMpcode($conn, $dao, $fb, "cover");

    // 표지 가격 검색
    $param["paper_mpcode"]         = $cover_paper_mpcode;
    $param["bef_print_mpcode"]     = $print_mpcode_arr["bef"];
    $param["bef_add_print_mpcode"] = $print_mpcode_arr["bef_add"];
    $param["aft_print_mpcode"]     = $print_mpcode_arr["aft"];
    $param["aft_add_print_mpcode"] = $print_mpcode_arr["aft_add"];
    $param["page"]                 = $cover_page;
    $param["page_dvs"]             = $cover_page_dvs;
    $param["page_detail"]          = $cover_page_detail;

    $cover_rs = $dao->selectPrdtCalcPrice($conn, $param);
    //$conn->debug = 0;
}

$inner1_rs = null;
if ($dvs === "all" || $dvs === "inner1") {
    if ($inner1_page !== '0') {
        $print_mpcode_arr = $frontUtil->getPrintMpcode($conn, $dao, $fb, "inner1");

        // 내지1 가격 검색
        $param["paper_mpcode"]         = $inner1_paper_mpcode;
        $param["bef_print_mpcode"]     = $print_mpcode_arr["bef"];
        $param["bef_add_print_mpcode"] = $print_mpcode_arr["bef_add"];
        $param["aft_print_mpcode"]     = $print_mpcode_arr["aft"];
        $param["aft_add_print_mpcode"] = $print_mpcode_arr["aft_add"];
        $param["page"]                 = $inner1_page;
        $param["page_dvs"]             = $inner1_page_dvs;
        $param["page_detail"]          = $inner1_page_detail;

        $inner1_rs = $dao->selectPrdtCalcPrice($conn, $param);
    } else {
        $inner1_rs = array();
        $inner1_rs["paper_price"]  = 0;
        $inner1_rs["print_price"]  = 0;
        $inner1_rs["output_price"] = 0;
        $inner1_rs["sum_price"]    = 0;
    }
}

$inner2_rs = null;
if ($dvs === "all" || $dvs === "inner2") {
    if ($inner2_page !== '0') {
        $print_mpcode_arr = $frontUtil->getPrintMpcode($conn, $dao, $fb, "inner2");

        // 내지2 가격 검색
        $param["paper_mpcode"]         = $inner2_paper_mpcode;
        $param["bef_print_mpcode"]     = $print_mpcode_arr["bef"];
        $param["bef_add_print_mpcode"] = $print_mpcode_arr["bef_add"];
        $param["aft_print_mpcode"]     = $print_mpcode_arr["aft"];
        $param["aft_add_print_mpcode"] = $print_mpcode_arr["aft_add"];
        $param["page"]                 = $inner2_page;
        $param["page_dvs"]             = $inner2_page_dvs;
        $param["page_detail"]          = $inner2_page_detail;

        $inner2_rs = $dao->selectPrdtCalcPrice($conn, $param);
    } else {
        $inner2_rs = array();
        $inner2_rs["paper_price"]  = 0;
        $inner2_rs["print_price"]  = 0;
        $inner2_rs["output_price"] = 0;
        $inner2_rs["sum_price"]    = 0;
    }
}

$inner3_rs = null;
if ($dvs === "all" || $dvs === "inner3") {
    if ($inner3_page !== '0') {
        $print_mpcode_arr = $frontUtil->getPrintMpcode($conn, $dao, $fb, "inner3");

        // 내지3 가격 검색
        $param["paper_mpcode"]         = $inner3_paper_mpcode;
        $param["bef_print_mpcode"]     = $print_mpcode_arr["bef"];
        $param["bef_add_print_mpcode"] = $print_mpcode_arr["bef_add"];
        $param["aft_print_mpcode"]     = $print_mpcode_arr["aft"];
        $param["aft_add_print_mpcode"] = $print_mpcode_arr["aft_add"];
        $param["page"]                 = $inner3_page;
        $param["page_dvs"]             = $inner3_page_dvs;
        $param["page_detail"]          = $inner3_page_detail;

        $inner3_rs = $dao->selectPrdtCalcPrice($conn, $param);
    } else {
        $inner3_rs = array();
        $inner3_rs["paper_price"]  = 0;
        $inner3_rs["print_price"]  = 0;
        $inner3_rs["output_price"] = 0;
        $inner3_rs["sum_price"]    = 0;
    }
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
    $outer  = sprintf($outer, $cover_rs["paper_price"]
                            , $cover_rs["print_price"]
                            , $cover_rs["output_price"]
                            , $cover_rs["sum_price"]
                            , $inner1_rs["paper_price"]
                            , $inner1_rs["print_price"]
                            , $inner1_rs["output_price"]
                            , $inner1_rs["sum_price"]
                            , $inner2_rs["paper_price"]
                            , $inner2_rs["print_price"]
                            , $inner2_rs["output_price"]
                            , $inner2_rs["sum_price"]
                            , $inner3_rs["paper_price"]
                            , $inner3_rs["print_price"]
                            , $inner3_rs["output_price"]
                            , $inner3_rs["sum_price"]);
} else if ($dvs === "cover") {
    $outer .= " \"cover\"   : %s";

    $outer  = sprintf($outer, $inner);
    $outer  = sprintf($outer, $cover_rs["paper_price"]
                            , $cover_rs["print_price"]
                            , $cover_rs["output_price"]
                            , $cover_rs["sum_price"]);
} else if ($dvs === "inner1") {
    $outer .= " \"inner1\"  : %s";

    $outer  = sprintf($outer, $inner);
    $outer  = sprintf($outer, $inner1_rs["paper_price"]
                            , $inner1_rs["print_price"]
                            , $inner1_rs["output_price"]
                            , $inner1_rs["sum_price"]);
} else if ($dvs === "inner2") {
    $outer .= " \"inner2\"  : %s";

    $outer  = sprintf($outer, $inner);
    $outer  = sprintf($outer, $inner2_rs["paper_price"]
                            , $inner2_rs["print_price"]
                            , $inner2_rs["output_price"]
                            , $inner2_rs["sum_price"]);
} else if ($dvs === "inner3") {
    $outer .= " \"inner3\"  : %s";

    $outer  = sprintf($outer, $inner);
    $outer  = sprintf($outer, $inner3_rs["paper_price"]
                            , $inner3_rs["print_price"]
                            , $inner3_rs["output_price"]
                            , $inner3_rs["sum_price"]);
}
$outer .= '}';

echo $outer;

$conn->Close();
?>
