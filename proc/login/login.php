<?
	/*
	로그인 관련 프로세스 처리

	*/

	function errPrint(){ //에러처리 합니다. 아직 안정함
		echo "<script>
				alert('로그아웃 처리되었습니다. 재로그인 해주시기 바랍니다');
			 </script>";
		exit;
	}

	include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/LoginCommonDAO.php");
	include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");


	/* 총무팀 기본필요값 아이디/이름/전화번호 */
	if(!empty($fb->fb['MYSEC_ID']) && !empty($fb->fb['USER_NM']) && !empty($fb->fb['PH_NO'])){
		$connectionPool = new ConnectionPool();
		$conn = $connectionPool->getPooledConnection();
		$dao = new LoginCommonDAO();

		$param['MYSEC_ID'] = $fb->fb['MYSEC_ID'];
		$param['USER_NM'] = $fb->fb['USER_NM'];
		$param['PH_NO'] = $fb->fb['PH_NO'];
		$param['TEL_NO'] = $fb->fb['TEL_NO'];
		$param['ZIPCODE'] = $fb->fb['ZIPCODE'];
		$param['ADDR1'] = $fb->fb['ADDR1'];
		$param['ADDR2'] = $fb->fb['ADDR2'];
		$param['SITE_CD'] = SITE_CD;
		$param['AUTH_CD'] = AUTH_CD;
		$param['SVC_NO'] = SVC_NO;

		$conn->StartTrans();
		$rs[] = $dao->checkCMember($conn,$param); //아이디 insert or update
		$rs[] = $dao->loginUpdate($conn,$param); //로그인 날짜 설정
		$result = $dao->selectSeq($conn, $param);//cmember_seq 값 가져오기
		$conn->CompleteTrans();

		include_once($_SERVER["DOCUMENT_ROOT"] . "/common/get_mileage.php");

		if(in_array(false, $rs)){
			errPrint();
		}else{
			$fb->addSession('MYSEC_ID',$fb->fb['MYSEC_ID']);
			$fb->addSession('USER_NM',$fb->fb['USER_NM']);
			$fb->addSession('PH_NO',$fb->fb['PH_NO']);
			$fb->addSession('TEL_NO',$fb->fb['TEL_NO']);
			$fb->addSession('ZIPCODE',$fb->fb['ZIPCODE']);
			$fb->addSession('ADDR1',$fb->fb['ADDR1']);
			$fb->addSession('ADDR2',$fb->fb['ADDR2']);

			/*3.0 호환 세션 셋팅 */
			$fb->addSession('cmember_seq',$result->fields['cmember_seq']);
			$fb->addSession("id",$fb->fb["MYSEC_ID"]); //3.0에서 사용하는 아이디
			$fb->addSession("member_name",$fb->fb["USER_NM"]);    //3.0에서 사용하는 이름
			$fb->addSession("biz_tel",$fb->fb["PH_NO"]);     //3.0에서 사용하는
		}

	}else{
		//errPrint();
	}
	//session_destroy();
?>