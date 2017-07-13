<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");

/**
 * @brief 추가 옵션 정보 html 반환
 *
 * @param $idx      = 구분용 인덱스
 * @param $opt_name = 옵션명
 * @param $info_arr = 추가옵션 정보
 *
 * @return div html OR input hidden html
 */
function getAddOptInfoHtml($idx, $opt_name, $info_arr) {
    $util = new FrontCommonUtil();

    $flag = false;

    $option = null;
    $info_arr_count = count($info_arr);
    for ($i = 0; $i < $info_arr_count; $i++) {
        $info = $info_arr[$i];

        $mpcode = $info["mpcode"];
        $dvs = $util->getOptAfterFullName($info);

        if ($dvs === '') {
            $flag = true;
            break;
        }

        $option .= option($mpcode, $dvs);
        $flag = false;
    }

    $html_sel = <<<html_sel
        <div id="opt_{$idx}_div" class="option">
        <dl>
            <dt>$opt_name</dt>
            <dd class="price" id="opt_{$idx}_price"></dd>
            <dd>
                <select id="opt_{$idx}_sel" onchange="getOptPrice('$idx', this.value);">
                    $option
                </select>
            </dd>
        </dl>
        </div>
html_sel;

    // 하위 depth 가 없는 경우
    $html_hidden = <<<html_hidden
        <div id="opt_{$idx}_div" class="option">
        <dl>
            <dt>$opt_name</dt>
            <dd class="price" id="opt_{$idx}_price"></dd>
            <input type="hidden" id="opt_{$idx}_sel" value="$mpcode" style="display:none;" />
        </dl>
        </div>
html_hidden;

    if ($flag === true) {
        return $html_hidden;
    }

    return $html_sel;
}

/**
 * @brief 명함디자인 추가 옵션 html 반환
 *
 * @param $idx      = 구분용 인덱스
 * @param $opt_name = 옵션명
 * @param $info_arr = 추가옵션 정보
 *
 * @return div html
 */
function getNcDesignInfoHtml($idx, $opt_name, $info_arr) {
    $html = <<<html
        <div id="opt_{$idx}_div" class="option _design">
            <dl>
                <dt>$opt_name</dt>
                <dd class="price" id="opt_{$idx}_price"></dd>
                <dd>
                    <select class="dselect1">
                        <option>기획제작</option>
                        <option value="oldedit">기존디자인수정</option>
                        <option>인쇄파일전환</option>
                    </select>
                    <select class="dselect2">
                        <option></option>
                        <option value="editnew">이미지수정</option>
                        <option>이미지대체</option>
                        <option>텍스트수정</option>
                    </select>
                    <select class="dselect3">
                        <option></option>
                        <option>50%</option>
                        <option>80%</option>
                    </select>
                </dd>
            </dl>
        </div>
html;

    return $html;
}
?>

