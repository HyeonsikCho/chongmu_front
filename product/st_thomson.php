<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/Template.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductStDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$template = new Template();
//$util = new FrontCommonUtil();
$dao = new ProductStDAO();

// 명함 상품 정보 처리부분 include
// 사진, 배너, 카테고리 셀렉트박스
include_once($_SERVER["DOCUMENT_ROOT"] . "/product/info/common_info.php");
// 종이, 옵션, 후공정 등
include_once($_SERVER["DOCUMENT_ROOT"] . "/product/info/st_thomson_info.php");

include_once($_SERVER["DOCUMENT_ROOT"] . "/common/login_check.php");

// 기본사용 자바스크립트, css 파일 불러오는 용
$template->reg("dir", "product");
$template->reg("page", "st");

//design_dir 경로
$template->reg("design_dir", "/design_template");
$template->htmlPrint($_SERVER["PHP_SELF"]);

$conn->Close();
?>
