/*
포인트사용
*/


$(document).ready(function() {
    var mimeTypes = [
        {extensions : "zip,egg,rar,jpg,jpeg,sit,zip,ai,png,alz,cdr,cdt,eps,cmx,7z,pdf"}
    ];

	var fcnt = $('#listcnt').val() || 0;
	for(var fidx=0;fidx < fcnt; fidx++){
		fupload[fidx] = setFiles(fidx);
	}
});


/********************************************************************
***** 주문정보 삭제
********************************************************************/

var delOrder = function(order_no,prd_detail_no){
	var cnt = parseInt($('#listcnt').val())/2;
	var recnt = parseInt($('#listcnt').val()) - 2;

	if(cnt == 1){
		alert('마지막 상품은 삭제하실 수 없습니다.');
		return;
	}

	var url = "/proc/order/del_order.php";

    var data = {
		"order_no" : order_no,
    	"prd_detail_no" : prd_detail_no
	};

    var callback = function(msg) {
		if(msg.result == "true"){
			var point_amnt = parseInt($('#EP_user_define2').val());

			if(point_amnt > msg.addtax_order_amnt){
				alert('포인트 사용금액이 주문금액을 초과하였습니다\n포인트 사용금액을 초기화합니다');
				$('#EP_user_define2').val(0);
				$('#EP_user_define1').val('');
				$('#discount').html(0);
				$('.vplzp_point').val(0);
				point_amnt = 0;
			}

			var tot_amnt = parseInt(msg.addtax_order_amnt) - point_amnt;
			$('#prd_amnt').html($.number(msg.addtax_prd_amnt));
			$('#order_amnt').html($.number(msg.addtax_order_amnt));
			$('.last_amnt').html($.number(tot_amnt));
			$('#EP_product_amt').val(tot_amnt);
			$('#tot_order_amnt').val(tot_amnt);
			$('#prdlist'+prd_detail_no).remove();
			$('#listcnt').val(recnt);
			$('#EP_product_nm').val($('#order_f_title').val()+'외 '+cnt+'건');
			$('#pay_order_amnt').val(msg.addtax_prd_amnt);

			location.reload();
		} else {
			alert('삭제가 실패되었습니다. 다시 시도해 주세요');
		}

    };

	showMask();
	ajaxCall(url, "json", data, callback);
}
/*

*/

var setDelivery  = function(obj,order_no,type){
	var url = "/ajax/order/set_delivery.php";
	var tot_amnt = 0;
	var point_amnt = parseInt($('#EP_user_define2').val());

	var order_amnt = parseInt($('#tot_order_amnt').val());
	var cal_val = 0;
    var data = {
		"order_no" : order_no,
		"type" : type
	};
    var callback = function(result) {
		if(result.result == 'true'){

			if(type == 'add'){
                //order_amnt 주문합계금액
                //addtax_prd_amnt 상품가격
                order_amnt = result.order_amnt; // 주문합계금액
                addtax_prd_amnt = result.addtax_prd_amnt; // 상품가격
                delivery_amnt = order_amnt - addtax_prd_amnt; // 배송비

				order_amnt = tot_amnt = order_amnt;
				tot_amnt = tot_amnt - point_amnt;
				$('#deli_pay').html($.number(delivery_amnt));
				$('.last_amnt').html($.number(tot_amnt));
				$('#order_amnt').html($.number(order_amnt));
                $('#prd_amnt').html($.number(addtax_prd_amnt));
				$('#tot_order_amnt').val(order_amnt);
				$('#EP_product_amt').val(tot_amnt);

			}else{
				console.log(point_amnt);
				console.log(order_amnt);
				if(point_amnt >= order_amnt){
					alert('포인트 사용금액이 주문금액을 초과하였습니다\n포인트 사용금액을 초기화합니다');
					$('#EP_user_define2').val(0);
					$('#EP_user_define1').val('');
					$('#discount').html(0);
					$('.vplzp_point').val(0);
					point_amnt = 0;
				}
                order_amnt = result.order_amnt; // 주문합계금액
                addtax_prd_amnt = result.addtax_prd_amnt; // 상품가격
                delivery_amnt = order_amnt - addtax_prd_amnt; // 배송비

                order_amnt = tot_amnt = order_amnt;
                tot_amnt = tot_amnt - point_amnt;

				$('#deli_pay').html($.number(0));
				$('.last_amnt').html($.number(tot_amnt));
				$('#order_amnt').html($.number(order_amnt));
                $('#prd_amnt').html($.number(addtax_prd_amnt));
				$('#tot_order_amnt').val(order_amnt);
				$('#EP_product_amt').val(tot_amnt);

			}

			$('#EP_product_amt').val(tot_amnt);
		}else{
			alert('배송비 지불변경중 오류가 발생했습니다.\n관리자에게 문의해주세요');
			obj.checked = true;
		}

		return;

    };
	showMask();
	ajaxCall(url, "json", data, callback);
}

var ftest = function(){
	console.log('결제대상금액 계속변해야한다(EP_product_amt) : '+$('#EP_product_amt').val());
	console.log('주문금액함계(tot_order_amnt) : '+$('#tot_order_amnt').val());
	console.log('포인트금액(ep_user_define2) : '+$('#EP_user_define2').val());
	console.log('주문금액(pay_order_amnt) : '+$('#pay_order_amnt').val());

}
var fupload = [];
var setFiles = function(idx){

		var uploader = new plupload.Uploader({
			runtimes : 'html5,flash,silverlight,html4',
			browse_button : 'pickfiles'+idx, // you can pass an id...
			container: document.getElementById('container'+idx), // ... or DOM Element itself
			url : '/proc/common/file_upload_common.php',
			flash_swf_url : '/plupload/js/Moxie.swf',
			silverlight_xap_url : '/plupload/js/Moxie.xap',
		    multipart_params : {
				"order_no" : "",
				"prd_detail_no" : "",
				"dvs" : "",
				"type" : "",
				"ftype" : ""
			},
			chunk_size: '5mb', //업로드단위 분할 사이즈
			max_file_count : 1,
			filters : {
				max_file_size : '1000mb',
				mime_types: [
					{title : "Image files", extensions : "zip,egg,rar,jpg,jpeg,sit,zip,ai,png,alz,cdr,cdt,eps,cmx,7z,pdf"}
				]
			},

			init: {
				PostInit: function() {
                    /*
					document.getElementById('uploadfiles'+idx).onclick = function() {
						uploader.settings.multipart_params["order_no"] = $('#container'+idx).attr('ono');
						uploader.settings.multipart_params["prd_detail_no"] = $('#container'+idx).attr('dno');
						uploader.settings.multipart_params["dvs"] = $('#container'+idx).attr('dvs');
						uploader.settings.multipart_params["type"] = 'user';
						uploader.settings.multipart_params["ftype"] = 'order';
						$('#filelist'+idx).attr('fchk',false); //파일업로드중 결제진행을 못하기 위해 설정
						uploader.start();
						return false;
					};
					*/
				},

				FilesAdded: function(up, files) {
				//console.log(uploader<?=$i?>.files);

					plupload.each(files, function(file) {
						if(uploader.files.length > 1){
							uploader.files.reverse();
							uploader.files.pop();
						}
						document.getElementById('filelist'+idx).innerHTML = '<span id="' + file.id + '">' + file.name +' (' + plupload.formatSize(file.size) + ')<b></b></span>';

						//$('#uploadfiles'+idx).show();

					});

                    uploader.settings.multipart_params["order_no"] = $('#container'+idx).attr('ono');
                    uploader.settings.multipart_params["prd_detail_no"] = $('#container'+idx).attr('dno');
                    uploader.settings.multipart_params["dvs"] = $('#container'+idx).attr('dvs');
                    uploader.settings.multipart_params["type"] = 'user';
                    uploader.settings.multipart_params["ftype"] = 'order';
                    $('#filelist'+idx).attr('fchk',false); //파일업로드중 결제진행을 못하기 위해 설정
                    uploader.start();

				},

				UploadProgress: function(up, file) {
					document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
				},
				UploadComplete: function(up, files) {
					$('#filelist'+idx).attr('fchk',true);
				},
				Error: function(up, err) {
					alert('파일업로드가 정상적으로 이루어지지 않았습니다 \n관리자에게 문의해주세요');
				}
			}
		});
		uploader.init();

		return uploader;
}


/********************************************************************
***** 포인트 사용
********************************************************************/

var getPoint = function(){
	if(parseInt($('#use_point').val()) > parseInt($('#user_point').val())){
		alert('보유포인트를 초과할 수 없습니다.');
		return;
	}

	if(parseInt($('#use_point').val()) > $('#tot_order_amnt').val()){
		alert('주문금액 총액을 초과할 수 없습니다.');
		return;
	}

	var use_point = parseInt($('#use_point').val());
	var tot_order_amnt = parseInt($('#tot_order_amnt').val());

	$('.vplzp_point').val($('#use_point').val());
	$('#EP_user_define1').val($('#plzp_id').val());
	$('#EP_user_define2').val($('#use_point').val());
	$('#discount').html($.number($('#use_point').val()));

	$('.last_amnt').html($.number(tot_order_amnt - use_point));
	$('#EP_product_amt').val(tot_order_amnt - use_point);

	return;
}

var getAllPoint = function(){
	var use_point;
	var tot_order_amnt;

	//구매금액보다 마일리지가 많을 때(구매금액만큼 사용)
	if(parseInt($('#user_point').val()) < $('#tot_order_amnt').val()) {
		use_point = parseInt($('#user_point').val());
		tot_order_amnt = parseInt($('#tot_order_amnt').val());
	}

	//마일리지가 구매금액보다 많을 때(마일리지만큼 사용)
	if(parseInt($('#user_point').val()) >= $('#tot_order_amnt').val()) {
		use_point = parseInt($('#tot_order_amnt').val());
		tot_order_amnt = parseInt($('#tot_order_amnt').val());
	}

	$('.vplzp_point').val(use_point);
	$('#EP_user_define1').val($('#plzp_id').val());
	$('#EP_user_define2').val(use_point);
	$('#discount').html($.number(use_point));

	$('.last_amnt').html($.number(tot_order_amnt - use_point));
	$('#EP_product_amt').val(tot_order_amnt - use_point);

	return;
}


var f_chk = function(){
	if($('#order_user_name').val() == ''){
		alert('보내시는분 이름을 입력해주세요');
		return false;
	}
	if($('#order_user_telno').val() == ''){
		alert('보내시는분 연락처를 입력해주세요');
		return false;
	}
	if($('#order_user_phno').val() == ''){
		alert('보내시는분 핸드폰번호를 입력해주세요');
		return false;
	}
	if($('#order_user_zipcode').val() == ''){
		alert('보내시는분 우편번호를 입력해주세요');
		return false;
	}
	if($('#order_user_addr1').val() == ''){
		alert('보내시는분 주소를 입력해주세요');
		return false;
	}
	if($('#order_user_addr2').val() == ''){
		alert('보내시는분 주소를 입력해주세요');
		return false;
	}
	if($('#res_user_name').val() == ''){
		alert('받으시는분 이름을 입력해주세요');
		return false;
	}
	if($('#res_user_telno').val() == ''){
		alert('받으시는분 연락처를 입력해주세요');
		return false;
	}
	if($('#res_user_phno').val() == ''){
		alert('받으시는분 핸드폰번호를 입력해주세요');
		return false;
	}
	if($('#res_user_zipcode').val() == ''){
		alert('받으시는분 우편번호를 입력해주세요');
		return false;
	}
	if($('#res_user_addr1').val() == ''){
		alert('받으시는분 주소를 입력해주세요');
		return false;
	}
	if($('#res_user_addr2').val() == ''){
		alert('받으시는분 주소를 입력해주세요');
		return false;
	}
	return true;
}
var orderComment = function(){
	var url = "/ajax/order/order_comment_insert.php";
	var prdData = [];
	var prdNo = [];
	var cnt = parseInt($('#listcnt').val())/2;
	for(var i=1;i<=cnt;i++){
			prdData.push($('#memo'+i).val());
			prdNo.push($('#memo'+i).attr('prd_detail_no'));

	}
	var data = {
		"order_no" : $('#EP_order_no').val(),
		"prd_detail_no" : prdNo,
		"prdData" : prdData,
		"order_comment" : $('#order_memo').val()
	}
    var callback = function(result) {
		if(result == 1){
			return true;
		}else{
			alert('주문요청사항 처리중 오류가 발생했습니다.\n관리자에게 문의하세요');
			return false;
		}


    };
    ajaxCallUnhide(url, "html", data, callback);

}
var orderDlvr  = function(){
	var url = "/ajax/order/order_dlvr_insert.php";

    dlvrWay = $(':radio[name="pamentType"]:checked').attr("value");
    order_user_telno = $('#order_user_telno1').val() + "-" + $('#order_user_telno2').val() + "-" + $('#order_user_telno3').val();
    order_user_phno = $('#order_user_phno1').val() + "-" + $('#order_user_phno2').val() + "-" + $('#order_user_phno3').val();
    res_user_telno = $('#res_user_telno1').val() + "-" + $('#res_user_telno2').val() + "-" + $('#res_user_telno3').val();
    res_user_phno = $('#res_user_phno1').val() + "-" + $('#res_user_phno2').val() + "-" + $('#res_user_phno3').val();

    var data = {
		"order_no" : $('#EP_order_no').val(),
		"order_user_name" : $('#order_user_name').val() ,
		"order_user_telno" : order_user_telno ,
		"order_user_phno" : order_user_phno ,
		"order_user_zipcode" : $('#order_user_zipcode').val() ,
		"order_user_addr1" : $('#order_user_addr1').val() ,
		"order_user_addr2" : $('#order_user_addr2').val() ,
		"res_user_name" : $('#res_user_name').val() ,
		"res_user_telno" : res_user_telno ,
        "res_user_phno" : res_user_phno ,
		"res_user_zipcode" : $('#res_user_zipcode').val() ,
		"res_user_addr1" : $('#res_user_addr1').val() ,
		"res_user_addr2" : $('#res_user_addr2').val() ,
        "dlvr_pay_way" : dlvrWay
	};
    var callback = function(result) {
		if(result == 1){
			return true;
		}else{
			alert('배송정보 처리중 오류가 발생했습니다.\n관리자에게 문의하세요');
			return false;
		}


    };
	ajaxCallUnhide(url, "html", data, callback);
}


/********************************************************************
***** 파일업로드 체크
********************************************************************/

var fileUploadingChk = function(){
	var chk = 'true';
	$('.filechk').each(function(){

		if($(this).attr('fchk') === 'false'){
			chk = 'false';
		}
	});
	if(chk === 'false'){
		return false;
	}else{
		return true;
	}
}


/********************************************************************
***** 숫자 체크
********************************************************************/

function checkDigitOnly(digitChar) {
     if ( digitChar == null ) return false ;

     for (var i = 0; i < digitChar.length; i++) {
		  var c=digitChar.charCodeAt(i);

		  if (!(0x30 <= c && c <= 0x39)) {
			 return false ;
		  }
     }

     return true ;
 }


/********************************************************************
***** 숫자 필드이동
********************************************************************/
function autoMove(o,m,s) {
	if (o.length == s) {
		m.focus();
	}
 }


/********************************************************************
***** 결제 시작
********************************************************************/

var start_pay = function(){

	var f_chk_val = fileUploadingChk();
	if(f_chk_val === false){
		alert('파일업로드 완료 후 결제를 진행하세요');
		return;
	}
    f_chk_val = f_chk();
	if(f_chk_val === false){
		return;
	}
	f_chk_val = orderDlvr();
	if(f_chk_val === false){
		return;
	}
	f_chk_val = orderComment();
	if(f_chk_val === false){
		return;
	}

	// 보내시는분
	if ($('#order_user_name').val() == '') {
		alert('보내시는분의 성명/상호명을 입력해주세요');
		$('#order_user_name').focus();
		return false;
	}

	if ($('#order_user_phno1').val() == '') {
		alert('보내시는분의 휴대전화 번호를 입력해주세요');
		$('#order_user_phno1').focus();
		return false;
	}

	if (checkDigitOnly($('#order_user_phno1').val()) == false) {
		alert('보내시는분의 휴대전화 번호는 숫자만 입력해주세요');
		$('#order_user_phno1').val();
		$('#order_user_phno1').focus();
		return false;
	}

	if ($('#order_user_phno2').val() == '') {
		alert('보내시는분의 휴대전화 번호를 입력해주세요');
		$('#order_user_phno2').focus();
		return false;
	}

	if (checkDigitOnly($('#order_user_phno2').val()) == false) {
		alert('보내시는분의 휴대전화 번호는 숫자만 입력해주세요');
		$('#order_user_phno2').val();
		$('#order_user_phno2').focus();
		return false;
	}

	if ($('#order_user_phno3').val() == '') {
		alert('보내시는분의 휴대전화 번호를 입력해주세요');
		$('#order_user_phno3').focus();
		return false;
	}

	if (checkDigitOnly($('#order_user_phno3').val()) == false) {
		alert('보내시는분의 휴대전화 번호는 숫자만 입력해주세요');
		$('#order_user_phno3').val();
		$('#order_user_phno3').focus();
		return false;
	}

	if ($('#order_user_zipcode').val() == '') {
		alert('보내시는분의 주소를 입력해주세요');
		$('#order_user_zipcode').focus();
		return false;
	}

	if ($('#order_user_addr1').val() == '') {
		alert('보내시는분의 주소를 입력해주세요');
		$('#order_user_addr1').focus();
		return false;
	}

	if ($('#order_user_addr2').val() == '') {
		alert('보내시는분의 상세주소를 입력해주세요');
		$('#order_user_addr2').focus();
		return false;
	}

	// 받으시는분
	if ($('#res_user_name').val() == '') {
		alert('받으시는분의 성명/상호명을 입력해주세요');
		$('#res_user_name').focus();
		return false;
	}

	if ($('#res_user_phno1').val() == '') {
		alert('보내시는분의 휴대전화 번호를 입력해주세요');
		$('#res_user_phno1').focus();
		return false;
	}

	if (checkDigitOnly($('#res_user_phno1').val()) == false) {
		alert('보내시는분의 휴대전화 번호는 숫자만 입력해주세요');
		$('#res_user_phno1').val();
		$('#res_user_phno1').focus();
		return false;
	}

	if ($('#res_user_phno2').val() == '') {
		alert('보내시는분의 휴대전화 번호를 입력해주세요');
		$('#res_user_phno2').focus();
		return false;
	}

	if (checkDigitOnly($('#res_user_phno2').val()) == false) {
		alert('보내시는분의 휴대전화 번호는 숫자만 입력해주세요');
		$('#res_user_phno2').val();
		$('#res_user_phno2').focus();
		return false;
	}

	if ($('#res_user_phno3').val() == '') {
		alert('보내시는분의 휴대전화 번호를 입력해주세요');
		$('#res_user_phno3').focus();
		return false;
	}

	if (checkDigitOnly($('#res_user_phno3').val()) == false) {
		alert('보내시는분의 휴대전화 번호는 숫자만 입력해주세요');
		$('#res_user_phno3').val();
		$('#res_user_phno3').focus();
		return false;
	}

	if ($('#res_user_zipcode').val() == '') {
		alert('보내시는분의 주소를 입력해주세요');
		$('#res_user_zipcode').focus();
		return false;
	}

	if ($('#res_user_addr1').val() == '') {
		alert('보내시는분의 주소를 입력해주세요');
		$('#res_user_addr1').focus();
		return false;
	}

	if ($('#res_user_addr2').val() == '') {
		alert('보내시는분의 상세주소를 입력해주세요');
		$('#res_user_addr2').focus();
		return false;
	}

	if (parseInt($('#EP_user_define2').val()) == parseInt($('#tot_order_amnt').val())) {
		pointPay();
	} else {
		f_start_pay();
	}

	return;
}


/********************************************************************
***** 포인트 조회
********************************************************************/

var cpointPop = function(){
	var url = "/order/popup/l_point.html";

    var data = {
	};
    var callback = function(result) {
		if (result == "FAILED") {
			layerPopupFail('l_point');
		} else {
			layerPopupSucess('l_point', result);
		}
    };

	showMask();
	ajaxCall(url, "html", data, callback);
}


/********************************************************************
***** 포인트 결제
********************************************************************/

var pointPay = function(){
	if($('#EP_user_define1').val() == "" || $('#EP_user_define2').val() == ""){
		alert('포인트 전액결제 오류가 발생했습니다.\n관리자에게 문의해주세요(err:001)');
		return;
	}

	var url = "/proc/order/point_order_pay.php";

    var data = {
		order_no : $('#EP_order_no').val(),
		title : $('#EP_product_nm').val(),
		plzp_id : $('#EP_user_define1').val(),
		plzp_point :$('#EP_user_define2').val(),
		cart_prdlist_id:$('#EP_user_define3').val()
	};

    var callback = function(data) {
		if(data.result == 'true'){
			$('#order_no').val($('#EP_order_no').val());
			$('#title').val($('#EP_product_nm').val());
			$('#t_amount').val($('#EP_user_define2').val());
			$('#plzp_point').val($('#EP_user_define2').val());
			$('#p_pay').submit();
		} else {
			alert('포인트 전액결제 오류가 발생했습니다.\n관리자에게 문의해주세요(err:002)');
		}
    };

	showMask();
	ajaxCall(url, "json", data, callback);
}

var dlvr_change = function(type_cd,info){

	if(info == 'send'){
		if(type_cd == 1){
			$('#order_user_name').val($('#fs_user_name').val());
			$('#order_user_telno').val($('#fs_tel_num').val());
			$('#order_user_phno').val($('#fs_ph_num').val());
			$('#order_user_zipcode').val($('#fs_zipcode').val());
			$('#order_user_addr1').val($('#fs_addr1').val());
			$('#order_user_addr2').val($('#fs_addr2').val());
		}else if(type_cd == 3){
			$('#order_user_name').val('');
			$('#order_user_telno1').val('');
			$('#order_user_telno2').val('');
			$('#order_user_telno3').val('');
			$('#order_user_phno1').val('');
			$('#order_user_phno2').val('');
			$('#order_user_phno3').val('');
			$('#order_user_zipcode').val('');
			$('#order_user_addr1').val('');
			$('#order_user_addr2').val('');
		}
	}else{
		if(type_cd == 1){
			$('#res_user_name').val($('#fs_user_name').val());
			$('#res_user_telno').val($('#order_user_telno').val());
			$('#res_user_phno').val($('#order_user_phno').val());
			$('#res_user_zipcode').val($('#order_user_zipcode').val());
			$('#res_user_addr1').val($('#order_user_addr1').val());
			$('#res_user_addr2').val($('#order_user_addr2').val());
		}else if(type_cd == 2){
			$('#res_user_name').val($('#order_user_name').val());
			$('#res_user_telno').val($('#fs_tel_num').val());
			$('#res_user_phno').val($('#fs_ph_num').val());
			$('#res_user_zipcode').val($('#fs_zipcode').val());
			$('#res_user_addr1').val($('#fs_addr1').val());
			$('#res_user_addr2').val($('#fs_addr2').val());
			$('#res_user_telno1').val($('#order_user_telno1').val());
			$('#res_user_telno2').val($('#order_user_telno2').val());
			$('#res_user_telno3').val($('#order_user_telno3').val());
			$('#res_user_phno1').val($('#order_user_phno1').val());
			$('#res_user_phno2').val($('#order_user_phno2').val());
			$('#res_user_phno3').val($('#order_user_phno3').val());
		}else if(type_cd == 3){
			$('#res_user_name').val('');
			$('#res_user_telno1').val('');
			$('#res_user_telno2').val('');
			$('#res_user_telno3').val('');
			$('#res_user_phno1').val('');
			$('#res_user_phno2').val('');
			$('#res_user_phno3').val('');
			$('#res_user_zipcode').val('');
			$('#res_user_addr1').val('');
			$('#res_user_addr2').val('');
		}
	}
	return;
}