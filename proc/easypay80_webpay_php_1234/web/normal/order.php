<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>KICC EASYPAY 8.0 SAMPLE</title>
<meta name="robots" content="noindex, nofollow"> 
<meta http-equiv="content-type" content="text/html; charset=euc-kr">
<meta http-equiv="X-UA-Compatible" content="requiresActiveX=true">
<meta http-equiv="cache-control" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<meta http-equiv="Pragma" content="no-cache"/>
<link href="../css/style.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/default.js" type="text/javascript"></script>
<!-- Test -->
<!--script type="text/javascript" src="http://testpg.easypay.co.kr/webpay/EasypayCard_Web.js"></script>
<!-- Real -->
< script type="text/javascript" src="https://pg.easypay.co.kr/webpay/EasypayCard_Web.js"></script>

<script type="text/javascript">
    
    /* �Է� �ڵ� Setting */
    function f_init()
    {
        var frm_pay = document.frm_pay;
        
        var today = new Date();
        var year  = today.getFullYear();
        var month = today.getMonth() + 1;
        var date  = today.getDate();
        var time  = today.getTime();
        
        if(parseInt(month) < 10) 
        {
            month = "0" + month;
        }

        if(parseInt(date) < 10) 
        {
            date = "0" + date;
        }
        
        
        frm_pay.EP_mall_id.value = "T5102001";
        frm_pay.EP_mall_nm.value = "�ѱ��������(��) �׽�Ʈ";
        frm_pay.EP_vacct_end_date.value = "" + year + month + date;
        frm_pay.EP_vacct_end_time.value = "235959";
        frm_pay.EP_order_no.value = "ORDER_" + year + month + date + time;   //�������ֹ���ȣ
        frm_pay.EP_user_id.value = "USER_" + time;                           //��ID
        frm_pay.EP_user_nm.value = "õ����";
        frm_pay.EP_user_mail.value = "test@kicc.co.kr";
        frm_pay.EP_user_phone1.value = "0212344567";
        frm_pay.EP_user_phone2.value = "01012344567";
        frm_pay.EP_user_addr.value = "���� ��õ�� ���굿 459-9 ";
        frm_pay.EP_product_nm.value = "�׽�Ʈ ��ǰ";
        frm_pay.EP_product_amt.value = "50000";
        frm_pay.EP_return_url.value = "http://hiprint.biz/proc/easypay80_webpay_php/web/normal/order_res.php";
    }
    
    function f_start_pay() 
    {
        var frm_pay = document.frm_pay;
        
        /* UTF-8 ��밡������ ��� EP_charset �� ���� �ʼ� */
        if( frm_pay.EP_charset.value == "UTF-8" ) 
        {
            // �ѱ��� ���� ���� ��� encoding �ʼ�.
            frm_pay.EP_mall_nm.value      = encodeURIComponent( frm_pay.EP_mall_nm.value );
            frm_pay.EP_product_nm.value   = encodeURIComponent( frm_pay.EP_product_nm.value );
        }
        
        
        /* ���������� ���ϴ� ����â ȣ�� ����� ���� */

        if( frm_pay.EP_window_type.value == "iframe" ) 
        {
            easypay_webpay(frm_pay,"./iframe_req.php","hiddenifr","0","0","iframe",30);
        }
        else if( frm_pay.EP_window_type.value == "popup" ) 
        {
            easypay_webpay(frm_pay,"./popup_req.php","hiddenifr","","","popup",30);
        }
    }
    
    function f_submit() 
    {
        var frm_pay = document.frm_pay;
        frm_pay.target = "_self";
        frm_pay.action = "../easypay_request.php";
        frm_pay.submit();
    }
    
</script>
</head>
<body onload="f_init();">
<form name="frm_pay" method="post" action="">

<!--------------------------->
<!-- ::: ���� ���� ��û �� -->
<!--------------------------->

<input type="hidden" id="EP_mall_nm"       name="EP_mall_nm"          value="">         <!-- ������ �̸� // -->
<input type="hidden" id="EP_order_no"       name="EP_order_no"          value="">         <!-- ������ �ֹ���ȣ // -->
<input type="hidden" id="EP_currency"       name="EP_currency"          value="00">       <!-- ��ȭ�ڵ� // 00 : ��ȭ-->
<input type="hidden" id="EP_return_url"     name="EP_return_url"        value="">         <!-- ������ CALLBACK URL // -->
<input type="hidden" id="EP_ci_url"         name="EP_ci_url"            value="">         <!-- CI LOGO URL // -->
<input type="hidden" id="EP_lang_flag"      name="EP_lang_flag"         value="">         <!-- ��� // -->
<input type="hidden" id="EP_charset"        name="EP_charset"           value="EUC-KR">   <!-- ������ CharSet // -->
<input type="hidden" id="EP_user_type"      name="EP_user_type"         value="">         <!-- ����ڱ��� // -->
<input type="hidden" id="EP_user_id"        name="EP_user_id"           value="">         <!-- ������ ��ID // -->
<input type="hidden" id="EP_memb_user_no"   name="EP_memb_user_no"      value="">         <!-- ������ ���Ϸù�ȣ // -->
<input type="hidden" id="EP_user_nm"        name="EP_user_nm"           value="">         <!-- ������ ���� // -->
<input type="hidden" id="EP_user_mail"      name="EP_user_mail"         value="">         <!-- ������ �� E-mail // -->
<input type="hidden" id="EP_user_phone1"    name="EP_user_phone1"       value="">         <!-- ������ �� ����ó1 // -->
<input type="hidden" id="EP_user_phone2"    name="EP_user_phone2"       value="">         <!-- ������ �� ����ó2 // -->
<input type="hidden" id="EP_user_addr"      name="EP_user_addr"         value="">         <!-- ������ �� �ּ� // -->
<input type="hidden" id="EP_user_define1"   name="EP_user_define1"      value="">         <!-- ������ �ʵ�1 // -->
<input type="hidden" id="EP_user_define2"   name="EP_user_define2"      value="">         <!-- ������ �ʵ�2 // -->
<input type="hidden" id="EP_user_define3"   name="EP_user_define3"      value="">         <!-- ������ �ʵ�3 // -->
<input type="hidden" id="EP_user_define4"   name="EP_user_define4"      value="">         <!-- ������ �ʵ�4 // -->
<input type="hidden" id="EP_user_define5"   name="EP_user_define5"      value="">         <!-- ������ �ʵ�5 // -->
<input type="hidden" id="EP_user_define6"   name="EP_user_define6"      value="">         <!-- ������ �ʵ�6 // -->
<input type="hidden" id="EP_product_type"   name="EP_product_type"      value="">         <!-- ��ǰ�������� // -->
<input type="hidden" id="EP_product_expr"   name="EP_product_expr"      value="">         <!-- ���� �Ⱓ // (YYYYMMDD) -->


<!--------------------------->
<!-- ::: ī�� ���� ��û �� -->
<!--------------------------->

<input type="hidden" id="EP_usedcard_code"      name="EP_usedcard_code"     value="">      <!-- ��밡���� ī�� LIST // FORMAT->ī���ڵ�:ī���ڵ�: ... :ī���ڵ� EXAMPLE->029:027:031 // �� : DB��ȸ-->
<input type="hidden" id="EP_quota"              name="EP_quota"             value="">      <!-- �Һΰ��� (ī���ڵ�-�Һΰ���) -->
<input type="hidden" id="EP_os_cert_flag"       name="EP_os_cert_flag"      value="2">     <!-- �ؿܾȽ�Ŭ�� ��뿩��(����Ұ�) // -->
<input type="hidden" id="EP_noinst_flag"        name="EP_noinst_flag"       value="">      <!-- ������ ���� (Y/N) // -->
<input type="hidden" id="EP_noinst_term"        name="EP_noinst_term"       value="">      <!-- ������ �Ⱓ(ī���ڵ�-�����Һΰ���) // -->
<input type="hidden" id="EP_set_point_card_yn"  name="EP_set_point_card_yn" value="">      <!-- ī�������Ʈ ��뿩�� (Y/N) // -->
<input type="hidden" id="EP_point_card"         name="EP_point_card"        value="">      <!-- ����Ʈī�� LIST  // -->
<input type="hidden" id="EP_join_cd"            name="EP_join_cd"           value="">      <!-- �����ڵ� // -->
<input type="hidden" id="EP_kmotion_useyn"      name="EP_kmotion_useyn"     value="">      <!-- ���ξ�ī�� ������� // -->

<!------------------------------->
<!-- ::: ������� ���� ��û �� -->
<!------------------------------->

<input type="hidden" id="EP_vacct_bank"      name="EP_vacct_bank"     value="">      <!-- ������� ��밡���� ���� LIST // -->
<input type="hidden" id="EP_vacct_end_date"  name="EP_vacct_end_date" value="">      <!-- �Ա� ���� ��¥ // -->
<input type="hidden" id="EP_vacct_end_time"  name="EP_vacct_end_time" value="">      <!-- �Ա� ���� �ð� // -->

<!------------------------------->
<!-- ::: ����ī�� ���� ��û �� -->
<!------------------------------->

<input type="hidden" id="EP_prepaid_cp"    name="EP_prepaid_cp"     value="">      <!-- ����ī�� CP // FORMAT->�ڵ�:�ڵ�: ... :�ڵ� EXAMPLE->CCB:ECB // �� : DB��ȸ-->

<!--------------------------------->
<!-- ::: ��������� ���� ��û �� -->
<!--------------------------------->

<input type="hidden" id="EP_res_cd"          name="EP_res_cd"         value="">      <!--  �����ڵ� // -->
<input type="hidden" id="EP_res_msg"         name="EP_res_msg"        value="">      <!--  ����޼��� // -->
<input type="hidden" id="EP_tr_cd"           name="EP_tr_cd"          value="">      <!--  ����â ��û���� // -->
<input type="hidden" id="EP_ret_pay_type"    name="EP_ret_pay_type"   value="">      <!--  �������� // -->
<input type="hidden" id="EP_ret_complex_yn"  name="EP_ret_complex_yn" value="">      <!--  ���հ��� ���� (Y/N) // -->
<input type="hidden" id="EP_card_code"       name="EP_card_code"      value="">      <!--  ī���ڵ� (ISP:KVPī���ڵ� MPI:ī���ڵ�) // -->
<input type="hidden" id="EP_eci_code"        name="EP_eci_code"       value="">      <!--  MPI�� ��� ECI�ڵ� // -->
<input type="hidden" id="EP_card_req_type"   name="EP_card_req_type"  value="">      <!--  �ŷ����� // -->
<input type="hidden" id="EP_save_useyn"      name="EP_save_useyn"     value="">      <!--  ī��� ���̺� ���� (Y/N) // -->
<input type="hidden" id="EP_trace_no"        name="EP_trace_no"       value="">      <!--  ������ȣ // -->
<input type="hidden" id="EP_sessionkey"      name="EP_sessionkey"     value="">      <!--  ����Ű // -->
<input type="hidden" id="EP_encrypt_data"    name="EP_encrypt_data"   value="">      <!--  ��ȣȭ���� // -->
<input type="hidden" id="EP_pnt_cp_cd"       name="EP_pnt_cp_cd"      value="">      <!--  ����Ʈ CP �ڵ� // -->
<input type="hidden" id="EP_spay_cp"         name="EP_spay_cp"        value="">      <!--  ������� CP �ڵ� // -->
<input type="hidden" id="EP_card_prefix"     name="EP_card_prefix"    value="">      <!--  �ſ�ī��prefix // -->
<input type="hidden" id="EP_card_no_7"       name="EP_card_no_7"      value="">      <!--  �ſ�ī���ȣ ��7�ڸ� // -->


<table border="0" width="910" cellpadding="10" cellspacing="0">
<tr>
    <td>
    <!-- title start -->
    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" bgcolor="#FFFFFF" align="left">&nbsp;<img src="../img/arow3.gif" border="0" align="absmiddle">&nbsp;�Ϲ� > <b>����</b></td>
    </tr>
    <tr>
        <td height="2" bgcolor="#2D4677"></td>
    </tr>
    </table>
    <!-- title end -->
       
    <!-- mallinfo start -->
    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" bgcolor="#FFFFFF">&nbsp;<img src="../img/arow2.gif" border="0" align="absmiddle">&nbsp;<b>����������</b>(*�ʼ�)</td>
    </tr>
    </table>
    
    <table border="0" width="900" cellpadding="0" cellspacing="1" bgcolor="#DCDCDC">
    <tr height="25">
        <td bgcolor="#EDEDED" width="150">&nbsp; *���������̵�</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" id="EP_mall_id" name="EP_mall_id" value="" size="50" maxlength="8" class="input_F"></td>
        <td bgcolor="#EDEDED" width="150">&nbsp; ������ Ÿ��</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;
            <select id="EP_window_type" name="EP_window_type" class="input_F">
                <option value="iframe" selected>iframe</option>
                <option value="popup" >popup</option>
            </select>
       </td>
    </tr>
    </table>
    <!-- mallinfo end -->
    
    <!-- webpay start -->
    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" bgcolor="#FFFFFF">&nbsp;<img src="../img/arow2.gif" border="0" align="absmiddle">&nbsp;<b>����â ����</b>(*�ʼ�)</td>
    </tr>
    </table>

    <table border="0" width="900" cellpadding="0" cellspacing="1" bgcolor="#DCDCDC">
    <tr height="25">
        <td bgcolor="#EDEDED" width="150">&nbsp; *��������</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;
            <select id="EP_pay_type" name="EP_pay_type" class="input_F">
                <option value="11" selected>�ſ�ī��</option>            
                <option value="21">������ü</option>
                <option value="22">�������Ա�</option>
                <option value="31">�޴���</option>
                <option value="50">���Ұ���</option>
                <option value="60">�������</option>
            </select>
        </td>
        <td bgcolor="#EDEDED" width="150">&nbsp;</td>        
        <td bgcolor="#FFFFFF" width="300">&nbsp;</td>      
    </tr>
    <tr height="25">
        <td bgcolor="#EDEDED" width="150">&nbsp; *��ǰ��</td>        
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" id="EP_product_nm" name="EP_product_nm" value="�׽�Ʈ��ǰ" size="50" class="input_F"></td>      
        <td bgcolor="#EDEDED" width="150">&nbsp; *��ǰ�ݾ�</td>        
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" id="EP_product_amt" name="EP_product_amt" value="50000" size="50" class="input_F"></td>      
    </tr>
    </table>
    <!-- webpay end -->

    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" align="center" bgcolor="#FFFFFF"><input type="button" value="�� ��" class="input_D" style="cursor:hand;" onclick="javascript:f_start_pay();"></td>
    </tr>
    </table>
    </td>
</tr>
</table>
</form>
</body>
</html>