<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/ClaimViewDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$claimDAO = new ClaimViewDAO(); 
$conn->StartTrans();

$claim_seqno = $fb->form("claim_seqno");
$comment = $fb->form("comment");

//클레임 후공정 댓글 추가
$param = array();
$param["table"] = "order_claim_comment";
$param["col"]["order_claim_seqno"] = $claim_seqno;
$param["col"]["comment"] = $comment;
$param["col"]["cust_yn"] = "Y";
$param["col"]["regi_date"] = date("Y-m-d H:i:s", time());

$result = $claimDAO->insertData($conn, $param);

echo $result;
$conn->CompleteTrans();
$conn->close();
?>
