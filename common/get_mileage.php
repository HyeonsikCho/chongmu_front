<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/doc/common/common.php");

    /********************************************************************
     ***** 배열정의
     ********************************************************************/

    $post_data = array("SITE_CD" => SITE_CD,
        "AUTH_CD" => AUTH_CD,
        "SVC_NO" => SVC_NO,
        "MYSEC_ID" => $fb->ss['MYSEC_ID'],
        "PH_NO" => $fb->ss['PH_NO'],
        "USER_NM" => $fb->ss['USER_NM']
    );

    /********************************************************************
     ***** 제휴사에 데이터 전송
     ********************************************************************/

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://adm.plzm.info/NsmdG/PI/WPI100_S100/main/');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $rs = curl_exec($ch);
    curl_close($ch);

    /********************************************************************
     ***** 리턴값
     ********************************************************************/
    $obj = simplexml_load_string($rs);
    $res_cd = (string)$obj->RES_CD;
    $cpoint = (string)$obj->PLZP_POINT;
    $cid = (string)$obj->PLZP_ID;

    $fb->addSession('RES_CD', $res_cd);
    $fb->addSession('cpoint', $cpoint);
    $fb->addSession('plzp_id', $cid);
?>
