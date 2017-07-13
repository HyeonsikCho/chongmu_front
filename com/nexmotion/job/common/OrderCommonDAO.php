<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/CommonDAO.php');

class OrderCommonDAO extends CommonDAO {
	function __construct() {
	}

	/**
	 * @brief CART->주문인서트111
	 *
	 *
	 *
	 * @param $conn  = connection identifier
	 * @param $param["prk_val"][] = 주문에 넣을 카트 ID
	 * @param $param["user_id"] = 유저아이디
	 * @return true / false
	 */
	function insertOrder($conn,$param){
		if ($this->connectionCheck($conn) === false) {
			return false;
		}
		$prkValCount = count($param["prkVal"]);
		for ($i = 0; $i < $prkValCount; $i++) {
			$val = $conn->qstr($param["prkVal"][$i], get_magic_quotes_gpc());
			$query .= $val;

			if ($i !== $prkValCount - 1) {
				$query .= ",";
			}
		}

		$ord_no = time().getRandom(1000, 9999);


		//order master insert

		$ord_mas_insert = "insert into order_master (order_no,user_id,order_stime,order_amnt,addtax_order_amnt,prd_amnt,addtax_prd_amnt,c_point,delivery_amnt,ohash,reg_date)
						   select '".$ord_no."','".$param["user_id"]."',now(),sum(t.tot_amnt),sum(t.tot_t_amnt),sum(t.tot_amnt),sum(t.tot_t_amnt),sum(t.tot_c_amnt),0,'".$param['ohash']."',now() from (
								select ifnull(a.cart_amnt,0) as tot_amnt,
								ifnull(a.addtax_amnt,0) as tot_t_amnt,
								ifnull(a.cart_amnt,0) as tot_c_amnt
								 from cart_prdlist as a
								left join cart_after_history as b on (a.cart_prdlist_id = b.cart_prdlist_id)
								left join cart_opt_history as c on (a.cart_prdlist_id = c.cart_prdlist_id)
								inner join cate as d on (a.cate_sortcode = d.sortcode)
								where user_id='".$param["user_id"]."'
								and a.cart_prdlist_id in ($query)
								group by cart_prdlist_seq
								) as t";



		//주문상품 insert
		$setSql = "set @rownum :=0";
		$prd_list_insert = "insert into order_prdlist (order_no,prd_detail_no,title,cate_sortcode,cate_print_mpcode,cate_paper_mpcode,cate_stan_mpcode,cate_stan_type,stan_cal,work_size_wid,work_size_vert,cut_size_wid,cut_size_vert,
							tomson_size_wid,tomson_size_vert,prd_amount,prd_count,prd_d_amnt,prd_amnt,addtax_d_amnt,addtax_amnt,c_rate,c_user_rate,cpoint,detail,regdate)
							select '".$ord_no."',plist.* from (
							select a.cart_prdlist_id as car_prdlist, max(a.title) as title,min(a.cate_sortcode) as cate_sortcode,
							min(a.cate_print_mpcode) as cate_print_mpcode,min(a.cate_paper_mpcode) as cate_paper_mpcode,
							min(a.cate_stan_mpcode) as cate_stan_mpcode,
							min(a.cate_stan_type) as cate_stan_type,min(a.stan_cal) as stan_cal,min(a.work_size_wid) as work_size_wid,
							min(a.work_size_vert) as work_size_vert,
							min(a.cut_size_wid) as cut_size_wid,min(a.cut_size_vert) as cut_size_vert,min(a.tomson_size_wid) as tomson_size_wid,
							min(a.tomson_size_vert) as tomson_size_vert,min(a.prd_amount) as prd_amount,min(a.prd_count) as prd_count,
							max(a.cart_d_amnt) as d_amnt,max(a.cart_amnt) as cart_amnt,max(a.addtax_d_amnt) as addtax_d_amnt,max(a.addtax_amnt) as addtax_amnt,
							max(ifnull(d.c_rate,0)),max(ifnull(d.c_user_rate,0)),min(ifnull(a.cpoint,0)) as cpoint,min(a.detail) as detail,now()
							 from cart_prdlist as a
							inner join cate as d on (a.cate_sortcode = d.sortcode)
							where a.user_id='".$param["user_id"]."'
							and a.cart_prdlist_id in ($query)
							group by a.cart_prdlist_seq
							) as plist";

		$setSql1 = "set @rownum := 0, @test :=0;";
		$after_list_insert = "insert into order_after_history (order_no,prd_detail_no,temp,after_name,basic_yn,price_effect_flag,depth1,depth2,depth3,detail,prd_count,d_amnt,amnt,addtax_d_amnt,addtax_amnt,c_rate,c_user_rate,cpoint,seq,regdate)
								select '".$ord_no."',
								a.cart_prdlist_id as prd_detail_no, @test := cart_prdlist_id as temp,
								after_name,basic_yn,price_effect_flag,depth1,depth2,depth3,detail,prd_count,d_amnt,amnt,addtax_d_amnt,addtax_amnt,c_rate,c_user_rate,cpoint,seq,now() from cart_after_history as a
								where exists (select 1 from cart_prdlist as b where a.cart_prdlist_id = b.cart_prdlist_id and b.user_id='".$param["user_id"]."')
								and a.cart_prdlist_id in ($query)";

		$opt_list_insert = "insert into order_opt_history (order_no,prd_detail_no,temp,opt_name,basic_yn,price_effect_flag,depth1,depth2,depth3,detail,prd_count,d_amnt,amnt,addtax_d_amnt,addtax_amnt,c_rate,c_user_rate,cpoint,seq,regdate)
								select '".$ord_no."',
								a.cart_prdlist_id as prd_detail_no, @test := cart_prdlist_id as temp,
								opt_name,basic_yn,price_effect_flag,depth1,depth2,depth3,detail,prd_count,d_amnt,amnt,addtax_d_amnt,addtax_amnt,c_rate,c_user_rate,cpoint,seq,now() from cart_opt_history as a
								where exists (select 1 from cart_prdlist as b where a.cart_prdlist_id = b.cart_prdlist_id and b.user_id='".$param["user_id"]."')
								and a.cart_prdlist_id in ($query)";

		//echo $prd_list_insert;

		//echo $opt_list_insert;
		//exit;

		$rs[] = $conn->Execute($ord_mas_insert);
		$rs[] = $conn->Execute($setSql);
		$conn->Execute($prd_list_insert);
		$rs[] = $conn->Execute($after_list_insert);
		$rs[] = $conn->Execute($opt_list_insert);

		if(in_array(false,$rs)){
			return false;
		}else{
			return $ord_no;
		}
		//echo $opt_list_insert;
	}

}

function getRandom($min, $max) {
  srand((double) microtime() * 1000000);
  $rand = rand($min, $max);
  return $rand;
 }
?>
