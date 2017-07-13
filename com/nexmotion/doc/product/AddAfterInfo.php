<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");

/**
 * @brief 추가 후공정 코팅 정보 html 반환
 *
 * @param $info = 코팅 정보
 *
 * @return div html
 */
function getCoatingInfoHtml($info) {
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

/**
 * @brief 추가 후공정 귀도리 정보 html 반환
 *
 * @param
 *
 * @return div html
 */
function getRoundingInfoHtml($info) {
    // <option class="_all">네귀도리</option>

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

/**
 * @brief 추가 후공정 오시 정보 html 반환
 *
 * @return div html
 */
function getImpressionInfoHtml() {
    $html = <<<html
        <div class="option _impression">
            <dl>
                <dt>오시</dt>
                <dd class="price" id="impression_price_dd"></dd>
                <dd>
                    <select id="impression" onchange="getAfterPrice.common('impression');">
                        <option value="1줄" class="_one">1줄</option>
                        <option value="2줄" class="_two">2줄</option>
                        <option value="3줄" class="_three">3줄</option>
                        <option value="4줄" class="_four">4줄</option>
                    </select>
                </dd>
                <dd class="br _one">
                    <label><input type="radio" name="impressionOne"> 중앙2단</label>
                    <label><input type="radio" name="impressionOne" class="_custom"> 비중앙2단</label>
                </dd>
                <dd class="br _one _custom">
                    <label>첫번째 선 <input id="impression_one_val1" type="text" class="mm">mm</label>
                </dd>
                <dd class="br _two">
                    <label><input type="radio" name="impressionTwo"> 비례3단</label>
                    <label><input type="radio" name="impressionTwo"> 십자4단</label>
                    <label><input type="radio" name="impressionTwo" class="_custom"> 비대칭다단</label>
                </dd>
                <dd class="br _two _custom">
                    <label>첫번째 선 <input id="impression_two_val1" type="text" class="mm">mm</label>
                    <label>두번째 선 <input id="impression_two_val2" type="text" class="mm">mm</label>
                </dd>
                <dd class="br _three">
                    <label><input type="radio" name="impressionThree"> 비례4단</label>
                    <label><input type="radio" name="impressionThree" class="_custom"> 비대칭다단</label>
                </dd>
                <dd class="br _three _custom">
                    <label>세번째 선 <input id="impression_three_val3" type="text" class="mm">mm</label>
                    <label>두번째 선 <input id="impression_three_val2" type="text" class="mm">mm</label>
                    <label>첫번째 선 <input id="impression_three_val1" type="text" class="mm">mm</label>
                </dd>
                <dd class="br _four">
                    <label><input type="radio" name="impressionFour"> 비례5단</label>
                    <label><input type="radio" name="impressionFour" class="_custom"> 비대칭다단</label>
                </dd>
                <dd class="br _four _custom">
                    <label>첫번째 선 <input id="impression_four_val1" type="text" class="mm">mm</label>
                    <label>두번째 선 <input id="impression_four_val2" type="text" class="mm">mm</label>
                    <label>세번째 선 <input id="impression_four_val3" type="text" class="mm">mm</label>
                    <label>네번째 선 <input id="impression_four_val4" type="text" class="mm">mm</label>
                </dd>
                <input type="hidden" id="impression_info" name="impression_info" value="" />
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 미싱 정보 html 반환
 *
 * @return div html
 */
function getDotlineInfoHtml() {
    $html = <<<html
        <div class="option _dotline">
            <dl>
                <dt>미싱</dt>
                <dd id="dotline_price_dd" class="price"></dd>
                <dd>
                    <select id="dotline" onchange="getAfterPrice.common('dotline');">
                        <option value="1줄" class="_one">1줄</option>
                        <option value="2줄" class="_two">2줄</option>
                        <option value="3줄" class="_three">3줄</option>
                        <option value="4줄" class="_four">4줄</option>
                    </select>
                </dd>
                <dd class="br _one">
                    <label><input type="radio" name="dotlineOne"> 중앙2단</label>
                    <label><input type="radio" name="dotlineOne" class="_custom"> 비중앙2단</label>
                </dd>
                <dd class="br _one _custom">
                    <label>첫번째 선 <input id="dotline_one_val1" type="text" class="mm">mm</label>
                </dd>
                <dd class="br _two">
                    <label><input type="radio" name="dotlineTwo"> 비례3단</label>
                    <label><input type="radio" name="dotlineTwo"> 십자4단</label>
                    <label><input type="radio" name="dotlineTwo" class="_custom"> 비대칭다단</label>
                </dd>
                <dd class="br _two _custom">
                    <label>첫번째 선 <input id="dotline_two_val1" type="text" class="mm">mm</label>
                    <label>두번째 선 <input id="dotline_two_val2" type="text" class="mm">mm</label>
                </dd>
                <dd class="br _three">
                    <label><input type="radio" name="dotlineThree"> 비례4단</label>
                    <label><input type="radio" name="dotlineThree" class="_custom"> 비대칭다단</label>
                </dd>
                <dd class="br _three _custom">
                    <label>세번째 선 <input id="dotline_three_val3" type="text" class="mm">mm</label>
                    <label>두번째 선 <input id="dotline_three_val2" type="text" class="mm">mm</label>
                    <label>첫번째 선 <input id="dotline_three_val1" type="text" class="mm">mm</label>
                </dd>
                <dd class="br _four">
                    <label><input type="radio" name="dotlineFour"> 비례5단</label>
                    <label><input type="radio" name="dotlineFour" class="_custom"> 비대칭다단</label>
                </dd>
                <dd class="br _four _custom">
                    <label>첫번째 선 <input id="dotline_four_val1" type="text" class="mm">mm</label>
                    <label>두번째 선 <input id="dotline_four_val2" type="text" class="mm">mm</label>
                    <label>세번째 선 <input id="dotline_four_val3" type="text" class="mm">mm</label>
                    <label>네번째 선 <input id="dotline_four_val4" type="text" class="mm">mm</label>
                </dd>
                <input type="hidden" id="dotline_info" name="dotline_info" value="" />
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 타공 정보 html 반환
 *
 * @return div html
 */
function getPunchingInfoHtml($info) {
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
                    <select class="_num" id="punching" onchange="loadPunchingVal();">
                        $depth1_option
                    </select>
                    <select id="punching_val" onchange="getAfterPrice.common('punching');">
                        $depth2_option
                    </select>
                </dd>
                <dd class="br">
                    첫번째 타공 위치
                    <label>가로 <input type="text" id="punching_one_w" class="mm">mm</label>
                    <label>세로 <input type="text" id="punching_one_h" class="mm">mm</label>
                </dd>
                <dd class="br">
                    두번째 타공 위치
                    <label>가로 <input type="text" id="punching_two_w" class="mm">mm</label>
                    <label>세로 <input type="text" id="punching_two_h" class="mm">mm</label>
                </dd>
                <dd class="br">
                    세번째 타공 위치
                    <label>가로 <input type="text" id="punching_three_w" class="mm">mm</label>
                    <label>세로 <input type="text" id="punching_three_h" class="mm">mm</label>
                 </dd>
                 <dd class="br">
                    네번째 타공 위치
                    <label>가로 <input type="text" id="punching_four_w" class="mm">mm</label>
                    <label>세로 <input type="text" id="punching_four_h" class="mm">mm</label>
                </dd>
                <dd class="br note">
                    File에 타공 부분을 먹1도로 업로드 해주세요.
                </dd>
                <input type="hidden" id="punching_price" name="punching_price" value="" />
                <input type="hidden" id="punching_info" name="punching_info" value="" />
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 접지 정보 html 반환
 *
 * @return div html
 */
function getFoldlineInfoHtml() {
    // <option class="_col2">2단 접지</option>
    $html = <<<html
        <div class="option _foldline">
            <dl>
                <dt>접지</dt>
                <dd id="foldline_price_dd" class="price"></dd>
                <dd>
                    <select id="foldline" onchange="getAfterPrice.common('foldline');">
                        %s
                    </select>
                </dd>
                <dd class="br _col2">
                    <label><input type="radio" name="foldlineCol2"> 중앙</label>
                    <label><input type="radio" name="foldlineCol2" class="_custom"> 비중앙</label>
                </dd>
                <dd class="br _col2">
                    <label>첫번째 선 <input type="text" class="mm">mm</label>
                </dd>
                <dd class="br _col3">
                    <label><input type="radio" name="foldlineCol3"> 정접지</label>
                    <label><input type="radio" name="foldlineCol3"> 정접지 후 반접지</label>
                    <label><input type="radio" name="foldlineCol3"> N접지</label>
                    <label><input type="radio" name="foldlineCol3"> N접지 후 반접지</label><br>
                    <label><input type="radio" name="foldlineCol3"> 반접지 후 정접지</label>
                    <label><input type="radio" name="foldlineCol3"> 반접지 후 정접지</label>
                    <label><input type="radio" name="foldlineCol3"> 반접지 후 N접지</label>
                </dd>
                <dd class="br _col4">
                    <label><input type="radio" name="foldlineCol4"> 정접지</label>
                    <label><input type="radio" name="foldlineCol4"> 정접지 후 반접지</label>
                    <label><input type="radio" name="foldlineCol4"> 병풍접지</label>
                    <label><input type="radio" name="foldlineCol4"> 병풍접지 후 반접지</label>
                    <label><input type="radio" name="foldlineCol4"> 대문접지</label><br>
                    <label><input type="radio" name="foldlineCol4"> 두루마리접지</label>
                    <label><input type="radio" name="foldlineCol4"> 십자접지</label>
                </dd>
                <dd class="br _col5">
                    <label><input type="radio"> 병풍접지</label>
                </dd>
                <dd class="br _col6">
                    <label><input type="radio"> 병풍접지</label>
                </dd>
                <input type="hidden" id="folding_info" name="folding_info" value="" />
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 엠보싱 정보 html 반환
 *
 * @return div html
 */
function getEmbossingInfoHtml() {
    $html = <<<html
        <div class="option _embossing">
            <dl>
                <dt>엠보싱</dt>
                <dd id="embossing_price_dd" class="price"></dd>
                <dd>
                    <select id="embossing" onchange="getAfterPrice('foldline', this.value);">
                        %s
                    </select>
                </dd>
                <dd class="br">
                    <label><input type="radio" name="embossing_val" value=""> 먹</label>
                    <label><input type="radio" name="embossing_val" value=""> 칼라</label>
                </dd>
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 박 정보 html 반환
 *
 * @param $info = 박 정보
 *
 * @return div html
 */
function getFoilInfoHtml($info) {
    $util = new FrontCommonUtil();

    $info_count = count($info);

    $option = "";
    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];

        $mpcode = $temp["mpcode"];
        $dvs = $util->getOptAfterFullName($temp);

        $option .= option($mpcode, $dvs);
    }

    $html = <<<html
        <div class="option _foil">
            <dl>
                <dt>박</dt>
                <dd id="foil_price_dd" class="price"></dd>
                <dd>
                    <select id="foil" onchange="getAfterPrice.common('foil');">
                        $option
                    </select>
                    <select id="foil_dvs">
                        <option value="bef">전면</option>
                        <option value="aft">후면</option>
                        <option value="all">양면</option>
                    </select>
                    <input type="hidden" id="foil_val" name="foil_val" value="" />
                </dd>
                <dd class="br">
                    <label>가로 <input id="foil_val_wid" type="text" class="mm">mm</label>
                    <label>세로 <input id="foil_val_vert" type="text" class="mm">mm</label>
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
 * @brief 추가 후공정 형압 정보 html 반환
 *
 * @return div html
 */
function getPressInfoHtml() {
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
                <dd id="press_price_dd" class="price"></dd>
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
 * @brief 추가 후공정 도무송 정보 html 반환
 *
 * @return div html
 */
function getTomsonInfoHtml() {
    $html = <<<html
        <div class="option _thomson">
            <dl>
                <dt>도무송</dt>
                <dd id="tomson_price_dd" class="price"></dd>
                <dd>
                    <select id="tomson" onchange="getAfterPrice('tomson', this.value);">
                        %s
                        <!--
                        <option value="type1">보험도무송</option>
                        <option value="type2">원형도무송</option>
                        <option value="type3">정사각도무송</option>
                        <option value="type4">반원도무송</option>
                        <option value="type5">하트도무송</option>
                        <option value="type6">직사각도무송</option>
                        <option value="type7">타원도무송</option>
                        <option value="type8">비디오/카세트</option>
                        <option value="type9">자유형도무송</option>
                        -->
                    </select>
                </dd>
                <dd class="br _type1">
                    <select id="tomson_val">
                        <option>보험-5017</option>
                        <option>보험-5020</option>
                        <option>보험-5524</option>
                        <option>보험-5533</option>
                    </select>
                </dd>
                <!--
                <dd class="br _type2">
                    <select>
                        <option>C-10</option>
                        <option>C-11</option>
                        <option>C-12</option>
                        <option>C-13</option>
                        <option>C-14</option>
                        <option>C-15</option>
                        <option>C-16</option>
                        <option>C-17</option>
                        <option>C-18</option>
                        <option>C-19</option>
                        <option>C-20</option>
                        <option>C-21</option>
                        <option>C-22</option>
                        <option>C-25</option>
                        <option>C-28</option>
                        <option>C-30</option>
                        <option>C-33</option>
                        <option>C-35</option>
                        <option>C-38</option>
                        <option>C-40</option>
                        <option>C-43</option>
                        <option>C-45</option>
                        <option>C-48</option>
                        <option>C-50</option>
                        <option>C-55</option>
                        <option>C-60</option>
                        <option>C-65</option>
                    </select>
                </dd>
                <dd class="br _type3">
                    <select>
                        <option>S-10</option>
                        <option>S-11</option>
                        <option>S-12</option>
                        <option>S-13</option>
                        <option>S-14</option>
                        <option>S-15</option>
                        <option>S-16</option>
                        <option>S-17</option>
                        <option>S-18</option>
                        <option>S-19</option>
                        <option>S-20</option>
                        <option>S-21</option>
                        <option>S-22</option>
                        <option>S-25</option>
                        <option>S-28</option>
                        <option>S-30</option>
                        <option>S-33</option>
                        <option>S-35</option>
                        <option>S-38</option>
                        <option>S-40</option>
                        <option>S-43</option>
                        <option>S-45</option>
                        <option>S-48</option>
                        <option>S-50</option>
                        <option>S-55</option>
                        <option>S-60</option>
                        <option>S-65</option>
                    </select>
                </dd>
                <dd class="br _type4">
                    <select>
                        <option>HC-10</option>
                        <option>HC- 11</option>
                        <option>HC-12</option>
                        <option>HC-13</option>
                        <option>HC-14</option>
                        <option>HC-15</option>
                        <option>HC-16</option>
                        <option>HC-17</option>
                        <option>HC-18</option>
                        <option>HC-19</option>
                        <option>HC-20</option>
                        <option>HC-21</option>
                        <option>HC-22</option>
                        <option>HC-25</option>
                        <option>HC-28</option>
                        <option>HC-30</option>
                        <option>HC-33</option>
                        <option>HC-35</option>
                        <option>HC-38</option>
                        <option>HC-40</option>
                        <option>HC-43</option>
                        <option>HC-45</option>
                        <option>HC-48</option>
                        <option>HC-50</option>
                        <option>HC-55</option>
                        <option>HC-60</option>
                        <option>HC-65</option>
                    </select>
                </dd>
                <dd class="br _type5">
                    <select>
                        <option>HS-10</option>
                        <option>HS-11</option>
                        <option>HS-12</option>
                        <option>HS-13</option>
                        <option>HS-14</option>
                        <option>HS-15</option>
                        <option>HS-16</option>
                        <option>HS-17</option>
                        <option>HS-18</option>
                        <option>HS-19</option>
                        <option>HS-20</option>
                        <option>HS-21</option>
                        <option>HS-22</option>
                        <option>HS-25</option>
                        <option>HS-28</option>
                        <option>HS-30</option>
                        <option>HS-33</option>
                        <option>HS-35</option>
                        <option>HS-38</option>
                        <option>HS-40</option>
                        <option>HS-43</option>
                        <option>HS-45</option>
                        <option>HS-48</option>
                        <option>HS-50</option>
                        <option>HS-55</option>
                        <option>HS-60</option>
                        <option>HS-65</option>
                    </select>
                </dd>
                <dd class="br _type6">
                    <select>
                        <option>R-5017</option>
                        <option>R-5020</option>
                        <option>R-5030</option>
                        <option>R-4220</option>
                        <option>R-4223</option>
                        <option>R-5515</option>
                        <option>R-5517</option>
                        <option>R-5524</option>
                        <option>R-5533</option>
                        <option>R-3525</option>
                        <option>R-4030</option>
                    </select>
                </dd>
                <dd class="br _type7">
                    <select>
                        <option>O-5020</option>
                        <option>O-5025</option>
                        <option>O-5030</option>
                        <option>O-5525</option>
                        <option>O-5530</option>
                        <option>O-6025</option>
                        <option>O-6030</option>
                    </select>
                </dd>
                <dd class="br _type8">
                    <select>
                        <option>비디오1</option>
                        <option>비디오2</option>
                        <option>카셋트</option>
                        <option>CD</option>
                    </select>
                </dd>
                <dd class="br _type9">
                    <select>
                        <option>유형1</option>
                        <option>유형2</option>
                        <option>유형3</option>
                        <option>유형4</option>
                    </select>
                </dd>
                -->
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 넘버링 정보 html 반환
 *
 * @return div html
 */
function getNumberingInfoHtml() {
    $html = <<<html
        <div class="option _numbering">
            <dl>
                <dt>넘버링</dt>
                <dd id="numbering_price_dd" class="price"></dd>
                <dd>
                    <select id="numbering" onchange="getAfterPrice.common('numbering');">
                        %s
                        <!--
                        <option>일반</option>
                        <option>난수</option>
                        -->
                    </select>
                    <select id="numbering_dvs" class="_num">
                        <option>한군데</option>
                        <option>두군데</option>
                        <option>세군데</option>
                    </select>

                    <input type="hidden" id="numbering_val" name="numbering_val" value="" />
                </dd>
                <dd class="br">
                    첫번째
                    <label>시작번호 <input type="text" class="mm100"></label>
                    <label>끝번호 <input type="text" class="mm100"></label>
                </dd>
                <dd class="br">
                    두번째
                    <label>시작번호 <input type="text" class="mm100"></label>
                    <label>끝번호 <input type="text" class="mm100"></label>
                </dd>
                <dd class="br">
                    세번째
                    <label>시작번호 <input type="text" class="mm100"></label>
                    <label>끝번호 <input type="text" class="mm100"></label>
                </dd>
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 재단 정보 html 반환
 *
 * @return div html
 */
function getCuttingInfoHtml($info) {
    // <option value="label">라벨재단</option>
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

/**
 * @brief 추가 후공정 제본 정보 html 반환
 *
 * @return div html
 */
function getBindingInfoHtml() {

    $html = <<<html
        <div class="option _binding">
            <dl>
                <dt>제본</dt>
                <dd id="binding_price_dd" class="price"></dd>
                <dd>
                    <select id="binding" onchange="loadBindingDepth2(this.value);">
                        %s
                    </select>
                    <select id="binding_val" onchange="getAfterPrice.common('binding');">
                        %s
                    </select>
                    <input type="text" class="page"> 매
                </dd>
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 접착 정보 html 반환
 *
 * @return div html
 */
function getBondingInfoHtml() {
    // <option value="bothside">양면테이프</option>
    $html = <<<html
        <div class="option _bonding">
            <dl>
                <dt>접착</dt>
                <dd id="bonding_price_dd" class="price"></dd>
                <dd>
                    <select id="bonding" onchange="getAfterPrice.common('bonding');">
                        %s
                    </select>
                </dd>
                <dd class="br _oneside">
                    <select>
                        <option>자동</option>
                        <option>수동</option>
                    </select>
                </dd>
                <dd class="br _bothside">
                    <select>
                        <option>1개</option>
                        <option>2개</option>
                        <option>3개</option>
                    </select>
                </dd>
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 라미넥스 정보 html 반환
 *
 * @return div html
 */
function getLaminexInfoHtml() {
    $html = <<<html
        <div class="option _laminex">
            <dl>
                <dt>라미넥스</dt>
                <dd id="laminex_price_dd" class="price"></dd>
                <dd>
                    <select id="laminex" onchange="getAfterPrice.common('laminex');">
                        %s
                    </select>
                </dd>
                <dd>
                    <label><input type="text" class="page"> 매</label>
                </dd>
                <input type="hidden" id="laminex_price" name="laminex_price" value="" />
            </dl>
        </div>
html;

    return $html;
}
?>
