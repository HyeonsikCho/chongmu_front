// 팝업 객체
var popupMask = null;
// calcPrice() 건너뛸 때 사용, 후공정 가격 재계산시 로직 반복타는거 방지
var passFlag = false;

$(document).ready(function() {
    // product.js에 존재함
    monoYn = '1';
    flattypYn = false;
    tmptDvs = $("#tmpt_dvs").val();

    getRealPaperAmt.exec("all");

	changePage('all');
});

/**
 * @param 인쇄방식에 해당하는 인쇄도수 검색
 *
 * @param dvs = 인쇄방식을 선택한 위치구분값
 * @param val = 인쇄방식
 */
var loadPrintTmpt = function(dvs, val) {
    var callback = function(result) {
        var dvs = loadPrintTmptCommon.dvs;

        $("#bef_tmpt_" + dvs).html(result.bef_tmpt);
        $("#bef_add_tmpt_" + dvs).html(result.bef_add_tmpt);
        $("#aft_tmpt_" + dvs).html(result.aft_tmpt);
        $("#aft_add_tmpt_" + dvs).html(result.aft_add_tmpt);

        changeData(dvs);
    };

    loadPrintTmptCommon(dvs, val, callback);
};

/**
 * @brief 수량과 페이지수에 따른 실제 종이수량 계산
 *
 * @param info = 계산용 정보데이터
 */
var calcRealPaperAmt = function(info) {
    amt      = info["amt"];
    posNum   = info["posNum"];
    pageNum  = info["pageNum"].split('!')[0];
    amtUnit  = info["amtUnit"];

    // 0page일 경우 인쇄 수량 0 반환
    if (pageNum == 0) {
        return 0;
    }

    var ret = Math.round((amt / posNum) / (2 / pageNum));
    return ret;
};

/**
 * @brief 가격 구성요소 셀렉트박스 변경시 변경된 정보로 가격 검색
 *
 * @param dvs = 데이터 변경 영역 구분값
 */
var changeData = function(dvs) {
    monoYn = $("#mono_yn").val();

    var data = {
        "cate_sortcode" : $("#cate_bot").val(),
        "stan_mpcode"   : $("#size").val(),
        //"amt"           : $("#page_inner option:selected").attr('value'),
        "amt"           : $("#amt").val(),
        "dvs"           : dvs,
        "mono_yn"       : monoYn,
        "tmpt_dvs"      : tmptDvs,
        "affil"         : $("#size").find("option:selected").attr("affil")
    };

    data.inner_paper_mpcode       = $("#paper_inner").val();
    data.inner_bef_print_name     = $("#bef_tmpt_inner").val();
    data.inner_bef_add_print_name = $("#bef_add_tmpt_inner").val();
    data.inner_aft_print_name     = $("#aft_tmpt_inner").val();
    data.inner_aft_add_print_name = $("#aft_add_tmpt_inner").val();
    data.inner_print_purp         = $("#print_purp_inner").val();
    data.inner_page_info          = $("#page_inner").val();

    loadPrdtPrice.data = data;
    loadPrdtPrice.dvs  = dvs;
    loadPrdtPrice.exec();
//alert($("#size").find("option:selected").attr("affil"));
	$('input[name="affil"]').val($("#size").find("option:selected").attr("affil"));
};

/**
 * @brief 상품 가격정보 json으로 반환
 */
var loadPrdtPrice = {
    "data"  : null,
    "dvs"   : null,
    "price" : {
        // 가격이 있는지 확인
        "inner" : false
    },
    "exec"  : function() {
        var url = "/ajax/product/load_inner_price.php";
        var callback = function(result) {
            var dvs = loadPrdtPrice.dvs;

            if (dvs === "all") {
                dvs = "inner";
            }

            loadPrdtPrice.price[dvs] = true;

            var paperPrice  = result[dvs].paper;
            var printPrice  = result[dvs].print;
            var outputPrice = result[dvs].output;
            var price = result[dvs].price;

            $("#" + dvs + "_paper_price").val(paperPrice);
            $("#" + dvs + "_print_price").val(printPrice);
            $("#" + dvs + "_output_price").val(outputPrice);
            $("#" + dvs + "_price").val(price);

            calcPrice();
        };

        ajaxCall(url, "json", loadPrdtPrice.data, callback);
    }
};

/**
 * @brief 화면에 출력되는 가격 및 빠른견적서 내용 수정
 */
var calcPrice = function() {
    // 부가세율
    var taxRate = 0.1;

    // 가격 정보
    var innerPaperPrice  = $("#inner_paper_price").val();
    var innerPrintPrice  = $("#inner_print_price").val();
    var innerOutputPrice = $("#inner_output_price").val();
    var innerPrice       = $("#inner_price").val();

    // 내지 가격
    var sellPrice = ceilVal(parseInt(innerPrice));

    // 회원등급 할인
    var gradeSale = parseFloat($("#grade_sale").attr("rate"));
    gradeSale /= 100.0;

    // 제본 가격
    var bindingPrice = $("#binding_val").attr("price");

    if (checkBlank(bindingPrice) === true) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : "제본",
        };

        getAfterPrice.load("binding", data);
        return false;
    }

    bindingPrice = parseInt(bindingPrice);

    // 옵션비 총합
    var optDefaultPrice = parseInt($("#opt_default_price").attr("price"));
    var sumOptPrice = getSumOptPrice();
    sumOptPrice += optDefaultPrice;
    sumOptPrice  = ceilVal(sumOptPrice);

    // 후공정비 총합
    var afterDefaultPrice = parseInt($("#after_default_price").attr("price"));
    var sumAfterPrice = getSumAfterPrice();
    //sumAfterPrice += bindingPrice;
    sumAfterPrice += afterDefaultPrice;
    //sumAfterPrice  = ceilVal(sumAfterPrice);

    // 견적서 종이비 계산
    var paper = parseInt(innerPaperPrice);

    // 견적서 인쇄비 계산
    var print = ceilVal(parseInt(innerPrintPrice));

    // 견적서 출력비 계산
    var output = ceilVal(parseInt(innerOutputPrice));

	// 부가세 포함가격 계산
    var tax = sellPrice * taxRate;
    var calcSellPrice = sellPrice + tax;

    // 정상 판매가 계산
    calcSellPrice += sumAfterPrice + sumOptPrice;

	calcSellPrice = Math.ceil(calcSellPrice / 100) * 100;
    var calcGradeSale = calcSellPrice * gradeSale;
    calcGradeSale = ceilVal(calcGradeSale);

	// 기본할인가 계산
    var calcSalePrice = calcSellPrice - calcGradeSale;
	var c_user_rate = parseInt($('#c_user_rate').val()) || 0;
	var c_mileage = calcSalePrice * c_user_rate * 0.01;

    // 정상 판매가 변경
    $("#sell_price").attr("val", sellPrice);
    $("#sell_price").html(calcSellPrice.format() + "원");
    // 회원등급 할인가 변경
    $("#grade_sale").html(c_mileage.format() + "마일리지");
    // 기본할인가 변경
    $("#sale_price").attr("val", calcSalePrice);
    $("#sale_price").html(calcSalePrice.format() + "원");

    // 견적서 종이비 변경
    $("#esti_paper").html(paper.format());
    // 견적서 출력비 변경
    $("#esti_output").html(output.format());
    // 견적서 인쇄비 변경
    $("#esti_print").html(print.format());
    // 견적서 후공정비 변경
    $("#esti_after").html(sumAfterPrice.format());
    // 견적서 옵션비 변경
    $("#esti_opt").html(sumOptPrice.format());
    // 견적서 합계 변경
    $("#esti_sum").html(sellPrice.format());
    // 견적서 부가세 변경
    $("#esti_tax").html(tax.format());
    // 견적서 판매가 변경
    $("#esti_sell_price").html(calcSellPrice.format());
    // 견적서 기본할인가 변경
    $("#esti_sale_price").html(calcSalePrice.format());
};

/**
 * @brief 페이지 변경시 종이수량 재계산을 위한 중간함수
 *
 * @param dvs = 바꾼 페이지 위치값
 */
var changePage = function(dvs) {
    getRealPaperAmt.exec(dvs);
    changeData(dvs);
};

/**
 * @brief 표지, 내지1/2/3 실제 종이인쇄수량 계산
 *
 * @param dvs = 바꾼 페이지 위치값
 */
var getRealPaperAmt = {
    "amt"  : {
        "inner" : 0,
    },
    "exec" : function(dvs) {
        var $amtObj = $("#amt");
        var amt     = parseFloat($amtObj.val());
        var amtUnit = $amtObj.attr("amt_unit");
        var posNum  = $("#size").attr("pos_num");

        var info = {
           "amt"      : amt,
           "posNum"   : posNum,
           "amtUnit"  : amtUnit
        };

        var pageNum = $("#page_inner").val();
        info.pageNum = pageNum;

        //this.amt.inner = calcRealPaperAmt(info);
		this.amt.inner = info['amt'];
    }
};

/**
 * @brief 즉시주문
 */
var purProduct = function() {
    $("#cart_flag").val('N');
    goCart();
};

/**
 * @brief 제본 미리보기 클릭시 팝업 출력
 */
var showBindingPop = function() {
    var url = "/ajax/product/load_preview_binding_pop.php";
    popupMask = layerPopup("l_preview_binding", url);
};

/**
 * @brief 장바구니로 이동
 */
var goCart = function(flag) {
    if ($("#il").val() === "0") {
        $("#cart_flag").val('Y');
        alert("로그인 후 확인 가능합니다.");
        return false;
    }

    if (checkBlank($("#title").val().trim()) === true) {
        $("#cart_flag").val('Y');
        alert("인쇄물제목을  입력해주세요.");
        $("#title").focus();
        return false;
    }

    var cateName  = $("#cate_bot").find("option:selected").text();
    var amtUnit   = $("#amt").attr("amt_unit");
    var paperName = $("#paper_inner").find("option:selected").text();
    var tmptName  = $("#bef_tmpt_inner").find("option:selected").text();
    var sizeName  = $("#size").find("option:selected").text();

    var innerPrice    = $("#inner_price").text();
    var basicPrice    = $("#sell_price").attr("val");
    var afterPrice    = getSumAfterPrice();
    var optPrice      = getSumOptPrice();
    var gradeSaleRate = $("#grade_sale").attr("rate");

    var ret = makeAfterInfo.all();

    if (ret === false) {
        return false;
    }

    setAddOptInfo();
	setAddAfterInfo();

    $frm = $("#frm");

    $frm.find("input[name='cate_name']").val(cateName);
    $frm.find("input[name='amt_unit']").val(amtUnit);
    $frm.find("input[name='paper_inner_name']").val(paperName);
    $frm.find("input[name='paper_inner_amt']").val(getRealPaperAmt.amt.inner);
    $frm.find("input[name='bef_tmpt_cover']").val(tmptName);
    $frm.find("input[name='size_name']").val(sizeName);

    $frm.find("input[name='prdt_price']").val(innerPrice);
    $frm.find("input[name='basic_price']").val(basicPrice);
    $frm.find("input[name='grade_sale_rate']").val(gradeSaleRate);
    $frm.find("input[name='after_price']").val(afterPrice);
    $frm.find("input[name='opt_price']").val(optPrice);
	$frm.find("input[name='direct_flag']").val(flag);

   var url = "/ajax/order/cart_insert.php";
	var data = $('#frm').formSerialize();
	var callback = function(data) {
		if(flag == 'N'){
			if(data.result == 'true'){
				if(confirm("장바구니에 상품이 담겼습니다.\n장바구니로 이동하시겠습니까?")){
					location.href='/order/cart.html';
				}else{
					return;
				}

			}else{
				alert("장바구니 처리가 올바르게 이루어지지 않았습니다. \n 관리자에게 문의해 주세요");
				return;
			}
		}else{
			if(data.result == 'true'){
				location.href = '/order/order.html?order_no='+data.order_no;
			}else{
				alert("주문이 올바르게 이루어지지 않았습니다. \n 관리자에게 문의해 주세요");
				return;
			}
		}
	};

	ajaxCall(url, "json", data, callback);
};

/******************************************************************************
 * 후공정 관련 함수
 ******************************************************************************/

/**
 * @brief 제본의 가격을 검색한다
 * 가격 배열이 null일 경우 새로 검색한다
 *
 * @param dvs = 후공정 구분값
 */
var getBindingCalcBookletPrice = function(dvs) {
    var mpcode = $("#" + dvs + "_val").val();
    var priceArr  = getAfterPrice.price[dvs];

    // 가격정보가 없을경우
    if (checkBlank(priceArr)) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : "제본",
        };

        getAfterPrice.load(dvs, data);

        return false;
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var sumPrice = getAfterSumPrice(priceArr);

    $("#" + dvs +"_val").attr("price", sumPrice);
	$("#" + dvs +"_price").val(sumPrice);
    execCalcPrice();
};

/**
 * @brief 종이 구성 변경 등으로 후공정 가격 재계산시
 * calcPrice() 함수 중복호출 되지 않도록 처리
 */
var execCalcPrice = function() {
    if (passFlag === true) {
        passFlag = false;
        return;
    }

    passFlag = false;
    calcPrice();

    return false;
}

/**
 * @brief 가격 배열에서 각 종이구분별로
 * 후공정 가격 계산해서 합산 후 반환
 *
 * @param priceArr = 가격 배열
 *
 * @return 합산된 가격
 */
var getAfterSumPrice = function(priceArr) {
    var crtrUnit = priceArr.crtr_unit;
    var amtUnit  = $("#amt").attr("amt_unit");

    // 종이수량
    var paperAmtInner = getRealPaperAmt.amt.inner;
    paperAmtInner = amtCalc(paperAmtInner, amtUnit, crtrUnit);

    // 내지 제본 가격 계산
    var priceInner = calcAfterPrice(priceArr, paperAmtInner);

    var sumPrice = priceInner;

    return sumPrice;
};

/**
 * @brief 내지 추가/삭제시 후공정 가격 재계산 함수
 */
var reCalcAfterPrice = function() {
    passFlag = true;
    getAfterPrice.common("binding");

    $("input[name='chk_after']").each(function() {
        $obj = $(this);

        if ($obj.prop("checked") === false) {
            return true;
        }

        // calcPrice() 함수 회피용
        passFlag = true;
        loadAfterPrice.exec($obj.prop("checked"), $obj.val())
    });
};
