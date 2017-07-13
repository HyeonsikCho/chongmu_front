<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/order_status.php");

class CommonUtil {
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
     * @brief 실제 종이 인쇄 수량 계산
     *
     * @detail $param["amt"] = 상품 수량
     * $param["pos_num"] = 자리수
     * $param["page_num"] = 페이지수
     * $param["amt_unit"] = 상품 수량 단위
     * $param["crtr_unit"] = 종이 기준 단위
     *
     * @detail 실제 계산 공식은 아래와 같다
     * ((소수점_올림{
     *     (($수량$ / $자리수$) / (2 / $페이지수$)) / $카테고리 적용 절 수$
     * }) + $핀장수$ / $카테고리 적용 절 수$)[ / 500]
     *
     * @param $param = 가격검색에 필요한 정보배열
     *
     * @return 실제 종이 인쇄 수량
     */
    function getPaperRealPrintAmt($info) {
        $amt       = $info["amt"];
        $pos_num   = $info["pos_num"];
        $page_num  = $info["page_num"];
        $amt_unit  = $info["amt_unit"];
        $crtr_unit = $info["crtr_unit"];

        // 0page일 경우 인쇄 수량 0 반환
        if ($page_num == 0) {
            return 0;
        }
echo $pos_num . 'aaaa';
        $ret = ceil(($amt / $pos_num) / (2 / $page_num));

       // echo "$amt : $pos_num : $page_num : $ret\n";

        if ($crtr_unit !== null && $crtr_unit === 'R') {
            if ($amt_unit !== '연' && $amt_unit !== 'R') {
                $ret /= 500.0;
            }
        }

        return $ret;
    }

    /**
     * @brief 전달받은 문자열 배열 or 문자열을 json으로
     * 넘길 수 있도록 \n을 제거하고 "를 \"로 변경한다
     *
     * @param $str = 변환할 문자열 배열 or 문자열
     *
     * @return 변환결과
     */
    function convJsonStr($str) {
        if (is_array($str) === true) {
            foreach ($str as $key => $val) {
                $val = preg_replace("/\t/", "", $val);
                $val = preg_replace("/\r/", "", $val);
                $val = preg_replace("/\n/", "", $val);
                $val = preg_replace("/\"/", "\\\\\"", $val);

                $str[$key] = $val;
            }
        } else {
            $str = preg_replace("/\t/", "", $str);
            $str = preg_replace("/\r/", "", $str);
            $str = preg_replace("/\n/", "", $str);
            $str = preg_replace("/\"/", "\\\\\"", $str);
        }

        return $str;
    }

    /**
     * @brief 연/월/일 로 이루어진 디렉토리 경로 반환
     *
     * @return 경로 문자열
     */
    function getYmdDirPath() {
        $ret = sprintf("%s/%s/%s/", date("Y")
                                  , date("m")
                                  , date("d"));
        return $ret;
    }
}
?>
