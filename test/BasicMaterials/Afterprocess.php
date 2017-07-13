<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ErpCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductNcDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/Common/CondimentDecorator.php');

abstract class Afterprocess extends CondimentDecorator {
	var $product;
	var $name;
	var $price;
	var $depth1;
	var $depth2;
	var $depth3;
	var $count;

	var $dao;
	var $connectionPool;
	var $util;
	var $conn;

	function __construct($product) {
		$this->product = $product;
		$this->dao = new ProductDAO();
		$connectionPool = new ConnectionPool();
		$this->conn = $connectionPool->getPooledConnection();
	}

	function getDescription() {
		return $this->product->getDescription() . "후공정 : " . $this->name . "(" .$this->price . ")</br>";
	}

	function setAfterprocess($sortcode ,$after_name, $amt, $count, $mpcode = '', $depth1='', $depth2='', $depth3 ='') {
		$param = array();
		$param['cate_sortcode'] = $sortcode;
		$param['amt'] = $amt;
		$param['depth1'] = $depth1;
		$param['depth2'] = $depth2;
		$param['depth3'] = $depth3;
		$param['after_mpcode'] = $mpcode;

		if($param['after_mpcode'] == '') {
			$param['after_mpcode'] = $this->getAfterMpcode($param);
		}

		if($param['after_mpcode']) {
			$rs_price = $this->dao->selectCateAftPriceList($this->conn, $param);
			$this->price = $this->getPrice($rs_price);
			$this->name = $after_name;
			$this->amt = $amt;
			$this->count = $count;
		} else {
			$this->price = 0;
			$this->name = '상품정보 없음';
		}
	}

	function getAfterMpcode($param) {
		$rs = $this->dao->selectCateAftInfo($this->conn, 'SEQ' ,$param);
		return $rs->fields['mpcode'];
	}

	function getPrice($rs_price) {
		return $rs_price->fields['sell_price'];
	}

	function makeHtml() { return 'after'; }

	function getName() { return 'after'; }

	abstract function makeAfterHtml($info);

	function cost() {
		return $this->costEach() + $this->product->cost();
	}

	function costEach() {
		return $this->price * $this->count;
	}

	function getJson() {
		return ",\"" . $this->getName() . "\" : \"" . $this->costEach() . "\"";
	}
}

?>
