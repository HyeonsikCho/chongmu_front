<?php
/**
 * Created by PhpStorm.
 * User: edohyune
 * Date: 2016-03-28
 * Time: 오전 2:22
 */
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/estimate_list/EstiListDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$ss = $fb->getSession();
echo  $ss["MYSEC_ID"];
$myid = $ss["MYSEC_ID"];
$dao = new EstiListDAO();
$b_result = $dao->selectCmemberSeqnoByMysecid($conn,$myid);
echo  $b_result;
?>