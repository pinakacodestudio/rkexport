$(document).ready(function() {   
 
    getprovince(countryid);
    $("#provinceid").change(function(){
        getcity(this.value);
        getmember();
    });
    $("#cityid").change(function(){
        getmember();
    });
    /****TYPE CHANGE EVENT****/
    $('#type').on('change', function (e) {
        if(this.value == 1){
            $('.memberproducttype').show();

            $('#categoryid')
                .find('option')
                .remove()
                .end()
                .val('')
            ;
            $('#productid')
                .find('option')
                .remove()
                .end()
                .val('')
            ;
            $('#memberid')
                .find('option')
                .remove()
                .end()
                .val('')
            ;
            $('#channelid').val('0');
            $('.selectpicker').selectpicker('refresh');
            $("#type_div").removeClass("has-error is-focused");
            $("#channel_div").removeClass("has-error is-focused");
            $("#member_div").removeClass("has-error is-focused");
            $("#category_div").removeClass("has-error is-focused");
            $("#product_div").removeClass("has-error is-focused");
        }else{
            
            $('#categoryid')
                .find('option')
                .remove()
                .end()
                .append(CategoryHTML)
                .val('')
            ;
            $('#productid')
                .find('option')
                .remove()
                .end()
                .val('')
            ;
            $('.selectpicker').selectpicker('refresh');
            $('.memberproducttype').hide();

            $("#category_div").removeClass("has-error is-focused");
            $("#product_div").removeClass("has-error is-focused");
        }
        $('#pricehistorytablediv').html('');
    });

    /****CHANNEL CHANGE EVENT****/
    $('#channelid').on('change', function (e) {
        getmember();
        $('#categoryid')
                .find('option')
                .remove()
                .end()
                .val('')
        ;
        $('#productid')
            .find('option')
            .remove()
            .end()
            .val('')
        ;
        $('.selectpicker').selectpicker('refresh');
    });

    /****MEMBER CHANGE EVENT****/
    $('#memberid').on('change', function (e) {
        getproductcategory();
    });

    /****CATEGORY CHANGE EVENT****/
    $('#categoryid').on('change', function (e) {
        getproduct();
    });
    
    /****AMOUNT KEYUP EVENT****/
    $('body').on('keyup', '.amount', function(e) {
        var elementid = e.target.id;
        elementid = elementid.split('_');
        var productpriceid = elementid[1];
        var memberid = elementid[2];
        var amount = $(this).val();
        var type = $("#type").val();

        if(type==0){
            var price = $("#price_"+productpriceid+"_"+memberid).val();
            price = (price!='')?price:0;
            if(parseFloat(amount)>parseFloat(price)){
                $(this).val(parseFloat(price).toFixed(2));
                amount = parseFloat(price);
            }
            if(amount!='' && amount > 0){
                var percentage = parseFloat(amount) * 100 / parseFloat(price);
                $("#pricepercentage_"+productpriceid+"_"+memberid).val(parseFloat(percentage).toFixed(2));
            }else{
                $("#pricepercentage_"+productpriceid+"_"+memberid).val('');
            }
        }
        if(memberid==0){
            $('.amount').each(function(){
                var ids = $(this).attr('id');
                ids = ids.split('_');
                var priceid = ids[1];
                var prmemberid = ids[2];
                if(prmemberid!=0 && productpriceid==priceid){
                    var memberprice = $("#price_"+priceid+"_"+prmemberid).val();
                    memberprice = (memberprice!='')?memberprice:0;
                    if(amount!='' && amount > 0){
                        if(parseFloat(amount)>parseFloat(memberprice)){
                            $("#amount_"+priceid+"_"+prmemberid).val(parseFloat(memberprice).toFixed(2));
                            var memberpercentage = parseFloat(memberprice) * 100 / parseFloat(memberprice);
                            $("#pricepercentage_"+priceid+"_"+prmemberid).val(parseFloat(memberpercentage).toFixed(2));
                        }else{
                            var memberpercentage = parseFloat(amount) * 100 / parseFloat(memberprice);
                            $("#pricepercentage_"+priceid+"_"+prmemberid).val(parseFloat(memberpercentage).toFixed(2));
                            $("#amount_"+priceid+"_"+prmemberid).val(parseFloat(amount).toFixed(2));
                        }
                    }else{
                        $("#pricepercentage_"+priceid+"_"+prmemberid).val('');
                        $("#amount_"+priceid+"_"+prmemberid).val('');
                    }
                }
            });
        }else{
            if(type==1){
                var price = $("#price_"+productpriceid+"_"+memberid).val();
                price = (price!='')?price:0;
                if(parseFloat(amount)>parseFloat(price)){
                    $(this).val(parseFloat(price).toFixed(2));
                    amount = parseFloat(price);
                }
                if(amount!='' && amount > 0){
                    var percentage = parseFloat(amount) * 100 / parseFloat(price);
                    $("#pricepercentage_"+productpriceid+"_"+memberid).val(parseFloat(percentage).toFixed(2));
                }else{
                    $("#pricepercentage_"+productpriceid+"_"+memberid).val('');
                }
            }
        }
    });
     /****PERCENTAGE KEYUP EVENT****/
    $('body').on('keyup', '.percentage', function(e) {
        var elementid = e.target.id;
        // elementid = elementid.replace ( /[^\d.]/g, '' );
        elementid = elementid.split('_');
        var productpriceid = elementid[1];
        var memberid = elementid[2];
        var percentage = $(this).val();
        var type = $("#type").val();

        if(this.value > 100){
            $(this).val('100');
            percentage = $(this).val();
        }   
        if(type==0){
            var price = $("#price_"+productpriceid+"_"+memberid).val();
            price = (price!='')?price:0;
            if(percentage==''){
                $("#amount_"+productpriceid+"_"+memberid).val('');
            }else{
                if(price==0){
                    $("#amount_"+productpriceid+"_"+memberid).val("0.00");
                }else{
                    var amount = parseFloat(price) * parseFloat(percentage) / 100;
                    $("#amount_"+productpriceid+"_"+memberid).val(parseFloat(amount).toFixed(2));
                }
            }
        }
        if(memberid==0){
            $('.amount').each(function(){
                var ids = $(this).attr('id');
                ids = ids.split('_');
                var priceid = ids[1];
                var prmemberid = ids[2];
                if(prmemberid!=0 && productpriceid==priceid){
                    var memberprice = $("#price_"+priceid+"_"+prmemberid).val();
                    memberprice = (memberprice!='')?memberprice:0;
                    if(percentage==''){
                        $("#amount_"+priceid+"_"+prmemberid).val('');
                        $("#pricepercentage_"+priceid+"_"+prmemberid).val('');
                    }else{
                        var memberamount = parseFloat(memberprice) * parseFloat(percentage) / 100;
                        $("#amount_"+priceid+"_"+prmemberid).val(parseFloat(memberamount).toFixed(2));
                        $("#pricepercentage_"+priceid+"_"+prmemberid).val(parseFloat(percentage).toFixed(2));
                    }
                }
            });
        }else{
            if(type==1){
                var price = $("#price_"+productpriceid+"_"+memberid).val();
                price = (price!='')?price:0;
                if(percentage==''){
                    $("#amount_"+productpriceid+"_"+memberid).val('');
                }else{
                    if(price==0){
                        $("#amount_"+productpriceid+"_"+memberid).val("0.00");
                    }else{
                        var amount = parseFloat(price) * parseFloat(percentage) / 100;
                        $("#amount_"+productpriceid+"_"+memberid).val(parseFloat(amount).toFixed(2));
                    }
                }
            }
        }
              
    });
    
    var date = new Date();
    date.setHours(date.getHours() + 1);

    $('#scheduleddate').datetimepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy HH P',
        viewMode: 'days',
        minView : 1,
        showMeridian: true,
        todayBtn: "linked",
        showClear: true,
        startDate: date,
        autoclose: true,
    });
    $('body').on('click', '.add-on', function(e) {
        $('#scheduleddate').val('');
    });
   
    if(ACTION==1 && usertype==1){
        getmember();
        getproductcategory();
    }

    if(categoryids!='' && ACTION==1){
        getproduct();
        getproductpricehistory();
    }
});


function getmember(){
    $('#memberid')
        .find('option')
        .remove()
        .end()
        .val('')
    ;
    $('#memberid').selectpicker('refresh');
    
    var channelid = $("#channelid").val();
    var provinceid = $("#provinceid").val();
    var cityid = $("#cityid").val();
    
    if(channelid!=0){
      var uurl = SITE_URL+"member/getmembers";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {channelid:String(channelid),provinceid:String(provinceid),cityid:String(cityid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
    
                if(ACTION==1 && memberids!=null || memberids!=''){
                    
                    memberids = memberids.toString().split(',');
                    
                    if(memberids.includes(response[i]['id'])){
                        $('#memberid').append($('<option>', { 
                            value: response[i]['id'],
                            selected: "selected",
                            text : ucwords(response[i]['name'])
                        }));
                    }else{
                        $('#memberid').append($('<option>', { 
                            value: response[i]['id'],
                            text : ucwords(response[i]['name'])
                        }));
                    }
                }else{
                    $('#memberid').append($('<option>', { 
                        value: response[i]['id'],
                        text : ucwords(response[i]['name'])
                    }));
                }
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $('#memberid').selectpicker('refresh');
}

function getproductcategory(){
    $('#categoryid')
        .find('option')
        .remove()
        .end()
        .val('')
    ;
    $('#categoryid').selectpicker('refresh');
    
    var memberid = $("#memberid").val();
    
    if(memberid!=''){
      var uurl = SITE_URL+"category/getMultipleMemberProductCategory";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {memberid:String(memberid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
    
                if(ACTION==1 && categoryids!=null || categoryids!=''){
                    
                    categoryids = categoryids.toString().split(',');
                    
                    if(categoryids.includes(response[i]['id'])){
                        $('#categoryid').append($('<option>', { 
                            value: response[i]['id'],
                            selected: "selected",
                            text : ucwords(response[i]['name'])
                        }));
                    }else{
                        $('#categoryid').append($('<option>', { 
                            value: response[i]['id'],
                            text : ucwords(response[i]['name'])
                        }));
                    }
                }else{
                    $('#categoryid').append($('<option>', { 
                        value: response[i]['id'],
                        text : ucwords(response[i]['name'])
                    }));
                }
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $('#categoryid').selectpicker('refresh');
}

function getproduct(){
    $('#productid')
        .find('option')
        .remove()
        .end()
        .val('')
    ;
    $('#productid').selectpicker('refresh');
    
    var memberid = ($("#type").val()==0)?'':$("#memberid").val();
    var channelid = ($("#type").val()==0)?'':$("#channelid").val();
    var categoryid = $("#categoryid").val();
    
    if(categoryid!=''){
      var uurl = SITE_URL+"product/getProductsByMultipleCategoryIds";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {categoryid:String(categoryid),channelid:String(channelid),memberid:String(memberid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
  
                var productname = response[i]['name'].replace("'","&apos;");
                
                if(ACTION==1 && productids!=null || productids!=''){
                    
                    productids = productids.toString().split(',');
                    
                    if(productids.includes(response[i]['id'])){
                        if(DROPDOWN_PRODUCT_LIST==0){
                            $('#productid').append($('<option>', { 
                                value: response[i]['id'],
                                selected: "selected",
                                text :  productname
                            }));
                        }else{
                            $('#productid').append($('<option>', { 
                                value: response[i]['id'],
                                selected: "selected",
                                "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
                            }));
                        }
                    }else{
                        if(DROPDOWN_PRODUCT_LIST==0){
                            $('#productid').append($('<option>', { 
                                value: response[i]['id'],
                                text :  productname
                            }));
                        }else{
                            $('#productid').append($('<option>', { 
                                value: response[i]['id'],
                                "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
                            }));
                        }
                    }
                }else{
                    if(DROPDOWN_PRODUCT_LIST==1){
                        $('#productid').append($('<option>', { 
                            value: response[i]['id'],
                            text :  productname
                        }));
                    }else{
                        $('#productid').append($('<option>', { 
                            value: response[i]['id'],
                            "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
                        }));
                    }
                }
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $('#productid').selectpicker('refresh');
}

function getproductpricehistory(){
    
    var type = $('#type').val();
    var channelid = $('#channelid').val();
    var memberid = $('#memberid').val();
    var categoryid = $('#categoryid').val();
    var productid = $('#productid').val();
    var pricehistoryid = $('#pricehistoryid').val();

    var isvalidtype = isvalidchannelid = isvalidmemberid = isvalidcategoryid = isvalidproductid = 1 ;
    
    if(type == ''){
        $("#type_div").addClass("has-error is-focused");
        new PNotify({title: "Please select type !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidtype = 0;
    }else{
        $("#type_div").removeClass("has-error is-focused");
    }
    if(type==1){
        if(channelid == 0){
            $("#channel_div").addClass("has-error is-focused");
            new PNotify({title: "Please select channel !",styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidchannelid = 0;
        }else{
            $("#channel_div").removeClass("has-error is-focused");
        }
        if(memberid == null){
            $("#member_div").addClass("has-error is-focused");
            new PNotify({title: "Please select "+member_label+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidmemberid = 0;
        }else{
            $("#member_div").removeClass("has-error is-focused");
        }
    }
    if(categoryid == null){
        $("#category_div").addClass("has-error is-focused");
        new PNotify({title: "Please select category !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcategoryid = 0;
    }else{
        $("#category_div").removeClass("has-error is-focused");
    }
    if(productid == null){
        $("#product_div").addClass("has-error is-focused");
        new PNotify({title: "Please select product !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidproductid = 0;
    }else{
        $("#product_div").removeClass("has-error is-focused");
    }

    if(isvalidtype == 1 && isvalidchannelid == 1 && isvalidmemberid == 1 && isvalidcategoryid == 1 && isvalidproductid == 1){
        var tablehtml = "";
        if(categoryid!=null){
            var uurl = SITE_URL+"price-history/getpricehistorydata";
            $.ajax({
                url: uurl,
                type: 'POST',
                data: {channelid:channelid,memberid:memberid,categoryid:categoryid,productid:productid,type:type,pricehistoryid:pricehistoryid,displaytype:displaytype},
                //async: false,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){

                    var obj = JSON.parse(response);
                    if(obj['html']!=''){
                        tablehtml += obj['html'];
                    }
                    $('#pricehistorytablediv').html(tablehtml);

                    $('.yesnoprice input[type="checkbox"]').bootstrapToggle({
                        on: '<i class="fa fa-plus"></i> <i class="fa fa-plus"></i>',
                        off: '<i class="fa fa-minus"></i> <i class="fa fa-minus"></i>',
                        onstyle: 'success',
                        offstyle: 'danger',
                        size: 'mini'
                    
                    });
                    $('.amntpercent input[type="checkbox"]').bootstrapToggle({
                        on: '<i class="fa fa-inr fa-lg"></i>',
                        off: '%',
                        onstyle: 'success',
                        offstyle: 'info',
                        size: 'mini'
                    });

                    $('.yesnoprice .toggle-group .btn,.amntpercent .toggle-group .btn').addClass('btn-raised');
                    $('.yesnoprice .toggle-group .toggle-handle,.amntpercent .toggle-group .toggle-handle').css({'border-radius':'5px','background-color': '#e0e0e0'});
                
                    $('.yesnoprice input[type="checkbox"]').change(function() {
                        var id = $(this).attr('id');
                        id = id.split("_");
                        var elmentname = id[0];
                        var priceid = id[1];
                        var memberid = id[2];
                        
                        if(memberid==0){
                            $('.yesnoprice input[type="checkbox"]').each(function() {
                                var elementid = $(this).attr('id');
                                elementid = elementid.split("_");
                                var mpriceid = elementid[1];
                                var mmemberid = elementid[2];

                                if(mmemberid!=0 && priceid==mpriceid){
                                    if(elmentname.trim()=="changeincrementdecrementprice"){
                                        if ($("#changeincrementdecrementprice_"+priceid+"_"+memberid).is(':checked')) 
                                        {
                                            $("#incrementdecrementprice_"+mpriceid+"_"+mmemberid).bootstrapToggle('on');
                                        } else 
                                        {
                                            $("#incrementdecrementprice_"+mpriceid+"_"+mmemberid).bootstrapToggle('off');
                                        }
                                    }else{
                                        if ($("#incrementdecrementprice_"+priceid+"_"+memberid).is(':checked')) 
                                        {
                                            $("#incrementdecrementprice_"+mpriceid+"_"+mmemberid).bootstrapToggle('on');
                                        } else 
                                        {
                                            $("#incrementdecrementprice_"+mpriceid+"_"+mmemberid).bootstrapToggle('off');
                                        }
                                    }
                                }
                            });
                        }
                    });
                    $('.amntpercent input[type="checkbox"]').change(function() {
                        var id = $(this).attr('id');
                        id = id.split("_");
                        var elmentname = id[0];
                        var priceid = id[1];
                        var memberid = id[2];

                        if(memberid==0){
                            $('.amntpercent input[type="checkbox"]').each(function() {
                                var elementid = $(this).attr('id');
                                elementid = elementid.split("_");
                                var mpriceid = elementid[1];
                                var mmemberid = elementid[2];

                                if(mmemberid!=0 && priceid==mpriceid){
                                    if(elmentname.trim()=="changepricetype"){
                                        if ($("#changepricetype_"+priceid+"_"+memberid).is(':checked')) 
                                        {
                                            $("#pricetype_"+mpriceid+"_"+mmemberid).bootstrapToggle('on');
                                        } else 
                                        {
                                            $("#pricetype_"+mpriceid+"_"+mmemberid).bootstrapToggle('off');
                                        }
                                    }else{
                                        if ($("#pricetype_"+priceid+"_"+memberid).is(':checked')) 
                                        {
                                            $("#pricetype_"+mpriceid+"_"+mmemberid).bootstrapToggle('on');
                                        } else 
                                        {
                                            $("#pricetype_"+mpriceid+"_"+mmemberid).bootstrapToggle('off');
                                        }
                                    }
                                }
                            });
                        }
                    });
                },
                error: function(xhr) {
                //alert(xhr.responseText);
                },
                complete: function(){
                    $('.mask').hide();
                    $('#loader').hide();
                    loadpopover();
                },
            });
        
        }else{
            $('#pricehistorytablediv').html('');
        }
    }
}

function resetdata(){

    $("#type_div").removeClass("has-error is-focused");
    $("#channel_div").removeClass("has-error is-focused");
    $("#member_div").removeClass("has-error is-focused");
    $("#category_div").removeClass("has-error is-focused");
    $("#product_div").removeClass("has-error is-focused");
    $("#scheduleddate_div").removeClass("has-error is-focused");
    
    if(ACTION==0){
        $('#categoryid').val('');
        $('#productid').val('');
        $('#type').val('0');
        $('#channelid').val('0');
        $('#scheduleddate').val('');
        $('#remarks').val('');
        $('.memberproducttype').hide();
        
        $('#pricehistorytablediv').html('');
        $('.selectpicker').selectpicker('refresh');
    }
    $('html, body').animate({scrollTop:0},'slow');
}

function checkvalidation(){
    
    var type = $('#type').val();
    var channelid = $('#channelid').val();
    var memberid = $('#memberid').val();
    var categoryid = $('#categoryid').val();
    var productid = $('#productid').val();
    var pricehistoryid = $('#pricehistoryid').val();

    var isvalidtype = isvalidchannelid = isvalidmemberid = isvalidcategoryid = isvalidproductid = 1 ;
    PNotify.removeAll();
    
    if(type == ''){
        $("#type_div").addClass("has-error is-focused");
        new PNotify({title: "Please select type !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidtype = 0;
    }else{
        $("#type_div").removeClass("has-error is-focused");
    }

    if(type==1){
        if(channelid == 0){
            $("#channel_div").addClass("has-error is-focused");
            new PNotify({title: "Please select channel !",styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidchannelid = 0;
        }else{
            $("#channel_div").removeClass("has-error is-focused");
        }
        if(memberid == null){
            $("#member_div").addClass("has-error is-focused");
            new PNotify({title: "Please select "+member_label+" !",styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidmemberid = 0;
        }else{
            $("#member_div").removeClass("has-error is-focused");
        }
    }
    if(categoryid == null){
        $("#category_div").addClass("has-error is-focused");
        new PNotify({title: "Please select category !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcategoryid = 0;
    }else{
        $("#category_div").removeClass("has-error is-focused");
    }
    if(productid == null){
        $("#product_div").addClass("has-error is-focused");
        new PNotify({title: "Please select product !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidproductid = 0;
    }else{
        $("#product_div").removeClass("has-error is-focused");
    }

    if(isvalidtype == 1 && isvalidchannelid == 1 && isvalidmemberid == 1 && isvalidcategoryid == 1 && isvalidproductid == 1){
    
        var formData = new FormData($('#pricehistoryform')[0]);
        
        if(pricehistoryid=='' && ACTION==0){
            var uurl = SITE_URL+"price-history/add-price-history";
            $.ajax({
                url: uurl,
                type: 'POST',
                data:formData,
                //async: false,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    if(response==1){
                        new PNotify({title: "Price history successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                        
                        setTimeout(function() { window.location=SITE_URL+"price-history"; }, 1500);
                    }else{
                        new PNotify({title: "Price history not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                },
                error: function(xhr) {
                //alert(xhr.responseText);
                },
                complete: function(){
                    $('.mask').hide();
                    $('#loader').hide();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        }else{
            var uurl = SITE_URL+"price-history/update-price-history";
            $.ajax({
                url: uurl,
                type: 'POST',
                data:formData,
                //async: false,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    if(response==1){
                        new PNotify({title: "Price history successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                        
                        setTimeout(function() { window.location=SITE_URL+"price-history"; }, 1500);
                    }else{
                        new PNotify({title: "Price history not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    }
                },
                error: function(xhr) {
                //alert(xhr.responseText);
                },
                complete: function(){
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