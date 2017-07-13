<?php
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Afterprocess.php');

class Punching extends Afterprocess
{
    function makeHtml() {
        $html = 'Punching';
        return $html;
    }

    function getName() {
        $html = 'punching';
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
        $i = 1;
        foreach ($merge_arr as $depth1 => $depth2_arr) {
            $attr = "";

            if ($flag === true) {
                $flag = false;

                foreach ($depth2_arr as $depth2 => $mpcode) {
                    $depth2_option .= option($mpcode, $depth2);
                }

                $attr = "selected=\"selected\"";
            }

            $depth1_option .= option($i++, $depth1, $attr);
        }

        $html = <<<html
        <div class="option _punching">
            <dl>
                <dt>타공</dt>
                <dd id="punching_price_dd" class="price"></dd>
                <dd>
                    <select class="_num" id="punching" onchange="loadPunchingDepth2(this.value);">
                        $depth1_option
                    </select>
                    <select id="punching_val" onchange="getAfterPrice.common('punching');">
                        $depth2_option
                    </select>
                </dd>
                <dd class="br _on" style="display:none">
                    첫 번째 타공 위치
                    <label>가로 <input type="text" id="punching_pos_w1" class="mm">mm</label>
                    <label>세로 <input type="text" id="punching_pos_h1" class="mm">mm</label>
                </dd>
                <dd class="br" style="display:none">
                    두 번째 타공 위치
                    <label>가로 <input type="text" id="punching_pos_w2" class="mm">mm</label>
                    <label>세로 <input type="text" id="punching_pos_h2" class="mm">mm</label>
                </dd>
                <dd class="br" style="display:none">
                    세 번째 타공 위치
                    <label>가로 <input type="text" id="punching_pos_w3" class="mm">mm</label>
                    <label>세로 <input type="text" id="punching_pos_h3" class="mm">mm</label>
                 </dd>
                 <dd class="br" style="display:none">
                    네 번째 타공 위치
                    <label>가로 <input type="text" id="punching_pos_w4" class="mm">mm</label>
                    <label>세로 <input type="text" id="punching_pos_h4" class="mm">mm</label>
                </dd>
                <dd class="br note" style="display:none">
                    File에 타공 부분을 먹1도로 업로드 해주세요.
                </dd>
                <input type="hidden" id="punching_price" name="punching_price" value="" />
                <input type="hidden" id="punching_info" name="punching_info" value="" />
            </dl>
        </div>
html;

        return $html;
    }
}

?>