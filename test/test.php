<!DOCTYPE html>
<head>
	<script src="/design_template/js/lib/jquery-1.11.2.min.js"></script>
	<script src="./Common/js/product.js"></script>
</head>
<body>

<?
require_once('./Common/DPrintingFactory.php');
require_once('./Common/PrintoutInterface.php');
require_once('./BasicMaterials/Paper.php');
require_once('./BasicMaterials/Option.php');
require_once('./BasicMaterials/Afterprocess.php');

$factory = new DPrintingFactory();
$product = $factory->create('001001001');


//해당 상품의 html문을 출력
echo $product->makeHtml();


// 1. sortcode를 받으면 선택가능한 종이, 후공정, 옵션의 내용을 보여줄것.

//2. 상품의 옵션(종이, 옵션, 후공정)들을 받아서 DB의 내용을 불러와 가격을 출력
/*
$product = new Paper($product);
$product->setPaper('001001001','60000','1','2','단면 4도', '일반옵셋'); // $cate_sortcode, $amt, $stan_mpcode, $paper_mpcode, $print_name, $print_purp

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
</body>
