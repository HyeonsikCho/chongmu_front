$(document).ready(function () {
    var text1 = $('header.title .location li:eq(2) span').text().replace(/\s/g, ''),
        text2 = $('header.title .location li:eq(3) span').text().replace(/\s/g, ''),
        thisText = '';

    $('nav.lnb > ul > li > a').each(function () {
        thisText = $(this).html().replace(/\s/g, '').split('<span')[0];
        if (thisText == text1) {
            $(this).closest('li').addClass('on');
            $(this).closest('li').children('ul').find('a').each(function () {
                thisText = $(this).html().replace(/\s/g, '').split('<span')[0];
                if(thisText == text2) {
                    $(this).closest('li').addClass('on');
                }
            });
        }
    });
});

var listCnt = "";
var order_seqno = "";
var memo = "";
var modalMask = "";
var pageName = "";

/**
 * @brief 선택조건으로 검색 클릭시
 */
var orderSearch = function(listSize, page, type) {

    pageName = type;

    var url = "/ajax/mypage/order_all/load_order_list.php";
    var blank = "";
    if (pageName == "unpaid" || pageName == "reorder" || pageName == "draft") {

        blank = "<tbody name=\"order_list\"><tr><td colspan=\"10\">검색 된 내용이 없습니다.</td></tr></tbody>";
        
    } else {

        blank = "<tbody name=\"order_list\"><tr><td colspan=\"9\">검색 된 내용이 없습니다.</td></tr></tbody>";

    }
    var data = {
    	"from"       : $("#from").val(),
    	"to"         : $("#to").val(),
    	"dlvr_way"   : $("#dlvr_way").val(),
    	"state"      : $("#order_state").val(),
    	"title"      : $("#title").val(),
        "type"       : type
	};
    var callback = function(result) {
        var rs = result.split("♪");
        if (rs[0].trim() == "") {

            $("tbody[name='order_list']").remove();
            $("#list").after(blank);
            $("#paging").html("<li><button class='on'>1</button><li>");
            $("#resultNum").html("<em>0</em>건의 검색결과가 있습니다.");
            return false;

        } else {

            $("tbody[name='order_list']").remove();
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

/**
* @brief 주문 취소
*/
var cancleOrder = function() {

    var url = "/proc/mypage/order_all/proc_order_cancle.php";
    var data = {
    	"seqno"      : order_seqno
	};
    var callback = function(result) {
        if (result.trim() == 1) {

            alert("주문취소 되었습니다.");
            closePopup(modalMask);
            orderSearch(listCnt, 1, pageName);

        } else if (result.trim() == 2){

            alert("이미 취소된 주문입니다.");

        } else if (result.trim() == 3){

            alert("접수 이후에는 취소할수 없습니다.");

        } else {

            alert("주문취소에 실패했습니다.");

	    }
    };

    showMask();
    ajaxCall(url, "html", data, callback);

}

/**
* @brief 보여줄 페이지 수 설정
*/
var changeOrderListNum = function(val) {
    listCnt = val;
    orderSearch(listCnt, 1, pageName);
}

/**
* @brief 페이지 이동
*/
var moveOrderPage = function(val) {

    orderSearch(listCnt, val, pageName);
}

/**
* @brief 조건 검색
*/
var orderSearchKey = function(event) {
    if(event.keyCode != 13) {
        return false;
    }
    orderSearch(listCnt, 1, pageName);
}

/**
* @brief 조건 검색
*/
var orderSearchTxt = function() {
    orderSearch(listCnt, 1, pageName);
}

/**
* @brief 재주문
*/
var reorder = function() {

    var url = "/proc/mypage/order_all/proc_reorder.php";
    var data = {
    	"seqno"      : order_seqno
	};
    var callback = function(result) {
        var tmp = result.split('♪♭§');
	if (tmp[0].trim() == 1) {

	        alert("[" + tmp[1] + "]을 복사하여 상품을 장바구니에 담았습니다.");
	        closePopup(modalMask);
    	    orderSearch(listCnt, 1, pageName);

        } else {

           alert("재주문에 실패했습니다.");

       }
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}	

/**
* @brief 클레임요청
*/
var reqClaim = function() {

    alert("클레임 미개발");
}

/**
* @brief 주문메모 LOAD
*/
var loadOrderMemo = function(seq) {

    order_seqno = seq;

    var url = "/ajax/mypage/order_all/load_order_memo.php";
    var data = {
    	"order_seqno"      : order_seqno
	};
    var callback = function(result) {

    	orderPopup('l_memo', '/design_template/mypage/popup/l_memo.html', order_seqno, 'memo');
	    memo = result;
    };

    showMask();
    ajaxCall(url, "html", data, callback);

}

/**
* @brief 주문메모 UPDATE
*/
var updateOrderMemo = function() {

    var url = "/proc/mypage/order_all/proc_order_memo.php";
    var data = {
    	    "order_seqno"      : order_seqno,
     	    "memo"             : $("#order_memo").val()
	};
    var callback = function(result) {

	if (result.trim() == 1) {

	    alert("저장했습니다.");
	    closePopup(modalMask);

        } else {

           alert("저장에 실패했습니다.");

       }
    };

    showMask();
    ajaxCall(url, "html", data, callback);

}

/**
* @brief 주문 팝업
*/
function orderPopup (code, html, seq, type) {

    order_seqno = seq;

    layerPopupHTML = '<div class="modalMask ' + code +' _num' + layerPopNum + '"><div class="loading">불러오는 중입니다.</div><div class="layerPopupWrap"><section class="layerPopup"></section></div></div>';
    $('body').append(layerPopupHTML);
    
    modalMask =  $('.modalMask._num' + layerPopNum),
        contents = modalMask.find('.layerPopup'),
        contentsWrap = modalMask.find('.layerPopupWrap');
    
    layerPopNum += 1;
    
    modalMask.fadeIn(300, function () {
        $.ajax({
            url: html,
            dataType: "html"
        }).done(function (data) {
            insertData(data);
        }).fail(function () {
            var data = '<header><h2>오류</h2><button class="close" title="닫기"><img src="../../images/common/btn_circle_x_white.png" alt="X"></button></header><article class="error"><em>오류가 있습니다.</em><br>잠시 후 다시 실행하시거나 관리자에게 문의하세요.</article>';
            insertData(data);
        });
    });
    
    function insertData (data) {
        contents.html(data);

        //popup position setting
        contentsWrap.css({
            'top' : modalMask.height() > contentsWrap.height() ? (modalMask.height() - contentsWrap.height()) / 2 + 'px' : 0,
            'left' : modalMask.width() > contentsWrap.width() ? (modalMask.width() - contentsWrap.width()) / 2 + 'px' : 0
        });

        if (modalMask.outerHeight() > contentsWrap.height() && modalMask.outerWidth() > contentsWrap.width()) {
            //drag
            contentsWrap.draggable({
                addClasses: false,
                cursor: false,
                containment: modalMask,
                handle: 'header'
            });
        } else {
            $('body').css('overflow', 'hidden');
        }
        
        modalMask.addClass('_on')
            .find('button.close').on('click', function () { closePopup(modalMask); });

	if (type == "memo") {

	    $("#order_memo").val(memo);
	}

        //readonly prompt blur
        modalMask.find('input[type=text]').on('focus click', function () {
            readOnlyPromptBlur($(this));
        });

        //general checkbox
        generalCheckbox(modalMask);

        //table sorting
        tableSorting(modalMask);
    }
}


