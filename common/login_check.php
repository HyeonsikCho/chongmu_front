<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/doc/common/common.php");


if ($is_login === false) {
    $template->reg("header_login", getLogOutHtml($session));
} else {
    /*
     * 로그인을 처리한다.
     * */
    $session = $fb->getSession();

    $date = date("Y-m-d");
    $start_date = date("Y-m-d", strtotime($date . "-7day")) . " 00:00:00";
    $end_date   = $date . " 23:59:59";

    $param = array();
    $param["member_seqno"] = $session["member_seqno"];
    $param["seqno"] = $session["MYSEC_ID"];
    $param["start_date"] = $start_date;
    $param["end_date"]   = $end_date;

    $template->reg("header_login_class", "memberInfo");
    $template->reg("header_login", getLoginHtml($session, $info));

    if(!$fb->session("plzp_id")) {
        include_once($_SERVER["DOCUMENT_ROOT"] . "/common/get_mileage.php");
    }

    /********************************************************************
     ***** 제휴사에 데이터 전송
     ********************************************************************/

    /*
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://adm.plzm.info/NsmdG/PI/WPI100_S100/main/');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $rs = curl_exec($ch);
    curl_close($ch);
*/

    /********************************************************************
     ***** 리턴값
     ********************************************************************/
    /*
    $obj = simplexml_load_string($rs);
//print_r((string)$obj); exit;
//$obj = json_encode($obj);
    $info = array();
    $info['RES_CD'] =  $obj->RES_CD;
    $info['cpoint'] =  $obj->PLZP_POINT;
    $info['plzp_id'] =  $obj->PLZP_ID;

    $template->reg("header_login_class", "memberInfo");
    $template->reg("header_login", getLoginHtml($session, $info));

    $template->reg("side_menu", getAsideHtml($session, $summary));

    $template->reg("member_page", "");
    */
}
?>
