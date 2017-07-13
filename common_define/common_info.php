<?
//관리자 hash
const ADMIN_FLAG = array(
        "ADMIN");

//메일 도메인
const EMAIL_DOMAIN = array(
        "naver.com",
        "nate.com",
        "gmail.com",
        "hanmail.net",
        "daum.net",
        "hotmail.com");

//전화 앞번호
const TEL_NUM = array(
        "02",
        "031",
        "032",
        "033",
        "041",
        "042",
        "043",
        "044",
        "051",
        "052",
        "053",
        "054",
        "055",
        "061",
        "062",
        "063",
        "064");


//휴대전화 앞번호
const CEL_NUM = array(
        "010",
        "017");

//운영체제
const OPER_SYS = array(
        "IBM",
        "MAC");

//정산 입력 구분
const INSERT_DVS = array(
        "dvs" => array(
            "매출차감",
            "에누리적립"   
        ),
        "매출차감" => array(

            "제품구입(수기입력)",
            "사고처리",
            "별도견적",
            "재단비",
            "후가공",
            "배송비",
            "기타"
        ),
        "에누리적립" => array(

            "주문취소",
            "사고처리",
            "별도견적",
            "DC",
            "후가공차감",
            "기타"
        )
);
?>

