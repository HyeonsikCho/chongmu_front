$(document).ready(function () {
    $('.main .byStatus._toggle span.num').on('click', function () {
        $(this).prev('button').click();
    });
});