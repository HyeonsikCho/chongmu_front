<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderAllDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/util/pageLib.php');

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();
$fb = new FormBean();
$orderDAO = new OrderAllDAO();

//한페이지에 출력할 게시물 갯수
$list_num = $fb->form("list_num"); 

//현재 페이지
$page = $fb->form("page");

//리스트 보여주는 갯수 설정
if (!$fb->form("list_num")) $list_num = 10;
// 페이지가 없으면 1 페이지
if (!$page) $page = 1; 

$type = $fb->form("type");
$s_num = $list_num * ($page-1);
$session = $fb->getSession();
$seqno = $session["org_member_seqno"];

$param = array();
$param["s_num"] = $s_num;
$param["list_num"] = $list_num;
$param["from"] = $fb->form("from");
$param["to"] = $fb->form("to");
$param["title"] = $fb->form("title");
$param["dlvr_way"] = $fb->form("dlvr_way");
$tmp = explode("@", $fb->form("state"));
$param["state_dvs"] = $tmp[1];
$param["state"] = $tmp[0];
$param["seqno"] = $session["org_member_seqno"];
$param["type"] = $fb->form("type");
$param["dvs"] = "SEQ";

$rs = $orderDAO->selectOrderList($conn, $param);

$param["dvs"] = "COUNT";
$count_rs = $orderDAO->selectOrderList($conn, $param);
$rsCount = $count_rs->fields["cnt"];

$param["count"] = $rsCount;
$list = makeOrderListHtml($conn, $rs, $param, $type);+
$paging = mkDotAjaxPage($rsCount, $page, $list_num, "moveOrderPage");

$html = "";
if ($fb->form("from") && $fb->form("to")) {
    $html .= "<strong>" . $param["from"] . "</strong>부터 <strong>";
    $html .= $param["to"] . "</strong>까지 ";
}
$html .= "<em>" . $rsCount . "</em>건의 검색결과가 있습니다.";

echo $list . "♪" . $paging . "♪" . $html;
$conn->close();
?>
