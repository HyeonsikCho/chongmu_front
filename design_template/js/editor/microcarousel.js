/**
 * Created by hyeonsik on 2017-08-02.
 */
(function($) {
    $.fn.extend({
        gallery_microcarousel: function(options, slide) {
            var ret = this;
            this.each(function() {
                var elem = $(this);
                var ul = elem.children("ul");

                if (options == "setActive") {
                    ul.children("li").hide().removeClass("active");
                    slide.show().addClass("active");

                    if (elem.data().options.change) {
                        elem.data().options.change.call(this, slide);
                    }

                    ul.children("li").slice(0, 6).show();

                    return slide;
                } else if (options == "getActive") {
                    ret = ul.children("li.active");
                } else {
                    elem.data("options", options);

                    ul.children("li").hide().removeClass("active");
                    var initLi = ul.children("li:first").show().addClass("active");

                    ul.children("li").slice(0, 6).show();

                    if (elem.data().options.init) {
                        elem.data().options.init.call(this, initLi);
                    }

                    ul.children("li").bind("mouseover", function(e) {
                        var li = $(this);
                        if (elem.data().options.change) {
                            elem.data().options.change.call(this, li);
                            ul.children("li").removeClass("active");
                            li.addClass('active');
                        }
                    });

                    elem.children(".next").bind("click", function(e) {
                        var lis = ul.children("li");
                        var li = ul.children("li:visible:last");
                        var last = ul.children("li:last");

                        lis.hide().removeClass("active");

                        if (li[0] === last[0]) {
                            var nextLi = ul.children("li:first").show().addClass("active");
                            lis.slice(0, 6).show();
                        } else {
                            var nextLi = li.next().show().addClass("active");
                            lis.slice(lis.index(li) + 1, lis.index(li) + 7).show();
                        }

                        if (elem.data().options.change) {
                            elem.data().options.change.call(this, nextLi);
                        }

                        e.preventDefault();
                    });

                    elem.children(".prev").bind("click", function(e) {
                        var lis = ul.children("li");
                        var li = ul.children("li:visible:first");
                        var first = ul.children("li:first");

                        lis.hide().removeClass("active");

                        if (li[0] === first[0]) {
                            var prevLi = ul.children("li:last").show().addClass("active");
                        } else {
                            var prevLi = li.prev().show().addClass("active");
                        }

                        var start = lis.index(li) - 6;

                        if (start < 0) {
                            lis.slice(Math.floor(lis.length / 6) * 6, lis.length).show();
                        } else {
                            lis.slice(start, lis.index(li) - 1).show();
                        }

                        prevLi = ul.children("li:visible:first");

                        if (elem.data().options.change) {
                            elem.data().options.change.call(this, prevLi);
                        }

                        e.preventDefault();
                    });
                }
            });
            return ret;
        },
        clipart_microcarousel: function(options, slide) {
            var ret = this;
            this.each(function() {
                var elem = $(this);
                var ul = elem.children("ul");
                var lis = ul.children("li");
                var list_index = 0; //lis.index(lis);

                //console.log("List Length: " + lis.length);
                if (options == "setActive") {
                    ul.children("li").hide().removeClass("active");
                    slide.show().addClass("active");

                    if (elem.data().options.change) {
                        elem.data().options.change.call(this, slide);
                    }

                    ul.children("li").slice(0, 6).show();

                    return slide;
                } else if (options == "getActive") {
                    ret = ul.children("li.active");
                } else {
                    elem.data("options", options);

                    ul.children("li").hide().removeClass("active");
                    var initLi = ul.children("li:first").addClass("active").show();

                    ul.children("li").slice(0, 6).show();

                    if (elem.data().options.init) {
                        elem.data().options.init.call(this, initLi);
                    }

                    // Initializing
                    $(lis).hide();
                    $(lis).eq(list_index).show();

                    elem.children(".next").bind("click", function(e) {

                        if (list_index < (lis.length - 1)) {
                            $(lis).hide().removeClass('active');
                            list_index += 1;
                            $(lis).eq(list_index).addClass('active').show();
                        } else {
                            $(lis).hide().removeClass('active');
                            list_index = 0;
                            $(lis).eq(list_index).show().addClass('active');
                        }

                        if (elem.data().options.change) {
                            elem.data().options.change.call(this, $(lis).eq(list_index));
                        }

                        e.preventDefault();
                    });

                    elem.children(".prev").bind("click", function(e) {

                        if (list_index > 0) {
                            $(lis).hide().removeClass('active');
                            list_index -= 1;
                            $(lis).eq(list_index).addClass('active').show();
                        } else {
                            $(lis).hide().removeClass('active');
                            list_index = (lis.length - 1);
                            $(lis).eq(list_index).show().addClass('active');
                        }

                        if (elem.data().options.change) {
                            elem.data().options.change.call(this, $(lis).eq(list_index));
                        }

                        e.preventDefault();
                    });
                }
            });
            return ret;
        }
    });
})(jQuery);
