<?
/********************************************************************
***** 프로 젝트 : 총무팀
***** 개  발  자 : 김성진
***** 수  정  일 : 2016.05.09
********************************************************************/

/********************************************************************
***** 라이브러리 인클루드
********************************************************************/

include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/OrderDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/CartCommonDAO.php");


/********************************************************************
***** 클래스 선언
********************************************************************/

$connectionPool = new ConnectionPool();                // DB 컨넥션 클래스 선언
$conn = $connectionPool->getPooledConnection();
$dao = new OrderDAO();                                         // 주문정보 데이터 클래스 선언
$cartDao = new CartCommonDAO();	                        // 장바구니 클래스 선언


/********************************************************************
***** 파라미터 선언
********************************************************************/

$param['plzp_id'] = $fb->fb['plzp_id'];
$param['plzp_point'] = $fb->fb['plzp_point'];
$param['cp_pay_type'] = 99;
$param['t_amount'] = (int)$param['plzp_point'];
$param['ohash'] = $fb->ss['ohash'];
$param['user_id'] = $fb->ss['MYSEC_ID'];
$param['order_no'] = $fb->fb['order_no'];
$param['prkVal'] = $fb->fb['cart_prdlist_id'];


/********************************************************************
***** 주문 결제
********************************************************************/

$o_res = $dao->setOrderPayment($conn,$param);

if ($o_res == 'true') {
	$prs = $dao->orderPointList($conn, $param);

	while ($prs && !$prs->EOF) {
		$param['prd_detail_no'] = $prs->fields['prd_detail_no'];
		$param['svc_amt'] = $prs->fields['order_amnt'];
		$param['svc_status'] = '199';
		$param['share_rate'] = $prs->fields['c_rate'];
		$param['gadd_rate'] = $prs->fields['c_user_rate'];
		$param['gadd_point'] = 0;

		/*
		$param['mysec_amt'] = $prs->fields['c_point'];
		$param['pay_amt'] = $prs->fields['pay_amnt'];
		*/

		$param['pay_amt'] = 0;
		$param['mysec_amt'] = 0;

		//포인트전액결제는 포인트를 각 상품판매금액으로 맞춘다.
		$param['used_mysec'] = $prs->fields['order_amnt'];

		if ($prs->fields['print_name'] && $prs->fields['paper_name']) {
			$param['title'] = $prs->fields['cate_name']."/".$prs->fields['print_name']."/".$prs->fields['paper_name'];
		} else {
			$param['title'] = $prs->fields['cate_name'];
		}

		$dao->insertOrderMileageHistory($conn,$param);

		//$dao->updateOrderMileageHistory($conn,$param);
		$param['pay_amt'] = 0;
		$param['mysec_amt'] = 0;
		$post_data = array(
									"SITE_CD" => SITE_CD,
									"AUTH_CD" => AUTH_CD,
									"SVC_NO" => SVC_NO,
									"ORDER_NO" => $param['order_no'],
									"MYSEC_ID" => $fb->ss['MYSEC_ID'],
									"PH_NO" => $fb->ss['PH_NO'],
									"USER_NM" => $fb->ss['USER_NM'],
									"SVC_AMT"=>(int)$param['svc_amt'],
									"SVC_STATUS"=>'199',
									"SVC_DESC"=>$param['title'],
									"SHARE_RATE"=>(int)$param['share_rate'],
									"GADD_RATE"=>(int)$param['gadd_rate'],
									"MYSEC_AMT"=>(int)$param['mysec_amt'],
									"PAY_AMT"=>(int)$param['pay_amt'],
									"USED_MYSEC"=>(int)$param['used_mysec']
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://adm.plzm.info/NsmdG/PI/WPI200_R010/main/');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$ch_rs = curl_exec($ch);
		curl_close($ch);

		$obj = simplexml_load_string($ch_rs);
		//print_r($obj);
		//$obj = json_encode($obj);
		$param['res_cd'] = $obj->RES_CD;
		$param['res_msg'] = $obj->RES_MSG;
		$param['tran_no'] = $obj->TRAN_NO;
		$dao->updateOrderMileageHistory($conn,$param);

		$prs->moveNext();
	}

	// 장바구니 삭제 (사용자id, 장바구니 id)
	$cartDao->setOrderDelCart($conn, $param);

	$ret['result'] = 'true';
	$ret['t_amount'] = $param['t_amount'];

	echo json_encode($ret);
} else {
	$ret['result'] = 'false';

	echo json_encode($ret);
}


/********************************************************************
***** DB 컨넥션 종료
********************************************************************/

$conn->Close();
?>