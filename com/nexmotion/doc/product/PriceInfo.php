<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/define/member_grade.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");

/**
 * @brief 회원 등급 할인 dl html 반환
 *
 * @param $param = 회원등급, 할인율 파라미터
 * @param $price_info_arr = 가격검색용 정보저장 배열
 *
 * @return dl html
 */
function getGradeSaleDl($param, &$price_info_arr) {
    $util = new FrontCommonUtil();

    $rate  = $param["rate"];

    $price = doubleval($param["price"]);

    $dscr  = $param["dscr"];
    if ($dscr === null) {
        $dscr  = sprintf("회원등급 : %s (할인율 : %s%%)", $grade, $rate);
    }
    $rate  = number_format(doubleval($rate));

    $grade_sale = ceil(($rate / 100.0) * $price);
    $grade_sale = $util->ceilVal($grade_sale);

    $price_info_arr["grade_sale"] = $grade_sale;

    $grade_sale = number_format($grade_sale);


    $html = <<<html
        <dl style="height:23px;">
            <dt>적립금</dt>
            <dd class="description"> $dscr , $rate , $price </dd>
            <dd id="grade_sale" rate="$rate" class="discountAmount">$grade_sale 원</dd>
        </dl>
html;

    return $html;
}

/**
 * @brief 이벤트 할인 dl html 반환
 *
 * @param $param = 이벤트명, 할인 요율/가격 정보 파라미터
 *
 * @return dl html
 */
function getEventSaleDl($param) {
    $name  = $param["name"];
    $price = $param["price"];
    $dscr  = $param["dscr"];

    $html = <<<html
        <dl style="height:23px;">
            <dt>이벤트 할인</dt>
            <dd>
                <button type="button"><img src="/design_template/images/product/discount_btn_event.png" alt="관련 이벤트"></button>
            </dd>
            <dd class="description">$dscr</dd>
            <dd id="event_sale" class="discountAmount"></dd>
        </dl>
html;

    return $html;
}
?>
