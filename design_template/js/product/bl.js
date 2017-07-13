var tmptDvs   = null;
var monoYn    = null;
var flattypYn = true;
var prdtDvs   = null;
var sortcode  = null;
var cateName  = null;

$(document).ready(function() {

    // 건수 초기화
    var option = "";
    for (var i = 1; i <= 99; i++) {
        option += "<option value=\"" + i + "\">" + i + "</option>";
    }
    $("#count").html(option);

    calcManuPosNum.defWid  = parseFloat($("#size").attr("def_cut_wid"));
    calcManuPosNum.defVert = parseFloat($("#size").attr("def_cut_vert"));

    monoYn   = $("#mono_yn").val();
    tmptDvs  = $("#tmpt_dvs").val();
    prdtDvs  = $("#prdt_dvs").val();
    sortcode = $("#sortcode").val();
    cateName = $("#sortcode").find("option:selected").text();
    amtUnit  = $("#amt").attr("amt_unit");

    // product.js에 존재함
    monoYn = '0';
    flattypYn = true;
	resetCheckbox(); //장바구니 이동후 뒤로가기버튼 선택시 히스토리 기록으로 인한 체크박스 선택초기화
    //getRealPaperAmt.exec();

    orderSummary();
    changePaper();
});

/**
 * @brief 상품 가격정보 json으로 반환
 */

    /*
var loadPrdtPrice = {
    "data"  : {},
    "price" : {},
    "exec"  : function() {
        var url = "/test/load_price.php";
        var callback = function(result) {
            loadPrdtPrice.price = result.cover;
            calcPrice();
        };
        ajaxCall(url, "json", loadPrdtPrice.data, callback);
        //calcPrice();
    }
};
*/
/**
 * @brief 화면에 출력되는 가격 및 빠른견적서 내용 수정
 */


var calcPrice = function() {
    // 부가세율
    //var taxRate = 0.1;

    // 자리수
    var posNum = 1;
    if ($("#size_dvs").val() === "manu") {
        posNum = parseFloat($("#manu_pos_num").val());
    }
    // 건수
    var count = parseInt($("#count").val());
    // 정상판매가
    var sellPrice = loadPrdtPrice.price.price;
    if (checkBlank(sellPrice)) {
        changeData();
        return false;
    }
    //2015-04-23, hscho, cate_price 변경
    $("#cate_price").attr("value", sellPrice);

    //sellPrice  = ceilVal(sellPrice);
    //sellPrice *= posNum;
    // 등급 할인율
    var gradeSale = parseFloat($("#grade_sale").attr("rate"));
    gradeSale /= 100.0;
    // 옵션비 총합
    var sumOptPrice = getSumOptPrice();
    sumOptPrice = Math.ceil(sumOptPrice);
    // 후공정비 총합
    var sumAfterPrice = getSumAfterPrice();
    sumAfterPrice = ceilVal(sumAfterPrice);

    // 견적서 종이비 계산
    var paper  = 0;
    if (monoYn === '1') {
        paper  = parseInt(loadPrdtPrice.price.paper);
        paper  = ceilVal(paper);
        paper *= count;
        $(".esti_paper_info").css("display", "");
    } else {
        $(".esti_paper_info").css("display", "none");
    }

    // 견적서 출력비 계산
    var output = 0;
    if (monoYn === '1') {
        output  = parseInt(loadPrdtPrice.price.output);
        output  = ceilVal(output);
        output *= count;
        $(".esti_output_info").css("display", "");
    } else {
        $(".esti_output_info").css("display", "none");
    }

    // 견적서 인쇄비 계산
    var print  = sellPrice;
    if (monoYn === '1') {
        print  = parseInt(loadPrdtPrice.price.print);
        print  = ceilVal(print);
    }
    //print *= count;

    // 견적서 후공정비 계산
    var after = sumAfterPrice * count;
    // 견적서 옵션비 계산
    var opt = sumOptPrice;
	//var opt = sumOptPrice;
    // 견적서 합계 계산
    var sum = print;
    //var taxedsum = paper + output + taxprint + after + opt
    // 견적서 부가세 계산
    //var tax = ceilVal(sum * taxRate);
    // 정상판매가 계산
    var calcSellPrice = sum; // 중요
    // 회원등급 할인가 계산
   // console.log("!! : " + gradeSale);
    var calcGradeSale = calcSellPrice * gradeSale;
   // console.log("@@ : " + calcGradeSale);
    calcGradeSale = ceilVal(calcGradeSale);
	calcSellPrice = Math.ceil(calcSellPrice / 100) * 100;
  //  console.log("## : " + calcGradeSale);
    // 기본할인가 계산
    var calcSalePrice = calcSellPrice - calcGradeSale;
	var c_user_rate = parseInt($('#c_user_rate').val()) || 0;
	var c_mileage = calcSellPrice * c_user_rate * 0.01;
    // 정상판매가 변경
    $("#sell_price").attr("val", calcSellPrice);
	$("#default_price").attr("value", paper + output + print);   ///////////세금 안붙은값으로 프린트에 세금안붙은 종이값
    $("#basic_price").attr("value", print);
    $("#sell_price").html(calcSellPrice.format() + '원');
    // 회원등급 할인가 변경
    $("#grade_sale").html(c_mileage.format() + ' 마일리지');
  //포인트 설정
    // 기본할인가 변경
    $("#sale_price").html(calcSalePrice.format() + '원');

    // 견적서 종이비 변경
    $("#esti_paper").html(paper.format());
    // 견적서 출력비 변경
    $("#esti_output").html(output.format());
    // 견적서 인쇄비 변경
    $("#esti_print").html(print.format());
    // 견적서 후공정비 변경
    $("#esti_after").html(after.format());
    // 견적서 옵션비 변경
    $("#esti_opt").html(opt.format());
    // 견적서 건수 변경
    $("#esti_count").html(count.format());
    // 견적서 합계 변경
    $("#esti_sum").html(sum.format());
    // 견적서 부가세 변경
    //$("#esti_tax").html(tax.format());
    // 견적서 판매가 변경
    $("#esti_sell_price").html(calcSellPrice.format());
    // 견적서 기본할인가 변경
    $("#esti_sale_price").html(calcSalePrice.format());
};

/**
 * @param 인쇄방식에 해당하는 인쇄도수 검색
 *
 * @param val = 인쇄방식
 */
var loadPrintTmpt = function(dvs, val) {
    var callback = function(result) {
        $("#print_tmpt").html(result.bef_tmpt);

        if (monoYn === '1') {
            changeData();
        }
    };

    loadPrintTmptCommon(null, val, callback);
};

/**
 * @brief 인쇄유형 변경시 전역변수 변경처리
 *
 * @param val = 인쇄유형 값
 */
var reCalcRealPaperAmt = function() {
    changeData();
}

/**
 * @brief 수량에 따른 종이 실제수량 계산
 */
var getRealPaperAmt = {
    "amt"  : 0,
    "exec" : function() {
        var url  = "/ajax/product/load_sheet_real_paper_amt.php";
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "size_dvs"      : $("#size_dvs").val(),
            "size_name"     : $("#size > option:selected").html(),
            "manu_pos_num"  : $("#manu_pos_num").val(),
            "amt"           : $("#amt").val(),
            "amt_unit"      : $("#amt").attr("amt_unit"),
        };
        var callback = function(result) {

            getRealPaperAmt.amt = parseFloat(result);
        };

        ajaxCall(url, "text", data, callback);
    }
}

/**
 * @brief 비규격 사이즈 선택할 경우 재단사이즈 값 초기화
 *
 * @param val = 구분값
 */
var changeSizeDvs = function(val) {
    // 비규격 사이즈 선택시 기본 사이즈로 데이터 변경
    if (val === "manu") {
        $("#cut_wid_size").val($("#size").attr("def_cut_wid"));
        $("#cut_vert_size").val($("#size").attr("def_cut_vert"));
        //$("#cut_wid_size").val($("#bl_size").attr("def_cut_wid"));
        //$("#cut_vert_size").val($("#bl_size").attr("def_cut_vert"));
    } else {
        $("#manu_pos_num").val("1");
    }
    changeData();
};

/******************************************************************************
 * 후공정 관련 함수
 ******************************************************************************/

/**
 * @brief 코팅 확정형 가격 검색
 *
 * @param dvs = 후공정 구분값
 */
var getCoatingPlySheetPrice = function(dvs) {
    var mpcode = $("#" + dvs + "_val").val();
	//console.log(dvs);
    var priceArr  = getAfterPrice.price[dvs];

    if (checkBlank(priceArr)) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : "코팅",
        };

        getAfterPrice.load(dvs, data);
        return false;
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var afterPrice = getAfterCalcPrice(priceArr);

    setAfterPrice(dvs, afterPrice);

    calcPrice();
};

/**
 * @brief 귀도리 계산형 가격 검색
 *
 * @param dvs = 후공정 구분값
 */
var getRoundingCalcSheetPrice = function(dvs) {
    var mpcode = $("#" + dvs + "_val").val();
    var priceArr  = getAfterPrice.price[dvs];

    if (checkBlank(priceArr)) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : "귀도리",
        };

        getAfterPrice.load(dvs, data);
        return false;
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var afterPrice = getAfterCalcPrice(priceArr);

    setAfterPrice(dvs, afterPrice);

    calcPrice();
};

/**
 * @brief 코팅 계산형 가격 검색
 *
 * @param dvs = 후공정 구분값
 */
var getCoatingCalcSheetPrice = function(dvs) {
    var mpcode = $("#" + dvs + "_val").val();
    var priceArr  = getAfterPrice.price[dvs];

    if (checkBlank(priceArr)) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : "코팅",
        };

        getAfterPrice.load(dvs, data);
        return false;
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var afterPrice = getAfterCalcPrice(priceArr);

    setAfterPrice(dvs, afterPrice);

    calcPrice();
};

/**
 * @brief 귀도리 확정형 가격 검색
 *
 * @param dvs = 후공정 구분값
 */
var getRoundingPlySheetPrice = function(dvs) {
    var mpcode = $("#" + dvs + "_val").val();
    var priceArr  = getAfterPrice.price[dvs];

    if (checkBlank(priceArr)) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : "귀도리",
        };

        getAfterPrice.load(dvs, data);
        return false;
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var afterPrice = getAfterCalcPrice(priceArr);

    setAfterPrice(dvs, afterPrice);

    calcPrice();
};
/**
 * @brief 재단 확정형 가격 검색
 *
 * @param dvs = 후공정 구분값
 */
var getCuttingPlySheetPrice = function(dvs) {
    var mpcode = $("#" + dvs + "_val").val();
    var priceArr  = getAfterPrice.price[dvs];

    if (checkBlank(priceArr)) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : "재단",
        };

        getAfterPrice.load(dvs, data);
        return false;
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var afterPrice = getAfterCalcPrice(priceArr);

    setAfterPrice(dvs, afterPrice);

    calcPrice();
};

/**
 * @brief 가격 배열에서 후공정 가격 계산해서 반환
 *
 * @param priceArr = 가격 배열
 *
 * @return 계산된 가격
 */
var getAfterCalcPrice = function(priceArr) {
    var crtrUnit = priceArr.crtr_unit;
    var amtUnit  = $("#amt").attr("amt_unit");
//	console.log(amtUnit);
    // 표지 종이수량
    var amt = getRealPaperAmt.amt;
//	console.log(amt);
    amt = amtCalc(amt, amtUnit, crtrUnit);
   // return calcAfterPrice(priceArr, amt);
   return calcAfterPrice(priceArr, $("#amt").val());
};



/**
 * @brief 장바구니로 이동
 */
var goCart = function(flag) {

    if ($("#il").val() === "0") {
        alert("로그인 후 확인 가능합니다.");
		$("#frm").find("input[name='title']").focus();
        return false;
    }
	if($("#frm").find("input[name='title']").val() == ''){
		alert('인쇄물 제목을 입력해주세요');
		return;
	}
    var cateName  = $("#cate_bot").find("option:selected").text();
    var amtUnit   = $("#amt").attr("amt_unit");
    var paperName = $("#paper").find("option:selected").text();
    var tmptName  = $("#print_tmpt").find("option:selected").text();
    var sizeName  = $("#size").find("option:selected").text();

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
    $("#frm").find("input[name='cate_name']").val(cateName);
    $("#frm").find("input[name='amt_unit']").val(amtUnit);
    $("#frm").find("input[name='paper_cover_amt']").val(getRealPaperAmt.amt);
    $("#frm").find("input[name='paper_cover_name']").val(paperName);
    $("#frm").find("input[name='bef_tmpt_cover_name']").val(tmptName);
    $("#frm").find("input[name='size_name']").val(sizeName);

    $("#frm").find("input[name='basic_price']").val(basicPrice.format());
    $("#frm").find("input[name='grade_sale_rate']").val(gradeSaleRate);
    $("#frm").find("input[name='after_price']").val(afterPrice);
    $("#frm").find("input[name='opt_price']").val(optPrice);
	$("#frm").find("input[name='direct_flag']").val(flag);
//	console.log($('#frm').formSerialize());
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
                post_to_url('/order/order.html', {'order_no' : data.order_no, 'cart_prdlist_id' : data.cart_prdlist_id});
			}else{
				alert("주문이 올바르게 이루어지지 않았습니다. \n 관리자에게 문의해 주세요");
				return;
			}
		}
	};

	ajaxCall(url, "json", data, callback);
  //  $("#frm").submit();
};

/**
 * @brief 견적서 팝업 본문정보 생성
 */
var makeEstiPopInfo = {
    "data" : null,
    "exec" : function(dvs) {
        //var prefix = getPrefix(prdtDvs);

        var cateName = $("#cate_sortcode > option:selected").text();
        var sumPrice    = $("#esti_sale_price").text();
        var paper   = $("#paper > option:selected").text();
        var size    = $("#size > option:selected").text();
        var tmpt    = $("#print_tmpt").val();
        var amt     = $("#amt").val();
        var amtUnit = $("#amt").attr("amt_unit");
        var count   = $("#count").val();

        var after = '';
        $('.after .overview ul li').each(function() {
            after += $(this).text();
            after += ', ';
        });
        after = after.substr(0, after.length - 2);

        var printPrice = $("#esti_print").text();
        var afterPrice = $("#esti_after").text();

        var data = {
            "cate_name" : [
                cateName
            ],
            "paper" : [
                paper
            ],
            "size" : [
                size
            ],
            "tmpt" : [
                tmpt
            ],
            "amt" : [
                amt
            ],
            "amt_unit" : [
                amtUnit
            ],
            "count" : [
                count
            ],
            "after" : [
                after
            ],
            "sum_price"    : sumPrice,
            "paper_price"  : '-',
            "print_price"  : printPrice,
            "output_price" : '-',
            "after_price"  : afterPrice
        };

        this.data = data;

        if (dvs === "pop") {
            getEstiPopHtml(data);
        } else {
            downEstiExcel(data);
        }
    }
};

var changePaper = function() {
    selectedValue= $("#paper_cover option:selected").val();

    if(selectedValue == '338') { // 아트지 백색 90g
        $("#size > option").each(function() {
            $(this).show();
        });
    } else if(selectedValue == '339') { // 아트지 백색 120g
        $("#size > option").each(function() {
           if($(this).attr('value') == '649') {
               $(this).hide();
           } else if($(this).attr('value') == '650') {
               $(this).hide();
           } else if($(this).attr('value') == '655') {
               $(this).hide();
           } else if($(this).attr('value') == '656') {
               $(this).hide();
           } else {
               $(this).show();
           }
        });
    } else if(selectedValue == '340') { // 아트지 백색 150g
        $("#size > option").each(function() {
            if($(this).attr('value') == '649') {
                $(this).hide();
            } else if($(this).attr('value') == '650') {
                $(this).hide();
            } else if($(this).attr('value') == '655') {
                $(this).hide();
            } else if($(this).attr('value') == '656') {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    } else if(selectedValue == '341') { // 아트지 백색 180g
        $("#size > option").each(function() {
            if($(this).attr('value') == '649') {
                $(this).hide();
            } else if($(this).attr('value') == '650') {
                $(this).hide();
            } else if($(this).attr('value') == '655') {
                $(this).hide();
            } else if($(this).attr('value') == '656') {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    } else if(selectedValue == '342') { // 아트지 백색 300g
        $("#size > option").each(function() {
            if($(this).attr('value') == '649') {
                $(this).hide();
            } else if($(this).attr('value') == '650') {
                $(this).hide();
            } else if($(this).attr('value') == '651') {
                $(this).hide();
            } else if($(this).attr('value') == '652') {
                $(this).hide();
            } else if($(this).attr('value') == '653') {
                $(this).hide();
            } else if($(this).attr('value') == '654') {
                $(this).hide();
            } else if($(this).attr('value') == '655') {
                $(this).hide();
            } else if($(this).attr('value') == '656') {
                $(this).hide();
            } else if($(this).attr('value') == '660') {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    } else if(selectedValue == '557') { // 모조지 백색 80g
        $("#size > option").each(function() {
            if($(this).attr('value') == '649') {
                $(this).hide();
            } else if($(this).attr('value') == '650') {
                $(this).hide();
            } else if($(this).attr('value') == '655') {
                $(this).hide();
            } else if($(this).attr('value') == '656') {
                $(this).hide();
            } else if($(this).attr('value') == '660') {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    }

    $("#size > option").each(function()
    {
        $(this).removeAttr('selected');
    });

    $("#size > option").each(function()
    {
        if($(this).css("display") != "none") {
            $(this).attr("selected",true);
            return false;
        }
    });

    setAmt();
    reCalcRealPaperAmt();
    size();
};

var setAmt = function() {
    stan_mpcode = $("#size").find("option:selected").val();
    from = 1;
    if(stan_mpcode == '649') { // 국전지
        from = 8;
    } else if(stan_mpcode == '650') { // 국반절
        from = 4;
    } else if(stan_mpcode == '651') { // A3

    } else if(stan_mpcode == '652') { // A4

    } else if(stan_mpcode == '653') { // A5

    } else if(stan_mpcode == '654') { // A6

    } else if(stan_mpcode == '655') { // 2절
        from = 8;
    } else if(stan_mpcode == '656') { // 4절
        from = 4;
    } else if(stan_mpcode == '657') { // 8절

    } else if(stan_mpcode == '658') { // 16절

    } else if(stan_mpcode == '659') { // 32절

    } else if(stan_mpcode == '660') { // 64절

    }

    $("#amt > option").each(function() {
        $(this).show();
        $(this).removeAttr('selected');
        $(this).removeAttr('style');
    });

    if(from == 4) {
        $('#amt option[value=5]').hide();
        $('#amt option[value=6]').hide();
        $('#amt option[value=7]').hide();
    }

    i = 1;
    $("#amt > option").each(function() {
        if(from <= i++ ) {
            if($(this).css("display") != "none") {
                $(this).prop("selected", "selected");
                $('#amt').change();
                return false;
            }
        } else {
            $(this).hide();
        }
    });
}