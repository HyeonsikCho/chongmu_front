<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderAllDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/util/pageLib.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/OrderAllHTML.php');
$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

//����Ʈ �����ִ� ���� ����

$list_num = 10;


//���� ������
$page = $fb->fb["page"];

// �������� ������ 1 ������
if (!$fb->fb["page"]) {
    $page = 1; 
}

//��� ����
$scrnum = 5; 
$s_num = $list_num * ($page-1);

$dao = new OrderAllDAO();
$param['user_id'] = $fb->ss['MYSEC_ID'];
$param["from"] = $fb->fb["from"];
$param["to"] = $fb->fb["to"];
$param['status'] = $fb->fb['status'];
$param['title'] = $fb->fb['title'];
$param["dvs"] = "";
$param["s_num"] = $s_num;
$param["list_num"] = $list_num;


$design_dir = "/design_template";

$rs =  $dao->getOrderList($conn,$param);

$list =  orderListHtml($conn,$rs,$design_dir);
$param["dvs"] = "COUNT";

$count_rs = $dao->getOrderList($conn, $param);
$rsCount = $count_rs->fields["cnt"];

$paging = mkDotAjaxPage($rsCount, $page, $list_num, "orderSearch",'',true);
if($rsCount){
	$retval['result'] = 'true';
	$retval['list'] = $list;
	$retval['listcnt'] = $rsCount;
	$retval['paging'] = $paging;
}else{
	$retval['result'] = 'false';
	$retval['listcnt'] = 0;
}
$retval = json_encode($retval);
echo $retval;
$conn->close();
?>
