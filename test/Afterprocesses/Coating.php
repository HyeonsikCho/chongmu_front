<?php
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Afterprocess.php');

class Coating extends Afterprocess
{
    function makeHtml() {
        $html = "Coating";
        return $html;
    }

    function getName() {
        $html = "coating";
        return $html;
    }

    function makeAfterHtml($info) {
        $util = new FrontCommonUtil();

        $info_count = count($info);

        $option = "";
        for ($i = 0; $i < $info_count; $i++) {
            $temp = $info[$i];

            $mpcode = $temp["mpcode"];
            $aft = $util->getOptAfterFullName($temp);

            $class = "";

            if (strpos($aft, "부분") !== false) {
                $class = "class=\"_part\"";
            }

            $option .= option($mpcode, $aft, $attr);
        }

        $html = <<<html
        <div class="option _coating">
            <dl>
                <dt>코팅</dt>
                <dd class="price" id="coating_price_dd"></dd>
                <dd>
                    <select id="coating_val" name="coating_val" onchange="getAfterPrice.common('coating');">
                        $option
                    </select>
                    <p class="note _part">File에 부분코팅 부분을 먹1도로 업로드 해주세요.</p>
                    <input type="hidden" id="coating_price" name="coating_price" value="" />
                </dd>
            </dl>
        </div>
html;

        return $html;
    }
}

?>