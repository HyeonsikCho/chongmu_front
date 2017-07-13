<?php
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Afterprocess.php');

class Bonding extends Afterprocess
{
    function makeHtml() {
        $html = "Bonding";
        return $html;
    }

    function getName() {
        $html = "Bonding";
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
        <div class="option _bonding">
            <dl>
                <dt>접착</dt>
                <dd id="bonding_price_dd" class="price" style="position: absolute; padding: 0 10px;"></dd>
                <dd>
                    <select id="bonding" onchange="loadBindingDepth2(this.value);">
                        $depth1_option
                    </select>
                    <select id="bonding_val" onchange="getAfterPrice.common('binding');">
                        $depth2_option
                    </select>
                </dd>
                <input type="hidden" id="bonding_price" name="bonding_price" value="" />
            </dl>
        </div>
html;

        return $html;
    }
}

?>