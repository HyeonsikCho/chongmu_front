<?
	include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/OrderDAO.php");
	$connectionPool = new ConnectionPool();
	$conn = $connectionPool->getPooledConnection();
	$dao = new OrderDAO();	

	foreach($fb->fb as $key => $value){
		$param[$key] = $value;
	}

	$param['user_id'] = $fb->ss['MYSEC_ID'];
	$ordResult = $dao->orderCommentInsert($conn,$param);
	


	if($ordResult){
		echo '1';
	}else{
		echo '2';
	}
	$conn->Close();
?>