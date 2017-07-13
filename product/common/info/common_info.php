<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/message.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/product_info_class.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/html/product/QuickEstimate.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");

$frontUtil = new FrontCommonUtil();

// 가격 검색용 정보배열
$price_info_arr = array();

// 쿼리 검색조건용 배열
$param = array();

$sell_site = $fb->session("sell_site");
$member_seqno = $fb->session("seqno");
$member_grade = $fb->session("grade");
$member_point = $fb->session("point");

$cate_sortcode = $fb->form("cs");

$sortcode_arr = $frontUtil->getTMBCateSortcode($cate_sortcode);

$sortcode_t = $sortcode_arr["sortcode_t"];
$sortcode_m = $sortcode_arr["sortcode_m"];
$sortcode_b = $sortcode_arr["sortcode_b"];

$price_info_arr["cate_sortcode"] = $sortcode_b;

$template->reg("cs", $sortcode_b); 

// 상품 사진 정보 생성
$picture_arr = array();
$param["cate_sortcode"] = $sortcode_b;
$picture_rs = $dao->selectCatePhoto($conn, $param);

if ($picture_rs->EOF) {
    $picture_arr[0]["org"]   = NO_IMAGE;
    $picture_arr[0]["thumb"] = NO_IMAGE_THUMB;
    $picture_arr[1]["org"]   = NO_IMAGE;
    $picture_arr[1]["thumb"] = NO_IMAGE_THUMB;
    $picture_arr[2]["org"]   = NO_IMAGE;
    $picture_arr[2]["thumb"] = NO_IMAGE_THUMB;
    $picture_arr[3]["org"]   = NO_IMAGE;
    $picture_arr[3]["thumb"] = NO_IMAGE_THUMB;
}

$root = $_SERVER["DOCUMENT_ROOT"];
$i = 0;
while ($picture_rs && !$picture_rs->EOF) {
    $file_path = $picture_rs->fields["file_path"];
    $file_name = $picture_rs->fields["save_file_name"];

    $full_path = $file_path . $file_name;

    $chk_path = $root . $full_path;

    if (is_file($chk_path) === false) {
        $full_path = NO_IMAGE;
    }

    $temp = explode('.', $full_path);
    $ext = strtolower($temp[1]);

    $thumb_full_path = $temp[0] . "_75_75." . $ext;

    $picture_arr[$i]["org"]     = $full_path;
    $picture_arr[$i++]["thumb"] = $thumb_full_path;

    $picture_rs->MoveNext();
}

unset($picture_rs);

$template->reg("pic_org_1"  , $picture_arr[0]["org"]); 
$template->reg("pic_thumb_1", $picture_arr[0]["thumb"]); 
$template->reg("pic_org_2"  , $picture_arr[1]["org"]); 
$template->reg("pic_thumb_2", $picture_arr[1]["thumb"]); 
$template->reg("pic_org_3"  , $picture_arr[2]["org"]); 
$template->reg("pic_thumb_3", $picture_arr[2]["thumb"]); 
$template->reg("pic_org_4"  , $picture_arr[3]["org"]); 
$template->reg("pic_thumb_4", $picture_arr[3]["thumb"]); 

unset($param);

// 상품 배너 정보 생성
$banner_rs = $dao->selectCateBanner($conn, $sortcode_b);

$url_addr  = $banner_rs->fields["url_addr"];
$target_yn = $banner_rs->fields["target_yn"];

$file_path = NIMDA_PATH . $banner_rs->fields["file_path"];
$file_name = $banner_rs->fields["save_file_name"];
$full_path = $file_path . $file_name;

unset($banner_rs);

if (is_file($full_path) === false) {
    $template->reg("banner_attr"  , "style=\"display:none;\""); 
    $template->reg("banner_url"   , "#none"); 
    $template->reg("banner_target", "_self"); 
    $template->reg("banner_src"   , '#none'); 
} else {
    $target_yn = ($target_yn === "Y") ? "_self" : "_blank";
    $banner_src = $full_path;

    $template->reg("banner_attr"  , ''); 
    $template->reg("banner_url"   , $url_addr); 
    $template->reg("banner_target", $target_yn); 
    $template->reg("banner_src"   , $banner_src);
}

// 카테고리 셀렉트박스 생성
$cate_top = $dao->selectCateHtml($conn, $sortcode_t);
$cate_mid = $dao->selectCateHtml($conn, $sortcode_m, $sortcode_t);
$cate_bot = $dao->selectCateHtml($conn, $sortcode_b, $sortcode_m);

$template->reg("cate_top", $cate_top); 
$template->reg("cate_mid", $cate_mid); 
$template->reg("cate_bot", $cate_bot); 
?>
