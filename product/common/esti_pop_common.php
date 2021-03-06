<?
$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new ProductCommonDAO();

$session = $fb->getSession();
$fb = $fb->getForm();

$cpn_rs = $dao->selectCpnInfo($conn, $session["sell_site"]);

$param = array();
$param["year"]  = date('Y');
$param["month"] = date('m');
$param["day"]   = date('d');

$param["sell_site"]   = $cpn_rs["sell_site"];
$param["repre_name"]  = $cpn_rs["repre_name"];
$param["repre_num"]   = $cpn_rs["repre_num"];
$param["addr"]        = $cpn_rs["addr"];
$param["addr_detail"] = $cpn_rs["addr_detail"];

$param["common_cate_name"] = $fb["common_cate_name"];
$param["cate_name_arr"] = $fb["cate_name"];
$param["paper_arr"]     = $fb["paper"];
$param["size_arr"]      = $fb["size"];
$param["tmpt_arr"]      = $fb["tmpt"];
$param["amt_arr"]       = $fb["amt"];
$param["amt_unit_arr"]  = $fb["amt_unit"];
$param["count_arr"]     = $fb["count"];
$param["after_arr"]     = $fb["after"];

$param["paper_price"]  = $fb["paper_price"];
$param["print_price"]  = $fb["print_price"];
$param["output_price"] = $fb["output_price"];
$param["after_price"]  = $fb["after_price"];
$param["sum_price"]    = $fb["sum_price"];

if ($is_login === false) {
    $param["member_name"]    = "손님";
    $param["member_tel"]     = '-';
    $param["member_mng"]     = '-';
    $param["member_mng_tel"] = '-';
} else {
    $member_seqno = $session["member_seqno"];
    $member_rs = $dao->selectMemberInfo($conn, $member_seqno);

    $param["member_name"]    = $session["member_name"];
    $param["member_tel"]     = $member_rs["tel_num"];
    $param["member_mng"]     = $session["member_mng_name"];
    $param["member_mng_tel"] = $session["member_mng_tel"];
}

unset($fb);
unset($member_rs);
unset($cpn_rs);
?>
