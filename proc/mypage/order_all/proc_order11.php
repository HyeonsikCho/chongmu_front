<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderAllDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$orderDAO = new OrderAllDAO();
$conn->StartTrans();
echo "<pre>";
$conn->debug=1;

$session = $fb->getSession();

$param = array();
$param["member_seqno"] = $session["org_member_seqno"];
$param["order_seqno"] = $fb->form("seqno");

$result = $orderDAO->selectMemberName($conn, $param);

$param["member_name"] = $result->fields["member_name"];

echo "============" . $member_name;


$result = $orderDAO->updateOrderState($conn, $param);







$conn->CompleteTrans();
$conn->close();
?>
