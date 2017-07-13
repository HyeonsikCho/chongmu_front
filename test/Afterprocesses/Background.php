<?php
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Afterprocess.php');

class Background extends Afterprocess
{
    function makeHtml() {
        $html = "background";
        return $html;
    }

    function getName() {
        $html = "background";
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
        <div class="option _background">
            <dl>
                <dt>빼다(배경)</dt>
                <dd id="background_price_dd" class="price" style="position: absolute; padding: 0 10px;"></dd>
                <dd>
                    <select id="background_val" onchange="background_click();">
                        $option
                    </select>
                </dd>
                <input type="hidden" id="background_price" name="background_price" value="" />
            </dl>
        </div>
html;

        return $html;
    }
}

?>