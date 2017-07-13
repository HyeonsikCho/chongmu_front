<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MemberCommonDAO.php');

class MemberFindPwDAO extends MemberCommonDAO {
    function __construct() {
    }
 
    /**
     * @brief 비밀번호 찾기
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectFindPw($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $search_cnd = substr($param["search_cnd"], 1, -1);

        $query  = "\nSELECT  member_seqno ";
        $query .= "\n  FROM  member ";
        $query .= "\n WHERE  member_name = $param[member_name] ";
        $query .= "\n   AND  member_id = $param[member_id] ";
        $query .= "\n   AND  $search_cnd = $param[search_txt]";

        return $conn->Execute($query);
    }

    /**
     * @brief 비밀번호 찾기 결과 정보
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectFindPwInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $search_cnd = substr($param["search_cnd"], 1, -1);

        $query  = "\nSELECT  member_id ";
        $query .= "\n  FROM  member ";
        $query .= "\n WHERE  1 = 1 ";

        //일련번호 검색
        if ($this->blankParameterCheck($param ,"seqno")) {
            $query .= "\n   AND  member_seqno = $param[seqno]";
        }
 
        return $conn->Execute($query);
    }
     
    /**
     * @brief 회원정보 비밀번호 변경
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateMemberPw($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param["final_modi_date"] = date("Y-m-d H:i:s");

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
  
        $query  = "\n    UPDATE  member ";
        $query .= "\n       SET  passwd = %s ";
        $query .= "\n           ,final_modi_date = %s ";
        $query .= "\n     WHERE  member_seqno = %s ";

        $query = sprintf($query, $param["passwd"],
                         $param["final_modi_date"],
                         $param["member_seqno"]);

        $resultSet = $conn->Execute($query);
 
        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }
}
?>
