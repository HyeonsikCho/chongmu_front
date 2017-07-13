$(document).ready(function () {
	changeData();
});

var changeData = function() {
	setAddOptInfo();
	setAddAfterInfo();

    var data ={
        "cate_sortcode" : $("#cate_bot").val(),
        "amt"           : $("#amt").val(),
        "stan_mpcode"   : $("#size").val(),
		"paper_mpcode"   : $("#paper").val(),
		"print_name"	: $("#print").val(),
		"print_purp"    : $("#print_purp").val(),
		"sortcode"    : $("#sortcode").val(),
		"opt_name_list"	:	$("#opt_name_list").val(),
		"opt_mp_list"	:	$("#opt_mp_list").val(),
		"after_name_list"	:	$("#after_name_list").val(),
		"after_mp_list"	:	$("#after_mp_list").val()
    };

    loadPrdtPrice.data = data;
    loadPrdtPrice.exec();
};

/**
 * @brief 상품 가격정보 json으로 반환
 */
var loadPrdtPrice = {
    "data"  : {},
    "price" : {},
    "exec"  : function() {
		var url = "/test/load_price.php";
        var callback = function(result) {
            var json = result.cover;
            $.each(json, function(key, value) {
                if(key == "price") {
                    price = value + "원";
                    $('#total_price').html(price);
                } else {
                    price = value + "원";
                    $('#'+key+"_price_dd").html(price);
                }
            });
        };
        ajaxCall(url, "json", loadPrdtPrice.data, callback);
    }
};

var loadOptPrice = {
    "data" : {},
    "idx"  : null,
    "exec" : function(obj, idx, val) {
        $divObj = $("#opt_" + idx + "_div");

        if ($(obj).prop("checked") === false) {

            $divObj.slideUp(300);
            $divObj.removeClass("_on");

            calcPrice();
            return false;
        }

        $divObj.slideDown(300);
        $divObj.addClass("_on");

        if (checkBlank(this.data[idx]) === false) {
            getOptPrice(idx, null);
            return false;
        } else {
            this.data[idx] = null;
        }

        this.idx = idx;

        var url = "/test/load_price.php";
        var data = {
            "sortcode"    : $("#sortcode").val(),
            "opt_name_list"	:	$("#opt_name_list").val(),
            "opt_mp_list"	:	$("#opt_mp_list").val()
        };
        var callback = function(result) {
            loadOptPrice.data[loadOptPrice.idx] = result;
            getOptPrice(loadOptPrice.idx, null);
        };

        ajaxCall(url, "json", data, callback);
    }
};

var getOptPrice = function(idx, mpcode) {
    changeData();
};


var ajaxCall  = function(url, dataType, data, sucCallback) {
    $.ajax({
        type     : "POST",
        url      : url,
        dataType : dataType,
        data     : data,
        success  : function(result) {
            return sucCallback(result);
        }
    });
};


/**
 * @brief 추가 옵션 정보 생성
 */
var setAddOptInfo = function() {
    var id = null;
    var mpcode = "";
    var name = "";

    $("input[name='chk_opt']").each(function() {
        if ($(this).prop("checked") === false ||
                $(this).prop("disabled") === true) {
            return true;
        }

        id = $(this).attr("id");
        mpcode += $("#" + id + "_sel").val();
        mpcode += '|';

        name += $(this).val();
        name += '|';
    });

    mpcode = mpcode.substr(0, (mpcode.length - 1));
    name = name.substr(0, (name.length - 1));

    $("#opt_mp_list").val(mpcode);
    $("#opt_name_list").val(name);
};

/**
 * @brief 추가 후공정 정보 생성
 */
var setAddAfterInfo = function() {
    var name = "";
    var mpcode = "";
    var price = "";
alert("setAddAfterInfo");
    $("input[name='chk_after[]']").each(function() {
        if ($(this).prop("checked") === false ||
                $(this).prop("disabled") === true) {
            return true;
        }

        name = $(this).attr("value");

        //mpcode += $("#" + setAfterList[name] + "_val").val();
        mpcode = getImpressionMpcode();
        mpcode += '|';

        price += $("#" + setAfterList[name] + "_price").val();
        price += '|';
    });

    mpcode = mpcode.substr(0, (mpcode.length - 1));
    price = price.substr(0, (price.length - 1));

	$("#after_mp_list").val(name);
    $("#after_name_list").val(mpcode);
};


