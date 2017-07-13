<?
/**
 * @brief 로그인 상태일 경우 헤더의 고객정보 html 반환
 *
 * @param $info = 사용자 정보
 *
 * @return login html
 */
function getLoginHtml($session, $point = 0) {
    $point = number_format(doubleval($session['cpoint']));
    $member_name = $session['member_name'];


    $html = <<<html
        <article>
            <h2><strong>$member_name</strong>님</h2>
            <dl>
                <dt>가용 마일리지</dt>
                <dd><em>$point</em>원</dd>
            </dl>
        </article>
html;

    return $html;
}

/**
 * @brief 로그아웃 상태일 경우 헤더의 로그아웃 html 반환
 *
 * @param $cookie = 쿠키 초전역 변수
 *
 * @return logout html
 */
function getLogoutHtml($cookie) {
    $html = <<<html
        <dl>
            <dt>아이디</dt>
            <dd><input id="id" type="text" onkeyup="idkey(event, '');" value="$cookie[id]"></dd>
            <dt>비밀번호</dt>
            <dd><input id="pw" type="password" onkeyup="loginKey(event, '');"></dd>
        </dl>
        <button onclick="login('');" class="login">LOGIN</button>
html;

    return $html;
}

/**
 * @brief 로그인 상태일 때 사이드메뉴 html 추가
 *
 * @param $info    = 사용자 정보
 * @param $summary = 주문요약 배열
 *
 * @return logout html
 */
function getAsideHtml($info, $summary) {
    $prepay_price = number_format(doubleval($info["prepay_price"]));
    $point = number_format(doubleval($info["own_point"]));

    // 제작 요약
    $summary_prdc = $summary["400"] + $summary["600"] +
                    $summary["700"] + $summary["800"];

    // 입출고 요약
    $summary_rels = $summary["900"] + $summary["000"];

    $html = <<<html
        <button type="button" title="닫기" class="switch _opened"><img src="/design_template/images/common/aside_member_btn_opened.png" alt="◀"></button>
        <button type="button" title="열기" class="switch _closed"><img src="/design_template/images/common/aside_member_btn_closed.png" alt="▶"></button>
        <div class="wrap">
            <section class="membership">
                <h2 class="grade6"><img src="/design_template/images/common/$info[grade_image].png" alt="PLATINUM"></h2>
                <ul class="infomation">
                    <li class="prepaid">
                        <dl>
                            <dt>선입금</dt>
                            <dd>$prepay_price 원</dd>
                        </dl>
                    </li>
                    <li class="point">
                        <dl>
                            <dt>마일리지</dt>
                            <dd>$point P</dd>
                        </dl>
                    </li>
                    <li class="coupon">
                        <dl>
                            <dt>쿠폰</dt>
                            <dd>$info[cp_count] 매</dd>
                        </dl>
                    </li>
                </ul>
            </section>
            <section class="myOrder">
                <h2>
                    나의 주문현황
                    <ul class="_switch">
                        <li class="_on"><button onclick="getOrderSummary('week');"><img src="/design_template/images/common/btn_text_week.png" alt="최근1주일"></button></li>
                    <li><button onclick="getOrderSummary('month');"><img src="/design_template/images/common/btn_text_month.png" alt="이번달"></button></li>
                    </ul>
                </h2>
                <ul class="list">
                    <li class="standby">
                        <a href="#none" target="_self">
                            <dl>
                                <dd id="summary_wait">$summary[200]</dd>
                                <dt>입금대기</dt>
                            </dl>
                        </a>
                    </li>
                    <li class="application">
                        <a href="#none" target="_self">
                            <dl>
                                <dd id="summary_rcpt">$summary[300]</dd>
                                <dt>접수</dt>
                            </dl>
                        </a>
                    </li>
                    <li class="manufacture">
                        <a href="#none" target="_self">
                            <dl>
                                <dd id="summary_prdc">$summary_prdc</dd>
                                <dt>제작</dt>
                            </dl>
                        </a>
                    </li>
                </ul>
                <ul class="list">
                    <li class="release">
                        <a href="#none" target="_self">
                            <dl>
                                <dd id="summary_rels">$summary_rels</dd>
                                <dt>입/출고</dt>
                            </dl>
                        </a>
                    </li>
                    <li class="delivery">
                        <a href="#none" target="_self">
                            <dl>
                                <dd id="summary_dlvr"></dd>
                                <dt>배송</dt>
                            </dl>
                        </a>
                    </li>
                    <li class="complete">
                        <a href="#none" target="_self">
                            <dl>
                                <dd id="summary_comp"></dd>
                                <dt>완료</dt>
                            </dl>
                        </a>
                    </li>
                </ul>
            </section>
            <section class="favorite">
                <h2>늘 했던 거</h2>
                <table>
                    <colgroup>
                        <col width="23">
                        <col>
                    </colgroup>
                    <tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>
                                <ul>
                                    <li>코팅명함</li>
                                    <li>아트지 백색 100g</li>
                                    <li>A4</li>
                                    <li>탄면칼라 4도</li>
                                    <li>1,000 x 1</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>
                                <ul>
                                    <li>코팅명함</li>
                                    <li>아트지 백색 100g</li>
                                    <li>A4</li>
                                    <li>탄면칼라 4도</li>
                                    <li>1,000 x 1</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>
                                <ul>
                                    <li>코팅명함</li>
                                    <li>아트지 백색 100g</li>
                                    <li>A4</li>
                                    <li>탄면칼라 4도</li>
                                    <li>1,000 x 1</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>
                                <ul>
                                    <li>코팅명함</li>
                                    <li>아트지 백색 100g</li>
                                    <li>A4</li>
                                    <li>탄면칼라 4도</li>
                                    <li>1,000 x 1</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>
                                <ul>
                                    <li>코팅명함</li>
                                    <li>아트지 백색 100g</li>
                                    <li>A4</li>
                                    <li>탄면칼라 4도</li>
                                    <li>1,000 x 1</li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="function">
                    <button type="button" class="_selectAll white">전체선택</button>
                    <div class="purchase">
                        <strong><button type="button">즉시주문</button></strong>
                        <button type="button">장바구니</button>
                    </div>
                </div>
            </section>
            <section class="contact">
                <h2>담당자 연락처</h2>
                <dl class="telNum">
                    <dt>영업담당</dt>
                    <dd>$info[biz_tel]</dd>
                    <dt>출고담당</dt>
                    <dd>$info[release_tel]</dd>
                    <dt>배송담당</dt>
                    <dd>$info[dlvr_tel]</dd>
                </dl>
                <strong>02.2260.9000</strong>
                <dl class="time">
                    <dt>평일</dt>
                    <dd>09:00~20:00</dd>
                    <dt>접수점심</dt>
                    <dd>12:30~13:30</dd>
                    <dt>출고점심</dt>
                    <dd>13:30~14:30</dd>
                    <dt>토요일</dt>
                    <dd>09:00~15:00</dd>
                </dl>
            </section>
        </div>
        <div class="cover">
            <section class="membership">
                <h2 class="grade6"><img src="/design_template/images/common/aside_member_grade6_folded.png" alt="platinum"></h2>
                <ul class="infomation">
                    <li class="prepaid">선입금</li>
                    <li class="point">마일리지</li>
                    <li class="coupon">쿠폰</li>
                </ul>
            </section>
            <section class="myOrder">
                <h2>나의 주문 현황</h2>
            </section>
            <section class="favorite">
                <h2>늘 했던 거</h2>
            </section>
            <section class="contact">
                <h2>담당자 연락처</h2>
            </section>
        </div>
html;

    return $html;
}
?>
