<?
class ProductInfoClass {
    // 후공정 종류 배열
    const AFTER_ARR = array(
        "코팅"     => "coating",
        "귀도리"   => "rounding",
        "오시"     => "impression",
        "미싱"     => "dotline",
        "타공"     => "punching",
        "접지"     => "foldline",
        "엠보싱"   => "embossing",
        "박"       => "foil",
        "형압"     => "press",
        "도무송"   => "thomson",
        "넘버링"   => "numbering",
        "재단"     => "cutting",
        "제본"     => "binding",
        "접착"     => "bonding",
        "라미넥스" => "laminex",
        "빼다" => "background"
    );

    //주문진행상태
    const ORDER_STATUS_ARR = array(
        '100'=>'주문완료',
        '320'=>'접수중',
        '330'=>'시안확인',
        '390'=>'접수완료',
        '400'=>'생산',
        '500'=>'배송준비중',
        '600'=>'배송완료');

    // 상품별 리다이렉트 페이지
    const PAGE_ARR = array(
        "001" => "nc_normal.html",
        "002" => array(
            "002003" => "st_normal.html",
            "002004" => "st_thomson.html"
        ),
        "003" => "bl_normal.html",
        "004" => "ad.html",
        "005" => array(
            "005001" => "ev_normal.html",
            "ELSE"   => "ev_master.html"
        ),
        "006" => "gb.html",
        "007" => array(
            "007001" => "mt_ncr.html",
            "007002" => "mt_form.html"
        ),
        "012" => array(
            "012001001" => "etc_lottery.html",
            "012001005" => "etc_multiple.html",
            "012001006" => "etc_multiple.html",
            "012001008" => "etc_memo.html",
            "ELSE" => "etc.html"
        )
    );

    // 상품별 리다이렉트 페이지
    const PAGE_SUB_ARR = array(
        "001" => "/product/info/nc_default_info.php",
        "002" => "bl_normal_info.php",
        "003" => "/product/info/bl_normal_info.php",
        "004" => "ad.html",
        "005" => array(
            "005001" => "ev_normal.html",
            "ELSE"   => "ev_master.html"
        ),
        "006" => "gb.html",
        "007" => array(
            "007001" => "mt_ncr.html",
            "007002" => "mt_form.html"
        ),
        "012" => array(
            "012001001" => "etc_lottery.html",
            "012001005" => "etc_multiple.html",
            "012001006" => "etc_multiple.html",
            "012001008" => "etc_memo.html",
            "ELSE" => "etc.html"
        )
    );


    // 카테고리별 재단, 작업사이즈 차이
    const SIZE_GAP = array(
        "001001001" => 2,
        "001001002" => 2,
        "001001004" => 2,
        "001002016" => 2,
        "001003024" => 2,
        "002003009" => 0,
        "002004009" => 4,
        "003001001" => 3,
        "003003001" => 3,
        "003004001" => 3,
        "004002001" => 8
    );

    // 카테고리별 재단, 작업사이즈 차이
    const NCR_DOTLINE_PER_PRICE = array(
        // NCR 상하
        "793" => 20,
        "794" => 40,
        "795" => 120,
        "796" => 80,
        "797" => 160,
        "798" => 40,
        "799" => 80,
        "800" => 160,
        "801" => 240,
        "802" => 320,

        // NCR 상중하
        "803" => 13,
        "804" => 27,
        "805" => 80,
        "806" => 53,
        "807" => 107,
        "808" => 27,
        "809" => 53,
        "810" => 107,
        "811" => 160,
        "812" => 213,

        // NCR 상중중하
        "813" => 10,
        "814" => 20,
        "815" => 60,
        "816" => 40,
        "817" => 80,
        "818" => 20,
        "819" => 40,
        "820" => 80,
        "821" => 120,
        "822" => 160,

        // NCR 상중하
        "823" => 20,
        "824" => 40,
        "825" => 120,
        "826" => 80,
        "827" => 160,
        "828" => 40,
        "829" => 80,
        "830" => 160,
        "831" => 240,
        "832" => 320
    );

    const PRICE_TABLE = array(
        "계산형" => "sum_price_gp",
        "확정형" => "ply_price_gp"
    );

    // 주문페이지 상단 상품명, 설명
    const CATE_INFO = array(
        "001001" => array(
            "cate_dvs"  => "nc",
            "cate_dscr" => "일반명함입니다."
        ),
        "001002" => array(
            "cate_dvs"  => "nc",
            "cate_dscr" => "고급명함입니다."
        ),
        "001003" => array(
            "cate_dvs"  => "nc",
            "cate_dscr" => "카드명함입니다."
        ),
        "002001" => array(
            "cate_dvs"  => "st",
            "cate_dscr" => "재단형스티커입니다."
        ),
        "002004" => array(
            "cate_dvs"  => "st",
            "cate_dscr" => "도무송형스티커입니다."
        ),
        "003001" => array(
            "cate_dvs"  => "bl",
            "cate_dscr" => "합판전단입니다."
        ),
        "003002" => array(
            "cate_dvs"  => "bl",
            "cate_dscr" => "특가전단입니다."
        ),
        "003003" => array(
            "cate_dvs"  => "bl",
            "cate_dscr" => "독판전단입니다."
        ),
        "003004" => array(
            "cate_dvs"  => "bl",
            "cate_dscr" => "초소량인쇄입니다."
        ),
        "004001" => array(
            "cate_dvs"  => "ad",
            "cate_dscr" => "카탈로그/브로셔입니다."
        ),
        "004003" => array(
            "cate_dvs"  => "ad",
            "cate_dscr" => "기획인쇄물입니다."
        ),
        "005001" => array(
            "cate_dvs"  => "ev",
            "cate_dscr" => "컬러옵셋봉투입니다."
        ),
        "006001" => array(
            "cate_dvs"  => "gb",
            "cate_dscr" => "그린백입니다."
        ),
        "007001" => array(
            "cate_dvs"  => "mt",
            "cate_dscr" => "마스터 ncr입니다."
        ),
        "007002" => array(
            "cate_dvs"  => "mt",
            "cate_dscr" => "마스터 모조지입니다."
        ),
        "012001" => array(
            "cate_dvs"  => "etc",
            "cate_dscr" => "기타인쇄입니다."
        )
    );
}

?>
