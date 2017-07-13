<?php
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Afterprocess.php');

class Rounding extends Afterprocess
{
    function makeHtml() {
        $html = 'Rounding';
        return $html;
    }

    function getName() {
        $html = 'rounding';
        return $html;
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
        $attr = null;
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
        <div class="option _rounding">
            <dl>
                <dt>귀도리</dt>
                <dd class="price" id="rounding_price_dd"></dd>
                <dd>
                    <select class="_num" id="rounding" onchange="loadRoundingDepth2(this.value);">
                        $depth1_option
                    </select>
                    <select id="rounding_val" name="rounding_val" onchange="getAfterPrice.common('rounding');">
                        $depth2_option
                    </select>
                </dd>
                <dd class="br">
                    <label class="left top"><input name="rounding_dvs" value="좌상" type="checkbox"> 좌상</label>
                    <label class="right top"><input name="rounding_dvs" value="우상" type="checkbox"> 우상</label>
                    <label class="right bottom"><input name="rounding_dvs" value="우하" type="checkbox"> 우하</label>
                    <label class="left bottom"><input name="rounding_dvs" value="좌하" type="checkbox"> 좌하</label>
                    <input type="hidden" id="rounding_info" name="rounding_info" value="" />
                    <input type="hidden" id="rounding_price" name="rounding_price" value="" />
                </dd>
            </dl>
        </div>
html;

        return $html;
    }
}

?>