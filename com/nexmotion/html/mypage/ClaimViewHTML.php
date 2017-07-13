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
function makeClaimComment($result) {

    $list = "";

    while ($result && !$result->EOF) {

        $comment = $result->fields["comment"];
        $regi_date = $result->fields["regi_date"];
        $cust_yn = $result->fields["cust_yn"];

        if ($cust_yn == "Y") {

            $list .= "\n  <dl class=\"reply customer\">";
            $list .= "\n    <dt>고객님</dt>";
            $list .= "\n    <dd>" . $comment . "</dd>";
            $list .= "\n  </dl>";

        } else {

            $list .= "\n  <dl class=\"reply cs\">";
            $list .= "\n    <dt>담당자</dt>";
            $list .= "\n    <dd>" . $comment . "</dd>";
            $list .= "\n  </dl>";
        }
 

        $result->moveNext();
    }

    return $list;
}

?>
