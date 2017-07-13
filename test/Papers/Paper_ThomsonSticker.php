<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ErpCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Common/DAO/ProductDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/Common/CondimentDecorator.php');

class Paper_ThomsonSticker extends Paper {

    function setPaper($cate_sortcode, $amt, $count, $stan_mpcode, $paper_mpcode, $print_name, $print_purp, $paper_depth) {
        $param = array();
        $param['cate_sortcode'] = $cate_sortcode;
        $param['amt'] = $amt;
        $param['stan_mpcode'] = $stan_mpcode;
        $param['paper_mpcode'] = $paper_mpcode;
        $param['tmpt'] = $print_name;
        $param['purp']      = $print_purp;
        $param["table_name"]    = 'ply_price_gp';
        $param["print_mpcode"]  = $this->getPrintMpcode($param);
        $param['paper_depth']      = $paper_depth;

        if($param["print_mpcode"]) {
            if($stan_mpcode == "958" || $stan_mpcode == "959" || $stan_mpcode == "960" || $stan_mpcode == "961") {
                $paper_depth = explode(",", $paper_depth);
                $wid = $paper_depth[0];
                $vert = $paper_depth[1];
                $rs = $this->dao->selectPricePerPaper($this->conn, $param);

                $per_paper = "";
                if ($param['paper_mpcode'] == "157") { // 일반
                    $per_paper = $rs->fields["stick_paper_price_per"];
                } else {
                    $per_paper = $rs->fields["especial_paper_price_per"];
                }
                $basic_price = $rs->fields["basic_price"];
                $k_array = $rs->fields["knife_price_per"];

                $area_c = ($wid + 5) * ($vert + 5) / 100;
                $area_c = round($area_c, 2);
                if ($area_c < 32) {
                    $area_c = 30;
                }

                $a_price = $area_c * $per_paper * $amt;
                if ($a_price < $basic_price) {
                    $a_price = $basic_price;
                }

                $check_area = $wid * $vert / 100;
                $check_area = round($check_area, 1);
                $param['check_area'] = $check_area;

                if ($param["stan_mpcode"] == "958") {
                    $param['f_name'] = "typ1_price";
                } else if ($param["stan_mpcode"] == "959") {
                    $param['f_name'] = "typ2_price";
                } else if ($param["stan_mpcode"] == "960") {
                    $param['f_name'] = "typ3_price";
                } else if ($param["stan_mpcode"] == "961") {
                    $param['f_name'] = "typ4_price";
                }

                $b_price = $this->dao->selectTomsonStickerPrice($this->conn, $param);
                $b_price = $b_price * $k_array * $amt / 1000;

                $price = round($a_price + $b_price, -2) * 1.1;

                if ($wid == 0 || $vert == 0) {
                    $this->price = 0;
                } else {
                    $this->price = $this->upCeil($price);
                }
                $this->name = $print_name;
                $this->amt = $amt;
                $this->count = $count;
            } else {
                $price = $this->dao->selectPrdtPlyPrice($this->conn, $param);
                $this->price = $this->upCeil($price);
                $this->name = $print_name;
                $this->amt = $amt;
                $this->count = $count;
            }
        } else {
            $this->price = 0;
            $this->name = '상품정보 없음';
        }
    }
}
?>