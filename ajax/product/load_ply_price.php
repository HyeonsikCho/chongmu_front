<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductNcDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new ProductNcDAO();
$fb = new FormBean();

$sell_site = $fb->session("sell_site");

/*
 * mono_yn=0
 * amt=500
 * stan_mpcode=1
 * paper_mpcode=1
 * print_name=%EB%8B%A8%EB%A9%B4+4%EB%8F%84                             //단면 4도
 * print_purp=%EC%9D%BC%EB%B0%98%EC%98%B5%EC%85%8B                      //일반옵셋
*/

$cate_sortcode = $fb->form("cate_sortcode");    // 001001001
$paper_mpcode  = $fb->form("paper_mpcode");     // 1
$print_name    = $fb->form("print_name");       // 단면 4도
$print_purp    = $fb->form("print_purp");       // 일반옵셋
$stan_mpcode   = $fb->form("stan_mpcode");      // 1
$amt           = $fb->form("amt");                 // 500

$price_tb = $dao->selectPriceTableName($conn, '0', $sell_site); //ply_price_gp

$param = array();

// 인쇄 맵핑코드 검색
$param["cate_sortcode"] = $cate_sortcode;
$param["name"]          = $print_name;
$param["purp_dvs"]      = $print_purp;

$print_mpcode = $dao->selectCatePrintMpcode($conn, $param);

// 가격 검색
$param["table_name"]    = $price_tb;
$param["cate_sortcode"] = $cate_sortcode;
$param["paper_mpcode"]  = $paper_mpcode;
$param["print_mpcode"]  = $print_mpcode;
$param["stan_mpcode"]   = $stan_mpcode;
$param["amt"]           = $amt;

$price = $dao->selectPrdtPlyPrice($conn, $param);
$price = ceil($price/100) * 100;
$conn->debug = 0;
$ret = "{\"cover\" : {\"price\" : \"%s\"}}";

echo sprintf($ret, $price);
$conn->Close();
?>
