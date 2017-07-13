<?php

class Paper_NCR extends Paper {

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
            $price = (int)$this->dao->selectPrdtPlyPrice($this->conn, $param);
            if($paper_depth == "Y") {
                if($cate_sortcode == "007001001") {
                    $price += 3300;
                } else if($cate_sortcode == "007001002") {
                    $price += 6600;
                } else if($cate_sortcode == "007001003") {
                    $price += 9900;
                }
            }
            $this->price = $this->upCeil($price);
            $this->name = $print_name;
            $this->amt = $amt;
            $this->count = $count;
        } else {
            $this->price = 0;
            $this->name = '상품정보 없음';
        }
    }
}
?>