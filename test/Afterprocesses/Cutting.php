<?php
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Afterprocess.php');

class Cutting extends Afterprocess
{
    function makeHtml() {
        $html = "Cutting";
        return $html;
    }

    function getName() {
        $html = "cutting";
        return $html;
    }

    function makeAfterHtml($info) {
        $util = new FrontCommonUtil();

        $info_count = count($info);

        $option = "";
        for ($i = 0; $i < $info_count; $i++) {
            $temp = $info[$i];

            $mpcode = $temp["mpcode"];
            $dvs = $util->getOptAfterFullName($temp);

            $class = "";

            if (strpos($dvs, "부분") !== false) {
                $class = "class=\"_part\"";
            }

            $option .= option($mpcode, $dvs, $attr);
        }
        $html = <<<html
        <div class="option _cutting">
            <dl>
                <dt>재단</dt>
                <dd id="cutting_price_dd" class="price"></dd>
                <dd>
                    <select id="cutting_val" name="cutting_val" onchange="getAfterPrice.common('cutting');">
                         $option
                    </select>
				<input type="hidden" id="cutting_price" name="cutting_price" value="" />
                </dd>
                <dd class="br">
                    <label>가로 <input type="text" class="mm"></label>
                    <label>세로 <input type="text" class="mm"></label>
                    <label><input type="text" class="mm"> 등분</label>
                </dd>
            </dl>
        </div>
html;

        return $html;
    }
}

?>