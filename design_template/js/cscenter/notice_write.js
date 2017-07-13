$(document).ready(function() {

});

var regiReq = function() {

    showMask();
    var formData = new FormData();

	if($("title").val == "" || $("#inq_cont").val() == "")
	{
		alert("제목 또는 내용이 없습니다.");
		return;
	}

    formData.append("title", $("#title").val());
    formData.append("inq_cont", $("#inq_cont").val());
    //formData.append("notice_file", $("#notice_file")[0].files[0]);
    if ($("#notice_file").val())
        formData.append("upload_yn", "Y");
    else
        formData.append("upload_yn", "N");

    $.ajax({
        type: "POST",
        data: formData,
        url: "/ajax/cscenter/notice_list/regi_notice_list.php",
        dataType : "json",
        processData : false,
        contentType : false,
        success: function(data) {
            hideMask();

			if(data.result == 'true'){
				alert(data.result_text);
				location.href = "/cscenter/notice_list.html";
			}else{
				alert(data.result_text);
			}
			return;
        },
        error    : getAjaxError
    });
}
