<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/define/common_config.php');

class FrontCommonUtil {
    /**
     * @brief adodb record set 캐쉬가 저장된 디렉토리를 비우는 함수
     * 기본적으로는 define/common_config.php에 지정된 경로를 비운다.
     */
    function rmCacheDir($dir = ADODB_CACHE_DIR) {
        if ($dir === "" || $dir === '' || $dir === '/') {
            return false;
        }

        $command = sprintf("rm -rf %s/*", $dir);
        $ret = null;

        system($command, $ret);

        return $ret;
    }

    /**
     * @brief 배열을 구분자가 붙어있는 문자열로 생성한다
     *
     * @param $arr       = 문자열로 변환할 배열
     * @param $delim     = 구분자
     * @param $enclosure = 문장 구분자
     *
     */
    function arr2delimStr($arr, $delim = ',', $enclosure = '"') {
        if (empty($arr) === true || count($arr) === 0) {
            return '';
        }

        $ret = "";
        $delim_ptrn = "/" . $delim . "/";
        $enclosure_ptrn = "/" . $enclosure . "/";

        foreach ($arr as $key => $val) {
            if (empty($val) === true) {
                continue;
            }

            $is_enclosure = preg_match($enclosure_ptrn, $val);

            if ($is_enclosure === true) {
                $val = preg_replace("/\"/", "\"\"", $val);
            }

            $is_delim = preg_match($delim_ptrn, $val);

            if ($is_delim === true) {
                $val = '"' . $val . '"';
            }

            $ret .= $val . $delim;
        }

        return substr($ret, 0, -1);
    }

    /**
     * @brief ie에서 utf-8 파일명 다운로드 받을 때 euc-kr로 인코딩
     *
     * @param $str = 인코딩할 문자열
     *
     * @return 인코딩된 문자열
     */
    function utf2euc($str) {
        return iconv("UTF-8", "cp949//IGNORE", $str);
    }

    /**
     * @brief 현재 브라우저가 ie인지 확인
     *
     * @return ie면 true
     */
    function isIe() {
        if(!isset($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
            return true; // IE7
        }
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) {
            return true; // IE8~11
        }
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'rv:') !== false) {
            return true; // IE11
        }

        return false;
    }

    /**
     * @brief 카테고리 대중소 분류코드 반환
     *
     * @detail 카테고리 중, 소 분류코드가 무조건 001로 시작한다고
     * 장담할 수 없으므로 DB검색으로 가장 작은값을 먼저 가져옴
     *
     * @param $conn = connection identifier
     * @param $dao  = 카테고리 분류코드 검색용 dao
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return array(
     *             "sortcode_t" => 카테고리 대분류 분류코드,
     *             "sortcode_m" => 카테고리 중분류 분류코드,
     *             "sortcode_b" => 카테고리 소분류 분류코드
     *         )
     */
    function getTMBCateSortcode($conn, $dao, $cate_sortcode) {
        $sortcode_t = false;
        $sortcode_m = false;
        $sortcode_b = false;

        if (strlen($cate_sortcode) === 3) {
            $sortcode_t = $cate_sortcode;
            $sortcode_m = $dao->selectCateSortcode($conn, $sortcode_t);
            $sortcode_b = $dao->selectCateSortcode($conn, $sortcode_m);
        }
        if (strlen($cate_sortcode) === 6) {
            $sortcode_t = substr($cate_sortcode, 0, 3);
            $sortcode_m = $cate_sortcode;
            $sortcode_b = $dao->selectCateSortcode($conn, $sortcode_m);
        }
        if (strlen($cate_sortcode) === 9) {
            $sortcode_t = substr($cate_sortcode, 0, 3);
            $sortcode_m = substr($cate_sortcode, 0, 6);
            $sortcode_b = $cate_sortcode;
        }

        return array("sortcode_t" => $sortcode_t,
                     "sortcode_m" => $sortcode_m,
                     "sortcode_b" => $sortcode_b);
    }

    /**
     * @brief 옵션/후공정 depth1, 2, 3을 붙여서 하나로 만듬
     *
     * @detail 추가 옵션/후공정 팝업에서 사용
     *
     * @param $info = 옵션/후공정 정보
     *
     * @return 옵션/후공정 풀네임
     */
    function getOptAfterFullName($info) {
        $depth1 = $info["depth1"];
        $depth2 = $info["depth2"];
        $depth3 = $info["depth3"];

        $dvs = '';

        if ($depth1 !== '-') {
            $dvs = $depth1;
        }
        if ($depth2 !== '-') {
            $dvs .= $depth2;
        }
        if ($depth3 !== '-') {
            $dvs .= $depth3;
        }

        return $dvs;
    }

    /**
     * @brief 검색결과를 배열로 변환
     *
     * @param $rs = 검색결과
     * @param $field = 배열에 저장할 필드명
     *
     * @return 변환된 배열
     */
    function rs2arr($rs, $field = "mpcode") {
        $ret = array();

        $i = 0;
        while ($rs && !$rs->EOF) {
            $ret[$i++] = $rs->fields[$field];
            $rs->MoveNext();
        }

        return $ret;
    }

    /**
     * @brief 진행상태 코드값으로 주문, 진행상태값 반환
     *
     * @param $p_code = 진행상태 코드값
     *
     * @return 주문상태 + 진행상태값
     */
    function statusCode2status($p_code) {
        $arr = OrderStatus::STATUS_PROC;

        foreach ($arr as $status => $proc_arr) {
            foreach ($proc_arr as $proc => $code) {
                if ($p_code === $code) {
                    return $status . $proc;
                }
            }
        }

        return false;
    }

    /**
     * @brief 사이드 메뉴 주문상태 요약 배열 생성
     *
     * @detail $ret["200"] = 입금대기
     * $ret["300"] = 접수
     * $ret["400"] = 조판
     * $ret["600"] = 출력
     * $ret["700"] = 인쇄
     * $ret["800"] = 후공정
     * $ret["900"] = 입고
     * $ret["000"] = 출고
     *
     * @param $rs = 검색결과
     *
     * @return 주문상태 + 진행상태값
     */
    function makeOrderSummaryArr($rs) {
        $ret = array(
            "200" => 0,
            "300" => 0,
            "400" => 0,
            "600" => 0,
            "700" => 0,
            "800" => 0,
            "900" => 0,
            "000" => 0
        );

        while ($rs && !$rs->EOF) {
            $order_state = $rs->fields["order_state"];
            $state_count = intval($rs->fields["state_count"]);

            $key = $order_state[0] . "00";

            $ret[$key] += $state_count;

            $rs->MoveNext();
        }

        return $ret;
    }

    /**
     * @brief 100원 단위 반올림
     * 부가세 단위가 10원으로 나오도록 하기 위함임
     *
     * @param $val = 올림할 값
     *
     * @return 계산된 값
     */
    function ceilVal($val) {
        $val = floatval($val);

        $val = round($val * 0.01) * 100;

        return $val;
    }

    /**
     * @brief 잘못 된 접근시
     *
     * @param $title = alert 내용
     *
     * @return 계산된 값
     */
    function errorGoBack($title = "") {
        echo "<script>";
        echo "    alert('" . $title . "');";
        echo "    history.go(-1);";
        echo "</script>";
    }

    /**
     * @brief 넘어온 정보로 인쇄 맵핑코드 검색
     *
     * @param $conn = connection identifer
     * @param $dao  = 검색을 수행할 dao객체
     * @param $fb   = 넘어온 값을 가져올 FormBean 객체
     * @param $dvs  = 영역구분값(all 때문에 전달받음)
     *
     * @return 맵핑코드 배열
     */
    function getPrintMpcode($conn, $dao, $fb, $dvs) {
        $ret = array();

        $flag = $fb["flag"];
        $cate_sortcode = $fb["cate_sortcode"];

        $bef_print_name     = $fb[$dvs . "_bef_print_name"];
        $bef_add_print_name = $fb[$dvs . "_bef_add_print_name"];
        $aft_print_name     = $fb[$dvs . "_aft_print_name"];
        $aft_add_print_name = $fb[$dvs . "_aft_add_print_name"];
        $print_purp         = $fb[$dvs . "_print_purp"];

        $param = array();
        $param["cate_sortcode"] = $cate_sortcode;
        $param["purp_dvs"]      = $print_purp;

        // 낱장형 일 때 검색
        if ($flag === 'Y') {
            $param["name"] = $bef_print_name;
            $bef_mpcode = $dao->selectCatePrintMpcode($conn, $param);

            $ret["bef"]     = $bef_mpcode;
            $ret["bef_add"] = '0';
            $ret["aft"]     = '0';
            $ret["aft_add"] = '0';

            //print_r($ret);

            return $ret;
        }

        // 전면 인쇄 맵핑코드
        $param["name"]     = $bef_print_name;
        $param["side_dvs"] = "전면";
        $bef_mpcode = $dao->selectCatePrintMpcode($conn, $param);
        // 전면 추가 인쇄 맵핑코드
        $param["name"]     = $bef_add_print_name;
        $param["side_dvs"] = "전면추가";
        $bef_add_mpcode = $dao->selectCatePrintMpcode($conn, $param);
        // 후면 인쇄 맵핑코드
        $param["name"]     = $aft_print_name;
        $param["side_dvs"] = "후면";
        $aft_mpcode = $dao->selectCatePrintMpcode($conn, $param);
        // 후면 추가 인쇄 맵핑코드
        $param["name"]     = $aft_add_print_name;
        $param["side_dvs"] = "후면추가";
        $aft_add_mpcode = $dao->selectCatePrintMpcode($conn, $param);

        $ret["bef"]     = $bef_mpcode;
        $ret["bef_add"] = $bef_add_mpcode;
        $ret["aft"]     = $aft_mpcode;
        $ret["aft_add"] = $aft_add_mpcode;

        return $ret;
    }

    /**
     * @brief 종이 정보 문자열 생성
     * 구분값이 '-' 일 경우 빼고 생성
     *
     * @param $arr = 종이 정보 배열
     *
     * @return 종이 정보 문자열
     */
    function makePaperInfoStr($arr) {
        $name        = $arr["name"];
        $dvs         = $arr["dvs"];
        $color       = $arr["color"];
        $basisweight = $arr["basisweight"];

        $ret = $name;
        if ($dvs !== '-') {
            $ret .= " " . $dvs;
        }
        if ($color !== '-') {
            $ret .= " " . $color;
        }
        if ($basisweight !== "0g") {
            $ret .= " " . $basisweight;
        }

        return $ret;
    }

    /**
     * @brief 문자열 길이 자르기
     *
     * @param $str = 원본문자열
     * @param $start = 추출할 문자열 시작 위치
     * @param $end = 추출할 문자열 끝 위치
     * @param $tail = 뒤에 붙일 문자열
     *
     * @return 다듬어진 문자열
     */

    function str_cut($str, $start = 0, $end, $tail = "..") {

        if (!$str) return "";

        if (strlen($str) > $end)
            return mb_substr($str, $start, $end, 'UTF-8') . $tail;
        else
            return $str;
    }

    /**
     * @brief 주문번호 생성
     */
    function makeOrderNum() {
        $t = strval(ceil(microtime(true) * 1000.0));
        $r = rand(0, 9);

        return $t . $r;
    }
}
?>
