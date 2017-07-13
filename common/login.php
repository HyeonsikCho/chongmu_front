<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/member_grade.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/common_info.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/CommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/doc/common/common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/PasswordEncrypt.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_lib/CommonUtil.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$frontUtil = new FrontCommonUtil();
$commonUtil = new CommonUtil();
$dao = new CommonDAO();

$seqno = $fb->form("seqno");
$flag = $fb->form("flag");
if ($seqno) {
    $rs = $dao->selectMember($conn, array("seqno" => $seqno));

    if (password_verify(ADMIN_FLAG[0], $flag) === false) {
        echo "false";
        exit;
    }

    $id = $rs->fields["member_id"];

} else {

    $id = $fb->form("id");
    $pw = $fb->form("pw");
    $id_save = $fb->form("id_save");

    $rs = $dao->selectMember($conn, array("id" => $id));

    $pw_hash = $rs->fields["passwd"];

    if (password_verify($pw, $pw_hash) === false) {
        echo "false";
        exit;
    }
}

if ($id_save === "Y") {
    //expore 차후 조정
    setcookie("id", $id, time()+864000, "/");
} else {
    setcookie("id","",0, "/");
}

// 로그인 한 사람에 대한 정보
$member_name       = $rs->fields["member_name"];
$member_photo_path = $rs->fields["member_photo_path"];
$group_name        = $rs->fields["group_name"];

// 그룹 아이디 있는지 체크함
$group_id          = $rs->fields["group_id"];
$org_member_seqno  = $rs->fields["member_seqno"];
if (empty($group_id) === false) {
    $rs = $dao->selectMember($conn, array("seqno" => $group_id));
}

// 기본정보 변수에 저장(그룹일련번호 존재할 경우 해당 그룹의 정보임)
$org_member_seqno  = $org_member_seqno;
$member_seqno      = $rs->fields["member_seqno"];
$grade             = $rs->fields["grade"];
$grade_name_ko     = MemberGrade::GRADE_KO[$grade];
$grade_name_en     = MemberGrade::GRADE_KO[$grade];
$grade_image       = MemberGrade::GRADE_IMAGE[$grade];
$bank_name         = $rs->fields["bank_name"];
$ba_num       = $rs->fields["ba_num"];
//$member_dvs      = $rs->fields["member_dvs"];
//$member_typ      = $rs->fields["member_typ"];
$own_point         = $rs->fields["own_point"];
$prepay_price      = $rs->fields["prepay_price"];
$order_lack_price  = $rs->fields["order_lack_price"];
$cumul_sales_price = $rs->fields["cumul_sales_price"];

if (is_file($member_photo_path) === false) {
    $member_photo_path = "/design_template/images/no_image.jpg";
}

// 쿠폰 매수 검색
$cp_count = $dao->selectMemberCpCount($conn, $member_seqno);

// 담당 전화번호 검색
$param = array();

$biz_resp     = $rs->fields["biz_resp"];
$release_resp = $rs->fields["release_resp"];
$dlvr_resp    = $rs->fields["dlvr_resp"];

$param["biz_resp"]     = $biz_resp;
$param["release_resp"] = $release_resp;
$param["dlvr_resp"]    = $dlvr_resp;

$rs = $dao->selectRespTellNum($conn, $param);

$biz_tel     = $rs->fields["biz_tel"];
$release_tel = $rs->fields["release_tel"];
$dlvr_tel    = $rs->fields["dlvr_tel"];
$member_seqno    = $rs->fields["cmember_seqno"];

// 세션에 값 저장
$fb->addSession("id"                , $id);
$fb->addSession("org_member_seqno"  , $org_member_seqno);
$fb->addSession("member_seqno"      , $member_seqno);
$fb->addSession("member_name"       , $member_name);
$fb->addSession("group_id"          , $group_id);
$fb->addSession("group_name"        , $group_name);
$fb->addSession("member_photo_path" , $member_photo_path);
$fb->addSession("grade"             , $grade);
$fb->addSession("grade_name_ko"     , $grade_name_ko);
$fb->addSession("grade_name_en"     , $grade_name_en);
$fb->addSession("grade_image"       , $grade_image);
$fb->addSession("bank_name"         , $bank_name);
$fb->addSession("ba_num"            , $ba_num);
$fb->addSession("own_point"         , $own_point);
$fb->addSession("prepay_price"      , $prepay_price);
$fb->addSession("order_lack_price"  , $order_lack_price);
$fb->addSession("biz_resp"          , $biz_resp);
$fb->addSession("release_resp"      , $release_resp);
$fb->addSession("dlvr_resp"         , $dlvr_resp);
$fb->addSession("biz_tel"           , $biz_tel);
$fb->addSession("release_tel"       , $release_tel);
$fb->addSession("dlvr_tel"          , $dlvr_tel);
$fb->addSession("cp_count"          , $cp_count);
$fb->addSession("cumul_sales_price" ,$cumul_sales_price );

$session = $fb->getSession();

if ($seqno) {
    header("Location: /index.html");
    exit;
}

// 헤더부분 처리
$header_html = $commonUtil->convJsonStr(getLoginHtml($session));

// 사이드 메뉴 처리
unset($param);
$date = date("Y-m-d");
$start_date = date("Y-m-d", strtotime($date . "-7day")) . " 00:00:00";
$end_date   = $date . " 23:59:59";

$param["seqno"] = $member_seqno;
$param["start_date"] = $start_date;
$param["end_date"]   = $end_date;

$summary = $dao->selectOrderSummary($conn, $param);
$summary = $frontUtil->makeOrderSummaryArr($summary);
$aside_html = $commonUtil->convJsonStr(getAsideHtml($session, $summary));

$ret  = '{';
$ret .= " \"header\" : \"%s\",";
$ret .= " \"aside\"  : \"%s\"";
$ret .= '}';

echo sprintf($ret, $header_html, $aside_html);
?>
