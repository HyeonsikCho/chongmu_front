<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductNcDAO.php");


abstract class Product {
	var $name;
	var $sortcode;

	var $dao;
	var $connectionPool;
	var $util;
	var $conn;

	function __construct($sortcode) {
		$this->sortcode = $sortcode;
		$this->connectionPool = new ConnectionPool();
		$this->conn = $this->connectionPool->getPooledConnection();
		$this->dao = new ProductNcDAO();
		$this->util = new FrontCommonUtil();
		$this->name = "";
	}

	function makePaperOption() {
		$price_info_arr = array();
		$param = array();

		$paper = $this->dao->selectCatePaperHtml($this->conn, $this->sortcode, $price_info_arr);
		$html = '<select id="paper_cover" name="paper_cover" onchange="changePaper();">' . $paper['info'] . '</select>';

		return $html;
	}

	function makePrintOption() {
		$price_info_arr = array();
		$price_info_arr['cate_sortcode'] = $this->sortcode;
		$param = array();
		$param['cate_sortcode'] = $this->sortcode;
		$print_tmpt = $this->dao->selectCatePrintTmptHtml($this->conn, $param, $price_info_arr);
		$print_tmpt = $print_tmpt["단면"] . $print_tmpt["양면"];

		$print_purp = $this->dao->selectCatePrintPurpHtml($this->conn, $this->sortcode);

		$html = '인쇄도수 : <select id="print" name="print" onchange="changeData();">' . $print_tmpt . '</select>';
		$html .= '<select id="print_purp" name="print_purp_cover " style="display:none">' . $print_purp .'</select>';

		return $html;
	}

	function makePrintTmptOption() {
		$price_info_arr = array();
		$price_info_arr['cate_sortcode'] = $this->sortcode;
		$param = array();
		$param['cate_sortcode'] = $this->sortcode;
		$print_tmpt = $this->dao->selectCatePrintTmptHtml($this->conn, $param, $price_info_arr);
		$print_tmpt = $print_tmpt["단면"] . $print_tmpt["양면"];

		$html = '<select id="bef_tmpt_cover" name="bef_tmpt_cover" onchange="changeData();">' . $print_tmpt . '</select>';

		return $html;
	}

	function makePrintPurpOption() {
		$price_info_arr = array();
		$price_info_arr['cate_sortcode'] = $this->sortcode;
		$param = array();
		$param['cate_sortcode'] = $this->sortcode;

		$print_purp = $this->dao->selectCatePrintPurpHtml($this->conn, $this->sortcode);

		$html = '<select id="print_purp" name="print_purp_cover " style="display:none">' . $print_purp .'</select>';

		return $html;
	}


	function makeSizeOption() {
		$price_info_arr = array();
		$price_info_arr['cate_sortcode'] = $this->sortcode;
		$param = array();
		$param['cate_sortcode'] = $this->sortcode;

		$size = $this->dao->selectCateSizeHtml($this->conn, $this->sortcode, $price_info_arr);
		$html = '<select class="_preset" id="size" name="size" onchange="setAmt();" def_cut_wid="'.$price_info_arr['def_cut_wid'].'" def_cut_vert="' . $price_info_arr['def_cut_vert'] . '" stan_mpcode="' . $price_info_arr['stan_mpcode'] . '">' . $size . '</select>';

		return $html;
	}

	function makeAmtOption() {
		$price_info_arr = array();
		$price_info_arr['cate_sortcode'] = $this->sortcode;
		$param = array();
		$param['cate_sortcode'] = $this->sortcode;
		$param["table_name"]    = 'ply_price_gp';
		$param["amt_unit"]      = '장';
		$amt = $this->dao->selectCateAmtHtml($this->conn, $param, $price_info_arr);

		$html =  '수량 : <select id="amt" name="amt" onchange="changeData();">' . $amt . '</select>';
		return $html;
	}

	function makeOptOption() {
		$opt = $this->dao->selectCateOptHtml($this->conn, $this->sortcode);
		$add_opt = $opt["info_arr"]["name"];
		$add_opt = $this->dao->parameterArrayEscape($this->conn, $add_opt);
		//$add_opt = $this->util->arr2delimStr($add_opt);

		$param = array();
		$param["cate_sortcode"] = $this->sortcode;
		$param["opt_name"]      = $add_opt;
		$param["opt_idx"]       = $opt["info_arr"]["idx"];
		$add_opt = $this->dao->selectCateAddOptInfoHtml($this->conn, $param);

		$html = '<dd class="_folder list">' . $opt['html'] .$add_opt . '</dd>';
		return $html;
	}

	function makeAfterOption() {
		$after = $this->dao->selectCateAfterHtml($this->conn, $this->sortcode);
		$add_after = $after["info_arr"]["add"];
		$add_after = $this->dao->parameterArrayEscape($this->conn, $add_after);
		$str_add_after  = $this->util->arr2delimStr($add_after, '|');

		$param = array();
		$param["cate_sortcode"] = $this->sortcode;
		$param["after_name"]      = $str_add_after;

		$rs = $this->dao->selectCateAddAfterInfo($this->conn, $param);

		$arr_after = $this->parseResultQuery($rs);

		$factory = new DPrintingFactory();

		$after_html = '';
		if($add_after === false)
			return $after_html;

		for($i = 0 ; $i < count($add_after); $i++) {
			$add_after[$i] = str_replace("'", "", $add_after[$i]);
			$after_tmp = $this;
			$after_tmp = $factory->createAfter($after_tmp, $add_after[$i]);
			$after_html .= $after_tmp->makeAfterHtml($arr_after[$add_after[$i]]);
		}

		$after_html = '<dd class="_folder list">' . $after['html'] .$after_html . '</dd>';

		return $after_html;
	}

	function parseResultQuery($rs) {
		$ret = "";

		$info_arr = array();
		$key_form = "%s!%s!%s!%s";

		$i = 0;
		while($rs && !$rs->EOF) {
			$mpcode = $rs->fields["mpcode"];

			$name   = $rs->fields["after_name"];
			$depth1 = $rs->fields["depth1"];
			$depth2 = $rs->fields["depth2"];
			$depth3 = $rs->fields["depth3"];

			$key = sprintf($key_form, $name, $depth1, $depth2, $depth3);

			if ($info_arr[$name] === null) {
				$i = 0;
			}

			$info_arr[$name][$i]["mpcode"] = $mpcode;
			$info_arr[$name][$i]["depth1"] = $depth1;
			$info_arr[$name][$i]["depth2"] = $depth2;
			$info_arr[$name][$i++]["depth3"] = $depth3;

			$rs->MoveNext();
		}

		unset($rs);

		return $info_arr;
	}

	function getDescription() {
		return $this->name;
	}

	function makeHtml() {return "";}

	function cost() {

	}
}