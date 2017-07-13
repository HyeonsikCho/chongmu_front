<?
//공지사항 리스트 - 일반
function makeNoticeListHtml($rs, $param) {

    if (!$rs) {
        return false;
    }

    $today = date("Y-m-d");

    $rs_html = "";
    $html  = "\n  <tr class='%s'>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class=\"subject\"><a href=\"/cscenter/notice_view.html?seqno=%s\" target=\"_self\">%s</a></td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n  </tr>";
    $i = $param["count"] - $param["s_num"];

    while ($rs && !$rs->EOF) {

        $class = "";
        $regi_date = date("Y-m-d", strtotime($rs->fields["regi_date"]));

        if ($today == $regi_date) {
            $class = "new";
        }

        $rs_html .= sprintf($html, $class,
                $i,
                $rs->fields["seq_no"],
                $rs->fields["title"],
                "관리자",
                $regi_date,
                $rs->fields["hits"]);
        $i--;
        $rs->moveNext();
    }

    return $rs_html;
}

//공지사항 리스트 - 공지
function makeNotiListHtml($rs) {

    if (!$rs) {
        return false;
    }

    $today = date("Y-m-d");

    $rs_html = "";
    $html  = "\n  <tr class='%s'>";
    $html .= "\n    <td></td>";
    $html .= "\n    <td class=\"subject\"><span class=\"%s\">%s</span><a href=\"/cscenter/notice_view.html?seqno=%s\" target=\"_self\"> %s</a></td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n  </tr>";

    while ($rs && !$rs->EOF) {
        if ($rs->fields["dvs"] != 0) {
            $class = "";
            $regi_date = date("Y-m-d", strtotime($rs->fields["regi_date"]));

            if ($today == $regi_date) {
                $class = "new";
            }

            $dvs = "";
            $dvs_class = "";
            if ($rs->fields["dvs"] == 1) {
                $dvs = "[호환성문제]";
                $dvs_class = "important";
            } else if ($rs->fields["dvs"] == 2) {
                $dvs = "[긴급]";
                $dvs_class = "alert";
            }

            $rs_html .= sprintf($html, $class,
                    $dvs_class,
                    $dvs,
                    $rs->fields["seq_no"],
                    $rs->fields["title"],
                    "",
                    $regi_date,
                    $rs->fields["hits"]);
		}
		$rs->moveNext();
    }
    return $rs_html;
}


function makeNotiRecent5ListHtml($rs) {

    if (!$rs) {
        return false;
    }

    $rs_html = "";
    $html =  '<li><span class="notice_title" style="height: 22px;"><a href="/cscenter/notice_view.html?seqno=%s" target="_self">%s </a></span><span class="notice_date" style="
    height: 20px;">%s</span></li>  ';

	$regi_date = substr($rs->fields["regi_date"], 0, 10);

	$i = 0;
    while ($rs && !$rs->EOF) {
            $rs_html .= sprintf($html,
                    $rs->fields["seq_no"],
					$rs->fields["title"],
					$regi_date);
            $rs->moveNext();
			$i++;
    }

	while($i < 5) {
		$rs_html .= '<li><a href="#none" target="_self"></a></li>';
		$i++;
	}

    return $rs_html;
}
?>
