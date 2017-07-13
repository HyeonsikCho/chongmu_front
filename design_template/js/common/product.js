// 상품 페이지 전체에서 사용되는 독판여부
// 중구난방 퍼져있어서 이걸로 통합해서 처리
var monoYn = null;
// 낱장형 여부
var flattypYn = null;

/**
 * @brief 비규격 사이즈 입력시 자리수 재계산
 * product_design.js에서 호출한다
 */
var calcManuPosNum = {
    "defWid"  : 0,
    "defVert" : 0,
    "maxWid"  : 0,
    "maxVert" : 0,
    "exec"    : function() {
        if($("#size_dvs").val() == "manu") {
            var w = parseFloat($("#cut_wid_size").val());
            var h = parseFloat($("#cut_vert_size").val());
            var calW = Math.ceil(w / this.defWid) * Math.ceil(h / this.defVert);
            var calH = Math.ceil(h / this.defWid) * Math.ceil(w / this.defVert);

            if (calW > calH) {
                $('#manu_pos_num').val(calH);
            } else {
                $('#manu_pos_num').val(calW);
            }
        } else {
            $('#manu_pos_num').val(1);
        }

        changeData();
    }
};
/**
 * @brief 상품 페이지에서 셀렉트 박스 변경시 화면 이동
 *
 * @param cateSortcode = 카테고리 분류코드(대 or 중 or 소)

var moveProduct = function(cateSortcode) {
    var sortcodeLength = cateSortcode.length;

    if (sortcodeLength === 3) {
        cateSortcode += "001001";
    } else if (sortcodeLength === 6) {
        cateSortcode += "001";
    }

    location.href = "/product/common/move_product.php?cs=" + cateSortcode;
};
*/

/**
 * @brief 지질느낌 검색
 *
 * @param dvs    = changeData(). callback()에서 쓰일 구분값
 * @param mpcode = 종이 맵핑코드
 */
var loadPaperDscr = {
    "obj"  : null,
    "exec" : function(dvs, mpcode) {
        this.obj = "#paper_sense_" + dvs;

        var url = "/ajax/product/load_paper_dscr.php";
        var data = {
            "mpcode" : mpcode
        };
        var callback = function(result) {
            $(loadPaperDscr.obj).html(result);
		//리얼종이계산횟수 추가 ㅠㅠ
		getRealPaperAmt.exec();
        };

        ajaxCall(url, "text", data, callback);

        changeData(dvs, mpcode);
    }
}

/**
 * @brief 상품 가격정보 json으로 반환
 */
var loadPrdtPrice = {
    "data"  : {},
    "price" : {},
    "exec"  : function() {
        var url = "/test/load_price.php";
        var callback = function(result) {
            loadPrdtPrice.price = result.cover;
            var json = result.cover;
            $.each(json, function(key, value) {
                if(key == "price") {
                    price = value + "원";
                    $('#sale_price').html(price);
                } else {
                    price = value.format() + "원";
                    $('#'+key+"_price_dd").html(price);

                    if(key == "foil") {
                        aft_mpcode = result.cover.foil_aft_mpcode;
                        bef_mpcode = result.cover.foil_bef_mpcode;

                        $("#foil_val_1").val(bef_mpcode);
                        $("#foil_val_2").val(aft_mpcode);
                    }
                }
            });
            calcPrice();
        };
        ajaxCall(url, "json", loadPrdtPrice.data, callback);
        //calcPrice();
    }
};

/**
 * @brief 옵션 가격 검색
 *
 * @param obj = 체크확인용 객체
 * @param val = 옵션 맵핑코드
 */
var loadOptPrice = {
    "data" : {},
    "idx"  : null,
    "exec" : function(obj, idx, val) {
        $divObj = $("#opt_" + idx + "_div");
        if ($(obj).prop("checked") === false) {

            $divObj.slideUp(300);
            $divObj.removeClass("_on");

            changeData();
            return false;
        }

        $divObj.slideDown(300);
        $divObj.addClass("_on");

        if (checkBlank(this.data[idx]) === false) {
            getOptPrice(idx);
            return false;
        } else {
            this.data[idx] = null;
        }

        this.idx = idx;
        setAddOptInfo();

        if($("#opt_"+idx).attr("price") != "") {
            changeData();
            return;
        }

        var url = "/test/load_price.php";
        var data = {
            "sortcode"    : $("#sortcode").val(),
            "opt_name_list"	:	$("#opt_name_list").val(),
            "opt_mp_list"	:	$("#opt_mp_list").val()
        };
        var callback = function(result) {
            var price = result.cover.price;
            $("#opt_" + idx).attr("price", price);
            $("#opt_" + idx + "_price").html(price.format() + '원');
            changeData();
        };

        ajaxCall(url, "json", data, callback);
    }
};

/**
 * @brief 수량에 해당하는 옵션가격 검색
 *
 * @param idx = 옵션 위치구분값
 * @param mpcode = 개별선택시 넘어오는 맵핑코드값
 */
var getOptPrice = function(idx) {
    var $amtObj  = $("#amt");
    var amt      = parseFloat($amtObj.val());
    var amtUnit  = $amtObj.attr("amt_unit");

    // 상품수럄이 장, 매 이면 수량/500
    amt = amtCalc(amt, amtUnit, 'R');

    var optPrice = 0;

    /*
    //! 수량관련 처리필요함
    for (var optAmt in priceArr) {
        var price = priceArr[optAmt];
        optAmt = parseFloat(optAmt);

        if (amt <= optAmt) {
            optPrice = price;
            break;
        }

        maxPrice = price;
    } */

    optPrice = parseInt(optPrice);

    $("#opt_" + idx).attr("price", optPrice);
    $("#opt_" + idx + "_price").html(optPrice.format() + '원');

    calcPrice();
};

/**
 * @brief 상품 수량과 후공정/옵션의 기준단위를 비교해서
 * 값을 통일시키는 함수
 *
 * @param amt      = 상품수량
 * @param amtUnit  = 수량단위
 * @param crtrUnit = 후공정/옵션 기준단위
 */
var amtCalc = function(amt, amtUnit, crtrUnit) {
    if ((amtUnit !== "연" && amtUnit !== "R") && crtrUnit === "R") {
        amt /= 500.0;
    }

    return amt;
};

/**
 * @brief 옵션 가격 검색
 *
 * @param obj = 체크확인용 객체
 * @param val = 옵션 맵핑코드
 */
var loadAfterPrice = {
    "data" : {},
    "idx"  : null,
    "exec" : function(obj, idx, val) {
        $divObj = $("#after_" + idx + "_div");
        if ($(obj).prop("checked") === false) {

            $divObj.slideUp(300);
            $divObj.removeClass("_on");

            //changeData();
            return false;
        }

        $divObj.slideDown(300);
        $divObj.addClass("_on");

        if (checkBlank(this.data[idx]) === false) {
            //getAfterPrice(idx);
            return false;
        } else {
            this.data[idx] = null;
        }

        this.idx = idx;
        setAddAfterInfo();

        if($("#after_"+idx).attr("price") != "") {
            //changeData();
            return;
        }

        var url = "/test/load_price.php";
        var data = {
            "sortcode"    : $("#sortcode").val(),
            "after_name_list"	:	$("#after_name_list").val(),
            "after_mp_list"	:	$("#after_mp_list").val()
        };
        var callback = function(result) {
            var price = result.cover.price;
            $("#after_" + idx).attr("price", price);
            $("#after_" + idx + "_price").html(price.format() + '원');
            changeData();
        };

        ajaxCall(url, "json", data, callback);
    }
};

/**
 * @brief 후공정 가격 배열에서 해당하는 수량의 가격 검색해서
 * 가격 계산 후 반환
 *
 * @param priceArr = 가격 배열
 * @param amt      = 수량
 *
 * @return 가격
 */
var calcAfterPrice = function(priceArr, amt) {
    var afterPrice = 0;
    var afterAmt   = 0;
    // 배열 넘어갈 경우
    var maxPrice = 0;
    for (afterAmt in priceArr) {
        if (afterAmt === "crtr_unit") {
            continue;
        }
        afterAmt = parseFloat(afterAmt);

        var price = priceArr[afterAmt];

        if (amt <= afterAmt) {
            afterPrice = price;
            break;
        }

        maxPrice = price;
    }
    if (afterPrice === 0) {
        afterPrice = maxPrice;
        afterAmt   = amt;
    }

    afterAmt = parseFloat(afterAmt);
    afterPrice = parseInt(afterPrice);
    return afterPrice;
};


/**
 * @param 인쇄방식에 해당하는 인쇄도수 검색
 *
 * @param dvs = 인쇄방식을 선택한 위치구분값
 * @param val = 인쇄방식
 * @param callback = ajax callback 함수
 */
var loadPrintTmptCommon = {
    "dvs"  : null,
    "exec" : function(dvs, val, callback) {
        this.dvs = dvs;

        var url = "/ajax/product/load_print_tmpt.php";
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "val"           : val
        };

        ajaxCall(url, "json", data, callback);
    }
};

/**
 * @brief 후공정 가격 합산해서 반환
 *
 * @return 합산된 후공정 가격
 */
var getSumAfterPrice = function() {
    var ret = 0;
    var dvs = null;
    var temp = null;

    $("input[name='chk_after[]']").each(function() {
        if ($(this).prop("checked") === false) {
            return true;
        }

        dvs = $(this).attr("id").split('_')[1];
        temp = $("#" + dvs + "_price").val();
        if (checkBlank(temp) === false) {
            temp = parseInt(temp);
            ret += temp;
        }
    });

    return ret;
};

/**
 * @brief 옵션 가격 합산해서 반환
 *
 * @return 합산된 옵션 가격
 */
var getSumOptPrice = function() {
    var ret = 0;
    var temp = null;

    $("input[name='chk_opt']").each(function() {
        if ($(this).prop("checked") === false) {
            return true;
        }

        temp = $(this).attr("price");
        if (checkBlank(temp) === false) {
            temp = parseInt(temp);
            ret += temp;
        }
    });

    return ret;
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

    $("input[name='chk_after[]']").each(function() {
        if ($(this).prop("checked") === false ||
            $(this).prop("disabled") === true) {
            return true;
        }

        tmp_name = $(this).attr("value");
        name += tmp_name;
        name += '|';

        mpcode += getMpcode(setAfterList[tmp_name]);
        mpcode += '|';

        price += $("#" + setAfterList[tmp_name] + "_price").val();
        price += '|';
    });

    name = name.substr(0, (name.length - 1));
    mpcode = mpcode.substr(0, (mpcode.length - 1));
    price = price.substr(0, (price.length - 1));

    $("#after_mp_list").val(mpcode);
    $("#after_name_list").val(name);
};

/**
 * @brief 추가 후공정 정보 생성
 */
var setAddDepthInfo = function() {
    var depth1 = "";
    var depth2 = "";
    var depth3 = "";
    var paper_depth = "";

    $("input[name='chk_after[]']").each(function() {
        if ($(this).prop("checked") === false ||
            $(this).prop("disabled") === true) {
            return true;
        }
        tmp_name = $(this).attr("value");

        depth1 += getDepth(setAfterList[tmp_name], '1');
        depth1 += '|';

        depth2 += getDepth(setAfterList[tmp_name], '2');
        depth2 += '|';

        depth3 += getDepth(setAfterList[tmp_name], '3');
        depth3 += '|';
    });

    depth1 = depth1.substr(0, (depth1.length - 1));
    depth2 = depth2.substr(0, (depth2.length - 1));
    depth3 = depth3.substr(0, (depth3.length - 1));
    //paper_depth = $("#cut_wid_size").val() + "," + $("#cut_vert_size").val();

    $("#depth1_list").val(depth1);
    $("#depth2_list").val(depth2);
    $("#depth3_list").val(depth3);
    //$("#paper_depth").val(paper_depth);
};

var addTax = function(orgPrice) {
    return orgPrice + Math.ceil(orgPrice*0.1);
}


/**
 * @brief 견적서 엑셀 다운로드
 */
var downEstiExcel = function() {
    var url = "/ajax/product/make_esti_excel.php";
    var callback = function(result) {
        var downUrl = "/common/down_esti_excel.php?filename=" + result;
        $("#file_ifr").attr("src", downUrl);
    };

    ajaxCall(url, "html", makeEstiPopInfo.data, callback);
};

/**
 * @brief 견적서 출력 팝업 출력
 */
var showEstiPop = function() {
    var $modalMask =  $(".modalMask.l_estimates");
    var $contentsWrap = $modalMask.find('.layerPopupWrap');

    if ($modalMask.outerHeight() > $contentsWrap.height() &&
        $modalMask.outerWidth() > $contentsWrap.width()) {
        //drag
        $contentsWrap.draggable({
            addClasses  : false,
            cursor      : false,
            containment : $modalMask,
            handle      : "header"
        });
    } else {
        $("body").css("overflow", "hidden");
    }

    $modalMask.fadeIn(300, function () {
        $contentsWrap.css({
            'top' : $(window).height() > $contentsWrap.height() ?
            ($(window).height() - $contentsWrap.height()) / 2 + 'px' : 0,
            'left' : $modalMask.width() > $contentsWrap.width() ?
            ($modalMask.width() - $contentsWrap.width()) / 2 + 'px' : 0
        });

        makeEstiPopInfo.exec("pop");

        orderTable($modalMask);

        var hideFunc = function() {
            $modalMask.fadeOut(300, function() {
                $("body").css("overflow", "auto");
            });
        };

        $modalMask.addClass("_on")
            .find("button.close")
            .on("click", hideFunc);
    });
};

/**
 * @breif 견적서 팝업 공통부분 생성
 *
 */
var getEstiPopHtml = function(data) {

    var url = "/ajax/product/load_estimate_pop.php";
    var callback = function(result) {
        $("#esti_cont").html(result);
    };

    ajaxCall(url, "html", data, callback);
};

/**
 * @brief 견적서 출력 > 이메일 발송 팝업 출력
 */
var showEmailPop = function() {
    var url = "/ajax/product/load_email_pop.php";
    emailPop = layerPopup("l_email", url);
};

/**
 * @brief 견적서 팝업에서 이메일 발송 클릭시
 */
var sendEmail = function() {
    var url = "/ajax/product/send_email.php";
    var data = makeEstiPopInfo.data;
    data.email_dvs = $("input[name='emailAddressType']:checked").val();
    data.m_acc = $("#m_acc").val();
    data.m_dom = $("#m_dom").val();
    data.d_acc = $("#d_acc").val();
    data.d_dom = $("#d_dom").val();
    var callback = function(result) {
        closePopup(emailPop);
        emailPop = null;

        if (result === 'F') {
            return alertReturnFalse("이메일 전송에 실패했습니다.");
        }

        return alertReturnFalse("이메일 전송에 성공했습니다.");
        hideMask();
    };

    showMask();
    ajaxCall(url, "text", data, callback);
};

var changeData = function() {
    setAddOptInfo();
    setAddAfterInfo();
    setAddDepthInfo();

    var data ={
        "amt"           : $("#amt").val(),
        "count"         : $("#count").val(),
        "manu_pos_num"  : $("#manu_pos_num").val(),
        "stan_mpcode"   : $("#size").val(),
        "paper_mpcode"  : $("#paper_cover").val(),
        "print_name"	: $("#bef_tmpt_cover").val(),
        "print_purp"    : $("#print_purp").val(),
        "sortcode"      : $("#sortcode").val(),
        "opt_name_list"	:	$("#opt_name_list").val(),
        "opt_mp_list"	:	$("#opt_mp_list").val(),
        "after_name_list"	:	$("#after_name_list").val(),
        "after_mp_list"	:	$("#after_mp_list").val(),
        "paper_depth"   :   $("#paper_depth").val(),
        "depth1"	    :	$("#depth1_list").val(),
        "depth2"	    :	$("#depth2_list").val(),
        "depth3"	    :	$("#depth3_list").val()
    };

    loadPrdtPrice.data = data;
    loadPrdtPrice.exec();
};

function post_to_url(path, params, method) {
    method = method || "post"; // Set method to post by default, if not specified.
    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);
    for(var key in params) {
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", key);
        hiddenField.setAttribute("value", params[key]);
        form.appendChild(hiddenField);
    }
    document.body.appendChild(form);
    form.submit();
}

function changePaper() {
    changeData();
}

function setAmt() {
    changeData();
}