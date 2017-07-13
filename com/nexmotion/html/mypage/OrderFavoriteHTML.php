<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderFavoriteDAO.php");

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
function makePrdtListHtml($conn, $result, $param) {

    $orderDAO = new OrderFavoriteDAO();

    $ret = "";
    
    $list .= "\n  <tbody name=\"prdt_list\">";
    $list .= "\n  <tr>";
    $list .= "\n    <td><input name=\"prdt_chk\" value=\"%d\" type=\"checkbox\" class=\"_individual\"></td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>";
    $list .= "\n      <button class=\"viewOrderDetails _showOrderDetails _on\" title=\"상세보기\"><img src=\"/design_template/images/common/btn_table_circle_bottom.png\" alt=\"▼\"></button>";
    $list .= "\n      <button class=\"viewOrderDetails _hideOrderDetails\" title=\"상세접기\"><img src=\"/design_template/images/common/btn_table_circle_top_green.png\" alt=\"▲\"></button>";
    $list .= "\n   </td>";
    $list .= "\n  </tr>";
    $list .= "\n  <tr class=\"_orderDetails\">";
    $list .= "\n      <td colspan=\"5\">";
    $list .= "\n      <div class=\"wrap\">";
    $list .= "\n        <figure><img src=\"%s%s\" alt=\"상품명\"></figure>";
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
    $list .= "\n          <dt>예상무게</dt>";
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
        $order_detail = $result->fields["order_detail"];
        $amt = $result->fields["amt"];
        $count = $result->fields["count"];
        $prdt_seqno = $result->fields["interest_prdt_seqno"];

        $after_param = array();
        $after_param["prdt_seqno"] = $prdt_seqno;
        $after_rs = $orderDAO->selectPrdtAfter($conn, $after_param);

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

        //파일 경로 생겼을때 img 보이게 수정해야함
        $file_path = $result->fields["file_path"];
        $file_name = $result->fields["save_file_name"];

        $expec_weight = $result->fields["expec_weight"];

        $ret .= sprintf($list, $prdt_seqno
                ,$regi_date 
                ,$order_detail
                ,$amt . "(" . $count . "건)"
                ,$file_path
                ,$file_name
                ,$order_detail
                ,$after_html
                ,$amt
                ,$count
                ,$expec_weight); 
 
        $i--;
        $result->moveNext();
    }

    return $ret;
}



?>
