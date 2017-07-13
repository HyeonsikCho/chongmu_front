var lnbHTML = '';
lnbHTML += '<ul>';
lnbHTML += '<li>';
lnbHTML += '<a href="/service/introduce.html" target="_self">하이프린트 소개</a>';
lnbHTML += '</li>';
lnbHTML += '<li>';
lnbHTML += '<a href="/service/customer_designcompany.html" target="_self">고객서비스</a>';
lnbHTML += '<ul>';
lnbHTML += '<li><a href="/service/customer_designcompany.html" target="_self">디자인 기업</a></li>';
lnbHTML += '<li><a href="/service/customer_generalcompany.html" target="_self">일반 기업</a></li>';
lnbHTML += '<li><a href="/service/customer_personaluser.html" target="_self">개인</a></li>';
lnbHTML += '<li><a href="/service/customer_foreignuser.html" target="_self">해외</a></li>';
lnbHTML += '</ul>';
lnbHTML += '</li>';
lnbHTML += '<li>';
lnbHTML += '<a href="/service/order_rapid.html" target="_self">주문 서비스</a>';
lnbHTML += '<ul>';
lnbHTML += '<li><a href="/service/order_rapid.html" target="_self">급한 인쇄물</a></li>';
lnbHTML += '<li><a href="" target="_self">SMS 빠른 상담</a></li>';
lnbHTML += '<li><a href="/service/order_remote.html" target="_self">원격 접속 서비스</a></li>';
lnbHTML += '<li><a href="/service/order_gps.html" target="_self">GPS 배송 관제 서비스</a></li>';
lnbHTML += '<li><a href="/service/order_estimate.html" target="_self">견적 서비스</a></li>';
lnbHTML += '</ul>';
lnbHTML += '</li>';
lnbHTML += '<li>';
lnbHTML += '<a href="/service/question_list.html" target="_self">지식센터</a>';
lnbHTML += '<ul>';
lnbHTML += '<li><a href="/service/question_list.html" target="_self">무엇이든 물어보세요</a></li>';
lnbHTML += '<li><a href="/service/file_list.html" target="_self">공유 자료실</a></li>';
lnbHTML += '</ul>';
lnbHTML += '</li>';
lnbHTML += '<li>';
lnbHTML += '<a href="/service/sitemap.html" target="_self">사이트맵</a>';
lnbHTML += '</li>';
lnbHTML += '</ul>';

$(document).ready(function () {
    $('nav.lnb').html(lnbHTML);
    
    var text1 = $('header.title .location li:eq(2) span').text().replace(/\s/g, ''),
        text2 = $('header.title .location li:eq(3) span').text().replace(/\s/g, ''),
        thisText = '';

    $('nav.lnb > ul > li > a').each(function () {
        thisText = $(this).html().replace(/\s/g, '').split('<span')[0];
        if (thisText == text1) {
            $(this).closest('li').addClass('on');
            $(this).closest('li').children('ul').find('a').each(function () {
                thisText = $(this).html().replace(/\s/g, '').split('<span')[0];
                if(thisText == text2) {
                    $(this).closest('li').addClass('on');
                }
            });
        }
    });
});


