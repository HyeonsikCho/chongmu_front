<?php
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Afterprocess.php');

class Numbering extends Afterprocess
{
    function makeHtml() {
        $html = "numbering";
        return $html;
    }

    function getName() {
        $html = "numbering";
        return $html;
    }

    function setAfterprocess($sortcode ,$after_name, $amt, $count, $mpcode = '', $depth1='', $depth2='', $depth3 ='') {
        if(substr($sortcode,0,3) == "007") {
            $per_price = ProductInfoClass::NCR_DOTLINE_PER_PRICE[$depth2];
            $this->price = ceil($amt / $per_price) * 57500;
            $this->name = $after_name;
            $this->amt = $amt;
            $this->count = $count;
        } else {
            $param = array();
            $param['cate_sortcode'] = $sortcode;
            $param['amt'] = $amt;
            $param['depth1'] = $depth1;
            $param['depth2'] = $depth2;
            $param['depth3'] = $depth3;
            $param['after_mpcode'] = $mpcode;

            if ($param['after_mpcode'] == '') {
                $param['after_mpcode'] = $this->getAfterMpcode($param);
            }

            if ($param['after_mpcode']) {
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
    }

    function makeAfterHtml($info) {
        $info_count = count($info);

        $merge_arr = array();

        for ($i = 0; $i < $info_count; $i++) {
            $temp = $info[$i];
            $depth1 = $temp["depth1"];
            $depth2 = $temp["depth2"];
            $mpcode = $temp["mpcode"];

            $merge_arr[$depth1][$depth2] = $mpcode;
        }

        $depth1_option = "";
        $depth2_option = "";

        $flag = true;
        foreach ($merge_arr as $depth1 => $depth2_arr) {
            $attr = "";

            if ($flag === true) {
                $flag = false;

                foreach ($depth2_arr as $depth2 => $mpcode) {
                    $depth2_option .= option($mpcode, $depth2);
                }

                $attr = "selected=\"selected\"";
            }

            $depth1_option .= option($depth1, $depth1, $attr);
        }

        $html = <<<html
        <div class="option _numbering">
            <dl>
                <dt>넘버링</dt>
                <dd id="numbering_price_dd" class="price" style="position: absolute; padding: 0 10px;"></dd>
                <dd>
                    <select id="numbering" onchange="loadBindingDepth2(this.value);" style="display:none;">
                        $depth1_option
                    </select>
                    <select id="numbering_val" onchange="getAfterPrice.common('numbering');">
                        $depth2_option
                    </select>
                </dd>
                <input type="hidden" id="numbering_price" name="numbering_price" value="" />
            </dl>
        </div>
html;

        return $html;
    }
}

?>