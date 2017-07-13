<?php
/**
* @brief 로그인 정보에 따라 우측 메뉴를 다르게 보여준다.
*
* @param $session -> 사용자 정보
*
* @return quickMenuHtml
*/

function getQuickMeunHtml($session) {

    $html = "";

    if($session["MYSEC_ID"]=="KD0DKGK"){
    //if(($session["MYSEC_ID"]=="KD0DKGK")&&($session["MYSEC_ID"]=="CNST3UK")){

        $html = <<<html
            <script src="/design_template/js/design/quickMenu_design.js"></script>
            <link rel="stylesheet" href="/design_template/css/quickMenu.css">
            <div class="quickMenu">
                <h3><div class="wrap">%s</div></h3>
                <dl>
                    <dt><a href="/product/nc_normal.html?cs=001001" class="button" btnType="order" pdtNum="001">명함주문하기</a></dt><dd></dd>
                    <dt><a href="#" class="button" btnType="order" pdtNum="001">TEST주문페이지</a></dt><dd></dd>
                    <dt><a href="/order/cart.html" class="button" btnType="order" pdtNum="001">장바구니</a></dt><dd></dd><!--dd><img src="[TPH_Vdesign_dir]/images/common/btn_circle_bottom.png" alt="▼"></dd-->
                    <dt><a href="/mypage/order_all.html" class="button" btnType="order" pdtNum="001">주문내역</a></dt><dd><span></span></dd>
                    <!--dt><a href="/main/sessiontest.php" class="button" btnType="order" pdtNum="001">TEST</a></dt><dd><span></span></dd-->
                    <dt><a href="/mypage/estimate_list.html" class="button" btnType="order" pdtNum="001">견적문의</a></dt><dd><span></span></dd>
                </dl>
                <div class="function">
                    <a href="/main/main.html" class="button">Home</a>
                </div>
            </div>
html;

    }else{

        $html = <<<html
            <script src="/design_template/js/design/quickMenu_design.js"></script>
            <link rel="stylesheet" href="/design_template/css/quickMenu.css">
            <div class="quickMenu">
                <h3><div class="wrap">%s</div></h3>
                <dl>
                    <dt><a href="/product/nc_normal.html?cs=001001" class="button" btnType="order" pdtNum="001">명함주문하기</a></dt> <dd></dd>
                    <!--dt><a href="#" class="button" btnType="order" pdtNum="001">TEST주문페이지</a></dt> <dd></dd-->
                    <dt><a href="/order/cart.html" class="button" btnType="order" pdtNum="001">장바구니</a></dt><dd></dd><!--dd><img src="[TPH_Vdesign_dir]/images/common/btn_circle_bottom.png" alt="▼"></dd-->
                    <dt><a href="/mypage/order_all.html" class="button" btnType="order" pdtNum="001">주문내역</a></dt><dd><span></span></dd>
                    <!--dt><a href="/main/sessiontest.php" class="button" btnType="order" pdtNum="001">TEST</a></dt><dd><span></span></dd-->
                    <dt><a href="/mypage/estimate_list.html" class="button" btnType="order" pdtNum="001">견적문의</a></dt><dd><span></span></dd>
                </dl>
                <div class="function">
                    <a href="/main/main.html" class="button">Home</a>
                </div>
            </div>
html;
    }

    $html  = sprintf($html,"SITE MENU");

return $html;
}
?>