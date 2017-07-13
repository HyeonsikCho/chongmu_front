/*
 *
 * Copyright (c) 2016 Nexmotion, Inc.
 * All rights reserved.
 *
 * REVISION HISTORY (reverse chronological order)
 *============================================================================
 * 2016/02/23 왕초롱 생성
 *============================================================================
 *
 */

$(document).ready(function() {

    //일자별 검색 datepicker 기본 셋팅
    $("#from").datepicker({
        dateFormat: 'yy-mm-dd',
        prevText: '이전 달',
        nextText: '다음 달',
        monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        dayNames: ['일','월','화','수','목','금','토'],
        dayNamesShort: ['일','월','화','수','목','금','토'],
        dayNamesMin: ['일','월','화','수','목','금','토'],
        showMonthAfterYear: true,
        yearSuffix: '년'
    });

    $("#to").datepicker({
        dateFormat: 'yy-mm-dd',
        prevText: '이전 달',
        nextText: '다음 달',
        monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        dayNames: ['일','월','화','수','목','금','토'],
        dayNamesShort: ['일','월','화','수','목','금','토'],
        dayNamesMin: ['일','월','화','수','목','금','토'],
        showMonthAfterYear: true,
        yearSuffix: '년'
    });
	dateSet(7);
	orderSearch();

});

var tempPop;

var draftPop = function(order_no,prd_detail_no) {
	var url = "/ajax/mypage/order_all/load_order_draft.php";
    var data = {
    	"order_no"       : order_no,
    	"prd_detail_no"  : prd_detail_no
	};
    var callback = function(result) {
		//alert(result);
		tempPop = layerPopup('l_draft',result);
    }

  //  showMask();
    ajaxCall(url, "html", data, callback);
}

var draftResult = function(order_no,prd_detail_no,draft_chk,draft_comment){
	if ($('.l_draft .draftCheck input[type=radio]:checked').length == 0) {
		alert('시안을 확인하시고 진행 여부를 선택해주세요.');
		return;
	}
	var url = "/proc/mypage/order_all/proc_draft_result.php";
	var data = {
		"order_no"       : order_no,
		"prd_detail_no"  : prd_detail_no,
		"draft_chk" :draft_chk,
		"draft_comment" : draft_comment

	};

	var callback = function(result) {
		if(result == '1'){
			alert('시안에 대한 사항이 처리되었습니다');
			closePopup(tempPop);
		}else{
			alert('시안정보 처리중 오류가 발생했습니다. 관리자에게 문의하세요');
		}
		return;
	}

    showMask();
    ajaxCall(url, "html", data, callback);
}


/********************************************************************
***** 페이지 로딩 후 주문내역 표시
********************************************************************/

var orderSearch = function(page){
	var url = "/ajax/mypage/order_all/load_order_all.php";
    var blank ="<tbody class='olist'  name=\"order_list\"><tr><td colspan=\"8\">검색 된 내용이 없습니다.</td></tr></tbody>";
    var data = {
    	"from"       : $("#from").val(),
    	"to"         : $("#to").val(),
    	"dvs"        : $("#dvs").val(),
    	"state"      : $("#order_state").val(),
    	"title"      : $("#title").val(),
		"page"       : page
	};

    var callback = function(data) {
		$(".olist").each(function() {
			$(this).remove();
		});

		if (data.result == 'false') {
			$("#list").after(blank);
		} else {
			$("#list").after(data.list);
			$("#page").html(data.paging);

			orderTable();
		}

		$('.from').html($("#from").val());
		$('.to').html($("#to").val());
		$('.listcnt').html(data.listcnt);

		return;
    }

    showMask();
    ajaxCall(url, "json", data, callback);
}

var orderSearch2 = function(){
    var url = "/ajax/mypage/order/load_claim_list.php";
    var blank ="<tbody name=\"claim_list\"><tr><td colspan=\"8\">검색 된 내용이 없습니다.</td></tr></tbody>";
    var data = {
    	"from"       : $("#from").val(),
    	"to"         : $("#to").val(),
    	"dvs"        : $("#dvs").val(),
    	"state"      : $("#state").val(),
    	"title"      : $("#title").val()
	};
    var callback = function(result) {
        var rs = result.split("♪");
        if (rs[0].trim() == "") {

            $("tbody[name='claim_list']").remove();
            $("#list").after(blank);
            $("#paging").html("<li><button class='on'>1</button><li>");
            $("#resultNum").html("<em>0</em>건의 검색결과가 있습니다.");
            return false;

        } else {

            $("tbody[name='claim_list']").remove();
            $("#list").after(rs[0]);
            $("#paging").html(rs[1]);
            $("#resultNum").html(rs[2]);

        }

        orderTable($('body'));
    };

    data.list_num      = listSize;
    data.page          = page;

    showMask();
    ajaxCall(url, "html", data, callback);
}


