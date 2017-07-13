<?
function str_cut($str, $start = 0, $end, $tail = "..") {

    if (!$str) return "";

    if (strlen($str) > $end)
        return mb_substr($str, $start, $end, 'UTF-8') . $tail;
    else
        return $str;
}

/**
 * @brief 견적 리스트 HTML
 */
function makeEstiListHtml($rs, $param) {

    if (!$rs) {
        return false;
    }

    $rs_html = "";
    $html  = "\n  <tr class='%s %s'>";
    $html .= "\n    <td>%d</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class='subject'><a style=\"cursor:pointer;\" onclick=\"estimateView(%s)\">%s</a></td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class=\"status\">%s</td>";
    $html .= "\n  </tr>";
    $i = $param["count"] - $param["s_num"];

    while ($rs && !$rs->EOF) {

        //오늘날짜보다 차이 1일차면 new
        if ( time() - strtotime($rs->fields["regi_date"]) < 60*60*24*1 )
            $new = "new";
        else
            $new = "";

        if ($rs->fields["state"] == "견적완료")
            $class = "";
        else
            $class = "waiting";


        $req_date = "-";
        if ($rs->fields["req_date"])
            $req_date = date("Y-m-d", strtotime($rs->fields["req_date"]));

        $regi_date = "-";
        if ($rs->fields["regi_date"])
            $regi_date = date("Y-m-d", strtotime($rs->fields["regi_date"]));


        //상태별 이미지 필요
        $buttons = "<img src=\"/design_template/images/mypage/text_estimate_waiting.png\" alt=\"견적대기\">";

        $buttons = $rs->fields["state"];

        $rs_html .= sprintf($html,
                            $class,
                            $new,
                            $i, //$rs->fields["esti_seqno"],									// 번호
                            $req_date,															// 요청일
                            $rs->fields["esti_seqno"],
                            //str_cut($rs->fields["title"], 0, 80, "..."),
                            $rs->fields["title"],												// 문의제목
                            number_format($rs->fields["esti_price"]) . "원",		// 견적금액
                            $regi_date,															// 등록일
							$rs->fields["user_nm"],
                            $buttons);
        $i--;
        $rs->moveNext();
    }

    return $rs_html;
}
?>

