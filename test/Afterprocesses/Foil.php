<?php
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Afterprocess.php');

class Foil extends Afterprocess
{
    var $bef_foil_mpcode;
    var $aft_foil_mpcode;

    function makeHtml() {
        $html = 'Foil';
    }

    function getName() {
        $html = 'foil';
        return $html;
    }

    function setAfterprocess($sortcode ,$after_name, $amt, $count, $mpcode = '', $depth1='', $depth2='', $depth3 ='')
    {
        $param = array();
        $param['cate_sortcode'] = $sortcode;
        $amt = intval($amt);
        $param['amt'] = $amt;
        $depth1 = explode(',', $depth1); // 박 이름
        $depth2 = explode(',', $depth2);; // 박1 구체정보 (면, 가로, 세로)
        $depth3 = explode(',', $depth3);; // 박2 구체정보 (면, 가로, 세로)
        $param['after_mpcode'] = $mpcode;
        $param['after_name'] = $after_name;

        $aft_1 = $depth1[0];
        $aft_2 = $depth1[1];

        $dvs_1 = $depth2[0];
        $wid_1 = intval($depth2[1]);
        $vert_1 = intval($depth2[2]);

        $dvs_2 = $depth3[0];
        $wid_2 = intval($depth3[1]);
        $vert_2 = intval($depth3[2]);

        if ($wid_1 < 20) {
            $wid_1 = 20;
        }

        if($vert_1 < 20) {
            $vert_1 = 20;
        }

        if ($wid_2 < 20) {
            $wid_2 = 20;
        }

        if($vert_2 < 20) {
            $vert_2 = 20;
        }

        if (!empty($aft_1)) {
            $param["depth1"] = $aft_1;
            $this->bef_foil_mpcode = $this->dao->selectCateAfterInfo($this->conn, $param)
                ->fields["mpcode"];
        }
        if (!empty($aft_2)) {
            $param["depth1"] = $aft_2;
            $this->aft_foil_mpcode = $this->dao->selectCateAfterInfo($this->conn, $param)
                ->fields["mpcode"];
        }

        $sum = 0;
        if ($dvs_1 === "양면") {
            // 양면같음
            $param["after_name"] = $this->getFoilAfterName($aft_1);
            $param["dvs"] = "양면";

            $price = $this->dao->selectAfterFoilPressPrice($this->conn, $param);
            $price = $price->fields['sell_price'];

            $wid_1 = $this->calcAreaVal($wid_1, $amt);
            $vert_1 = $this->calcAreaVal($vert_1, $amt);

            $sum = $price + $wid_1 + $vert_1;
        } else if (!empty($aft_1) && !empty($dvs_1) &&
            empty($aft_2) && empty($dvs_2)
        ) {
            // 전면만
            $param["after_name"] = $this->getFoilAfterName($aft_1);
            $param["dvs"] = "단면";

            $price = $this->dao->selectAfterFoilPressPrice($this->conn, $param);
            $price = $price->fields['sell_price'];

            $wid_1 = $this->calcAreaVal($wid_1, $amt);
            $vert_1 = $this->calcAreaVal($vert_1, $amt);

            $sum = $price + $wid_1 + $vert_1;
        } else if (empty($aft_1) && empty($dvs_1) &&
            !empty($aft_2) && !empty($dvs_2)
        ) {
            // 후면만
            $param["after_name"] = $this->getFoilAfterName($aft_2);
            $param["dvs"] = "단면";

            $price = $this->dao->selectAfterFoilPressPrice($this->conn, $param);
            $price = $price->fields['sell_price'];

            $wid_2 = $this->calcAreaVal($wid_2, $amt);
            $vert_2 = $this->calcAreaVal($vert_2, $amt);

            $sum = $price + $wid_2 + $vert_2;
        } else if (!empty($aft_1) && !empty($dvs_1) &&
            !empty($aft_2) && !empty($dvs_2)
        ) {
            // 양면다름
            $param["after_name"] = $this->getFoilAfterName($aft_1);
            $param["dvs"] = "단면";

            $bef_price = $this->dao->selectAfterFoilPressPrice($this->conn, $param);
            $bef_price = $bef_price->fields['sell_price'];

            $param["after_name"] = $this->getFoilAfterName($aft_2);
            $aft_price = $this->dao->selectAfterFoilPressPrice($this->conn, $param);
            $aft_price = $aft_price->fields['sell_price'];

            $wid_1 = $this->calcAreaVal($wid_1, $amt);
            $vert_1 = $this->calcAreaVal($vert_1, $amt);
            $wid_2 = $this->calcAreaVal($wid_2, $amt);
            $vert_2 = $this->calcAreaVal($vert_2, $amt);

            $sum = $bef_price + $aft_price + $wid_1 + $vert_1 + $wid_2 + $vert_2;
        }
        $this->price = $sum;
        $this->name = $after_name;
        $this->count = $count;
    }

    function makeAfterHtml($info) {
        $info_count = count($info);

        $dup_chk = array();

        $opt1 = '';
        $opt2 = '';

        for ($i = 0; $i < $info_count; $i++) {
            $temp = $info[$i];
            $depth1 = $temp["depth1"];
            $attr = '';

            if ($i === 0) {
                $attr = "selected";
            }

            if ($dup_chk[$depth1] === null) {
                $dup_chk[$depth1] = true;
                $opt1 .= option($depth1, $depth1, $attr);
                $opt2 .= option($depth1, $depth1);
            }
        }

        $html = <<<html
        <div class="option _foil">
            <dl>
                <dt>박</dt>
                <dd id="foil_price_dd" class="price" style="position: absolute; padding: 0 10px;"></dd>
                <dd>
                    <select id="foil_1" style="width:85px;" onchange="foilAreaInit( this.value, '1');">
                        <option value="">-</option>
                        $opt1
                    </select>
                    <select id="foil_dvs_1" style="min-width:60px;" onchange="changeFoilDvs( this.value);">
                        <option value="">-</option>
                        <option value="전면" selected>전면</option>
                        <option value="양면">양면</option>
                    </select>
                    &nbsp;/&nbsp;
                    <select id="foil_2" style="width:85px;" onchange="foilAreaInit(this.value, '2');">
                        <option value="">-</option>
                        $opt2
                    </select>
                    <select id="foil_dvs_2" style="min-width:60px;" onchange="getAfterPrice.common('foil');">
                        <option value="">-</option>
                        <option value="후면">후면</option>
                    </select>
                    <input type="hidden" id="foil_val_1" name="foil_val" value="" />
                    <input type="hidden" id="foil_val_2" name="foil_val_2" value="" />
                    <input type="hidden" id="foil_info" name="foil_info" value="" />
                    <input type="hidden" id="foil_price" name="foil_price" value="" />
                </dd>
                <dd class="br">
                    <label>가로 <input id="foil_wid_1" type="text" class="mm" onblur="getAfterPrice.common('foil');">mm</label>
                    <label>세로 <input id="foil_vert_1" type="text" class="mm" onblur="getAfterPrice.common('foil');">mm</label>
                    &nbsp;/&nbsp;
                    <label>가로 <input id="foil_wid_2" type="text" class="mm" onblur="getAfterPrice.common('foil');">mm</label>
                    <label>세로 <input id="foil_vert_2" type="text" class="mm" onblur="getAfterPrice.common('foil');">mm</label>
                </dd>
                <dd class="br note">
                    File에 박 부분을 먹1도로 업로드 해주세요.
                </dd>
            </dl>
        </div>
html;

        return $html;
    }

    /**
     * @brief 박일 때 금박유광 이런식으로 넘어오는 이름을 금박만 반환
     *
     * @param $aft = 넘어온 후공정명
     *
     * @return 잘라낸 후공정명
     */
    function getFoilAfterName($aft) {
        if (strpos($aft, "금박") !== false) {
            return "금박";
        } else if (strpos($aft, "녹박") !== false) {
            return "녹박";
        } else if (strpos($aft, "먹박") !== false) {
            return "먹박";
        } else if (strpos($aft, "은박") !== false) {
            return "은박";
        } else if (strpos($aft, "적박") !== false) {
            return "적박";
        } else if (strpos($aft, "청박") !== false) {
            return "청박";
        } else {
            return "금박";
        }
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

    function getJson() {
        return ",\"" . $this->getName() . "\" : \"" . $this->costEach() . "\", \"foil_aft_mpcode\" : \"".$this->aft_foil_mpcode . "\", \"foil_bef_mpcode\" : \"".$this->bef_foil_mpcode . "\"";
    }
}

?>