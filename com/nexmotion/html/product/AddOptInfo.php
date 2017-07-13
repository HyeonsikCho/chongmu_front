<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");

/**
 * @brief 추가 옵션 정보 html 반환
 *
 * @param $idx      = 구분용 인덱스
 * @param $opt_name = 옵션명
 * @param $info_arr = 추가옵션 정보
 *
 * @return div html
 */
function getAddOptInfoHtml($idx, $opt_name, $info_arr) {
    $util = new FrontCommonUtil();

    $html_base  = "\n <div id=\"opt_div_%s\" class=\"option\">";
    $html_base .= "\n <dl>";
    $html_base .= "\n     <dt>%s</dt>";
    $html_base .= "\n     <dd class=\"price\" id=\"opt_price_%s\"></dd>";
    $html_base .= "\n     <dd>";
    $html_base .= "\n         <select id=\"opt_sel_%s\" name=\"opt_sel_%s\"  onchange=\"getOptPrice('%s', this.value); style=\"display:none;\">";
    $html_base .= "\n            %s";
    $html_base .= "\n         </select>";
    $html_base .= "\n     </dd>";
    $html_base .= "\n </dl>";
    $html_base .= "\n </div>";

    // depth가 없어서 옵션명만 출력되는 경우
    $hidden_base = "\n <input type=\"hidden\" id=\"opt_sel_%s\" name=\"opt_sel_%s\" value=\"%s\" style=\"display:none;\" />";

    $option = null;
    $info_arr_count = count($info_arr);
    for ($i = 0; $i < $info_arr_count; $i++) {
        $info = $info_arr[$i];

        $mpcode = $info["mpcode"];
        $dvs = $util->getOptAfterFullName($info);

        if ($dvs === '') {
            return sprintf($hidden_base, $idx,$idx, $mpcode);
        }

        $option .= option($mpcode, $dvs);
    }

    $ret = sprintf($html_base, $idx
                             , $opt_name
                             , $idx
                             , $idx
                             , $idx
							 , $idx
                             , $option);

    return $ret;
}
?>
