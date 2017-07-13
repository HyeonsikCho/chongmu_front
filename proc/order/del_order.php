<?
/********************************************************************
***** 프로 젝트 : 총무팀
***** 개  발  자 : 김성진
***** 수  정  일 : 2016.05.03
********************************************************************/

/********************************************************************
***** 라이브러리 인클루드
********************************************************************/

include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/OrderDAO.php");


/********************************************************************
***** 클래스 선언
********************************************************************/

$connectionPool = new ConnectionPool();					// DB 컨넥션 클래스 선언
$conn = $connectionPool->getPooledConnection();

$dao = new OrderDAO();											// 주문정보 데이터 클래스 선언


/********************************************************************
***** 파라미터 선언
********************************************************************/

$param['user_id'] = $fb->ss['MYSEC_ID'];
$param['order_no'] = $fb->fb['order_no'];
$param['prd_detail_no'] = $fb->fb['prd_detail_no'];
$param['ohash'] = $fb->ss['ohash'];


/********************************************************************
***** 주문 삭제 처리
********************************************************************/

if ($param['user_id'] && $param['order_no'] && $param['prd_detail_no'] && $param['ohash']) {
	$conn->StartTrans();
	$ordResult = $dao->delOrder($conn,$param);
	$conn->CompleteTrans();

	$obj = json_decode($ordResult);
	echo $ordResult;
}else{
	echo "{\"result\" : \"false\"}";
}


/********************************************************************
***** DB 컨넥션 종료
********************************************************************/

$conn->Close();

?>