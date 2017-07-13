<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/OrderCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/order/orderListHTML.php');

class OrderDAO extends OrderCommonDAO {
 		function __construct() {
		}

	function setDlvrList($conn,$param){
		$param = $this->parameterArrayEscape($conn,$param);
		$inSql = "insert into order_dlvr (
					dlvr_pay_way,
					order_no,
					order_user_name,
					order_user_telno,
					order_user_phno,
					order_user_zipcode,
					order_user_addr1,
					order_user_addr2,
					res_user_name,
					res_user_telno,
					res_user_phno,
					res_user_zipcode,
					res_user_addr1,
					res_user_addr2,
					regdate) values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,now())";
		$inSql = sprintf($inSql,$param['dlvr_pay_way'],
								$param['order_no'],
								$param['order_user_name'],
								$param['order_user_telno'],
								$param['order_user_phno'],
								$param['order_user_zipcode'],
								$param['order_user_addr1'],
								$param['order_user_addr2'],
								$param['res_user_name'],
								$param['res_user_telno'],
								$param['res_user_phno'],
								$param['res_user_zipcode'],
								$param['res_user_addr1'],
								$param['res_user_addr2']);
		$rs = $conn->Execute($inSql);
		return $rs;
	}


     /**
     * @brief CART->주문인서트
     *
     *
     *
     * @param $conn  = connection identifier
     * @param $param["order_no"]= 주문에 넣을 카트 ID
	 * @param $param["user_id"] = 유저아이디
     * @return true / false
     */

	 /********************************************************************
	 ***** 주문정보 데이터 가져오기
	 ********************************************************************/

	 function getOrderList($conn,$param){
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

		$param['user_id'] = $this->parameterEscape($conn, $param['user_id']);
		$param['ohash'] =  $this->parameterEscape($conn, $param['ohash']);
		$param['order_no'] =  $this->parameterEscape($conn, $param['order_no']);
		$param['prd_detail_no'] = $this->parameterEscape($conn, $param['prd_detail_no']);

		$sql = "SELECT max(a.order_no) as order_no, b.prd_detail_no,max(a.order_amnt) as order_amnt, ";
		$sql .= "max(a.addtax_order_amnt) as addtax_order_amnt, max(a.c_point) as c_point, ";
		$sql .= "ifnull(b.addtax_amnt, 0) as addtax_prd_amnt, ";
		$sql .= "max(a.addtax_prd_amnt) as all_addtax_prd_amnt, max(b.title) as title,max(e.cate_name) as cate_name, ";
		$sql .= "group_concat(distinct concat(i.origin_file_name,'_',i.dvs) order by i.dvs asc SEPARATOR '/') as img_name, ";
		$sql .= "group_concat(distinct i.dvs  order by i.dvs asc SEPARATOR '/') as img_dvs, ";
		$sql .= "max(concat(f.name, ' ', f.color, ' ', f.basisweight)) as paper_name, ";
		$sql .= "max(h.name) as print_name, max(b.prd_amount) as prd_amount,max(b.prd_count) as prd_count, ";
		$sql .= "group_concat(distinct c.opt_name order by c.seq asc SEPARATOR '/') as opt_name, ";
		$sql .= "group_concat(distinct d.after_name order by d.seq asc SEPARATOR '/') as after, ";
		$sql .= "sum(d.amnt) as after_amnt, sum(c.amnt) as opt_amnt, ifnull(b.prd_amnt, 0) as prd_amnt, ";
		$sql .= "((ifnull(b.prd_amnt, 0)) + sum(ifnull(c.amnt, 0)) + sum(ifnull(d.amnt, 0))) as tot_amnt, ";
		$sql .= "((ifnull(b.prd_amnt, 0)) + sum(ifnull(c.amnt, 0)) + sum(ifnull(d.amnt, 0))) * max(ifnull(e.c_rate, 0) * 0.01) as c_amnt, ";
		$sql .= "max(ifnull(e.c_rate, 0)), max(i.file_path) as file_path, max(i.save_file_name) as save_file_name ";
		$sql .= "FROM order_master as a ";
		$sql .= "inner join order_prdlist as b on (a.order_no = b.order_no) ";
		$sql .= "left join order_opt_history as c on (b.order_no = c.order_no and b.prd_detail_no = c.prd_detail_no) ";
		$sql .= "left join order_after_history as d on (b.order_no = d.order_no and b.prd_detail_no = d.prd_detail_no) ";
		$sql .= "left join order_img as i on (b.order_no = i.order_no and b.prd_detail_no = i.prd_detail_no) ";
		$sql .= "inner join cate as e on (b.cate_sortcode = e.sortcode) ";
		$sql .= "inner join cate_paper as f on (b.cate_sortcode = f.cate_sortcode and b.cate_paper_mpcode = f.mpcode) ";
		$sql .= "inner join cate_print as g on (b.cate_sortcode = g.cate_sortcode and b.cate_print_mpcode = g.mpcode) ";
		$sql .= "inner join prdt_print as h on (g.prdt_print_seqno = h.prdt_print_seqno) ";
		$sql .= "WHERE a.order_no = %s ";
		$sql .= "GROUP BY b.prd_detail_no";

		/*$sql = "SELECT MAX(a.order_no) AS order_no, b.prd_detail_no,MAX(a.order_amnt) AS order_amnt, ";
		$sql .= "MAX(a.addtax_order_amnt) AS addtax_order_amnt, MAX(a.c_point) AS c_point, ";
		$sql .= "b.addtax_amnt + SUM(DISTINCT d.addtax_amnt) + SUM(c.addtax_amnt) AS addtax_prd_amnt, ";
		$sql .= "MAX(a.addtax_prd_amnt) AS all_addtax_prd_amnt, MAX(b.title) AS title, MAX(e.cate_name) AS cate_name, ";
		$sql .= "group_concat(DISTINCT concat(i.origin_file_name,'_',i.dvs) ORDER BY i.dvs ASC SEPARATOR '/') AS img_name, ";
		$sql .= "group_concat(DISTINCT i.dvs  ORDER BY i.dvs ASC SEPARATOR '/') AS img_dvs, ";
		$sql .= "MAX(concat(f.name, ' ', f.color, ' ', f.basisweight)) AS paper_name, ";
		$sql .= "MAX(h.name) AS print_name, MAX(b.prd_amount) AS prd_amount,MAX(b.prd_count) AS prd_count, ";
		$sql .= "group_concat(DISTINCT c.opt_name ORDER BY c.seq ASC SEPARATOR '/') AS opt_name, ";
		$sql .= "group_concat(DISTINCT d.after_name ORDER BY d.seq ASC SEPARATOR '/') AS after, ";
		$sql .= "SUM(d.amnt) AS after_amnt, SUM(c.amnt) AS opt_amnt, ifnull(b.prd_amnt, 0) AS prd_amnt, ";
		$sql .= "b.prd_amnt + SUM(c.amnt) + SUM(d.amnt) AS tot_amnt, ";
		$sql .= "(b.prd_amnt + SUM(c.amnt) + SUM(d.amnt)) * MAX(e.c_rate * 0.01) AS c_amnt, ";
		$sql .= "MAX(e.c_rate), MAX(i.file_path) AS file_path, MAX(i.save_file_name) AS save_file_name ";
		$sql .= "FROM order_master AS a ";
		$sql .= "inner join order_prdlist AS b on (a.order_no = b.order_no) ";
		$sql .= "left join order_opt_history AS c on (b.order_no = c.order_no and b.prd_detail_no = c.prd_detail_no) ";
		$sql .= "left join order_after_history AS d on (b.order_no = d.order_no and b.prd_detail_no = d.prd_detail_no) ";
		$sql .= "left join order_img AS i on (b.order_no = i.order_no and b.prd_detail_no = i.prd_detail_no) ";
		$sql .= "inner join cate AS e on (b.cate_sortcode = e.sortcode) ";
		$sql .= "inner join cate_paper AS f on (b.cate_sortcode = f.cate_sortcode and b.cate_paper_mpcode = f.mpcode) ";
		$sql .= "inner join cate_print AS g on (b.cate_sortcode = g.cate_sortcode and b.cate_print_mpcode = g.mpcode) ";
		$sql .= "inner join prdt_print AS h on (g.prdt_print_seqno = h.prdt_print_seqno) ";
		$sql .= "WHERE a.order_no = %s AND user_id = %s AND ohash = %s ";
		$sql .= "GROUP BY b.prd_detail_no";*/

		$Sql = sprintf($sql,$param['order_no'],$param['user_id'],$param['ohash']);
		return orderListHtml($conn->Execute($Sql),$param['design_dir']);
	 }

	 function getDeliveryData($conn,$param){
		 $param = $this->parameterArrayEscape($conn,$param);
		 $sql = "select max(order_no) as order_no,sum(addtax_amnt) as delivery_amnt from order_prdlist
					where order_no=".$param['order_no']."
					and cate_sortcode='999999999'";
		 $rs = $conn->Execute($sql);
		 $rtval = array();
		 if($rs && !$rs->EOF){
			 $rtval['order_no'] = $rs->fields['order_no'];
			 $rtval['delivery_amnt'] = $rs->fields['delivery_amnt'];
		 }else{
			$rtval['order_no'] = '';
			$rtval['delivery_amnt']=0;
		 }
		 return $rtval;
	 }
	 function getDeleveryPrdno($conn,$param){
		 $param = $this->parameterArrayEscape($conn,$param);
		 $sql = "select max(order_no) as order_no,max(prd_detail_no) as prd_detail_no from order_prdlist
					where order_no=".$param['order_no']."
					and cate_sortcode='999999999'";
		 $rs = $conn->Execute($sql);
		 $rtval = array();
		 if($rs && !$rs->EOF){
			 $rtval['order_no'] = $rs->fields['order_no'];
			 $rtval['prd_detail_no'] = $rs->fields['prd_detail_no'];
		 }else{
			$rtval['order_no'] = '';
			$rtval['prd_detail_no']='';
		 }
		 return $rtval;
	 }
		/**
		* @brief Order 상품삭제
		*
		*
		*
		* @param $conn  = connection identifier
		* @param $param["order_no"]= 주문에 넣을 카트 ID
		* @param $param["user_id"] = 유저아이디
		* @param $param["prd_detail_no"] = 주문번호에속한 상품고유 키
		* @return true / false
		*/
		function addOrderDelivery($conn,$param){
			$param = $this->parameterArrayEscape($conn, $param);

			/*
			$getPrdNo = "select max(prd_detail_no)+1 new_prd_detail_no from order_prdlist where order_no = %s";
			$getPrdNo = sprintf($getPrdNo,$param['order_no']);
			$rs[0] = $conn->Execute($getPrdNo);
			$new_prd_detail_no = $rs[0]->fields['new_prd_detail_no'];
			*/
			$prdnm=$this->parameterEscape($conn,'택배배송');
			$prd_cate_cd=$this->parameterEscape($conn,'999999999');
			$inSql = "insert into order_prdlist (order_no,title,cate_sortcode,prd_count,prd_d_amnt,prd_amnt,addtax_d_amnt,addtax_amnt,c_rate,cpoint,regdate)
							value (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,now())";
			$inSql = sprintf($inSql,$param['order_no'],$prdnm,$prd_cate_cd,
									1,2500,2500,2500,2500,0,0);
			$rs[1] = $conn->Execute($inSql);
			$selSql = "select sum(tot_amnt) as tot_amnt,sum(c_amnt) as c_amnt,sum(tot_t_amnt) as tot_t_amnt , sum(p_amnt) p_amnt,sum(p_t_amnt) p_t_amnt from (
					select b.prd_detail_no,max(b.title) as title,max(e.cate_name) as cate_name,max(b.prd_amount) as prd_amount,max(b.prd_count) as prd_count,
						group_concat(distinct c.opt_name  order by c.seq asc SEPARATOR '/') as opt_name,group_concat(distinct d.after_name  order by d.seq asc SEPARATOR '/') as after,
						sum(distinct d.amnt) as after_amnt,sum(c.amnt) as opt_amnt,
						ifnull(b.prd_amnt,0) as prd_amnt,
						ifnull(b.prd_amnt,0) as tot_amnt,
						ifnull(b.addtax_amnt,0) as tot_t_amnt,
						if(b.cate_sortcode='999999999',0,ifnull(b.prd_amnt,0)) as p_amnt,
						if(b.cate_sortcode='999999999',0,ifnull(b.addtax_amnt,0)) as p_t_amnt,
						if(b.cate_sortcode = '999999999',0,ifnull(b.prd_amnt,0))+sum(ifnull(c.amnt,0))+sum(distinct ifnull(d.amnt,0))*max(ifnull(e.c_rate,0)*0.01) as c_amnt,
						max(ifnull(e.c_rate,0))
						from  order_prdlist as b
						left join order_opt_history as c on (b.order_no = c.order_no and b.prd_detail_no = c.prd_detail_no)
						left join order_after_history as d on (b.order_no = d.order_no and b.prd_detail_no = d.prd_detail_no)
						inner join cate as e on (b.cate_sortcode = e.sortcode)
						where b.order_no = ".$param['order_no']."
						group by b.prd_detail_no
						) as t";

			$rs[2] = $conn->Execute($selSql);
			$upSql = "update order_master set order_amnt = '".$rs[2]->fields['tot_amnt']."',c_point='".$rs[2]->fields['c_amnt']."',addtax_order_amnt='".$rs[2]->fields['tot_t_amnt']."',
						delivery_amnt=delivery_amnt+2500,
						prd_amnt='".$rs[2]->fields['p_amnt']."',
						addtax_prd_amnt='".$rs[2]->fields['p_t_amnt']."'
						where order_no = ".$param['order_no']."
						and ohash = ".$param['ohash']."
						and user_id = ".$param['user_id'];
			$conn->debug = 0;

			$rs[3] = $conn->Execute($upSql);
			if(in_array(false,$rs)){
				$return = "{
								\"result\" : \"false\",
								\"order_amnt\" : 0,
								\"addtax_order_amnt\" : 0,
								\"addtax_prd_amnt\" : 0
							}";

			}else{
				$return = "{
								\"result\" : \"true\",
								\"order_amnt\" : ".$rs[2]->fields['tot_amnt'].",
								\"addtax_order_amnt\" : ".$rs[2]->fields['tot_t_amnt'].",
								\"addtax_prd_amnt\" : ".$rs[2]->fields['p_t_amnt']."
							}";
			}
			return $return;
		}
		/**
		* @brief Order 상품삭제
		*
		*
		*
		* @param $conn  = connection identifier
		* @param $param["order_no"]= 주문에 넣을 카트 ID
		* @param $param["user_id"] = 유저아이디
		* @param $param["prd_detail_no"] = 주문번호에속한 상품고유 키
		* @return true / false
		*/

		/********************************************************************
		***** 주문정보 삭제
		********************************************************************/

		function delOrder($conn,$param){
			$param['user_id'] = $this->parameterEscape($conn, $param['user_id']);
			$param['ohash'] =  $this->parameterEscape($conn, $param['ohash']);
			$param['order_no'] =  $this->parameterEscape($conn, $param['order_no']);
			$param['prd_detail_no'] =  $this->parameterEscape($conn, $param['prd_detail_no']);

			if($param['type'] =='del'){
				$delivery_sql = "delivery_amnt = if (delivery_amnt = 0, 0, delivery_amnt - 2500), ";
			}

			$delPrdSql = "DELETE FROM order_prdlist ";
			$delPrdSql .= "WHERE order_no = ".$param['order_no']." AND prd_detail_no = ".$param['prd_detail_no'];
			$rs[0] = $conn->Execute($delPrdSql);

			$delOptSql = "DELETE FROM order_after_history ";
			$delOptSql .= "WHERE order_no = ".$param['order_no']." AND prd_detail_no = ".$param['prd_detail_no'];
			$rs[1] = $conn->Execute($delOptSql);


			$delAfterSql = "DELETE FROM order_opt_history ";
			$delAfterSql .= "WHERE order_no = ".$param['order_no']." AND prd_detail_no = ".$param['prd_detail_no'];
			$rs[2] = $conn->Execute($delAfterSql);

			$delImgSql = "DELETE FROM order_img ";
			$delImgSql .= "WHERE order_no = ".$param['order_no']." AND prd_detail_no = ".$param['prd_detail_no'];
			$rs[3] = $conn->Execute($delImgSql);

			/*$selSql = "select sum(tot_amnt) as tot_amnt,sum(c_amnt) as c_amnt,sum(tot_t_amnt) as tot_t_amnt , sum(p_amnt) p_amnt,sum(p_t_amnt) p_t_amnt from (
						select b.prd_detail_no,max(b.title) as title,max(e.cate_name) as cate_name,max(b.prd_amount) as prd_amount,max(b.prd_count) as prd_count,
						group_concat(distinct c.opt_name  order by c.seq asc SEPARATOR '/') as opt_name,group_concat(distinct d.after_name  order by d.seq asc SEPARATOR '/') as after,
						sum(distinct d.amnt) as after_amnt,sum(c.amnt) as opt_amnt,
						ifnull(b.prd_amnt,0) as prd_amnt,
						((ifnull(b.prd_amnt,0))+sum(ifnull(c.amnt,0))+sum(distinct ifnull(d.amnt,0))) as tot_amnt,
						((ifnull(b.addtax_amnt,0))+sum(ifnull(c.addtax_amnt,0))+sum(distinct ifnull(d.addtax_amnt,0))) as tot_t_amnt,
						((if(b.cate_sortcode='003001001',0,ifnull(b.prd_amnt,0)))+sum(ifnull(c.amnt,0))+sum(distinct ifnull(d.amnt,0))) as p_amnt,
						((if(b.cate_sortcode='003001001',0,ifnull(b.addtax_amnt,0)))+sum(ifnull(c.addtax_amnt,0))+sum(distinct ifnull(d.addtax_amnt,0))) as p_t_amnt,
						((if(b.cate_sortcode = '003001001',0,ifnull(b.prd_amnt,0)))+sum(ifnull(c.amnt,0))+sum(distinct ifnull(d.amnt,0)))*max(ifnull(e.c_rate,0)*0.01) as c_amnt,
						max(ifnull(e.c_rate,0))
						 from  order_prdlist as b
						left join order_opt_history as c on (b.order_no = c.order_no and b.prd_detail_no = c.prd_detail_no)
						left join order_after_history as d on (b.order_no = d.order_no and b.prd_detail_no = d.prd_detail_no)
						inner join cate as e on (b.cate_sortcode = e.sortcode)
						where b.order_no = ".$param['order_no']."
						group by b.prd_detail_no
						) as t";*/

			$selSql = "SELECT SUM(tot_amnt) AS tot_amnt, SUM(c_amnt) AS c_amnt, SUM(tot_t_amnt) AS tot_t_amnt , ";
			$selSql .= "SUM(p_amnt) AS p_amnt, sum(p_t_amnt) AS p_t_amnt ";
			$selSql .= "FROM (";
			$selSql .= "SELECT b.prd_detail_no, MAX(b.title) AS title, MAX(e.cate_name) AS cate_name, ";
			$selSql .= "MAX(b.prd_amount) AS prd_amount, MAX(b.prd_count) AS prd_count, ";
			$selSql .= "group_concat(DISTINCT c.opt_name ORDER BY c.seq ASC SEPARATOR '/') AS opt_name, ";
			$selSql .= "group_concat(DISTINCT d.after_name ORDER BY d.seq ASC SEPARATOR '/') AS after, ";
			$selSql .= "SUM(DISTINCT d.amnt) AS after_amnt, SUM(c.amnt) AS opt_amnt, b.prd_amnt AS prd_amnt, ";
			$selSql .= "(b.prd_amnt + SUM(ifnull(c.amnt,0)) + SUM(DISTINCT ifnull(d.amnt,0))) AS tot_amnt, ";
			$selSql .= "(b.addtax_amnt + SUM(ifnull(c.addtax_amnt, 0)) + SUM(DISTINCT ifnull(d.addtax_amnt,0))) AS tot_t_amnt, ";
			$selSql .= "((if(b.cate_sortcode='999999999', 0, b.prd_amnt)) + SUM(ifnull(c.amnt,0)) + SUM(DISTINCT ifnull(d.amnt,0))) AS p_amnt, ";
			$selSql .= "((if(b.cate_sortcode='999999999', 0, b.addtax_amnt)) + SUM(ifnull(c.addtax_amnt, 0)) + SUM(DISTINCT ifnull(d.addtax_amnt,0))) AS p_t_amnt, ";
			$selSql .= "((if(b.cate_sortcode = '999999999', 0, b.prd_amnt)) + SUM(ifnull(c.amnt,0)) + SUM(DISTINCT ifnull(d.amnt,0))) * MAX(ifnull(e.c_rate, 0) * 0.01) AS c_amnt, ";
			$selSql .= "MAX(ifnull(e.c_rate,0)) ";
			$selSql .= "FROM order_prdlist AS b ";
			$selSql .= "LEFT JOIN order_opt_history AS c on (b.order_no = c.order_no and b.prd_detail_no = c.prd_detail_no) ";
			$selSql .= "LEFT JOIN order_after_history AS d on (b.order_no = d.order_no and b.prd_detail_no = d.prd_detail_no) ";
			$selSql .= "INNER JOIN cate AS e on (b.cate_sortcode = e.sortcode) ";
			$selSql .= "WHERE b.order_no = ".$param['order_no']." ";
			$selSql .= "GROUP BY b.prd_detail_no";
			$selSql .= ") AS t";
			$rs[4] = $conn->Execute($selSql);

			$upSql = "UPDATE order_master set ";
			$upSql .= "order_amnt = '".$rs[4]->fields['tot_amnt']."', c_point='".$rs[4]->fields['c_amnt']."', ";
			$upSql .= "addtax_order_amnt='".$rs[4]->fields['tot_t_amnt']."', ".$delivery_sql." prd_amnt='".$rs[4]->fields['p_amnt']."', ";
			$upSql .= "addtax_prd_amnt='".$rs[4]->fields['p_t_amnt']."'";
			$upSql .= "WHERE order_no = ".$param['order_no']." AND ohash = ".$param['ohash']." AND user_id = ".$param['user_id'];
			$rs[5] = $conn->Execute($upSql);

			if (in_array(false, $rs)) {
				$return = "{
								\"result\" : \"false\",
								\"order_amnt\" : 0,
								\"addtax_order_amnt\" : 0,
								\"addtax_prd_amnt\" : 0
							  }";

			}else{
				$return = "{
								\"result\" : \"true\",
								\"order_amnt\" : ".$rs[4]->fields['tot_amnt'].",
								\"addtax_order_amnt\" : ".$rs[4]->fields['tot_t_amnt'].",
								\"addtax_prd_amnt\" : ".$rs[4]->fields['p_t_amnt']."
							  }";
			}

			return $return;
		}

		function getOrderMaster($conn,$param){
			$Sql = "select addtax_order_amnt as order_amnt,c_point from order_master
					where a.order_no = %s
					and user_id = %s
					and ohash = %s
					and order_etile is null
					and order_status is null
					and pay_amnt is null";
			$Sql = sprintf($Sql,$param['order_no'],
								$param['user_id'],
								$param['ohash']);
			$rs = $conn->Execute(Sql);
			if($rs && !$rs->EOF){
				$rtval['order_amnt'] = (int)$rs->fields['order_amnt'];
				$rtval['cpoint'] = (int)$rs->fields['c_point'];
			}else{
				$rtval['order_amnt'] = 0;
				$rtval['cpoint'] = 0;
			}
			return $rtval;
		}


		/********************************************************************
		***** 주문 포인트 결제
		********************************************************************/

		function setOrderPayment($conn, $param) {
			$Sql = "SELECT addtax_order_amnt as order_amnt,c_point FROM order_master ";
			$Sql .= "WHERE order_no = '".$param['order_no']."' AND user_id = '".$param['user_id']."' AND ohash = '".$param['ohash']."' ";
			$Sql .= "AND order_etime = '0000-00-00 00:00:00' AND order_status = '' AND pay_amnt = '0'";
			$rs = $conn->Execute($Sql);

			if ($rs && !$rs->EOF) {
				$rtval['order_amnt'] = (int)$rs->fields['order_amnt'];
				$rtval['cpoint'] = (int)$rs->fields['c_point'];
			} else {
				$rtval['order_amnt'] = 0;
				$rtval['cpoint'] = 0;
				return false;
			}

			if ($rtval['order_amnt'] != $param['t_amount']) {
				return false;
				exit;
			}

			$om_rs = array();
			$conn->StartTrans();

			$inSql = "INSERT INTO order_payment (";
			$inSql .= "order_no, order_pay_type, order_auth_no, order_cno, order_tran_date, order_card_no, order_issuer_nm, order_issuer_cd, ";
			$inSql .= "order_acquirer_nm, order_acquirer_cd, order_install_period, order_noint, order_bank_cd, order_bank_nm, order_account_no, ";
			$inSql .= "order_deposit_nm, order_expire_date, order_bank_deposit_time, order_cash_res_msg, order_cash_auth_no, order_cash_tran_date, ";
			$inSql .= "order_escrow_yn, order_complex_yn, order_amount, regdate";
			$inSql .= ") VALUES (";
			$inSql .= "'".$param['order_no']."', '".$param['pay_type']."', '".$param['auth_no']."', '".$param['cno']."', '".$param['tran_date']."', ";
			$inSql .= "'".$param['card_no']."', '".$param['issuer_nm']."', '".$param['issuer_cd']."', '".$param['acquirer_nm']."', ";
			$inSql .= "'".$param['acquirer_cd']."', '".$param['install_period']."', '".$param['noint']."', '".$param['bank_cd']."', '".$param['bank_nm']."', ";
			$inSql .= "'".$param['account_no']."', '".$param['deposit_nm']."', '".$param['expire_date']."', '".$param['bank_deposit_time']."', ";
			$inSql .= "'".$param['cash_res_msg']."', '".$param['cash_auth_no']."', '".$param['cash_tran_date']."', '".$param['escrow_yn']."', ";
			$inSql .= "'".$param['complex_yn']."', '".$param['amount']."', now())";

			if ($param['pay_type']) {
				$om_rs[] = $conn->Execute($inSql);
			}

			$cpInSql = "INSERT INTO order_payment (";
			$cpInSql .= "order_no, order_pay_type, plzp_id, order_amount, regdate";
			$cpInSql .= ") VALUES (";
			$cpInSql .= "'".$param['order_no']."','".$param['cp_pay_type']."','".$param['plzp_id']."','".$param['plzp_point']."',now())";

			if ($param['cp_pay_type'] && $param['plzp_id'] && $param['plzp_point']) {
				$om_rs[] = $conn->Execute($cpInSql);
			}

			$omUpSql = "UPDATE order_master SET ";
			$omUpSql .= "order_etime = now(), order_status = '100', pay_amnt = '".$param['t_amount']."' ";
			$omUpSql .= "WHERE order_no = '".$param['order_no']."' AND user_id = '".$param['user_id']."' AND ohash = '".$param['ohash']."' ";
			$omUpSql .= "AND order_etime = '0000-00-00 00:00:00' AND order_status = '' AND pay_amnt = '0'";
			$om_rs[] = $conn->Execute($omUpSql);

			$opUpSql = "UPDATE order_prdlist SET prd_status='100' WHERE order_no = '".$param['order_no']."'";
			$om_rs[] = $conn->Execute($opUpSql);

			$conn->CompleteTrans();

			if (in_array(false, $om_rs)) {
				return false;
			} else {
				return true;
			}
		}

		function orderCommentInsert($conn,$param){
			$detail_no_cnt = count($param['prd_detail_no']);
			for($i=0;$i<$detail_no_cnt;$i++){

				$upSql[$i] = "update order_prdlist set comment = '".$param['prdData'][$i]."'
							where order_no='".$param['order_no']."'
							and prd_detail_no='".$param['prd_detail_no'][$i]."'";
			}

			if($param['order_comment']){
				$oupSql = "update order_master set order_comment='".$param['order_comment']."'
							where order_no='".$param['order_no']."'
							and user_id='".$param['user_id']."'";
			}
			$rs = array();
			$conn->StartTrans();
			if($i){
				for($n=0;$n<$i;$n++){
					$rs[] = $conn->Execute($upSql[$n]);
				}
			}
			if($oupSql){
				$rs[] = $conn->Execute($oupSql);
			}
			$conn->CompleteTrans();
				if(in_array(false,$rs)){
				return false;
			}else{
				return true;
			}

		}


		/********************************************************************
		***** 주문 포인트 가져오기
		********************************************************************/

		function orderPointList($conn,$param){
			$cntSql = "SELECT count(*) AS cnt FROM order_prdlist ";
			$cntSql .= "WHERE user_id='".$param['user_id']."' AND ohash='".$param['ohash']."' AND order_no='".$param['order_no']."'";
			$cntRs = $conn->Execute($cntSql);
			$row_cnt = $cntRs['cnt'];

			$setSql = "set @mileage := %s, @acc_sum :=0, @list_sum :=0";
			$setSql = sprintf($setSql,$param['plzp_point']);
			$conn->Execute($setSql);

			$selectSql = "select max(a.order_no) as order_no,
								max(b.prd_detail_no) as prd_detail_no,
								max(ifnull(b.c_rate,0)) as c_rate,
								max(ifnull(b.c_user_rate,0)) as c_user_rate,
								(max(ifnull(b.addtax_amnt,0)) + sum(distinct ifnull(c.addtax_amnt,0)) + sum(distinct ifnull(d.addtax_amnt,0))) as order_amnt,
								(max(ifnull(b.addtax_amnt,0)) + sum(distinct ifnull(c.addtax_amnt,0)) + sum(distinct ifnull(d.addtax_amnt,0))) -
								(@mileage * round((max(ifnull(b.addtax_amnt,0)) + sum(distinct ifnull(c.addtax_amnt,0)) + sum(distinct ifnull(d.addtax_amnt,0))) / max(a.addtax_order_amnt),3))
								as pay_amnt,
								((max(ifnull(b.addtax_amnt,0)) + sum(distinct ifnull(c.addtax_amnt,0)) + sum(distinct ifnull(d.addtax_amnt,0))) -
								(@mileage * round((max(ifnull(b.addtax_amnt,0)) + sum(distinct ifnull(c.addtax_amnt,0)) + sum(distinct ifnull(d.addtax_amnt,0))) / max(a.addtax_order_amnt),3)))
								* max(ifnull(b.c_rate,0))  * 0.01
								as c_point,
								@mileage * round((max(ifnull(b.addtax_amnt,0)) + sum(distinct ifnull(c.addtax_amnt,0)) + sum(distinct ifnull(d.addtax_amnt,0))) / max(a.addtax_order_amnt),3) as used_mileage,
								min(e.cate_name) as cate_name,
								min(h.name) as print_name,min(cate_paper_mpcode) as cate_paper_mpcode,min(concat(f.name,' ',f.basisweight)) as paper_name
							from order_master as a
							inner join order_prdlist as b on (a.order_no = b.order_no)
							left join order_opt_history as c on (b.order_no = c.order_no and b.prd_detail_no = c.prd_detail_no)
							left join order_after_history as d on (b.order_no = d.order_no and b.prd_detail_no = d.prd_detail_no)
							left join cate as e on (b.cate_sortcode = e.sortcode)
							left join cate_paper as f on (b.cate_sortcode = f.cate_sortcode and b.cate_paper_mpcode = f.mpcode)
							left join cate_print as g on (b.cate_sortcode = g.cate_sortcode and b.cate_print_mpcode = g.mpcode)
							left join prdt_print as h on (g.prdt_print_seqno = h.prdt_print_seqno)
							where a.order_no ='".$param['order_no']."'
							group by b.prd_detail_no";

			$rs = $conn->Execute($selectSql);

			return $rs;
	}


	/********************************************************************
	***** 주문 마일리지 히스토리 등록
	********************************************************************/

	function insertOrderMileageHistory($conn, $param) {
		$sql = "INSERT INTO order_mileage_history (";
		$sql .= "order_no, prd_detail_no, svc_status, svc_amt, share_rate, gadd_rate, gadd_point, mysec_amt, pay_amt, used_mysec, starttime";
		$sql .= ") values (";
		$sql .= "'".$param['order_no']."', '".$param['prd_detail_no']."', '".$param['svc_status']."', '".$param['svc_amt']."', '".$param['share_rate']."', ";
		$sql .= "'".$param['gadd_rate']."', '".$param['gadd_point']."', '".$param['mysec_amt']."', '".$param['pay_amt']."', '".$param['used_mysec']."', now())";

		return $conn->Execute($sql);

	}


	/********************************************************************
	***** 주문 마일리지 히스토리 수정
	********************************************************************/

	function updateOrderMileageHistory($conn, $param) {
		$param = $this->parameterArrayEscape($conn, $param);

		$sql = "UPDATE order_mileage_history SET ";
		$sql .= "res_cd=".$param['res_cd'].", res_msg=".$param['res_msg'].", tran_no=".$param['tran_no'].", endtime = now()";
		$sql .= "WHERE order_no = ".$param['order_no']." AND prd_detail_no = ".$param['prd_detail_no'];

		return $conn->Execute($sql);
	}

}
?>


