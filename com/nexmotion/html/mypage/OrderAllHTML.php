<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/order_status.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_lib/CommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderAllDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/product_info_class.php");
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
function makeOrderListHtml($conn, $result, $param, $type) {
    $orderDAO = new OrderAllDAO();
    $util = new CommonUtil();

    $ret = "";

    $list = "\n  <tbody name=\"order_list\">";
    $list .= "\n  <tr>";
    $list .= "\n    <td>%d</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";

    if ($type == "unpaid") {

        $list .= "\n    <td><button class=\"tableFunction\" onclick=\"orderPopup('l_orderCancel', '/design_template/mypage/popup/l_ordercancel.html', %d)\"><img src=\"/design_template/images/mypage/btn_text_ordercancel.png\" alt=\"주문취소\"></button></td>";

    } else if ($type == "reorder") {

        $list .= "\n    <td><button class=\"tableFunction\" onclick=\"orderPopup('l_reorder', '/design_template/mypage/popup/l_reorder.html', %d)\"><img src=\"/design_template/images/mypage/btn_text_reorder.png\" alt=\"재주문\"></button></td>";

    } else if ($type == "draft") {

        $list .= "\n    <td><button class=\"tableFunction\" onclick=\"orderPopup('l_draft', '/design_template/mypage/popup/l_draft.html', %d)\"><img src=\"/design_template/images/mypage/btn_text_draft.png\" alt=\"시안보기\"></button></td>";

    }

    $list .= "\n    <td>";
    $list .= "\n      <button class=\"viewOrderDetails _showOrderDetails _on\" title=\"상세보기\"><img src=\"/design_template/images/common/btn_table_circle_bottom.png\" alt=\"▼\"></button>";
    $list .= "\n      <button class=\"viewOrderDetails _hideOrderDetails\" title=\"상세접기\"><img src=\"/design_template/images/common/btn_table_circle_top_green.png\" alt=\"▲\"></button>";
    $list .= "\n   </td>";
    $list .= "\n  </tr>";
    $list .= "\n  <tr class=\"_orderDetails\">";

    if ($type == "unpaid" || $type == "reorder" || $type == "draft") {

        $list .= "\n    <td colspan=\"10\">";

    } else {

        $list .= "\n    <td colspan=\"9\">";
    }

    $list .= "\n      <div class=\"wrap\">";
    $list .= "\n        <figure><img src=\"%s%s\" alt=\"%s\"></figure>";
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
    $list .= "\n        <dl>";
    $list .= "\n          <dt>관리</dt>";
    $list .= "\n          <dd>";
    $list .= "\n            <button onclick=\"orderPopup('l_orderCancel', '/design_template/mypage/popup/l_ordercancel.html', %d, '')\"><img src=\"/design_template/images/mypage/btn_text_ordercancel.png\" alt=\"주문취소\"></button>";
    $list .= "\n            <button onclick=\"orderPopup('l_draft','/design_template/mypage/popup/l_draft.html', %d, '')\"><img src=\"/design_template/images/mypage/btn_text_draft.png\" alt=\"시안보기\"></button>";
    $list .= "\n            <button><img src=\"/design_template/images/mypage/btn_text_deliverytracking.png\" alt=\"배송조회\"></button>";
    $list .= "\n            <button onclick=\"orderPopup('l_reorder', '/design_template/mypage/popup/l_reorder.html', %d, '')\"><img src=\"/design_template/images/mypage/btn_text_reorder.png\" alt=\"재주문\"></button>";
    $list .= "\n            <button><img src=\"/design_template/images/mypage/btn_text_claim.png\" onclick=\"reqClaim();\" alt=\"클레임요청\"></button>";
    $list .= "\n            <button onclick=\"loadOrderMemo(%d);\"><img src=\"/design_template/images/mypage/btn_text_memo.png\" alt=\"메모\"></button>";
    $list .= "\n            </dd>";
    $list .= "\n         </dl>";
    $list .= "\n        </div>";
    $list .= "\n    </td>";
    $list .= "\n  </tr>";
    $list .= "\n </tbody>";

    $i = $param["count"] - $param["s_num"];

    if ($result) {
        $total_cnt = $result->recordCount();
    }

    while ($result && !$result->EOF) {

        $regi_date = substr($result->fields["order_regi_date"], 0,10);
        $order_num = $result->fields["order_num"];
        $title = $result->fields["title"];
        $order_detail = $result->fields["order_detail"];
        $amt = $result->fields["amt"];
        $count = $result->fields["count"];
        $order_seqno = $result->fields["order_common_seqno"];

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

        $pay_price = $result->fields["pay_price"];
        $order_state = $result->fields["order_state"];
        $state = $util->statusCode2status($order_state);

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

        $cate_name = $result->fields["cate_name"];

        //파일 경로 생겼을때 img 보이게 수정해야함
        $file_path = $result->fields["file_path"];
        $file_name = $result->fields["save_file_name"];

        if ($type == "") {

            $ret .= sprintf($list, $i
                    ,$regi_date
                    ,$order_num
                    ,$title
                    ,$order_detail
                    ,$amt . "(" . $count . "건)"
                    ,number_format($pay_price) . "원"
                    ,$state
                    ,$file_path
                    ,$file_name
                    ,$cate_name
                    ,$title
                    ,$order_detail
                    ,$after_html
                    ,number_format($amt)
                    ,$count
                    ,number_format($grade_price)
                    ,number_format($event_price)
                    ,number_format($cp_price)
                    ,$expec_weight
                    ,$dlvr_way
                    ,$order_seqno
                    ,$order_seqno
                    ,$order_seqno
                    ,$order_seqno);

        } else {

            $ret .= sprintf($list, $i
                    ,$regi_date
                    ,$order_num
                    ,$title
                    ,$order_detail
                    ,$amt . "(" . $count . "건)"
                    ,number_format($pay_price) . "원"
                    ,$state
                    ,$order_seqno
                    ,$file_path
                    ,$file_name
                    ,$cate_name
                    ,$title
                    ,$order_detail
                    ,$after_html
                    ,number_format($amt)
                    ,$count
                    ,number_format($grade_price)
                    ,number_format($event_price)
                    ,number_format($cp_price)
                    ,$expec_weight
                    ,$dlvr_way
                    ,$order_seqno
                    ,$order_seqno
                    ,$order_seqno
                    ,$order_seqno);

        }


        $i--;
        $result->moveNext();
    }

    return $ret;
}
	function orderListHtml($conn, $result,$design_dir){
		$status_arr = OrderStatus::ORDER_STATUS_ARR;

		if ($result) {
			$total_cnt = $result->recordCount();
		}
		$list = "<tbody class='olist' name='order_list'>
				<tr>
				<td>%s</td>
				<td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td class=\"subject\">
					%s
                </td>
                <td>%s(%s건)</td>
                <td>%s</td>
                <td>
                    <button class=\"viewOrderDetails _showOrderDetails _on\" title=\"상세보기\"><img src=\"".$design_dir."/images/common/btn_table_circle_bottom.png\" alt=\"▼\"></button>
                    <button class=\"viewOrderDetails _hideOrderDetails\" title=\"상세접기\"><img src=\"".$design_dir."/images/common/btn_table_circle_top_green.png\" alt=\"▲\"></button>
                </td>
            </tr>
            <tr class=\"_orderDetails\">
                <td colspan=\"8\">
                    <div class=\"wrap\">
                        <figure><a href=\"http://hiprint.biz/%s%s\" target=\"_blank\"><img src=\"http://hiprint.biz/%s%s\" alt=\"이미지\"></a></figure>
                        <dl>
                            <dt>인쇄물 제목</dt>
                            <dd>%s</dd>
                        </dl>
                        <dl>
                            <dt>상품내역</dt>
                            <dd>
                                <ul class=\"information\">
                                    %s
                                </ul>
                            </dd>
                        </dl>
						<dl>
							<dt>옵션</dt>
							<dd>
								<ul class=\"information\">
									<li>%s</li>
								</ul>
							</dd>
						</dl>
						<dl>
							<dt>후공정</dt>
							<dd>
								<ul class=\"information\">
									<li>%s</li>
								</ul>
							</dd>
						</dl>
						<dl>
							<dt>수량/건</dt>
							<dd>
								<span class=\"_output\">%s</span>
								<span>x</span>
								<span class=\"_output\">%s</span>
								건
							</dd>
						</dl>

                        <dl>
                            <dt>배송</dt>
                            <dd>택배</dd>
                        </dl>
                        <dl>
                            <dt>관리</dt>
                            <dd>

                                %s %s
                            </dd>
                        </dl>
                    </div>
                </td>
            </tr>
        </tbody>";
		$out_list = '';
		 while ($result && !$result->EOF) {
			 if($result->fields['prd_status'] > 330){
				$draft_btn = "<button onclick=\"draftPop('".$result->fields['order_no']."','".$result->fields['prd_detail_no']."')\">
				<img src=\"".$design_dir."/images/mypage/btn_text_draft.png\" alt=\"시안보기\"></button>";
			 }else{
				 $draft_btn='';
			 }
			 if($result->fields['prd_status'] > 330){
				$dliv_btn = "<button><img src=\"".$design_dir."/images/mypage/btn_text_deliverytracking.png\" alt=\"배송조회\"></button>";
			 }else{
				 $dliv_btn='';
			 }

			 $title = $result->fields['title'];
			 if($result->fields['o_cnt'] > 1){
				 $title .= '외 '.(int)($result->fields['o_cnt']-1).'건';
			 }
			 $prd_text = $result->fields["cate_name"]." / ".$result->fields["paper_name"]." / ".$result->fields["print_name"];
			if($result->fields['file_path'] == "")
			 {
				$result->fields['file_path'] = "design_template/images/no_image_75_75.jpg";
			 }
			 $out_list .= sprintf($list,$total_cnt,
										date('Y-m-d',strtotime($result->fields['order_etime'])),
										$result->fields['order_no'],
										$title,
										$prd_text,
										$result->fields['prd_amount'],
										$result->fields['prd_count'],
										$status_arr[$result->fields['prd_status']],
                                        $result->fields['file_path'],
                                        $result->fields['save_file_name'],
                                        $result->fields['file_path'],
                                        $result->fields['save_file_name'],
										$title,
										$prd_text,
										$result->fields['opt_name'],
										$result->fields['after_name'],
										$result->fields['prd_amount'],
										$result->fields['prd_count'],
										$draft_btn,
										$dliv_btn);
			         $result->moveNext();
					 $total_cnt--;
		 }
		 return $out_list;
	}

	function makeDraftHTML($result){
		$list .="<header>
						<h2>시안보기</h2>
						<button class=\"close\" title=\"닫기\"><img src=\"/design_template/images/common/btn_circle_x_white.png\" alt=\"X\"></button>
					</header>
					<article>
						<section class=\"draftImage\"><img src='%s' width='500' height='400' onClick=\"window.open('%s')\"></section>
						<ul class=\"draftCheck\">
							<li><label><input type=\"radio\" name=\"draftCheck\" value='1'> 시안을 확있했으며, 이대로 인쇄를 진행하겠습니다.</label></li>
							<li><label><input type=\"radio\" name=\"draftCheck\" value='2' class=\"error\"> 시안에 오류가 있습니다.</label>
								<div class=\"textareaWrap\">
									<textarea disabled name='draft_comment' id='draft_comment'></textarea>
								</div>
							</li>
						</ul>
						<div class=\"function center\">
							<strong><button class=\"confirm\">확인</button></strong>
							<button class=\"close\">취소</button>
						</div>
					</article>
					<input type='hidden' name='order_no' id='order_no' value='%s'>
					<input type='hidden' name='prd_detail_no' id='prd_detail_no' value='%s'>
					<script>
						$('.l_draft .draftCheck input[type=radio]').on('click', function () {
							if ($(this).hasClass('error')) {
								alert('시안의 오류를 작성해주세요.');
								$('.l_draft textarea').attr('disabled', false);
							} else {
								$('.l_draft textarea').attr('disabled', true);
							}
						});

						$('.l_draft button.confirm').on('click', function () {
							draftResult($('#order_no').val(),$('#prd_detail_no').val(),$('.l_draft .draftCheck input[type=radio]:checked').val(),$('#draft_comment').val());
						});

					</script>";
		$img = "/".$result->fields['file_path'].$result->fields['save_file_name'];
		$list = sprintf($list,$img,$img,$result->fields['order_no'],$result->fields['prd_detail_no']);
		return $list;

	}

?>
