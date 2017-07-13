<?
/* 
 * 주문 list  생성 
 * $result : $result->fields["order_regi_date"] = "주문등록일자" 
 * $result : $result->fields["order_num"] = "주문번호" 
 * $result : $result->fields["title"] = "인쇄물제목" 
 * $result : $result->fields["order_detail"] = "주문 상세" 
 * $result : $result->fields["amt"] = "수량" 
 * $result : $result->fields["pay_price"] = "결제금액" 
 * $result : $result->fields["order_state"] = "주문상태" 
 * $result : $result->fields["order_common_seqno"] = "주문 공통 일련번호" 
 * 
 * return : list
 */
function makeCpListHtml($result, $param) {

    $ret = "";
    
    $list .= "\n  <tr>";
    $list .= "\n    <td>%d</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td class=\"subject\">%s<span class=\"request\"> %s</span>%s</td>";
    $list .= "\n    <td>%s까지</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n  </tr>";

    $i = $param["count"] - $param["s_num"];

    if ($result) {
        $total_cnt = $result->recordCount();
    }

    while ($result && !$result->EOF) {

        $cp_name = $result->fields["cp_name"];
        $val = $result->fields["val"];
        $unit = $result->fields["unit"];
        $max_sale_price = $result->fields["max_sale_price"];
        $min_order_price = $result->fields["min_order_price"];
        $use_start_date = $result->fields["use_able_start_date"];

        $use_cnd = "";
        $use_val = "";
        $use_dvs = "";

        //요율일때
        if ($max_sale_price != "") {

            $use_cnd = "최대 ";
            $use_val = "&#8361;" . number_format($max_sale_price);
            $use_dvs = " 할인";
        }

        //금액일때
        if ($min_order_price != "") {

            $use_cnd = "주문금액 ";
            $use_val =  "&#8361;" . number_format($min_order_price);
            $use_dvs =  " 이상";
        } 

        $use_deadline = substr($result->fields["use_deadline"], 0,10);
        $issue_date = substr($result->fields["issue_date"], 0,10);
        $use_yn = $result->fields["use_yn"];
        $today = date("Y-m-d H:i:s", time());

        $state = "";
        //사용
        if ($use_yn == "N") {

            //사용기한이 현재 날짜보다 크고 현재날짜가 사용 가능 시작 일자보다 클때
            if ($today <= $use_deadline && $today >= $use_start_date) {

                $state = "사용가능";


            //현재 날짜가 사용기한보다 클때
            } else {

                $state = "기한만료";

            }
        }

        $ret .= sprintf($list
                ,$i
                ,$cp_name
                ,number_format($val) . $unit
                ,$use_cnd
                ,$use_val
                ,$use_dvs
                ,$use_deadline
                ,$issue_date
                ,$state); 

        $i--;
        $result->moveNext();
    }

    return $ret;
}





?>
