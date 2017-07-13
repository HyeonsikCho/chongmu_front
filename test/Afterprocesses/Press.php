<?php
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Afterprocess.php');

class Press extends Afterprocess
{
    function makeHtml() {
        $html = 'Press';
        return $html;
    }

    function getName() {
        $html = 'press';
        return $html;
    }

    function setAfterprocess($sortcode ,$after_name, $amt, $count, $mpcode = '', $depth1='', $depth2='', $depth3 ='') {
        $param = array();
        $param['cate_sortcode'] = $sortcode;
        $amt = intval($amt);
        $param['amt'] = $amt;
        $depth1 = explode(',', $depth1); // 가로,세로
        $param['after_mpcode'] = $mpcode;
        $param['after_name'] = '형압';
        $param['dvs'] = '단면';

        $wid_1 = intval($depth1[0]);
        $vert_1 = intval($depth1[1]);

        if ($wid_1 < 20) {
            $wid_1  = 20;
        }

        if($vert_1 < 20) {
            $vert_1 = 20;
        }

        $param["after_name"] = "형압";
        $param["dvs"]        = "단면";

        $price = $this->dao->selectAfterFoilPressPrice($this->conn, $param);
        $price = $price->fields['sell_price'];

        $wid_1  = $this->calcAreaVal($wid_1, $amt);
        $vert_1 = $this->calcAreaVal($vert_1, $amt);

        $this->price = $price + $wid_1 + $vert_1;
        $this->name = $after_name;
        $this->count = $count;
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
        <div class="option _press">
            <dl>
                <dt>형압</dt>
                <dd id="press_price_dd" class="price" style="position: absolute; padding: 0 10px;"></dd>
                <dd>
                    <select id="press_1" onchange="loadPressDepth2(this.value);">
                        $depth1_option
                    </select>
                    <select id="press_val" onchange="getAfterPrice.common('press');">
                        $depth2_option
                    </select>
                    <input type="hidden" id="press_info" name="press_info" value="" />
                    <input type="hidden" id="press_price" name="press_price" value="" />
                </dd>
                <dd class="br">
                    <label>가로 <input id="press_wid_1" type="text" class="mm"  onblur="getAfterPrice.common('press');">mm</label>
                    <label>세로 <input id="press_vert_1" type="text" class="mm" onblur="getAfterPrice.common('press');">mm</label>
                </dd>
                <dd class="br note">
                    File에 형압 부분을 먹1도로 업로드 해주세요.
                </dd>
            </dl>
        </div>
html;

        return $html;
    }

    /**
     * @brief 각 너비 가중값 계산
     *
     * @param $val = 너비/높이값
     * @param $amt = 수량
     *
     * @return 계산값
     */
    function calcAreaVal($val, $amt) {
        return (($val / 10) - 2) * 10 * $amt;
    }
}

?>