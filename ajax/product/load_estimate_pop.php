<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 2016-08-24
 * Time: 오후 6:50
 */

include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/html/product/EstimatePop.php");

// 실제 html 생성부분
include_once($_SERVER["DOCUMENT_ROOT"] . "/product/common/esti_pop_common.php");

$html_top = getHtmlTop($param);
$html_mid = getHtmlMid($param);
$html_bot = getHtmlBot($param);

echo $html_top . $html_mid . $html_bot;

$conn->Close();
exit;
?>
