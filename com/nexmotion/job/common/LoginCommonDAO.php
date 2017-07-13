<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/CommonDAO.php');

class LoginCommonDAO extends CommonDAO {
	function __construct() {
	}

	/**
	 * @brief 총무팀 아이디 인서트 및 업데이트
	 *
	 * @param $conn  = connection identifier
	 * $param['MYSEC_ID'] = 총무팀 유저아이디
	 * $param['USER_NM'] = 총무팀 유저이름
	 * $param['PH_NO'] = 총무팀 유저폰번호
	 * $param['TEL_NO'] = 총무팀 유저핸드폰번호
	 * $param['ZIPCODE'] = 총무팀 유저우편번호
	 * $param['ADDR1'] = 총무팀 유저주소1
	 * $param['ADDR2'] = 총무팀 유저주소2
	 * $param['SITE_CD'] = $fb->fb['MYSEC_ID'];
	 * $param['AUTH_CD'] = $fb->fb['MYSEC_ID'];
	 * $param['SVC_NO'] = $fb->fb['MYSEC_ID'];
	 * @param $param["user_id"] = 유저아이디
	 * @return true / false
	 */

	function checkCMember($conn,$param){
		if ($this->connectionCheck($conn) === false) {
			return false;
		}
		$param = $this->parameterArrayEscape($conn, $param);
		$sql = "insert into cmember (site_cd,auth_cd,svc_no,mysec_id,user_nm,ph_no,tel_no,zipcode,addr1,addr2,regdate)
									values (".$param['SITE_CD'].",
											".$param['AUTH_CD'].",
											".$param['SVC_NO'].",
											".$param['MYSEC_ID'].",
											".$param['USER_NM'].",
											".$param['PH_NO'].",
											".$param['TEL_NO'].",
											".$param['ZIPCODE'].",
											".$param['ADDR1'].",
											".$param['ADDR2'].",
											now())
									ON DUPLICATE KEY UPDATE user_nm=".$param['USER_NM'].",
																	ph_no=".$param['PH_NO'].",
																	tel_no=".$param['TEL_NO'].",
																	zipcode=".$param['ZIPCODE'].",
																	addr1=".$param['ADDR1'].",
																	addr2=".$param['ADDR2']."";
		return$conn->Execute($sql);
	}
	/**
	 * @brief 총무팀 최근로그인 기록 인서트 및 업데이트
	 *
	 * @param $conn  = connection identifier
	 * $param['MYSEC_ID'] = 총무팀 유저아이디
	 * $param['USER_NM'] = 총무팀 유저이름
	 * $param['PH_NO'] = 총무팀 유저폰번호
	 * $param['TEL_NO'] = 총무팀 유저핸드폰번호
	 * $param['ZIPCODE'] = 총무팀 유저우편번호
	 * $param['ADDR1'] = 총무팀 유저주소1
	 * $param['ADDR2'] = 총무팀 유저주소2
	 * $param['SITE_CD'] = $fb->fb['MYSEC_ID'];
	 * $param['AUTH_CD'] = $fb->fb['MYSEC_ID'];
	 * $param['SVC_NO'] = $fb->fb['MYSEC_ID'];
	 * @param $param["user_id"] = 유저아이디
	 * @return true / false
	 */
	function loginUpdate($conn,$param){
		$param = $this->parameterArrayEscape($conn, $param);
		$sql = "insert into cmember_login_log (site_cd,auth_cd,svc_no,mysec_id,regdate)
									values (".$param['SITE_CD'].",
											".$param['AUTH_CD'].",
											".$param['SVC_NO'].",
											".$param['MYSEC_ID'].",now())
									ON DUPLICATE KEY UPDATE regdate = now()";
		return $conn->Execute($sql);
	}

	function selectSeq($conn, $param) {
		$param = $this->parameterArrayEscape($conn, $param);
		$sql = "select cmember_seq from cmember where mysec_id = " . $param['MYSEC_ID'];

		return $conn->Execute($sql);
	}
}
?>
