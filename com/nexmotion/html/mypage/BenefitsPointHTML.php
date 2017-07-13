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
function makePointListHtml($conn, $result, $param, $type) {

    $ret = "";
    
    $list .= "\n  <tr>";
    $list .= "\n    <td>%d</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td %s>%s</td>";
    $list .= "\n    <td %s>%s</td>";
    $list .= "\n    <td %s>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n  </tr>";

    $i = $param["count"] - $param["s_num"];

    if ($result) {
        $total_cnt = $result->recordCount();
    }

    while ($result && !$result->EOF) {

        $regi_date = substr($result->fields["regi_date"], 0,10);
        $dvs = $result->fields["dvs"];
        $point = $result->fields["point"];
        $dvs_class = "";
        $plus_class = "";
        $minus_class = "";

        if ($dvs == "적립") {

            $save_point = number_format($point);
            $use_point = "-";
            $dvs_class = " class=\"plus\"";
            $plus_class = " class=\"plus\"";

        } else if ($dvs == "사용") {

            $use_point = number_format($point);
            $save_point = "-";
            $dvs_class= " class=\"minus\"";
            $minus_class= " class=\"minus\"";

        }

        $order_num = $result->fields["order_num"];
        $order_price = number_format($result->fields["order_price"]);
        $rest_point = number_format($result->fields["rest_point"]);

        $ret .= sprintf($list
                ,$i
                ,$regi_date
                ,$dvs_class
                ,$dvs
                ,$plus_class
                ,$save_point
                ,$minus_class
                ,$use_point 
                ,$order_num
                ,"&#8361;" . $order_price
                ,$rest_point . "p"); 

        $i--;
        $result->moveNext();
    }

    return $ret;
}



?>
