$(document).ready(function() {
    $("#id").focus();
});

var readyImgShow = function(el) {
    $(el).next().show();
}

var readyImgHide = function(el) {
    $(el).hide();
}
