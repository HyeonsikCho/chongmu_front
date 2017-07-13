<?
/**
 * @brief option html 공통사용함수
 *
 * @param $val  = option 실제 값
 * @param $dvs  = option 화면 출력값
 * @param $attr = option에 추가로 입력할 attribute
 *
 * @return option html
 */
function option($val, $dvs, $attr = '') {
    $option_form = "<option %s value=\"%s\">%s</option>";

    $ret = sprintf($option_form, $attr, $val, $dvs);

    return $ret;
}

//콤보박스 option 생성
function optionForEsti($str, $val="") {
    return "\n  <option value=\"" . $val . "\">" . $str . "</option>";
}

/**
 * @brief 옵션 html 생성
 *
 * @param $rs = 검색결과
 * @param $arr["flag"]    = "기본 값 존재여부"
 * @param $arr["def"]     = "기본 값(ex:전체)"
 * @param $arr["def_val"] = "기본 값의 option value"
 * @param $arr["val"]      = "option value에 들어갈 필드 값"
 * @param $arr["dvs"]      = "option에 표시할 필드 값"
 * @param $arr["dvs_tail"] = "option 값 뒤에 붙일 단어"
 * @param $arr["dvs_tail"] = "option 값 뒤에 붙일 단어"
 * @param $arr["sel"] = "selected할 val값"
 *
 * @return option html
 */
function makeOptionHtml($rs, $arr) {
    $html = "";

    if ($arr["flag"] === true) {
        $html = option($arr["def_val"], $arr["def"], "selected=\"selected\"");
    }

    $dvs_tail = $arr["dvs_tail"];
    $sel_val  = $arr["sel"];
    $val = $arr["val"];
    $dvs = $arr["dvs"];

    while ($rs && !$rs->EOF) {
        $opt_dvs = $rs->fields[$dvs];
        $opt_val = null;

        //필드 값 뒤에 붙일 단어
        if ($dvs_tail !== null) {
            $opt_dvs = $opt_dvs . $dvs_tail;
        }

        //만약 $val 빈값이 아니면
        if ($val !== null) {
			$opt_val = $rs->fields[$val];

            if (empty($opt_val) === true) {
                $opt_val = $opt_dvs;
            }
        } else {
            $opt_val = $opt_dvs;
        }

        $selected = "";
        if ($opt_val === $sel_val) {
            $selected = "selected=\"selected\"";
        }

        $html .= option($opt_val, $opt_dvs, $selected);

        $rs->MoveNext();
    }

    return $html;
}


function makeEstiOptionHtml($rs, $val, $dvs, $base="전체", $flag="Y") {

    if ($flag == "Y") {
        $html = "\n" . optionForEsti($base);

    } else {
        $html = "";
    }

    while ($rs && !$rs->EOF) {
        $fields = $rs->fields[$dvs];

        //만약 $val 빈값이면
        if ($val === "") {
            $value = $fields;
        } else {
			$value = $rs->fields[$val];
		}

        $html .= "\n" . optionForEsti($fields, $value);
        $rs->MoveNext();
    }

    return $html;
}
?>
