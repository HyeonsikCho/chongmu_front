<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderAllDAO.php");

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
function makeClaimListHtml($conn, $result, $param, $type) {

    $orderDAO = new OrderAllDAO();

    $ret = "";
    
    $list .= "\n  <tbody name=\"claim_list\">";
    $list .= "\n  <tr %s>";
    $list .= "\n    <td>%d</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td class=\"subject\"><a href=\"#\" onclick=\"claimDetailMove(%d);\" target=\"_self\">%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>";
    $list .= "\n      <button class=\"viewOrderDetails _showOrderDetails _on\" title=\"상세보기\"><img src=\"/design_template/images/common/btn_table_circle_bottom.png\" alt=\"▼\"></button>";
    $list .= "\n      <button class=\"viewOrderDetails _hideOrderDetails\" title=\"상세접기\"><img src=\"/design_template/images/common/btn_table_circle_top_green.png\" alt=\"▲\"></button>";
    $list .= "\n   </td>";
    $list .= "\n  </tr>";
    $list .= "\n  <tr class=\"_orderDetails\">";
    $list .= "\n    <td colspan=\"8\">";
    $list .= "\n      <div class=\"wrap\">";
    $list .= "\n        <figure><img src=\"%s%s\" alt=\"상품명\"></figure>";
    $list .= "\n        <dl>";
    $list .= "\n          <dt>주문일</dt>";
    $list .= "\n          <dd>%s</dd>";
    $list .= "\n          <dt>주문번호</dt>";
    $list .= "\n          <dd>%s</dd>";
    $list .= "\n          <dt>접수구분</dt>";
    $list .= "\n          <dd>%s</dd>";
    $list .= "\n        </dl>";
    $list .= "\n        <dl>";
    $list .= "\n          <dt>인쇄물 제목</dt>";
    $list .= "\n          <dd>%s</dd>";
    $list .= "\n        </dl>";
    $list .= "\n        <dl>";
    $list .= "\n          <dt>상품내역</dt>";
    $list .= "\n          <dd>%s";
    $list .= "\n          </dd>";
    $list .= "\n        </dl>";
    $list .= "\n        <dl>";
    $list .= "\n          <dt>후공정</dt>";
    $list .= "\n            <dd>";
    $list .= "\n                %s";
    $list .= "\n            </dd>";
    $list .= "\n        </dl>";
    $list .= "\n        <dl>";
    $list .= "\n          <dt>수량/건</dt>";
    $list .= "\n          <dd>%s x %s 건</dd>";
    $list .= "\n        </dl>";
    $list .= "\n        <dl>";
    $list .= "\n          <dt>할인내역</dt>";
    $list .= "\n          <dd>";
    $list .= "\n            <ul class=\"information\">";
    $list .= "\n              <li>회원등급할인 &#8361; %s</li>";
    $list .= "\n              <li>이벤트할인 &#8361; %s</li>";
    $list .= "\n              <li>쿠폰할인 &#8361; %s</li>";
    $list .= "\n            </ul>";
    $list .= "\n          </dd>";
    $list .= "\n        </dl>";
    $list .= "\n        <dl>";
    $list .= "\n          <dt>예상무게</dt>";
    $list .= "\n          <dd>%s</dd>";
    $list .= "\n          <dt>배송</dt>";
    $list .= "\n          <dd>%s</dd>";
    $list .= "\n        </dl>";
    $list .= "\n        </div>";
    $list .= "\n    </td>";
    $list .= "\n  </tr>";
    $list .= "\n </tbody>";

    $i = $param["count"] - $param["s_num"];

    if ($result) {
        $total_cnt = $result->recordCount();
    }

    while ($result && !$result->EOF) {

        $regi_date = substr($result->fields["regi_date"], 0,10);

        $today = date("Y-m-d", time());

        $class_style = "";
        if ($regi_date == $today) {

            $class_style = "class=\"waiting new\"";

        }

        $order_num = $result->fields["order_num"];
        $print_title = $result->fields["print_title"];
        $title = $result->fields["title"];
        $dvs = $result->fields["dvs"];
        $state = $result->fields["state"];
        $order_date = substr($result->fields["order_regi_date"], 0,10);
        $receipt_dvs = $result->fields["receipt_dvs"];
        $order_detail = $result->fields["order_detail"];
        $order_seqno = $result->fields["order_common_seqno"];
        $claim_seqno = $result->fields["order_claim_seqno"];


        $after_param = array();
        $after_param["order_seqno"] = $order_seqno;
        $after_rs = $orderDAO->selectOrderAfter($conn, $after_param);

        $after_html = "";
        while ($after_rs && !$after_rs->EOF) {

            $after_name = $after_rs->fields["after_name"];
            $depth1 = $after_rs->fields["depth1"];
            $depth2 = $after_rs->fields["depth2"];
            $depth3 = $after_rs->fields["depth3"];

            if ($after_name == "-" || $after_name == "") {

                $after_name = "";

            } else {

                $after_name = "<li>" . $after_name . "</li>";

            }

            if ($depth1 == "-" || $depth1 == "") {

                $depth1 = "";

            } else {

                $depth1 = "<li>" . $depth1 . "</li>";

            }

            if ($depth2 == "-" || $depth2 == "") {

                $depth2 = "";

            } else {

                $depth2 = "<li>" . $depth2 . "</li>";

            }

            if ($depth3 == "-" || $depth3 == "") {

                $depth3 = "";

            } else {

                $depth3 = "<li>" . $depth3 . "</li>";

            }

            $after_html .= "\n              <ul class=\"information\">";
            $after_html .= $after_name . $depth1 . $depth2 . $depth3;
            $after_html .= "\n              </ul>";

            $after_rs->moveNext();
        }

        $amt = $result->fields["amt"];
        $count = $result->fields["count"];
        $pay_price = $result->fields["pay_price"];
        $order_state = $result->fields["order_state"];
        $grade_price = $result->fields["grade_sale_price"]; 
        //등급할인금액이 없을때
        if ($grade_price == "") $grade_price = 0;

        //이벤트할인금액이 없을때
        $event_price = $result->fields["event_price"]; 
        if ($event_price == "") $event_price = 0;

        //쿠폰할인금액이 없을때
        $cp_price = $result->fields["cp_price"]; 
        if ($cp_price == "") $cp_price = 0;

        $expec_weight = $result->fields["expec_weight"];
        $dlvr_way = $result->fields["dlvr_way"];

        $file_path = $result->fields["file_path"];
        $file_name = $result->fields["save_file_name"];

        $ret .= sprintf($list
                ,$class_style
                ,$i
                ,$regi_date 
                ,$order_num
                ,$print_title
                ,$claim_seqno
                ,$title
                ,$dvs
                ,$state
                ,$file_path
                ,$file_name
                ,$order_date 
                ,$order_num
                ,$receipt_dvs
                ,$print_title
                ,$order_detail
                ,$after_html
                ,number_format($amt)
                ,$count
                ,number_format($grade_price)
                ,number_format($event_price)
                ,number_format($cp_price)
                ,$expec_weight
                ,$dlvr_way); 

        $i--;
        $result->moveNext();
    }

    return $ret;
}


?>
