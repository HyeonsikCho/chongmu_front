<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/excel/PHPExcel/IOFactory.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/product/common/esti_pop_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");

$base_path = $_SERVER["DOCUMENT_ROOT"] . ESTI_EXCEL;
$file_name = null;

if (empty($param["common_cate_name"]) === false) {
    $file_name = "esti_sample_catabro";
    /*
    } else if ($param["cate_name"] === "독판전단(주문형)") {
        $file_name = "esti_sample_calc";
    */
} else {
    $file_name = "esti_sample_ply";
}

$input_file = $base_path . $file_name .".xlsx";

$objPHPExcel = PHPExcel_IOFactory::load($input_file);

$sheet = $objPHPExcel->getActiveSheet();

$cate_name_arr = $param["cate_name_arr"];
$paper_arr     = $param["paper_arr"];
$size_arr      = $param["size_arr"];
$tmpt_arr      = $param["tmpt_arr"];
$amt_arr       = $param["amt_arr"];
$amt_unit_arr  = $param["amt_unit_arr"];
$count_arr     = $param["count_arr"];
$after_arr     = $param["after_arr"];

//! 최상단 공통정보
// 견적일
$sheet->setCellValue("C5", sprintf("견적일: %s년 %s월 %s일", $param["year"]
    , $param["month"]
    , $param["day"]));
// 회원명
$sheet->setCellValue("E6", $param["member_name"]);
// 회원전화번호
$sheet->setCellValue("D7", $param["member_tel"]);
// 사업장소재지
$sheet->setCellValue("K5", $param["addr"] . ' ' . $param["addr_detail"]);
// 상호
$sheet->setCellValue("K6", $param["sell_site"]);
// 대표자성명
$sheet->setCellValue("K7", $param["repre_name"]);
// 대표번호
$sheet->setCellValue("K8", $param["repre_num"]);
// 합계금액
$sheet->setCellValue("K11", "\\ " . $param["sum_price"]);


if (empty($param["common_cate_name"]) === false) {
    //! 카탈로그 브로셔

    // 표지 품명
    $sheet->setCellValue("E14", $param["common_cate_name"] . " - " . $cate_name_arr[0]);
    // 표지 재질
    $sheet->setCellValue("E15", $paper_arr[0]);
    // 표지 사이즈
    $sheet->setCellValue("E16", $size_arr[0]);
    // 표지 안쇄도수
    $sheet->setCellValue("E17", $tmpt_arr[0]);
    // 표지 수량
    $sheet->setCellValue("E18", $amt_arr[0] . $amt_unit_arr[0] . " x " . $count_arr[0] . "건");
    // 표지 후공정
    $sheet->setCellValue("E28", $after_arr[0]);

    // 내지 품명
    $sheet->setCellValue("E21", $param["common_cate_name"] . " - " . $cate_name_arr[1]);
    // 내지 재질
    $sheet->setCellValue("E22", $paper_arr[1]);
    // 내지 사이즈
    $sheet->setCellValue("E23", $size_arr[1]);
    // 내지 안쇄도수
    $sheet->setCellValue("E24", $tmpt_arr[1]);
    // 내지 수량
    $sheet->setCellValue("E25", $amt_arr[1] . $amt_unit_arr[1] . " x " . $count_arr[1] . "건");
    // 내지 후공정
    $sheet->setCellValue("E29", $after_arr[1]);

    // 종이비
    $sheet->setCellValue("E32", "\\" . $param["paper_price"]);
    // 인쇄비
    $sheet->setCellValue("E33", "\\" . $param["print_price"]);
    // 출력비
    $sheet->setCellValue("E34", "\\" . $param["output_price"]);
    // 후공정비
    $sheet->setCellValue("E35", "\\" . $param["after_price"]);
    // 주문건수(표지)
    $sheet->setCellValue("E36", $count_arr[0] . "건");
    // 주문건수(내지)
    $sheet->setCellValue("E37", $count_arr[1] . "건");
    // 합계
    $sheet->setCellValue("E38", "\\ " . $param["sum_price"]);

    // 담당자명
    $sheet->setCellValue("J40", $param["member_mng"]);
    // 담당자 연락처
    $sheet->setCellValue("K40", $param["member_mng_tel"]);
    /*
    } else if ($param["cate_name"] === "독판전단(주문형)") {
        //! 독판전단

        // 품명
        $sheet->setCellValue("E14", $cate_name_arr[0]);
        // 재질
        $sheet->setCellValue("E15", $paper_arr[0]);
        // 사이즈
        $sheet->setCellValue("E16", $size_arr[0]);
        // 안쇄도수
        $sheet->setCellValue("E17", $tmpt_arr[0]);
        // 수량
        $sheet->setCellValue("E18", $amt_arr[0] . $amt_unit_arr[0] . " x " . $count_arr[0] . "건");
        // 후공정
        $sheet->setCellValue("C21", $after_arr[0]);

        // 종이비
        $sheet->setCellValue("E24", "\\" . $param["paper_price"]);
        // 인쇄비
        $sheet->setCellValue("E25", "\\" . $param["print_price"]);
        // 출력비
        $sheet->setCellValue("E26", "\\" . $param["output_price"]);
        // 후공정비
        $sheet->setCellValue("E27", "\\" . $param["after_price"]);
        // 주문건수
        $sheet->setCellValue("E28", $count_arr[0] . "건");
        // 합계
        $sheet->setCellValue("E29", "\\ " . $param["sum_price"]);

        // 담당자명
        $sheet->setCellValue("J31", $param["member_mng"]);
        // 담당자 연락처
        $sheet->setCellValue("K31", $param["member_mng_tel"]);
    */
} else {
    //! 그 외

    // 품명
    $sheet->setCellValue("E14", $cate_name_arr[0]);
    // 재질
    $sheet->setCellValue("E15", $paper_arr[0]);
    // 사이즈
    $sheet->setCellValue("E16", $size_arr[0]);
    // 안쇄도수
    $sheet->setCellValue("E17", $tmpt_arr[0]);
    // 수량
    $sheet->setCellValue("E18", $amt_arr[0] . $amt_unit_arr[0] . " x " . $count_arr[0] . "건");
    // 후공정
    $sheet->setCellValue("C21", $after_arr[0]);

    // 인쇄비
    $sheet->setCellValue("E24", "\\" . $param["print_price"]);
    // 후공정비
    $sheet->setCellValue("E25", "\\" . $param["after_price"]);
    // 주문건수
    $sheet->setCellValue("E26", $count_arr[0] . "건");
    // 합계
    $sheet->setCellValue("E27", "\\ " . $param["sum_price"]);

    // 담당자명
    $sheet->setCellValue("J29", $param["member_mng"]);
    // 담당자 연락처
    $sheet->setCellValue("K29", $param["member_mng_tel"]);
}

$save_name = uniqid();

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($base_path . $save_name . ".xlsx");

$objPHPExcel->disconnectWorksheets();
unset($objPHPExcel);

echo $save_name;
?>
