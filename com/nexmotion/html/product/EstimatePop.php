<?php
/**
 * @brief 팝업 상단 html 생성
 *
 * @param $param = 정보
 *
 * @return 상단 html
 */
function getHtmlTop($param) {
    $html_top  = '';
    $html_top .= "\n    <header class=\"estimate\">";
    $html_top .= "\n        <h3>견적서</h3>";
    $html_top .= "\n        <div class=\"leftWrap\">";
    $html_top .= "\n            <dl class=\"date\">";
    $html_top .= "\n                <dt>견적일</dt>";
    $html_top .= "\n                <dd>%s년 %s월 %s일</dd>"; //#0 year, month, day
    $html_top .= "\n            </dl>";
    $html_top .= "\n            <dl class=\"to\">";
    $html_top .= "\n                <dt>%s <span class=\"sub\">귀하</span></dt>"; //#1 member_name
    $html_top .= "\n                <dd>";
    $html_top .= "\n                    <dl>";
    $html_top .= "\n                        <dt>전화</dt>";
    $html_top .= "\n                        <dd>%s</dd>"; //#2 member_tel
    $html_top .= "\n                    </dl>";
    /*
    $html_top .= "\n                    <dl>";
    $html_top .= "\n                        <dt>팩스</dt>";
    $html_top .= "\n                        <dd>" . memberFax . "</dd>";
    $html_top .= "\n                    </dl>";
    */
    $html_top .= "\n                </dd>";
    $html_top .= "\n            </dl>";
    $html_top .= "\n        </div>";

    $html_top .= "\n        <table class=\"information center\">";
    $html_top .= "\n            <colgroup>";
    $html_top .= "\n                <col width=\"45\">";
    $html_top .= "\n                <col width=\"80\">";
    $html_top .= "\n                <col>";
    $html_top .= "\n                <col width=\"55\">";
    $html_top .= "\n                <col width=\"95\">";
    $html_top .= "\n            </colgroup>";
    $html_top .= "\n            <tbody>";
    $html_top .= "\n                <tr>";
    $html_top .= "\n                    <th rowspan=\"4\" scope=\"row\" class=\"thead\">공<br>급<br>자</th>";
    $html_top .= "\n                    <th scope=\"row\">상호</th>";
    $html_top .= "\n                    <td>%s</td>"; //#3 sell_site
    $html_top .= "\n                    <th scope=\"row\">대표자</th>";
    $html_top .= "\n                    <td class=\"name\">";
    $html_top .= "\n                        %s"; //#4 repre_name
    $html_top .= "\n                        <img src=\"/design_template/images/product/estimate_sign.png\" alt=\"직인\" class=\"sign\">";
    $html_top .= "\n                    </td>";
    $html_top .= "\n                </tr>";
    $html_top .= "\n                <tr>";
    $html_top .= "\n                    <th scope=\"row\">사업장소재지</th>";
    $html_top .= "\n                    <td colspan=\"3\">%s %s</td>"; //#5 addr, addr_detail
    $html_top .= "\n                </tr>";
    $html_top .= "\n                <tr>";
    $html_top .= "\n                    <th scope=\"row\">전화번호</th>";
    $html_top .= "\n                    <td colspan=\"3\">%s</td>"; //#6 repre_num
    $html_top .= "\n                </tr>";
    $html_top .= "\n                <tr>";
    $html_top .= "\n                    <th scope=\"row\">담당자</th>";
    $html_top .= "\n                    <td>%s</td>"; //#7 member_mng
    $html_top .= "\n                    <th scope=\"row\">연락처</th>";
    $html_top .= "\n                    <td>%s</td>"; //#8 member_mng_tel
    $html_top .= "\n                </tr>";
    $html_top .= "\n            </tbody>";
    $html_top .= "\n        </table>";
    $html_top .= "\n        <p>아래와 같이 견적합니다.</p>";
    $html_top .= "\n    </header>";

    return sprintf($html_top, $param["year"]             //#0
        , $param["month"]            //#0
        , $param["day"]              //#0
        , $param["member_name"]      //#1
        , $param["member_tel"]       //#2
        , $param["sell_site"]        //#3
        , $param["repre_name"]       //#4
        , $param["addr"]             //#5
        , $param["addr_detail"]      //#5
        , $param["repre_num"]        //#6
        , $param["member_mng"]       //#7
        , $param["member_mng_tel"]); //#8
}

/**
 * @brief 팝업 중단 html 생성
 *
 * @param $param = 정보
 *
 * @return 중단 html
 */
function getHtmlMid($param) {
    $common_cate_name = $param["common_cate_name"];
    $cate_name_arr = $param["cate_name_arr"];
    $paper_arr     = $param["paper_arr"];
    $size_arr      = $param["size_arr"];
    $tmpt_arr      = $param["tmpt_arr"];
    $amt_arr       = $param["amt_arr"];
    $amt_unit_arr  = $param["amt_unit_arr"];
    $count_arr     = $param["count_arr"];
    $after_arr     = $param["after_arr"];

    $paper_price  = $param["paper_price"];
    $print_price  = $param["print_price"];
    $output_price = $param["output_price"];
    $after_price  = $param["after_price"];
    $sum_price    = $param["sum_price"];

    $prdt_count = count($cate_name_arr);

    $mid_top  = "\n    <table class=\"information\">";
    $mid_top .= "\n        <colgroup>";
    $mid_top .= "\n            <col width=\"265\">";
    $mid_top .= "\n            <col width=\"136\">";
    $mid_top .= "\n            <col>";
    $mid_top .= "\n        </colgroup>";
    $mid_top .= "\n        <tbody>";
    $mid_top .= "\n            <tr>";
    $mid_top .= "\n                <th scope=\"row\" class=\"thead left\">합계금액 (공급가액+세액+배송비별도)</th>";
    $mid_top .= "\n                <th scope=\"row\" class=\"right\">총 합계금액</th>";
    $mid_top .= "\n                <td class=\"right\">&#8361; %s</td>"; //#0 sum_price
    $mid_top .= "\n            </tr>";
    $mid_top .= "\n        </tbody>";
    $mid_top .= "\n    </table>";

    $mid_top = sprintf($mid_top, $sum_price);

    $mid_mid_base  = "\n    <table class=\"information\">";
    $mid_mid_base .= "\n        <colgroup>";
    $mid_mid_base .= "\n            <col width=\"195\">";
    $mid_mid_base .= "\n            <col>";
    $mid_mid_base .= "\n        </colgroup>";
    $mid_mid_base .= "\n        <thead>";
    $mid_mid_base .= "\n            <tr>";
    $mid_mid_base .= "\n                <th colspan=\"2\">제품규격</th>";
    $mid_mid_base .= "\n            </tr>";
    $mid_mid_base .= "\n        </thead>";
    $mid_mid_base .= "\n        <tbody class=\"center\">";
    $mid_mid_base .= "\n            <tr>";
    $mid_mid_base .= "\n                <th>품명</th>";
    $mid_mid_base .= "\n                <td>%s</td>"; //#1 cate_name
    $mid_mid_base .= "\n            </tr>";
    $mid_mid_base .= "\n            <tr>";
    $mid_mid_base .= "\n                <th>재질</th>";
    $mid_mid_base .= "\n                <td>%s</td>"; //#2 cate_paper
    $mid_mid_base .= "\n            </tr>";
    $mid_mid_base .= "\n            <tr>";
    $mid_mid_base .= "\n                <th>사이즈</th>";
    $mid_mid_base .= "\n                <td>%s</td>"; //#3 cate_size
    $mid_mid_base .= "\n            </tr>";
    $mid_mid_base .= "\n            <tr>";
    $mid_mid_base .= "\n                <th>인쇄도수</th>";
    $mid_mid_base .= "\n                <td>%s</td>"; //#4 cate_tmpt
    $mid_mid_base .= "\n            </tr>";
    $mid_mid_base .= "\n            <tr>";
    $mid_mid_base .= "\n                <th>수량</th>";
    $mid_mid_base .= "\n                <td>%s%s &times; %s건</td>"; //#5 amt, amt_unit, count
    $mid_mid_base .= "\n            </tr>";
    $mid_mid_base .= "\n        </tbody>";
    $mid_mid_base .= "\n    </table>";

    $mid_mid = '';
    for ($i = 0; $i < $prdt_count; $i++) {
        $cate_name = $cate_name_arr[$i];
        $paper     = $paper_arr[$i];
        $size      = $size_arr[$i];
        $tmpt      = $tmpt_arr[$i];
        $amt       = $amt_arr[$i];
        $amt_unit  = $amt_unit_arr[$i];
        $count     = $count_arr[$i];

        if (empty($common_cate_name) === false) {
            $cate_name = $common_cate_name . " - " . $cate_name;
        }

        $mid_mid .= sprintf($mid_mid_base, $cate_name
            , $paper
            , $size
            , $tmpt
            , $amt
            , $amt_unit
            , $count);
    }

    $mid_bot  = "\n    <table class=\"information\">";
    $mid_bot .= "\n        <colgroup>";
    $mid_bot .= "\n            <col width=\"195\">";
    $mid_bot .= "\n            <col>";
    $mid_bot .= "\n        </colgroup>";
    $mid_bot .= "\n        <thead>";
    $mid_bot .= "\n            <tr>";
    $mid_bot .= "\n                <th colspan=\"2\">후공정내역</th>";
    $mid_bot .= "\n            </tr>";
    $mid_bot .= "\n        </thead>";

    $mid_bot_after .= "\n        <tbody class=\"center\">";
    $mid_bot_after .= "\n            <tr>";
    $mid_bot_after .= "\n                <th>%s</th>"; //#6-1 cate_name
    $mid_bot_after .= "\n                <td>%s</td>"; //#6-2 cate_after
    $mid_bot_after .= "\n            </tr>";
    $mid_bot_after .= "\n        </tbody>";

    for ($i = 0; $i < $prdt_count; $i++) {
        $cate_name = $cate_name_arr[$i];
        $after     = $after_arr[$i];

        $mid_bot .= sprintf($mid_bot_after, $cate_name
            , $after);
    }

    $mid_bot .= "\n    </table>";
    $mid_bot .= "\n    <table class=\"information\">";
    $mid_bot .= "\n        <colgroup>";
    $mid_bot .= "\n            <col width=\"195\">";
    $mid_bot .= "\n            <col>";
    $mid_bot .= "\n        </colgroup>";
    $mid_bot .= "\n        <thead>";
    $mid_bot .= "\n            <tr>";
    $mid_bot .= "\n                <th colspan=\"2\">제품견적금액</th>";
    $mid_bot .= "\n            </tr>";
    $mid_bot .= "\n        </thead>";

    $mid_bot .= "\n        <tbody class=\"center\">";
    if ($paper_price !== '-') {
        $mid_bot .= "\n            <tr>";
        $mid_bot .= "\n                <th>종이비</th>";
        $mid_bot .= "\n                <td>&#8361; " . $paper_price . "</td>"; //#7 paper_price
        $mid_bot .= "\n            </tr>";
    }
    if ($print_price !== '-') {
        $mid_bot .= "\n            <tr>";
        $mid_bot .= "\n                <th>인쇄비</th>";
        $mid_bot .= "\n                <td>&#8361; " . $print_price . "</td>"; //#8 print_price
        $mid_bot .= "\n            </tr>";
    }
    if ($output_price !== '-') {
        $mid_bot .= "\n            <tr>";
        $mid_bot .= "\n                <th>출력비</th>";
        $mid_bot .= "\n                <td>&#8361; " . $output_price . "</td>"; //#9 output_price
        $mid_bot .= "\n            </tr>";
    }
    $mid_bot .= "\n            <tr>";
    $mid_bot .= "\n                <th>후공정비</th>";
    $mid_bot .= "\n                <td>&#8361; %s</td>"; //#10 after_price
    $mid_bot .= "\n            </tr>";

    $mid_bot_count .= "\n            <tr>";
    $mid_bot_count .= "\n                <th>주문건수</th>";
    $mid_bot_count .= "\n                <td>%s건(%s)</td>"; //#11 count
    $mid_bot_count .= "\n            </tr>";
    for ($i = 0; $i < $prdt_count; $i++) {
        $cate_name = $cate_name_arr[$i];
        $count     = $count_arr[$i];

        $mid_bot .= sprintf($mid_bot_count, $count
            , $cate_name);
    }

    $mid_bot .= "\n            <tr>";
    $mid_bot .= "\n                <th>합 계</th>";
    $mid_bot .= "\n                <td>&#8361; %s</td>"; //#12 sum_price
    $mid_bot .= "\n            </tr>";
    $mid_bot .= "\n        </tbody>";
    $mid_bot .= "\n    </table>";

    $mid_bot  = sprintf($mid_bot, $after_price
        , $sum_price);

    return $mid_top . $mid_mid . $mid_bot;
}

/**
 * @brief 팝업 하단 html 생성
 *
 * @param $param = 정보
 *
 * @return 하단 html
 */
function getHtmlBot($param) {
    $html_bot  = "\n    <table class=\"information officer center\">";
    $html_bot .= "\n        <colgroup>";
    $html_bot .= "\n            <col width=\"25%%\">";
    $html_bot .= "\n            <col width=\"25%%\">";
    $html_bot .= "\n            <col>";
    $html_bot .= "\n        </colgroup>";
    $html_bot .= "\n        <tbody>";
    $html_bot .= "\n            <tr>";
    $html_bot .= "\n                <th scope=\"row\" class=\"thead\">담당자</th>";
    $html_bot .= "\n                <th>%s</th>"; //#0 member_mng
    $html_bot .= "\n                <td>%s</td>"; //#1 member_mng_tel
    $html_bot .= "\n            </tr>";
    $html_bot .= "\n        </tbody>";
    $html_bot .= "\n    </table>";

    return sprintf($html_bot, $param["member_mng"]
        , $param["member_mng_tel"]);
}
?>
