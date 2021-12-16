$(document).ready(function() {
  /* $("#categoryid").change(function()
  {
    var productcategory = $(this).val();
    var memberid = $("#memberid").val();
        var uurl = SITE_URL+"member/getProduct";
        $.ajax({
          url: uurl,
          type: 'POST',
          data: {categoryid:productcategory,memberid:memberid},
          dataType: 'json',
          async: false,
          success: function(response){
            $('#productid')
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select Product</option>')
            .val('whatever')
            ;

            for(var i = 0; i < response.length; i++) {

              $('#productid').append($('<option>', { 
                value: response[i]['id'],
                text : response[i]['name']
              }));

            }
            // $('#product'+prow).val(areaid);
            $('#productid').selectpicker('refresh');
          },
          error: function(xhr) {
                //alert(xhr.responseText);
              },
            });
      $("#load_variants").html("");
  })
 */
  $("#sellermemberid").change(function(){

    var sellermemberid = $(this).val();
    var memberid = $("#memberid").val();

    $('#categoryid')
          .find('option')
          .remove()
          .end()
          .append('<option value="0">Select Category</option>')
          .val('whatever')
          ;

    if(sellermemberid!=''){
      var uurl = SITE_URL+"category/getCategoryBySeller";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {sellermemberid:sellermemberid,memberid:memberid},
        dataType: 'json',
        async: false,
        success: function(response){
          
          for(var i = 0; i < response.length; i++) {

            $('#categoryid').append($('<option>', { 
              value: response[i]['id'],
              text : response[i]['name']
            }));

          }
          
          $('#categoryid').selectpicker('refresh');
        },
        error: function(xhr) {
          //alert(xhr.responseText);
        },
      });
    }
    $("#load_variants").html("");
    $('#categoryid').selectpicker('refresh');
  });
  $("#categoryid").change(function(){
    loadproductdata();
  });
  $("#brandid").change(function(){
    loadproductdata();
  });
  $("#defaultchannelid").change(function(){
    $('select[name="channelid[]"]').val(this.value);
    $('.selectpicker').selectpicker('refresh');
  });

  $(".add_variantprice0").hide();
  $(".add_variantprice0:last").show();
});

$(document).on('keyup', '.memberprice', function(e) {
  var elementid = e.target.id;
  rowid = (ACTION==0)?elementid.match(/(\d+)/g):"";

  calculatediscount(rowid);
});
$(document).on('keyup', '.discper', function(e) {
  var elementid = e.target.id;
  rowid = (ACTION==0)?elementid.match(/(\d+)/g):"";
  
  if(parseFloat(this.value)>=100){
    $("#discper"+rowid).val("100");
  }
  calculatediscount(rowid);
});
$(document).on('keyup', '.discamnt', function(e) {
  var elementid = e.target.id;
  rowid = (ACTION==0)?elementid.match(/(\d+)/g):"";
  calculatediscountmount(rowid,$(this).val());
});

$(document).on('change','.pricetype',function (e) {
	var divid = parseInt($(this).attr('id').match(/\d+/));
	if($(this).val()==0){
		$("#memberprice"+divid+"_div,#salesprice"+divid+"_div,#discper"+divid+"_div,#discamnt"+divid+"_div").parent().show();
		$("#multiplepricesection"+divid).hide();
	}else{
    $("#memberprice"+divid+"_div,#salesprice"+divid+"_div,#discper"+divid+"_div,#discamnt"+divid+"_div").parent().hide();
		$("#multiplepricesection"+divid).show();
	}
});

function calculatediscount(rowid){
  var discountpercentage = $("#discper"+rowid).val(); 
  discountpercentage = (discountpercentage!='' && discountpercentage!=0)?discountpercentage:0;
  var price = $("#memberprice"+rowid).val();
  price = (price!='' && price!=0)?price:0;
  
  if(price!=0 && discountpercentage!=0){
    var discountamount = (parseFloat(price)*parseFloat(discountpercentage)/100);
    
    $("#discamnt"+rowid).val(parseFloat(discountamount).toFixed(2));
  }else{
    $("#discamnt"+rowid).val('');
  }
}
function calculatediscountmount(rowid,discountamount){

  var discountpercentage = 0;
  var price = $("#memberprice"+rowid).val();
  price = (price!=0)?price:0;

  if(discountamount!=undefined && discountamount!=''){
    
    if(parseFloat(discountamount)>parseFloat(price)){
      discountamount = parseFloat(price);
      $("#discamnt"+rowid).val(parseFloat(discountamount).toFixed(2));
    }
    
    if(parseFloat(price)!=0){
      var discountpercentage = ((parseFloat(discountamount)*100) / parseFloat(price));
    }
    
    $("#discper"+rowid).val(parseFloat(discountpercentage).toFixed(2)); 
  }else{
    $("#discamnt"+rowid).val('');
    $("#discper"+rowid).val(""); 
  }
}
function loadproductdata() {
  var productcategory = $("#categoryid").val();
  var brandid = $("#brandid").val();
  var memberid = $("#memberid").val();
  var sellermemberid = $("#sellermemberid").val();

  if(productcategory!=0){
    var uurl = SITE_URL+"member/getProductByCategorywithNotAssignMember";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {categoryid:productcategory,memberid:memberid,sellermemberid:sellermemberid,brandid:brandid},
      dataType: 'json',
      async: false,
      success: function(response){
        
        var producttablehtml = '<table class="table table-bordered">';
        producttablehtml += '<thead>\
                              <tr>\
                                <th class="width5">Sr. No.</th>\
                                <th width="15%">Product Name</th>\
                                <th class="text-right">Price</th>\
                                <th width="55%" class="text-center">Details</th>\
                                <th class="width5 text-center">Allow Product</th>\
                              </tr></thead>';
        if(response.length>0){

          producttablehtml += '<tbody>';
          for(var i=0; i<response.length; i++){
              var pointshtml = '';
              if(REWARDS_POINTS==1){
                pointshtml += '<br><div class="col-md-12 mt-xs mb-xs p-n">';
                
                pointshtml += '<span class="label panel-teal mr-sm" title="Points for Seller" style="padding: 3px 10px !important;font-size: 10px;">PS : '+response[i]['pointsforseller']+'</span>';   
                
                pointshtml += '<span class="label panel-teal" title="Points for Buyer" style="padding: 3px 10px !important;font-size: 10px;">PB : '+response[i]['pointsforbuyer']+'</span>';   
              
                pointshtml += '</div>';
              }
              
              var MULTIPLE_PRICE_HTML = '<div class="row" id="multiplepricesection'+(i+1)+'" style="display: none;">\
                                          <div class="col-md-12">\
                                            <div id="headingmultipleprice_'+(i+1)+'" class="headingmultipleprice'+(i+1)+'">\
                                              <div class="col-md-3">\
                                                <div class="form-group">\
                                                  <div class="col-md-12 pr-xs pl-n">\
                                                    <label class="control-label">Price <span class="mandatoryfield">*</span></label>\
                                                  </div>\
                                                </div>\
                                              </div>\
                                              <div class="col-md-3">\
                                                <div class="form-group">\
                                                  <div class="col-md-12 pr-xs pl-xs">\
                                                    <label class="control-label">Sales Price <span class="mandatoryfield">*</span></label>\
                                                  </div>\
                                                </div>\
                                              </div>\
                                              <div class="col-md-2">\
                                                <div class="form-group">\
                                                  <div class="col-md-12 pr-xs pl-xs">\
                                                    <label class="control-label">Quantity <span class="mandatoryfield">*</span></label>\
                                                  </div>\
                                                </div>\
                                              </div>\
                                              <div class="col-md-2">\
                                                <div class="form-group text-right">\
                                                  <div class="col-md-12 pl-xs">\
                                                    <label class="control-label">Disc. (%)</label>\
                                                  </div>\
                                                </div>\
                                              </div>\
                                            </div>\
                                          </div>\
                                          <div class="col-md-12">\
                                            <div id="countmultipleprice_'+(i+1)+'_1" class="countmultipleprice'+(i+1)+'">\
                                              <div class="col-md-3">\
                                                <div class="form-group mt-n" for="variantprice_'+(i+1)+'_1" id="variantprice_div_'+(i+1)+'_1">\
                                                  <div class="col-md-12 pr-xs pl-n">\
                                                    <input type="text" id="variantprice_'+(i+1)+'_1" onkeypress="return decimal_number_validation(event,this.value,10)" class="form-control text-right variantprices'+(i+1)+'" name="variantprice['+(i+1)+'][]" value="">\
                                                  </div>\
                                                </div>\
                                              </div>\
                                              <div class="col-md-3">\
                                                <div class="form-group mt-n" for="variantsalesprice_'+(i+1)+'_1" id="variantsalesprice_div_'+(i+1)+'_1">\
                                                  <div class="col-md-12 pr-xs pl-xs">\
                                                    <input type="text" id="variantsalesprice_'+(i+1)+'_1" onkeypress="return decimal_number_validation(event,this.value,10)" class="form-control text-right variantsalesprices'+(i+1)+'" name="variantsalesprice['+(i+1)+'][]" value="">\
                                                  </div>\
                                                </div>\
                                              </div>\
                                              <div class="col-md-2">\
                                                <div class="form-group mt-n" for="variantqty_'+(i+1)+'_1" id="variantqty_div_'+(i+1)+'_1">\
                                                  <div class="col-md-12 pr-xs pl-xs">\
                                                    <input type="text" id="variantqty_'+(i+1)+'_1" onkeypress="return isNumber(event)" class="form-control variantqty'+(i+1)+'" name="variantqty['+(i+1)+'][]" value="" maxlength="4">\
                                                  </div>\
                                                </div>\
                                              </div>\
                                              <div class="col-md-2">\
                                                <div class="form-group text-right mt-n" for="variantdiscpercent_'+(i+1)+'_1" id="variantdiscpercent_div_'+(i+1)+'_1">\
                                                  <div class="col-md-12 pl-xs">\
                                                    <input type="text" id="variantdiscpercent_'+(i+1)+'_1" onkeypress="return decimal_number_validation(event,this.value,5)" class="form-control text-right variantdiscpercent'+(i+1)+'" name="variantdiscpercent['+(i+1)+'][]" value="" onkeyup="return onlypercentage(this.id)">\
                                                  </div>\
                                                </div>\
                                              </div>\
                                              <div class="col-md-2">\
                                                <div class="form-group pt-sm mt-n">\
                                                  <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice'+(i+1)+'" onclick="removevariantprice('+(i+1)+',1)" style="display:none;"><i class="fa fa-minus"></i></button>\
                                                  <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice'+(i+1)+'" onclick="addnewvariantprice('+(i+1)+')"><i class="fa fa-plus"></i></button>\
                                                </div>\
                                              </div>\
                                            </div>\
                                          </div>\
                                        </div>';

              producttablehtml += "<tr>";
              producttablehtml += "<td>"+(i+1)+"</td>";
              producttablehtml += '<td>'+ucwords(response[i]['name'])+pointshtml+'\
                                    <input type="hidden" name="productid[]" id="productid'+(i+1)+'" value="'+response[i]['id']+'">\
                                    <input type="hidden" name="productpriceid[]" id="productpriceid'+(i+1)+'" value="'+response[i]['priceid']+'">\
                                    <input type="hidden" name="pricetype[]" value="'+response[i]['pricetype']+'">\
                                  </td>';

              if(parseFloat(response[i]['minprice']).toFixed(2) == parseFloat(response[i]['maxprice']).toFixed(2)){
                var product_price = parseFloat(response[i]['minprice']).toFixed(2);
              }else{
                var product_price = parseFloat(response[i]['minprice']).toFixed(2)+" - "+parseFloat(response[i]['maxprice']).toFixed(2);
              }
              
              producttablehtml += "<td class='text-right'>"+product_price+"</td>";
              producttablehtml += '<td>\
                                  <div class="col-md-3 pl-n pr-xs">\
                                    <div class="form-group m-n" id="memberprice'+(i+1)+'_div">\
                                      <input type="text" name="memberprice[]" id="memberprice'+(i+1)+'" class="form-control text-right m-n memberprice" value="" onkeypress="return decimal_number_validation(event,this.value,8)" placeholder="'+Member_label+' Price">\
                                    </div>\
                                  </div>\
                                  <div class="col-md-3 pr-sm pl-xs">\
                                    <div class="form-group m-n" id="salesprice'+(i+1)+'_div">\
                                      <input type="text" name="salesprice[]" id="salesprice'+(i+1)+'" class="form-control text-right m-n" value="" onkeypress="return decimal_number_validation(event,this.value,8)" placeholder="Sales Price">\
                                    </div>\
                                  </div>\
                                  <div class="col-md-3 pr-xs pl-n">\
                                    <div class="form-group m-n" id="memberstock'+(i+1)+'_div">\
                                      <input type="text" name="memberstock[]" id="memberstock'+(i+1)+'" class="form-control text-right m-n" value="" onkeypress="return isNumber(event)" placeholder="'+Member_label+' Stock">\
                                    </div>\
                                  </div>\
                                  <div class="col-md-3 pr-n pl-xs">\
                                    <div class="form-group m-n" for="channelid" id="channelid'+(i+1)+'_div">\
                                      <select class="form-control selectpicker m-n" id="channelid'+(i+1)+'" name="channelid[]" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                        '+ChannelDataHTML+'\
                                      </select>\
                                    </div>\
                                  </div>\
                                    <div class="col-md-12 p-n">\
                                      <div class="col-md-3 pl-n pr-xs">\
                                        <div class="form-group m-n p-n" id="minimumsalesprice'+(i+1)+'_div">\
                                            <div class="col-md-12 p-n text-left">\
                                              <label for="minimumsalesprice'+(i+1)+'" class="control-label">Min. Sales Price</label>\
                                              <input type="text" style="width:100%;" name="minimumsalesprice[]" id="minimumsalesprice'+(i+1)+'" class="form-control text-right" value="" onkeypress="return isNumber(event)" maxlength="4">\
                                            </div>\
                                        </div>\
                                      </div>\
                                      <div class="col-md-2 pl-n pr-xs">\
                                        <div class="form-group m-n p-n" id="minqty'+(i+1)+'_div">\
                                            <div class="col-md-12 p-n text-left">\
                                              <label for="minqty'+(i+1)+'" class="control-label">Min. Qty</label>\
                                              <input type="text" style="width:100%;" name="minqty[]" id="minqty'+(i+1)+'" class="form-control text-right" value="" onkeypress="return isNumber(event)" maxlength="4">\
                                            </div>\
                                        </div>\
                                      </div>\
                                      <div class="col-md-2 pr-xs pl-xs">\
                                        <div class="form-group m-n p-n" id="maxqty'+(i+1)+'_div">\
                                            <div class="col-md-12 p-n text-left">\
                                              <label for="maxqty'+(i+1)+'" class="control-label">Max. Qty</label>\
                                              <input type="text" style="width:100%;" name="maxqty[]" id="maxqty'+(i+1)+'" class="form-control text-right" value="" onkeypress="return isNumber(event)" maxlength="4">\
                                            </div>\
                                        </div>\
                                      </div>\
                                      <div class="col-md-2 pl-xs pr-xs">\
                                        <div class="form-group m-n p-n" id="discper'+(i+1)+'_div">\
                                            <div class="col-md-12 p-n text-left">\
                                              <label for="discper'+(i+1)+'" class="control-label">Disc. (%)</label>\
                                              <input type="text" style="width:100%;" name="discper[]" id="discper'+(i+1)+'" class="form-control text-right discper" value="" onkeypress="return decimal_number_validation(event,this.value,5)">\
                                            </div>\
                                        </div>\
                                      </div>\
                                      <div class="col-md-3 pr-n pl-xs">\
                                        <div class="form-group m-n p-n" id="discamnt'+(i+1)+'_div">\
                                            <div class="col-md-12 p-n text-left">\
                                              <label for="discamnt'+(i+1)+'" class="control-label">Disc. ('+CURRENCY_CODE+')</label>\
                                              <input type="text" style="width:100%;" name="discamnt[]" id="discamnt'+(i+1)+'" class="form-control text-right discamnt" value="" onkeypress="return decimal_number_validation(event,this.value,10)">\
                                            </div>\
                                        </div>\
                                      </div>\
                                    </div>\
                                    <div class="col-md-12">\
                                        <div class="form-group">\
                                            <label for="focusedinput" class="col-sm-3 control-label pt-xs pl-n" style="text-align: left;">Price Type</label>\
                                            <div class="col-sm-8 pl-n">\
                                                <div class="col-sm-6 col-xs-6" style="padding-left: 0px;">\
                                                    <div class="radio">\
                                                        <input type="radio" name="pricetype'+(i+1)+'" id="singleqty'+(i+1)+'" class="pricetype" value="0" checked>\
                                                        <label for="singleqty'+(i+1)+'">Single Quantity</label>\
                                                    </div>\
                                                </div>\
                                                <div class="col-sm-6 col-xs-6 p-n">\
                                                    <div class="radio">\
                                                        <input type="radio" name="pricetype'+(i+1)+'" id="multipleqty'+(i+1)+'" class="pricetype" value="1">\
                                                        <label for="multipleqty'+(i+1)+'">Multiple Quantity</label>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>\
                                    '+MULTIPLE_PRICE_HTML+'\
                                  </td>';

              producttablehtml += '<td class="text-center">\
                                      <div class="checkbox"><input id="allowcheck'+(i+1)+'"  type="checkbox" value="1" name="allowcheck'+(i+1)+'" class="checkradios m-n" checked>\
                                      <label for="allowcheck'+(i+1)+'"></label></div>\
                                    </td>';
              producttablehtml += "</tr>";

          }
        }
        if(response.length==0){
          producttablehtml += '<tbody><tr><td colspan="6" class="text-center">No data available in table.</td></tr></tbody></table>';
        }
        producttablehtml += '</tbody></table>';
        $("#load_variants").html(producttablehtml);

        if($('#defaultchannelid').val()!=0){
          $('select[name="channelid[]"]').val($('#defaultchannelid').val());
        }

        $(".selectpicker").selectpicker("refresh");
      },
      error: function(xhr) {
        //alert(xhr.responseText);
      },
    });
  }else{
    $("#load_variants").html("");
  }
}
function addnewvariantprice(MainID,action='add') {
  
  var key = "";
  if(action == "add"){
    key = '['+MainID+']';
  } 
	
	var SubID = parseInt($(".countmultipleprice"+MainID+":last").attr("id").split("_")[2])+1;

	var HTML = '<div id="countmultipleprice_'+MainID+'_'+SubID+'" class="countmultipleprice'+MainID+'">\
					<div class="col-md-3">\
						<div class="form-group mt-n" for="variantprice_'+MainID+'_'+SubID+'" id="variantprice_div_'+MainID+'_'+SubID+'">\
              <div class="col-md-12 pr-xs pl-n"> \
                <input type="text" id="variantprice_'+MainID+'_'+SubID+'" onkeypress="return decimal_number_validation(event,this.value,10)" class="form-control text-right variantprices'+MainID+'" name="variantprice'+key+'[]" value="">\
              </div>\
						</div>\
					</div>\
          <div class="col-md-3">\
            <div class="form-group mt-n" for="variantsalesprice_'+MainID+'_'+SubID+'" id="variantsalesprice_div_'+MainID+'_'+SubID+'">\
              <div class="col-md-12 pr-xs pl-xs">\
                <input type="text" id="variantsalesprice_'+MainID+'_'+SubID+'" onkeypress="return decimal_number_validation(event,this.value,10)" class="form-control text-right variantsalesprices'+MainID+'" name="variantsalesprice'+key+'[]" value="">\
              </div>\
            </div>\
          </div>\
					<div class="col-md-2">\
            <div class="form-group mt-n" for="variantqty_'+MainID+'_'+SubID+'" id="variantqty_div_'+MainID+'_'+SubID+'">\
              <div class="col-md-12 pr-xs pl-xs">\
                <input type="text" id="variantqty_'+MainID+'_'+SubID+'" onkeypress="return isNumber(event)" class="form-control variantqty'+MainID+'" name="variantqty'+key+'[]" value="" maxlength="4">\
              </div>\
						</div>\
					</div>\
					<div class="col-md-2">\
						<div class="form-group mt-n text-right" for="variantdiscpercent_'+MainID+'_'+SubID+'" id="variantdiscpercent_div_'+MainID+'_'+SubID+'">\
              <div class="col-md-12 pl-xs">\
                <input type="text" id="variantdiscpercent_'+MainID+'_'+SubID+'" onkeypress="return decimal_number_validation(event,this.value,5)" class="form-control text-right variantdiscpercent'+MainID+'" name="variantdiscpercent'+key+'[]" value="" onkeyup="return onlypercentage(this.id)">\
              </div>\
            </div>\
					</div>\
					<div class="col-md-2">\
            <div class="form-group mt-n pt-sm">\
              <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice'+MainID+'" onclick="removevariantprice('+MainID+','+SubID+')" style=""><i class="fa fa-minus"></i></button>\
              <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice'+MainID+'" onclick="addnewvariantprice('+MainID+')"><i class="fa fa-plus"></i></button>\
            </div>\
          </div>\
				</div>';


	$(".remove_variantprice"+MainID+":first").show();
  $(".add_variantprice"+MainID+":last").hide();
  $("#countmultipleprice_"+MainID+"_"+(SubID-1)).after(HTML);
}

function removevariantprice(MainID,SubID) {

	$("#countmultipleprice_"+MainID+"_"+SubID).remove();

	$(".add_variantprice"+MainID+":last").show();
	if ($(".remove_variantprice"+MainID+":visible").length == 1) {
		$(".remove_variantprice"+MainID+":first").hide();
	}
}
function resetdata(){
  if(ACTION == 0){
    // $("#resetbtn").trigger("click");
    $("#load_variants").html("");
    $("#categoryid").val(0);
    $('.selectpicker').selectpicker('refresh');
  }
}


function checkvalidation(type='add') {

  var categoryid = $("#categoryid").val();
  
  var productid = $("input[name='productid[]']").map(function(){return $(this).val();}).get();
  var isvalidcategoryid = 1;
  var isvalidproduct = isvalidmultipleprice = 1;

  PNotify.removeAll();
  if(ACTION == 0){
    if(categoryid=='' || categoryid==0){
      $("#categoryid_div").addClass("has-error is-focused");
      new PNotify({title: "Please select category !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcategoryid = 0;
    }else{
      $("#categoryid_div").removeClass("has-error is-focused");
    }
    if(productid.length==0){
      isvalidproduct=0;
      new PNotify({title: "No product assign in select category !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcategoryid = 0;
    }

  }
  var c=1;
  $('.memberprice').each(function(){
    var divid = $(this).attr('id').match(/\d+/);
    divid = (divid!=null)?parseInt(divid):0;
    
    if($("#multipleqty"+divid).is(":checked")){
      $('.countmultipleprice'+divid).each(function(index){
        var id = parseInt($(this).attr('id').split("_")[2]);
        var eleID = "_"+divid+"_"+id;
        var variantprice = $("#variantprice"+eleID).val();
        var variantsalesprice = $("#variantsalesprice"+eleID).val();
        var variantqty = $("#variantqty"+eleID).val();
        
        if((variantprice!="" && variantprice!=0) || (variantsalesprice!="" && variantsalesprice!=0) || (variantqty!="" && variantqty!=0)){
          if(variantprice==0){
            $("#variantprice_div"+eleID).addClass("has-error is-focused");
            new PNotify({title: 'Please enter '+(c)+' product '+(index+1)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidmultipleprice = 0;
          }else{
            $("#variantprice_div"+eleID).removeClass("has-error is-focused");
          }
          if(variantsalesprice==0){
            $("#variantsalesprice_div"+eleID).addClass("has-error is-focused");
            new PNotify({title: 'Please enter '+(c)+' product '+(index+1)+' sales price !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidmultipleprice = 0;
          }else{
            $("#variantsalesprice_div"+eleID).removeClass("has-error is-focused");
          }
          if(variantqty==0){
            $("#variantqty_div"+eleID).addClass("has-error is-focused");
            new PNotify({title: 'Please enter '+(c)+' product '+(index+1)+' quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidmultipleprice = 0;
          }else{
            $("#variantqty_div"+eleID).removeClass("has-error is-focused");
          }
        }else{
          $("#variantprice_div"+eleID).removeClass("has-error is-focused");
          $("#variantqty_div"+eleID).removeClass("has-error is-focused");
        }

      });
    }
    c++;
  });

  if(isvalidcategoryid==1 && isvalidproduct==1 && isvalidmultipleprice==1){

    var formData = new FormData($('#memberproductform')[0]);
    if(ACTION == 0){    
      var uurl = SITE_URL+"member/member-product-add";
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
            new PNotify({title: Member_label+" product successfully added!",styling: 'fontawesome',delay: '3000',type: 'success'});
            // resetdata();
            if(type=='addandnew'){
              setTimeout(function() { location.reload(); }, 1500);
            }else{
              setTimeout(function() { window.location=SITE_URL+"member/member-detail/"+$("#memberid").val()+"/products"; }, 1500);
            }

          }else{
            new PNotify({title: Member_label+" product not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"member/member-product-edit";
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
            new PNotify({title: Member_label+" product successfully updated !",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"member/member-detail/"+$("#memberid").val()+"/products"; }, 1500);
          }else{
            new PNotify({title: Member_label+" product not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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

function resetsellerform(){
  $("#sellercode_div").removeClass("has-error is-focused");
  $("#sellercode").val("");
  PNotify.removeAll();
}

function searchmembercode(){

  var sellercode = $("#sellercode").val();

  var isvalidsellercode = 1;
  PNotify.removeAll();

  if(sellercode == ""){
      $("#sellercode_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter seller '+member_label+' code !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidsellercode = 0;
  }else{
      if(sellercode.length != 8){
          $("#sellercode_div").addClass("has-error is-focused");
          new PNotify({title: 'Seller '+member_label+' code required between 8 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidsellercode = 0;
      }
  }

  if(isvalidsellercode == 1){

      var sellermemberid = $("#sellermemberid").find("[data-code='"+sellercode+"']").val();

      if(sellermemberid==undefined){
          var formData = new FormData($('#addsellerform')[0]);
         
          var uurl = SITE_URL+"member/search-seller";
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
                  if(response!=0){
                      var obj = JSON.parse(response);
                      
                      $('#sellermemberid').append($('<option>', { 
                          value: obj['id'],
                          'data-code': obj['membercode'],
                          text : obj['name']+" ("+obj['email']+")",
                          selected : 'selected'
                      }));
                      $('#sellermemberid').selectpicker('refresh');
                      $('#addsellerModal').modal("hide");

                      new PNotify({title: "Seller added successfully.",styling: 'fontawesome',delay: '3000',type: 'success'});
                  }else {
                      new PNotify({title: 'Seller code not found !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
          $("#sellermemberid").val(sellermemberid);
          $('#sellermemberid').selectpicker('refresh');
          $('#addsellerModal').modal("hide");

          new PNotify({title: "Seller already exist !",styling: 'fontawesome',delay: '3000',type: 'error'});
      }
      
  }
}
function onlypercentage(val){
  fieldval = $("#"+val).val();
  if (parseFloat(fieldval) < 0) $("#"+val).val(0);
  if (parseFloat(fieldval) > 100) $("#"+val).val(100);
}