// 작업 파일 업로더 객체
var uploaderObj = "";
// 작업 파일 업로더 확인여부
var fileFlag = 0;
// 파일아이디
var fileId = "";

$(document).ready(function() {
    fileUpload();
});

/**
 * @brief 파일업로드
 */
var fileUpload = function() {
    var runtimes = "html5,flash,silverlight,html4";
    var mimeTypes = [
        {title : "Zip files", extensions: "zip"}
    ];

    var btnId    = "work_file";
    var listId   = "work_file_list";
    var uploadId = "work_file_upload";
    var delId    = "work_file_del";

    var uploader = new plupload.Uploader({
        url                 : "/proc/mypage/esti_write/upload_file.php",
        runtimes            : runtimes,
        browse_button       : btnId, // you can pass in id...
        flash_swf_url       : "/design_template/js/uploader/Moxie.swf",
        silverlight_xap_url : "/design_template/js/uploader/Moxie.xap",
        multi_selection     : false,

        filters : {
                max_file_size : "4096mb",
                mime_types    : mimeTypes
        },
        init : {
            PostInit : function() {
                document.getElementById(listId).innerHTML = '';
            },
            FilesAdded : function(up, files) {

                // 파일을 새로 추가할 경우
                if (up.files.length > 1) {
                    var fileSeqno = $("#" + delId).attr("file_seqno");

                    // 파일이 업로드 된 상태(fileSeqno !== empty)에서
                    // 다른 파일을 새로 업로드 할 경우
                    if (checkBlank(fileSeqno) === false &&
                            confirm("기존 파일은 삭제합니다." +
                                    "\n계속 하시겠습니까?") === false) {
                        return false;
                    }
                    up.removeFile(up.files[0]);

                    if (checkBlank(fileSeqno) === false) {
                        removeFile(fileSeqno, false);
                    }
                }

                plupload.each(files, function(file) {
                    document.getElementById(listId).innerHTML =
		                    "<div id=\"" + file.id + "\">" +
		                    file.name + " (" +
		                    plupload.formatSize(file.size) +
		                    ")<b></b>" +
                            "&nbsp;" +
                            "<img src=\"/design_template/images/common/btn_circle_x_red.png\"" +
                            "     id=\"esti_file_del\"" +
                            "     file_seqno=\"\"" +
                            "     alt=\"X\"" +
                            "     onclick=\"removeFile('', true);\"" +
                            "     style=\"cursor:pointer;\" /></div>";
                });
            },

            FilesRemoved : function(up, files) {
                document.getElementById(listId).innerHTML = '';
                fileFlag = 0;
                $("#esti_file_seqno").val('');
            },

            UploadProgress : function(up, file) {
                fileFlag = 1;
                fileId = file.id;

                document.getElementById(file.id)
                        .getElementsByTagName("b")[0]
                        .innerHTML = "<span>" + file.percent + "%</span>";
            },

            FileUploaded : function(up, file, response) {
                var jsonObj = JSON.parse(response.response);
                var fileSeqno = jsonObj.file_seqno;

                $("#" + delId).attr(
                    {"onclick"    : "removeFile('" + fileSeqno + "', true);",
                     "file_seqno" : fileSeqno}
                );

                fileFlag = 2;

                $("#esti_file_seqno").val(fileSeqno);
            },

            Error : function(up, err) {
                if(err.code == -601) {
                    alert("견적문의 파일첨부는 압축파일만 가능합니다.");
                }
                /*
                document.getElementById(listId).innerHTML +=
                        "\nError #" + err.code + ": " + err.message;
                */
            }
        }
    });

    uploader.init();
    uploaderObj = uploader;
}


//옵션 html 생성
var makeOption =  function(str) {
    var html = "<option value=\"\" selected>" + str + "</option>";
    return html;
}


/**
 * @brief 카테고리 선택시 하위 카테고리 정보 검색
 *
 * @param cateType     = 카테고리 선택 구분(top, mid)
 * @param cateSortcode = 카테고리 분류코드
 */
var cateSelect = {
    "type" : null,
    "exec" : function(cateType, cateSortcode) {
        //카테고리 분류코드가 빈값인 경우
        if (checkBlank(cateSortcode)) {
            var html = "";

            //카테고리 대분류
            if (cateType === "top") {
                html = makeOption("중분류(전체)");
                $("#cate_mid").html(html);
                html = makeOption("소분류(전체)");
                $("#cate_bot").html(html);
            } else if (cateType === "mid") {
                html = makeOption("소분류(전체)");
                $("#cate_bot").html(html);
            }

            // 상세 검색정보 초기화 하는 함수 있을경우 실행
            if (isFunc('resetDetailInfo') === true) {
                resetDetailInfo();
            }
            return false;
        }

        this.type = cateType;

        // 카테고리 대 변경시 카테고리 소 초기화
        if (cateType === "top") {
            html = makeOption("소분류(전체)");
            $("#cate_bot").html(html);
        }

        var url = "/ajax/mypage/esti_write/load_cate_list.php";
        var data = {
            "cate_sortcode" : cateSortcode,
            "cate_type"     : cateType
        };
        var callback = function(result) {
            var html = "";
            if (cateSelect.type === "top") {
                $("#cate_mid").html(result);
            } else if (cateSelect.type === "mid") {
                $("#cate_bot").html(result);
            }
        }

        //ajax call 함수
        ajaxCall(url, "html", data, callback);
    },
    "pop" : function(cateType, cateSortcode) {
        //카테고리 분류코드가 빈값인 경우
        if (checkBlank(cateSortcode)) {
            var html = "";

            //카테고리 대분류
            if (cateType === "top") {
                html = makeOption("중분류(전체)");
                $("#cate_mid").html(html);
                html = makeOption("소분류(전체)");
                $("#cate_bot").html(html);
            } else if (cateType === "mid") {
                html = makeOption("소분류(전체)");
                $("#cate_bot").html(html);
            }

            // 상세 검색정보 초기화 하는 함수 있을경우 실행
            if (isFunc('resetDetailInfo') === true) {
                resetDetailInfo();
            }
            return false;
        }

        this.type = cateType;

        // 카테고리 대 변경시 카테고리 소 초기화
        if (cateType === "top") {
            html = makeOption("소분류(전체)");
            $("#cate_bot").html(html);
        }

        var url = "/ajax/common/load_cate_list.php";
        var data = {
            "cate_sortcode" : cateSortcode,
            "cate_type"     : cateType
        };
        var callback = function(result) {
            var html = "";
            if (cateSelect.type === "top") {
                $("#cate_mid").html(result);
            } else if (cateSelect.type === "mid") {
                $("#cate_bot").html(result);
            }
            showBgMask();
        }

        //ajax call 함수
        ajaxCall(url, "html", data, callback);
    }
};


/**
 * @brief 카테고리 선택시 하위 카테고리 정보 검색
 *
 * @param cateType     = 카테고리 선택 구분(top, mid)
 * @param cateSortcode = 카테고리 분류코드
 */
var formatSelect = {
    "type" : null,
    "exec" : function(cateSortcode) {
		if(cateSortcode != '') {
		    alert(cateSortcode);
            var url = "/ajax/mypage/esti_write/load_content_format.php";
            var data = {
                "cate_sortcode" : cateSortcode
            };
            var callback = function(result) {
			    $("#inq_cont").html(result);
            }

        //ajax call 함수
            ajaxCall(url, "html", data, callback);
		}
    }
};

/**
 * @brief 작업파일 부분 삭제
 *
 * @param seqno = 주문 파일 일련번호
 * @param flag  = uploader.removeFile 여부
 */
var removeFile = function(seqno, flag) {

    if (checkBlank(seqno) === true) {
        var uploader = uploaderObj;
        var files = uploader.files;
        uploader.removeFile(files[0]);

        return false;
    }

    if (flag === true) {
        if (confirm("작업파일을 삭제하시겠습니까?" +
                    "\n삭제된 파일은 복구되지 않습니다.") === false) {
            return false;
        }
    }

    var url = "/proc/mypage/esti_write/delete_esti_file.php";
    var data = {
        "esti_file_seqno"  : seqno
    };
    var callback = function(result) {

        if (result == "F") {
            alert("파일 정보 삭제에 실패했습니다.");
            return false;
        }

        if (flag === true) {
            var uploader = uploaderObj;
            var files = uploader.files;
            uploader.removeFile(files[0]);
        }
    };

    showMask();
    ajaxCall(url, "text", data, callback);
};

/**
 * @brief 작업파일 업로드
 */
var uploadFile = function() {
    var uploader = uploaderObj;
    var url = "/proc/mypage/esti_write/upload_file.php";

    uploader.settings.url = url;
    uploader.start();
};

/**
 * @brief 견적문의 등록
 */
var regiReq = function() {

    //validation 체크
    var title = $("#title").val();
    var inq_cont = $("#inq_cont").val();
    var esti_file = $("#esti_file").val();

    /**
     * fileFlag = 0 , 업로드를 전혀 하지 않은 상황 or 업로드된 파일이 삭제된
     *                상황으로 견적문의 클릭시 OK
     * fileFlag = 1 , 업로드 진행중 상황으로 견적문의 클릭시 NO
     * fileFlag = 2 , 업로드 완료 후 상황으로 견적문의 클릭시 OK
     */
    if (fileFlag == 1) {
        alert("파일 업로드 진행 중입니다.");
        return;
    }

    if (trim(title) == "" || trim(title) == null) {
        alert("제목을 입력해주세요.");
		$("#title").focus();
        return;
    }
    if (trim(inq_cont) == "" || trim(inq_cont) == null) {
        alert("내용을 입력해주세요.");
		$("#inq_cont").focus();
        return;
    }

    showMask();
    var formData = new FormData($("#esti_form")[0]);

    $.ajax({
        type: "POST",
        data: formData,
        processData : false,
        contentType : false,
		url: "/proc/mypage/esti_write/regi_esti.php",
        success: function(result) {
            hideMask();
            alert($.trim(result));
            location.href = "/mypage/estimate_list.html";
        },
        error    : getAjaxError
    });
}

/**
 * @brief 공백제거함수
 * @param str = 해당 문자열
 */
var trim = function(str) {
    return str.replace(/(^\s*)|(\s*$)/gi, "");
}

/**
 * @brief 취소버튼 클릭시 List페이지로 이동
 */

var goList = function() {
    var url = "/mypage/estimate_list.html";
    $("#esti_form").attr("action", url);
    $("#esti_form").attr("method", "post");
    $("#esti_form").submit();
};

