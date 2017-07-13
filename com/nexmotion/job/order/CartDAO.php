<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/OrderCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/order/cartListHTML.php');

class CartDAO extends OrderCommonDAO {

		function __construct() {
		}
    /**
     * @brief 장바구니에 사용할 시퀀스생성
     * @param $conn  = connection identifier
     * @return sequence
     */
	function getCartSequence($conn){
		$seqSql = "insert into cart_sequence values (null)";
		if(!$conn->Execute($seqSql)){
			return false;
		}
		$getSeqSql = "select last_insert_id() as seq";
		if(!$idx = $conn->Execute($getSeqSql)){
			return false;
		}else{
			$seq = $idx->fields["seq"];
		}
		return $seq;
	 }
    /**
     * @brief 장바구니 기본상품 insert
     * @param $conn  = connection identifier
	 * @param $param["user_id"] = 유저아이디
     * @return true / false
     */
	function setCartPrdlist($conn,$param){
		$param = $this->parameterArrayEscape($conn, $param);
		$Sql = "insert into cart_prdlist (cart_prdlist_id,
											user_id,
											title,
											cate_sortcode,
											cate_print_mpcode,
											cate_paper_mpcode,
											cate_stan_mpcode,
											cate_stan_type,
											stan_cal,
											work_size_wid,
											work_size_vert,
											cut_size_wid,
											cut_size_vert,
											tomson_size_wid,
											tomson_size_vert,
											prd_amount,
											prd_count,
											cart_d_amnt,
											cart_amnt,
											addtax_d_amnt,
											addtax_amnt,
											c_rate,
											c_user_rate,
											cpoint,
											direct_flag,
											detail,
											regdate) value (
											%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,now())";
		$Sql = sprintf($Sql,    $param['cart_prdlist_id'],
							    $param['user_id'],
								$param['title'],
								$param['cate_sortcode'],
								$param['cate_print_mpcode'],
								$param['cate_paper_mpcode'],
								$param['cate_stan_mpcode'],
								$param['cate_stan_type'],
								$param['stan_cal'],
								$param['work_size_wid'],
								$param['work_size_vert'],
								$param['cut_size_wid'],
								$param['cut_size_vert'],
								$param['tomson_size_wid'],
								$param['tomson_size_vert'],
								$param['prd_amount'],
								$param['prd_count'],
								$param['cart_d_amnt'],
								$param['cart_amnt'],
								$param['addtax_d_amnt'],
								$param['addtax_amnt'],
								$param['c_rate'],
								$param['c_user_rate'],
								$param['cpoint'],
								$param['direct_flag'],
								$param['paper_detail']);
		$rs = $conn->Execute($Sql);
		return $rs;
	}
    /**
     * @brief 장바구니 옵션 insert
     * @param $conn  = connection identifier
	 * @param $param["user_id"] = 유저아이디
     * @return true / false
     */
	function setCartOptlist($conn,$param){
		$param = $this->parameterArrayEscape($conn, $param);
		$Sql = "insert into cart_opt_history (cart_prdlist_id,
											opt_name,
											mpcode,
											basic_yn,
											price_effect_flag,
											depth1,
											depth2,
											depth3,
											prd_count,
											d_amnt,
											amnt,
											addtax_d_amnt,
											addtax_amnt,
											c_rate,
											c_user_rate,
											cpoint,
											seq,
											detail,
											direct_flag,
											regdate) values %s";
		$tempSql = "(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,now())";
		$loopCnt = count($param['opt']);

		for($i=0;$i < $loopCnt;$i++){
			$inSql .= sprintf($tempSql, $param['cart_prdlist_id'],
								$param['opt'][$i]['name'],
								$param['opt'][$i]['mpcode'],
								$param['opt'][$i]['basic_yn'],
								"'Y'",
								$param['opt'][$i]['depth1'],
								$param['opt'][$i]['depth2'],
								$param['opt'][$i]['depth3'],
								$param['prd_count'],
								$param['opt'][$i]['d_amnt'],
								$param['opt'][$i]['amnt'],
								$param['opt'][$i]['addtax_d_amnt'],
								$param['opt'][$i]['addtax_amnt'],
								$param['c_rate'],
								$param['c_user_rate'],
								$param['opt'][$i]['cpoint'],
								$i,
								$param['opt'][$i]['detail'],
								$param['direct_flag']);
			if ($i !== $loopCnt - 1) {
				$inSql .= ",";
			}

		}
		$Sql = sprintf($Sql,$inSql);
		$rs = $conn->Execute($Sql);
		return $rs;
	}
    /**
     * @brief 장바구니 후공정 insert
     * @param $conn  = connection identifier
	 * @param $param["user_id"] = 유저아이디
     * @return true / false
     */
	function setCartAfterlist($conn,$param){
		$param = $this->parameterArrayEscape($conn, $param);
		$Sql = "insert into cart_after_history (cart_prdlist_id,
											after_name,
											mpcode,
											basic_yn,
											price_effect_flag,
											depth1,
											depth2,
											depth3,
											prd_count,
											d_amnt,
											amnt,
											addtax_d_amnt,
											addtax_amnt,
											c_rate,
											c_user_rate,
											cpoint,
											seq,
											detail,
											direct_flag,
											regdate) values %s";
		$tempSql = "(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,now())";
		$loopCnt = count($param['after']);

		for($i=0;$i < $loopCnt;$i++){
			$inSql .= sprintf($tempSql, $param['cart_prdlist_id'],
								$param['after'][$i]['name'],
								$param['after'][$i]['mpcode'],
								$param['after'][$i]['basic_yn'],
								"'Y'",
								$param['after'][$i]['depth1'],
								$param['after'][$i]['depth2'],
								$param['after'][$i]['depth3'],
								$param['prd_count'],
								$param['after'][$i]['d_amnt'],
								$param['after'][$i]['amnt'],
								$param['after'][$i]['addtax_d_amnt'],
								$param['after'][$i]['addtax_amnt'],
								$param['c_rate'],
								$param['c_user_rate'],
								$param['after'][$i]['cpoint'],
								$i,
								$param['after'][$i]['detail'],
								$param['direct_flag']);
			if ($i !== $loopCnt - 1) {
				$inSql .= ",";
			}

		}
		$Sql = sprintf($Sql,$inSql);
		$rs = $conn->Execute($Sql);
		return $rs;
	}
    /**
     * @brief 장바구니 조회
     * @param $conn  = connection identifier
	 * @param $param["user_id"] = 유저아이디
     * @return true / false
     */
	 function getCartList($conn, $param){
        if ($this->connectionCheck($conn) === false) {
            return false;
        }
		$param['user_id'] = $this->parameterEscape($conn, $param['user_id']);

		$Sql = "select  group_concat(distinct opt_name order by c.seq asc SEPARATOR ' / ')as opt_name,group_concat(distinct after_name order by b.seq asc SEPARATOR ' / ')as after_name,cart_prdlist_seq,
				max(user_id) as user_id,max(a.title) as title,max(a.cart_prdlist_id) as cart_prdlist_id,min(a.cate_sortcode) as cate_sortcode,
				min(d.cate_name) as cate_name,
				min(cate_print_mpcode) as cate_print_mpcode,min(g.name) as print_name,min(cate_paper_mpcode) as cate_paper_mpcode,min(concat(e.name,' ',e.basisweight)) as paper_name,
				min(cate_stan_type) as cate_stan_type,min(stan_cal) as stan_cal,min(work_size_wid) as work_size_wid,min(work_size_vert) as work_size_vert,
				min(cut_size_wid) as cut_size_wid,min(cut_size_vert) as cut_size_vert,min(tomson_size_wid) as tomson_size_wid,
				min(tomson_size_vert) as tomson_size_vert,min(prd_amount) as prd_amount,
				sum(distinct b.amnt) as after_amnt,sum(c.amnt) as opt_amnt,ifnull(a.addtax_amnt,0) tot_amnt,
				ifnull(a.cart_amnt,0) as cart_amnt,max(a.regdate) as regdate,max(a.prd_count) as prd_count
				 from cart_prdlist as a
				left join cart_after_history as b on (a.cart_prdlist_id = b.cart_prdlist_id)
				left join cart_opt_history as c on (a.cart_prdlist_id = c.cart_prdlist_id)
				inner join cate as d on (a.cate_sortcode = d.sortcode)
				inner join cate_paper as e on (a.cate_sortcode = e.cate_sortcode and a.cate_paper_mpcode = e.mpcode)
				inner join cate_print as f on (a.cate_sortcode = f.cate_sortcode and a.cate_print_mpcode = f.mpcode)
				inner join prdt_print as g on (f.prdt_print_seqno = g.prdt_print_seqno)
				where user_id=".$param['user_id']."
				group by cart_prdlist_seq";
		 //$conn->debug = 1;
		return cartListHTML($conn->Execute($Sql),$param['design_dir']);

	 }

    /**
     * @brief 기본 가격조회
     *
     * @param $conn  = connection identifier
	 * @param $param["cate_sortcode"] = 카테고리코드
	 * @param $param["cate_paper_mpcode"] = 종이매핑코드
	 * @param $param["cate_print_mpcode"] = 인쇄매핑코드
	 * @param $param["cate_stan_mpcode"] = 규격매핑코드
	 * @param $param["amt"] = 매수
     * @return resultSet
     */
	 function getCatePrice($conn,$param){
        if ($this->connectionCheck($conn) === false) {
            return false;
        }
		//$param = $this->parameterArrayEscape($conn, $param);
		$query = "";
		if($param["flag"] == 'N') { //책자형
			$query = "select sum_price as price from sum_price_gp
					where cate_sortcode = %s
					and cate_paper_mpcode = %s
					and cate_beforeside_print_mpcode = %s
					and cate_stan_mpcode = %s
					and amt = %s
					and page = %s
					and affil =  '%s'";

			$query  = sprintf($query,
								$param['cate_sortcode'],
								$param['cate_paper_mpcode'],
								$param['cate_print_mpcode'],
								$param['cate_stan_mpcode'],
								$param['prd_count'],
								$param['prd_amount'],
								$param['affil']);
		} else {
			$query = "select new_price as price from ply_price_gp
					where cate_sortcode = %s
					and cate_paper_mpcode = %s
					and cate_beforeside_print_mpcode = %s
					and cate_stan_mpcode = %s
					and (amt + 0) >= %s
					and new_price != 0
					ORDER BY  amt ASC
					LIMIT 1 ";

			$query  = sprintf($query,
								$param['cate_sortcode'],
								$param['cate_paper_mpcode'],
								$param['cate_print_mpcode'],
								$param['cate_stan_mpcode'],
								$param['prd_amount']);
		}

		$rs = $conn->Execute($query);
		if($rs && !$rs->EOF) {
			return $rs->fields['price'];
		}else{
			return false;
		}
	 }
    /**
     * @brief 인쇄도수 매핑코드 조회
     *
     * @param $conn  = connection identifier
	 * @param $param["cate_sortcode"] = 카테고리코드
	 * @param $param["print_name"] = 종이매핑코드
     * @return mpcode
     */
	 function getPrintMpcode($conn,$param){
        if ($this->connectionCheck($conn) === false) {
            return false;
        }
		$param = $this->parameterArrayEscape($conn, $param);

		$query = "select a.mpcode as mpcode from cate_print as a
					inner join prdt_print as b on (a.prdt_print_seqno = b.prdt_print_seqno)
					where a.cate_sortcode = %s
					and b.name = %s";
		$query  = sprintf($query,
							$param['cate_sortcode'],
							$param['print_name']);
		//$conn->debug=1;
		$rs = $conn->Execute($query);
		//$conn->debug = 0;
		if($rs && !$rs->EOF) {
			return $rs->fields['mpcode'];
		}else{
			return false;
		}
	 }
    /**
     * @brief 옵션가격  조회
     *
     * @param $conn  = connection identifier
	 * @param $param["cate_sortcode"] = 카테고리코드
	 * @param $param["opt_mpcode"] = 옵션매핑코드
     * @return return option price sum value
     */
	 function getOptList($conn,$param){
        if ($this->connectionCheck($conn) === false) {
            return false;
        }
		$param = $this->parameterArrayEscape($conn, $param);
		$count = count($param["opt_mpcode"]);
		for ($i = 0; $i < $count; $i++) {
			$inSql .= $param["opt_mpcode"][$i];

			if ($i !== $count - 1) {
				$inSql .= ",";
			}
		}
		$query = "select opt_name ,depth1,depth2,depth3,mpcode,basic_yn,sell_price  from cate_opt as a
					inner join cate_opt_price as b on (a.mpcode = b.cate_opt_mpcode)
					inner join prdt_opt as c on (a.prdt_opt_seqno = c.prdt_opt_seqno)
					where a.cate_sortcode=%s
					and mpcode in (%s)";
		$query  = sprintf($query,
							$param['cate_sortcode'],
							$inSql);
		$rs =  $conn->Execute($query);
		return $rs;

	 }

    /**
     * @brief 후공정가격  조회
     *
     * @param $conn  = connection identifier
	 * @param $param["cate_sortcode"] = 카테고리코드
	 * @param $param["after_mpcode"] = 옵션매핑코드
     * @return return option price sum value
     */
	 function getAfterList($conn,$param){
		 $cnt = intval($param['count']);
        if ($this->connectionCheck($conn) === false) {
            return false;
        }
		$param = $this->parameterArrayEscape($conn, $param);
		$count = count($param["after_mpcode"]);
		for ($i = 0; $i < $count; $i++) {
			$inSql .= $param["after_mpcode"][$i];

			if ($i !== $count - 1) {
				$inSql .= ",";
			}
		}
		//$conn->debug=1;
		$query = "select after_name,depth1,depth2,depth3,mpcode,basic_yn  from cate_after as a
					inner join prdt_after as c on (a.prdt_after_seqno = c.prdt_after_seqno)
					where a.cate_sortcode=%s
					and mpcode in (%s)";
		$query  = sprintf($query,
							$param['cate_sortcode'],
							$inSql);

		 $rs = $conn->Execute($query);

		return $rs;
	 }
}
?>
