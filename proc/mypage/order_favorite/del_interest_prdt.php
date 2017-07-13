<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderFavoriteDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$orderDAO = new OrderFavoriteDAO();
$conn->StartTrans();

$check = 1;

$seqno_set = explode(",", $fb->form("select_prdt"));

//관심상품 for문 돌며 삭제
for ($i = 0; $i < count($seqno_set); $i++) {

    //관심 상품 상세 삭제
    $param = array();
    $param["table"] = "interest_prdt_detail";
    $param["prk"] = "interest_prdt_seqno";
    $param["prkVal"] = $seqno_set[$i];
    $result = $orderDAO->deleteData($conn, $param);
    if (!$result) $check = 0;
   
    //관심 상품 후공정 내역 삭제
    $param = array();
    $param["table"] = "interest_prdt_after_history";
    $param["prk"] = "interest_prdt_seqno";
    $param["prkVal"] = $seqno_set[$i];
    $result = $orderDAO->deleteData($conn, $param);
    if (!$result) $check = 0;
   
    //관심 상품 옵션 내역 삭제
    $param = array();
    $param["table"] = "interest_prdt_opt_history";
    $param["prk"] = "interest_prdt_seqno";
    $param["prkVal"] = $seqno_set[$i];
    $result = $orderDAO->deleteData($conn, $param);
    if (!$result) $check = 0;
   
    //관심 상품 삭제
    $param = array();
    $param["table"] = "interest_prdt";
    $param["prk"] = "interest_prdt_seqno";
    $param["prkVal"] = $seqno_set[$i];
    $result = $orderDAO->deleteData($conn, $param);
    if (!$result) $check = 0;

}

if ($check == 1) {
    
    echo "1";

} else {

    echo "2";
}

$conn->CompleteTrans();
$conn->close();
?>
