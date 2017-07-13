<?
/* adodb 캐쉬저장경로 설정 */
$ADODB_CACHE_DIR = dirname(__FILE__) . '/cache';
define("ADODB_CACHE_DIR", $ADODB_CACHE_DIR);

define("NO_IMAGE", "/design_template/images/no_image.jpg");
define("NO_IMAGE_THUMB", "/design_template/images/no_image_75_75.jpg");

define("TAX_RATE", 0.1);

/******************** 파일 저장 경로 ****************/
define("SHARE_LIBRARY_FILE", "attach/share_library_file");  //공유자료실 첨부파일
define("CLAIM_SAMPLE_FILE", "attach/claim_sample_file");  //클레임 견본 파일
define("SITE_DEFAULT_ESTI_FILE", "attach/esti_file"); //견적 파일
define("ORDER_TEMP_FILE", "attach/order_file"); //견적 파일
define("ORDER_USERUPLOAD_FILE", "attach/order_file_2.0/IBM"); //견적 파일
define("ORDER_ADMINUPLOAD_FILE", "attach/order_adminupload_file"); //견적 파일
define("SITE_DEFAULT_OTO_INQ_REPLY_FILE", "attach/oto_inq_reply_file");
/**************총무팀 기초값 설정********************/
//define("JC_ADDR","http://joochong.co.kr/"); // 실서버
define("JC_ADDR","http://ing3.plzm.kr/"); // 테스트서버
define("SITE_CD","00034");
define("AUTH_CD","GP331313");
define("SVC_NO","0038");


/************** 카드사 코드 *******************/
$card['029'] = '신한';
$card['007'] = '신한';
$card['027'] = '현대';
$card['031'] = '삼성';
$card['008'] = '하나(외환)';
$card['026'] = '비씨';
$card['016'] = '국민';
$card['045'] = '롯데';
$card['047'] = '롯데';
$card['018'] = 'NH농협';
$card['006'] = '하나';
$card['022'] = '씨티';
$card['036'] = '씨티';
$card['021'] = '우리';
$card['002'] = '광주';
$card['017'] = '수협';
$card['010'] = '전북';
$card['011'] = '제주';
$card['058'] = '산업';
$card['050'] = '해외VISA';
$card['028'] = '해외JCB';
$card['048'] = '해외다이너스';
$card['049'] = '해외Master';
$card['046'] = '해외Amex';
$card['081'] = '은련';



/*********** 가상계좌 은행 코드 ****************/
$bank['003'] = '기업은행';
$bank['004'] = '국민은행';
$bank['011'] = '농협중앙회';
$bank['020'] = '우리은행';
$bank['023'] = 'SC제일은행';
$bank['026'] = '신한은행';
$bank['032'] = '부산은행';
$bank['071'] = '우체국';
$bank['081'] = '하나은행';

define("ESTI_EXCEL", "/esti_sample/"); //책자조판_지시서공정

?>
