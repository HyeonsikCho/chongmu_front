<?php
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Afterprocess.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/product_info_class.php");

class Dotline extends Afterprocess
{
    function makeHtml() {
        $html = 'Dotline';
        return $html;
    }

    function getName() {
        $html = 'dotline';
        return $html;
    }

    function setAfterprocess($sortcode ,$after_name, $amt, $count, $mpcode = '', $depth1='', $depth2='', $depth3 ='') {
        if(substr($sortcode,0,3) == "007") {
            $per_price = ProductInfoClass::NCR_DOTLINE_PER_PRICE[$depth2];
            $price = 0;
            if($depth1 == "one" || $depth1 == "two") {
                //올림(amt / 위에서 구한값) * 28700
                $price = ceil($amt / $per_price) * 28700;
            } else {
                //올림(amt / 위에서 구한값) * 46000
                $price = ceil($amt / $per_price) * 46000;
            }
            $this->price = $price;
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

        // html 생성하기 쉽도록 배열 가공
        $html_info = array();

        $option = "";
        for ($i = 0; $i < $info_count; $i++) {
            $temp = $info[$i];

            $mpcode = $temp["mpcode"];
            $depth1 = $temp["depth1"];
            $depth2 = $temp["depth2"];

            $html_info[$depth1][$depth2] = $mpcode;
        }

        $is_one   = false;
        $is_two   = false;
        $is_three = false;
        $is_four  = false;
        $is_five  = false;

        // 1줄
        $one_mpcode1 = $html_info["1줄"]["중앙"];
        $one_mpcode2 = $html_info["1줄"]["비중앙"];
        if (!empty($one_mpcode1) || !empty($one_mpcode_2)) {
            $is_one   = true;
        }
        // 2줄
        $two_mpcode1 = $html_info["2줄"]["비례3단"];
        $two_mpcode2 = $html_info["2줄"]["비대칭다단"];
        if (!empty($two_mpcode1) || !empty($two_mpcode_2)) {
            $is_two   = true;
        }
        // 3줄
        $three_mpcode1 = $html_info["3줄"]["비례4단"];
        $three_mpcode2 = $html_info["3줄"]["비대칭다단"];
        if (!empty($three_mpcode1) || !empty($three_mpcode_2)) {
            $is_three = true;
        }
        // 4줄
        $four_mpcode1 = $html_info["4줄"]["비례5단"];
        $four_mpcode2 = $html_info["4줄"]["비대칭다단"];
        if (!empty($four_mpcode1) || !empty($four_mpcode_2)) {
            $is_four  = true;
        }
        // 5줄
        $five_mpcode1 = $html_info["5줄"]["비례6단"];
        $five_mpcode2 = $html_info["5줄"]["비대칭다단"];
        if (!empty($five_mpcode1) || !empty($five_mpcode_2)) {
            $is_five  = true;
        }

        // 조건때문에 doc에서 str로 변경함
        $html  = "    <div class=\"option _dotline\">";
        $html .= "        <dl>";
        $html .= "            <dt>미싱</dt>";
        $html .= "            <dd id=\"dotline_price_dd\" class=\"price\"></dd>";
        $html .= "            <dd>";
        $html .= "                <select id=\"dotline_cnt\" onchange=\"getAfterPrice.common('dotline');\">";
        if ($is_one) {
            $html .= "                    <option value=\"one\" class=\"_one\">1줄</option>";
        }
        if ($is_two) {
            $html .= "                    <option value=\"two\" class=\"_two\">2줄</option>";
        }
        if ($is_three) {
            $html .= "                    <option value=\"three\" class=\"_three\">3줄</option>";
        }
        if ($is_four) {
            $html .= "                    <option value=\"four\" class=\"_four\">4줄</option>";
        }
        if ($is_five) {
            $html .= "                    <option value=\"five\" class=\"_five\">5줄</option>";
        }
        $html .= "                </select>";
        $html .= "            </dd>";
        // 1줄
        $html .= "            <dd class=\"br _one\" style=\"display:none\">";
        if (isset($one_mpcode1)) {
            $html .= "                <label><input type=\"radio\" name=\"dotline_one_val\" onclick=\"getAfterPrice.common('dotline');\" value=\"{$one_mpcode1}\" dvs=\"M\" checked> 중앙</label>";
        }
        if (isset($one_mpcode2)) {
            $html .= "                <label><input type=\"radio\" name=\"dotline_one_val\" onclick=\"getAfterPrice.common('dotline');\" value=\"{$one_mpcode2}\" class=\"_custom\" dvs=\"C\"> 비중앙</label>";
        }
        $html .= "            </dd>";
        if (isset($one_mpcode2)) {
            $html .= "            <dd class=\"br _one _custom\" style=\"display:none\">";
            $html .= "                <label>첫 번째 선 <input id=\"dotline_one_pos1\" type=\"text\" class=\"mm\">mm</label>";
            $html .= "            </dd>";
        }

        // 2줄
        $html .= "            <dd class=\"br _two\" style=\"display:none\">";
        if (isset($two_mpcode1)) {
            $html .= "                <label><input type=\"radio\" name=\"dotline_two_val\" onclick=\"getAfterPrice.common('dotline');\" value=\"{$two_mpcode1}\" dvs=\"M\" checked> 비례3단</label>";
        }
        if (isset($two_mpcode2)) {
            $html .= "                <label><input type=\"radio\" name=\"dotline_two_val\" onclick=\"getAfterPrice.common('dotline');\" value=\"{$two_mpcode2}\" class=\"_custom\" dvs=\"C\"> 비대칭다단</label>";
        }
        $html .= "            </dd>";
        if (isset($two_mpcode2)) {
            $html .= "            <dd class=\"br _two _custom\" style=\"display:none\">";
            $html .= "                <label>첫 번째 선 <input id=\"dotline_two_pos1\" type=\"text\" class=\"mm\">mm</label><br />";
            $html .= "                <label>두 번째 선 <input id=\"dotline_two_pos2\" type=\"text\" class=\"mm\">mm</label>";
            $html .= "            </dd>";
        }

        // 3줄
        $html .= "            <dd class=\"br _three\" style=\"display:none\">";
        if (isset($three_mpcode1)) {
            $html .= "                <label><input type=\"radio\" name=\"dotline_three_val\" onclick=\"getAfterPrice.common('dotline');\" value=\"$three_mpcode1\" dvs=\"M\" checked> 비례4단</label>";
        }
        if (isset($three_mpcode2)) {
            $html .= "                <label><input type=\"radio\" name=\"dotline_three_val\" onclick=\"getAfterPrice.common('dotline');\" value=\"$three_mpcode2\" class=\"_custom\" dvs=\"C\"> 비대칭다단</label>";
        }
        $html .= "            </dd>";
        if (isset($three_mpcode2)) {
            $html .= "            <dd class=\"br _three _custom\" style=\"display:none\">";
            $html .= "                <label>첫 번째 선 <input id=\"dotline_three_pos1\" type=\"text\" class=\"mm\">mm</label><br />";
            $html .= "                <label>두 번째 선 <input id=\"dotline_three_pos2\" type=\"text\" class=\"mm\">mm</label><br />";
            $html .= "                <label>세 번째 선 <input id=\"dotline_three_pos3\" type=\"text\" class=\"mm\">mm</label>";
            $html .= "            </dd>";
        }

        // 4줄
        $html .= "            <dd class=\"br _four\" style=\"display:none\">";
        if (isset($four_mpcode1)) {
            $html .= "                <label><input type=\"radio\" name=\"dotline_four_val\" onclick=\"getAfterPrice.common('dotline');\" value=\"$four_mpcode1\" dvs=\"M\" checked> 비례5단</label>";
        }
        if (isset($four_mpcode2)) {
            $html .= "                <label><input type=\"radio\" name=\"dotline_four_val\" onclick=\"getAfterPrice.common('dotline');\" value=\"$four_mpcode2\" class=\"_custom\" dvs=\"C\"> 비대칭다단</label>";
        }
        $html .= "            </dd>";
        if (isset($four_mpcode2)) {
            $html .= "            <dd class=\"br _four _custom\" style=\"display:none\">";
            $html .= "                <label>첫 번째 선 <input id=\"dotline_four_pos1\" type=\"text\" class=\"mm\">mm</label><br />";
            $html .= "                <label>두 번째 선 <input id=\"dotline_four_pos2\" type=\"text\" class=\"mm\">mm</label><br />";
            $html .= "                <label>세 번째 선 <input id=\"dotline_four_pos3\" type=\"text\" class=\"mm\">mm</label><br />";
            $html .= "                <label>네 번째 선 <input id=\"dotline_four_pos4\" type=\"text\" class=\"mm\">mm</label>";
            $html .= "            </dd>";
        }

        // 5줄
        $html .= "            <dd class=\"br _five\" style=\"display:none\">";
        if (isset($five_mpcode1)) {
            $html .= "                <label><input type=\"radio\" name=\"dotline_five_val\" onclick=\"getAfterPrice.common('dotline');\" value=\"$five_mpcode1\" dvs=\"M\" checked> 비례6단</label>";
        }
        if (isset($five_mpcode2)) {
            $html .= "                <label><input type=\"radio\" name=\"dotline_five_val\" onclick=\"getAfterPrice.common('dotline');\" value=\"$five_mpcode2\" class=\"_custom\" dvs=\"C\"> 비대칭다단</label>";
        }
        $html .= "            </dd>";
        if (isset($five_mpcode2)) {
            $html .= "            <dd class=\"br _five _custom\" style=\"display:none\">";
            $html .= "                <label>첫 번째 선 <input id=\"dotline_five_pos1\" type=\"text\" class=\"mm\">mm</label><br />";
            $html .= "                <label>두 번째 선 <input id=\"dotline_five_pos2\" type=\"text\" class=\"mm\">mm</label><br />";
            $html .= "                <label>세 번째 선 <input id=\"dotline_five_pos3\" type=\"text\" class=\"mm\">mm</label><br />";
            $html .= "                <label>네 번째 선 <input id=\"dotline_five_pos4\" type=\"text\" class=\"mm\">mm</label>";
            $html .= "                <label>다섯 번째 선 <input id=\"dotline_five_pos4\" type=\"text\" class=\"mm\">mm</label>";
            $html .= "            </dd>";
        }
        $html .= "            <input type=\"hidden\" id=\"dotline_price\" name=\"dotline_price\" value=\"\" />";
        $html .= "            <input type=\"hidden\" id=\"dotline_info\" name=\"dotline_info\" value=\"\" />";
        $html .= "            <input type=\"hidden\" id=\"dotline_val\" name=\"dotline_val\" value=\"\" />";
        $html .= "        </dl>";
        $html .= "    </div>";

        return $html;
    }
}

?>