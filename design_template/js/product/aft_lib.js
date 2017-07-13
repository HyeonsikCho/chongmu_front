
/**
 * @brief 후공정 리스트
 *
 * @param dvs = 후공정 구분값
 * @param price = 후공정 가격
 */

var setAfterList = {
	"코팅" : "coating",
    "귀도리" : "rounding",
    "오시" : "impression",
    "미싱" : "dotline",
    "타공" : "punching",
    "접지" : "foldline",
    "엠보싱" : "embossing",
    "박" : "foil",
    "형압" : "press",
    "도무송" : "thomson",
    "넘버링" : "numbering",
    "재단" : "cutting",
    "제본" : "binding",
    "접착" : "bonding",
    "라미넥스" : "laminex",
    "빼다" : "background"
}


/**
 * @brief 후공정 가격 세팅
 *
 * @param dvs = 후공정 구분값
 * @param price = 후공정 가격
 */
var setAfterPrice = function(dvs, price) {
    $("#" + dvs + "_price").val(price);
    $("#" + dvs + "_price_dd").html(price.format() + "원");
};

/**
 * @brief 후공정 가격검색을 위해 후공정명을
 * 공통으로 사용하는 영어명으로 변경하고 실검색 함수로 전달
 *
 * @param obj = 체크확인용
 * @param val = 후공정명
 */
var loadAfterPrice = {
    "dvs"     : null,
    "exec"    : function(checked, val) {
        if (checked === false) {
            changeData();
            return false;
        }

        switch (val) {
            case "코팅" :
                this.dvs = "coating";
                break;
            case "귀도리" :
                this.dvs = "rounding";
                break;
            case "오시" :
                this.dvs = "impression";
                break;
            case "미싱" :
                this.dvs = "dotline";
                break;
            case "타공" :
                this.dvs = "punching";
                break;
            case "접지" :
                this.dvs = "foldline";
                break;
            case "엠보싱" :
                this.dvs = "embossing";
                break;
            case "박" :
                this.dvs = "foil";
                break;
            case "형압" :
                this.dvs = "press";
                break;
            case "도무송" :
                this.dvs = "thomson";
                break;
            case "넘버링" :
                this.dvs = "numbering";
                break;
            case "재단" :
                this.dvs = "cutting";
                break;
            case "제본" :
                this.dvs = "binding";
                break;
            case "접착" :
                this.dvs = "bonding";
                break;
            case "라미넥스" :
                this.dvs = "laminex";
                break;
            case "빼다" :
                this.dvs = "background";
                break;
        }

        changeData();
    }
};

/**
 * @brief 불러온 후공정 가격에서 해당하는 가격 검색
 * 실제로 수행하는 함수는 각 페이지별 자바스크립트 파일에
 * 존재하는 공통된 이름을 가진 함수를 호출한다
 *
 * 각 상품별로 처리 로직이 다를 수 있기 때문에 별도로 처리한다
 *
 * @param dvs = 후공정 구분값
 * @param mpcode = 맵핑코드
 */
var getAfterPrice = {
    "price" : {
        "coating"    : null,
        "rounding"   : null,
        "impression" : null,
        "dotline"    : null,
        "punching"   : null,
        "foldline"   : null,
        "embossing"  : null,
        "foil"       : null,
        "press"      : null,
        "thomson"    : null,
        "numbering"  : null,
        "cutting"    : null,
        "binding"    : null,
        "bonding"    : null,
        "laminex"    : null,
        "background"    : null
    },
    "load" : function(dvs, data) {
        // 가격이 없을경우 검색하는 함수
        var url = "/ajax/product/load_after_price.php";
        var callback = function(result) {
            getAfterPrice.price[dvs] = result;
            getAfterPrice.common(dvs);
        };

        ajaxCall(url, "json", data, callback);
    },
    "common" : function(dvs) {
        // dvs값만 받아서 아래 정의되어있는 함수로 분리시켜주는 함수
        this[dvs][monoYn](dvs);
    },
    "coating"    : {
        "0" : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "rounding"   : {
        "0" : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "impression" : {
        "0" : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "dotline"    : {
        "0"  : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "punching"   : {
        "0"  : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "foldline"   : {
        "0"  : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "embossing"  : {
        "0"  : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "foil"       : {
        "0"  : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "press"      : {
        "0"  : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "thomson"    : {
        "0"  : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "numbering"  : {
        "0"  : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "cutting"    : {
        "0" : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "binding"    : {
        "0" : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "bonding"    : {
        "0"  : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    },
    "laminex"    : {
        "0"  : function getPrice(dvs) {
        },
        "1" : function getPrice(dvs) {
        }
    },
    "background"    : {
        "0"  : function getPrice(dvs) {
            changeData();
        },
        "1" : function getPrice(dvs) {
            changeData();
        }
    }
};

/******************************************************************************
 * 후공정 하위 depth 관련 함수
 *****************************************************************************/

/**
 * @brief 후공정 하위항목 검색
 *
 * @param data     = ajaxCall에서 사용할 파라미터
 * @param callback = ajaxCall에서 사용할 callback함수
 */
var loadAfterDepth = function(data, callback) {
    var url = "/ajax/product/load_after_depth.php";
    ajaxCall(url, "html", data, callback);
};

/**
 * @brief 제본 하위항목 검색
 *
 * @param val = 제본 depth1 이름
 */
var loadBindingDepth2 = function(val) {
    var callback = function(result) {
        $("#binding_val").html(result);
        getAfterPrice.common("binding");
    };

    var data = {
        "cate_sortcode" : $("#cate_bot").val(),
        "after_name"    : "제본",
        "depth1"        : val,
        "flag"          : 'Y'
    };

    loadAfterDepth(data, callback);
};

var loadPunchingDepth2 = function(val) {
    var callback = function(result) {
        $("#punching_val").html(result);
        getAfterPrice.common("punching");
    };

    var data = {
        "cate_sortcode" : $("#cate_bot").val(),
        "after_name"    : "타공",
        "depth1"        : val + '개',
        "flag"          : 'Y'
    };

    loadAfterDepth(data, callback);
};

/**
 * @brief 제본 하위항목 검색
 *
 * @param val = 제본 depth1 이름
 */
var loadRoundingDepth2 = function(val) {

    var callback = function(result) {
        $("#rounding_val").html(result);
        getAfterPrice.common("rounding");
    };

    var data = {
        "cate_sortcode" : $("#cate_bot").val(),
        "after_name"    : "귀도리",
        "depth1"        : val,
        "flag"          : 'Y'
    };

    loadAfterDepth(data, callback);
};

/******************************************************************************
 * 후공정 정보 생성 함수
 *****************************************************************************/

/**
 * @brief 후공정 정보 생성 객체
 *
 * @param dvs = 후공정 구분값
 *
 * @return validation 체크 통과여부
 */
var makeAfterInfo = {
    "all" : function() {
        var ret = null;
        var dvs = null;
        var func = null;

        $("input[name='chk_after[]']").each(function() {
            if ($(this).prop("checked") === false) {
                return true;
            }

            dvs = $(this).attr("id").split('_')[1];
            func = makeAfterInfo[dvs];

            if (checkBlank(func) === true) {
                return true;
            }

            ret = true;

            ret = func(dvs);

            if (ret === false) {
                return false;
            }
        });
        return ret;
    },
    "rounding"   : function(dvs) {
        var str = "";
		var cnt = 0;
		var roundingCntCheck = {
				"한귀도리" : 1,
				"두귀도리" : 2,
				"세귀도리" : 3,
				"네귀도리" : 4
		}

        $("input[name='rounding_dvs']:checked").each(function() {
            str += $(this).val();
            str += ", ";
			cnt++;
        });


		if(parseInt(roundingCntCheck[$('#'+dvs).val()]) < cnt ||  parseInt(roundingCntCheck[$('#'+dvs).val()]) > cnt){
			alert($('#'+dvs).val()+' 위치를 정확히 선택해주세요');
			return false;
		}
        if (checkBlank(str) === true) {
			alert("귀도리 위치를 선택해주세요.");
            return false;
        }

        str = str.substr(0, (str.length - 2));

        $("#" + dvs + "_info").val(str);

        return str;
    },
    "impression" : function(dvs) {
        var prefix = dvs + '_';
        var val = $('#' + prefix + "cnt").val();
        var selector = "input[name='" + prefix + val + "_val']:checked";

        if ($(selector).length === 0) {
            makeAfterInfo.msg = "오시 위치를 선택해주세요.";
            return false;
        }

        var mpcode = $(selector).val();
        $("#" + prefix + "val").val(mpcode);

        if ($(selector).attr("dvs") === "M") {
            return '';
        }

        selector = prefix + val + "_pos";
        var str = "";
        var posVal = $("#" + selector + '1').val();

        if (checkBlank(posVal) === true) {
            makeAfterInfo.msg = "오시 선 위치를 입력해주세요.";
            return false;
        }
        str += "첫 번째 선 : ";
        str += posVal;
        str += "mm";

        var $obj = $("#" + selector + '2');
        if ($obj.length > 0) {
            posVal = $obj.val();
            if (checkBlank(posVal) === true) {
                makeAfterInfo.msg = "오시 선 위치를 입력해주세요.";
                return false;
            }

            str += " / 두 번째 선 : ";
            str += posVal;
            str += "mm";
        }

        $obj = $("#" + selector + '3');
        if ($("#" + selector + '3').length > 0) {
            posVal = $obj.val();
            if (checkBlank(posVal) === true) {
                makeAfterInfo.msg = "오시 선 위치를 입력해주세요.";
                return false;
            }

            str += " / 세 번째 선 : ";
            str += posVal;
            str += "mm";
        }

        $obj = $("#" + selector + '4');
        if ($("#" + selector + '4').length > 0) {
            posVal = $obj.val();
            if (checkBlank(posVal) === true) {
                makeAfterInfo.msg = "오시 선 위치를 입력해주세요.";
                return false;
            }

            str += " / 네 번째 선 : ";
            str += posVal;
            str += "mm";
        }

        $("#" + dvs + "_info").val(str);
        return str;
    },
    "dotline"    : function(dvs) {
        var prefix = dvs + '_';
        var val = $('#' + prefix + "cnt").val();
        var selector = "input[name='" + prefix + val + "_val']:checked";

        if ($(selector).length === 0) {
            makeAfterInfo.msg = "미싱 위치를 선택해주세요.";
            return false;
        }

        var mpcode = $(selector).val();
        $("#" + prefix + "val").val(mpcode);

        if ($(selector).attr("dvs") === "M") {
            return true;
        }

        selector = prefix + val + "_pos";
        var str = "";
        var posVal = $("#" + selector + '1').val();

        if (checkBlank(posVal) === true) {
            makeAfterInfo.msg = "미싱 선 위치를 입력해주세요.";
            return false;
        }
        str += "첫 번째 선 : ";
        str += posVal;
        str += "mm";

        var $obj = $("#" + selector + '2');
        if ($obj.length > 0) {
            posVal = $obj.val();
            if (checkBlank(posVal) === true) {
                makeAfterInfo.msg = "미싱 선 위치를 입력해주세요.";
                return false;
            }

            str += " / 두 번째 선 : ";
            str += posVal;
            str += "mm";
        }

        $obj = $("#" + selector + '3');
        if ($("#" + selector + '3').length > 0) {
            posVal = $obj.val();
            if (checkBlank(posVal) === true) {
                makeAfterInfo.msg = "미싱 선 위치를 입력해주세요.";
                return false;
            }

            str += " / 세 번째 선 : ";
            str += posVal;
            str += "mm";
        }

        $obj = $("#" + selector + '4');
        if ($("#" + selector + '4').length > 0) {
            posVal = $obj.val();
            if (checkBlank(posVal) === true) {
                makeAfterInfo.msg = "미싱 선 위치를 입력해주세요.";
                return false;
            }

            str += " / 네 번째 선 : ";
            str += posVal;
            str += "mm";
        }

        $("#" + dvs + "_info").val(str);
        return str;
    },
    "punching"   : function(dvs) {
        var cnt = parseInt($("#" + dvs).val());
        var str = "";

        /*
        for (var i = 1; i <= cnt; i++) {
            var selectorW = "#" + dvs + "_pos_w" + i;
            var selectorH = "#" + dvs + "_pos_h" + i;

            var valW = $(selectorW).val();
            var valH = $(selectorH).val();

            if (checkBlank(valW) === true ||
                checkBlank(valH) === true) {
                makeAfterInfo.msg = "타공 위치를 입력해주세요.";
                return false;
            }


            if (i === 1) {
                str += "첫 번째 타공 위치 가로 ";
                str += $(selectorW).val();
                str += "mm, ";
                str += "첫 번째 타공 위치 세로 ";
                str += $(selectorH).val();
                str += "mm";
            } else if (i === 2) {
                str += " / 두 번째 타공 위치 가로 ";
                str += $(selectorW).val();
                str += "mm, ";
                str += "두 번째 타공 위치 세로 ";
                str += $(selectorH).val();
                str += "mm";
            } else if (i === 3) {
                str += " / 세 번째 타공 위치 가로 ";
                str += $(selectorW).val();
                str += "mm, ";
                str += "세 번째 타공 위치 세로 ";
                str += $(selectorH).val();
                str += "mm";
            } else if (i === 4) {
                str += " / 네 번째 타공 위치 가로 ";
                str += $(selectorW).val();
                str += "mm, ";
                str += "네 번째 타공 위치 세로 ";
                str += $(selectorH).val();
                str += "mm";
            }
        }
*/
        $("#" + dvs + "_info").val(str);
        return str;
    },
    "foldline"   : function(dvs) {
        //var prefix = getPrefix(dvs);
        var aftPrefix = dvs + '_';

        var wid_1  = parseInt($(aftPrefix + "wid_1").val());
        var vert_1 = parseInt($(aftPrefix + "vert_1").val());
        var wid_2  = parseInt($(aftPrefix + "wid_2").val());
        var vert_2 = parseInt($(aftPrefix + "vert_2").val());

        var aft_1 = $(aftPrefix + "1").val();
        var dvs_1 = $(aftPrefix + "dvs_1").val();
        var aft_2 = $(aftPrefix + "2").val();
        var dvs_2 = $(aftPrefix + "dvs_2").val();

        if (checkBlank(aft_1) && checkBlank(dvs_1) &&
            checkBlank(aft_2) && checkBlank(dvs_2)) {
            return false;
        }

        var str = ''

        if (dvs_1 === "양면") {
            if (isNaN(wid_1)) {
                makeAfterInfo.msg = "가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_1)) {
                makeAfterInfo.msg = "세로값을 입력해주세요.";
                return false;
            }

            str += "전면(" + aft_1 + ") : 가로 " + wid_1 + "mm, 세로 " + vert_1 + "mm / ";
            str += "후면(" + aft_1 + ") : 가로 " + wid_1 + "mm, 세로 " + vert_1 + "mm";
        } else if (!checkBlank(aft_1) && !checkBlank(dvs_1) &&
            checkBlank(aft_2) && checkBlank(dvs_2)) {
            // 전면만
            if (isNaN(wid_1)) {
                makeAfterInfo.msg = "가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_1)) {
                makeAfterInfo.msg = "세로값을 입력해주세요.";
                return false;
            }

            str += "전면(" + aft_1 + ") : 가로 " + wid_1 + "mm, 세로 " + vert_1 + "mm";
        } else if (checkBlank(aft_1) && checkBlank(dvs_1) &&
            !checkBlank(aft_2) && !checkBlank(dvs_2)) {
            // 후면만
            if (isNaN(wid_2)) {
                makeAfterInfo.msg = "가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_2)) {
                makeAfterInfo.msg = "세로값을 입력해주세요.";
                return false;
            }

            str += "후면(" + aft_2 + ") : 가로 " + wid_2 + "mm, 세로 " + vert_2 + "mm";
        } else if (!checkBlank(aft_1) && !checkBlank(dvs_1) &&
            !checkBlank(aft_2) && !checkBlank(dvs_2)) {
            if (isNaN(wid_1)) {
                makeAfterInfo.msg = "전면 가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_1)) {
                makeAfterInfo.msg = "전면 세로값을 입력해주세요.";
                return false;
            }

            if (isNaN(wid_2)) {
                makeAfterInfo.msg = "후면 가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_2)) {
                makeAfterInfo.msg = "후면 세로값을 입력해주세요.";
                return false;
            }

            str += "전면(" + aft_1 + ") : 가로 " + wid_1 + "mm, 세로 " + vert_1 + "mm / ";
            str += "후면(" + aft_2 + ") : 가로 " + wid_2 + "mm, 세로 " + vert_2 + "mm";
        } else {
            return false;
        }

        $("#" + dvs + "_info").val(str);
        return str;
    },
    "embossing"  : function(dvs) {
    },
    "foil"       : function(dvs) {
        var aftPrefix = '#' + dvs + '_';
        var wid_1  = parseInt($(aftPrefix + "wid_1").val());
        var vert_1 = parseInt($(aftPrefix + "vert_1").val());
        var wid_2  = parseInt($(aftPrefix + "wid_2").val());
        var vert_2 = parseInt($(aftPrefix + "vert_2").val());

        var aft_1 = $(aftPrefix + "1").val();
        var dvs_1 = $(aftPrefix + "dvs_1").val();
        var aft_2 = $(aftPrefix + "2").val();
        var dvs_2 = $(aftPrefix + "dvs_2").val();

        if (checkBlank(aft_1) && checkBlank(dvs_1) &&
            checkBlank(aft_2) && checkBlank(dvs_2)) {
            return false;
        }

        var str = ''

        if (dvs_1 === "양면") {
            if (isNaN(wid_1)) {
                makeAfterInfo.msg = "가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_1)) {
                makeAfterInfo.msg = "세로값을 입력해주세요.";
                return false;
            }

            str += "전면(" + aft_1 + ") : 가로 " + wid_1 + "mm, 세로 " + vert_1 + "mm / ";
            str += "후면(" + aft_1 + ") : 가로 " + wid_1 + "mm, 세로 " + vert_1 + "mm";
        } else if (!checkBlank(aft_1) && !checkBlank(dvs_1) &&
            checkBlank(aft_2) && checkBlank(dvs_2)) {
            // 전면만
            if (isNaN(wid_1)) {
                makeAfterInfo.msg = "가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_1)) {
                makeAfterInfo.msg = "세로값을 입력해주세요.";
                return false;
            }

            str += "전면(" + aft_1 + ") : 가로 " + wid_1 + "mm, 세로 " + vert_1 + "mm";
        } else if (checkBlank(aft_1) && checkBlank(dvs_1) &&
            !checkBlank(aft_2) && !checkBlank(dvs_2)) {
            // 후면만
            if (isNaN(wid_2)) {
                makeAfterInfo.msg = "가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_2)) {
                makeAfterInfo.msg = "세로값을 입력해주세요.";
                return false;
            }

            str += "후면(" + aft_2 + ") : 가로 " + wid_2 + "mm, 세로 " + vert_2 + "mm";
        } else if (!checkBlank(aft_1) && !checkBlank(dvs_1) &&
            !checkBlank(aft_2) && !checkBlank(dvs_2)) {
            if (isNaN(wid_1)) {
                makeAfterInfo.msg = "전면 가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_1)) {
                makeAfterInfo.msg = "전면 세로값을 입력해주세요.";
                return false;
            }

            if (isNaN(wid_2)) {
                makeAfterInfo.msg = "후면 가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_2)) {
                makeAfterInfo.msg = "후면 세로값을 입력해주세요.";
                return false;
            }

            str += "전면(" + aft_1 + ") : 가로 " + wid_1 + "mm, 세로 " + vert_1 + "mm / ";
            str += "후면(" + aft_2 + ") : 가로 " + wid_2 + "mm, 세로 " + vert_2 + "mm";
        } else {
            return false;
        }

        $("#" + dvs + "_info").val(str);
        return str;
    },
    "press"      : function(dvs) {
        var prefix = '#' + dvs + '_';

        var wid  = $(prefix + "wid_1").val();
        var vert = $(prefix + "vert_1").val();

        if (checkBlank(wid)) {
            makeAfterInfo.msg = "가로값을 입력해주세요.";
            return false;
        }

        if (checkBlank(vert)) {
            makeAfterInfo.msg = "세로값을 입력해주세요.";
            return false;
        }

        var str = "가로 : " + wid + ", 세로 : " + vert;

        $("#" + dvs + "_info").val(str);
        return str;
    },
    "thomson"    : function(dvs) {
    },
    "numbering"  : function(dvs) {
    },
    "cutting"    : function(dvs) {
    },
    "binding"    : function(dvs) {
    },
    "bonding"    : function(dvs) {
    },
    "laminex"    : function(dvs) {
    },
    "background"    : function(dvs) {
    }
};
/******************************************************************************
 * 후공정 옵션선택정보 리셋
 *****************************************************************************/

/**
 * @brief 후공정 정보 생성 객체
 *
 * @param dvs = 후공정 구분값
 *
 * @return validation 체크 통과여부
 */
var resetCheckbox = function(){
	$(":checkbox").each(function(){
		$(this).prop("checked",false);
	});

	return;
}


var getMpcode = function(aft) {
    switch (aft) {
        case 'impression':
            return getImpressionMpcode();
        case 'dotline':
            return getDotlineMpcode();
        case 'punching':
            return getPunchingMpcode();
        case 'press':
            return getPressMpcode();
        case 'foil':
            return getFoilMpcode();
        default :
            return $("#" + aft + "_val").val();
    }
}

var getImpressionMpcode = function() {
    cnt = $("#impression_cnt").find("option:selected").val();
    mpcode = $("input[name='impression_" + cnt + "_val']:checked").val();
    return mpcode;
}

var getDotlineMpcode = function() {
    cnt = $("#dotline_cnt").find("option:selected").val();
    mpcode = $("input[name='dotline_" + cnt + "_val']:checked").val();

    return mpcode;
}

var getPunchingMpcode = function() {
    mpcode = $("#punching_val").find("option:selected").val();

    return mpcode;
}

var getPressMpcode = function() {
    mpcode = $("#press_val").find("option:selected").val();

    return mpcode;
}

var getFoilMpcode = function() {
    mpcode = $("#foil_val_1").val();
    return mpcode;
}

var getDepth = function(aft, cnt) {
    switch (aft) {
        case 'press':
            return getPressDepth(cnt);
        case 'foil':
            return getFoilDepth(cnt);
        case 'dotline':
            return getDotlineDepth(cnt);
        case 'numbering':
            return getNumberingDepth(cnt);
        default :
            return '';
    }
}

var getNumberingDepth = function(cnt) {
    switch(cnt) {
        case '1' : // depth1
            return $("#numbering_val").find("option:selected").text();
            break;
        case '2' : // depth2
            return $("#size").find("option:selected").val();
            break;
        case '3' : // depth3
            return "";
            break;
        default :
            return "";
    }
}

var getDotlineDepth = function(cnt) {
    switch(cnt) {
        case '1' : // depth1
            return $("#dotline_cnt").find("option:selected").val();
            break;
        case '2' : // depth2
            return $("#size").find("option:selected").val();
            break;
        case '3' : // depth3
            return "";
            break;
        default :
            return "";
    }
}

var getPressDepth = function(cnt) {
    switch(cnt) {
        case '1' : // depth1
            return $("#press_wid_1").val() + "," + $("#press_vert_1").val();
            break;
        case '2' : // depth2
            return "";
            break;
        case '3' : // depth3
            return "";
            break;
        default :
            return "";
    }
}

var getFoilDepth = function(cnt) {
    switch(cnt) {
        case '1' : // depth1
            return getFoilName();
            break;
        case '2' : // depth2
            return getFoilDepth23('1');
            break;
        case '3' : // depth3
            return getFoilDepth23('2');
            break;
        default :
            return "";
    }
}

var getFoilDepth23 = function(cnt) {
    dvs = $("#foil_dvs_"+cnt).children('option:selected').val();

    depth = '';
    if(dvs != '-') {
        foil_wid = $("#foil_wid_"+cnt).val();
        foil_vert = $("#foil_vert_"+cnt).val();

        if(foil_wid == "" || foil_vert == "") {
            return "";
        }
        depth = dvs + "," + foil_wid + "," + foil_vert;
    }

    return depth;
}

var getFoilName = function() {
    name1 = $("#foil_1").children('option:selected').val();
    name2 = $("#foil_2").children('option:selected').val();

    name = '';
    if(name1 != '-') {
        name += name1;
    }

    if(name2 != '-') {
        name += ',' + name2;
    }

    return name;
};

var changeFoilDvs = function(val) {
    if (checkBlank(val)) {
        return false;
    }

    //var prefix = getPrefix(dvs);

    if (val === "양면") {
        $("#foil_2").val('');
        $("#foil_dvs_2").val('');
        $("#foil_wid_2").val('');
        $("#foil_vert_2").val('');

        $("#foil_2").prop("disabled", true);
        $("#foil_dvs_2").prop("disabled", true);
        $("#foil_wid_2").prop("disabled", true);
        $("#foil_vert_2").prop("disabled", true);
    } else {
        $("#foil_2").prop("disabled", false);
        $("#foil_dvs_2").prop("disabled", false);
        $("#foil_wid_2").prop("disabled", false);
        $("#foil_vert_2").prop("disabled", false);
    }

    getAfterPrice.common("foil");
};
