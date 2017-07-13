<?
	include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/OrderDAO.php");
	$connectionPool = new ConnectionPool();
	$conn = $connectionPool->getPooledConnection();
	$dao = new OrderDAO();	
	//cart_prdlist_id


	$param['user_id'] = $fb->ss['MYSEC_ID'];
	$param['order_no'] = $fb->fb['order_no'];
	$param['ohash'] = $fb->ss['ohash'];
	$param['type'] =  $fb->fb['type'];
	
	if($fb->fb['type'] == 'add'){ // 배송비지불 선불
		$conn->StartTrans();
		$ordResult = $dao->addOrderDelivery($conn,$param);
		$conn->CompleteTrans();		
	}else{ // 배송비지불 착불

		$conn->StartTrans();
		$rtval = $dao->getDeleveryPrdno($conn,$param);
		$param['prd_detail_no'] = $rtval['prd_detail_no'];
		$ordResult = $dao->delOrder($conn,$param);
		$conn->CompleteTrans();		
	}


	echo $ordResult;
	$conn->Close();
?>