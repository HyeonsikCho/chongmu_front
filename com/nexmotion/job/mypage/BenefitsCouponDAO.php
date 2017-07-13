<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MypageCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/BenefitsCouponHTML.php');

class BenefitsCouponDAO extends MypageCommonDAO {
 
    /**
     * @brief 쿠폰 내역 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCpList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);
        $today = date("Y-m-d H:i:s", time());

        if ($type === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n    SELECT  A.cp_name";
            $query .= "\n           ,A.val";
            $query .= "\n           ,A.unit";
            $query .= "\n           ,A.max_sale_price";
            $query .= "\n           ,A.min_order_price";
            $query .= "\n           ,A.public_start_date";
            $query .= "\n           ,B.use_yn";
            $query .= "\n           ,B.issue_date";
            $query .= "\n           ,B.use_deadline";
            $query .= "\n           ,B.cp_issue_seqno";
        }
        $query .= "\n      FROM  cp A";
        $query .= "\n           ,cp_issue B";
        $query .= "\n     WHERE  B.member_seqno = " . $param["seqno"];
        $query .= "\n       AND  A.cp_seqno = B.cp_seqno";
        $query .= "\n       AND  B.use_yn = 'N'";
        $query .= "\n       AND  B.use_able_start_date <= '" . $today . "'";

        //상태
        if ($this->blankParameterCheck($param ,"state")) {
            
            $state = substr($param["state"], 1, -1);

            //미사용 사용가능한 쿠폰
            if ($state == 1) {

                $query .= "\n       AND B.use_able_start_date <= '";
                $query .= $today . "'";
                $query .= "\n       AND B.use_deadline >= '" . $today . "'";

            //기한 만료된 쿠폰
            } else  {

                $query .= "\n       AND (B.use_able_start_date > '";
                $query .= $today . "'";
                $query .= "\n        OR B.use_deadline < '" . $today . "')";
            }

        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($type == "SEQ") {

            $query .= "\n ORDER BY B.use_deadline DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;

        }

        return $conn->Execute($query);
    }
}
?>
