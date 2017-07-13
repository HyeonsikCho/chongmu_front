<?php
/*
 * 주문리스트 및 파일업로드 영역 생성
 *
 * return : list
 */
	function cartListHTML($rs,$design_dir){
		$dHtmlList = "
					<tbody>
						<tr>
							<td colspan='8'>장바구니에 담긴 상품이 없습니다.</td>
						</tr>
					</tbody>";
		$HtmlList = "
        <tbody>
            <tr>
                <td><input type=\"checkbox\" class=\"_individual cart_prdlist_id\" name=\"cart_prdlist_id[]\" value='%s'>
					<input type=\"hidden\" name=\"prd_amnt[]\" class='prd_amnt' value='%s' >
					<input type=\"hidden\" name=\"taxadd_prd_amnt[]\" class='taxadd_prd_amnt' value='%s' >
				</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td class=\"subject\">
				%s
                </td>
                <td>%s(%s건)</td>
                <td>%s원</td>
                <td>
                    <button type='button' class=\"viewOrderDetails _showOrderDetails _on\" title=\"상세보기\"><img src=\"".$design_dir."/images/common/btn_table_circle_bottom.png\" alt=\"▼\"></button>
                    <button type='button' class=\"viewOrderDetails _hideOrderDetails\" title=\"상세접기\"><img src=\"".$design_dir."/images/common/btn_table_circle_top_blue.png\" alt=\"▲\"></button>
                </td>
            </tr>
            <tr class=\"_orderDetails\">
                <td colspan=\"8\">
                    <div class=\"wrap\">
                        <dl>
                            <dt>인쇄물 제목</dt>
                            <dd>
                                <span class=\"_output\">%s</span>
                            </dd>
                        </dl>
                        <dl>
                            <dt>상품내역</dt>
                            <dd>
                                <ul class=\"information\">
									<li>%s</li>
                                    <li>%s</li>
                                    <li>%s</li>
                                </ul>
                            </dd>
                        </dl>
                        <dl>
                            <dt>옵션</dt>
                            <dd>
                                %s
                            </dd>
                        </dl>
                        <dl>
                            <dt>후공정</dt>
                            <dd>
                                %s
                            </dd>
                        </dl>
                        <dl>
                            <dt>수량/건</dt>
                            <dd>
                                <span class=\"_output\">%s/(%s)건</span>
                            </dd>
                        </dl>
                    </div>
                </td>
            </tr>
        </tbody>";


	   $seq = 1;
	   $cart_html = null;
	   while ($rs && !$rs->EOF) {
			$basic_price = intval($rs->fields["basic_price"]);
			$after_price = intval($rs->fields["add_after_price"]);
			$opt_price   = intval($rs->fields["add_opt_price"]);

			$order_price = $basic_price + $after_price + $opt_price;

			$prd_text = $rs->fields["cate_name"]." / ".$rs->fields["paper_name"]." / ".$rs->fields["print_name"];

			$cart_html .= sprintf($HtmlList,$rs->fields["cart_prdlist_id"], $rs->fields["tot_amnt"],$rs->fields["tot_amnt"]+($rs->fields["tot_amnt"]*0.1), $seq,date("Y-m-d", strtotime($rs->fields["regdate"])),
										 $rs->fields["title"],
										 $prd_text,
										 $rs->fields["prd_amount"],
										 $rs->fields["prd_count"],
										 number_format($rs->fields["tot_amnt"]),
										 $rs->fields["title"],
										 $rs->fields["cate_name"],
										 $rs->fields["paper_name"],
										 $rs->fields["print_name"],
										 $rs->fields["opt_name"],
										 $rs->fields["after_name"],
										 $rs->fields["prd_amount"],
										 $rs->fields["prd_count"]);
			$order_amnt += $rs->fields["tot_amnt"];
			$rs->MoveNext();
			$seq++;

	   }
	   if($cart_html === null){
		   $rt_val['html'] = $dHtmlList;
		   $rt_val['order_amnt'] = 0;
	   }else{
		   $rt_val['html'] = $cart_html;
		   $rt_val['order_amnt'] = $order_amnt;
	   }
	   return $rt_val;
}
?>