<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderAllDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new OrderAllDAO();

$param = array();
$param["order_no"] = $fb->fb['order_no'];
$param["prd_detail_no"] = $fb->fb['prd_detail_no'];
$result = $dao->selectDraft($conn, $param);

echo $result;
?>
