<?
	header('Content-type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/OrderDAO.php");
	$connectionPool = new ConnectionPool();
	$conn = $connectionPool->getPooledConnection();
	$fb = new FormBean();
	$orderDao = new OrderDAO();	
	//cart_prdlist_id

	$param['ohash'] = $dparam['ohash'] = password_hash(rand(1,1000000).time(),PASSWORD_DEFAULT);
	$param['user_id'] = $dparam['user_id'] = $fb->ss['MYSEC_ID'];
	$param['prkVal'] = $fb->fb['cart_prdlist_id'];	

	
	$conn->StartTrans();

	$rs = $orderDao->insertOrder($conn,$param);
	if($rs){
		//$conn->debug=1;
		$dparam['order_no'] = $rs;
		$rs2 = $orderDao->addOrderDelivery($conn,$dparam);
	}
	$conn->CompleteTrans();

	if($rs && $rs2){
		//주문 프로세스에 비교할 해시값을 세션에 할당
		$fb->addSession('ohash',$param['ohash']);
		$return['result'] = 'true';
		$return['ordno'] = $rs;

	}else{
		$return['result'] = 'false';
	}
	echo json_encode($return);
	$conn->Close();
?>