<?
/**
 * @breif 빠른 견적서 html 반환
 *
 * @param $info = 정보 배열
 *
 * @return 견적서 html
 */
function getQuickEstimateHtml($info) {
    $paper  = number_format($info["esti_paper"]);
    $output = number_format($info["esti_output"]);
    $print  = number_format($info["esti_print"]);
    $after  = number_format($info["esti_after"]);
    $opt    = number_format($info["esti_opt"]);
    $tax    = number_format($info["esti_tax"]);
    $sell_price = number_format($info["esti_sell_price"]);
    $sale_price = number_format($info["esti_sale_price"]);
    $org_sell_price = $info["esti_sell_price"] - $info["esti_tax"];
    $org_sell_price = number_format($org_sell_price);

    $attr = '';
    
    $html  = "\n <dl>";
    if ($paper === '0') {
        $attr = "style=\"display:none;\"";
    }
    $html .= "\n     <dt class=\"esti_paper_info\" " . $attr . ">종이비</dt>";
    $html .= "\n     <dd class=\"esti_paper_info\" " . $attr . ">";
    $html .= "\n         <span id=\"esti_paper\">" . $paper . "</span> 원";
    $html .= "\n     </dd>";

    $attr = '';
    if ($output === '0') {
        $attr = "style=\"display:none;\"";
    }
    $html .= "\n     <dt class=\"esti_output_info\" " . $attr . ">출력비</dt>";
    $html .= "\n     <dd class=\"esti_output_info\" " . $attr . ">";
    $html .= "\n         <span id=\"esti_output\">" . $output .  "</span> 원";
    $html .= "\n     </dd>";

    $attr = '';
    if ($print === '0') {
        $attr = "style=\"display:none;\"";
    }
    $html .= "\n     <dt class=\"esti_print_info\" " . $attr . ">인쇄비</dt>";
    $html .= "\n     <dd class=\"esti_print_info\" " . $attr . ">";
    $html .= "\n         <span id=\"esti_print\">" . $print . "</span> 원";
    $html .= "\n     </dd>";

    $html .= "\n     <dt>후공정</dt>";
    $html .= "\n     <dd><span id=\"esti_after\">" . $after . "</span> 원</dd>";
    $html .= "\n     <dt>옵션비</dt>";
    $html .= "\n     <dd><span id=\"esti_opt\">" . $opt . "</span> 원</dd>";
    $html .= "\n     <dt>주문건</dt>";
    $html .= "\n     <dd><span id=\"esti_count\">1</span> 건</dd>";
    $html .= "\n     <dt>계</dt>";
    $html .= "\n     <dd><span id=\"esti_sum\">" . $org_sell_price . "</span> 원</dd>";
    $html .= "\n     <dt>부가세</dt>";
    $html .= "\n     <dd><span id=\"esti_tax\">" . $tax . "</span> 원</dd>";
    $html .= "\n </dl>";
    $html .= "\n <dl class=\"price\">";
    $html .= "\n     <dt>판매가</dt>";
    $html .= "\n     <dd class=\"regular\"><span id=\"esti_sell_price\">" . $sell_price . "</span> 원</dd>";
    $html .= "\n     <dt>기본할인가</dt>";
    $html .= "\n     <dd><strong id=\"esti_sale_price\">" . $sale_price . "</strong> 원</dd>";
    $html .= "\n </dl>";

    return $html;
}
?>
