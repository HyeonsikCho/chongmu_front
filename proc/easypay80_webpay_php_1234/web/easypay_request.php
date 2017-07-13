<?php
    include("./easypay_client.php");
    
    /* -------------------------------------------------------------------------- */
    /* ::: ó������ ����                                                          */
    /* -------------------------------------------------------------------------- */
    $TRAN_CD_NOR_PAYMENT    = "00101000";   // ����(�Ϲ�, ����ũ��)
    $TRAN_CD_NOR_MGR        = "00201000";   // ����(�Ϲ�, ����ũ��)

    /* -------------------------------------------------------------------------- */
    /* ::: ���θ� ���� ���� ����                                                  */
    /* -------------------------------------------------------------------------- */
    $g_gw_url    = "testgw.easypay.co.kr";               // Gateway URL ( test )
    //$g_gw_url               = "gw.easypay.co.kr";      // Gateway URL ( real )    
    $g_gw_port   = "80";                                           // ��Ʈ��ȣ(����Ұ�) 

    /* -------------------------------------------------------------------------- */ 
    /* ::: ���� ������ �¾� (��ü�� �°� ����)                                    */ 
    /* -------------------------------------------------------------------------- */ 
    /* �� ���� ��                                                                 */ 
    /* cert_file ���� ����                                                        */
    /* - pg_cert.pem ������ �ִ� ���丮��  ���� ��� ����                       */ 
    /* log_dir ���� ����                                                          */
    /* - log ���丮 ����                                                        */
    /* log_level ���� ����                                                        */
    /* - log ���� ����                                                            */
    /* -------------------------------------------------------------------------- */
    
    $g_home_dir   = "/home/dprinting/front/proc/easypay80_webpay_php";
    $g_cert_file  = "/home/dprinting/front/proc/easypay80_webpay_php/cert/pg_cert.pem";
    $g_log_dir    = "/home/dprinting/front/proc/easypay80_webpay_php/log";
    $g_log_level  = "1"; 
    
    $g_mall_id   = $_POST["EP_mall_id"];              // [�ʼ�]�����̵�
    
    /* -------------------------------------------------------------------------- */
    /* ::: �÷����� �������� ����                                                 */
    /* -------------------------------------------------------------------------- */
    $tr_cd            = $_POST["EP_tr_cd"];           // [�ʼ�]��û����
    $trace_no         = $_POST["EP_trace_no"];        // [�ʼ�]����������ȣ
    $sessionkey       = $_POST["EP_sessionkey"];      // [�ʼ�]��ȣȭŰ
    $encrypt_data     = $_POST["EP_encrypt_data"];    // [�ʼ�]��ȣȭ ����Ÿ
    
    $pay_type         = $_POST["EP_ret_pay_type"];    // [����]��������
    $complex_yn       = $_POST["EP_ret_complex_yn"];  // [����]���հ�������    
    $card_code        = $_POST["EP_card_code"];       // [����]�ſ�ī�� ī���ڵ�
    
    
    /* -------------------------------------------------------------------------- */
    /* ::: ���� �ֹ� ���� ����                                                    */
    /* -------------------------------------------------------------------------- */    
    $user_type        = $_POST["EP_user_type"];       // [����]����ڱ��б���[1:�Ϲ�,2:ȸ��]
    $order_no         = $_POST["EP_order_no"];        // [�ʼ�]�ֹ���ȣ
    $memb_user_no     = $_POST["EP_memb_user_no"];    // [����]������ ���Ϸù�ȣ
    $user_id          = $_POST["EP_user_id"];         // [����]�� ID
    $user_nm          = $_POST["EP_user_name"];       // [�ʼ�]����
    $user_mail        = $_POST["EP_user_mail"];       // [�ʼ�]�� E-mail
    $user_phone1      = $_POST["EP_user_phone1"];     // [�ʼ�]������ �� ����ó1
    $user_phone2      = $_POST["EP_user_phone2"];     // [����]������ �� ����ó2
    $user_addr        = $_POST["EP_user_addr"];       // [����]������ �� �ּ�
    $product_type     = $_POST["EP_product_type"];    // [�ʼ�]��ǰ��������[0:�ǹ�,1:������]
    $product_nm       = $_POST["EP_product_nm"];      // [�ʼ�]��ǰ��
    $product_amt      = $_POST["EP_product_amt"];     // [�ʼ�]��ǰ�ݾ�
    
    /* -------------------------------------------------------------------------- */
    /* ::: ������� ���� ����                                                     */
    /* -------------------------------------------------------------------------- */
    $mgr_txtype       = $_POST["mgr_txtype"];         // [�ʼ�]�ŷ�����
    $mgr_subtype      = $_POST["mgr_subtype"];        // [����]���漼�α���
    $org_cno          = $_POST["org_cno"];            // [�ʼ�]���ŷ�������ȣ
    $mgr_amt          = $_POST["mgr_amt"];            // [����]�κ����/ȯ�ҿ�û �ݾ�
    $mgr_bank_cd      = $_POST["mgr_bank_cd"];        // [����]ȯ�Ұ��� �����ڵ�
    $mgr_account      = $_POST["mgr_account"];        // [����]ȯ�Ұ��� ��ȣ
    $mgr_depositor    = $_POST["mgr_depositor"];      // [����]ȯ�Ұ��� �����ָ�
    $mgr_socno        = $_POST["mgr_socno"];          // [����]ȯ�Ұ��� �ֹι�ȣ
    $mgr_telno        = $_POST["mgr_telno"];          // [����]ȯ�Ұ� ����ó
    $deli_cd          = $_POST["deli_cd"];            // [����]��۱���[�ڰ�:DE01,�ù�:DE02]
    $deli_corp_cd     = $_POST["deli_corp_cd"];       // [����]�ù���ڵ�
    $deli_invoice     = $_POST["deli_invoice"];       // [����]����� ��ȣ
    $deli_rcv_nm      = $_POST["deli_rcv_nm"];        // [����]������ �̸�
    $deli_rcv_tel     = $_POST["deli_rcv_tel"];       // [����]������ ����ó
    $req_ip           = $_POST["req_ip"];             // [�ʼ�]��û�� IP
    $req_id           = $_POST["req_id"];             // [����]��û�� ID
    $mgr_msg          = $_POST["mgr_msg"];            // [����]���� ����
    $mgr_paytype      = $_POST["mgr_paytype"];        // [����]��������
    
    /* -------------------------------------------------------------------------- */
    /* ::: ����                                                                   */
    /* -------------------------------------------------------------------------- */
    $mgr_data    = "";     // ��������
    $mall_data   = "";     // ��û����
    
    /* -------------------------------------------------------------------------- */
    /* ::: ���� ���                                                              */
    /* -------------------------------------------------------------------------- */
    $res_cd               = "";
    $res_msg              = "";
    
    
    /* -------------------------------------------------------------------------- */
    /* ::: EasyPayClient �ν��Ͻ� ���� [����Ұ� !!].                             */
    /* -------------------------------------------------------------------------- */
    $easyPay = new EasyPay_Client;         // ����ó���� Class (library���� ���ǵ�)
    $easyPay->clearup_msg();

    $easyPay->set_home_dir($g_home_dir);
    $easyPay->set_gw_url($g_gw_url);
    $easyPay->set_gw_port($g_gw_port);
    $easyPay->set_log_dir($g_log_dir);
    $easyPay->set_log_level($g_log_level);
    $easyPay->set_cert_file($g_cert_file);
    
    /* -------------------------------------------------------------------------- */
    /* ::: IP ���� ����                                                           */
    /* -------------------------------------------------------------------------- */
    $client_ip = $easyPay->get_remote_addr();    // [�ʼ�]������ IP
    
    /* -------------------------------------------------------------------------- */
    /* ::: ���ο�û(�÷����� ��ȣȭ ���� ����)                                    */
    /* -------------------------------------------------------------------------- */
    if( $TRAN_CD_NOR_PAYMENT == $tr_cd ) {
    
        //���ο�û ���� ����
        $easyPay->set_trace_no($trace_no);
        $easyPay->set_snd_key($sessionkey);
        $easyPay->set_enc_data($encrypt_data);  
        
    /* -------------------------------------------------------------------------- */
    /* ::: ������� ��û                                                          */
    /* -------------------------------------------------------------------------- */
    }else if( $TRAN_CD_NOR_MGR == $tr_cd ) {

    $mgr_data = $easyPay->set_easypay_item("mgr_data");    
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"      , $mgr_txtype       );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_subtype"     , $mgr_subtype      );
    $easyPay->set_easypay_deli_us( $mgr_data, "org_cno"         , $org_cno          );
    $easyPay->set_easypay_deli_us( $mgr_data, "order_no"        , $order_no         );
    $easyPay->set_easypay_deli_us( $mgr_data, "pay_type"        , $pay_type         );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_amt"         , $mgr_amt          );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_bank_cd"     , $mgr_bank_cd      );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_account"     , $mgr_account      );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_depositor"   , $mgr_depositor    );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_socno"       , $mgr_socno        );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_telno"       , $mgr_telno        );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_cd"         , $deli_cd          );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_corp_cd"    , $deli_corp_cd     );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_invoice"    , $deli_invoice     );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_rcv_nm"     , $deli_rcv_nm      );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_rcv_tel"    , $deli_rcv_tel     );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_ip"          , $client_ip        );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_id"          , $req_id           );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_msg"         , $mgr_msg          );
        
    }

    /* -------------------------------------------------------------------------- */
    /* ::: ����                                                                   */
    /* -------------------------------------------------------------------------- */         
    $opt = "option value";    
    $easyPay->easypay_exec($g_mall_id, $tr_cd, $order_no, $client_ip, $opt);
    $res_cd  = $easyPay->_easypay_resdata["res_cd"];    // �����ڵ�
    $res_msg = $easyPay->_easypay_resdata["res_msg"];   // ����޽���
    
    /* -------------------------------------------------------------------------- */
    /* ::: ��� ó��                                                              */
    /* -------------------------------------------------------------------------- */

    $r_cno             = $easyPay->_easypay_resdata[ "cno"             ];    // PG�ŷ���ȣ 
    $r_amount          = $easyPay->_easypay_resdata[ "amount"          ];    //�� �����ݾ�
    $r_order_no          = $easyPay->_easypay_resdata[ "order_no"          ];    //�ֹ���ȣ
    $r_auth_no         = $easyPay->_easypay_resdata[ "auth_no"         ];    //���ι�ȣ
    $r_tran_date       = $easyPay->_easypay_resdata[ "tran_date"       ];    //�����Ͻ�
    $r_escrow_yn       = $easyPay->_easypay_resdata[ "escrow_yn"       ];    //����ũ�� �������
    $r_complex_yn       = $easyPay->_easypay_resdata[ "complex_yn"       ];    //���հ��� ����
    $r_stat_cd       = $easyPay->_easypay_resdata[ "stat_cd"       ];    //�����ڵ�
    $r_stat_msg       = $easyPay->_easypay_resdata[ "stat_msg"       ];    //���¸޽���
    $r_pay_type       = $easyPay->_easypay_resdata[ "pay_type"       ];    //��������
    $r_mall_id       = $easyPay->_easypay_resdata[ "mall_id"       ];    //��������
    $r_card_no         = $easyPay->_easypay_resdata[ "card_no"         ];    //ī���ȣ
    $r_issuer_cd       = $easyPay->_easypay_resdata[ "issuer_cd"       ];    //�߱޻��ڵ�
    $r_issuer_nm       = $easyPay->_easypay_resdata[ "issuer_nm"       ];    //�߱޻��
    $r_acquirer_cd     = $easyPay->_easypay_resdata[ "acquirer_cd"     ];    //���Ի��ڵ�
    $r_acquirer_nm     = $easyPay->_easypay_resdata[ "acquirer_nm"     ];    //���Ի��
    $r_install_period  = $easyPay->_easypay_resdata[ "install_period"  ];    //�Һΰ���
    $r_noint           = $easyPay->_easypay_resdata[ "noint"           ];    //�����ڿ���
    $r_join_no           = $easyPay->_easypay_resdata[ "join_no"           ];    //������ ��ȣ
    $r_part_cancel_yn   = $easyPay->_easypay_resdata[ "part_cancel_yn"  ];     //�κ���� ���ɿ���
    $r_card_gubun       = $easyPay->_easypay_resdata[ "card_gubun"      ];     //�ſ�ī�� ����
    $r_card_biz_gubun   = $easyPay->_easypay_resdata[ "card_biz_gubun"  ];     //�ſ�ī�� ����
    $r_cpon_flag        = $easyPay->_easypay_resdata[ "cpon_flag"       ];     //�����������
    $r_van_tid          = $easyPay->_easypay_resdata[ "van_tid"         ];     //VAN Tid
    $r_cc_expr_date     = $easyPay->_easypay_resdata[ "cc_expr_date"    ];     //�ſ�ī�� ��ȿ�Ⱓ
    $r_bank_cd          = $easyPay->_easypay_resdata[ "bank_cd"         ];     //�����ڵ�
    $r_bank_nm          = $easyPay->_easypay_resdata[ "bank_nm"         ];     //�����
    $r_account_no       = $easyPay->_easypay_resdata[ "account_no"      ];     //���¹�ȣ
    $r_deposit_nm       = $easyPay->_easypay_resdata[ "deposit_nm"      ];     //�Ա��ڸ�
    $r_expire_date      = $easyPay->_easypay_resdata[ "expire_date"     ];     //���»�븸����
    $r_cash_res_cd      = $easyPay->_easypay_resdata[ "cash_res_cd"     ];     //���ݿ����� ����ڵ�
    $r_cash_res_msg     = $easyPay->_easypay_resdata[ "cash_res_msg"    ];     //���ݿ����� ����޼���
    $r_cash_auth_no     = $easyPay->_easypay_resdata[ "cash_auth_no"    ];     //���ݿ����� ���ι�ȣ
    $r_cash_tran_date   = $easyPay->_easypay_resdata[ "cash_tran_date"  ];     //���ݿ����� �����Ͻ�
    $r_cash_issue_type  = $easyPay->_easypay_resdata[ "cash_issue_type" ];     //���ݿ���������뵵
    $r_cash_auth_type   = $easyPay->_easypay_resdata[ "cash_auth_type"  ];     //��������
    $r_cash_auth_value  = $easyPay->_easypay_resdata[ "cash_auth_value" ];     //������ȣ
    $r_auth_id          = $easyPay->_easypay_resdata[ "auth_id"         ];     //PhoneID
    $r_billid           = $easyPay->_easypay_resdata[ "billid"          ];     //������ȣ
    $r_mobile_no        = $easyPay->_easypay_resdata[ "mobile_no"       ];     //�޴�����ȣ
    $r_mob_ansim_yn     = $easyPay->_easypay_resdata[ "mob_ansim_yn"    ];     //�Ƚɰ��� �������
    $r_ars_no           = $easyPay->_easypay_resdata[ "ars_no"          ];     //��ȭ��ȣ
    $r_cp_cd            = $easyPay->_easypay_resdata[ "cp_cd"           ];     //����Ʈ��/������
    $r_pnt_auth_no      = $easyPay->_easypay_resdata[ "pnt_auth_no"     ];     //����Ʈ���ι�ȣ
    $r_pnt_tran_date    = $easyPay->_easypay_resdata[ "pnt_tran_date"   ];     //����Ʈ�����Ͻ�
    $r_used_pnt         = $easyPay->_easypay_resdata[ "used_pnt"        ];     //�������Ʈ
    $r_remain_pnt       = $easyPay->_easypay_resdata[ "remain_pnt"      ];     //�ܿ��ѵ�
    $r_pay_pnt          = $easyPay->_easypay_resdata[ "pay_pnt"         ];     //����/�߻�����Ʈ
    $r_accrue_pnt       = $easyPay->_easypay_resdata[ "accrue_pnt"      ];     //��������Ʈ
    $r_deduct_pnt       = $easyPay->_easypay_resdata[ "deduct_pnt"      ];     //������ ����Ʈ
    $r_payback_pnt      = $easyPay->_easypay_resdata[ "payback_pnt"     ];     //payback ����Ʈ
    $r_cpon_auth_no     = $easyPay->_easypay_resdata[ "cpon_auth_no"    ];     //�������ι�ȣ
    $r_cpon_tran_date   = $easyPay->_easypay_resdata[ "cpon_tran_date"  ];     //���������Ͻ�
    $r_cpon_no          = $easyPay->_easypay_resdata[ "cpon_no"         ];     //������ȣ
    $r_remain_cpon      = $easyPay->_easypay_resdata[ "remain_cpon"     ];     //�����ܾ�
    $r_used_cpon        = $easyPay->_easypay_resdata[ "used_cpon"       ];     //���� ���ݾ�
    $r_rem_amt          = $easyPay->_easypay_resdata[ "rem_amt"         ];     //�ܾ�
    $r_bk_pay_yn        = $easyPay->_easypay_resdata[ "bk_pay_yn"       ];     //��ٱ��� ��������
    $r_canc_acq_date    = $easyPay->_easypay_resdata[ "canc_acq_date"   ];     //��������Ͻ�
    $r_canc_date        = $easyPay->_easypay_resdata[ "canc_date"       ];     //����Ͻ�
    $r_refund_date      = $easyPay->_easypay_resdata[ "refund_date"     ];     //ȯ�ҿ����Ͻ�    

    
    /* -------------------------------------------------------------------------- */
    /* ::: ������ DB ó��                                                         */
    /* -------------------------------------------------------------------------- */
    /* �����ڵ�(res_cd)�� "0000" �̸� ������� �Դϴ�.                            */
    /* r_amount�� �ֹ�DB�� �ݾװ� �ٸ� �� �ݵ�� ��� ��û�� �Ͻñ� �ٶ��ϴ�.     */
    /* DB ó�� ���� �� ��� ó���� ���ֽñ� �ٶ��ϴ�.                             */
    /* -------------------------------------------------------------------------- */
    if ( $res_cd == "0000" ) {
        $bDBProc = "true";     // DBó�� ���� �� "true", ���� �� "false"
        if ( $bDBProc != "true" ) {
        // ���ο�û�� ���� �� �Ʒ� ����
        if( $TRAN_CD_NOR_PAYMENT == $tr_cd ) {          
            $easyPay->clearup_msg();
            
            $tr_cd = $TRAN_CD_NOR_MGR; 
            $mgr_data = $easyPay->set_easypay_item("mgr_data");
            if ( $r_escrow_yn != "Y" )    
            {
                $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"      , "40"   );
            }
            else
            {
                $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"      , "61"   );
                $easyPay->set_easypay_deli_us( $mgr_data, "mgr_subtype"     , "ES02" );
            }
            $easyPay->set_easypay_deli_us( $mgr_data, "org_cno"         , $r_cno     );
            $easyPay->set_easypay_deli_us( $mgr_data, "order_no"          , $order_no );
            $easyPay->set_easypay_deli_us( $mgr_data, "req_ip"          , $client_ip );
            $easyPay->set_easypay_deli_us( $mgr_data, "req_id"          , "MALL_R_TRANS" );
            $easyPay->set_easypay_deli_us( $mgr_data, "mgr_msg"         , "DB ó�� ���з� �����"  );
            
            $easyPay->easypay_exec($g_mall_id, $tr_cd, $order_no, $client_ip, $opt);
            $res_cd      = $easyPay->_easypay_resdata["res_cd"     ];    // �����ڵ�
            $res_msg     = $easyPay->_easypay_resdata["res_msg"    ];    // ����޽���
            $r_cno       = $easyPay->_easypay_resdata["cno"        ];    // PG�ŷ���ȣ 
            $r_canc_date = $easyPay->_easypay_resdata["canc_date"  ];    // ����Ͻ�
        }
    }
}
?>
<html>
<meta name="robots" content="noindex, nofollow">
<script type="text/javascript">
    function f_submit(){
        document.frm.submit();
    }
</script>

<body onload="f_submit();">
<form name="frm" method="post" action="./result.php">
    <input type="hidden" id="res_cd"           name="res_cd"          value="<?=$res_cd?>">              <!-- ����ڵ� //-->
    <input type="hidden" id="res_msg"          name="res_msg"         value="<?=$res_msg?>">             <!-- ����޽��� //-->
    <input type="hidden" id="cno"              name="cno"             value="<?=$r_cno?>">               <!-- PG�ŷ���ȣ //-->
    <input type="hidden" id="amount"           name="amount"          value="<?=$r_amount?>">            <!-- �� �����ݾ� //-->
    <input type="hidden" id="order_no"         name="order_no"        value="<?=$r_order_no?>">          <!-- �ֹ���ȣ //-->
    <input type="hidden" id="auth_no"          name="auth_no"         value="<?=$r_auth_no?>">           <!-- ���ι�ȣ //-->
    <input type="hidden" id="tran_date"        name="tran_date"       value="<?=$r_tran_date?>">         <!-- �����Ͻ� //-->
    <input type="hidden" id="escrow_yn"        name="escrow_yn"       value="<?=$r_escrow_yn?>">         <!-- ����ũ�� ������� //-->
    <input type="hidden" id="complex_yn"       name="complex_yn"      value="<?=$r_complex_yn?>">        <!-- ���հ��� ���� //-->
    <input type="hidden" id="stat_cd"          name="stat_cd"         value="<?=$r_stat_cd?>">           <!-- �����ڵ� //-->
    <input type="hidden" id="stat_msg"         name="stat_msg"        value="<?=$r_stat_msg?>">          <!-- ���¸޽��� //-->
    <input type="hidden" id="pay_type"         name="pay_type"        value="<?=$r_pay_type?>">          <!-- �������� //-->
    <input type="hidden" id="mall_id"          name="mall_id"         value="<?=$r_mall_id?>">           <!-- ������ Mall ID //-->
    <input type="hidden" id="card_no"          name="card_no"         value="<?=$r_card_no?>">           <!-- ī���ȣ //-->
    <input type="hidden" id="issuer_cd"        name="issuer_cd"       value="<?=$r_issuer_cd?>">         <!-- �߱޻��ڵ� //-->
    <input type="hidden" id="issuer_nm"        name="issuer_nm"       value="<?=$r_issuer_nm?>">         <!-- �߱޻�� //-->
    <input type="hidden" id="acquirer_cd"      name="acquirer_cd"     value="<?=$r_acquirer_cd?>">       <!-- ���Ի��ڵ� //-->
    <input type="hidden" id="acquirer_nm"      name="acquirer_nm"     value="<?=$r_acquirer_nm?>">       <!-- ���Ի�� //-->
    <input type="hidden" id="install_period"   name="install_period"  value="<?=$r_install_period?>">    <!-- �Һΰ��� //-->
    <input type="hidden" id="noint"            name="noint"           value="<?=$r_noint?>">             <!-- �����ڿ��� //-->
    <input type="hidden" id="join_no"          name="join_no"         value="<?=$r_join_no?>">           <!-- ������ ��ȣ //-->
    <input type="hidden" id="part_cancel_yn"   name="part_cancel_yn"  value="<?=$r_part_cancel_yn?>">    <!-- �κ���� ���ɿ��� //-->
    <input type="hidden" id="card_gubun"       name="card_gubun"      value="<?=$r_card_gubun?>">        <!-- �ſ�ī�� ���� //-->
    <input type="hidden" id="card_biz_gubun"   name="card_biz_gubun"  value="<?=$r_card_biz_gubun?>">    <!-- �ſ�ī�� ���� //-->
    <input type="hidden" id="cpon_flag"        name="cpon_flag"       value="<?=$r_cpon_flag?>">         <!-- ����������� //-->
    <input type="hidden" id="van_tid"          name="van_tid"         value="<?=$r_van_tid?>">           <!-- VAN Tid //-->
    <input type="hidden" id="cc_expr_date"     name="cc_expr_date"    value="<?=$r_cc_expr_date?>">      <!-- �ſ�ī�� ��ȿ�Ⱓ //-->
    <input type="hidden" id="bank_cd"          name="bank_cd"         value="<?=$r_bank_cd?>">           <!-- �����ڵ� //-->
    <input type="hidden" id="bank_nm"          name="bank_nm"         value="<?=$r_bank_nm?>">           <!-- ����� //-->
    <input type="hidden" id="account_no"       name="account_no"      value="<?=$r_account_no?>">        <!-- ���¹�ȣ //-->
    <input type="hidden" id="deposit_nm"       name="deposit_nm"      value="<?=$r_deposit_nm?>">        <!-- �Ա��ڸ� //-->
    <input type="hidden" id="expire_date"      name="expire_date"     value="<?=$r_expire_date?>">       <!-- ���»�븸���� //-->
    <input type="hidden" id="cash_res_cd"      name="cash_res_cd"     value="<?=$r_cash_res_cd?>">       <!-- ���ݿ����� ����ڵ� //-->
    <input type="hidden" id="cash_res_msg"     name="cash_res_msg"    value="<?=$r_cash_res_msg?>">      <!-- ���ݿ����� ����޼��� //-->
    <input type="hidden" id="cash_auth_no"     name="cash_auth_no"    value="<?=$r_cash_auth_no?>">      <!-- ���ݿ����� ���ι�ȣ //-->
    <input type="hidden" id="cash_tran_date"   name="cash_tran_date"  value="<?=$r_cash_tran_date?>">    <!-- ���ݿ����� �����Ͻ� //-->
    <input type="hidden" id="cash_issue_type"  name="cash_issue_type" value="<?=$r_cash_issue_type?>">   <!-- ���ݿ���������뵵 //-->
    <input type="hidden" id="cash_auth_type"   name="cash_auth_type"  value="<?=$r_cash_auth_type?>">    <!-- �������� //-->
    <input type="hidden" id="cash_auth_value"  name="cash_auth_value" value="<?=$r_cash_auth_value?>">   <!-- ������ȣ //-->
    <input type="hidden" id="auth_id"          name="auth_id"         value="<?=$r_auth_id?>">           <!-- PhoneID //-->
    <input type="hidden" id="billid"           name="billid"          value="<?=$r_billid?>">            <!-- ������ȣ //-->
    <input type="hidden" id="mobile_no"        name="mobile_no"       value="<?=$r_mobile_no?>">         <!-- �޴�����ȣ //-->
    <input type="hidden" id="mob_ansim_yn"     name="mob_ansim_yn"    value="<?=$r_mob_ansim_yn?>">      <!-- �Ƚɰ��� ������� //-->
    <input type="hidden" id="ars_no"           name="ars_no"          value="<?=$r_ars_no?>">            <!-- ��ȭ��ȣ //-->
    <input type="hidden" id="cp_cd"            name="cp_cd"           value="<?=$r_cp_cd?>">             <!-- ����Ʈ��/������ //-->
    <input type="hidden" id="pnt_auth_no"      name="pnt_auth_no"     value="<?=$r_pnt_auth_no?>">       <!-- ����Ʈ���ι�ȣ //-->
    <input type="hidden" id="pnt_tran_date"    name="pnt_tran_date"   value="<?=$r_pnt_tran_date?>">     <!-- ����Ʈ�����Ͻ� //-->
    <input type="hidden" id="used_pnt"         name="used_pnt"        value="<?=$r_used_pnt?>">          <!-- �������Ʈ //-->
    <input type="hidden" id="remain_pnt"       name="remain_pnt"      value="<?=$r_remain_pnt?>">        <!-- �ܿ��ѵ� //-->
    <input type="hidden" id="pay_pnt"          name="pay_pnt"         value="<?=$r_pay_pnt?>">           <!-- ����/�߻�����Ʈ //-->
    <input type="hidden" id="accrue_pnt"       name="accrue_pnt"      value="<?=$r_accrue_pnt?>">        <!-- ��������Ʈ //-->
    <input type="hidden" id="deduct_pnt"       name="deduct_pnt"      value="<?=$r_deduct_pnt?>">        <!-- ������ ����Ʈ //-->
    <input type="hidden" id="payback_pnt"      name="payback_pnt"     value="<?=$r_payback_pnt?>">       <!-- payback ����Ʈ //-->
    <input type="hidden" id="cpon_auth_no"     name="cpon_auth_no"    value="<?=$r_cpon_auth_no?>">      <!-- �������ι�ȣ //-->
    <input type="hidden" id="cpon_tran_date"   name="cpon_tran_date"  value="<?=$r_cpon_tran_date?>">    <!-- ���������Ͻ� //-->
    <input type="hidden" id="cpon_no"          name="cpon_no"         value="<?=$r_cpon_no?>">           <!-- ������ȣ //-->
    <input type="hidden" id="remain_cpon"      name="remain_cpon"     value="<?=$r_remain_cpon?>">       <!-- �����ܾ� //-->
    <input type="hidden" id="used_cpon"        name="used_cpon"       value="<?=$r_used_cpon?>">         <!-- ���� ���ݾ� //-->
    <input type="hidden" id="rem_amt"          name="rem_amt"         value="<?=$r_rem_amt?>">           <!-- �ܾ� //-->
    <input type="hidden" id="bk_pay_yn"        name="bk_pay_yn"       value="<?=$r_bk_pay_yn?>">         <!-- ��ٱ��� �������� //-->
    <input type="hidden" id="canc_acq_date"    name="canc_acq_date"   value="<?=$r_canc_acq_date?>">     <!-- ��������Ͻ� //-->
    <input type="hidden" id="canc_date"        name="canc_date"       value="<?=$r_canc_date?>">         <!-- ����Ͻ� //-->
    <input type="hidden" id="refund_date"      name="refund_date"     value="<?=$r_refund_date?>">       <!-- ȯ�ҿ����Ͻ� //-->
    
</form>
</body>
</html>
