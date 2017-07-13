<?
/**
 * @brief 나의배송지 선택 팝업 html 생성
 *
 * @param $rs = 검색결과
 *
 * @return 팝업 html
 */
function addressListPopup($rs) {
    $tr_base  = "<tr>";
    $tr_base .= "    <th scope=\"row\">%s</th>"; // 별칭
    $tr_base .= "    <td>%s</td>"; // 받으시는분
    $tr_base .= "    <td class=\"address\">%s %s</td>"; // 주소
    $tr_base .= "    <td class=\"btn\">";
    $tr_base .= "       <button onclick=\"setMemberAddrInfo.exec(this);\">선택</button>";
    $tr_base .= "       <input type=\"hidden\" name=\"name\" value=\"%s\" />";
    $tr_base .= "       <input type=\"hidden\" name=\"tel_num\" value=\"%s\" />";
    $tr_base .= "       <input type=\"hidden\" name=\"cell_num\" value=\"%s\" />";
    $tr_base .= "       <input type=\"hidden\" name=\"zipcode\" value=\"%s\" />";
    $tr_base .= "       <input type=\"hidden\" name=\"addr\" value=\"%s\" />";
    $tr_base .= "       <input type=\"hidden\" name=\"addr_detail\" value=\"%s\" />";
    $tr_base .= "    </td>";
    $tr_base .= "</tr>";

    $tr = "";

    if ($rs->EOF) {
        $tr = "<tr><td colspan=\"4\">나의배송지 정보가 없습니다.</td></tr>";
    }

    while ($rs && !$rs->EOF) {
        $fields = $rs->fields;

        $tr .= sprintf($tr_base, $fields["dlvr_name"]
                               , $fields["recei"]
                               , $fields["addr"]
                               , $fields["addr_detail"]
                               , $fields["recei"]
                               , $fields["tel_num"]
                               , $fields["cell_num"]
                               , $fields["zipcode"]
                               , $fields["addr"]
                               , $fields["addr_detail"]);
        $rs->MoveNext();
    }

    $html = <<<html
        <header>
            <h2>나의 배송지 목록</h2>
            <button class="close" title="닫기"><img src="/design_template/images/common/btn_circle_x_white.png" alt="X"></button>
        </header>
        <article>
            <table class="list thead">
                <colgroup>
                    <col width="150">
                    <col width="80">
                    <col width="260">
                    <col>
                </colgroup>
                <thead>
                    <tr>
                        <th>배송지 별칭</th>
                        <th>받으시는 분</th>
                        <th>주소</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
            <div class="tableScroll">
                <div class="wrap">
                    <table class="list">
                        <colgroup>
                            <col width="150">
                            <col width="80">
                            <col width="260">
                            <col>
                        </colgroup>
                        <tbody>
                            $tr
                        </tbody>
                    </table>
                </div>
            </div>
        </article>
html;
    
    return $html;
}

/**
 * @brief 마일리지 사용 팝업 html 생성
 *
 * @param $own_point = 보유마일리지
 *
 * @return 팝업 html
 */
function pointPopup($own_point) {
    $own_point = number_format(doubleval($own_point));

    $html = <<<html
        <header>
            <h2>마일리지 현황</h2>
            <button class="close" title="닫기"><img src="/design_template/images/common/btn_circle_x_white.png" alt="X"></button>
        </header>
        <article>
            <ul class="amount">
                <li><label><h3>보유 마일리지</h3> <input type="text" id="own_point" readonly value="$own_point"></label> P</li>
                <li><label><h3>사용 마일리지</h3> <input type="text" id="use_point" value="0"></label> P</li>
            </ul>
            <div class="function center">
                <strong><button onclick="setPointPrice();">사용</button></strong>
                <button class="close">취소</button>
            </div>
        </article>
html;

    return $html;
}

/**
 * @brief 쿠폰 사용 팝업 html 생성
 *
 * @param
 *
 *
 * @return 팝업 html
 */
function couponPopup() {
    $html = <<<html
        <header>
            <h2>쿠폰 현황</h2>
            <button class="close" title="닫기"><img src="/design_template/images/common/btn_circle_x_white.png" alt="X"></button>
        </header>
        <article>
            <table class="list thead">
                <colgroup>
                    <col width="60">
                    <col width="220">
                    <col width="105">
                    <col width="150">
                    <col>
                </colgroup>
<!--
                <caption class="legend">○ : 적용 가능 / △ 조건 만족 시 사용 가능 / X : 적용 불가</caption>
-->
                <thead>
                    <tr>
                        <th><input type="checkbox" class="_general"></th>
                        <th>쿠폰명</th>
                        <th>할인금액</th>
                        <th>기간</th>
                        <th>적용가능</th>
                    </tr>
                </thead>
            </table>
            <div class="tableScroll">
                <div class="wrap">
                    <table class="list">
                        <colgroup>
                    <col width="60">
                    <col width="220">
                    <col width="105">
                    <col width="150">
                    <col>
                        </colgroup>
                        <tbody>
                            <tr>
                                <td class="btn"><input type="checkbox" class="_individual" disabled></td>
                                <th scope="row" class="subject">고객 감사 장바구니 할인</th>
                                <td>&#8361; 1,000</td>
                                <td>2015-03-19 ~ 2015-04-19</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td class="btn"><input type="checkbox" class="_individual"></td>
                                <th scope="row" class="subject">고객 감사 장바구니 할인</th>
                                <td>&#8361; 1,000</td>
                                <td>2015-03-19 ~ 2015-04-19</td>
                                <td>○</td>
                            </tr>
                            <tr>
                                <td class="btn"><input type="checkbox" class="_individual"></td>
                                <th scope="row" class="subject">고객 감사 장바구니 할인</th>
                                <td>&#8361; 1,000</td>
                                <td>2015-03-19 ~ 2015-04-19</td>
                                <td>○</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="function center">
                <strong><button>적용</button></strong>
                <button class="close">취소</button>
            </div>
        </article>
html;

    return $html;
}
?>
