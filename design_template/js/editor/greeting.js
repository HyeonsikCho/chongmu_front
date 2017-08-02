/**
 * Created by hyeonsik on 2017-08-02.
 */
$(function() {
    // change paper size & orientation
    $("#pageSizeSelector input").click(function(e) {

        var elem = $(this);

        $("#pageSizeSelector .size").removeClass("active");
        elem.closest(".size").addClass("active");

        $("#pageSizeSelector .size .img *").removeClass("active");
        elem.closest(".size").find(".img ."+elem.attr("class")).addClass("active");

        $("#pageSizeSelector input").not(this).removeAttr("checked");

        var size = elem.val().match(/^(.*) x (.*)$/);
        var width = parseFloat(size[1]);
        var height = parseFloat(size[2]);
        var orientation = elem.attr("class");

        Editor.document.update({
            width: width,
            height: height,
            orientation: orientation
        });

        if ( orientation == "portrait" ) {
            $.each(Editor.document.pages, function(pageName, page) {
                if ((pageName == "page2" || pageName == "page3") && Editor.document.type == "halfFold") {
                    var shrinkRatio = Editor.config.canvasWidth / (page.width*2);
                    page.width = Editor.config.canvasWidth/2;
                    page.height = page.height*shrinkRatio;
                }
                else {
                    page.width = Math.floor(page.width/1.2);
                    page.height = Math.floor(page.height/1.2);
                }
                page.el.css({
                    width: page.width,
                    height: page.height,
                    float: 'left'
                });
            });
        }
        $("#pageSelector").attr("class", orientation);

        $("#canvas").trigger("changeWidth");
    });

    //trigger change of paper size by clicking on an image
    $("#pageSizeSelector a").click(function(e){
        var elem = $(this);
        var orientation = elem.attr("class");
        elem.closest(".size").find("input."+orientation).click();
        e.preventDefault();
    });

    //change active page(s)
    $("#pageSelector a").click(function(e) {
        var page = $(this).attr("id");
        if (page == 'front') {
            Editor.setActivePages(['page1']);
        }
        if (page == 'inside') {
            Editor.setActivePages(['page2','page3']);
        }
        if (page == 'back' && Editor.document.type == "halfFold") {
            Editor.setActivePages(['page4']);
        }
        else if (page == 'back' && Editor.document.type == "generic") {
            Editor.setActivePages(['page2']);
        }

        var obj = Editor.getObject( Editor.selected );
        if ( obj && $(obj.el).hasClass('text') ) {
            $("#textTool").click();
        }
        if ( obj && $(obj.el).hasClass('image') ) {
            $("#imageTool").click();
        }
        Editor.selected = null;

        e.preventDefault();
    });

    //scroll right panel so it's always on screen
    $(".rblock").jScroll({speed: 'fast'});

// 		//textarea highlight
// 		$("textarea").focus(function(){
// 			$(this).css({background: '#e9fbff'});
// 		}).blur(function(){
// 			$(this).css({background: '#faffe3'});
// 		});

// 		//display safety warning if too close to the edge
// 		$("#canvas").click(function(e){
// 			elem = $(this);
// 			if ( $.browser.msie ) {
// 			  var x = e.clientX - elem.position().left;
// 				var y = e.clientY - elem.position().top;
// 			}
// 			else {
// 			  var x = e.layerX;
// 				var y = e.layerY;
// 			}
//
// 			var width = elem.width();
// 			var height = elem.height();
// 			if ( x < 11 || x > (width-11) || y < 11 || y > (height-11) ) {
// 			   $("#safetyWarning").show();
// 			   setTimeout('$("#safetyWarning").hide()', 4000);
// 			}
// 		});

    //show tool hints
    $(".tool").hover(function(e){
        $(this).children(".toolHint").fadeIn(100);
    }, function(){
        $(".toolHint").hide();
    });

    //insert image if clicked on thumbnail
    $("#imageCarousel li").click(function(e){
        var activePage = Editor.getPage( $(".page:visible:first").attr("id") );
        Editor.insertObject(50, 50, activePage.name);
    });

    //change canvas width
    $("#canvas").bind("changeWidth", function(){
        var pages = $(".page:visible").length;
        var b = 2;
        var pageWidth =	Editor.getPage( $(".page:visible").attr("id") ).width;
        if (pages > 1 && Editor.document.orientation == "portrait") {
            if(navigator.platform.match(/(Mac)/i)) {
                b = 4;
            }
            $("#canvas").width( (pageWidth+b)*pages );
        }
        else {
            $("#canvas").width( pageWidth+b );
        }
        $("#canvas").css({backgroundColor: $(".page:visible:first").css("backgroundColor")});
    });

    //close various boxes
    $(".closeBox").click(function(){
        $(this).parent().fadeOut();
    });

    //close various boxes
    $(".dontDisplay").click(function(){
        $(this).parent().fadeOut();
        var dontDisplay = $.cookie('dontDisplay');
        if (dontDisplay == null || typeof dontDisplay == "undefined") {
            dontDisplay = {};
        }
        else {
            dontDisplay = $.parseJSON(dontDisplay);
        }
        dontDisplay[ $(this).parent().attr("class") ] = true;
        $.cookie('dontDisplay', jsonStringify(dontDisplay), { expires: 365 });
    });

    //beta logo
    /*var beta = $('<img src="/images/beta_header.png">');
     beta.css({
     position: 'absolute',
     top: 10,
     left: 0
     });
     $("#header_cap").css({position: 'relative'});
     $("#header_cap").append(beta);*/

    //text suggestion at start
    setTimeout('$("#toolPanel .suggestionBox .closeBox").click()', 4000);
    $(".toolSettings.text textarea").focus(function() {
        $("#toolPanel .suggestionBox .closeBox").click();

        //help for putting text on canvas
        if (Editor.selected == null) {
            $(".textSuggestion").html("When you're done, click inside the<br>white box to position your text");
        }
        else {
            $(".textSuggestion").html("If you wish to edit other text,<br>simply click on it");
        }
        $(".textSuggestion").fadeIn();
    });

    $(".toolSettings.text textarea").blur(function(){
        $(".textSuggestion").fadeOut();
    });

    //dont display stuff that customer never want to see
    var dontDisplay = $.cookie('dontDisplay');
    if (dontDisplay != null) {
        dontDisplay = $.parseJSON(dontDisplay);
        $.each(dontDisplay, function (key, val){
            $("."+key).addClass("hide").hide();
        });
    }

    //reset rotator
    $(".resetRotate").click(function(e){
        $(this).parent().children(".ui-slider").slider("value", 0).trigger("slidechange");
        e.preventDefault();
    });

    //button hide
// 		$(".openSave").hide();
// 		$("#generate").hide();
    $("#download").hide();
    $(".page").live("click", function(){
        $(".openSave").fadeIn();
        $("#generate").fadeIn();
    });
    $(".setting").change(function(){
        $(".openSave").fadeIn();
        $("#generate").fadeIn();
    });


    $('#customerRegister').live('click', function(){
        alert('A form will appear in a new tab. Register there, then go back here to save your design!');
        $('#loginDialog').dialog("close");
    });

    console.log('show buttons');
    $('.preCheckout').show();
    $("#generatingPdf").hide();
    $("#download").fadeIn();
    $("#checkout").hide();
});



Editor.setActivePages = function(activePages) {
    this.activePages = activePages;

    $.each(Editor.document.pages, function(pageName, page) {
        page.el.hide();
    });

    $.each(activePages, function(i, pageName) {
        Editor.document.pages[pageName].el.show();
    });

    if (activePages.compare(['page1'])) {
        var pageHumanized = "front";
    }
    else if (activePages.compare(['page2', 'page3'])) {
        var pageHumanized = "inside";
    }
    else {
        var pageHumanized = "back";
    }

    $(".pageIndicator span").html(pageHumanized);
    $("#pageSelector a").removeClass("active");
    $("#"+pageHumanized).addClass("active");

    $("#canvas").trigger("changeWidth");
}
