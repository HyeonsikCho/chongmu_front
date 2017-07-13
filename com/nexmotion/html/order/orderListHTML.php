<?php

/*
 * 주문리스트 및 파일업로드 영역 생성
 *
 * return : list
 */
	function orderListHtml($rs,$design_dir){
		$list ="
		<table class=\"list _details order fileUploads\" id='prdlist%s'>
            <colgroup>
                <col width=\"40\">
                <col width=\"250\">
                <col >
                <col width=\"100\">
                <col width=\"120\">
                <col width=\"70\">
                <col width=\"70\">
            </colgroup>
            <thead>
                <tr>
                    <th>번호</th>
                    <th>인쇄물제목</th>
                    <th>상품정보</th>
                    <th>수량(건)</th>
                    <th>주문금액</th>
                    <th>상세보기</th>
                    <th>삭제</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td >%s</td>
                    <td class=\"_name\">%s</td>
                    <td>%s</td>
                    <td>%s(%s건)</td>
                    <td>%s원</td>
                    <td>
                        <button type='button' class=\"viewOrderDetails _showOrderDetails _on\" title=\"상세보기\"><img src=\"".$design_dir."/images/common/btn_table_circle_bottom.png\" alt=\"▼\"></button>
                        <button type='button' class=\"viewOrderDetails _hideOrderDetails\" title=\"상세접기\"><img src=\"".$design_dir."/images/common/btn_table_circle_top_blue.png\" alt=\"▲\"></button>
                    </td>
                    <td>
						<button type='button' class=\"deleteOrder\" title=\"삭제\" onClick=\"delOrder('%s','%s')\"><img src=\"".$design_dir."/images/common/btn_circle_x_red.png\" alt=\"X\"></button>
					</td>
                </tr>
                <tr class=\"_orderDetails\">
                    <td colspan=\"7\">
                        <div class=\"wrap\">
                            <figure><img src='%s' alt='상품명'></figure>
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
                                        <li>
                                            <span class=\"_output\">%s</span>
                                        </li>
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
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody class=\"fileUploads\">
                <tr>
                    <td colspan=\"7\">
                        <table class=\"input\">
                            <colgroup>
                                <col width=\"120\">
                                <col>
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th>작업파일</th>
                                    <td id='container%s' dvs='1' ono='%s' dno='%s' ><button type='button' id='pickfiles%s'>파일첨부</button><span id='filelist%s' class='filechk' fchk=true >%s</span>
									<button type='button' id='uploadfiles%s' style='display:none'>업로드</button>
									<p class=\"note\">작업 파일은 Adobe cs6, CorelDRAW 13 버전 이하로 저장하여 올려 주십시오.</p>
									</td>
                                </tr>
                                <!--tr>
                                    <th>후공정 작업파일</th>
                                    <td id='container%s' dvs='2' ono='%s' dno='%s'>
                                        <button type='button' id='pickfiles%s'>파일첨부</button><span id='filelist%s' class='filechk' fchk='true'>%s</span>
										<button type='button' id='uploadfiles%s' style='display:none'>업로드</button>
                                        <p class=\"note\">후공정 작업 파일은 한 파일로 작업하여 올려 주십시오.</p>
                                    </td>
                                </tr-->
                                <tr>
                                    <th>특이사항</th>
                                    <td>
                                        <div class=\"textareaWrap\"><textarea class=\"memo\" id='memo%s' prd_detail_no='%s'></textarea></div>
                                        <p class=\"note\">인쇄 시 작업자가 참고해야 할 사항을 적어주세요.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>";

	   $listcnt = 0;
	   $slistcnt = 0;
	   $seq = 1;
	   $seq_dep = 2;
	   $oList = null;

	   while ($rs && !$rs->EOF) {
			if(empty($title)) $title = $rs->fields['title'];

			if($slistcnt) $listcnt = $slistcnt;
			$slistcnt = $listcnt+1;
			$basic_price = intval($rs->fields["basic_price"]);
			$after_price = intval($rs->fields["add_after_price"]);
			$opt_price   = intval($rs->fields["add_opt_price"]);
			$img_file = explode("/",$rs->fields['img_name']);
			$img_dvs = explode("/",$rs->fields['img_dvs']);
			$cnt = count($img_dvs);
			$f_img1 = $f_img2 = '';
			$rep_arr = array('_1','_2');
		   	$img_path = $rs->fields["file_path"] . $rs->fields["save_file_name"];
			$addtax_prd_amnt = $rs->fields['addtax_prd_amnt'];
			for($i=0;$i<$cnt;$i++){
				if($img_dvs[$i] == 1){
					$f_img1 = str_replace($rep_arr,'',$img_file[$i]);

				}else{
					$f_img2 = str_replace($rep_arr,'',$img_file[$i]);
				}
			}
			$order_price = $basic_price + $after_price + $opt_price;

			$prd_text = $rs->fields["cate_name"]." / ".$rs->fields["paper_name"]." / ".$rs->fields["print_name"];
			/*$oList .= sprintf($list,$seq,$rs->fields['prd_detail_no'],
										$rs->fields['title'],
										$prd_text,
										$rs->fields['prd_amount'],
										$rs->fields['prd_count'],
										number_format($rs->fields['addtax_prd_amnt']),
										$rs->fields['order_no'],
										$rs->fields['prd_detail_no'],
										$img_path,
										$rs->fields['title'],
										$prd_text,
										$rs->fields['opt_name'],
										$rs->fields['after'],
										$rs->fields['prd_amount'],
										$rs->fields['prd_count'],
										$listcnt,
										$rs->fields['order_no'],
										$rs->fields['prd_detail_no'],
										$listcnt,
										$listcnt,
										$f_img1,
										$listcnt,
										$slistcnt,
										$rs->fields['order_no'],
										$rs->fields['prd_detail_no'],
										$slistcnt,
										$slistcnt,
										$f_img2,
										$slistcnt,$seq,$rs->fields['prd_detail_no']
										);*/

			$oList .= sprintf($list,$seq,$seq,
										$rs->fields['title'],
										$prd_text,
										$rs->fields['prd_amount'],
										$rs->fields['prd_count'],
										number_format($addtax_prd_amnt),
										$rs->fields['order_no'],
										$rs->fields['prd_detail_no'],
										$img_path,
										$rs->fields['title'],
										$prd_text,
										$rs->fields['opt_name'],
										$rs->fields['after'],
										$rs->fields['prd_amount'],
										$rs->fields['prd_count'],
										$listcnt,
										$rs->fields['order_no'],
										$rs->fields['prd_detail_no'],
										$listcnt,
										$listcnt,
										$f_img1,
										$listcnt,
										$slistcnt,
										$rs->fields['order_no'],
										$rs->fields['prd_detail_no'],
										$slistcnt,
										$slistcnt,
										$f_img2,
										$slistcnt,$seq,$rs->fields['prd_detail_no']
										);
			$order_amnt = $rs->fields["order_amnt"];
			$addtax_order_amnt = $rs->fields["addtax_order_amnt"];
			$all_addtax_prd_amnt = $rs->fields["all_addtax_prd_amnt"];
			$rs->MoveNext();
			$slistcnt++;
			$seq++;

	   }
	   $prd_cnt = $seq-2;
	   $f_title = $title;
	   if($seq > 2) $title .= $title."외 ".$prd_cnt."건";
	   if($oList === null){
		   $rt_val['html'] = '';
		   $rt_val['order_amnt'] = 0;
		   $rt_val['addtax_order_amnt'] = 0;
		   $rt_val['addtax_prd_amnt'] = 0;
		   $rt_val['all_addtax_prd_amnt'] = $all_addtax_prd_amnt;
		   $rt_val['title'] = $title;
		   $rt_val['f_title'] = $f_title;
	   }else{
		   $rt_val['html'] = $oList;
		   $rt_val['order_amnt'] = $order_amnt;
		   $rt_val['addtax_order_amnt'] = $addtax_order_amnt;
		   $rt_val['addtax_prd_amnt'] = $addtax_prd_amnt;
		   $rt_val['all_addtax_prd_amnt'] = $all_addtax_prd_amnt;
		   $rt_val['title'] = $title;
		   $rt_val['f_title'] = $f_title;
	   }
	   $rt_val['listcnt'] = $slistcnt;
		return $rt_val;
}
?>