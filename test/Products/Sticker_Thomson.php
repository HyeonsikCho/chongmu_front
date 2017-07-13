<?php
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/Common/PrintoutInterface.php');
//include_once($_SERVER["DOCUMENT_ROOT"] .'/test/Products/Product.php');

class Sticker_Thomson extends Sticker implements PrintoutInterface
{
    /**
     * @var string
     */
    var $amt;
    var $size_w, $size_h;
    var $papers;
    var $options;
    var $afterprocesses;
    function makeHtml() {
        $html = '<h2>명함주문</h2></br></br>';
        $html .= $this->makePaperOption();
        $html .= $this->makePrintOption();
        $html .= $this->makeSizeOption();
        $html .= $this->makeAmtOption();
        $html .= $this->makeOptOption();
        $html .= $this->makeAfterOption();
        $html .= '<input type="hidden" id="sortcode" name="sortcode" value="' . $this->sortcode .'"></br></br>';
        $html .= '<input type="hidden" id="opt_name_list" name="opt_name_list" value="">';
        $html .= '<input type="hidden" id="opt_mp_list" name="opt_mp_list" value="">';
        $html .= '<input type="hidden" id="after_name_list" name="after_name_list" value="">';
        $html .= '<input type="hidden" id="after_mp_list" name="after_mp_list" value="">';
        $html .= '<span id="total_price">0원</span>';
        return $html;
    }

    function makeKindOption() {
        $price_info_arr = array();
        $price_info_arr['cate_sortcode'] = $this->sortcode;
        $param = array();
        $param['cate_sortcode'] = $this->sortcode;

        $kind = $this->dao->selectCateKindHtml($this->conn, $this->sortcode, $price_info_arr);

        return $kind;
    }
}

?>