<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/EstiInfoDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/util/pageLib.php');

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new EstiInfoDAO();
$commonDAO = $dao;

//한페이지에 출력할 게시물 갯수
$list_num = $fb->form("listSize");

//리스트 보여주는 갯수 설정
if (!$fb->form("listSize")) {
    $list_num = 30;
}

//현재 페이지
$page = $fb->form("page");

// 페이지가 없으면 1 페이지
if (!$fb->form("page")) {
    $page = 1;
}

//블록 갯수
$scrnum = 5;
$s_num = $list_num * ($page-1);

$from = $fb->form("from");
$to = $fb->form("to");

$state = "";

if ($fb->form("state")) {
    $state = "견적" . $fb->form("state");
}

$param = array();
$param["s_num"] = $s_num;
$param["list_num"] = $list_num;
$param["member_seqno"] = $fb->session("cmember_seq");
$param["dvs"] = "";
$param["state"] = $state;
$param["search_cnd"] = "req_date";
if ($from) {
    $param["from"] = $from." 00:00;00";
}
if ($to) {
    $param["to"] = $to." 23:59;59";
}
$param["title"] = $fb->form("title");

$rs = $dao->selectEstiListCond($conn, $param);

$param["dvs"] = "COUNT";
$count_rs = $dao->selectEstiListCond($conn, $param);
$rsCount = $count_rs->fields["cnt"];
$param["count"] = $rsCount;

$list = makeEstiListHtml($rs, $param);
$paging = mkDotAjaxPage($rsCount, $page, $list_num, "movePage");

echo $list . "♪" . $paging . "♪" . $rsCount;
$conn->Close();
?>
