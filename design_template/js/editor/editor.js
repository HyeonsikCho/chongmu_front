/**
 * Created by hyeonsik on 2017-08-02.
 */
var Editor = {
    config : {
        canvas : '#canvas',
        canvasWidth : 420,
        uploadDir : '/editor/uploads/',
        type : ''
    },
    toolMode : 'text',
    selected : null,
    activePages : [],
    saves : {},
    exitWarning : true,
    file : '',
    template : '',
    gridEnabled : false,
    gridSize : 30
};

Editor.setConfig = function(options) {
    $.extend(true, this.config, options);
};

Editor.setDocument = function(doc) {
    this.document = doc;
};

Editor.init = function(options) {
    this.setDocument(options.document);
    this.render();

    if (options.config) {
        this.setConfig(options.config);
    }
    this.unselectObject();

    this.setActivePages(options.activePages);

    this.initUploaders();
    this.binding();

    if (document.location.search) {
        var get = location.search.split('?')[1].split('&'),
            parts = null;

        for (var k in get) {
            if (typeof get[k] != 'string') {
                continue;
            }

            parts = get[k].split('=');

            if (parts[0] == 'template') {
                this.template = parts[1];
            }
        }
    }

    if ( typeof Customer != "undefined" && $.isEmptyObject(this.saves)) {
        Customer.isLogged(function() {
            Editor.getSaves(function(saves) {
            });
        });
    }
};

Editor.setActivePages = function(activePages) {
    this.activePages = activePages;

    $.each(Editor.document.pages, function(pageName, page) {
        page.el.hide();
    });

    $.each(activePages, function(i, pageName) {
        Editor.document.pages[pageName].el.show();
    });
};

Editor.getActivePages = function() {
    var active = {};
    $.each(this.activePages, function(i, pageName) {
        active[pageName] = Editor.document.pages[pageName];
    });
    return active;
};

Editor.render = function() {
    this.document.el = $(this.config.canvas);
    this.document.el.empty();

    $.each(this.document.pages, function(pageName, page) {
        Editor.document.el.append(page.el);
    });

    this.document.el.append('<div class="bleed top"></div><div class="bleed right"></div><div class="bleed bottom"></div><div class="bleed left"></div>');
    this.document.el.append('<div class="safety top"></div><div class="safety right"></div><div class="safety bottom"></div><div class="safety left"></div>');
};

Editor.getPage = function(pageName) {
    var returnElement = null;
    $.each(this.document.pages, function(key, page) {
        if (key == pageName) {
            returnElement = page;
        }
    });
    return returnElement;
};

Editor.getObject = function(objId) {
    var returnElement = null;
    for (var i in this.document.pages) {
        if (this.document.pages.hasOwnProperty(i)) {
            var element = this.document.pages[i].get(objId);
            if (element !== null) {
                returnElement = element;
            }
        }
    }
    return returnElement;
};

Editor.removeObject = function(objId) {
    for (var i in this.document.pages) {
        if (this.document.pages.hasOwnProperty(i)) {
            var element = this.document.pages[i].get(objId);
            if (element !== null) {
                this.document.pages[i].remove(objId);
            }
        }
    }
};

Editor.setGridSize = function(inchSize) {
    inchSize = parseFloat(inchSize);
    if (isNaN(inchSize)) {
        inchSize = 0.25;
    } else if (inchSize > 5) {
        inchSize = 5;
    } else if (inchSize < 0.05) {
        inchSize = 0.05;
    }
    $("#gridSize").val(inchSize);
    var pxSize = this.document.getViewportRatio() * inchSize;
    this.gridSize = pxSize;
};

Editor.enableGrid = function() {
    this.gridEnabled = true;
    this.renderGrid();
};

Editor.disableGrid = function() {
    this.gridEnabled = false;
    this.clearGrid();
    $(".ui-draggable").draggable("option", "grid", false);
};

Editor.renderGrid = function() {
    this.clearGrid();

    $.each(Editor.document.pages, function(pageName, page) {
        var width = page.width;
        var height = page.height;
        var gLine;

        for (i = 0; i <= width; i += Editor.gridSize) {
            gLine = $("<div></div>").addClass("grid").css({
                position : 'absolute',
                top : 0,
                width : 0,
                height : height,
                left : i - 1,
                borderRight : "1px solid #ccc"
            });
            page.el.prepend(gLine.clone());
        }

        for (i = 0; i <= height; i += Editor.gridSize) {
            gLine = $("<div></div>").addClass("grid").css({
                position : 'absolute',
                left : 0,
                width : width,
                top : i - 1,
                borderBottom : "1px solid #ccc"
            });
            page.el.prepend(gLine.clone());
        }
    });
};

Editor.clearGrid = function() {
    $(".grid").remove();
};

Editor.getClosestGrid = function(x) {
    return Math.round(x / this.gridSize) * this.gridSize;
};

Editor.initUploaders = function() {
    if ( typeof qq == "undefined") {
        $.getScript("/design_template/js/editor/fileuploader.js", function() {
            //image uploader

            // Aki #2513
            // ----------
            // Categories
            // ----------
            // birthday
            // christmas
            // flower
            // pattern
            // recycled
            // ribbon
            // snowflake
            // socialmedia
            // valentine

            var uploader = new qq.FileUploader({
                element : document.getElementById('imageUpload'),
                action : 'api.php?method=uploadImage',
                allowedExtensions : ['jpg', 'jpeg', 'png', 'gif'],
                buttonLabel : 'Upload Image',
                drop : false,
                onComplete : function(id, fileName, responseJSON) {
                    console.log(responseJSON);
                    console.log(id);
                    console.log(fileName);
                    if (responseJSON.success) {
                        var name = responseJSON.name;
                        var src = responseJSON.url;

                        var thumb = src;
                        var li = $('<li><img src="' + thumb + '"></li>');
                        li.data({
                            src : src,
                            name : name,
                            checkSize : true
                        });
                        $("#imageCarousel ul").append(li);
                        $("#imageCarousel").gallery_microcarousel("setActive", li);

                        $("#imageTool").click();
                        var activePage = Editor.getPage($(".page:visible:first").attr("id"));
                        Editor.insertObject(50, 50, activePage.name);
                        /* 2016-05-10 Shu Add Start #1457 */
                        //li.hide();
                        //$('.settingWrapper.imageSelectionWrapper').appendTo($('.toolSettings.image'));
                        /* 2016-05-10 Shu Add Start #1457 */
                    } else {
                        alert('Some error has occured. Try to re-upload your file.');
                    }
                    $("#uploadBox").hide();
                },
                onProgress : function(id, fileName, loaded, total) {
                    var text = Math.round(loaded / total * 100) + '% Completed';
                    var filesize = (total / 1048576);

                    $("#uploadBox #uploadFilename").html(fileName);
                    $("#uploadBox #uploadedSize").html(text);
                    $("#uploadBox #totalSize").html(filesize.toFixed(2) + 'MB');
                },
                onSubmit : function(id, fileName) {
                    $("#uploadBox").show();
                }
            });
        });
    }

    var minWidth = Editor.document.width * 300;
    var minHeight = Editor.document.height * 300;
    $("#imageError .width").html(minWidth);
    $("#imageError .height").html(minHeight);
};

Editor.insertObject = function(x, y, pageName) {
    var inputX = x;
    var inputY = y;

    var clickedPage = this.getPage(pageName);

    if (this.toolMode == 'text') {
        var t = {
            type : 'text',
            id : 'text_' + new Date().getTime(),
            x : inputX,
            y : inputY
        };

        $(".toolSettings:visible .setting").each(function() {
            if ($(this).hasClass("ui-slider")) {
                t[$(this).attr("id")] = $(this).slider("value");
            } else {
                var attrname = $(this).attr("name");
                t[attrname] = $(this).val();
                if (attrname === 'fontFamily') {
                    var fontWeight = $(this).find(":selected").data("weight");
                    if (fontWeight) {
                        t.fontFamily = t.fontFamily.substring(0, t.fontFamily.indexOf(':'));
                        t.fontWeight = fontWeight;
                    }
                }
            }
        });

        var obj = clickedPage.add(t);
        obj.el.click();
    }

    if (this.toolMode == 'image') {
        var imageData = $("#imageCarousel").gallery_microcarousel("getActive").data();

        if (imageData == null) {
            alert('You have to upload an image first!');
            return false;
        }

        var i = {
            type : 'image',
            id : 'image_' + new Date().getTime(),
            x : inputX,
            y : inputY,
            src : imageData.src,
            autofit : imageData.autofit !== undefined ? imageData.autofit : true,
            pdfratio : imageData.pdfratio !== undefined ? imageData.pdfratio : 0.33,
            tool: this.toolMode
        };

        //$.extend(i, imageData);

        var obj = clickedPage.add(i);
        obj.el.load(function() {
            obj.el.click();
        });
    }

    // Aki Ticket: #2552
    // Add Clipart selection
    if(this.toolMode == 'clipart'){
        var clipartData = $(".clipartCarousel.selected").clipart_microcarousel("getActive").data();

        if (clipartData == null) {
            alert('No cilpart images found');
            return false;
        }

        var i = {
            type : 'image',
            id : 'image_' + new Date().getTime(),
            x : inputX,
            y : inputY,
            src : clipartData.src,
            autofit : clipartData.autofit !== undefined ? clipartData.autofit : true,
            pdfratio : clipartData.pdfratio !== undefined ? clipartData.pdfratio : 0.33,
            tool: this.toolMode
        };

        var obj = clickedPage.add(i);
        obj.el.load(function() {
            obj.el.click();
        });
    }

    if (this.toolMode == 'shape') {
        var s = {
            type : 'shape',
            id : 'shape_' + new Date().getTime(),
            x : inputX,
            y : inputY,
            cornerRadius : $("#cornerRadius").slider("value")
        };

        $(".toolSettings:visible .setting").each(function() {
            if ($(this).hasClass("ui-slider")) {
                s[$(this).attr("id")] = $(this).slider("value");
            } else {
                s[$(this).attr("name")] = $(this).val();
            }
        });

        if (s.width > 0 && s.height > 0) {
            var obj = clickedPage.add(s);
            obj.el.click();
        }
    }

    if (this.toolMode == 'pattern' && !Editor.blocked) {
        var imageWidth = (Editor.document.height > Editor.document.width) ? Editor.document.height : Editor.document.width;
        imageWidth = imageWidth * Editor.document.getViewportRatio() * 1.5;

        var p = {
            type : 'pattern',
            id : 'pattern_' + new Date().getTime(),
            x : inputX,
            y : inputY,
            imageWidth : imageWidth,
            colors : []
        };

        $(".toolSettings:visible .setting").each(function() {
            if ($(this).hasClass("ui-slider")) {
                p[$(this).attr("id")] = $(this).slider("value");
            } else if ($(this).attr("name") == 'colors[]') {
                p['colors'].push($(this).val());
            } else {
                p[$(this).attr("name")] = $(this).val();
            }
        });

        var obj = clickedPage.add(p);
        obj.el.click();
    }
}

Editor.selectTool = function(element, clearSelected) {
    var tool = $(element).attr("id").match(/(.+)Tool/)[1];
    this.toolMode = tool;

    if (clearSelected) {
        this.unselectObject();
    }

    $(".tool").removeClass("active");
    $(element).addClass("active");

    $(".toolSettings").hide();
    $(".toolSettings." + tool).show();

    if (tool == "shape" && Editor.selected == null) {
        $(".ui-draggable").draggable("disable");
        $(".ui-draggable").css({
            opacity : 1
        });

        $("#shapeScale").val(100);
    } else {
        $(".ui-draggable").draggable("enable");
    }
}

Editor.unselectObject = function() {
    var obj = Editor.getObject(Editor.selected);
    if (obj) {
        obj.unselect();
    }
    Editor.selected = null;
}

Editor.selectObject = function(element, e) {
    if ( element instanceof dObject) {
        var obj = element;
    } else if ( typeof element == "string") {
        var obj = Editor.getObject(element);
    } else {
        var obj = Editor.getObject($(element).attr('id'));
    }

    if (obj != null && obj.templated != true) {
        $(".toolSettings").hide();

        Editor.unselectObject();
        Editor.selected = obj.id;
        obj.select();

        if (obj.type == "text") {
            Editor.selectTool($("#textTool"));
            if (obj.shadow) {
                $("#removeShadow").show();
            } else {
                $("#removeShadow").hide();
            }
        }
        if (obj.type == "image") {
            if(obj.tool){
                Editor.selectTool($("#"+obj.tool+"Tool"));
            }else{
                Editor.selectTool($("#imageTool"));
            }
        }
        if (obj.type == "shape") {
            Editor.selectTool($("#shapeTool"));
        }
        if (obj.type == "pattern") {
            Editor.selectTool($("#patternTool"));
        }
        /* Aki Ticket: #2552 */
        if (obj.type == "clipart"){
            Editor.selectTool($("#clipartTool"));
        }

        $(".toolSettings:visible .setting").each(function() {
            if ($(this).hasClass("ui-slider")) {
                var attr = $(this).attr("id");
                $(this).slider("value", obj[attr]);
            } else {
                var attr = $(this).attr("name");
                $(this).val(obj[attr]);
                if (attr === "fontFamily" && obj.fontWeight) {
                    $(this).val(obj[attr]+":"+obj.fontWeight);
                }

                if ($(this).attr("name") == "textAlign") {
                    $("#align" + obj[attr]).click();
                }
            }
        });

        if ( typeof obj.colors != "undefined") {
            var clone = $(".stripeColors .stripe:first").clone();
            $(".stripeColors .stripe").remove();
            $.each(obj.colors, function(i, color) {
                var stripe = clone.clone();
                stripe.insertBefore("#addStripe");
                stripe.find("input").val(color).bind("change", function(e) {
                    Editor.changeSettings(this, e);
                });
                stripe.find(".colorPreview").css({
                    backgroundColor : obj['colors'][i]
                });
            });
        }

        if (e) {
            e.stopPropagation();
        }
    }
}

Editor.changeSettings = function(element, e) {
    if (this.selected) {
        var obj = this.getObject(this.selected);
        if (obj != null) {
            var data = {};

            if ($(element).hasClass("ui-slider")) {
                data[$(element).attr("id")] = $(element).slider("value");
            } else if ($(element).attr("name") == 'colors[]') {
                data.colors = obj.colors;
                var i = $(element).index(".stripeColors input");
                data['colors'][i] = $(element).val();
            } else if ($(element).attr("name") == 'fontFamily') {
                data.fontFamily = $(element).val();
                var fontWeight = $(element).find(':selected').data('weight');
                if (fontWeight) {
                    data.fontWeight = fontWeight;
                }
            } else {
                data[$(element).attr("name")] = $(element).val();
            }

            obj.update(data);
        }
    }

    if (this.toolMode == 'background') {
        var activePages = this.getActivePages();
        var color = $("#backgroundColor").val();
        $.each(activePages, function(pageName, page) {
            page.setBackground({
                color : color
            });
        });
    }
}

Editor.generate = function(element, e) {
    var data = this.document.serialize();
    // console.log(data);
    // console.log("--------------------------------------");
    // console.log(Editor.document.serialize());
    e.preventDefault();
    e.stopPropagation();

    if ($(".closeEdgeWarning").is(":visible") == false) {
        var close = false;
        //Editor.getCloseToEdgeObjects();
        if (close != false) {
            $(".closeEdgeWarning").slideDown();
            $("#showBleed").click();
            return false;
        }
    }

    var method = "generatePdf";
    if (this.document.type == "halfFold") {
        method = "generateHalfFoldPdf";
    }
    if (this.document.type == "triFold") {
        method = "generateTriFoldPdf";
    }
    if (this.document.templatePdf != undefined && this.document.templatePdf.length > 0) {
        method = "generateTemplatePdf";
    }

    if (this.template != '') {
        method += ('&template=' + this.template);
    }

    // christopher - 2016-05-09 - remove error message if it exists before adding a new one
    var generateCode = $("#generateCode");
    if (generateCode.length > 0) {
        generateCode.remove();
    }

    $("#download").hide();
    $("#generatingPdf").show();

    var isSafari = navigator.vendor && navigator.vendor.indexOf('Apple') > -1 &&
        navigator.userAgent && !navigator.userAgent.match('CriOS');

    var isChromium = window.chrome,
        winNav = window.navigator,
        vendorName = winNav.vendor,
        isOpera = winNav.userAgent.indexOf("OPR") > -1,
        isIEedge = winNav.userAgent.indexOf("Edge") > -1,
        isIOSChrome = winNav.userAgent.match("CriOS");

    // christopher - 2016-08-23 - #2081 - popup blocker on pdf download
    // christopher - 2016-09-19 - #2260 - safari times out automatically after 10 seconds, ensure async
    // christopher - 2016-10-27 - #2461 - async: false no longer avoids popup blocker on chrome, trying safari solution
    if (isIOSChrome || (isChromium !== null && isChromium !== undefined && vendorName === "Google Inc." && isOpera == false && isIEedge == false) || isSafari) {
        var windowReference = window.open("http://www.jukeboxprint.com/editor/file_generating.html", "_blank");
        $.ajax({
            url : 'api.php?method=' + method,
            type : 'post',
            dataType : 'json',
            data : {
                json : data
            },
            success : function(response) {
                console.log(response);
                if (response.link) {
                    Editor.file = response.link;
                    windowReference.location = "/editor/api.php?method=download&file=" + Editor.file;
                    //window.open("/editor/api.php?method=download&file=" + Editor.file, '_blank');
                }
                $("#generatingPdf").hide();
                $("#download").fadeIn();
                $("#checkout").fadeIn();
                setTimeout(function() { windowReference.close(); }, 6000);
            }
        });
    } else {
        $.ajax({
            url : 'api.php?method=' + method,
            type : 'post',
            dataType : 'json',
            async: false,
            data : {
                json : data
            },
            success : function(response) {
                console.log(response);
                if (response.link) {
                    Editor.file = response.link;
                    window.open("/editor/api.php?method=download&file=" + Editor.file, '_blank');
                }
                $("#generatingPdf").hide();
                $("#download").fadeIn();
                $("#checkout").fadeIn();
            }
        });
    }
};

Editor.checkout = function(options) {
    var data = {
        file : Editor.file,
        type : Editor.config.type,
        width : Editor.document.width,
        height : Editor.document.height,
        output : 'offset'
    };
    $.extend(true, data, options);

    Editor.document.activePages = Editor.activePages;
    data.document = Editor.document.serialize();

    $.ajax({
        url : '/editor/api.php?method=checkout',
        type : 'post',
        dataType : 'json',
        data : data,
        success : function(response) {
            if (response.redirect) {
                Editor.exitWarning = false;
                window.location = response.redirect;
            }
        }
    });
}

Editor.loadTemplate = function(templateId, callback) {
    $.ajax({
        url : '/editor/api.php?method=getTemplate',
        type : 'post',
        dataType : 'json',
        data : {
            id : templateId
        },
        success : function(response) {
            if (response.document) {
                var doc = $.parseJSON(response.document);
                Editor.setDocument(new Document(doc));
                Editor.render();
                Editor.unselectObject();
                Editor.setActivePages((doc.activePages) ? doc.activePages : ['page1']);

                if (callback) {
                    callback.call(this, response);
                }
            } else {
                var url = $.url(window.location);
                window.location = url.attr('protocol') + "://" + url.attr('host') + url.attr('path');
            }
        }
    });
}

Editor.loadResizedTemplate = function(templateId, callback) {
    $.ajax({
        url : '/editor/api.php?method=getTemplate',
        type : 'post',
        dataType : 'json',
        data : {
            id : templateId
        },
        success : function(response) {
            if (response.document) {
                var doc = $.parseJSON(response.document);
                doc = Editor.resizeDocument(doc, Editor.document.width, Editor.document.height);
                Editor.setDocument(new Document(doc));
                Editor.render();
                Editor.unselectObject();
                Editor.setActivePages((doc.activePages) ? doc.activePages : ['page1']);

                if (callback) {
                    callback.call(this, response);
                }
            } else {
                var url = $.url(window.location);
                window.location = url.attr('protocol') + "://" + url.attr('host') + url.attr('path');
            }
        }
    });
}

Editor.loadSave = function(saveId, callback) {
    $.ajax({
        url : '/editor/api.php?method=getSavedDesign',
        type : 'post',
        dataType : 'json',
        data : {
            id : saveId
        },
        success : function(response) {
            if (response.document) {
                var doc = $.parseJSON(response.document);
                console.log('loaded document', doc);
                console.log('editor type', Editor.config.type);
                Editor.setDocument(new Document(doc));
                Editor.render();
                Editor.unselectObject();
                Editor.setActivePages((doc.activePages) ? doc.activePages : ['page1']);

                document.location.hash = "save" + saveId;

                if (callback) {
                    callback.call(this, response);
                }

                $(".openSave").fadeIn();
                $("#generate").fadeIn();
                $("#download").fadeIn();


                $(window).trigger('save:loaded', doc);
            } else {
                var url = $.url(window.location);
                window.location = url.attr('protocol') + "://" + url.attr('host') + url.attr('path');
            }
        }
    });
}

Editor.saveCheck = function() {
    if ( typeof Customer != "undefined") {
        Customer.isLogged(function(response) {
            //yes
            Editor.saveDialog();
        }, function(response) {
            //no
            Customer.loginDialog(function() {
                Editor.saveDialog();
            });
        });
    }
}

Editor.connectCheck = function() {
    if ( typeof Customer != "undefined") {
        Customer.isLogged(function(response) {
            //yes
            Editor.connectDialog();
        }, function(response) {
            //no
            Customer.loginDialog(function() {
                Editor.connectDialog();
            });
        });
    }
}

Editor.getSaves = function(callback) {
    $.ajax({
        url : '/editor/api.php?method=getSavedDesigns',
        type : 'post',
        dataType : 'json',
        data : {
            customer_id : Customer.data.id,
            type : Editor.config.type
        },
        success : function(response) {
            if (response) {
                Editor.saves = response;
                Editor.setLoadPanel();

                if (callback) {
                    callback.call(this, response);
                }
            }
        }
    });
}

Editor.saveDialog = function() {
    Editor.getSaves(function() {
        $("#saveDialog .previousSaves tr").not(":first, :last").remove();
        var row = $("#saveDialog .previousSaves tr:last");
        $.each(Editor.saves, function(i, save) {
            if (i != 0) {
                row = row.clone();
                $("#saveDialog .previousSaves table").append(row);
            }
            row.children("td.name").html('<a id="save' + save.id + '" class="previousSave" href="#">' + save.name + '</a>');
            row.children("td.date").html($.datepicker.formatDate('M d, yy', new Date(mysqlToDate(save.date))));
            row.children("td.delete").html('<a id="delete' + save.id + '" class="deleteSave" href="#">Delete</a>');
        });
    });

    $("#saveDialog").dialog("open");
}

Editor.save = function(data, callback) {
    Editor.document.activePages = Editor.activePages;
    data.document = Editor.document.serialize();
    data.type = Editor.config.type;

    $.ajax({
        url : '/editor/api.php?method=saveDesign',
        type : 'post',
        dataType : 'json',
        data : data,
        success : function(response) {
            if (response.id) {
                $("#saveDialog").dialog("close");
                $(".openSave").addClass('designSaved').html('Design Saved!');
                setTimeout(function () {
                    $(".openSave").removeClass('designSaved').html("Save Your Design<div>(You must be a registered customer)</div>");
                }, 4000);

                if (response.id != data.id) {
                    Editor.saves.push(response);
                }
                document.location.hash = "save" + response.id;
                Editor.setLoadPanel();

                if (callback) {
                    callback.call(this, response);
                }
            }
        }
    });
}

Editor.setLoadPanel = function() {
    $("#loadPanel #load option").not(":first").remove();

    var saveId = $.url(window.location).attr('fragment');
    if (saveId != "") {
        saveId = saveId.match(/save(.+)/)[1];
        $("#loadPanel #load").val(saveId);
    }

    $.each(this.saves, function(i, save) {
        var option = $('<option></option>').val(save.id).html(save.name);
        if (saveId == save.id) {
            option.attr("selected", "selected");
        }
        $("#loadPanel #load").append(option);
    });
    $(".loadHeader span").html(this.saves.length);

    if (this.saves.length > 0) {
        $("#loadPanel").slideDown();
    }
}

Editor.deleteSave = function(elem, e) {
    var id = elem.attr("id").match(/delete(\d+)/)[1];
    e.preventDefault();

    $.ajax({
        url : '/editor/api.php?method=deleteDesign',
        type : 'post',
        dataType : 'json',
        data : {
            id : id
        },
        success : function(response) {
            if (response == true) {
                elem.parent().parent().slideUp();
            }
        }
    });
}

Editor.connectDialog = function() {
    Editor.getOpenOrders(function(orders) {
        $("#connectDialog .existingOrders tr").not(":first, :last").remove();
        var row = $("#connectDialog .existingOrders tr:last");
        $.each(orders, function(i, order) {
            if (i != 0) {
                row = row.clone();
                $("#connectDialog .existingOrders table").append(row);
            }
            row.children("td.oid").html(order.oid);
            row.children("td.name").html(order.name);
            row.children("td.product").html(order.product);
            row.children("td.date").html($.datepicker.formatDate('M d, yy', new Date(mysqlToDate(order.date))));
            row.children("td.status").html(order.status);
        });
        if (orders.length > 0) {
            $("#connectDialog .existingOrders table").show();
            $("#connectDialog .noOrders").hide();
        }
    });

    $("#connectDialog").dialog("open");
}

Editor.getSizeFormat = function(id, callback) {
    $.ajax({
        url : '/editor/api.php?method=getSizeFormat',
        type : 'post',
        dataType : 'json',
        data : {
            id : id
        },
        success : function(response) {
            if (response.id) {
                if (callback) {
                    callback.call(this, response);
                }
            }
        }
    });
}

Editor.getOpenOrders = function(callback) {
    $.ajax({
        url : '/editor/api.php?method=getOpenOrders',
        type : 'post',
        dataType : 'json',
        data : {
            customer_id : Customer.data.id
        },
        success : function(response) {
            if (response) {
                if (callback) {
                    callback.call(this, response);
                }
            }
        }
    });
}

Editor.connect = function(data, callback) {
    $.ajax({
        url : '/editor/api.php?method=connect',
        type : 'post',
        dataType : 'json',
        data : data,
        success : function(response) {
            if (response) {
                if (callback) {
                    callback.call(this, response);
                }
            }
        }
    });
}

Editor.getCloseToEdgeObjects = function() {
    var x2limit = Editor.config.canvasWidth - 8;
    var y2limit = (Editor.height / Editor.width) * Editor.config.canvasWidth - 8;
    var close = false;

    $.each(this.document.pages, function(pageName, page) {
        $.each(page.objects, function(objId, object) {
            if (object.type == "text") {
                var x1 = object.x;
                var y1 = object.y;
                var x2 = object.x + object.width;
                var y2 = object.y + object.height;

                if (x1 < 8 || x2 > x2limit) {
                    close = object;
                    return false;
                }
                if (y1 < 8 || y2 > y2limit) {
                    close = object;
                    return false;
                }
            }
        });
    });

    return close;
}

Editor.insertQrCode = function(data) {
    $.ajax({
        url : '/editor/api.php?method=insertQrCode',
        type : 'post',
        dataType : 'json',
        data : data,
        success : function(response) {
            if (response.src) {
                var i = {
                    type : 'image',
                    id : 'image_' + new Date().getTime(),
                    x : 50,
                    y : 50,
                    src : response.src,
                    autofit : true,
                    pdfratio : 1
                };

                var obj = Editor.getPage($(".page:visible:first").attr("id")).add(i);
                obj.el.load(function() {
                    obj.el.click();
                });
            } else {
                alert('Some error occured');
            }
        }
    });
}

Editor.getPattern = function(data, callback) {
    var filename = null;

    Editor.blocked = true;
    if ($("#canvas .loader").length == 0) {
        $("#canvas").append('<div class="loader" />');
    }
    $("#canvas .loader").show();

    $.ajax({
        url : '/editor/api.php?method=generateStripes',
        type : 'post',
        dataType : 'json',
        data : {
            imageWidth : data.imageWidth,
            colors : data.colors,
            stripeSize : data.stripeSize,
            filename : data.backgroundImage
        },
        async : false,
        success : function(response) {
            if (response.filename) {
                filename = response.filename;

                if (callback) {
                    callback.call(this, response);
                }

                Editor.blocked = false;
            }
        }
    });
    return filename;
}

Editor.resizeDocument = function(doc, newWidth, newHeight) {
    var xratio = newWidth / doc.width;
    var yratio = newHeight / doc.height;

    doc.width = newWidth;
    doc.height = newHeight;

    _.each(doc.pages, function(page) {
        page.width = page.width * xratio;
        page.height = page.height * yratio;

        _.each(page.objects, function(object) {
            object.x = object.x * xratio;
            object.y = object.y * yratio;
            if (object.snap) {
                object.snap.x = object.snap.x * xratio;
                object.snap.y = object.snap.y * yratio;
            }
            object.width = object.width * xratio;
            object.height = object.height * xratio;
            if (object.type == 'image') {
                object.draggable = true;
            }

            // Aki Ticket: #2552
            if (object.type == 'clipart') {
                object.draggable = true;
            }
        });
    });
    return doc;
};

Editor.binding = function() {
    if (!this.binded) {
        this.binded = true;
    } else {
        return false;
    }

    //select tool
    $(".tool").click(function(e) {
        Editor.selectTool(this, true);
        e.preventDefault();
    });

    //select object
    $("#canvas .text, #canvas .image, #canvas .shape, #canvas .pattern, #canvas .clipart").live('click', function(e) {
        if ($(this).hasClass(Editor.toolMode) || Editor.toolMode == "image" || Editor.toolMode == "background" || Editor.toolMode == "clipart") {
            Editor.selectObject(this, e);
        }
    });

    //insert shape
    $("#canvas .page").live('mousedown', function(e) {
        if (Editor.toolMode == "shape" && Editor.selected == null) {
            var page = $(this);
            var x = e.pageX - $(this).offset().left;
            var y = e.pageY - $(this).offset().top;

            if (Editor.gridEnabled) {
                x = Editor.getClosestGrid(x);
                y = Editor.getClosestGrid(y);
            }

            var drawBox = $('<div id="drawBox"></div>');
            drawBox.css({
                position : 'absolute',
                background : $("#shapeBackgroundColor").val(),
                opacity : 0.8,
                left : x,
                top : y
            });

            drawBox.data("pageName", $(this).attr("id"));
            page.prepend(drawBox);

            return false;
        }
    });
    $("#canvas .page").live('mousemove', function(e) {
        var drawBox = $("#drawBox");
        if (Editor.toolMode == "shape" && drawBox.length) {
            var x = e.pageX - $(this).offset().left;
            var y = e.pageY - $(this).offset().top;

            var width = x - drawBox.position().left;
            var height = y - drawBox.position().top;

            if (Editor.gridEnabled) {
                width = Editor.getClosestGrid(width);
                height = Editor.getClosestGrid(height);
            }

            if ($("#symmetricShape").val() == 1) {
                width = height = Math.min(width, height);
            }
            var borderRadius = Math.floor(Math.min(width, height) * $("#cornerRadius").slider("value") / 100);

            drawBox.css({
                width : width,
                height : height,
                '-moz-border-radius' : borderRadius,
                'border-radius' : borderRadius
            });

            return false;
        }
    });
    $("body").live('mouseup', function(e) {
        var drawBox = $("#drawBox");
        if (Editor.toolMode == "shape" && drawBox.length) {
            var pageName = drawBox.data("pageName");
            var x = drawBox.position().left;
            var y = drawBox.position().top;
            drawBox.remove();

            $(".toolSettings.shape input[name='height']").val(drawBox.height());
            $(".toolSettings.shape input[name='width']").val(drawBox.width());

            Editor.insertObject(x, y, pageName);
        }
    });

    //insert new objects
    $("#canvas .page").live('click', function(e) {
        if (Editor.toolMode == "text" || Editor.toolMode == "image" || Editor.toolMode == "clipart") {
            var pageName = $(this).attr("id");
            var x = 0;
            var y = 0;

            x = e.pageX - $(this).offset().left;
            y = e.pageY - $(this).offset().top;

            Editor.insertObject(x, y, pageName);
        }
    });

    //image scale slider
    /*$("#scale").slider({
     value : 100,
     min : 0,
     max : 200,
     step : 0.05
     });

     //clipart images scale slider
     $("#clipartScale").slider({
     value : 100,
     min : 0,
     max : 200,
     step : 0.05
     });*/

    $(".scale").slider({
        value : 100,
        min : 0,
        max : 200,
        step : 0.05
    });

    //line height slider
    $("#lineHeight").slider({
        value : 100,
        min : 0,
        max : 200,
        change: function (e, ui) {
            console.log(e, ui);
        }
    });
    $("#lineHeightInput").change(function(event) {
        var newVal = $(this).val();
        $("#lineHeight").slider({
            value : newVal
        });
    });

    //letter spacing slider
    $("#letterSpacing").slider({
        value : 0,
        min : -20,
        max : 20,
        change: function (e, ui) {
            console.log(e, ui);
        }
    });

    //rotate slider
    $(".rotateTool .setting").slider({
        value : 0,
        min : 0,
        max : 360,
        step : 15
    });

    //corner radius slider
    $("#cornerRadius").slider({
        value : 0,
        min : 0,
        max : 100
    });

    //corner radius slider
    $("#shapeScale").slider({
        value : 100,
        min : 0,
        max : 200
    });

    //stripe width slider
    $("#stripeSize").slider({
        value : 10,
        min : 1,
        max : 100
    });

    //change of tool settings
    $(".toolSettings .setting").bind("change slide slidechange", function(e) {
        Editor.changeSettings(this, e);

        if ($(this).attr("id") == "rotateDegree") {
            $(this).closest(".toolSettings").find("[name='rotateDegree']").val($(this).slider("value"));
        }
        if ($(this).attr("name") == "rotateDegree") {
            $(this).closest(".toolSettings").find("#rotateDegree").slider("value", $(this).val());
        }
    });

    //delete & move object with keyboard
    $(document).keydown(function(e) {
        if (Editor.selected && $("textarea").is(":focus") == false && $("input").is(":focus") == false) {
            var obj = Editor.getObject(Editor.selected);

            //delete
            if (e.keyCode == 46 || e.keyCode == 8) {
                Editor.removeObject(Editor.selected);
                e.preventDefault();
            }

            //move
            if (obj != null && obj.draggable) {
                var x = Math.round(obj.x);
                var y = Math.round(obj.y);
                if (e.keyCode >= 37 && e.keyCode <= 40) {
                    if (e.keyCode == 38) {
                        y = y - 1;
                    }
                    if (e.keyCode == 39) {
                        x = x + 1;
                    }
                    if (e.keyCode == 40) {
                        y = y + 1;
                    }
                    if (e.keyCode == 37) {
                        x = x - 1;
                    }
                    obj.update({
                        x : x,
                        y : y
                    });
                    e.preventDefault();
                }
            }
        }
    });

    //color selector
    $(".colorSelector .colorBox").live('click', function() {
        var color = ($(this).css('background-color') != 'transparent') ? $(this).css('background-color') : null;
        $(this).parent().children("input").val(color).change();
    });

    //generate
    $("#download").click(function(e) {
        Editor.generate(this, e);
    });

    //open save
    $(".openSave").click(function(e) {
        Editor.saveCheck();
        e.preventDefault();
    });

    //save
    $("#save").click(function(e) {
        Editor.save({
            customer_id : Customer.data.id,
            id : $("#saveId").val(),
            name : $("#saveName").val()
        });
        e.preventDefault();
    });

    // Insert item if thumbnail is clicked
    $(".clipartCarousel li").click(function(e){
        var activePage = Editor.getPage($(".page:visible:first").attr("id"));
        Editor.insertObject(50, 50, activePage.name);
    });

    /*$("#imageCarousel li").click(function(e){
     var activePage = Editor.getPage( $(".page:visible:first").attr("id") );
     Editor.insertObject(50, 50, activePage.name);
     });*/

    //open connect to existing order
    $(".openConnect").click(function(e) {
        Editor.connectCheck();
        e.preventDefault();
    });

    //continue to checkout
    $("#checkout").click(function(e) {
        $(".output").slideToggle();
        e.preventDefault();
    });

    //select print type and proceed to checkout
    $(".output a").click(function(e) {
        var elem = $(this);
        e.preventDefault();

        var data = {output : elem.attr("id")};
        if (elem.attr("href")!=="#") {
            data.buynow = elem.attr("href");
        }
        Editor.checkout(data);
    });

    //update text instantly
    $(".toolSettings textarea").keyup(function(e) {
        $(this).change();
    });

    //text align
    $(".alignButton").click(function(e) {
        var val = $(this).attr("id").match(/align(.+)/)[1];
        $("#textAlign").val(val).change();

        $(".toolSettings.text textarea").css({
            textAlign : val
        });

        $(".alignButton").removeClass("active");
        $(this).addClass("active");
    });

    //reset textarea
    $("#textTool").click(function(e) {
        $(".toolSettings.text textarea").val("Enter Your Text");
    });

    //clear textarea / input
    $("textarea, input").click(function(e) {
        if ($(this).val() == "Enter Your Text" || $(this).val() == "Enter File Name") {
            $(this).val("");
        }
    });

    // Aki #2513
    // ----------
    // Categories
    // ----------
    // birthday
    // christmas
    // flower
    // pattern
    // recycled
    // ribbon
    // snowflake
    // socialmedia
    // valentine

    // Gallery

    //setup image thumb carousel
    $("#imageCarousel").gallery_microcarousel({
        change : function(li) {
            $("#imageTool").click();
            $("#imageCarousel .imagename").html(li.data("name"));
        },
        init : function(li) {
            $("#imageCarousel .imagename").html(li.data("name"));
        }
    });

    /* Aki Ticket: #2552*/
    $('.clipartCarousel').each(function(){
        $(this).clipart_microcarousel({
            change : function(li) {
                $("#clipartTool").click();
                var categoryName = $('#category_selector option:selected').attr('category_name');
                if (!categoryName) {
                    categoryName = $('#category_selector option.category_list:first').attr('category_name');
                }
                var imageCnt = $('#'+categoryName+' li').length;
                var imageIdx = li.data("idx");
                $('.clipart .imageSelectionWrapper .image-idx').text('Image '+imageIdx+' of '+imageCnt);
            },
            init : function(li) {
                var categoryName = 'christmas';
                var imageCnt = $('#'+categoryName+' li').length;
                var imageIdx = li.data("idx");
                $('.clipart .imageSelectionWrapper .image-idx').text('Image '+imageIdx+' of '+imageCnt);
            }
        });
    });

    $('.clipartCarousel').hide();
    var categoryName = $('#category_selector option.category_list:first').attr('category_name');
    $('.clipartCarousel#'+categoryName).addClass('selected').show();

    $('#category_selector').change(function(e){
        var optionCategoryName = $('option:selected', this).attr('category_name');
        var imageCnt = $('#'+optionCategoryName+' li').length;
        var imageIdx = $('#'+optionCategoryName+' li.active').eq(0).attr('data-idx');
        $('.clipartCarousel').hide();
        $('.clipartCarousel').removeClass('selected');

        $('.clipartCarousel#'+optionCategoryName).addClass('selected').show();

        $('.clipart .imageSelectionWrapper .image-idx').text('Image '+imageIdx+' of '+imageCnt);

    });


    //shape selection (rect, circle)
    $("#shapeSelector a").click(function(e) {
        $("#shapeTool").click();

        var shape = $(this).attr("id");
        if (shape == "rectangle") {
            $("#symmetricShape").val(0);
            $("#cornerRadius").slider("value", 0);
        }
        if (shape == "square") {
            $("#symmetricShape").val(1);
            $("#cornerRadius").slider("value", 0);
        }
        if (shape == "circle") {
            $("#symmetricShape").val(1);
            $("#cornerRadius").slider("value", 100);
        }

        $("#shapeSelector a").removeClass("active");
        $(this).addClass("active");

        e.preventDefault();
    });

    //rewrite previous save
    $(".previousSave").live('click', function(e) {
        var id = $(this).attr("id").match(/save(\d+)/)[1];
        var name = $(this).text();
        $("#saveId").val(id);
        $("#saveName").val(name);

        e.preventDefault();
    });

    //delete previous save
    $(".deleteSave").live('click', function(e) {
        Editor.deleteSave($(this), e);
    });

    //save dialog
    $("#saveDialog").dialog({
        autoOpen : false,
        width : 500,
        modal : true,
        resizable : false,
        dialogClass : 'saveDialog'
    });

    //connect to order dialog
    $("#connectDialog").dialog({
        autoOpen : false,
        width : 800,
        modal : true,
        resizable : false,
        dialogClass : 'connectDialog'
    });

    //connect dialog - select order
    $("#connectDialog .existingOrders tr").live('click', function() {
        $("#connectDialog .existingOrders tr").removeClass("selected");
        $(this).addClass("selected");
        $("#connect").show();
    });

    //connect with order
    $("#connect").click(function(e) {
        e.preventDefault();
        var data = {
            oid : $("#connectDialog .existingOrders .selected .oid").html(),
            file : Editor.file
        };
        Editor.connect(data, function() {

        });
    });

    //prevent window close
    $(window).bind('beforeunload', function() {
        if (Editor.exitWarning == true) {
            return 'The designs you made will be lost if you navigate away from this page.';
        }
    });

    //load saved design
    $("#load").change(function(e) {
        var saveId = $(this).val();
        if (saveId != "") {
            Editor.loadSave(saveId);
        }
    });

    //error dialog
    $("#imageError").dialog({
        autoOpen : false,
        width : 400,
        modal : true,
        resizable : false,
        dialogClass : 'imageError'
    });

    //close various boxes
    $(".closeDialog").click(function(e) {
        $(this).closest(".ui-dialog-content").dialog("close");
        e.preventDefault();
    });

    //delete active object
    $("#delete").click(function(e) {
        Editor.removeObject(Editor.selected);
        e.preventDefault();
    });

    //show help
    $("#helpWindow").load("help_" + Editor.config.type + ".html", function(responseText, textStatus) {
        if (textStatus == "error") {
            $("#helpWindow").load("help_greeting_card.html");
        }
    });
    $("#helpWindow").dialog({
        dialogClass : 'helpWindow',
        autoOpen : false,
        width : 680,
        modal : true
    });
    $("#help").click(function() {
        $("#helpWindow").dialog("open");
        return false;
    });

    //show bleed
    $("#showBleed").click(function() {
        $(".bleed").fadeToggle();
        if ($(this).html() == 'Show Cut Area') {
            $(this).html('Hide Cut Area');
        } else {
            $(this).html('Show Cut Area');
        }
        return false;
    });

    //show safety margin
    $("#showSafety").click(function() {
        $(".safety").fadeToggle();
        if ($(this).html() == 'Show Safety Margin') {
            $(this).html('Hide Safety Margin');
        } else {
            $(this).html('Show Safety Margin');
        }
        return false;
    });

    //font preview
    $("#fontFamily option").each(function() {
        var fontFamily = $(this).attr("value");
        var fontWeight = $(this).data("weight");
        $(this).css({
            fontFamily : fontFamily,
            fontSize : 18
        });
        if (fontWeight) {
            $(this).css({
                fontWeight : fontWeight
            });
        }
    });
    $("#fontFamily").height(20);

    //qr code dialog
    $("#qrCode").dialog({
        autoOpen : false,
        width : 400,
        modal : true,
        resizable : false,
        dialogClass : 'qrCode'
    });

    //qr help dialog
    $("#qrHelp").dialog({
        autoOpen : false,
        width : 400,
        modal : true,
        resizable : false,
        dialogClass : 'qrHelp'
    });

    //open QR box
    $("#openQrBox").click(function(e) {
        e.preventDefault();
        $("#qrCode").dialog("open");
    });

    //open QR help
    $("#openQrHelp").click(function(e) {
        e.preventDefault();
        $("#qrHelp").dialog("open");
    });

    //insert QR code on canvas
    $("#insertQrCode").click(function(e) {
        e.preventDefault();
        var text = $("#qrEncodeText").val();
        if (text == '') {
            alert('Please enter some text');
            return false;
        }

        Editor.insertQrCode({
            text : text
        });
    });

    //text shasow dialog
    $("#addShadowDialog").dialog({
        autoOpen : false,
        width : 460,
        modal : true,
        resizable : false,
        dialogClass : 'addShadowDialog'
    });

    //open add shadow
    $("#openAddShadow").click(function(e) {
        e.preventDefault();
        if (Editor.selected == null) {
            alert("You have to create/select text first");
        } else {
            $("#addShadowDialog").dialog("open");
            $("#shadowObject").val(Editor.selected);

            var shadowPreview = $.extend(true, {}, Editor.getObject(Editor.selected), {
                el : Editor.getObject(Editor.selected).el.clone()
            });
            shadowPreview.el.attr("id", Editor.selected + "_preview").css({
                position : 'static'
            });
            $(".shadowPreview").html(shadowPreview.el);
            $("#addShadowDialog").data("shadowPreview", shadowPreview);
            $(".shadowPreview").css({
                backgroundColor : $(".page:visible:first").css("backgroundColor")
            });

            $(".shadowDirection .bottomright").click();
        }
    });

    //shadow direction
    $(".shadowDirection .arrow").click(function(e) {
        e.preventDefault();
        var button = $(this);
        var directions = ["topright", "topleft", "bottomright", "bottomleft"];
        $.each(directions, function() {
            if (button.hasClass(this)) {
                $(".shadowDirection input").val(this);
                $("#addShadowDialog").data("shadowPreview").applyShadow($("#shadowColor").val(), $("#shadowOffset").slider("value"), $("#shadowDirection").val());
            }
        });
        $(".shadowDirection .arrow").removeClass("active");
        button.addClass("active");
    });

    //shadow offset
    $("#shadowOffset").slider({
        value : 1,
        min : 0,
        max : 20
    }).bind("slide slidechange", function(e) {
        $("#addShadowDialog").data("shadowPreview").applyShadow($("#shadowColor").val(), $("#shadowOffset").slider("value"), $("#shadowDirection").val());
    });

    //shadow color
    $("#shadowColor").change(function() {
        $("#addShadowDialog").data("shadowPreview").applyShadow($("#shadowColor").val(), $("#shadowOffset").slider("value"), $("#shadowDirection").val());
    });

    //apply shadow
    $("#applyShadow").click(function(e) {
        Editor.getObject($("#shadowObject").val()).applyShadow($("#shadowColor").val(), $("#shadowOffset").slider("value"), $("#shadowDirection").val());
        $("#removeShadow").show();
        e.preventDefault();
    });

    //remove shadow
    $("#removeShadow").click(function(e) {
        if (Editor.selected) {
            Editor.getObject(Editor.selected).applyShadow('transparent', 0);
            e.preventDefault();
        }
    });

    //remove preloader
    $(window).load(function() {
        $(".preloader").remove();
        $(".rblock").css({
            margin : 0
        });
        // if ($(".sideSuggestionBox").hasClass("hide") == false) {
        // $(".sideSuggestionBox").fadeIn();
        // }
        $(".jPicker.Container").css("top", $("#canvas").offset().top);
    });

    //open stripe tab
    $(".stripeColors .stripe .openPalette, .stripeColors .stripe .colorPreview").live('click', function(e) {
        e.preventDefault();
        $(".stripeColors .stripe .colorSelector").hide();
        $(this).parent().children(".colorSelector").slideDown();
    });

    //stripe colours
    $(".stripeColors .stripe .colorSelector input").live('change', function() {
        $(this).parent().slideUp();
        var color = $(this).val();
        $(this).closest(".stripe").children(".colorPreview").css({
            backgroundColor : color
        });
    });
    $(".stripeColors .stripe .colorSelector").each(function() {
        random = Math.round(Math.random() * $(this).children(".colorBox").length);
        $(this).children(".colorBox").eq(random).click();
    });

    //add stripe
    $("#addStripe").click(function(e) {
        e.preventDefault();
        var stripe = $(".stripeColors .stripe:first").clone();
        $(this).before(stripe);
        random = Math.round(Math.random() * stripe.find(".colorBox").length);
        stripe.find(".colorBox").eq(random).click();
    });

    //remove stripe
    $(".stripeColors .stripe .removeStripe").live('click', function(e) {
        e.preventDefault();
        $(this).parent().remove();
    });

    //insert pattern
    $("#insertPattern").click(function(e) {
        e.preventDefault();
        var activePage = Editor.getPage($(".page:visible:first").attr("id"));
        var offset = (Editor.document.height > Editor.document.width) ? Editor.document.height : Editor.document.width;
        offset = offset * Editor.document.getViewportRatio() * (-0.25)
        Editor.insertObject(offset, offset, activePage.name);
    });

    //disable slide event on stripe size
    $("#stripeSize").unbind("slide");

    //enable grid snap
    $("#gridToggle").click(function(e) {
        e.preventDefault();
        $(this).toggleClass("enabled");
        if ($(this).hasClass("enabled")) {
            $("#gridSizeWrapper").show();
            Editor.setGridSize($("#gridSize").val());
            Editor.enableGrid();
        } else {
            $("#gridSizeWrapper").hide();
            Editor.disableGrid();
        }
    });

    //change grid size
    $("#gridSize").change(function() {
        Editor.setGridSize($("#gridSize").val());
        Editor.enableGrid();
    });

    //custom colour picker
    $(".customColour").jPicker({
        window : {
            expandable : true
        },
        images : {
            clientPath : '/design_template/js/lib/images/'
        }
    }, function(color, context) {
        all = color.val('all');
        $(this).parent().children("input").val("#" + all.hex).change();
    });
}
function Document(options) {
    this.pages = {};
    this.pdfratio = 1
    this.width = options.width;
    this.height = options.height;
    this.type = 'generic';
    this.orientation = 'landscape';

    if (options.type) {
        this.type = options.type;
    }

    if (options.orientation) {
        this.orientation = options.orientation;
    }

    if (options.templatePdf) {
        this.templatePdf = options.templatePdf;
    }

    if (options.cropmark) {
        this.cropmark = options.cropmark;
    }

    if (options.cropmarks) {
        this.cropmarks = options.cropmarks;
    }

    if (options.pages) {
        $.each(options.pages, $.proxy(function(i, page) {
            if ( page instanceof Page) {
                this.addPage(page);
            } else {
                newPage = new Page({
                    name : page.name,
                    width : page.width,
                    height : page.height,
                    backgroundColor : page.backgroundColor,
                    objects : page.objects
                });
                this.addPage(newPage);
            }
        }, this));
    } else if (options.numPages) {
        this.viewportRatio = Editor.config.canvasWidth / options.width;

        var pageWidth = Editor.config.canvasWidth;
        var pageHeight = this.viewportRatio * options.height;

        var i = 1;
        for (i; i <= options.numPages; i++) {
            var page = new Page({
                name : 'page' + i,
                width : pageWidth,
                height : pageHeight,
                backgroundColor : '#fff'
            });
            this.addPage(page);
        }
    }

}

Document.prototype.addPage = function(page) {
    this.pages[page.name] = page;
    this.viewportRatio = null;
}

Document.prototype.update = function(options) {
    $.extend(true, this, options);
    this.viewportRatio = Editor.config.canvasWidth / this.width;

    var pageWidth = Editor.config.canvasWidth;
    var pageHeight = this.viewportRatio * options.height;

    $.each(this.pages, function(pageName, page) {
        page.width = pageWidth;
        page.height = pageHeight;

        page.el.css({
            width : pageWidth,
            height : pageHeight
        });
    });
}

Document.prototype.serialize = function() {
    var serialized = jsonStringify(this);
    return serialized;
}
jsonStringify = function(obj) {
    var t = typeof (obj);
    if (t != "object" || obj === null) {
        // simple data type
        if (t == "string")
            obj = '"' + obj + '"';
        return String(obj);
    } else {
        // recurse array or object
        var n, v, json = [], arr = (obj && obj.constructor == Array);
        for (n in obj) {
            v = obj[n];
            t = typeof (v);
            if ((t == "string" || t == "number" || t == "boolean" || v == null || v.constructor == Array || __getClass(v) == 'Object') && v instanceof jQuery == false) {
                if (t == "string") {
                    v = v.replace(/\n/gi, "\\n");
                    v = v.replace(new RegExp('"', "g"), '\\\"');
                    v = v.replace(/\t/g, '&nbsp;&nbsp;&nbsp;');
                    v = '"' + v + '"';
                } else if (t == "object" && v !== null) {
                    v = jsonStringify(v);
                }
                json.push(( arr ? "" : '"' + n + '":') + String(v));
            }
        }
        return ( arr ? "[" : "{") + String(json) + ( arr ? "]" : "}");
    }
};

Document.prototype.getViewportRatio = function() {
    if ( typeof this.viewportRatio == 'undefined' || this.viewportRatio == null) {
        this.viewportRatio = Editor.config.canvasWidth / this.width;
    }
    return this.viewportRatio;
};

function dObject(data, page) {
    this.draggable = true;
    this.rotateDegree = 0;

    for (var i in data) {
        this[i] = data[i];
    }

    this.render(page);
}

dObject.prototype.render = function(page) {
    if (this.type == 'text') {
        this.lineHeight = (this.lineHeight) ? this.lineHeight : 100;
        this.letterSpacing = (this.letterSpacing) ? this.letterSpacing : 0;

        this.el = $('<div class="text"></div>');
        this.el.html(this.text);
        this.el.css({
            fontWeight : this.fontWeight,
            fontSize : this.fontSize,
            fontFamily : this.fontFamily,
            color : this.color,
            textAlign : this.textAlign,
            lineHeight : this.lineHeight + "%",
            letterSpacing : this.letterSpacing
        });

        if (this.shadow) {
            this.el.css({
                textShadow : this.shadow.color + ' ' + this.shadow.x + 'px ' + this.shadow.y + 'px 0px'
            });
        }
    } else if (this.type == 'image') {
        this.el = $('<img class="image" />');
        this.el.attr('src', this.src);

        if ( typeof this.width != "undefined" && typeof this.height != "undefined") {
            this.el.width(this.width);
            this.el.height(this.height);
        } else {
            this.el.load($.proxy(function() {
                this.originalWidth = this.el.width();
                this.originalHeight = this.el.height();

                if (this.scale) {
                    this.resize();
                } else {
                    this.scale = 100;
                    if (this.autofit) {
                        this.autoResize(page);
                    }
                }

                if (this.checkSize) {
                    var minWidth = parseInt($("#imageError .width").html());
                    var minHeight = parseInt($("#imageError .height").html());

                    if (this.originalWidth < minWidth || this.originalHeight < minHeight) {
                        $("#imageError .tooSmall").show();
                        $("#imageError .tooBig").hide();
                        $("#imageError").dialog("open");
                    }
                    if (this.originalWidth > (minWidth + 300) && this.originalHeight > (minHeight + 300)) {
                        $("#imageError .tooSmall").hide();
                        $("#imageError .tooBig").show();
                        $("#imageError").dialog("open");
                    }
                }
            }, this));
        }
    } else if (this.type == 'shape') {
        this.originalWidth = this.width;
        this.originalHeight = this.height;
        this.borderRadius = Math.floor(Math.min(this.width, this.height) * this.cornerRadius / 100);

        this.el = $('<div class="shape"></div>');
        this.el.css({
            width : this.width,
            height : this.height,
            backgroundColor : this.backgroundColor,
            '-moz-border-radius' : this.borderRadius,
            'border-radius' : this.borderRadius
        });
    } else if (this.type == 'pattern') {
        this.el = $('<img class="pattern" />');
        this.src = '/editor/temp/' + Editor.getPattern(this);
        this.width = this.height = this.imageWidth;
        this.el.attr('src', this.src);
        this.el.css({
            width : this.width,
            height : this.height
        });
        this.el.load($.proxy(function() {
            $("#canvas .loader").hide();
        }, this));
    }

    this.el.attr("id", this.id);
    this.el.css({
        position : 'absolute',
        top : this.y,
        left : this.x,
        display : 'inline-block'
    });

    page.el.append(this.el);

    this.width = (this.width) ? this.width : this.el.width();
    this.height = (this.height) ? this.height : this.el.height();

    if (this.draggable) {
        var obj = this;
        this.el.draggable({
            start : function() {
                Editor.selectObject(obj.el);
            },
            stop : function() {
                obj.update({
                    x : parseFloat(obj.el.css("left")),
                    y : parseFloat(obj.el.css("top"))
                });
            }
        });
        if (Editor.gridEnabled) {
            this.el.draggable("option", "grid", [Editor.gridSize, Editor.gridSize]);
            this.x = Editor.getClosestGrid(this.x);
            this.y = Editor.getClosestGrid(this.y);
            this.el.css({
                top : this.y,
                left : this.x,
            });
        }
    }

    if (this.snap) {
        this.keepSnapped();
    }

    if (this.rotateDegree) {
        this.rotate();
    }
}

dObject.prototype.update = function(data) {
    for (var i in data) {
        if (i != 'stripeSize') {
            this[i] = data[i];
        }
        if (i === 'fontFamily') {
            if (data[i].indexOf(':') > 0) {
                this[i] = data[i].substring(0, data[i].indexOf(':'));
            }
        }
    }

    if (this.type == 'text') {
        this.el.html(this.text);
        this.el.css({
            fontWeight : this.fontWeight,
            fontSize : this.fontSize,
            fontFamily : this.fontFamily,
            textAlign : this.textAlign,
            lineHeight : this.lineHeight + "%",
            letterSpacing : this.letterSpacing,
            color : this.color
        });
    }

    if (this.type == 'image' && data.scale) {
        this.resize();
    }

    if (this.type == 'shape') {
        this.borderRadius = Math.floor(Math.min(this.width, this.height) * this.cornerRadius / 100);

        if (data.shapeScale) {
            this.width = this.originalWidth * data.shapeScale / 100;
            this.height = this.originalHeight * data.shapeScale / 100;
        }

        this.el.css({
            width : this.width,
            height : this.height,
            backgroundColor : this.backgroundColor,
            '-moz-border-radius' : this.borderRadius,
            'border-radius' : this.borderRadius
        });
    }

    if (this.type == 'pattern' && ((data.stripeSize && data.stripeSize != this.stripeSize) || data.colors)) {
        if (data.stripeSize) {
            this.stripeSize = data.stripeSize;
        }
        this.src = '/editor/temp/' + Editor.getPattern(this);
        this.el.attr("src", this.src);
    }

    if (data.shadow) {
        this.shadow = data.shadow;
        this.el.css({
            textShadow : this.shadow.color + ' ' + this.shadow.x + 'px ' + this.shadow.y + 'px 0px'
        });
    }

    if (data.x || data.y) {
        if (Editor.gridEnabled) {
            this.el.draggable("option", "grid", [Editor.gridSize, Editor.gridSize]);
            this.x = Editor.getClosestGrid(this.x);
            this.y = Editor.getClosestGrid(this.y);
        }

        this.el.css({
            left : this.x,
            top : this.y
        });
    }

    this.width = this.el.width();
    this.height = this.el.height();

    if (this.type == "text" && this.width < 50) {
        this.width = 50;
    }

    if (this.snap) {
        this.keepSnapped();
    }

    if ( typeof data.rotateDegree !== "undefined") {
        this.rotate();
    }
}

dObject.prototype.remove = function(data) {
    this.el.remove();
}

dObject.prototype.resize = function() {
    if (this.type == 'image') {
        var pdfratio = (this.pdfratio) ? this.pdfratio : Editor.document.pdfratio;
        var newWidth = this.originalWidth * pdfratio * this.scale / 100;
        var newHeight = this.originalHeight * pdfratio * this.scale / 100;

        this.el.width(newWidth);
        this.el.height(newHeight);
        this.width = newWidth;
        this.height = newHeight;
    }
}

dObject.prototype.autoResize = function(page) {
    var pdfratio = (this.pdfratio) ? this.pdfratio : Editor.document.pdfratio;
    var inputWidth = page.width / (this.originalWidth * pdfratio);
    var inputHeight = page.height / (this.originalHeight * pdfratio);

    if (inputWidth < 1 && inputWidth < inputHeight) {
        this.scale = inputWidth * 100;
    } else if (inputHeight < 1 && inputHeight <= inputWidth) {
        this.scale = inputHeight * 100;
    }

    this.resize();
}

dObject.prototype.rotate = function() {
    if ( typeof this.rotateDegree === "undefined") {
        this.rotateDegree = 0;
    }

    this.el.css({
        "-moz-transform" : "rotate(" + this.rotateDegree + "deg)",
        "-webkit-transform" : "rotate(" + this.rotateDegree + "deg)",
        "-ms-transform" : "rotate(" + this.rotateDegree + "deg)"
    });
}

dObject.prototype.keepSnapped = function(data) {
    if (this.type == 'text') {
        if (this.textAlign == "Center") {
            var x = this.snap.x - (this.width / 2);
            var y = this.snap.y - (this.height / 2);
        } else if (this.textAlign == "Right") {
            var x = this.snap.x - this.width;
            var y = this.snap.y;
        } else {
            var x = this.snap.x;
            var y = this.snap.y;
        }

        this.x = x;
        this.y = y;
        this.el.css({
            left : x,
            top : y
        });
    }
    if (this.type == 'image') {
        $(this.el).load($.proxy(function() {
            var x = this.snap.x - (this.el.width() / 2);
            var y = this.snap.y - (this.el.height() / 2);

            this.x = x;
            this.y = y;
            this.el.css({
                left : x,
                top : y
            });
        }, this));
    }
}

dObject.prototype.applyShadow = function(color, offset, position) {
    var shadow = {
        color : color,
        x : offset,
        y : offset
    };
    if (position == "topleft") {
        shadow.x = shadow.x * (-1);
        shadow.y = shadow.y * (-1);
    }
    if (position == "topright") {
        shadow.y = shadow.y * (-1);
    }
    if (position == "bottomleft") {
        shadow.x = shadow.x * (-1);
    }
    this.update({
        shadow : shadow
    });
}

dObject.prototype.unselect = function() {
    this.el.removeClass("objSelected");
}

dObject.prototype.select = function() {
    this.el.addClass("objSelected");
}
function Page(options) {
    this.name = 'page';
    this.width = 800;
    this.height = 600;
    this.backgroundColor = '#ffc';
    this.objects = {};

    $.extend(true, this, options);

    this.render();

    if (options.objects) {
        $.each(options.objects, $.proxy(function(objId, obj) {
            this.add(obj);
        }, this));
    }
}

Page.prototype.render = function() {
    this.el = $('<div class="page"></div>');
    this.el.attr("id", this.name);
    this.el.css({
        position : 'relative',
        width : this.width,
        height : this.height,
        backgroundColor : this.backgroundColor,
        "float" : "left"
    });
    // 		this.el.append('<div class="safeArea"></div>');
}

Page.prototype.add = function(data) {
    var objId = data.id;
    this.objects[objId] = new dObject(data, this);
    return this.objects[objId];
}

Page.prototype.get = function(objId) {
    if (this.objects[objId] !== undefined) {
        return this.objects[objId];
    } else {
        return null;
    }
}

Page.prototype.remove = function(objId) {
    if (this.objects[objId] !== undefined) {
        this.objects[objId].remove();
        delete this.objects[objId];
    }
}

Page.prototype.setBackground = function(options) {
    this.backgroundColor = options.color;
    this.el.css({
        background : this.backgroundColor
    });
    $("#canvas").css({
        backgroundColor : this.backgroundColor
    });
}
function __getClass(object) {
    return Object.prototype.toString.call(object)
        .match(/^\[object\s(.*)\]$/)[1];
};

jQuery.expr[':'].focus = function(elem) {
    return elem === document.activeElement && (elem.type || elem.href );
};

Array.prototype.compare = function(testArr) {
    if (this.length != testArr.length)
        return false;
    for (var i = 0; i < testArr.length; i++) {
        if (this[i].compare) {
            if (!this[i].compare(testArr[i]))
                return false;
        }
        if (this[i] !== testArr[i])
            return false;
    }
    return true;
}
function mysqlToDate(timestamp) {
    //function parses mysql datetime string and returns javascript Date object
    //input has to be in this format: 2007-06-05 15:26:02
    var regex = /^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
    var parts = timestamp.replace(regex, "$1 $2 $3 $4 $5 $6").split(' ');
    return new Date(parts[0], parts[1] - 1, parts[2], parts[3], parts[4], parts[5]);
}
