<?
/********************************************************************
***** ���� ��Ʈ : �ѹ���
***** ��  ��  �� : �輺��
***** ��  ��  �� : 2016.05.09
********************************************************************/

/********************************************************************
***** ���̺귯�� ��Ŭ���
********************************************************************/

include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/OrderDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/CartCommonDAO.php");


/********************************************************************
***** Ŭ���� ����
********************************************************************/

$connectionPool = new ConnectionPool();                // DB ���ؼ� Ŭ���� ����
$conn = $connectionPool->getPooledConnection();
$dao = new OrderDAO();                                         // �ֹ����� ������ Ŭ���� ����
$cartDao = new CartCommonDAO();	                        // ��ٱ��� Ŭ���� ����


/********************************************************************
***** �Ķ���� ����
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
***** �ֹ� ����
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

		//����Ʈ���װ����� ����Ʈ�� �� ��ǰ�Ǹűݾ����� �����.
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

	// ��ٱ��� ���� (�����id, ��ٱ��� id)
	$cartDao->setOrderDelCart($conn, $param);

	$ret['result'] = 'true';
	$ret['t_amount'] = $param['t_amount'];

	echo json_encode($ret);
} else {
	$ret['result'] = 'false';

	echo json_encode($ret);
}


/********************************************************************
***** DB ���ؼ� ����
********************************************************************/

$conn->Close();
?>