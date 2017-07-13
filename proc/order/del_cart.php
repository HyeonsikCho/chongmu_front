<?
	include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/CartCommonDAO.php");
	$connectionPool = new ConnectionPool();
	$conn = $connectionPool->getPooledConnection();
	$fb = new FormBean();
	$cartDao = new CartCommonDAO();	
	//cart_prdlist_id

	
	$param['user_id'] = $fb->ss['MYSEC_ID'];
	$param['prkVal'] = $fb->fb['cart_prdlist_id'];


	$conn->StartTrans();
	$delResult = $cartDao->delCart($conn,$param);
	$conn->CompleteTrans();

	if($delResult){
		echo '1';
	}else{
		echo '2';
	}
	$conn->Close();
?>