
$(document).ready(function() {
    $("#categoryid").change(function(){
      getproduct();
    });
    $("#productid").change(function(){
        getproductvariant();
    });
    $("#priceid").change(function(){
        getchannelpricelistdata();
    });
    if(ACTION==1){
        getproduct(0);
        getproductvariant(0);
        /* if(PRODUCTID!=0 && PRICEID!=0){
            getchannelpricelistdata();
        } */

        $('.channelid').each(function(){
            var channelid = parseInt($(this).val());
            
            $(".add_variantprice"+channelid).hide();
            $(".add_variantprice"+channelid+":last").show();
        });
    }
});
$(document).on('change','.pricetype',function (e) {
	var channelid = parseInt($(this).attr('id').match(/\d+/));
	if($(this).val()==0){
		$("#salesprice"+channelid+"_div,#discper"+channelid+"_div,#discamnt"+channelid+"_div").parent().show();
		$("#multiplepricesection"+channelid).hide();
	}else{
		$("#salesprice"+channelid+"_div,#discper"+channelid+"_div,#discamnt"+channelid+"_div").parent().hide();
		$("#multiplepricesection"+channelid).show();
	}
});
$(document).on('keyup', '.price', function(e) {
    var channelid = e.target.id.match(/\d+/);
    
    calculatediscount(channelid);
});
$(document).on('keyup', '.discper', function(e) {
    var channelid = e.target.id.match(/\d+/);
    
    if(parseFloat(this.value)>=100){
      $("#discper"+channelid).val("100");
    }
    calculatediscount(channelid);
});
$(document).on('keyup', '.discamnt', function(e) {
    var channelid = e.target.id.match(/\d+/);
    
    calculatediscountmount(channelid,$(this).val());
});
function calculatediscount(channelid){
    var discountpercentage = $("#discper"+channelid).val(); 
    discountpercentage = (discountpercentage!='' && discountpercentage!=0)?discountpercentage:0;
    var price = $("#salesprice"+channelid).val();
    price = (price!='' && price!=0)?price:0;
    
    if(price!=0 && discountpercentage!=0){
        var discountamount = (parseFloat(price)*parseFloat(discountpercentage)/100);
        
        $("#discamnt"+channelid).val(parseFloat(discountamount).toFixed(2));
    }else{
        $("#discamnt"+channelid).val('');
    }
}
function calculatediscountmount(channelid,discountamount){
  
    var discountpercentage = 0;
    var price = $("#salesprice"+channelid).val();
    price = (price!=0)?price:0;
  
    if(discountamount!=undefined && discountamount!=''){
      
      if(parseFloat(discountamount)>parseFloat(price)){
          discountamount = parseFloat(price);
          $("#discamnt"+channelid).val(parseFloat(discountamount).toFixed(2));
      }
      
      if(parseFloat(price)!=0){
          var discountpercentage = ((parseFloat(discountamount)*100) / parseFloat(price));
      }
      
      $("#discper"+channelid).val(parseFloat(discountpercentage).toFixed(2)); 
    }else{
        $("#discamnt"+channelid).val('');
        $("#discper"+channelid).val(""); 
    }
}
function getproduct(type=1){

    $('#productid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select Product</option>')
            .val('0')
        ;
    $('#priceid')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Variant</option>')
        .val('0')
    ;
    
    $('#productid').selectpicker('refresh');
    $('#priceid').selectpicker('refresh');
    var categoryid = $("#categoryid").val();
    if(ACTION==0){
        $("#channelsection").html("");
    }
    if(categoryid!=0){
      var uurl = SITE_URL+"price-list/getProductByCategoryId";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {categoryid:categoryid,type:type},
        dataType: 'json',
        async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
  
                var productname = response[i]['name'].replace("'","&apos;");
                if(DROPDOWN_PRODUCT_LIST==0){
                    
                    $("#productid").append($('<option>', { 
                        value: response[i]['id'],
                        text : productname,
                        "data-producttype" : response[i]['producttype']
                    }));
      
                }else{
                    
                    $("#productid").append($('<option>', { 
                        value: response[i]['id'],
                        text : productname,
                        "data-producttype" : response[i]['producttype'],
                        "data-content" :'<img src="'+PRODUCT_PATH+response[i]['image']+'" style="width:40px">  ' + productname
                    }));
                  }
            }
            if(PRODUCTID!=0 && ACTION==1){
                $('#productid').val(PRODUCTID);
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
            $('.mask').hide();
            $('#loader').hide();
        },
      });
    }
    $('#productid').selectpicker('refresh');
    $('#priceid').selectpicker('refresh');
}
function getproductvariant(type=1){

    $('#priceid')
        .find('option')
        .remove()
        .end()
        .append('<option value="0">Select Variant</option>')
        .val('0')
    ;
    
    $('#priceid').selectpicker('refresh');
    var productid = $("#productid").val();
    if(ACTION==0){
        $("#channelsection").html("");
    }

    if(productid!=0){
      var uurl = SITE_URL+"price-list/getVariantByProductId";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {productid:productid,type:type},
        dataType: 'json',
        async: false,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){
  
            for(var i = 0; i < response.length; i++) {
  
                $("#priceid").append($('<option>', { 
                    value: response[i]['id'],
                    text : response[i]['name']
                }));
            }
            if(PRICEID!=0 && ACTION==1){
                $('#priceid').val(PRICEID);
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
            $('.mask').hide();
            $('#loader').hide();
        },
      });
    }
    $('#priceid').selectpicker('refresh');
}
function getchannelpricelistdata(){
    $("#channelsection").html("");
    
    var productid = $("#productid").val();
    var priceid = $("#priceid").val();
    var producttype = $("#productid option:selected").attr("data-producttype");
    
    if(priceid!=0){
        var uurl = SITE_URL+"price-list/getchannelpricelistdata";
      
        $.ajax({
          url: uurl,
          type: 'POST',
          data: {productid:productid,priceid:priceid,producttype:producttype},
          dataType: 'json',
          async: false,
          beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
          },
          success: function(response){
            // var obj = JSON.parse(response);
            var channeldata = response['channeldata'];
            var HTML = "";

            if(channeldata.length > 0){
                for(var i = 0; i < channeldata.length; i++) {
                    var channelid = channeldata[i]['id'];
    
                    var MULTIPLE_PRICE_HTML = '<div class="row" id="multiplepricesection'+channelid+'" style="display: none;">\
                                                <div class="col-md-12"><hr></div>\
                                                <div class="col-md-12 p-n">\
                                                    <div id="headingmultipleprice_'+channelid+'_1" class="col-md-4 headingmultipleprice'+channelid+'">\
                                                        <div class="col-md-4">\
                                                            <div class="form-group">\
                                                                <div class="col-md-12 pr-xs pl-xs">\
                                                                    <label class="control-label">Price <span class="mandatoryfield">*</span></label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <div class="form-group">\
                                                                <div class="col-md-12 pr-xs pl-xs">\
                                                                    <label class="control-label">Quantity <span class="mandatoryfield">*</span></label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <div class="form-group text-right">\
                                                                <div class="col-md-12 pl-xs">\
                                                                    <label class="control-label">Disc. (%)</label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    <div id="headingmultipleprice_'+channelid+'_2" class="col-md-4 headingmultipleprice'+channelid+'" style="display: none;">\
                                                        <div class="col-md-4">\
                                                            <div class="form-group">\
                                                                <div class="col-md-12 pr-xs pl-xs">\
                                                                    <label class="control-label">Price <span class="mandatoryfield">*</span></label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <div class="form-group">\
                                                                <div class="col-md-12 pr-xs pl-xs">\
                                                                    <label class="control-label">Quantity <span class="mandatoryfield">*</span></label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <div class="form-group text-right">\
                                                                <div class="col-md-12 pl-xs">\
                                                                    <label class="control-label">Disc. (%)</label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    <div id="headingmultipleprice_'+channelid+'_3" class="col-md-4 headingmultipleprice'+channelid+'" style="display: none;">\
                                                        <div class="col-md-4">\
                                                            <div class="form-group">\
                                                                <div class="col-md-12 pr-xs pl-xs">\
                                                                    <label class="control-label">Price <span class="mandatoryfield">*</span></label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <div class="form-group">\
                                                                <div class="col-md-12 pr-xs pl-xs">\
                                                                    <label class="control-label">Quantity <span class="mandatoryfield">*</span></label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <div class="form-group text-right">\
                                                                <div class="col-md-12 pl-xs">\
                                                                    <label class="control-label">Disc. (%)</label>\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                                <div id="countmultipleprice_'+channelid+'_1" class="col-md-4 countmultipleprice'+channelid+'">\
                                                    <div class="col-md-4">\
                                                        <div class="form-group mt-n" for="variantsalesprice_'+channelid+'_1" id="variantsalesprice_div_'+channelid+'_1">\
                                                            <div class="col-md-12 pr-xs pl-xs">\
                                                                <input type="text" id="variantsalesprice_'+channelid+'_1" onkeypress="return decimal_number_validation(event,this.value,10)" class="form-control variantsalesprices text-right" name="variantsalesprice['+channelid+'][]" value="">\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    <div class="col-md-3">\
                                                        <div class="form-group mt-n" for="variantqty_'+channelid+'_1" id="variantqty_div_'+channelid+'_1">\
                                                            <div class="col-md-12 pr-xs pl-xs">\
                                                                <input type="text" id="variantqty_'+channelid+'_1" onkeypress="return isNumber(event)" class="form-control variantqty" name="variantqty['+channelid+'][]" value="" maxlength="4">\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    <div class="col-md-3">\
                                                        <div class="form-group mt-n text-right mt-n" for="variantdiscpercent_'+channelid+'_1" id="variantdiscpercent_div_'+channelid+'_1">\
                                                            <div class="col-md-12 pl-xs">\
                                                                <input type="text" id="variantdiscpercent_'+channelid+'_1" onkeypress="return decimal_number_validation(event,this.value,5)" class="form-control text-right variantdiscpercent" name="variantdiscpercent['+channelid+'][]" value="" onkeyup="return onlypercentage(this.id)">\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    <div class="col-md-2 pr-n pl-xs">\
                                                        <div class="form-group pt-sm mt-n">\
                                                            <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice'+channelid+'" onclick="removevariantprice('+channelid+',1)" style="display:none;"><i class="fa fa-minus"></i></button>\
                                                            <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice'+channelid+'" onclick="addnewvariantprice('+channelid+')"><i class="fa fa-plus"></i></button>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                            </div>';
    
                    HTML += '<div class="panel panel-default border-panel">\
                                <div class="panel-heading">\
                                    <h2><span id="channelname'+channelid+'">'+channeldata[i]['name']+'</span> Channel Price Details</h2>\
                                </div>\
                                <div class="panel-body pt-n">\
                                    <div class="col-md-12 p-n">\
                                        <input type="hidden" name="productbasicpricemappingid[]" id="productbasicpricemappingid'+channelid+'" value="">\
                                        <input type="hidden" name="channelid[]" class="channelid" value="'+channelid+'">\
                                        <div class="col-md-2">\
                                            <div class="form-group" id="salesprice'+channelid+'_div">\
                                                <div class="col-sm-12 pl-n pr-md">\
                                                    <label for="salesprice'+channelid+'" class="control-label">Sales Price</label>\
                                                    <input type="text" name="salesprice[]" id="salesprice'+channelid+'" class="form-control text-right price" value="" onkeypress="return decimal_number_validation(event,this.value,8)" div-id="'+channelid+'">\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-2">\
                                            <div class="form-group" id="minimumsalesprice'+channelid+'_div">\
                                                <div class="col-md-12 pl-n pr-sm text-left">\
                                                    <label for="minimumsalesprice'+channelid+'" class="control-label">Min. Sales Price</label>\
                                                    <input type="text" name="minimumsalesprice[]" id="minimumsalesprice'+channelid+'" class="form-control text-right" value="" onkeypress="return decimal_number_validation(event,this.value,8)" div-id="'+channelid+'">\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-1">\
                                            <div class="form-group" id="minqty'+channelid+'_div">\
                                                <div class="col-md-12 pl-n pr-sm text-left">\
                                                    <label for="minqty'+channelid+'" class="control-label">Min. Qty</label>\
                                                    <input type="text" name="minqty[]" id="minqty'+channelid+'" class="form-control text-right" value="" onkeypress="return isNumber(event)" maxlength="4">\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-1">\
                                            <div class="form-group" id="maxqty'+channelid+'_div">\
                                                <div class="col-md-12 pl-sm pr-sm text-left">\
                                                    <label for="maxqty'+channelid+'" class="control-label">Max. Qty</label>\
                                                    <input type="text" name="maxqty[]" id="maxqty'+channelid+'" class="form-control text-right" value="" onkeypress="return isNumber(event)" maxlength="4">\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-2">\
                                            <div class="form-group" id="discper'+channelid+'_div">\
                                                <div class="col-md-12 pl-sm pr-sm text-left">\
                                                    <label for="discper'+channelid+'" class="control-label">Disc. (%)</label>\
                                                    <input type="text" name="discper[]" id="discper'+channelid+'" class="form-control text-right discper" value="" onkeypress="return decimal_number_validation(event,this.value,5)">\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-2">\
                                            <div class="form-group" id="discamnt'+channelid+'_div">\
                                                <div class="col-md-12 pl-sm pr-sm text-left">\
                                                    <label for="discamnt'+channelid+'" class="control-label">Disc. ('+CURRENCY_CODE+')</label>\
                                                    <input type="text" name="discamnt[]" id="discamnt'+channelid+'" class="form-control text-right discamnt" value="" onkeypress="return decimal_number_validation(event,this.value,10)">\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-2">\
                                            <div class="form-group mt-xl" id="price'+channelid+'_div">\
                                                <div class="col-sm-12">\
                                                    <div class="checkbox">\
                                                        <input id="allowproduct'+channelid+'" type="checkbox" value="1" name="allowproduct'+channelid+'" class="checkradios m-n">\
                                                        <label style="font-size: 14px;" for="allowproduct'+channelid+'"> Allowed</label>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-12">\
                                            <div class="form-group">\
                                                <label for="focusedinput" class="col-sm-1 control-label pt-xs pl-n" style="text-align: left;">Price Type</label>\
                                                <div class="col-sm-4 pl-n">\
                                                    <div class="col-sm-6 col-xs-6" style="padding-left: 0px;">\
                                                        <div class="radio">\
                                                            <input type="radio" name="pricetype'+channelid+'" id="singleqty'+channelid+'" class="pricetype" value="0" checked>\
                                                            <label for="singleqty'+channelid+'">Single Quantity</label>\
                                                        </div>\
                                                    </div>\
                                                    <div class="col-sm-6 col-xs-6 p-n">\
                                                        <div class="radio">\
                                                            <input type="radio" name="pricetype'+channelid+'" id="multipleqty'+channelid+'" class="pricetype" value="1">\
                                                            <label for="multipleqty'+channelid+'">Multiple Quantity</label>\
                                                        </div>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                        '+MULTIPLE_PRICE_HTML+'\
                                    </div>\
                                </div>\
                            </div>';
    
                }
                $("#channelsection").html(HTML);
                
                if(ACTION==1){
                    for(var j = 0; j < channeldata.length; j++) {
                        $(".remove_variantprice"+channeldata[j]['id']+":first").show();
                        $(".add_variantprice"+channeldata[j]['id']+":last").hide();
                    }
                }
            }
          },
          error: function(xhr) {
            //alert(xhr.responseText);
          },
          complete: function(){
            $('.mask').hide();
            $('#loader').hide();
          },
        });    
    }
}

function addnewvariantprice(MainID) {
	
	var SubID = parseInt($(".countmultipleprice"+MainID+":last").attr("id").split("_")[2])+1;

	var HTML = '<div id="countmultipleprice_'+MainID+'_'+SubID+'" class="col-md-4 countmultipleprice'+MainID+'">\
					<div class="col-md-4">\
						<div class="form-group mt-n" for="variantsalesprice_'+MainID+'_'+SubID+'" id="variantsalesprice_div_'+MainID+'_'+SubID+'">\
                            <div class="col-md-12 pr-xs pl-xs">\
                                <input type="text" id="variantsalesprice_'+MainID+'_'+SubID+'" onkeypress="return decimal(event,this.value)" class="form-control variantsalesprice'+MainID+'" name="variantsalesprice['+MainID+'][]" value="">\
                            </div>\
						</div>\
					</div>\
					<div class="col-md-3">\
						<div class="form-group mt-n" for="variantqty_'+MainID+'_'+SubID+'" id="variantqty_div_'+MainID+'_'+SubID+'">\
                            <div class="col-md-12 pl-xs pr-xs">\
                                <input type="text" id="variantqty_'+MainID+'_'+SubID+'" onkeypress="return isNumber(event)" class="form-control variantqty'+MainID+'" name="variantqty['+MainID+'][]" value="" maxlength="4">\
                            </div>\
						</div>\
					</div>\
					<div class="col-md-3">\
						<div class="form-group text-right mt-n" for="variantdiscpercent_'+MainID+'_'+SubID+'" id="variantdiscpercent_div_'+MainID+'_'+SubID+'">\
                            <div class="col-md-12 pl-xs">\
						        <input type="text" id="variantdiscpercent_'+MainID+'_'+SubID+'" onkeypress="return decimal(event,this.value)" class="form-control text-right variantdiscpercent'+MainID+'" name="variantdiscpercent['+MainID+'][]" value="" onkeyup="return onlypercentage(this.id)">\
                            </div>\
						</div>\
					</div>\
					<div class="col-md-2 pr-n pl-xs">\
                        <div class="form-group pt-sm mt-n">\
                            <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice'+MainID+'" onclick="removevariantprice('+MainID+','+SubID+')" style=""><i class="fa fa-minus"></i></button>\
                            <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice'+MainID+'" onclick="addnewvariantprice('+MainID+')"><i class="fa fa-plus"></i></button>\
                        </div>\
					</div>\
				</div>';


	$(".remove_variantprice"+MainID+":first").show();
    $(".add_variantprice"+MainID+":last").hide();
    $("#countmultipleprice_"+MainID+"_"+(SubID-1)).after(HTML);

	if($(".countmultipleprice"+MainID).length > 1){
		$("#headingmultipleprice_"+MainID+"_2").show();
        if($(".countmultipleprice"+MainID).length > 2){
            $("#headingmultipleprice_"+MainID+"_3").show();
        }
	}
}

function removevariantprice(MainID,SubID) {

	$("#countmultipleprice_"+MainID+"_"+SubID).remove();

	$(".add_variantprice"+MainID+":last").show();
	if ($(".remove_variantprice"+MainID+":visible").length == 1) {
		$(".remove_variantprice"+MainID+":first").hide();
	}

	if($(".countmultipleprice"+MainID).length > 2){
		$("#headingmultipleprice_"+MainID+"_2,#headingmultipleprice_"+MainID+"_3").show();
	}else if($(".countmultipleprice"+MainID).length == 2){
		$("#headingmultipleprice_"+MainID+"_2").show();
        $("#headingmultipleprice_"+MainID+"_3").hide();
	}else{
		$("#headingmultipleprice_"+MainID+"_2,#headingmultipleprice_"+MainID+"_3").hide();
	}
}
function onlypercentage(val){
    fieldval = $("#"+val).val();
    if (parseFloat(fieldval) < 0) $("#"+val).val(0);
    if (parseFloat(fieldval) > 100) $("#"+val).val(100);
}

function resetdata(){
    $("#category_div").removeClass("has-error is-focused");
    $("#product_div").removeClass("has-error is-focused");
    $("#price_div").removeClass("has-error is-focused");

    if(ACTION==0){
        $('#categoryid,#productid,#priceid').val('0');
        $('.selectpicker').selectpicker('refresh');

        $('#categoryid').change();

    }
    $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(btntype=0) {
    
    var categoryid = $('#categoryid').val();
    var productid = $('#productid').val();
    var priceid = $('#priceid').val();

    var isvalidcategoryid = isvalidproductid = isvalidpriceid = isvalidsalesprice = isvalidvariantsalesprice = isvalidvariantqty = 1;
    PNotify.removeAll();
    
    if(categoryid == 0){
        $("#category_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select category !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcategoryid = 0;
    }else {
        $("#category_div").removeClass("has-error is-focused");
    }

    if(productid == 0){
        $("#product_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select product !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidproductid = 0;
    }else {
        $("#product_div").removeClass("has-error is-focused");
    }
    if(priceid == 0){
        $("#price_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidpriceid = 0;
    }else {
        $("#price_div").removeClass("has-error is-focused");
    }

    var c=1;
    $('.channelid').each(function(){
		var channelid = parseInt($(this).val());
        var channel = $("#channelname"+channelid).html();
		
        if($("#multipleqty"+channelid).is(":checked")){
            var firstRowId = parseInt($('.countmultipleprice'+channelid+':first').attr('id').split("_")[2]);
			$('.countmultipleprice'+channelid).each(function(index){
				var id = parseInt($(this).attr('id').split("_")[2]);
				var eleID = "_"+channelid+"_"+id;
				var variantprice = $("#variantsalesprice"+eleID).val();
				var variantqty = $("#variantqty"+eleID).val();
				
				if((variantprice!="" && variantprice!=0) || (variantqty!="" && variantqty!=0)/*  || parseInt(id)==parseInt(firstRowId) */){
					if(variantprice==0){
						$("#variantsalesprice_div"+eleID).addClass("has-error is-focused");
						new PNotify({title: 'Please enter '+(index+1)+' sales price in '+channel.toLowerCase()+' channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
						isvalidvariantsalesprice = 0;
					}else{
						$("#variantsalesprice_div"+eleID).removeClass("has-error is-focused");
					}
					if(variantqty==0){
						$("#variantqty_div"+eleID).addClass("has-error is-focused");
						new PNotify({title: 'Please enter '+(index+1)+' quantity in '+channel.toLowerCase()+' channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
						isvalidvariantqty = 0;
					}else{
						$("#variantqty_div"+eleID).removeClass("has-error is-focused");
					}
				}else{
					$("#variantsalesprice_div"+eleID).removeClass("has-error is-focused");
					$("#variantqty_div"+eleID).removeClass("has-error is-focused");
				}

			});
		}/* else{
            if($("#salesprice"+channelid).val()==0){
                $("#salesprice"+channelid+"_div").addClass("has-error is-focused");
                new PNotify({title: 'Please enter sales price in '+channel.toLowerCase()+' channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
                isvalidsalesprice = 0;
            }else{
                $("#salesprice"+channelid+"_div").removeClass("has-error is-focused");
            }
        } */
		c++;
	});

    if(isvalidcategoryid == 1 && isvalidproductid ==1 && isvalidpriceid == 1 && isvalidsalesprice == 1 && isvalidvariantsalesprice == 1 && isvalidvariantqty == 1){
        var formData = new FormData($('#productpriceform')[0]);
        if(ACTION==0){
            var uurl = SITE_URL+"price-list/product-price-add";
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
                    if(response==1){
                        new PNotify({title: "Price successfully added !",styling: 'fontawesome',delay: '3000',type: 'success'});
                        if(btntype==1){
                            resetdata();
                        }else{
                            setTimeout(function() { window.location=SITE_URL+"price-list"; }, 1500);
                        }
                    }else{
                        new PNotify({title: 'Price not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var uurl = SITE_URL+"price-list/update-product-price";
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
                if(response==1){
                    new PNotify({title: "Price successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    if(btntype==1){
                        setTimeout(function() { window.location=SITE_URL+"price-list/add-product-price"; }, 1500);
                    }else{
                        setTimeout(function() { window.location=SITE_URL+"price-list"; }, 1500);
                    }
                }else{
                    new PNotify({title: 'Price not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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