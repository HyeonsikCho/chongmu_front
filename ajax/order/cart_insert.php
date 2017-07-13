<?
	include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/CartDAO.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/OrderDAO.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/define/product_info_class.php");
	include_once($_SERVER["DOCUMENT_ROOT"] .'/test/Common/DPrintingFactory.php');

	$connectionPool = new ConnectionPool();
	$conn = $connectionPool->getPooledConnection();
	$dao = new CartDAO();
	$orderDao = new OrderDAO();
	$info = ProductInfoClass::AFTER_ARR;
	$table_info = ProductInfoClass::PRICE_TABLE;

	$sel_opt_price = $sel_after_price = 0;

	$param['user_id'] = $fb->ss['MYSEC_ID'];
	$param['title'] = $fb->fb['title'];
	$param['cate_sortcode'] = $fb->fb['sortcode'];
	$param['print_name'] = $fb->fb['bef_tmpt_cover'];
	$param['cate_stan_mpcode'] = $fb->fb['size'];
	$param['cate_paper_mpcode'] = $fb->fb['paper_cover'];
	$param['prd_amount'] = $fb->fb['amt'];
	$param['prd_count'] = $fb->fb['count'];
	$param['c_rate'] = (int)$fb->fb['c_rate'];
	$param['c_user_rate'] = (int)$fb->fb['c_user_rate'];
	$param['cut_size_wid'] = $fb->fb['cut_wid_size'];
	$param['cut_size_vert'] = $fb->fb['cut_vert_size'];
	$param['work_size_wid'] = $fb->fb['work_wid_size'];
	$param['work_size_vert'] = $fb->fb['work_vert_size'];
	$param['tomson_size_wid'] = null;
	$param['tomson_size_vert'] = null;
	$param['cate_stan_type'] = $fb->fb['size_dvs'];
	$param['stan_cal'] = $fb->fb['manu_pos_num'];
	$param['direct_flag'] = $fb->fb['direct_flag'];
	$param['affil'] = $fb->fb['affil'];
	$param['flag'] = $fb->fb['flag'];
	$param['paper_depth'] = $fb->fb['paper_depth'];
	$param['paper_detail'] = $fb->fb['paper_detail'];

	$amt = $fb->fb['amt'];
	$count = $fb->fb['count'];
	$manu_pos_num = $fb->fb['manu_pos_num'];
	$count *= $manu_pos_num;


	$factory = new DPrintingFactory();
	$product = $factory->create($param['cate_sortcode']);

	 //옵션이 있는지 확인후 처리
	if(empty($fb->fb['opt_mp_list']) === false) {
		$param['opt_mpcode'] = explode('|',$fb->fb['opt_mp_list']);
		$param['opt_price'] = explode('|',$fb->fb['opt_add_price']);
		$param['count'] = count($param['opt_mpcode']);
		$opt_sum_price = array_sum($param['opt_price']);
		$opt_list = $dao->getOptList($conn,$param);
		$i=0;
		while($opt_list && !$opt_list->EOF){
			$sel_opt_price += $opt_list->fields['sell_price'];
			$param['opt'][$i]['mpcode'] = $opt_list->fields['mpcode'];
			$param['opt'][$i]['depth1'] = $opt_list->fields['depth1'];
			$param['opt'][$i]['depth2'] = $opt_list->fields['depth2'];
			$param['opt'][$i]['depth3'] = $opt_list->fields['depth3'];
			$param['opt'][$i]['basic_yn'] = $opt_list->fields['basic_yn'];
			$param['opt'][$i]['d_amnt'] = $opt_list->fields['sell_price'];
			$param['opt'][$i]['amnt'] = $opt_list->fields['sell_price'];
			$param['opt'][$i]['addtax_d_amnt'] = addTax($opt_list->fields['sell_price']);
			$param['opt'][$i]['addtax_amnt'] = addTax($opt_list->fields['sell_price']);
			$param['opt'][$i]['cpoint'] = $param['opt'][$i]['amnt'] * 0.01 * $param['c_rate'];
			$param['opt'][$i]['name'] = $opt_list->fields['opt_name'];
			$param['opt'][$i]['detail'] = trim($fb->fb[$info[$opt_list->fields['opt_name']].'_info']); // 예)후공정 두귀도리 $fb->fb['rounding_info']
			$param['opt'][$i]['seq'] = $i;
			$opt_list->MoveNext();
			$i++;
		}
	}

	 //후공정이 있는지 확인후 처리
	if(empty($fb->fb['after_mp_list']) === false){
		$param['after_mpcode'] = explode('|',$fb->fb['after_mp_list']);
		$param['after_price'] = explode('|',$fb->fb['after_add_price']);
		$param['count'] = count($param['after_mpcode']);
		$param['depth1_list'] = explode('|',$fb->fb['depth1_list']);
		$param['depth2_list'] = explode('|',$fb->fb['depth2_list']);
		$param['depth3_list'] = explode('|',$fb->fb['depth3_list']);

		$depth1 = array();
		$depth2 = array();
		$depth3 = array();
		for($i = 0; $i < $param['count']; $i++) {
			$depth1[$param['after_mpcode'][$i]] = $param['depth1_list'][$i];
			$depth2[$param['after_mpcode'][$i]] = $param['depth2_list'][$i];
			$depth3[$param['after_mpcode'][$i]] = $param['depth3_list'][$i];
		}

		$after_sum_price = array_sum($param['after_price']);// * (int)$fb->fb['count'];
		$after_list = $dao->getAfterList($conn,$param);
		$i=0;
		while($after_list && !$after_list->EOF) {
			$sel_after_price = $after_list->fields['sell_price'];// * (int)$fb->fb['count'];
			$param['after'][$i]['mpcode'] = $after_list->fields['mpcode'];
			$param['after'][$i]['depth1'] = $after_list->fields['depth1'];
			$param['after'][$i]['depth2'] = $after_list->fields['depth2'];
			$param['after'][$i]['depth3'] = $after_list->fields['depth3'];
			$param['after'][$i]['basic_yn'] = $after_list->fields['basic_yn'];
			$param['after'][$i]['cpoint'] = $param['after'][$i]['amnt'] * 0.01 * $param['c_rate'];
			$param['after'][$i]['name'] = $after_list->fields['after_name'];
			$param['after'][$i]['detail'] = trim($fb->fb[$info[$after_list->fields['after_name']].'_info']); // 예)후공정 두귀도리 $fb->fb['rounding_info']
			$param['after'][$i]['seq'] = $i;
			$mpcode = $after_list->fields['mpcode'];
			$product = $factory->createAfter($product, $after_list->fields['after_name']);
			$product->setAfterprocess($param['cate_sortcode'] ,$after_list->fields['after_name'], $amt, $count, $mpcode, $depth1[$mpcode], $depth2[$mpcode], $depth3[$mpcode]);
			$price = $product->costEach();

			$param['after'][$i]['d_amnt'] = $price;
			$param['after'][$i]['amnt'] = $price;
			$param['after'][$i]['addtax_d_amnt'] = $price;
			$param['after'][$i]['addtax_amnt'] = $price;
			$after_list->MoveNext();
			$i++;
		}

	}
	//	158950//10807390
	$param['cate_print_mpcode'] = $dao->getPrintMpcode($conn,$param);
	$real_cate_price = $dao->getCatePrice($conn,$param);
	$real_cate_price = ceil($real_cate_price/100) * 100;

	$product = $factory->createPaper($product,$param['cate_sortcode']);
	$product->setPaper($param['cate_sortcode'], $amt, $count, $param['cate_stan_mpcode'], $param['cate_paper_mpcode'], $param['print_name'], $print_purp, $param['paper_depth']);
	$price = $product->cost();

	//기본상품가(종이/인쇄/규격) = 기본가 * 건수 * 자리수
	$default_price  = 0;
	if($param['flag'] == 'N') { // 책자형, 계산형
		$post_price = (int)$fb->fb['inner_price'];
		$default_price = (int)$fb->fb['inner_price'];
		$sel_cate_price  = $real_cate_price * $fb->fb['manu_pos_num'];
		$param['cart_d_amnt'] = $default_price;
		//$param['addtax_d_amnt'] = $default_price;
	} else { // 낱장형, 확정형
		$post_price = (int)$price;
		$default_price = (int)$price;
		$sel_cate_price  = $real_cate_price * $fb->fb['count'] * $fb->fb['manu_pos_num'];
		$param['cart_d_amnt'] = (int)$price;
		$param['addtax_d_amnt'] = (int)$price;
	}

	$param['cart_amnt'] = (int)$price;
	$param['addtax_amnt'] = (int)$price;

	$param['cpoint'] = $param['cart_amnt'] * 0.01 * $param['c_rate'];
    $conn->StartTrans();
	$param['cart_prdlist_id'] = $cart_prdlist_id = $dao->getCartSequence($conn);
	$cartRs[] = $dao->setCartPrdlist($conn,$param);
	if(empty($fb->fb['opt_mp_list']) === false){
		$cartRs[] = $dao->setCartOptlist($conn,$param);
	}
	if(empty($fb->fb['after_mp_list']) === false){
		$cartRs[] = $dao->setCartAfterlist($conn,$param);
	}
	$conn->CompleteTrans();

	if(in_array(false,$cartRs)){
		echo '{"result":"false"}';
		goto BLANK;
	}else{
		/* 즉시주문은 바로 order를 실행*/
		if($fb->fb['direct_flag'] == 'Y'){
			$param = null;

			$param['ohash'] = $dparam['ohash'] = password_hash(rand(1,1000000).time(),PASSWORD_DEFAULT);
			$param['user_id'] = $dparam['user_id'] = $fb->ss['MYSEC_ID'];
			$param['prkVal'][0] = $cart_prdlist_id;


			$conn->StartTrans();
			$rs = $orderDao->insertOrder($conn,$param);
			if($rs){
				$dparam['order_no'] = $rs;
				$rs2 = $orderDao->addOrderDelivery($conn,$dparam);
			}
			$conn->CompleteTrans();

			if($rs && $rs2){
				//주문 프로세스에 비교할 해시값을 세션에 할당
				$fb->addSession('ohash',$param['ohash']);
				$return['result'] = 'true';
				$return['order_no'] = $rs;
				$return['cart_prdlist_id'] = $cart_prdlist_id;

			}else{
				$return['result'] = 'false';

			}
			echo json_encode($return);
			goto BLANK;
		}else{
			echo '{"result":"true"}';
			goto BLANK;
		}
	}
BLANK:
    $conn->Close();
    exit;

function addTax($orgPrice)
{
	return $orgPrice;
}

function upCeil($orgPrice)
{
	return ceil($orgPrice*0.01) * 100;
}

?>