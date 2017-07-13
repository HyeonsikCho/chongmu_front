$(document).ready(function () {
    //main banner
    /*
    (function () {
        var mainbanner = $('.mainBanner'),
            list = mainbanner.children('.list'),
            lists = list.children('li'),
            nav = mainbanner.children('nav'),
            navUl = nav.children('ul'),
            prev = nav.children('.prev'),
            next = nav.children('.next'),
            rollingInterval = 3000,
            autoRolling;

        lists.each(function () {
            var target = $(this);

            navUl.append('<li><button>' + $(this).find('img').attr('alt') + '</button></li>');
            navUl.children('li:last-child').children('button').on('click', function () {
                if (!$(this).hasClass('on')) {
                    list.children('.previous').remove();
                    list.append(list.children('.on').clone().addClass('previous'));

                    list.children('.on').removeClass('on');
                    navUl.children('.on').removeClass('on');

                    $(this).parent().addClass('on');
                    target.addClass('on');
                }

                clearTimeout(autoRolling);
                autoRolling = setTimeout(function () { next.click(); }, rollingInterval);
            });
        });

        //prev
        prev.on('click', function () {
            if (navUl.children('.on').prev().length > 0) {
                navUl.children('.on').prev().children('button').click();
            } else {
                navUl.children('li:last-child').children('button').click();
            }

            clearTimeout(autoRolling);
            autoRolling = setTimeout(function () { next.click(); }, rollingInterval);
        });
        //next
        next.on('click', function () {
            if (navUl.children('.on').next().length > 0) {
                navUl.children('.on').next().children('button').click();
            } else {
                navUl.children('li:first-child').children('button').click();
            }

            clearTimeout(autoRolling);
            autoRolling = setTimeout(function () { next.click(); }, rollingInterval);
        });

        //initialize
        list.append(list.children('li:first-child').clone().addClass('previous'));
        list.children('li:first-child').addClass('on');
        navUl.children('li:first-child').addClass('on');

        autoRolling = setTimeout(function () { next.click(); }, rollingInterval);
    })();
    
    //event
    (function () {
        //image number - img url이 js에 있어 유지보수 시 직관성이 떨어짐. 혹시 모를 경우를 대비해 script 남김.
        $('.event .label').each(function () {
            var label = $(this),
                number = label.attr('class').split(' ');
            
            $(number).each(function () {
                if (this.substring(0,4) == '_num') {
                    number = this.substring(4,this.length)
                    return false;
                }
            });
            
            for (var i= 0; i < number.length; i++) {
                var num = number.substring(i, i+1);
                label.append('<img src="../../images/main/event_number_' + num + '.png" alt="' + num + '"> ');
            }
        });
        
        //rolling
        $('.event > article').each(function () {
            var banner = $(this),
                list = banner.children('.list'),
                lists = list.children('li'),
                navUl = banner.children('nav').children('ul'),
                rollingInterval = 3000,
                autoRolling;

            lists.each(function (i) {
                var target = $(this);

                navUl.append('<li><button>' + (i + 1) + '</button></li>');
                navUl.children('li:last-child').children('button').on('click', function () {
                    if (!$(this).hasClass('on')) {
                        list.children('.previous').remove();
                        list.append(list.children('.on').clone().addClass('previous'));

                        list.children('.on').removeClass('on');
                        navUl.children('.on').removeClass('on');

                        $(this).parent().addClass('on');
                        target.addClass('on');
                    }

                    clearTimeout(autoRolling);
                    autoRolling = setTimeout(function () { 
                        if (navUl.children('.on').next().length > 0) {
                            navUl.children('.on').next().children('button').click();
                        } else {
                            navUl.children('li:first-child').children('button').click();
                        }
                    }, rollingInterval);
                });
            });

            //initialize
            list.append(list.children('li:first-child').clone().addClass('previous'));
            list.children('li:first-child').addClass('on');
            navUl.children('li:first-child').addClass('on');

            autoRolling = setTimeout(function () { 
                if (navUl.children('.on').next().length > 0) {
                    navUl.children('.on').next().children('button').click();
                } else {
                    navUl.children('li:first-child').children('button').click();
                }
            }, rollingInterval);
        });
    })();
*/
});
