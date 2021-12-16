$(document).ready(function() {

    $(".add_btn").hide();
    $(".add_btn:last").show();
    if(ACTION==1){
        var channelid = $("#channelid").val();
        getmembers(channelid);
    }
    $("#channelid").change(function(){
        var channelid = $(this).val();
        getmembers(channelid);
    });
    $('#datepicker-range').datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn:"linked"
    });
    $('#cleardatebtn').click(function(){
        $("#startdate").val("");
        $("#enddate").val("");
    });
});
/****PRODUCT CHANGE EVENT****/
$(document).on('change', 'select.productid', function() { 
    var divid = $(this).attr("div-id");
    var productid = $("#productid"+divid).val();
    var uniqueproduct = (productid!="" && productid!=0)?productid+"_0":"";
    $("#uniqueproduct"+divid).val(uniqueproduct);
    
    getproductprice(divid);
});
/****PRODUCT VARIANT CHANGE EVENT****/
$(document).on('change', 'select.priceid', function() { 
    var divid = $(this).attr("div-id");
   
    var uniqueproduct = $("#uniqueproduct"+divid).val();
    if(uniqueproduct!=""){
        var elementarr = uniqueproduct.split("_");
        var priceid = (this.value!="")?this.value:0;
        $("#uniqueproduct"+divid).val(elementarr[0]+"_"+priceid);
    }
});

function getmembers(channelid){
    $('#memberid')
        .find('option')
        .remove()
        .end()
        .append('')
        .val('whatever')
    ;
    $('#memberid').selectpicker('refresh');
  
    if(channelid!=0 && channelid!=null){
      var uurl = SITE_URL+"member/get-multiple-channel-members";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {channelid:channelid},
        dataType: 'json',
        async: false,
        success: function(response){
  
          for(var i = 0; i < response.length; i++) {
            
            if(ACTION==1){
              if(memberidarr!=null || memberidarr!=''){
               
                memberidarr = memberidarr.toString().split(',');
               
                if(memberidarr.includes(response[i]['id'])){
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
      $('#memberid').selectpicker('refresh');
    }
}
function getproductprice(divid){
    
    $('#priceid'+divid)
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select Variant</option>')
        .val('0')
    ;
    $('#priceid'+divid).selectpicker('refresh');
  
    var productid = $("#productid"+divid).val();
    
    if(productid!=0){
      var uurl = SITE_URL+"cashback-offer/getVariantByProductId";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:String(productid)},
        dataType: 'json',
        async: false,
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
                $('#priceid'+divid).append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['variantname']
                }));
                /* if(ACTION==1){
                    if(typeof(response[i]['universal'])!='undefined'){
                        $('#priceid'+divid).append($('<option>', { 
                          value: response[i]['id'],
                          text : response[i]['variantname']
                        }));
                        $('#priceid'+divid).val(response[i]['id']);
                        $('#priceid'+divid).selectpicker("refresh");
                    }else{
                        $('#priceid'+divid).append($('<option>', { 
                            value: response[i]['id'],
                            text : response[i]['memberprice']
                        }));
                    }
                }else{
                    $('#priceid'+divid).append($('<option>', { 
                        value: response[i]['id'],
                        text : response[i]['memberprice']
                    }));
                }   */
            }
            if(response.length == 1){
                $('#priceid'+divid).val(response[0]['id']);
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
      });
    }
    $('#priceid'+divid).selectpicker('refresh');
}
function addnewproduct(){

    var divcount = parseInt($(".countproducts:last").attr("id").match(/\d+/))+1;
    
    producthtml = '<div class="col-sm-12 countproducts" id="countproducts'+divcount+'">\
                    <input type="hidden" name="uniqueproduct[]" id="uniqueproduct'+divcount+'">\
                    <div class="col-sm-4 pl-sm pr-sm">\
                        <div class="form-group" id="product'+divcount+'_div">\
                            <div class="col-sm-12">\
                                <select id="productid'+divcount+'" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="'+divcount+'">\
                                    <option value="0">Select Product</option>\
                                    '+PRODUCT_DATA+'\
                                </select>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="col-sm-3 pl-sm pr-sm">\
                        <div class="form-group" id="price'+divcount+'_div">\
                            <div class="col-md-12">\
                                <select id="priceid'+divcount+'" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="'+divcount+'">\
                                    <option value="">Select Variant</option>\
                                </select>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="col-sm-2 pl-sm pr-sm">\
                        <div class="form-group" id="earnpoints'+divcount+'_div">\
                            <div class="col-md-12">\
                                <input type="text" class="form-control earnpoints" id="earnpoints'+divcount+'" name="earnpoints[]" value="" maxlength="4" onkeypress="return isNumber(event);" style="display: block;" div-id="'+divcount+'">\
                            </div>\
                        </div>\
                    </div>\
                    <div class="col-md-2 form-group m-n p-sm pt-md">\
                        <button type = "button" class = "btn btn-default btn-raised remove_btn" onclick="removeproduct('+divcount+')" style="padding: 5px 10px;"> <i class = "fa fa-minus"></i></button> \
                        <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> \
                    </div>\
                </div>';

    $(".remove_btn:first").show();
    $(".add_btn:last").hide();
    $("#countproducts"+(divcount-1)).after(producthtml);
    
    $(".selectpicker").selectpicker("refresh");
}
function removeproduct(divid){

    $("#countproducts"+divid).remove();

    $(".add_btn:last").show();
    if ($(".remove_btn:visible").length == 1) {
        $(".remove_btn:first").hide();
    }
}
function resetdata(){

    $("#offername_div").removeClass("has-error is-focused");
    $("#channel_div").removeClass("has-error is-focused");
    $("#member_div").removeClass("has-error is-focused");
    $("#shortdescription_div").removeClass("has-error is-focused");
    $("#description_div").removeClass("has-error is-focused");
    $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});

    if(ACTION==0){
        $("#offername").val("");
        $("#startdate").val("");
        $("#enddate").val("");
        $("#shortdescription").val("");
        $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
        CKEDITOR.instances['description'].setData("");
        $("#channelid").val('');
        getmembers(0);
        
        $("#channelid option").removeAttr('disabled');
        $("#memberid").prop('disabled', false);
 
        $(".countproducts:not(:first)").remove();
        var divid = parseInt($(".countproducts:first").attr("id").match(/\d+/));

        $('#productid'+divid+',#priceid'+divid).val("0");
        $('#qty'+divid).val("1");
        getproductprice(divid);

        $('.add_btn:first').show();
        $('.remove_btn').hide();

        $(".selectpicker").selectpicker('refresh');
        $('#yes').prop("checked", true);
    }
    $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){

    var offername = $("#offername").val().trim();
    var channelid = $("#channelid").val();
    var memberid = $("#memberid").val();
    var shortdescription = $("#shortdescription").val();
    var description = CKEDITOR.instances['description'].getData();
    description = encodeURIComponent(description);
    CKEDITOR.instances['description'].updateElement();
    
    var isvalidoffername = isvalidmemberid = isvaliddescription = isvalidshortdescription = isvalidproductid = isvalidpriceid = isvalidearnpoints = isvaliduniqueproducts = 1;
    
    PNotify.removeAll();
    if(offername=='') {
        $("#offername_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter cashback offer name !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidoffername = 0;
    } else {
      if(offername.length < 3){
        $("#offername_div").addClass("has-error is-focused");
        new PNotify({title: 'Cashback offer name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidoffername = 0;
      }else{
        $("#offername_div").removeClass("has-error is-focused");
      }
      
    }
    if(channelid!=null && channelid!=0 && memberid==null) {
        $("#member_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select members !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmemberid = 0;
    } else {
        $("#member_div").removeClass("has-error is-focused");
    }
    if(shortdescription!='' && shortdescription.length < 2) {
        $("#shortdescription_div").addClass("has-error is-focused");
        new PNotify({title: 'Short description require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidshortdescription = 0;
    } else {
        $("#shortdescription_div").removeClass("has-error is-focused");
    }
    if(description!='' && description.length < 3) {
        $("#description_div").addClass("has-error is-focused");
        new PNotify({title: 'Description require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        $('.cke_inner').css({"background-color":"#FFECED","border":"1px solid #e51c23"});
        isvaliddescription = 0;
    } else {
        $("#description_div").removeClass("has-error is-focused");
        $('.cke_inner').css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
    }

    var c=1;
    var firstproductid = $('.countproducts:first').attr('id').match(/\d+/);
    $('.countproducts').each(function(){
        var id = $(this).attr('id').match(/\d+/);
       
        if($("#productid"+id).val() > 0 || $("#priceid"+id).val() > 0 || $("#earnpoints"+id).val() != 0 || parseInt(id)==parseInt(firstproductid)){
            if($("#productid"+id).val() == 0){
                $("#product"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' product !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidproductid = 0;
            }else {
                $("#product"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#priceid"+id).val() == "" || $("#priceid"+id+" option:selected").text() == "Select Variant"){
                $("#price"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(c)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidpriceid = 0;
            }else {
                $("#price"+id+"_div").removeClass("has-error is-focused");
            }
            if($("#earnpoints"+id).val() == 0){
                $("#earnpoints"+id+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter '+(c)+' earn points !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidearnpoints = 0;
            }else {
                $("#earnpoints"+id+"_div").removeClass("has-error is-focused");
            }
        } else{
            $("#product"+id+"_div").removeClass("has-error is-focused");
            $("#price"+id+"_div").removeClass("has-error is-focused");
            $("#earnpoints"+id+"_div").removeClass("has-error is-focused");
        }
        c++;
    });

    var products = $('input[name="uniqueproduct[]"]');
    var values = [];
    for(j=0;j<products.length;j++) {
        var uniqueproducts = products[j];
        var id = uniqueproducts.id.match(/\d+/);
        
        if(uniqueproducts.value!="" && ($("#priceid"+id[0]).val()!="" && $("#priceid"+id[0]+" option:selected").text()!="Select Variant")){
            if(values.indexOf(uniqueproducts.value)>-1) {
                $("#product"+id[0]+"_div,#price"+id[0]+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please select '+(j+1)+' is different product & variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvaliduniqueproducts = 0;
            }
            else{ 
                values.push(uniqueproducts.value);
                if(($("#product"+id[0]+"_div,#priceid"+id[0]).val()!="" && $("#priceid"+id[0]+" option:selected").text()!="Select Variant")){
                    $("#product"+id[0]+"_div,#price"+id[0]+"_div").removeClass("has-error is-focused");
                }
            }
        }
    } 

    if(isvalidoffername == 1 && isvalidmemberid == 1 && isvaliddescription == 1 && isvalidshortdescription == 1 && isvalidproductid == 1 && isvalidpriceid == 1 && isvalidearnpoints == 1 && isvaliduniqueproducts == 1){
                            
        var formData = new FormData($('#cashbackofferform')[0]);
        if(ACTION == 0){    
            var uurl = SITE_URL+"cashback-offer/add-cashback-offer";
            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function(){
                $('.mask').show();
                $('#loader').show();
                },
                success: function(response){
                    var obj = JSON.parse(response);
                    if(obj['error']==1){
                        new PNotify({title: 'Cashback offer successfully added !',styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function() { location.reload(); }, 1500);
                    }else if(obj['error'] == 2){
                        new PNotify({title: 'Cashback offer already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(obj['error'] == 3){
                        new PNotify({title: obj['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Cashback offer not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var uurl = SITE_URL+"cashback-offer/update-cashback-offer";
            $.ajax({
                url: uurl,
                type: 'POST',
                data: formData,
                //async: false,
                beforeSend: function(){
                    $('.mask').show();
                    $('#loader').show();
                },
                success: function(response){
                    var obj = JSON.parse(response);
                    if(obj['error']==1){
                        new PNotify({title: 'Cashback offer successfully Updated !',styling: 'fontawesome',delay: '3000',type: 'success'});
                        setTimeout(function() { window.location=SITE_URL+"cashback-offer"; }, 1500);
                    }else if(obj['error'] == 2){
                        new PNotify({title: 'Cashback offer already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else if(obj['error'] == 3){
                        new PNotify({title: obj['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
                    }else{
                        new PNotify({title: 'Cashback offer not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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