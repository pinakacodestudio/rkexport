var scalefac = 0.50;
var text_max = boxincludetext_max = 150;
$(document).ready(function() {
    var isvalidfiletext = 0;
    $('.productfile').change(function() {
        validfile($(this), this);
    });
    $("#price_div,#discount_div").hide();
    $("#stock_div").hide();
    // $("#barcode_div,#barcodeimage_div,#sku_div").hide();
    if (ACTION == 1) {
        if ($("#checkuniversal").prop('checked') == true) {
            $("#price_div").show();
            $("#stock_div").show();
            $("#prices_div").hide();
            $("#pointspriority_div").hide();
            $("#barcode_div,#barcodeimage_div,#sku_div,#orderquantitydiv,#minimumstocklimit_div,#weight_div,#pricetype_div,#discount_div,#addpriceinpricelist_div,#minimumsalesprice_div").show();

            if ($("#multipleqty").is(':checked') == true) {
                $("#multiplepricesection").show();
                $("#price_div,#discount_div").hide();
            }
        } else {
            $("#price_div,#discount_div,#multiplepricesection").hide();
            $("#stock_div").hide();
            $("#prices_div").hide();
            $("#pointspriority_div").show();
            $("#barcode_div,#barcodeimage_div,#sku_div,#orderquantitydiv,#minimumstocklimit_div,#weight_div,#pricetype_div,#addpriceinpricelist_div,#minimumsalesprice_div").hide();
            $('#weight').val('0');
        }
    }

    $("input[name=pricetype]").change(function(e) {

        if ($(this).val() == 0) {
            $("#price_div,#discount_div").show();
            $("#multiplepricesection").hide();
        } else {
            $("#price_div,#discount_div").hide();
            $("#multiplepricesection").show();
        }
    });

    $("#checkuniversal").click(function() {
        if ($("#checkuniversal").prop('checked') == true) {
            $("#price_div,#discount_div").show();
            $("#stock_div").show();
            $("#prices_div").hide();
            $("#pointspriority_div").hide();
            $("#barcode_div,#barcodeimage_div,#sku_div,#orderquantitydiv,#minimumstocklimit_div,#weight_div,#pricetype_div,#addpriceinpricelist_div,#minimumsalesprice_div").show();

            if ($("#multipleqty").is(':checked') == true) {
                $("#multiplepricesection").show();
                $("#price_div,#discount_div").hide();
            }
        } else {
            $("#price_div,#discount_div,#multiplepricesection").hide();
            $("#stock_div").hide();
            $("#pointspriority_div").show();
            $("#barcode_div,#barcodeimage_div,#sku_div,#orderquantitydiv,#minimumstocklimit_div,#weight_div,#pricetype_div,#addpriceinpricelist_div,#minimumsalesprice_div").hide();
            if (ACTION == 0) {
                $("#prices_div").show();
            }
            $("#weight").val('0');
        }
    })

    //trigger blur once for the initial setup:
    $('input[name="price[]"]').trigger("blur");

    $('#universalprice').on("blur", function() {
        if ($(this).val().trim().length == 0) {
            $(this).val("0.00");
        }
    });
    //trigger blur once for the initial setup:
    $('#universalprice').trigger("blur");

    if (ACTION == 1) {
        // countlength();
    }

    $("#discount").keyup(function(e) {
        if ($(this).val() > 100) {
            $(this).val('100.00');
        }
    });
    $(function() {
        $('.panel-heading.filter-panel').click(function() {
            $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
            //$(this).children().toggleClass(" ");
            $(this).next().slideToggle({ duration: 200 });
            $(this).toggleClass('panel-collapsed');
            savecollapse($(this).attr("display-type"), 'panel-heading.filter-panel');

            return false;
        });
    });

    $("#btnremovecatalog").click(function() {
        $("#catalogfiletext").val("");
        $("#isvalidcatalogfile").val("0");
        $('#catalogfile_div').removeClass("has-error is-focused");
        $('#catalogfile').val("");
    });
    $(".add_variantprice").hide();
    $(".add_variantprice:last").show();
});
$("[data-provide='prodcutid']").each(function() {
    var $element = $(this);

    $element.select2({
        allowClear: true,
        minimumInputLength: 3,
        width: '100%',
        multiple: true,
        placeholder: $element.attr("placeholder"),
        ajax: {
            url: $element.data("url"),
            dataType: 'json',
            type: "POST",
            quietMillis: 50,
            data: function(term) {
                return {
                    term: term,
                };
            },
            results: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            text: item.text,
                            id: item.id
                        }
                    })
                };
            }
        },
        initSelection: function(element, callback) {
            var id = $(element).val();
            if (id !== "" && id != 0) {
                $.ajax($element.data("url"), {
                    data: {
                        ids: id,
                    },
                    type: "POST",
                    dataType: "json",
                }).done(function(data) {
                    callback(data);
                });
            }
        }
    });
});
$("[data-provide='tagid']").each(function() {
    var $element = $(this);

    $element.select2({
        allowClear: true,
        minimumInputLength: 3,
        width: '100%',
        multiple: true,
        placeholder: $element.attr("placeholder"),
        tokenSeparators: [','],
        createSearchChoice: function(term, data) {

            if ($(data).filter(function() {
                    return this.text.localeCompare(term) === 0;
                }).length === 0) {
                if (term.match(/^[a-zA-Z0-9() ]/g))
                    return { id: term, text: term };
            }
        },
        ajax: {
            url: $element.data("url"),
            dataType: 'json',
            type: "POST",
            quietMillis: 50,
            data: function(term) {
                return {
                    term: term,
                };
            },
            results: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            text: item.text,
                            id: item.id
                        }
                    })
                };
            }
        },
        initSelection: function(element, callback) {
            var id = $(element).val();
            if (id !== "" && id != 0) {
                $.ajax($element.data("url"), {
                    data: {
                        ids: id,
                    },
                    type: "POST",
                    dataType: "json",
                }).done(function(data) {
                    callback(data);
                });
            }
        }
    });
});
$('#count_message').html(text_max + ' remaining');
$('#boxincludescount_message').html(text_max + ' remaining');

$('#keyfeatures').keyup(function() {
    countlength();
});
$('#boxincludes').keyup(function() {
    boxincludecountlength();
});
$("#productslug").keyup(function(e) {
    $("#productslug").val(($("#productslug").val()).toLowerCase());
});

$('#isuniversal').change(function() {
    if ($(this).is(":checked")) {
        $('#universalprice').removeAttr("disabled", "disabled");
        $("#price1_div").removeClass("has-error is-focused");
    } else {
        $('#universalprice').attr("disabled", "disabled");
    }
    $('#universalprice').val('');
});
$('#singlelinkcheck').change(function() {
    $("#singlelink_div").removeClass("has-error is-focused");
    if ($(this).is(":checked")) {
        $('#singlelink').removeAttr("disabled", "disabled");
    } else {
        $('#singlelink').attr("disabled", "disabled");
    }
    $('#singlelink').val('');
});
$("#relatedproductid").on("change", function(e) {
    if (e && e.removed) {
        $('#removerelatedproductid').val($('#removerelatedproductid').val() + ',' + e.removed.id);
    }
});
$("#combinationproductid").on("change", function(e) {
    if (e && e.removed) {
        $('#removecombinationproductid').val($('#removecombinationproductid').val() + ',' + e.removed.id);
    }
});
$("[data-provide='categoryid']").each(function() {
    var $element = $(this);

    $element.select2({
        allowClear: true,
        minimumInputLength: 3,
        width: '100%',
        multiple: true,
        placeholder: $element.attr("placeholder"),
        tags: true,
        tokenSeparators: [','],

        createSearchChoice: function(term, data) {
            if ($(data).filter(function() {
                    return this.text.localeCompare(term) === 0;
                }).length === 0) {
                if (term.match(/^[a-zA-Z ]+$/g))
                    return { id: term, text: term };
            }
        },
        ajax: {
            url: $element.data("url"),
            dataType: 'json',
            type: "POST",
            quietMillis: 50,
            data: function(term) {
                return {
                    term: term,
                };
            },
            results: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            text: item.text,
                            id: item.id
                        }
                    })
                };
            }
        },
        initSelection: function(element, callback) {
            var id = $(element).val();
            if (id !== "" && id != 0) {
                $.ajax(element.data("url"), {
                    data: {
                        ids: id,
                    },
                    type: "POST",
                    dataType: "json",
                }).done(function(data) {
                    callback(data);
                });
            }
        }
    });
});
$("#metakeyword").each(function() {
    var $element = $(this);

    $maximumselectionsize = 25;
    if ($element.data("selectionlength") != undefined) {
        $maximumselectionsize = $element.data("selectionlength");
    }
    $element.select2({

        language: {

            inputTooLong: function(args) {
                // args.maximum is the maximum allowed length
                // args.input is the user-typed text
                return "You typed too much";
            },
            noResults: function() {
                return "No results found";
            },
            searching: function() {
                return "Searching...";
            },
            maximumSelected: function(args) {
                // args.maximum is the maximum number of items the user may select
                return "You can enter only 25 keywords";
            }
        },
        allowClear: true,
        minimumInputLength: 3,
        placeholder: $element.attr("placeholder"),
        tokenSeparators: [','],
        multiple: true,
        width: '100%',
        maximumSelectionSize: $maximumselectionsize,
        createSearchChoice: function(term, data) {
            if ($(data).filter(function() {
                    return this.text.localeCompare(term) === 0;
                }).length === 0) {
                return {
                    id: term,
                    text: term
                };
            }
        },
        data: [],
        tags: true,
        initSelection: function(element, callback) {
            var id = $(element).val();
            if (id !== "") {
                data = [];
                var result = id.split(',');
                for (var prop in result) {

                    keyword = {};
                    keyword['id'] = result[prop]
                    keyword['text'] = result[prop];
                    data.push(keyword);
                }
                callback(data);
            }
        }

    });
});
$("#prices").each(function() {
    var $element = $(this);

    $maximumselectionsize = 25;
    if ($element.data("selectionlength") != undefined) {
        $maximumselectionsize = $element.data("selectionlength");
    }
    $element.select2({

        language: {

            inputTooLong: function(args) {
                // args.maximum is the maximum allowed length
                // args.input is the user-typed text
                return "You typed too much";
            },
            noResults: function() {
                return "No results found";
            },
            searching: function() {
                return "Searching...";
            },
            maximumSelected: function(args) {
                // args.maximum is the maximum number of items the user may select
                return "You can enter only 25 price";
            }
        },
        allowClear: true,
        minimumInputLength: 1,
        placeholder: $element.attr("placeholder"),
        tokenSeparators: [','],
        multiple: true,
        width: '100%',
        maximumSelectionSize: $maximumselectionsize,
        createSearchChoice: function(term, data) {
            if ($(data).filter(function() {
                    return this.text.localeCompare(term) === 0;
                }).length === 0) {
                return {
                    id: term,
                    text: term
                };
            }
        },
        data: [],
        tags: true,
        initSelection: function(element, callback) {
            var id = $(element).val();
            if (id !== "") {
                data = [];
                var result = id.split(',');
                for (var prop in result) {

                    prices = {};
                    prices['id'] = result[prop]
                    prices['text'] = result[prop];
                    data.push(prices);
                }
                callback(data);
            }
        }

    });
});
$("#barcode").blur(function(e) {
    if (this.value != '') {
        var barcode = $('#barcode').val();
        if (ACTION == 1 && barcode == $('#oldbarcode').val()) {
            $('#barcodeimg').attr('src', SITE_URL + 'product/set_barcode/' + barcode);
        } else {
            verifyBarcode();
        }
    } else {
        new PNotify({ title: 'Please enter or generate barcode !', styling: 'fontawesome', delay: '3000', type: 'error' });
        setTimeout(() => {
            $("#barcode_div").addClass("has-error is-focused");
            $("#barcodeimg").attr("src", '');
        }, 100);
    }
});

function verifyBarcode() {

    var barcode = $('#barcode').val();

    var uurl = SITE_URL + "product/verifyBarcode";
    $.ajax({
        url: uurl,
        type: 'POST',
        data: { barcode: barcode },
        dataType: 'json',
        async: false,
        success: function(response) {
            if (response == 1) {
                $('#barcodeimg').attr('src', SITE_URL + 'product/set_barcode/' + barcode);
            } else {
                $('#barcode_div').addClass("has-error is-focused");
                new PNotify({ title: 'Barcode already exist ! Please enter unique barcode.', styling: 'fontawesome', delay: '3000', type: 'error' });
            }
        },
        error: function(xhr) {
            //alert(xhr.responseText);
        },
    });
}

function generateBarcode() {

    $("#barcode_div").removeClass("has-error is-focused");
    var uurl = SITE_URL + "product/generateBarcode";
    $.ajax({
        url: uurl,
        type: 'POST',
        dataType: 'json',
        async: false,
        success: function(response) {

            $('#barcode').val(response);
            $('#barcodeimg').attr('src', SITE_URL + 'product/set_barcode/' + response);
        },
        error: function(xhr) {
            //alert(xhr.responseText);
        },
    });
}

function countlength() {
    var text_length = $('#keyfeatures').val().length;
    var text_remaining = text_max - text_length;

    $('#count_message').html(text_remaining + ' remaining');
}

function include(filename, onload) {
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.src = filename;
    script.type = 'text/javascript';
    script.onload = script.onreadystatechange = function() {
        if (script.readyState) {
            if (script.readyState === 'complete' || script.readyState === 'loaded') {
                script.onreadystatechange = null;
                onload();
            }
        } else {
            onload();
        }
    };
    head.appendChild(script);
}

function capture(video, scalefac) {

    //var w = video.videoWidth * scalefac;
    //var h = video.videoHeight * scalefac;
    var w = PRODUCT_IMG_WIDTH;
    var h = PRODUCT_IMG_HEIGHT;
    var canvas = document.createElement('canvas');
    canvas.width = w;
    canvas.height = h;
    var ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, w, h);
    return canvas;
}

function filetype(id, type) {
    $("#productfile" + id).val("");
    $("#imagepreview" + id).attr('src', DEFAULT_IMAGE_PREVIEW);
    $("#Filetext" + id).val("");
    $("#youtubeurl" + id).val("");
    if (type == 1 || type == 2) {
        $('#fileupload' + id).show();
        $('#youtube' + id).hide();
    } else if (type == 3) {
        $('#fileupload' + id).hide();
        $('#youtube' + id).show();
    }
}

function getThumbImage(Link, imagepreview) {

    if (Link.match(/youtube\.com/)) {

        var youtube_video_id = Link.match("[\?&]v=([^&#]*)").pop();
        if (youtube_video_id.length == 11) {
            $('#' + imagepreview).attr('src', '//img.youtube.com/vi/' + youtube_video_id + '/mqdefault.jpg');
        } else {
            new PNotify({ title: 'Please enter valid video link for youtube !', styling: 'fontawesome', delay: '3000', type: 'error' });
            $('#' + imagepreview).attr('src', DEFAULT_IMAGE_PREVIEW);
        }

    }
}

function validfile(obj, thisobj) {
    var val = obj.val();
    var id = obj.attr('id').match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');
    var filetype = $('input[name=filetype' + id + ']').val();
    $("#videothumb" + id).val('');

    switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
        case 'jpeg':
        case 'jpg':
        case 'png':
        case 'avi':
        case 'mov':
        case 'flv':
        case 'mp4':
        case 'wmv':
            var imagearr = ['jpeg', 'jpg', 'png'];
            var videoarr = ['avi', 'flv', 'mp4', 'wmv'];
            if (filetype == 1) {

                if ($.inArray(val.substring(val.lastIndexOf('.') + 1).toLowerCase(), imagearr) == -1) {
                    $("#productfile" + id).val("");
                    $("#Filetext" + id).val("");
                    isvalidfiletext = 0;
                    $("#productfile" + id + "_div").addClass("has-error is-focused");
                    new PNotify({ title: 'Please upload valid image file !', styling: 'fontawesome', delay: '3000', type: 'error' });
                } else {
                    $("#Filetext" + id).val(filename);
                    readURL(thisobj, "imagepreview" + id);
                    isvalidfiletext = 1;
                    $("#productfile" + id + "_div").removeClass("has-error is-focused");
                }
            }

            if (filetype == 2) {

                if ($.inArray(val.substring(val.lastIndexOf('.') + 1).toLowerCase(), videoarr) == -1) {
                    $("#productfile" + id).val("");
                    $("#Filetext" + id).val("");
                    isvalidfiletext = 0;
                    $("#productfile" + id + "_div").addClass("has-error is-focused");
                    new PNotify({ title: 'Please upload valid video file !', styling: 'fontawesome', delay: '3000', type: 'error' });

                } else {
                    $('.mask').show();
                    $('#loader').show();

                    var lasource = $('#video_src' + id);
                    var videodurationinseconds = 0;
                    lasource[0].src = URL.createObjectURL($('#productfile' + id).prop("files")[0]);
                    lasource.parent()[0].load();
                    var video = document.getElementById("videoelem" + id);
                    setTimeout(function() {
                        // Video needs to load then we check the state.
                        // Il faut que la vidéo charge puis nous vérifier l'état.

                        if (video.readyState == "4") {
                            var videoduration = $("#videoelem" + id).get(0).duration;
                            var timetogoto = videodurationinseconds / 2;
                            $("#videoelem" + id).get(0).currentTime = timetogoto;
                            setTimeout(function() {
                                // Video needs to load again
                                // Il faut que la vidéo charge de nouveau
                                var video = document.getElementById("videoelem" + id);
                                // function the screen grab.
                                // fonctionne la capture d'écan.
                                var canvas = capture(video, scalefac);
                                //screenshots.unshift(canvas);

                                $("#videothumb" + id).val(canvas.toDataURL());
                                $("#imagepreview" + id).attr('src', canvas.toDataURL());

                            }, 500);
                        }
                        $('.mask').hide();
                        $('#loader').hide();
                    }, 3000);

                    $("#Filetext" + id).val(filename);
                    isvalidfiletext = 1;
                    $("#productfile" + id + "_div").removeClass("has-error is-focused");
                }
            }

            break;
        default:
            $("#productfile" + id).val("");
            $("#Filetext" + id).val("");
            isvalidfiletext = 0;
            $("#productfile" + id + "_div").addClass("has-error is-focused");
            new PNotify({ title: 'Please upload valid image!', styling: 'fontawesome', delay: '3000', type: 'error' });
            break;
    }
}

function validimageorpdffile(obj, element) {
    var val = obj.val();
    var id = obj.attr('id').match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');


    switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
        case 'pdf':
        case 'gif':
        case 'bmp':
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'docx':
            $("#" + element + "text").val(filename);
            $("#" + element + '_div').removeClass("has-error is-focused");
            $("#isvalid" + element).val('1');
            break;
        default:
            $("#" + element).val("");
            $("#" + element + "text").val("");
            $("#isvalid" + element).val('0');
            $("#" + element + '_div').addClass("has-error is-focused");
            new PNotify({ title: 'Please upload valid ' + element + ' file !', styling: 'fontawesome', delay: '3000', type: 'error' });
            break;
    }
}

function setslug(name) {
    $('#productslug').val(name.toLowerCase().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-'));
}

function addnewvariantprice() {

    var RowID = parseInt($(".countmultipleprice:last").attr("id").match(/\d+/)) + 1;

    var HTML = '<div id="countmultipleprice' + RowID + '" class="col-md-6 countmultipleprice">\
					<div class="col-md-4">\
						<div class="form-group" for="variantprice' + RowID + '" id="variantprice_div' + RowID + '">\
              <div class="col-md-12 pr-xs pl-xs">\
                <input type="text" id="variantprice' + RowID + '" onkeypress="return decimal(event,this.value)" class="form-control variantprices" name="variantprice[]" value="">\
              </div>\
						</div>\
					</div>\
					<div class="col-md-3">\
						<div class="form-group" for="variantqty' + RowID + '" id="variantqty_div' + RowID + '">\
              <div class="col-md-12 pr-xs pl-xs">\
                <input type="text" id="variantqty' + RowID + '" onkeypress="return isNumber(event)" class="form-control variantqty" name="variantqty[]" value="" maxlength="4">\
              </div>\
						</div>\
					</div>\
					<div class="col-md-2">\
						<div class="form-group text-right" for="variantdiscpercent' + RowID + '" id="variantdiscpercent_div' + RowID + '">\
              <div class="col-md-12 pr-xs pl-xs">\
						    <input type="text" id="variantdiscpercent' + RowID + '" onkeypress="return decimal(event,this.value)" class="form-control text-right variantdiscpercent" name="variantdiscpercent[]" value="" onkeyup="return onlypercentage(this.id)">\
              </div>\
						</div>\
					</div>\
					<div class="col-md-3">\
            <div class="form-group pt-sm">\
              <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice" onclick="removevariantprice(' + RowID + ')" style=""><i class="fa fa-minus"></i></button>\
              <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice" onclick="addnewvariantprice()"><i class="fa fa-plus"></i></button>\
            </div>\
          </div>\
				</div>';


    $(".remove_variantprice:first").show();
    $(".add_variantprice:last").hide();
    $("#countmultipleprice" + (RowID - 1)).after(HTML);

    if ($(".countmultipleprice").length > 1) {
        $("#headingmultipleprice2").show();
    }
}

function removevariantprice(RowID) {

    $("#countmultipleprice" + RowID).remove();

    $(".add_variantprice:last").show();
    if ($(".remove_variantprice:visible").length == 1) {
        $(".remove_variantprice:first").hide();
    }

    if ($(".countmultipleprice").length > 1) {
        $("#headingmultipleprice2").show();
    } else {
        $("#headingmultipleprice2").hide();
    }
}

function resetdata() {

    $("#price_div").hide();
    $("#prices_div").show();
    $("#productname_div").removeClass("has-error is-focused");
    $("#productslug_div").removeClass("has-error is-focused");
    $("#shortdescriptiontitle_div").removeClass("has-error is-focused");
    $("#shortdescription_div").removeClass("has-error is-focused");
    $("#prices_div").removeClass("has-error is-focused");
    $("#price_div").removeClass("has-error is-focused");
    $("#discount_div").removeClass("has-error is-focused");
    $("#stock_div").removeClass("has-error is-focused");
    $("#categoryid_div").removeClass("has-error is-focused");
    $("#hsncode_div").removeClass("has-error is-focused");
    $('#s2id_categoryid > ul').css({ "background-color": "#fcfcfc", "border": "1px solid #e3e3e3" });
    $("#description_div").removeClass("has-error is-focused");
    $("#universalprice_div").removeClass("has-error is-focused");
    $("#metatitle_div").removeClass("has-error is-focused");
    $('#s2id_metakeyword > ul').css({ "background-color": "#fcfcfc", "border": "1px solid #e3e3e3" });
    $("#metakeyword_div").removeClass("has-error is-focused");
    $("#metadescription_div").removeClass("has-error is-focused");
    $("#priority_div").removeClass("has-error is-focused");
    $('.cke_inner').css({ "border": "none" });
    $('#s2id_prices > ul').css({ "background-color": "#FFF", "border": "1px solid #cccccc" });
    $('#catalogfile_div').removeClass("has-error is-focused");
    $('#s2id_tagid > ul').css({ "background-color": "#fcfcfc", "border": "1px solid #e3e3e3" });
    $("#barcode_div").removeClass("has-error is-focused");
    $("#unit_div").removeClass("has-error is-focused");

    if (ACTION == 1) {

        if ($('#checkuniversal').is(':checked') && $('#oldbarcode').val() != '') {
            $('#barcode').val($('#oldbarcode').val());
            $('#barcodeimg').attr('src', SITE_URL + 'product/set_barcode/' + $('#oldbarcode').val());
            $("#barcode_div,#barcodeimage_div,#sku_div,#orderquantitydiv,#minimumstocklimit_div,#weight_div,#addpriceinpricelist_div,#minimumsalesprice_div").show();
        } else {
            $("#barcode_div,#barcodeimage_div,#sku_div,#orderquantitydiv,#minimumstocklimit_div,#weight_div,#addpriceinpricelist_div,#minimumsalesprice_div").hide();
            $('#weight').val(0);
        }
    } else {
        $('#productname').val('');
        $('#importerproductname').val('');
        $('#supplierproductname').val('');
        $('#installationcost').val(0);
        $('#productslug').val('');
        $('#priority').val('');
        $('#shortdescription').val('');
        $('#description_div .cke_inner').css({ "background-color": "#FFF", "border": "1px solid #D2D2D2" });
        CKEDITOR.instances['description'].setData("");
        // $("#categoryid").select2("val", "");
        $("#tagid").select2("val", "");
        $('#keyfeatures').val('');
        $('#boxincludes').val('');
        $('#universalprice').val('');
        $("#metakeyword").select2("val", "");
        $("#prices").select2("val", "");
        $('#universalprice').removeAttr("disabled", "disabled");
        $("#price1_div").removeClass("has-error is-focused");
        $("#categoryid,#unitid").val(0);
        $("#priority").val('');
        $("#relatedproductid").select2("val", "");
        $("#combinationproductid").select2("val", "");
        $('#regularproduct').prop("checked", true);
        $('#catalogfile').val("");
        $('#catalogfiletext').val("");

    }
    $('#categoryid').selectpicker('refresh');
    $('html, body').animate({ scrollTop: 0 }, 'slow');

}

function checkvalidation() {

    var productname = $("#productname").val().trim();
    var importerproductname = $("#importerproductname").val().trim();
    var supplierproductname = $("#supplierproductname").val().trim();
    var installationcost = $("#installationcost").val().trim();
    var productslug = $("#productslug").val().trim();
    var shortdescription = $("#shortdescription").val().trim();
    var description = CKEDITOR.instances['description'].getData();
    description = encodeURIComponent(description);
    CKEDITOR.instances['description'].updateElement();
    var categoryid = $("#categoryid").val();
    var priority = $("#priority").val().trim();
    var price = $("#price").val().trim();
    var stock = $("#stock").val().trim();
    var hsncodeid = $("#hsncodeid").val().trim();
    var prices = $("#prices").val().trim();
    var catalogfile = $('#catalogfile').val();
    var isvalidcatalog = $('#isvalidcatalogfile').val();
    var unitid = $("#unitid").val();

    var checkuniversal = $('#checkuniversal').is(':checked') ? 1 : 0;

    // var barcode = $("#barcode").val().trim();
    var sku = $("#sku").val().trim();
    var isvalidproductname = isvalidproductslug = isvalidshortdescription = isvaliddescription = isvalidcategoryid = 0;

    var isvalidpriority = isvalidstock = isvalidprice = isvalidcheckuniversal = isvalidfiletext = isvalidprices = isvalidcatalogfile = isvalidsku = isvalidmultipleprice = isvalidmultiplepriceqty = isvalidhsncodeid = isvalidunitid = 1;
    // = isvalidbarcode 
    PNotify.removeAll();


    if (categoryid == 0 || categoryid == null) {
        $("#categoryid_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please select category !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidcategoryid = 0;
    } else {
        isvalidcategoryid = 1;
    }

    if (productname == '') {
        $("#productname_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter product name !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidproductname = 0;
    } else {
        if (productname.length < 3) {
            $("#productname_div").addClass("has-error is-focused");
            new PNotify({ title: "Product name require minimum 3 characters !", styling: 'fontawesome', delay: '3000', type: 'error' });
            isvalidproductname = 0;
        } else {
            isvalidproductname = 1;
        }
    }

    // if (HSNCODE_IS_COMPULSARY == 1) {
        if (hsncodeid == 0) {
            $("#hsncode_div").addClass("has-error is-focused");
            new PNotify({ title: 'Please select HSN code !', styling: 'fontawesome', delay: '3000', type: 'error' });
            isvalidhsncodeid = 0;
        } else {
            $("#hsncode_div").removeClass("has-error is-focused");
        }
    // }
    if (priority == '') {
        $("#priority_div").addClass("has-error is-focused");
        new PNotify({ title: "Please enter valid priority !", styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidpriority = 0;
    } else {
        $("#priority_div").removeClass("has-error is-focused");
        isvalidpriority = 1;
    }

    if (productslug == '') {
        $("#productslug_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter link !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidproductslug = 0;
    } else {
        if (productslug.length < 3) {
            $("#productslug_div").addClass("has-error is-focused");
            new PNotify({ title: "Product link require minimum 3 characters !", styling: 'fontawesome', delay: '3000', type: 'error' });
            isvalidproductslug = 0;
        } else {
            isvalidproductslug = 1;
        }
    }
   


    // if (PRODUCT_UNIT_IS_OPTIONAL == 0) {
    if (unitid == 0) {
        $("#unit_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please select unit !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidunitid = 0;
    } else {
        $("#unit_div").removeClass("has-error is-focused");
    }
    // }

    if (ACTION == 0) {
        if (checkuniversal == 0) {
            var allprices = prices.split(',');
            if (prices.trim() != '') {
                $.each(allprices, function(index, value) {
                        if (isNaN(value)) {
                            new PNotify({ title: "Please enter valid price", styling: 'fontawesome', delay: '3000', type: 'error' });
                            isvalidprices = 0;
                        }
                    })
                    /*$.each(allprices, function( index, value ) {
                      if(isNaN(value)){
                        new PNotify({title: "Please entet valid price",styling: 'fontawesome',delay: '3000',type: 'error'});
                        isvalidprices = 0;
                      }​
                    })*/
                $('#s2id_prices > ul').css({ "background-color": "#FFF", "border": "1px solid #cccccc" });
            } else {
                $('#s2id_prices > ul').css({ "background-color": "#FFECED", "border": "1px solid #FFB9BD" });
                new PNotify({ title: "Enter minimum one product price", styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidprices = 0;
            }
        }
    }

    if (shortdescription == '') {
        $("#shortdescription_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter short description !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidshortdescription = 0;
    } else {
        if (shortdescription.length < 4) {
            $("#shortdescription_div").addClass("has-error is-focused");
            new PNotify({ title: "Short description require minimum 4 characters !", styling: 'fontawesome', delay: '3000', type: 'error' });
            isvalidshortdescription = 0;
        } else {
            isvalidshortdescription = 1;
        }
    }

    if (description.trim() == '') {
        // $("#description_div").addClass("has-error is-focused");
        new PNotify({ title: 'Please enter description !', styling: 'fontawesome', delay: '3000', type: 'error' });
        $('#description_div .cke_inner').css({ "background-color": "#FFECED", "border": "1px solid #e51c23" });
        isvaliddescription = 0;
    } else {
        if (description.length < 3) {
            // $("#description_div").addClass("has-error is-focused");
            $('#description_div .cke_inner').css({ "background-color": "#FFECED", "border": "1px solid #e51c23" });
            new PNotify({ title: "Description require minimum 3 characters !", styling: 'fontawesome', delay: '3000', type: 'error' });
            isvaliddescription = 0;
        } else {
            isvaliddescription = 1;
            $('#description_div .cke_inner').css({ "border": "none" });
        }
    }

    if (checkuniversal == 1) {

        if (stock == '') {
            $("#stock_div").addClass("has-error is-focused");
            new PNotify({ title: 'Please enter stock !', styling: 'fontawesome', delay: '3000', type: 'error' });
            isvalidstock = 0;
        } else {
            isvalidstock = 1;
        }
        
        if (sku == "") {
            $("#sku_div").addClass("has-error is-focused");
            new PNotify({ title: 'Please enter SKU !', styling: 'fontawesome', delay: '3000', type: 'error' });
            isvalidsku = 0;
        } else {
            $("#sku_div").removeClass("has-error is-focused");
        }

      
        // if (barcode == "") {
        //     $("#barcode_div").addClass("has-error is-focused");
        //     new PNotify({ title: 'Please enter or generate barcode !', styling: 'fontawesome', delay: '3000', type: 'error' });
        //     isvalidbarcode = 0;
        // } else {
        //     $("#barcode_div").removeClass("has-error is-focused");
        // }

        if ($("#singleqty").is(":checked")) {
            if (price == "") {
                $("#price_div").addClass("has-error is-focused");
                new PNotify({ title: 'Please enter price !', styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidprice = 0;
            } else {
                isvalidprice = 1;
            }
        } else {
            var firstRowId = parseInt($('.countmultipleprice:first').attr('id').match(/\d+/));
            $('.countmultipleprice').each(function(index) {
                var id = parseInt($(this).attr('id').match(/\d+/));
                var variantprice = $("#variantprice" + id).val();
                var variantqty = $("#variantqty" + id).val();

                if ((variantprice != "" && variantprice != 0) || (variantqty != "" && variantqty != 0) || parseInt(id) == parseInt(firstRowId)) {
                    if (variantprice == 0) {
                        $("#variantprice_div" + id).addClass("has-error is-focused");
                        new PNotify({ title: 'Please enter ' + (index + 1) + ' price !', styling: 'fontawesome', delay: '3000', type: 'error' });
                        isvalidmultipleprice = 0;
                    } else {
                        $("#variantprice_div" + id).removeClass("has-error is-focused");
                    }
                    if (variantqty == 0) {
                        $("#variantqty_div" + id).addClass("has-error is-focused");
                        new PNotify({ title: 'Please enter ' + (index + 1) + ' quantity !', styling: 'fontawesome', delay: '3000', type: 'error' });
                        isvalidmultiplepriceqty = 0;
                    } else {
                        $("#variantqty_div" + id).removeClass("has-error is-focused");
                    }
                } else {
                    $("#variantprice_div" + id).removeClass("has-error is-focused");
                    $("#variantqty_div" + id).removeClass("has-error is-focused");
                }
            });
        }
    }




    var i = 1;
    $('.productfile').each(function() {
        var id = $(this).attr('id').match(/\d+/);

        var filetype = $('input[name=filetype' + id + ']:checked').val();

        if (filetype == 1 || filetype == 2) {
            if ($("#Filetext" + id).val() == '') {
                $("#productfile" + id + "_div").addClass("has-error is-focused");
                new PNotify({ title: 'Please select ' + (i) + ' product file !', styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidfiletext = 0;
            }
        } else if (filetype == 3) {
            if ($("#youtubeurl" + id).val() == '') {
                $("#productfile" + id + "_div").addClass("has-error is-focused");
                new PNotify({ title: 'Please enter ' + (i) + ' youtube product video !', styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidfiletext = 0;
            } else if (!validateYouTubeUrl($("#youtubeurl" + id).val())) {
                $("#productfile" + id + "_div").addClass("has-error is-focused");
                new PNotify({ title: 'Please enter ' + (i) + ' valid youtube product video !', styling: 'fontawesome', delay: '3000', type: 'error' });
                isvalidfiletext = 0;
            }
        }
        i++;
    });

    if (catalogfile != "" && isvalidcatalog == "0") {
        $('#catalogfile_div').addClass("has-error is-focused");
        new PNotify({ title: 'Please upload valid catalog file !', styling: 'fontawesome', delay: '3000', type: 'error' });
        isvalidcatalogfile = 0;
    } else {
        $('#catalogfile_div').removeClass("has-error is-focused");
    }

    if (isvalidproductname == 1 && isvalidproductslug == 1 && isvalidshortdescription == 1 && isvaliddescription == 1 && isvalidcategoryid == 1 && isvalidhsncodeid == 1 && isvalidcheckuniversal == 1 && isvalidprice == 1 && isvalidfiletext == 1 && isvalidpriority == 1 && isvalidprices == 1 && isvalidstock == 1 && isvalidcatalogfile == 1  && isvalidmultipleprice == 1 && isvalidmultiplepriceqty == 1 && isvalidunitid == 1) {

        var formData = new FormData($('#productform')[0]);
        if (ACTION == 0) {
            var uurl = SITE_URL + "product/product-add";
            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function() {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response) {
                    $('#catalogfile_div').removeClass("has-error is-focused");
                    if (response == 1) {
                        new PNotify({ title: "Product successfully added.", styling: 'fontawesome', delay: '3000', type: 'success' });
                        setTimeout(function() { window.location = SITE_URL + "product"; }, 1500);
                    } else if (response == 2) {
                        new PNotify({ title: 'Product image not uploaded !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (response == 3) {
                        new PNotify({ title: 'Invalid type of product image !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (response == 4) {
                        new PNotify({ title: 'Product name or link already exist !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (response == 5) {
                        new PNotify({ title: 'Product catalog file is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE_CATALOG) + ') !', styling: 'fontawesome', delay: '3000', type: 'error' });
                        $('#catalogfile_div').addClass("has-error is-focused");
                    } else if (response == 6) {
                        new PNotify({ title: 'Product catalog file type does not valid !', styling: 'fontawesome', delay: '3000', type: 'error' });
                        $('#catalogfile_div').addClass("has-error is-focused");
                    } else if (response == 7) {
                        new PNotify({ title: 'Product catalog file not uploaded !', styling: 'fontawesome', delay: '3000', type: 'error' });
                        $('#catalogfile_div').addClass("has-error is-focused");
                    } else if (response == 8) {
                        new PNotify({ title: 'Product SKU already exists. !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: "Product not added !", styling: 'fontawesome', delay: '3000', type: 'error' });
                    }
                },
                error: function(xhr) {
                    //alert(xhr.responseText);
                },
                complete: function() {
                    $('.mask').hide();
                    $('#loader').hide();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        } else {
            var uurl = SITE_URL + "product/update-product";
            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function() {
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response) {
                    $('#catalogfile_div').removeClass("has-error is-focused");
                    if (response == 1) {
                        new PNotify({ title: "Product successfully updated.", styling: 'fontawesome', delay: '3000', type: 'success' });
                        setTimeout(function() { window.location = SITE_URL + "product"; }, 1500);
                    } else if (response == 2) {
                        new PNotify({ title: 'Product image not uploaded !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (response == 3) {
                        new PNotify({ title: 'Invalid type of product image !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (response == 4) {
                        new PNotify({ title: 'Product name or link already exist !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else if (response == 5) {
                        new PNotify({ title: 'Product catalog file is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE_CATALOG) + ') !', styling: 'fontawesome', delay: '3000', type: 'error' });
                        $('#catalogfile_div').addClass("has-error is-focused");
                    } else if (response == 6) {
                        new PNotify({ title: 'Product catalog file type does not valid !', styling: 'fontawesome', delay: '3000', type: 'error' });
                        $('#catalogfile_div').addClass("has-error is-focused");
                    } else if (response == 7) {
                        new PNotify({ title: 'Product catalog file not uploaded !', styling: 'fontawesome', delay: '3000', type: 'error' });
                        $('#catalogfile_div').addClass("has-error is-focused");
                    } else if (response == 8) {
                        new PNotify({ title: 'Product SKU already exists. !', styling: 'fontawesome', delay: '3000', type: 'error' });
                    } else {
                        new PNotify({ title: "Product not updated !", styling: 'fontawesome', delay: '3000', type: 'error' });
                    }
                },
                error: function(xhr) {
                    //alert(xhr.responseText);
                },
                complete: function() {
                    $('.mask').hide();
                    $('#loader').hide();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        }

    }


}

function onlypercentage(val) {
    fieldval = $("#" + val).val();
    if (parseInt(fieldval) < 0) $("#" + val).val(0);
    if (parseInt(fieldval) > 100) $("#" + val).val(100);
}