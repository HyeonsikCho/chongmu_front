<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/ClaimWriteDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/file/FileAttachDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();
$check = 1;

$fb = new FormBean();

$claimDAO = new ClaimWriteDAO(); 
$fileDAO = new FileAttachDAO();
$conn->StartTrans();

//클레임 추가
$param = array();
$param["table"] = "order_claim";
$param["col"]["order_common_seqno"] = $fb->form("order_seqno");
$param["col"]["title"] = $fb->form("claim_title");
$param["col"]["dvs"] = $fb->form("dvs");
$param["col"]["cust_cont"] = $fb->form("claim_cont");
$param["col"]["regi_date"] = date("Y-m-d H:i:s", time());
$param["col"]["order_yn"] = "N";
$param["col"]["agree_yn"] = "N";
$param["col"]["state"] = "요청";

if ($_FILES["sample_file"]) {

    //클레임 견본파일
    $f_param = array();
    $f_param["file_path"] = CLAIM_SAMPLE_FILE;
    $f_param["tmp_name"] = $_FILES["sample_file"]["tmp_name"];
    $f_param["origin_file_name"] = $_FILES["sample_file"]["name"];

    //파일을 업로드 한 후 저장된 경로를 리턴한다.
    $result= $fileDAO->upLoadFile($f_param);
    if (!$result) $check = 0;

    //클레임 견본파일 추가
    $param["col"]["sample_origin_file_name"] = $_FILES["sample_file"]["name"];
    $param["col"]["sample_save_file_name"] = $result["save_file_name"];
    $param["col"]["sample_file_path"] = $result["file_path"];

}

$result = $claimDAO->insertData($conn, $param);
if (!$result) $check = 0;

echo $check;
$conn->CompleteTrans();
$conn->close();
?>
