<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/CommonDAO.php');

class FileDAO extends CommonDAO {
	function __construct() {
	}

	function setOrderImg($conn,$param){

		$param = $this->parameterArrayEscape($conn, $param);
		$sql = "insert into order_img (order_no,prd_detail_no,dvs,type,file_path,save_file_name,origin_file_name,regdate)
									values (".$param['order_no'].",
											".$param['prd_detail_no'].",
											".$param['dvs'].",
											".$param['type'].",
											".$param['file_path'].",
											".$param['save_file_name'].",
											".$param['origin_file_name'].",
											now()
											) 
									ON DUPLICATE KEY UPDATE file_path=".$param['file_path'].",
															save_file_name=".$param['save_file_name'].",
															origin_file_name=".$param['origin_file_name'].",
															regdate=now()";

		return $conn->Execute($sql);
	}

}
?>
