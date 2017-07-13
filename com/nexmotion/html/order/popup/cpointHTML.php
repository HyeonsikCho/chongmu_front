<?
	function getCpointHTML($param){
		if($param['RES_CD'] == '0000'){
			$html = "<header>
						<h2>마일리지 현황</h2>
						<button class=\"close\" title=\"닫기\"><img src=\"/design_template/images/common/btn_circle_x_white.png\" alt=\"X\"></button>
					</header>
					<article>
						<ul class=\"amount\">
							<li><label><h3>보유 마일리지</h3> <input type=\"text\" id='user_point' readonly value=\"%s\"></label> P</li>
							<li><label><h3>사용 마일리지</h3>
								<input type=\"text\" id='use_point' value=\"0\">
								<input type='hidden' id='plzp_id' value='%s'>
							</label> P</li>
						</ul>
						<div class=\"function center\">
							<strong><button onClick='getPoint()' class=\"close\"  style='height: 40px;'>사용</button></strong>
							<strong><button onClick='getAllPoint()' class=\"close\"  style='height: 40px;'>전액사용</button></strong>
							<button class=\"close\"  style='height: 40px;'>취소</button>
						</div>
					</article>";
		}else{
			$html = "<header>
						<h2>마일리지 현황</h2>
						<button class=\"close\" title=\"닫기\"><img src=\"/design_template/images/common/btn_circle_x_white.png\" alt=\"X\"></button>
					</header>
					<article>
						<ul class=\"amount\" %s>
							<li>마일리지 조회오류 (관리자에게 문의하세요)</li>
						</ul>

					</article>";
		}
		//print_r($param);
		$html = sprintf($html,$param['cpoint'],$param['plzp_id']);
		return $html;
	}
?>
