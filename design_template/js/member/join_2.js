var goPage = function(page, dvs) {

    if ($("input:checkbox[id='agree1']").is(":checked") == false) {
        alert("하이프린트 이용약관에 동의하셔야 됩니다.");
        return false;
    }
    if ($("input:checkbox[id='agree2']").is(":checked") == false) {
        alert("개인정보 취급방침에 동의하셔야 됩니다.");
        return false;
    }

    location.href = "/member/join_" + page + ".html?dvs=" + dvs;
}


