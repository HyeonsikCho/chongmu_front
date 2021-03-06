
$(document).ready(function(){
	$('.cart_prdlist_id').bind('click',function(){
		calPrice();
	});
	$('#c_order').bind('click',function(){
		goOrder();
	});
	$('#a_order').bind('click',function(){
		goOrder('all');
	});
	$('#cart_general').bind('click',function(){
		setPrdCheck();
	});
	$('#del_cart').bind('click',function(){
		delCart();
	});
});
var setPrdCheck = function(){
	if($('._general').prop('checked') == true){
		$('.cart_prdlist_id').each(function(){
			$(this).attr('checked',true);
		});
	}else{
		$('.cart_prdlist_id').each(function(){
			$(this).attr('checked',false);
		});
	}
	calPrice();
	return;
}
//금액계산 function
var calPrice = function(){
	var i=0;
	var c_check = false;
	var amount = 0;
	var tax_amount = 0;
	$('.cart_prdlist_id, .prd_amnt, .taxadd_prd_amnt').each(function(){

		if($(this).is('.cart_prdlist_id') && $(this).prop('checked') == true){
			//객체가 체크박스이고 체크된 상태이면 ...
			c_check = true;
		}
		if(c_check === true){
			if($(this).is('.prd_amnt')){
				amount += parseInt($(this).val());
			}
			if($(this).is('.taxadd_prd_amnt')){
				tax_amount += parseInt($(this).val());
				c_check = false;
			}
		}
	});

	//number format jquery 모듈 붙여다가 처리함 $.number
	$('#price_target').text($.number(amount));
	$('#tax_price').text($.number(tax_amount));
	return;
}
//장바구니 삭제 function

var delCart = function(){
	var chkCnt = 0;
	var cart_prdlist_id = [];
	var obj;
	var url = "/proc/order/del_cart.php";
	$('.cart_prdlist_id').each(function(){
		obj = $(this);
		if(obj.prop('checked') == true){
			cart_prdlist_id.push(obj.val());
			chkCnt++;
		}
	});
	if(chkCnt === 0){
		alert('삭제할 상품을 선택해 주세요');
		return;
	}

    var data = {
    	"cart_prdlist_id" : cart_prdlist_id
	};
    var callback = function(result) {
		if(result == 1){
			alert('삭제가 완료되었습니다');
		}else{
			alert('삭제가 실패되었습니다. 다시 시도해 주세요');
		}

		location.reload();
    };
	showMask();
	ajaxCall(url, "html", data, callback);
	return;
}


//주문결제넘기기 function

var goOrder = function(flag){
	var chkCnt = 0;
	var cart_prdlist_id = [];
	var obj;

	var url = "/proc/order/set_order.php";
	$('.cart_prdlist_id').each(function(){
		obj = $(this);
		if(flag == 'all') obj.prop('checked',true);
		if(obj.prop('checked') == true){
			cart_prdlist_id.push(obj.val());
			chkCnt++;
		}
	});
	if(chkCnt === 0){
		alert('주문할 상품을 선택해 주세요');
		return;
	}

    var data = {
    	"cart_prdlist_id" : cart_prdlist_id
	};
    var callback = function(result) {
		var obj = result;


		if(obj.result === "true"){
			location.replace('/order/order.html?order_no='+obj.ordno+"&cart_prdlist_id="+cart_prdlist_id);
		}else{
			alert('주문처리중 오류가 발생하였습니다. 다시 시도해주세요');
		}


    };
	//console.log(chkCnt);
	showMask();
	ajaxCall(url, "json", data, callback);
	return;
}

