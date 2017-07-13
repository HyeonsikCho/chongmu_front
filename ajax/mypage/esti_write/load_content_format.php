<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/esti_default_format.php");

$fb = new FormBean();

$cate_sortcode = $fb->form("cate_sortcode");

echo EstiDefaultFormat::DEFAULT_FORMAT[$cate_sortcode]['format'];
?>