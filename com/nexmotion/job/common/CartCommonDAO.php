<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/CommonDAO.php');

class CartCommonDAO extends CommonDAO {
    function __construct() {
    }

    /**
     * @brief 장바구니 삭제
     *
     * $param["table_name"] = 장바구니 관련 테이블명
     *
     * @param $conn  = connection identifier
     * @param $param["prk_val"][] = 삭제될 키값
	 * @param $param["user_id"] = 유저아이디
     * @return true / false
     */
	 function delCart($conn,$param){
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

		$delPrdSql = "delete from cart_prdlist where
						cart_prdlist_id in ($query)
						and user_id = '".$param['user_id']."'";

		$delOptSql = "delete from cart_opt_history where
						cart_prdlist_id in ($query)";

		$delAfterSql = "delete from cart_after_history where
						cart_prdlist_id in ($query)";

		$rs[] = $conn->Execute($delPrdSql);
		$rs[] = $conn->Execute($delOptSql);
		$rs[] = $conn->Execute($delAfterSql);


		if(in_array(false,$rs)){
			return false;
		}else{
			return true;
		}
	 }
    /**
     * @brief
     *
     * $param["table_name"] = 장바구니 관련 테이블명
     *
     * @param $conn  = connection identifier
     * @param $param["prk_val"][] = 삭제될 키값
	 * @param $param["user_id"] = 유저아이디
     * @return true / false
     */


	  function setOrderDelCart($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return falses;
        }

		$delPrdSql = "DELETE FROM cart_prdlist WHERE cart_prdlist_id IN (".trim($param["prkVal"]).") AND user_id = '".trim($param['user_id'])."'";
		$rs[] = $conn->Execute($delPrdSql);

		$delOptSql = "DELETE FROM cart_opt_history WHERE cart_prdlist_id IN (".trim($param["prkVal"]).")";
		$rs[] = $conn->Execute($delOptSql);

		$delAfterSql = "DELETE FROM cart_after_history WHERE cart_prdlist_id IN (".trim($param["prkVal"]).")";
		$rs[] = $conn->Execute($delAfterSql);

		if (in_array(false,$rs)) {
			return false;
		} else {
			return true;
		}
	 }

}
?>
