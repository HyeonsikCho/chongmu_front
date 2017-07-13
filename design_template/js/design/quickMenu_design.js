/**
 * Created by edohyune on 2016-03-04.
 */
$(document).ready(function () {
    var scrollTop = $(window).scrollTop(),
        scrollLeft = $(window).scrollLeft(),
        headerMargin = $('header.top').outerHeight() + $('header.title').outerHeight() - 10,
        quickMenu = $('.quickMenu'),
        quickMenuHeight = quickMenu.outerHeight(),
        quickMenuHiddenHeight,
        baseMargin = 80,
        bodyWidth = $('body').width(),
        bodyHeight = $(document).height(),
        footerHeight = $('footer').outerHeight(),
        bottomDistance,
        windowWidth = $(window).width(),
        windowHeight = $(window).height(),
        topForHidden = 0;

    $(window).resize(function () {
        windowWidth = $(window).width();
        windowHeight = $(window).height();
        bodyWidth = $('body').width(),
            windowScroll();
    });

    $(window).scroll(function () {
        windowScroll();
    });

    windowScroll();

    function windowScroll () {
        //상하
        scrollTop = $(window).scrollTop();
        bodyHeight = $(document).height();

        if (scrollTop > headerMargin - baseMargin) { //스크롤이 빠른견적서 상단보다 더 내려갈 경우 top 조정
            quickMenu.css('top', baseMargin);
        } else {
            quickMenu.css('top', headerMargin - scrollTop);
        }

        quickMenuHiddenHeight = quickMenuHeight - windowHeight + baseMargin; //빠른견적서보다 창이 작은 경우

        if (quickMenuHiddenHeight > 0 && scrollTop > headerMargin - baseMargin) {
            if(scrollTop - (headerMargin - baseMargin) > quickMenuHiddenHeight) {
                topForHidden = quickMenuHiddenHeight * -1;
                quickMenu.css('top', topForHidden);
            } else {
                topForHidden = quickMenu.css('top').replace('px','') - (scrollTop - (headerMargin - baseMargin));
                quickMenu.css('top', topForHidden);
            }
            quickMenuHiddenHeight = quickMenuHiddenHeight + baseMargin;
        } else if (quickMenuHiddenHeight <= 0) {
            quickMenuHiddenHeight = 0;
        }

        bottomDistance = bodyHeight - (quickMenuHeight + baseMargin * 2 + scrollTop + footerHeight - quickMenuHiddenHeight); //하단과의 거리
        if (bottomDistance > 0) {
            quickMenu.css('margin-top', 0);
        } else {
            quickMenu.css('margin-top', bottomDistance);
        }

        //좌우
        if(windowWidth > bodyWidth) {
            quickMenu.css('right',0);
        } else {
            scrollLeft = $(window).scrollLeft();
            quickMenu.css('right', windowWidth - bodyWidth + scrollLeft);
        }
    }
});