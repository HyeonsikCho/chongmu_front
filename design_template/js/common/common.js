//새로고침 F5 막기
//document.onkeydown = processKey;
/*
function processKey() {
    if((event.ctrlKey == true &&
          (event.keyCode == 78 || event.keyCode == 82)) ||
          (event.keyCode >= 112 && event.keyCode <= 123) ||
          (event.keycode==8)) {
        event.keyCode = 0;
        event.cancelBubble = true;
        event.returnValue = false;
    }
}
*/

// html escape 대상 배열
var entityMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': '&quot;',
    "'": '&#39;',
    "/": '&#x2F;'
};

/**
 * @brief 문자열에 들어있는 escape대상 문자 변환
 *
 * @param string = 대상 문자열
 */
function escapeHtml(str) {
    return String(str).replace(/[&<>"'\/]/g, function(s) {
       return entityMap[s];
    });
}

// 숫자 타입에서 쓸 수 있도록 format() 함수 추가
Number.prototype.format = function(){
    if(this==0) return 0;

    var reg = /(^[+-]?\d+)(\d{3})/;
    var n = (this + '');

    while (reg.test(n)) n = n.replace(reg, '$1' + ',' + '$2');

    return n;
};

// 문자열 타입에서 쓸 수 있도록 format() 함수 추가
String.prototype.format = function(){
    var num = parseFloat(this);
    if( isNaN(num) ) return "0";

    return num.format();
};


//어떤 값이 공백값이거나 undefined 값이면 false 반환
var checkBlank = function(val) {
   if (val === ""
           || val === ''
           || val === null
           || typeof val === "undefined") {
       return true;
   } else {
       return false;
   }
};

// Ajax Call 공통 함수
// 사용 예제 ajaxCall('호출주소', 'html', {data:data}, callback);
var ajaxCall  = function(url, dataType, data, sucCallback) {
    if (checkBlank(url) === true) {
        return false;
    }

    $.ajax({
        type     : "POST",
        url      : url,
        dataType : dataType,
        data     : data,
        success  : function(result) {
            hideMask();
            return sucCallback(result);
        },
        error    : getAjaxError
    });
};

// Ajax Call 공통 함수
// 사용 예제 ajaxCall('호출주소', 'html', {data:data}, callback);
var ajaxCallUnhide  = function(url, dataType, data, sucCallback) {
    if (checkBlank(url) === true) {
        return false;
    }

    $.ajax({
        type     : "POST",
        url      : url,
        dataType : dataType,
        data     : data,
        success  : function(result) {
            return sucCallback(result);
        },
        error    : getAjaxError
    });
};

//Ajax error 공통 함수
var getAjaxError = function(request,status,error) {
    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
    console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
    hideBgMask();
    hideMask();
};

//로딩 중 이미지 보이기
var showMask = function() {
    showBgMask();

    $obj = $("#loading_img");
    $obj.css("position","absolute");
    $obj.css("top", Math.max(0, (($(window).height() - $obj.height()) / 2) + $(window).scrollTop()) + "px");
    $obj.css("left", Math.max(0, (($(window).width() - $obj.width()) / 2) + $(window).scrollLeft()) + "px");
    $("#loading_img").show();
}

/**
 * @brief 상품 페이지에서 셀렉트 박스 변경시 화면 이동
 *
 * @param cateSortcode = 카테고리 분류코드(대 or 중 or 소)
 */
var moveProduct = function(cateSortcode) {
    location.href = "/product/common/move_product.php?cs=" + cateSortcode;
};

//로딩 중 이미지 숨기기
var hideMask = function() {
    $("#loading_img").hide()
    hideBgMask();
}

//Background 마스크 show
var showBgMask = function() {
    var maskHeight = $(document).height();
    var maskWidth = $(window).width();

    //마스크의 높이와 너비를 화면 것으로 만들어 전체 화면을 채운다.
    $("#black_mask").css({'width':maskWidth,'height':maskHeight});
    $("#black_mask").show();
}

//Background 마스크 hide
var hideBgMask = function() {
    $("#black_mask").hide();
}

/**
 * 로그인 처리함수
 */
var login = function(el) {

    if (checkBlank($("#" + el + "id").val())) {
        alert("아이디를 입력 해주세요.");
        $("#" + el + "id").focus();
        return false;
    }

    if (checkBlank($("#" + el + "pw").val())) {
        alert("비밀번호를 입력 해주세요.");
        $("#" + el + "pw").focus();
        return false;
    }

    var url = "/common/login.php";
    var data = {
        "id" : $("#" + el + "id").val(),
        "pw" : $("#" + el + "pw").val()
    };

    var save_yn = "N";
    if ($("input:checkbox[id='id_save']").is(":checked")) {
        save_yn = "Y";
    }

    data.id_save = save_yn;

    var callback = function(result) {
        if (result === false) {
            alert("로그인에 실패했습니다.");
            location.href = "/member/login.html";
            return false;
        } else {
            location.href = "/main/main.html";
        }
    };

    ajaxCall(url, "json", data, callback);
};

/**
 * @brief 로그인
 */
var loginKey = function(event, el) {

    if (event.keyCode == 13) {
        login(el);
    }
}

/**
 * @brief 암호 입력란으로 이동
 */
var idkey = function(event, el) {
    if (event.keyCode == 13) {
        $("#" + el + "pw").focus();
    }
}

/**
 * @brief 로그아웃 처리함수
 */
var logout = function() {
    location.href = "/common/logout.php";
};

/**
 * @brief 주문 요약정보 가져옴
 *
 * @param dvs = 1주일, 해당월 구분
 */
var getOrderSummary = function(dvs) {
    var url = "/json/common/load_order_summary.php";
    var data = {
        "dvs" : dvs
    };
    var callback = function(result) {
        if (checkBlank(result.err) === false) {
            alert("로그아웃되서 메인화면으로 이동합니다.");
            location.href = "/common/logout.php";
        }

        $("#summary_wait").html(result.wait);
        $("#summary_rcpt").html(result.rcpt);
        $("#summary_prdc").html(result.prdc);
        $("#summary_rels").html(result.rels);
    };

    ajaxCall(url, "json", data, callback);
};

//검색 날짜 범위 설정
var dateSet = function(num) {

    var day = new Date();
    var time = day.getHours();
    var d_day = new Date(day - (num * 1000 * 60 * 60 * 24));
    var last = new Date(day - (365 * 1000 * 60 * 60 * 24));

    //전체 범위 검색시 날짜 범위 초기화
    if (num == "last") {
        $("#from").datepicker("setDate", last);
        $("#to").datepicker("setDate", last);
    } else if (num == "all"){
        $("#from").val("");
        $("#to").val("");
    } else {
        $("#from").datepicker("setDate", d_day);
        $("#to").datepicker("setDate", '0');
    }
};

//인풋박스 숫자만 가능
var onlyNumber = function(event) {

    event = event || window.event;

    var keyID = (event.which) ? event.which : event.keyCode;
    if (keyID == 8 || keyID == 46 || keyID == 37 || keyID == 39) {
        return;
    } else {
        event.target.value = event.target.value.replace(/[^0-9]/g, "");
    }
};

var chkMaxLength = function(obj) {

    if (obj.value.length > obj.maxLength) {
        obj.value = obj.value.slice(0, obj.maxLength);
    }
}

/**
 * @brief 원단위 반올림
 *
 * @param val = 반올림할 값
 *
 * @return 계산된 값
 */
var ceilVal = function(val) {
    val = parseFloat(val);

    val = Math.round(val * 0.01) * 100;

    return val;
};
