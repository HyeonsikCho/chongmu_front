<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/ClaimViewDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/util/pageLib.php');

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();
echo "<pre>";
$conn->debug=1;

$fb = new FormBean();
$claimDAO = new ClaimViewDAO();

$session = $fb->getSession();

$param = array();
$param["claim_seqno"] = $fb->form("claim_seqno");
$param["member_seqno"] = $session["org_member_seqno"];

$result = $claimDAO->selectClaimDetail($conn, $param);


?>
