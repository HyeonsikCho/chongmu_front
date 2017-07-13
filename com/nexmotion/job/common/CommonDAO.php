<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/common/MakeCommonHtml.php');

/*! 공통 DAO Class */
class CommonDAO {

    var $errorMessage = "";

    function __construct() {
    }

    /**
     * @brief 다중 데이터 수정 쿼리 함수 (공통) <br>
     *        param 배열 설명 <br>
     *        $param : <br>
     *        $param["table"] = "테이블명"<br>
     *        $param["col"]["컬럼명"] = "수정데이터" (다중)<br>
     *        $param["prk"] = "primary key colulm"<br>
     *        $param["prkVal"] = "primary data"  ex) 1,2,3,4
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */
    function updateMultiData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" ||
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $prkArr = str_replace(" ", "", $param["prkVal"]);
        $prkArr = str_replace("'", "", $prkArr);
        $prkArr = explode(",", $prkArr);

        $parkVal = "";

        for ($i = 0; $i < count($prkArr); $i++) {
            $prkVal .= $conn->qstr($prkArr[$i], get_magic_quotes_gpc()) . ",";
        }
        $prkVal = substr($prkVal, 0, -1);

        $query = "\n UPDATE " . $param["table"]  . " set";

        $i = 0;
        $col = "";
        $value = "";

        while (list($key, $val) = each($param["col"])) {

            $inchr = $conn->qstr($val,get_magic_quotes_gpc());

            if ($i == 0) {
                $value  .= "\n " . $key . "=" . $inchr;
            } else {
                $value  .= "\n ," . $key . "=" . $inchr;
            }

            $i++;
        }

        $query .= $value;
        $query .= " WHERE " . $param["prk"] . " in(";
        $query .= $prkVal . ")";

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            $errorMessage = "데이터 수정에 실패 하였습니다.";
            return false;
        } else {
            return true;
        }

    }

    /**
     * @brief 데이터 수정 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"]["컬럼명"] = "수정데이터" (다중)<br>
     *        $param["prk"] = "primary key colulm"<br>
     *        $param["prkVal"] = "primary data" <br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */
    function updateData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" ||
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query = "\n UPDATE " . $param["table"]  . " set";

        $i = 0;
        $col = "";
        $value = "";

        while (list($key, $val) = each($param["col"])) {

            //   $inchr = $val;
            $inchr = $conn->qstr($val,get_magic_quotes_gpc());

            if ($i == 0) {
                $value  .= "\n " . $key . "=" . $inchr;
            } else {
                $value  .= "\n ," . $key . "=" . $inchr;
            }

            $i++;
        }

        $query .= $value;
        $query .= " WHERE " . $param["prk"] . "=" . $conn->qstr($param["prkVal"], get_magic_quotes_gpc());

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * @brief 데이터 삽입 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"]["컬럼명"] = "데이터" (다중)<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */
    function insertData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" ||
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query = "\n INSERT INTO " . $param["table"] . "(";

        $i = 0;
        $col = "";
        $value = "";

        while (list($key, $val) = each($param["col"])) {

            $inchr = $conn->qstr($val,get_magic_quotes_gpc());

            if ($i == 0) {
                $col  .= "\n " . $key;
                $value  .= "\n " . $inchr;
            } else {
                $col  .= "\n ," . $key;
                $value  .= "\n ," . $inchr;
            }

            $i++;
        }

        $query .= $col;
        $query .= "\n ) VALUES (";
        $query .= $value;
        $query .= "\n )";

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            $errorMessage = "데이터 입력에 실패 하였습니다.";
            return false;
        } else {
            return true;
        }
    }

	/**
     * @brief 카테고리 검색
     *
     * @param $conn = connection identifier
     * @param $sortcode = connection identifier
     *
     * @return 검색결과
     */
    function selectCateList($conn, $sortcode = null) {
        $param = array();
        $param["col"]   = "sortcode, cate_name";
        $param["table"] = "cate_for_esti";
        if ($sortcode === null) {
            $param["where"]["cate_level"] = "1";
        } else {
            $param["where"]["high_sortcode"] = $sortcode;
        }

        $rs = $this->selectData($conn, $param);

        $basic_option = "대분류(전체)";

        if (strlen($sortcode) === 3) {
            $basic_option = "중분류(전체)";
        } else if (strlen($sortcode) === 6) {
            $basic_option = "소분류(전체)";
        }

        //return makeEstiOptionHtml($rs, $basic_option);
		return makeEstiOptionHtml($rs, "sortcode", "cate_name", $basic_option);
    }

    /**
     * @brief 데이터 삽입/수정 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"]["컬럼명"] = "데이터" (다중)<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */
    function replaceData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" ||
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query = "\n INSERT INTO " . $param["table"] . "(";

        $i = 0;
        $col = "";
        $value = "";

        while (list($key, $val) = each($param["col"])) {

            $inchr = $conn->qstr($val,get_magic_quotes_gpc());
            if ($i == 0) {
                $col  .= "\n " . $key;
                $value  .= "\n " . $inchr;
            } else {
                $col  .= "\n ," . $key;
                $value  .= "\n ," . $inchr;
            }

            $i++;
        }

        $query .= $col;
        $query .= "\n ) VALUES (";
        $query .= $value;
        $query .= "\n )";
        $query .= "\n ON DUPLICATE KEY UPDATE";

        $i = 0;
        $col = "";
        $value = "";

        reset($param["col"]);

        while (list($key, $val) = each($param["col"])) {

            $inchr = $conn->qstr($val,get_magic_quotes_gpc());

            if ($i == 0) {
                $value  .= "\n " . $key . "=" . $inchr;
            } else {
                $value  .= "\n ," . $key . "=" . $inchr;
            }

            $i++;
        }
        $query .= $value;

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            $errorMessage = "데이터 입력에 실패 하였습니다.";
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 다중 데이터 삭제 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["prk"] = "primary key colulm" <br>
     *        $param["prkVal"] = "primary data"  ex) 1,2,3,4 <br>
     *        $param["not"] = "제외 검색"  ex) Y<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */
    function deleteMultiData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" ||
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query  = "\n DELETE ";
        $query .= "\n   FROM " . $param["table"];
        $query .= "\n  WHERE " . $param["prk"];
        $query .= "\n     IN (";

        $prkValCount = count($param["prkVal"]);
        for ($i = 0; $i < $prkValCount; $i++) {
            $val = $conn->qstr($param["prkVal"][$i], get_magic_quotes_gpc());
            $query .= $val;

            if ($i !== $prkValCount - 1) {
                $query .= ",";
            }
        }
        $query .= ")";

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            $errorMessage = "데이터 삭제에 실패 하였습니다.";
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 데이터 삭제 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["prk"] = "primary key column"<br>
     *        $param["prkVal"] = "primary data" <br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */
    function deleteData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" ||
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query  = "\n DELETE ";
        $query .= "\n   FROM " . $param["table"];
        $query .= "\n  WHERE " . $param["prk"];
        $query .= "\n       =" . $conn->qstr($param["prkVal"], get_magic_quotes_gpc());

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            $errorMessage = "데이터 삭제에 실패 하였습니다.";
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 전체 데이터 삭제 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["prk"] = "primary key colulm"<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */
    function deleteAllData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" ||
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query  = "\n DELETE ";
        $query .= "\n   FROM " . $param["table"];
        $query .= "\n  WHERE " . $param["prk"] . " >= 0";

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            $errorMessage = "데이터 삭제에 실패 하였습니다.";
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief DISTINCT 데이터 검색 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"] = "컬럼명"<br>
     *        $param["where"]["컬럼명"] = "조건" (다중)<br>
     *        $param["order"] = "ORDER BY 컬럼"<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */
    function distinctData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" ||
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query = "\n SELECT DISTINCT " . $param["col"] . " FROM " . $param["table"];
        $i = 0;
        $value = "";

        if ($param["where"]) {

            while (list($key, $val) = each($param["where"])) {

                $inchr = $conn->qstr($val, get_magic_quotes_gpc());

                if ($i == 0) {
                    $value  .= "\n WHERE " . $key . "=" . $inchr;
                } else {
                    $value  .= "\n   AND " . $key . "=" . $inchr;
                }
                $i++;
            }
        }

        $query .= $value;

        if ($param["order"]) {
            $query .= "\n ORDER BY " . $param["order"];
        }

        //Query Cache
        if ($param["cache"] == 1) {
            $rs = $conn->CacheExecute(1800, $query);
        } else {
            $rs = $conn->Execute($query);
        }

        return $rs;
    }

    /**
     * @brief 데이터 검색 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"] = "컬럼명"<br>
     *        $param["where"]["컬럼명"] = "조건" (다중)<br>
     *        $param["not"]["컬럼명"] = "조건" (다중)<br>
     *        $param["order"] = "order by 조건"<br>
     *        $param["group"] = "group by 조건"<br>
     *        $param["cache"] = "1" 캐쉬 생성<br>
     *        $param["limit"]["start"] = 리미트 시작값<br>
     *        $param["limit"]["end"] =  리미트 종료값<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */
    function selectData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" ||
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query = "\n SELECT " . $param["col"] . " FROM " . $param["table"];

        $i = 0;
        $col = "";
        $value = "";

        if ($param["where"]) {

            while (list($key, $val) = each($param["where"])) {

                $inchr = $conn->qstr($val,get_magic_quotes_gpc());

                if ($i == 0) {
                    $value  .= "\n WHERE " . $key . "=" . $inchr;
                } else {
                    $value  .= "\n   AND " . $key . "=" . $inchr;
                }
                $i++;
            }
        }

        //임시로 만듬
        if ($param["not"]) {

            while (list($key, $val) = each($param["not"])) {

                $inchr = $conn->qstr($val,get_magic_quotes_gpc());
                $value  .= "\n AND NOT " . $key . "=" . $inchr;
                $i++;
            }
        }

        //like search
        if ($param["like"]) {

            while (list($key, $val) = each($param["like"])) {

                $inchr = substr($conn->qstr($val,get_magic_quotes_gpc()),1, -1);

                if ($i == 0) {
                    $value  .= "\n WHERE " . $key . " LIKE '%" . $inchr . "%'";
                } else {
                    $value  .= "\n   AND " . $key . " LIKE '%" . $inchr . "%'";
                }
                $i++;
            }
        }

        $query .= $value;

        if ($param["group"]) {
            $query .= "\n GROUP BY " . $param["group"];
        }

        if ($param["order"]) {
            $query .= "\n ORDER BY " . $param["order"];
        }

        if ($param["limit"]) {

            $query .= "\n LIMIT " . $param["limit"]["start"] . ",";
            $query .= $param["limit"]["end"];
        }
        //Query Cache
        if ($param["cache"] == 1) {
            $rs = $conn->CacheExecute(1800, $query);
        } else {
            $rs = $conn->Execute($query);
        }

        return $rs;
    }

    /**
     * @brief COUNT 데이터 검색 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["where"]["컬럼명"] = "조건" (다중)<br>
     *        $param["cache"] = "1" 캐쉬 생성<br>
     *        $param["limit"]["start"] = 리미트 시작값<br>
     *        $param["limit"]["end"] =  리미트 종료값<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */
    function countData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        $query = "\n SELECT count(*) cnt  FROM " . $param["table"];

        $i = 0;
        $col = "";
        $value = "";

        if ($param["where"]) {

            while (list($key, $val) = each($param["where"])) {

                $inchr = $conn->qstr($val,get_magic_quotes_gpc());

                if ($i == 0) {
                    $value  .= "\n WHERE " . $key . "=" . $inchr;
                } else {
                    $value  .= "\n   AND " . $key . "=" . $inchr;
                }
                $i++;
            }
        }

        if ($param["like"]) {

            while (list($key, $val) = each($param["like"])) {

                $inchr = $conn->qstr($val,get_magic_quotes_gpc());

                if ($i == 0) {
                    $value  .= "\n WHERE " . $key . " LIKE " . $inchr;
                } else {
                    $value  .= "\n   AND " . $key . " LIKE " . $inchr;
                }
                $i++;
            }
        }

        if ($param["group"]) {
            $query .= "\n GROUP BY " . $param["group"];
        }

        if ($param["limit"]) {

            $query .= "\n LIMIT " . $param["limit"]["start"] . ",";
            $query .= $param["limit"]["end"];
        }

        $query .= $value;

        $rs = $conn->Execute($query);
        return $rs;

    }

    /**
     * @brief 커넥션 검사
     * @param $conn DB Connection
     * @return boolean
     */
    function connectionCheck($conn) {
        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        return true;
    }

    /**
     * @brief SQL 인젝션 방지용
     *
     * @param $conn  = DB Connection
     * @param $param = 검색조건
     *
     * @return 변환 된 인자
     */
    function parameterEscape($conn, $param) {
        $param = htmlspecialchars($param, ENT_QUOTES, "UTF-8", false);
        $ret = $conn->qstr($param, get_magic_quotes_gpc());
        return $ret;
    }

    /**
     * @brief SQL 인젝션 방지용, 배열
     *
     * @detail $except_arr 배열은 $except["제외할 필드명"] = true
     * 형식으로 입력받는다.
     *
     * @param $conn       = DB Connection
     * @param $param      = 검색조건 배열
     * @param $except_arr = 이스케이프 제외할 필드명
     *
     * @return 변환 된 배열
     */
    function parameterArrayEscape($conn, $param, $except_arr = null) {
        if (!is_array($param)) return false;

        $arrSize = count($param);

        while (list($key, $val) = each($param)) {
            if ($except_arr[$key] === true) {
                continue;
            }

            if (is_array($val)) {
                $val = $this->parameterArrayEscape($conn, $val, $except_arr);
            } else {
                $val = $this->parameterEscape($conn, $val);
            }

            $param[$key] = $val;
        }

        return $param;
    }

    /**
     * @brief NULL 이거나 공백값('')이 아닌 파라미터만 체크
     * @param $param 임의의 배열 인자
     * @param $key 임의의 배열 인자의 키
     * @return boolean
     */
    function blankParameterCheck($param, $key) {
        // 파라미터가 빈 값이 아닐경우
        if ($param !== ""
                && empty($param[$key]) !== true
                && $param[$key] !== "''"
                && $param[$key] !== "NULL"
                && $param[$key] !== "null") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @brief CUD 실패시 입력된 에러메시지 반환
     * @return 에러메시지
     */
    function getErrorMessage() {
        return $errorMessage;
    }

    /**
     * @brief 캐쉬를 삭제하는 함수
     * @param $conn DB Connection
     */
    function cacheFlush($conn) {
        $conn->CacheFlush();
    }

    /**
     * @brief 카테고리 검색
     *
     * @param $conn         = connection identifier
     * @param $sel_sortcode = html 선택으로 표시할 분류코드
     * @param $sortcode     = 검색조건 분류코드
     *
     * @return 검색결과
     */
    function selectCateHtml($conn, $sel_sortcode, $sortcode = null) {
        $param = array();
        $param["col"]   = "sortcode, cate_name";
        $param["table"] = "cate";
        if ($sortcode === null) {
            $param["where"]["cate_level"] = "1";
        } else {
            $param["where"]["high_sortcode"] = $sortcode;
        }

        $param["order"] = "sortcode";

        $rs = $this->selectData($conn, $param);

        $arr = array();
        $arr["val"] = "sortcode";
        $arr["dvs"] = "cate_name";
        $arr["sel"] = $sel_sortcode;

        return makeOptionHtml($rs, $arr);
    }

    /*
     * @brief 지번 주소 Select
     * @param $conn : DB Connection
     * @param $param["val"] : 지번 검색어

     * @param $param["area"] : 지역
     * @return : resultSet
     */
    function selectJibunZip($conn, $param) {

        if (!$this->connectionCheck($conn)) return false;
        $param = $this->parameterArrayEscape($conn, $param);
        $area = substr($param["area"], 1, -1);
        $val = substr($param["val"], 1, -1);

        $query  = "\n    SELECT  zipcode";
        $query .= "\n           ,sido";
        $query .= "\n           ,gugun";
        $query .= "\n           ,eup";
        $query .= "\n           ,dong";
        $query .= "\n           ,bldg";
        $query .= "\n           ,jibun_bonbun";
        $query .= "\n           ,jibun_bubun";
        $query .= "\n           ,bldg";
        $query .= "\n           ,ri";
        $query .= "\n      FROM  " . $area . "_zipcode";
        $query .= "\n     WHERE  (dong LIKE '%" . $val . "%'";
        $query .= "\n        OR   eup LIKE '%" . $val . "%'";
        $query .= "\n        OR   ri LIKE '%" . $val . "%')";

        $result = $conn->Execute($query);

        return $result;
    }

    /*
     * @brief 도로명 주소 Select
     * @param $conn : DB Connection
     * @param $param["val"] : 지번 검색어
     * @param $param["area"] : 지역
     * @return : resultSet
     */
    function selectDoroZip($conn, $param) {

        if (!$this->connectionCheck($conn)) return false;
        $param = $this->parameterArrayEscape($conn, $param);
        $area = substr($param["area"], 1, -1);
        $val = substr($param["val"], 1, -1);

        $query  = "\n    SELECT  zipcode";
        $query .= "\n           ,sido";
        $query .= "\n           ,gugun";
        $query .= "\n           ,doro";
        $query .= "\n           ,bldg";
        $query .= "\n           ,bldg_bonbun";
        $query .= "\n           ,bldg_bubun";
        $query .= "\n      FROM  " . $area . "_zipcode";
        $query .= "\n     WHERE  doro LIKE '%" . $val .  "%'";

        $result = $conn->Execute($query);

        return $result;
    }

    /**
     * @brief 아이디와 비밀번호로 회원 정보 검색
     *
     * @detail $param["id"] = 회원 아이디
     * $param["seqno"] = 회원 일련번호
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectMember($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query = "\n   SELECT  A.member_seqno ";      /* 회원일련번호 */
        $query .= "\n          ,A.member_name ";       /* 회원명 */
        $query .= "\n          ,A.member_id ";         /* 회원아이디 */
        $query .= "\n          ,A.group_id ";          /* 그룹회원일련번호 */
        $query .= "\n          ,A.group_name ";        /* 그룹명 */
        $query .= "\n          ,A.member_photo_path "; /* 회원사진경로 */
        $query .= "\n          ,A.grade ";             /* 등급 */
        $query .= "\n          ,B.bank_name ";         /* 은행명 */
        $query .= "\n          ,B.ba_num ";            /* 가상계좌번호 */
        $query .= "\n          ,A.member_dvs ";        /* 회원구분 */
        $query .= "\n          ,A.member_typ ";        /* 회원종류 */
        $query .= "\n          ,A.own_point ";         /* 보유포인트 */
        $query .= "\n          ,A.prepay_price ";      /* 선입금액 */
        $query .= "\n          ,A.order_lack_price ";  /* 주문부족금액 */
        $query .= "\n          ,A.biz_resp ";          /* 영업담당부서 */
        $query .= "\n          ,A.release_resp ";      /* 출고담당부서 */
        $query .= "\n          ,A.dlvr_resp ";         /* 배송담당부서 */
        $query .= "\n          ,A.passwd ";            /* 비밀번호 */
        $query .= "\n          ,A.cumul_sales_price "; /* 누적매출금액 */
        $query .= "\n          ,A.onefile_etprs_yn";   /* 원파일업체여부 */
        $query .= "\n          ,A.card_pay_yn";        /* 카드결제여부 */
        $query .= "\n     FROM  member  AS A ";
        $query .= "\nLEFT JOIN  virt_ba_admin AS B ";
        $query .= "\n       ON  A.member_seqno = B.member_seqno ";
        $query .= "\n    WHERE  1 = 1";
        $query .= "\n      AND  withdraw_dvs = 1";
        if ($this->blankParameterCheck($param, "id")) {
            $query .= "\n      AND  A.member_id = " . $param["id"];
        }
        if ($this->blankParameterCheck($param, "seqno")) {
            $query .= "\n      AND  A.member_seqno = " . $param["seqno"];
        }

        return $conn->Execute($query);
    }

    /**
     * @brief 담당부서코드로 팀장전화번호 검색
     *
     * @detail $param["biz_resp"] = 영업담당 부서코드
     * $param["release_resp"] = 출고담당 부서코드
     * $param["dlvr_resp"] = 배송담당 부서코드
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectRespTellNum($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  A1.tel_num AS biz_tel";
        $query .= "\n        ,A2.tel_num AS release_tel";
        $query .= "\n        ,A3.tel_num AS dlvr_tel";

        $query .= "\n   FROM  empl      AS A1";
        $query .= "\n        ,empl      AS A2";
        $query .= "\n        ,empl      AS A3";
        $query .= "\n        ,job_admin AS B";

        $query .= "\n  WHERE  A1.job_code = B.job_code";
        $query .= "\n    AND  A2.job_code = B.job_code";
        $query .= "\n    AND  A3.job_code = B.job_code";
        $query .= "\n    AND  B.job_code = '001'";
        $query .= "\n    AND  A1.depar_code = %s";
        $query .= "\n    AND  A2.depar_code = %s";
        $query .= "\n    AND  A3.depar_code = %s";

        $query  = sprintf($query, $param["biz_resp"]
                                , $param["release_resp"]
                                , $param["dlvr_resp"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 쿠폰 매수 검색
     *
     * @param $conn  = connection identifier
     * @param $Seqno = 회원 일련번호
     *
     * @return 쿠폰 매수
     */
    function selectMemberCpCount($conn, $seqno) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $seqno = $this->parameterEscape($conn, $seqno);

        $query = "\nSELECT  COUNT(*) AS cp_count ";
        $query .= "\n  FROM  cp_use_history AS A ";
        $query .= "\n WHERE  member_seqno = %s";
        $query .= "\n   AND  A.use_yn = 'N'";

        $query  = sprintf($query, $seqno);

        $rs = $conn->Execute($query);

        return $rs->fields["cp_count"];
    }

    /**
     * @brief 주문 요약 배열 생성
     *
     * @detail $param["seqno"] = 회원일련번호
     * $param["date"] = 요약범위
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건파라미터
     *
     * @return 쿠폰 매수
     */
    function selectOrderSummary($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  A.order_state";
        $query .= "\n        ,count(1) AS state_count";

        $query .= "\n   FROM  order_common AS A";

        $query .= "\n  WHERE  A.member_seqno = %s";
        $query .= "\n    AND  A.order_regi_date BETWEEN %s AND %s";

        $query .= "\n  GROUP BY order_state";

        $query  = sprintf($query, $param["seqno"]
                                , $param["start_date"]
                                , $param["end_date"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 카테고리 종이 정보 검색
     *
     * @param $conn   = connection identifier
     * @param $mpcode = 카테고리 종이 맵핑코드
     * @param $col    = 상품종이에서 검색할 필드
     *
     * @return 종이 기준단위
     */
    function selectCatePaperInfo($conn, $mpcode) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"]   = "name, dvs, color, basisweight";
        $temp["table"] = "cate_paper";
        $temp["where"]["mpcode"] = $mpcode;

        $rs = $this->selectData($conn, $temp);

        return $rs->fields;
    }

    /**
     * @brief 도수명과 인쇄용도로 카테고리 인쇄 맵핑코드 검색
     *
     * @detail $param["name"] = 카테고리 후공정 맵핑코드
     * $param["purp_dvs"] = 판매채널
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCatePrintMpcode($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  B.mpcode";

        $query .= "\n   FROM  prdt_print AS A";
        $query .= "\n        ,cate_print AS B";

        $query .= "\n  WHERE  A.prdt_print_seqno = B.prdt_print_seqno";
        $query .= "\n    AND  A.name          = %s";
        $query .= "\n    AND  A.purp_dvs      = %s";
        $query .= "\n    AND  B.cate_sortcode = %s";
        if ($this->blankParameterCheck($param, "side_dvs")) {
            $query .= "\n    AND  A.side_dvs      = " . $param["side_dvs"];
        }

        $query  = sprintf($query, $param["name"]
                                , $param["purp_dvs"]
                                , $param["cate_sortcode"]);
        $rs = $conn->Execute($query);
        return $rs->fields["mpcode"];
    }


    /**
     * @brief 결제확인 팝업용 사용자 기본정보 검색
     *
     * @param $conn = connection identifier
     * @param $member_seqno = 사용자 일련번호
     *
     * @return 검색결과
     */
    function selectMemberInfo($conn, $member_seqno) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $member_seqno = $this->parameterEscape($conn, $member_seqno);

        $query  = "\n SELECT  A.mail";
        $query .= "\n        ,A.tel_num";
        $query .= "\n        ,A.cell_num";
        $query .= "\n        ,A.zipcode";
        $query .= "\n        ,A.addr";
        $query .= "\n        ,A.addr_detail";
        $query .= "\n   FROM  member      AS A";
        $query .= "\n  WHERE  A.member_seqno = %s";

        /*
        $query  = "\n SELECT  A.mail";
        $query .= "\n        ,B.tel_num";
        $query .= "\n        ,B.cell_num";
        $query .= "\n        ,B.zipcode";
        $query .= "\n        ,B.addr";
        $query .= "\n        ,B.addr_detail";
        $query .= "\n   FROM  member      AS A";
        $query .= "\n        ,member_dlvr AS B";
        $query .= "\n  WHERE  A.member_seqno = B.member_seqno";
        $query .= "\n    AND  A.member_seqno = %s";
        $query .= "\n    AND  B.basic_yn = 'Y'";
        */

        $query  = sprintf($query, $member_seqno);

        $rs = $conn->Execute($query);

        return $rs->fields;
    }
}
?>
