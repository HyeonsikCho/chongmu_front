<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderAllDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();
$dao = new OrderAllDAO();

$param = array();
$param["order_no"] = $fb->fb['order_no'];
$param["prd_detail_no"] = $fb->fb['prd_detail_no'];
$param["draft_chk"] = $fb->fb['draft_chk'];
$param["draft_comment"] = $fb->fb['draft_comment'];

$result = $dao->updateDraft($conn, $param);

if($result) echo '1';
else        echo '2';
$conn->Close();
?>
