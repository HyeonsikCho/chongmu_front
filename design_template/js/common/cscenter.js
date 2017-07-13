var lnbHTML = '';
lnbHTML += '<ul>';
lnbHTML += '<li>';
lnbHTML += '<a href="/cscenter/notice_list.html" target="_self">공지사항</a>';
lnbHTML += '</li>';
lnbHTML += '<li>';
lnbHTML += '<a href="/cscenter/guide_membership.html" target="_self">이용안내</a>';
lnbHTML += '<ul>';
lnbHTML += '<li><a href="/cscenter/guide_membership.html" target="_self">회원</a></li>';
lnbHTML += '<li><a href="/cscenter/guide_order.html" target="_self">주문</a></li>';
lnbHTML += '<li><a href="/cscenter/guide_event.html" target="_self">이벤트</a></li>';
lnbHTML += '<li><a href="/cscenter/guide_pointcoupon.html" target="_self">마일리지/쿠폰</a></li>';
lnbHTML += '<li><a href="/cscenter/guide_estimate.html" target="_self">견적</a></li>';
lnbHTML += '<li><a href="/cscenter/guide_delivery.html" target="_self">배송</a></li>';
lnbHTML += '<li><a href="/cscenter/guide_claim.html" target="_self">클레임</a></li>';
lnbHTML += '</ul>';
lnbHTML += '</li>';
lnbHTML += '<li>';
lnbHTML += '<a href="/cscenter/faq_membership.html" target="_self">FAQ</a>';
lnbHTML += '<ul>';
lnbHTML += '<li><a href="/cscenter/faq_membership.html" target="_self">회원</a></li>';
lnbHTML += '<li><a href="/cscenter/faq_order.html" target="_self">주문</a></li>';
lnbHTML += '<li><a href="/cscenter/faq_event.html" target="_self">이벤트</a></li>';
lnbHTML += '<li><a href="/cscenter/faq_pointcoupon.html" target="_self">마일리지/쿠폰</a></li>';
lnbHTML += '<li><a href="/cscenter/faq_estimate.html" target="_self">견적</a></li>';
lnbHTML += '<li><a href="/cscenter/faq_production.html" target="_self">생산</a></li>';
lnbHTML += '<li><a href="/cscenter/faq_delivery.html" target="_self">배송</a></li>';
lnbHTML += '<li><a href="/cscenter/faq_claim.html" target="_self">클레임</a></li>';
lnbHTML += '<li><a href="/cscenter/faq_accounting.html" target="_self">회계</a></li>';
lnbHTML += '<li><a href="/cscenter/faq_etc.html" target="_self">기타</a></li>';
lnbHTML += '</ul>';
lnbHTML += '</li>';
lnbHTML += '<li>';
lnbHTML += '<a href="/cscenter/contact_part.html" target="_self">연락처</a>';
lnbHTML += '<ul>';
lnbHTML += '<li><a href="/cscenter/contact_part.html" target="_self">각 부서 연락처</a></li>';
lnbHTML += '<li><a href="/cscenter/contact_map.html" target="_self">오시는 길</a></li>';
lnbHTML += '</ul>';
lnbHTML += '</li>';
lnbHTML += '<li>';
lnbHTML += '<a href="/cscenter/opinion_list.html" target="_self">고객의 소리</a>';
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
