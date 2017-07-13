/*
 *
 * Copyright (c) 2016 Nexmotion, Inc.
 * All rights reserved.
 * 
 * REVISION HISTORY (reverse chronological order)
 *============================================================================
 * 2016/03/04 왕초롱 생성
 *============================================================================
 *
 */


$(document).ready(function() {

        claimView($("#claim_seqno").val());
});

/**
 * @brief 클레임 상세 검색
 */
var claimView = function(val) {

    var url = "/ajax/mypage/claim_view/load_claim_detail.php";
    var data = {
    	"claim_seqno"       : val
	};
    var callback = function(result) {
        var rs = result.split("♪♭@");
        alert(result);

    };

    showMask();
    ajaxCall(url, "html", data, callback);
}


