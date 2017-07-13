<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_lib/CommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$util = new FrontCommonUtil();
$dao = new CommonDAO();
$fb = new FormBean();

$sell_site = $fb->session("sell_site");

$cate_sortcode = $fb->form("cate_sortcode");
$purp_dvs      = $fb->form("val");

$param = array();
$param["cate_sortcode"] = $cate_sortcode;
$param["purp_dvs"]      = $purp_dvs;

$temp = array();

// 카테고리와 인쇄용도로 전/후면 인쇄도수 검색
$rs = $dao->selectCatePrintTmptHtml($conn, $param, $temp);

$sheet_tmpt   = $rs["단면"] . $rs["양면"];
$bef_tmpt     = $rs["전면"];
$bef_add_tmpt = $rs["전면추가"];
$aft_tmpt     = $rs["후면"];
$aft_add_tmpt = $rs["후면추가"];

$ret  = '{';
$ret .= " \"sheet_tmpt\"   : \"%s\",";
$ret .= " \"bef_tmpt\"     : \"%s\",";
$ret .= " \"bef_add_tmpt\" : \"%s\",";
$ret .= " \"aft_tmpt\"     : \"%s\",";
$ret .= " \"aft_add_tmpt\" : \"%s\"";
$ret .= '}';

echo sprintf($ret, $util->convJsonStr($sheet_tmpt)
                 , $util->convJsonStr($bef_tmpt)
                 , $util->convJsonStr($bef_add_tmpt)
                 , $util->convJsonStr($aft_tmpt)
                 , $util->convJsonStr($aft_add_tmpt));

$conn->Close();
?>
