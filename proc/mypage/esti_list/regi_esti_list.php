<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/estimate_list/EstiListDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/file/FileAttachDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$conn->StartTrans();

$fb = new FormBean();
$dao = new EstiListDAO();
$fileDAO = new FileAttachDAO();

$ss = $fb->getSession();
$myid = $ss["MYSEC_ID"];

$b_result = $dao->selectCmemberSeqnoByMysecid($conn,$myid);

//견적 등록
$param = array();
$param["title"] = $fb->form("title");
$param["inq_cont"] = $fb->form("inq_cont");
$param["member_seqno"] = $b_result->fields["cmember_seq"];
$param["state"] = "견적대기";

$insID = $dao->insertEsti($conn, $param);

if (!$insID) {
	$return = "{\"result\" : \"false\",
			\"result_text\" : \"견적등록이 실패하였습니다.\"}";
    $conn->CompleteTrans();
    $conn->Close();
    echo $return;
    exit;
}
 
if ($fb->form("upload_yn") == "Y") {

    //파일 업로드 경로
    $param = array();
    $param["file_path"] = SITE_DEFAULT_ESTI_FILE;
    $param["tmp_name"] = $_FILES["esti_file"]["tmp_name"];
    $param["origin_file_name"] = $_FILES["esti_file"]["name"];
    $param["size"] = $_FILES["esti_file"]["size"];
	
    //파일을 업로드 한 후 저장된 경로를 리턴한다.
    $f_rs = $fileDAO->upLoadFile($param);


    $param = array();
    $param["table"] = "esti_file";
    $param["col"]["origin_file_name"] = $_FILES["esti_file"]["name"];
    $param["col"]["save_file_name"] = $f_rs["save_file_name"];
    $param["col"]["file_path"] = $f_rs["file_path"];
    $param["col"]["size"] = $_FILES["esti_file"]["size"];
    $param["col"]["esti_seqno"] = $insID;
	
    $rs = $dao->insertData($conn,$param);

    if (!$rs || !$f_rs) {
        $return = "{\"result\" : \"false\",
					\"result_text\" : \"지정된 형식의 파일만 업로드 가능합니다.\"}";
        $conn->CompleteTrans();
        $conn->Close();
        echo $check;
        exit;
    }
}
$return = "{\"result\" : \"true\",
			\"result_text\" : \"견적이 등록되었습니다.\"}";

$conn->CompleteTrans();
$conn->Close();
echo $return;
?>
