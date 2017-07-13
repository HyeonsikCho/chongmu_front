<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/Common/DPrintingFactory.php');

$fb = new FormBean();

$sortcode = $fb->form("sortcode");    // 001001001
$stan_mpcode  = $fb->form("stan_mpcode");     // 1
$paper_mpcode    = $fb->form("paper_mpcode");       // 단면 4도
$print_name    = $fb->form("print_name");       // 일반옵셋
$print_purp   = $fb->form("print_purp");      // 1
$amt           = $fb->form("amt");                 // 500
$count           = $fb->form("count");
$manu_pos_num           = $fb->form("manu_pos_num");
$opt_name   = explode("|", $fb->form("opt_name_list"));
$opt_mpcode   = explode("|", $fb->form("opt_mp_list"));
$after_name   = explode("|", $fb->form("after_name_list"));
$after_mpcode   = explode("|", $fb->form("after_mp_list"));
$paper_depth 	= explode("|", $fb->form("paper_depth"));
$depth1			= explode("|", $fb->form("depth1"));
$depth2			= explode("|", $fb->form("depth2"));
$depth3			= explode("|", $fb->form("depth3"));

$count = $count * $manu_pos_num;

$factory = new DPrintingFactory();
$product = $factory->create($sortcode);

$product = $factory->createPaper($product, $sortcode);
$product->setPaper($sortcode, $amt, $count, $stan_mpcode, $paper_mpcode, $print_name, $print_purp, $paper_depth[0]); // $cate_sortcode, $amt, $stan_mpcode, $paper_mpcode, $print_name, $print_purp

$opt_count = count($opt_name);
for($i = 0; $i < $opt_count ; $i++) {
	if($opt_name[$i] == '') {
		break;
	}

	$product = new Option($product);
	$product->setOption($sortcode, $opt_name[$i] , $opt_mpcode[$i], $amt); // $sortcode ,$opt_name, $mpcode, $amt = ''
}

$after_json = "";
$after_count = count($after_name);
for($i = 0; $i < $after_count ; $i++) {
	if($after_name[$i] == '') {
		break;
	}

	$product = $factory->createAfter($product, $after_name[$i]);
	//$product->setAfterprocess($sortcode, $after_name[$i] , $amt, $after_mpcode[$i]);
	 $product->setAfterprocess($sortcode ,$after_name[$i], $amt, $count, $after_mpcode[$i], $depth1[$i], $depth2[$i], $depth3[$i]);

	$after_json .= $product->getJson();
}

$price = $product->cost();
$ret = "{\"cover\" : {\"price\" : \"%s\"%s}}";
echo sprintf($ret, $price, $after_json);

/*
$product = new Option($product);
$product->setOption('001001001', '로고디자인' , '1', '손글씨'); // $sortcode ,$after_name, $amt, $depth1='', $depth2='', $depth3 =''

$product = new Option($product);
$product->setOption('001001001', '명함디자인');

$product = new Afterprocess($product);
$product->setAfterProcess('005001001', '제본', '60', '중철제본', '가로상철', ''); // $sortcode ,$after_name, $amt, $depth1='', $depth2='', $depth3 =''

echo $product->getDescription() . "</br>-------------------------------</br>";
echo "합계금액 : " . $product->cost();
*/


/*구현해야할 기능
1. sortcode를 받으면 선택가능한 종이, 후공정, 옵션의 내용을 보여줄것.


2. 상품의 옵션(종이, 옵션, 후공정)들을 받아서 DB의 내용을 불러와 가격을 출력
->1) 종이, 옵션, 후공정의 가격정보를 가지고 오기위한 필수 정보들
--> 1. 종이 : sortcode, amt, stan_mpcode, paper_mpcode, print_name, print_purp
--> 2. 옵션 : sortcode, opt_name, depth1, depth2, depth3
--> 3. 후공정 : sortcode, after_name, depth1, depth2, depth3


3. 사용자가 주문하고자하는 상품의 정보들(종이, 옵션, 후공정 등)을 받아서 테이블에 저장



4. Product.php에서 상품의 유형을 결정 할 수있는지?
*/

?>