$(document).ready(function(){

	$(".attributeids").each(function() {
		if($(this).val()!=""){
    		loadvariant($(this).attr("div-id"),$(this).attr("attribute-id"));
		}
	});

	$(".variantids").each(function() {
		$(this).val($(this).attr("variant-value"));
		$(this).selectpicker('refresh');
	});

	$(".productvariantdiv").each(function() {
		var id = $(this).attr('id');
		var counter = id.match(/\d+/);
		$("#"+id+" .multi_variant_btn").hide();
		$("#"+id+" .multi_variant_btn:last").show();

		$(".add_variantprice"+counter).hide();
		$(".add_variantprice"+counter+":last").show();
 	});
	
	$("#addnewprice").click(function(){
		pricescount = $("#allprices_count").val();

		var PRICE_TYPE_HTML = '<div class="row">\
									<div class="col-md-6 pr-xs pl-xs">\
										<div class="form-group">\
											<label for="focusedinput" class="col-sm-3 control-label pt-xs">Price Type</label>\
											<div class="col-sm-9">\
												<div class="col-sm-6 col-xs-6" style="padding-left: 0px;">\
													<div class="radio">\
														<input type="radio" name="pricetype'+pricescount+'" id="singleqty'+pricescount+'" class="pricetype" value="0" checked>\
														<label for="singleqty'+pricescount+'">Single Quantity</label>\
													</div>\
												</div>\
												<div class="col-sm-6 col-xs-6 p-n">\
													<div class="radio">\
														<input type="radio" name="pricetype'+pricescount+'" id="multipleqty'+pricescount+'" class="pricetype" value="1">\
														<label for="multipleqty'+pricescount+'">Multiple Quantity</label>\
													</div>\
												</div>\
											</div>\
										</div>\
									</div>\
									<div class="col-md-6 p-n" id="addpriceinpricelist_div'+pricescount+'">\
										<div class="form-group">\
											<div class="col-sm-8">\
												<div class="checkbox text-left">\
												<input id="addpriceinpricelist'+pricescount+'" name="addpriceinpricelist'+pricescount+'" type="checkbox" checked>\
												<label for="addpriceinpricelist'+pricescount+'">Add Price in Price List</label>\
												</div>\
											</div>\
										</div>\
									</div>\
								</div>';

		var MULTIPLE_PRICE_HTML = '<div class="row" id="multiplepricesection'+pricescount+'" style="display: none;">\
										<div class="col-md-12"><hr></div>\
										<div class="col-md-12 p-n">\
											<div id="headingmultipleprice_'+pricescount+'_1" class="col-md-6 headingmultipleprice'+pricescount+'">\
												<div class="col-md-4 pr-xs pl-xs">\
													<div class="form-group">\
														<label class="control-label">Price <span class="mandatoryfield">*</span></label>\
													</div>\
												</div>\
												<div class="col-md-3 pr-xs pl-xs">\
													<div class="form-group">\
														<label class="control-label">Quantity <span class="mandatoryfield">*</span></label>\
													</div>\
												</div>\
												<div class="col-md-2 pr-xs pl-xs">\
													<div class="form-group text-right">\
														<label class="control-label">Disc. (%)</label>\
													</div>\
												</div>\
											</div>\
											<div id="headingmultipleprice_'+pricescount+'_2" class="col-md-6 headingmultipleprice'+pricescount+'" style="display: none;">\
												<div class="col-md-4 pr-xs pl-xs">\
													<div class="form-group">\
														<label class="control-label">Price <span class="mandatoryfield">*</span></label>\
													</div>\
												</div>\
												<div class="col-md-3 pr-xs pl-xs">\
													<div class="form-group">\
														<label class="control-label">Quantity <span class="mandatoryfield">*</span></label>\
													</div>\
												</div>\
												<div class="col-md-2 pr-xs pl-xs">\
													<div class="form-group text-right">\
														<label class="control-label">Disc. (%)</label>\
													</div>\
												</div>\
											</div>\
										</div>\
										<div id="countmultipleprice_'+pricescount+'_1" class="col-md-6 countmultipleprice'+pricescount+'">\
											<div class="col-md-4 pr-xs pl-xs">\
												<div class="form-group" for="variantprice_'+pricescount+'_1" id="variantprice_div_'+pricescount+'_1">\
													<input type="text" id="variantprice_'+pricescount+'_1" onkeypress="return decimal(event,this.value)" class="form-control variantprices'+pricescount+'" name="variantprice['+pricescount+'][]" value="">\
												</div>\
											</div>\
											<div class="col-md-3 pr-xs pl-xs">\
												<div class="form-group" for="variantqty_'+pricescount+'_1" id="variantqty_div_'+pricescount+'_1">\
													<input type="text" id="variantqty_'+pricescount+'_1" onkeypress="return isNumber(event)" class="form-control variantqty'+pricescount+'" name="variantqty['+pricescount+'][]" value="" maxlength="4">\
												</div>\
											</div>\
											<div class="col-md-2 pr-xs pl-xs">\
												<div class="form-group text-right" for="variantdiscpercent_'+pricescount+'_1" id="variantdiscpercent_div_'+pricescount+'_1">\
													<input type="text" id="variantdiscpercent_'+pricescount+'_1" onkeypress="return decimal(event,this.value)" class="form-control text-right variantdiscpercent'+pricescount+'" name="variantdiscpercent['+pricescount+'][]" value="" onkeyup="return onlypercentage(this.id)">\
												</div>\
											</div>\
											<div class="col-md-3 mt-xs">\
												<button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice'+pricescount+'" onclick="removevariantprice('+pricescount+',1)" style="display:none;"><i class="fa fa-minus"></i></button>\
												<button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice'+pricescount+'" onclick="addnewvariantprice('+pricescount+')"><i class="fa fa-plus"></i></button>\
											</div>\
										</div>\
									</div>';


		newprice = '<div class="productvariantdiv border-panel" id="maindiv'+pricescount+'" style="position: relative;">\
						<input type="hidden" name="priceid['+pricescount+']" value="0">\
						<div class="row m-n" style="position: absolute;right: 0;">\
							<div class="pr-sm">\
								<button type="button" class="btn btn-raised btn-danger btn-sm pull-right" onclick="removemainprice('+"'maindiv"+pricescount+"'"+')"><i class="fa fa-remove"></i> REMOVE</button>\
							</div>\
						</div>\
						<div class="row m-n">\
                          <div class="col-md-2 pr-xs pl-xs">\
                            <div class="form-group" for="price" id="price_div'+pricescount+'">\
                                <label class="control-label" for="price'+pricescount+'">Price '+(parseInt(pricescount)+1)+'\
                               <span class="mandatoryfield"> * </span></label>\
                               <input type="text" id="price'+pricescount+'" onkeypress="return decimal(event,this.value)" class="form-control prices" placeholder="Price" name="price['+pricescount+']" value="">\
                            </div>\
                          </div>\
						  <div class="col-md-1 pr-xs pl-xs">\
                            <div class="form-group text-right" for="discount'+pricescount+'" id="discount_div'+pricescount+'">\
                                <label class="control-label" for="discount'+pricescount+'">Disc. (%)</label>\
                                <input type="text" id="discount'+pricescount+'" onkeypress="return decimal_number_validation(event,this.value)" class="form-control discount" name="discount['+pricescount+']" value="" onkeyup="return onlypercentage(this.id)">\
                            </div>\
                          </div>\
                          <div class="col-md-1 pr-xs pl-xs">\
                            <div class="form-group" for="stock" id="stock_div'+pricescount+'">\
                                <label class="control-label" for="stock'+pricescount+'">Stock\
                               <span class="mandatoryfield"> * </span></label>\
                               <input type="text" id="stock'+pricescount+'" onkeypress="return isNumber(event)" class="form-control stocks" placeholder="Stock" name="stock['+pricescount+']" value="">\
                            </div>\
						  </div>\
						  <div class="col-md-2 pr-xs pl-xs">\
							<div class="form-group" for="pointsforseller" id="pointsforseller_div'+pricescount+'">\
								<label class="control-label" for="pointsforseller'+pricescount+'">Points for Seller</label>\
								<input type="text" id="pointsforseller'+pricescount+'" onkeypress="return isNumber(event)" class="form-control pointsforseller" placeholder="" name="pointsforseller['+pricescount+']">\
							</div>\
						  </div>\
						  <div class="col-md-2 pr-xs pl-xs">\
							<div class="form-group" for="pointsforbuyer" id="pointsforbuyer_div'+pricescount+'">\
								<label class="control-label" for="pointsforbuyer'+pricescount+'">Points for Buyer</label>\
								<input type="text" id="pointsforbuyer'+pricescount+'" onkeypress="return isNumber(event)" class="form-control pointsforbuyer" placeholder="" name="pointsforbuyer['+pricescount+']">\
							</div>\
						  </div>\
						  <div class="col-md-2 pr-xs pl-xs">\
							<div class="form-group" for="sku" id="sku_div'+pricescount+'">\
								<label class="control-label" for="sku'+pricescount+'">SKU <span class="mandatoryfield"> * </span></label>\
								<input type="text" id="sku'+pricescount+'" class="form-control sku" name="sku['+pricescount+']">\
							</div>\
						  </div>\
						  <div class="col-md-2 pr-xs pl-xs">\
							<div class="form-group" for="minimumsalesprice" id="minimumsalesprice_div'+pricescount+'">\
								<label class="control-label" for="minimumsalesprice'+pricescount+'">Min. Sales Price</label>\
								<input type="text" id="minimumsalesprice'+pricescount+'" class="form-control minimumsalesprice" name="minimumsalesprice['+pricescount+']">\
							</div>\
						  </div>\
						 \
						</div>\
						<div class="row m-n">\
							<div class="col-md-2 pr-xs pl-xs">\
								<div class="form-group" id="minimumstocklimit_div'+pricescount+'">\
								<label class="control-label" for="minimumstocklimit'+pricescount+'">Min. Stock Limit</label>\
								<input type="text" id="minimumstocklimit'+pricescount+'" class="form-control minimumstocklimit" name="minimumstocklimit['+pricescount+']" value="" onkeypress="return isNumber(event)" maxlength="4">\
								</div>\
							</div>\
							<div class="col-md-1 pr-xs pl-xs">\
								<div class="form-group" id="weight_div'+pricescount+'">\
								<label class="control-label" for="weight'+pricescount+'">Weight (kg)</label>\
								<input type="text" id="weight'+pricescount+'" class="form-control weight" name="weight['+pricescount+']" value="" onkeypress="return decimal_number_validation(event,this.value,6,3)" >\
								</div>\
							</div>\
							<div class="col-md-3 pr-xs pl-xs">\
								<div class="col-md-6 p-n">\
									<div class="form-group pb-n" style="margin-top: 7px">\
										<div class="col-md-12 pr-xs pl-n">\
											<label class="control-label" for="minimumorderqty">Min. Order Qty</label>\
										</div>\
									</div>\
								</div>\
								<div class="col-md-6 p-n">\
									<div class="form-group pb-n" style="margin-top: 7px;">\
										<div class="col-md-12 pl-xs pr-n">\
											<label class="control-label" for="maximumorderqty">Max. Order Qty</label>\
										</div>\
									</div>\
								</div>\
								<div class="col-md-6 p-n">\
									<div class="form-group" id="minimumorderqty_div'+pricescount+'">\
										<div class="col-md-12 pr-xs pl-n">\
											<input type="text" id="minimumorderqty'+pricescount+'" class="form-control m-n" name="minimumorderqty['+pricescount+']" value="" onkeypress="return isNumber(event)" maxlength="4">\
										</div>\
									</div>\
								</div>\
								<div class="col-md-6 p-n">\
									<div class="form-group" id="maximumorderqty_div'+pricescount+'">\
										<div class="col-md-12 pl-xs pr-n">\
											<input type="text" id="maximumorderqty'+pricescount+'" class="form-control m-n" name="maximumorderqty['+pricescount+']" value="" onkeypress="return isNumber(event)" maxlength="4">\
										</div>\
									</div>\
								</div>\
                           	</div>\
							<div class="col-md-2 pr-xs pl-xs">\
								<div class="form-group" id="barcode_div'+pricescount+'">\
									<label class="control-label" for="barcode'+pricescount+'">Barcode <span class="mandatoryfield"> * </span></label>\
									<div class="col-md-12 p-n">\
										<div class="col-md-11 pl-n">\
											<input type="text" id="barcode'+pricescount+'" class="form-control barcode" name="barcode['+pricescount+']" value="" onkeypress="return alphanumeric(event)" maxlength="30">\
											<input type="hidden" id="oldbarcode'+pricescount+'" value="">\
										</div>\
										<div class="col-sm-1 p-n" style="padding-top: 5px !important;">\
											<a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised btn-sm" title="Generate Barcode" onclick="generateBarcode('+pricescount+')" style="padding: 9px 14px;"><i class="fa fa-refresh" aria-hidden="true"></i></a>\
										</div>\
									</div>\
								</div>\
							</div>\
							<div class="col-md-4 pr-xs pl-xs pt-sm">\
								<div class="form-group text-center" id="barcodeimage_div'+pricescount+'">\
									<label class="control-label"></label>\
									<div class="col-sm-12 pt-sm p-n">\
										<img id="barcodeimg'+pricescount+'" src="" style="max-width: 100%;">\
									</div>\
								</div>\
							</div>\
						</div>\
						\
						<div class="row m-n">\
							<div id="variant_div'+pricescount+'">\
								<input type="hidden" id="variant_count'+pricescount+'" name="variant_count['+pricescount+']" value="1">\
								<div class="col-md-6" id="variants'+pricescount+'0" style="padding: 0;">\
									<div class="col-md-5 pl-xs pr-xs">\
										<div class="form-group" id="attributediv'+pricescount+'0"><label class="control-label" for="attributeid'+pricescount+'0">Attribute <span class="mandatoryfield">*</span></label>\
											<select id="attributeid'+pricescount+'0" name="attributeid['+pricescount+'][0]" class="selectpicker form-control attributeids" div-id="'+pricescount+'" attribute-id="0" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8" onchange="loadvariant('+"'"+pricescount+"'"+','+"'"+0+"'"+')">\
												<option value="0">Select Attribute</option>\
											</select>\
										</div>\
									</div>\
									<div class="col-md-5 pl-xs pr-xs">\
										<div class="form-group" id="variantdiv'+pricescount+'0"><label class="control-label" for="variantid'+pricescount+'0">Variant <span class="mandatoryfield">*</span></label>\
											<select id="variantid'+pricescount+'0" name="variantid['+pricescount+'][0]" class="selectpicker form-control variantids" div-id="'+pricescount+'" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8">\
												<option value="0">Select Variant</option>\
											</select>\
										</div>\
									</div>\
									<div class="col-md-2" style="margin-top: 34px;padding: 0;">\
										<button type="button" class="btn btn-danger btn-raised btn-sm multi_variant_btn_variant" variant-div="'+pricescount+'" onclick="removevariant('+"'"+'variants'+pricescount+"0'"+','+pricescount+')" style="display:none;"><i class="fa fa-minus"></i></button>\
										<button type="button" class="btn btn-primary btn-raised btn-sm multi_variant_btn" onclick="addnewvariant('+"'"+pricescount+"'"+')"  variant-div="'+pricescount+'" style=""><i class="fa fa-plus"></i></button>\
									</div>\
								</div>\
							</div>\
                        </div>\
						\
						'+PRICE_TYPE_HTML+'\
						\
						'+MULTIPLE_PRICE_HTML+'\
						\
					</div>';
		
		$("#variant_div"+pricescount+" .multi_variant_btn_variant:first").show();
		$("#variant_div"+pricescount+" .multi_variant_btn:last").hide();

		$(".remove_variantprice"+pricescount+":first").show();
		$(".add_variantprice"+pricescount+":last").hide();
		
		$("#allprices_div").append(newprice);
		for(var i = 0; i < allattribute.length; i++) {
	        $("#attributeid"+pricescount+0).append($('<option>', { 
	          value: allattribute[i]['id'],
	          text : allattribute[i]['variantname']
	        }));
	      }
	    $("#variantid"+pricescount+0).selectpicker();
		$("#attributeid"+pricescount+0).selectpicker();
		$("#allprices_count").val(parseInt(pricescount)+1);
	});

	$(".multi_variant_btn").click(function(){
		variantdiv = $(this).attr("variant-div");
		newvariant = parseInt($("#variant_count"+variantdiv).val());
		variantdata = '<div class="col-md-6" id="variants'+variantdiv+newvariant+'" style="padding: 0;">\
								<div class="col-md-5 pl-xs pr-xs">\
									<div class="form-group" id="attributediv'+variantdiv+newvariant+'"><label class="control-label" for="attributeid'+variantdiv+newvariant+'">Attribute <span class="mandatoryfield">*</span></label>\
										<select id="attributeid'+variantdiv+newvariant+'" name="attributeid['+variantdiv+']['+newvariant+']" class="selectpicker form-control attributeids" div-id="'+variantdiv+'" attribute-id="'+newvariant+'" data-live-search="true" onchange="loadvariant('+"'"+variantdiv+"'"+','+"'"+newvariant+"'"+')" data-select-on-tab="true" data-size="5" tabindex="8">\
											<option value="0">Select Attribute</option>\
										</select>\
								</div>\
								</div>\
								<div class="col-md-5 pl-xs pr-xs">\
									<div class="form-group" id="variantdiv'+variantdiv+newvariant+'"><label class="control-label" for="variantid'+variantdiv+newvariant+'">Variant <span class="mandatoryfield">*</span></label>\
											<select id="variantid'+variantdiv+newvariant+'" name="variantid['+variantdiv+']['+newvariant+']" class="selectpicker form-control variantids" div-id="'+variantdiv+'" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8">\
												<option value="0">Select Variant</option>\
											</select>\
									</div>\
								</div>\
								<div class="col-md-2" style="margin-top: 34px;padding: 0;">\
									<button type="button" class="btn btn-danger btn-raised btn-sm multi_variant_btn_variant" variant-div="'+variantdiv+'" onclick="removevariant('+"'"+'variants'+variantdiv+newvariant+"'"+','+variantdiv+')" style=""><i class="fa fa-minus"></i></button>\
									<button type="button" class="btn btn-primary btn-raised btn-sm multi_variant_btn" variant-div="'+variantdiv+'" onclick="addnewvariant('+variantdiv+')" style=""><i class="fa fa-plus"></i></button>\
								</div>\
							</div>';
			
						$("#variant_div"+variantdiv+" .multi_variant_btn_variant:first").show();
						$("#variant_div"+variantdiv+" .multi_variant_btn:last").hide();

						$("#variant_div"+variantdiv).append(variantdata);
						$("#variant_count"+variantdiv).val(newvariant+1);
						for(var i = 0; i < allattribute.length; i++) {
			            $("#attributeid"+variantdiv+newvariant).append($('<option>', { 
			              value: allattribute[i]['id'],
			              text : allattribute[i]['variantname']
			            }));
			          }
			          $("#variantid"+variantdiv+newvariant).selectpicker();
					 $("#attributeid"+variantdiv+newvariant).selectpicker();
	});

});
$(document).on('blur','.barcode',function (e) {
	var divid = parseInt($(this).attr('id').match(/\d+/));
	if(this.value != ''){
	  var barcode = $('#barcode'+divid).val();
	  if($('#oldbarcode'+divid).val()!='' && barcode==$('#oldbarcode'+divid).val()){
		$('#barcodeimg'+divid).attr('src',SITE_URL+'product/set_barcode/'+barcode);
	  }else{
		verifyBarcode(divid);
	  }
	}else{
		new PNotify({title: 'Please enter or generate '+(parseInt(divid)+1)+' variant barcode !',styling: 'fontawesome',delay: '3000',type: 'error'});
		setTimeout(() => {
			$("#barcode_div"+divid).addClass("has-error is-focused");
		}, 100);
	}
});
$(document).on('change','.pricetype',function (e) {
	var divid = parseInt($(this).attr('id').match(/\d+/));
	if($(this).val()==0){
		$("#price_div"+divid+",#discount_div"+divid).parent().show();
		$("#multiplepricesection"+divid).hide();
	}else{
		$("#price_div"+divid+",#discount_div"+divid).parent().hide();
		$("#multiplepricesection"+divid).show();
	}
});
// <button type="button" class="btn btn-danger remove_product_btn" onclick="remove_product(2)"><i class="fa fa-remove"></i></button>

function loadvariant(divid,attributeid) {
	fill_variant_dropdown("#attributeid"+divid+attributeid,"#variantid"+divid+attributeid);
	// variantid01
}

function removevariant(variantsid,variantdivid) {
	$("#"+variantsid).remove();
	$("#variant_div"+variantdivid+" .multi_variant_btn:last").show();

	if ($("#variant_div"+variantdivid+" .multi_variant_btn_variant:visible").length == 1) {
		$("#variant_div"+variantdivid+" .multi_variant_btn_variant:first").hide();
	}		

}

/*$(".attributeids").change(function(){
		attributeid = $(this).attr("attribute-id");
		alert(attributeid);
		divid = $(this).attr("div-id");
		fill_variant_dropdown("#attributeid"+divid+attributeid,"#variantid"+divid+attributeid);
	})
*/

function addnewvariant(variantdiv) {
	
		newvariant = parseInt($("#variant_count"+variantdiv).val());
		variantdata = '<div class="col-md-6" id="variants'+variantdiv+newvariant+'" style="padding: 0;">\
					    <div class="col-md-5 pr-xs pl-xs">\
					      <div class="form-group" id="attributediv'+variantdiv+newvariant+'"><label class="control-label" for="attributeid'+variantdiv+newvariant+'">Attribute <span class="mandatoryfield">*</span></label>\
					        <select id="attributeid'+variantdiv+newvariant+'" name="attributeid['+variantdiv+']['+newvariant+']" class="selectpicker form-control attributeids" div-id="'+variantdiv+'" attribute-id="'+newvariant+'" data-live-search="true" onchange="loadvariant('+"'"+variantdiv+"'"+','+"'"+newvariant+"'"+')" data-select-on-tab="true" data-size="5" tabindex="8">\
					          <option value="0">Select Attribute</option>\
					        </select>\
					    </div>\
					    </div>\
					    <div class="col-md-5 pr-xs pl-xs">\
					      <div class="form-group" id="variantdiv'+variantdiv+newvariant+'"><label class="control-label" for="variantid'+variantdiv+newvariant+'">Variant <span class="mandatoryfield">*</span></label>\
					          <select id="variantid'+variantdiv+newvariant+'" name="variantid['+variantdiv+']['+newvariant+']" class="selectpicker form-control variantids" div-id="'+variantdiv+'" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8">\
					            <option value="0">Select Variant</option>\
					          </select>\
					      </div>\
					    </div>\
							<div class="col-md-2" style="margin-top: 34px;padding:0;">\
								<button type="button" class="btn btn-danger btn-raised btn-sm multi_variant_btn_variant" variant-div="'+variantdiv+'" onclick="removevariant('+"'"+'variants'+variantdiv+newvariant+"'"+','+variantdiv+')" style=""><i class="fa fa-minus"></i></button>\
								<button type="button" class="btn btn-primary btn-raised btn-sm multi_variant_btn" variant-div="'+variantdiv+'" onclick="addnewvariant('+variantdiv+')" style=""><i class="fa fa-plus"></i></button>\
					    </div>\
						</div>';
					
		$("#variant_div"+variantdiv+" .multi_variant_btn_variant:first").show();
		$("#variant_div"+variantdiv+" .multi_variant_btn:last").hide();

		$("#variant_div"+variantdiv).append(variantdata);
		$("#variant_count"+variantdiv).val(newvariant+1);
		for(var i = 0; i < allattribute.length; i++) {
            $("#attributeid"+variantdiv+newvariant).append($('<option>', { 
              value: allattribute[i]['id'],
              text : allattribute[i]['variantname']
            }));
          }
          $("#variantid"+variantdiv+newvariant).selectpicker();
		 $("#attributeid"+variantdiv+newvariant).selectpicker();
}

function removemainprice(mainpricediv) {
	$("#"+mainpricediv).remove();
}

function fill_variant_dropdown(attributedropdown,variantdropdown)
{
	 attributeid = $(attributedropdown).val();
      var uurl = SITE_URL+"product/getvariant";
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {attributeid:attributeid},
        dataType: 'json',
        async: false,
        success: function(response){
          $(variantdropdown)
          .find('option')
          .remove()
          .end()
          .append('<option value="0">Select Variant</option>')
          .val('whatever')
          ;

          for(var i = 0; i < response.length; i++) {

            $(variantdropdown).append($('<option>', { 
              value: response[i]['id'],
              text : response[i]['value']
            }));

          }
          // $('#product'+prow).val(areaid);
          $(variantdropdown).selectpicker('refresh');
        },
        error: function(xhr) {
              //alert(xhr.responseText);
            },
          });
  // alert(product_id);
}

function checkvalidation() {
	checkattribute = 1;
	checkvariant = 1;
	variantids_arr = [];
	allattribute_arr = [];
	attributeerror = "";
	varianterror = "";
	attributeids_arr = [];
	checkdivid = "0";
	allvariant_arr = [];
	
	var isvalidprice = isvalidstock = isvalidbarcode = isvaliduniquebarcode = isvalidsku = isvaliduniquesku = isvalidvariantprice = isvalidvariantqty = 1;
	
	$(".attributeids select").each(function() {
		if(checkdivid!=$(this).attr("div-id") && $(this).val()!=0){
			checkdivid = $(this).attr("div-id");
			allattribute_arr.push(attributeids_arr); 
			attributeids_arr = [];
		}
		if($(this).val()==0){
			attributeerror = "Please select all attributes";
			checkattribute = 0;
		}		
		if(jQuery.inArray($(this).val(), attributeids_arr) !== -1 && $(this).val()!=0){
			checkattribute = 0;
			new PNotify({title: 'Please select different attributes for price '+(parseInt($(this).attr("div-id"))+1)+'!',styling: 'fontawesome',delay: '3000',type: 'error'});
		}
		attval = $(this).val();
	    attributeids_arr.push(attval);
	});
	
	allattribute_arr.push(attributeids_arr);
	checkdivid="0";
	$(".variantids select").each(function() {
		if(checkdivid!=$(this).attr("div-id") && $(this).val()!=0){
			checkdivid = $(this).attr("div-id");
			allvariant_arr.push(variantids_arr); 
			variantids_arr = [];
		}
		if($(this).val()==0){
			varianterror = "Please select all variants";
			checkvariant = 0;
		}			
		if(jQuery.inArray($(this).val(), variantids_arr) !== -1 && $(this).val()!=0){
			checkvariant = 0;
			// new PNotify({title: 'Please select different variants for price '+(parseInt($(this).attr("div-id"))+1)+'!',styling: 'fontawesome',delay: '3000',type: 'error'});
		}
		varval = $(this).val();
	
		variantids_arr.push(varval);
	});
	allvariant_arr.push(variantids_arr);
	

	samevariant = 0;
	console.log(allvariant_arr);
	$.each(allvariant_arr, function( index, value ) {
		$.each(allvariant_arr, function( index1, value1 ) {
			if(index!=index1){
				if(arr_diff(value,value1).length==0){
					samevariant=1;
				}
			}
		})
	});
	if(samevariant==1){
		new PNotify({title: 'Please select different variants for all prices!',styling: 'fontawesome',delay: '3000',type: 'error'});
		return false;
	}
	$(".variantids select").each(function() {
		if($(this).val()==0){
			varianterror = "Please select all variants";
			checkvariant = 0;
		}
	    variantids_arr.push($(this).val());
	});
	if(attributeerror!=""){
		checkattribute=0;
		new PNotify({title: 'Please select all attributes !',styling: 'fontawesome',delay: '3000',type: 'error'});
	}
	if(varianterror!=""){
		checkvariant=0;
		new PNotify({title: 'Please select all variants !',styling: 'fontawesome',delay: '3000',type: 'error'});
	}
	
	var c=1;
    $('.productvariantdiv').each(function(){
		var divid = $(this).attr('id').match(/\d+/);

		if($("#singleqty"+divid).is(":checked")){
			if($("#price"+divid).val()==0){
				$("#price_div"+divid).addClass("has-error is-focused");
				new PNotify({title: 'Please enter '+(c)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
				isvalidprice = 0;
			}else{
				$("#price_div"+divid).removeClass("has-error is-focused");
			}
		}else{
			var firstRowId = parseInt($('.countmultipleprice'+divid+':first').attr('id').split("_")[2]);
			$('.countmultipleprice'+divid).each(function(index){
				var id = parseInt($(this).attr('id').split("_")[2]);
				var eleID = "_"+divid+"_"+id;

				var variantprice = $("#variantprice"+eleID).val();
				var variantqty = $("#variantqty"+eleID).val();
				
				alert(variantprice);

				if((variantprice!="" && variantprice!=0) || (variantqty!="" && variantqty!=0) || parseInt(id)==parseInt(firstRowId)){
					alert(firstRowId);
					if(variantprice==0){
						$("#variantprice_div"+eleID).addClass("has-error is-focused");
						new PNotify({title: 'Please enter '+(c)+' variant '+(index+1)+' price !',styling: 'fontawesome',delay: '3000',type: 'error'});
						isvalidvariantprice = 0;
					}else{
						$("#variantprice_div"+eleID).removeClass("has-error is-focused");
					}
					if(variantqty==0){
						$("#variantqty_div"+eleID).addClass("has-error is-focused");
						new PNotify({title: 'Please enter '+(c)+' variant '+(index+1)+' quantity !',styling: 'fontawesome',delay: '3000',type: 'error'});
						isvalidvariantqty = 0;
					}else{
						$("#variantqty_div"+eleID).removeClass("has-error is-focused");
					}
				}else{
					$("#variantprice_div"+eleID).removeClass("has-error is-focused");
					$("#variantqty_div"+eleID).removeClass("has-error is-focused");
				}

			});
		}
		if($("#stock"+divid).val()==0){
			$("#stock_div"+divid).addClass("has-error is-focused");
			new PNotify({title: 'Please enter '+(c)+' stock !',styling: 'fontawesome',delay: '3000',type: 'error'});
			isvalidstock = 0;
		}else{
			$("#stock_div"+divid).removeClass("has-error is-focused");
		}
		
		if($("#sku"+divid).val() == ""){
			$("#sku_div"+divid).addClass("has-error is-focused");
			new PNotify({title: 'Please enter '+(c)+' variant SKU !',styling: 'fontawesome',delay: '3000',type: 'error'});
			isvalidsku = 0;
		}else {
			$("#sku_div"+divid).removeClass("has-error is-focused");
		}

		if($("#barcode"+divid).val() == ""){
			$("#barcode_div"+divid).addClass("has-error is-focused");
			new PNotify({title: 'Please enter or generate '+(c)+' variant barcode !',styling: 'fontawesome',delay: '3000',type: 'error'});
			isvalidbarcode = 0;
		}else {
			$("#barcode_div"+divid).removeClass("has-error is-focused");
		}
		c++;
	});

	var input_barcode = $('.barcode');
	var values = [];
	for(j=0;j<input_barcode.length;j++) {
		var inputbarcode = input_barcode[j];
		var id = inputbarcode.id.match(/\d+/);
		
		if(inputbarcode.value!=''){
			if(values.indexOf(inputbarcode.value)>-1) {
				$("#barcode_div"+id).addClass("has-error is-focused");
				new PNotify({title: 'Please enter or generate unique barcode in '+(j+1)+' variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
				isvaliduniquebarcode = 0;
			}
			else{ 
				values.push(inputbarcode.value);
				$("#barcode_div"+id).removeClass("has-error is-focused");
			}
		}
	}
	var input_sku = $('.sku');
	var values = [];
	for(j=0;j<input_sku.length;j++) {
		var inputsku = input_sku[j];
		var id = inputsku.id.match(/\d+/);
		
		if(inputsku.value!=''){
			if(values.indexOf(inputsku.value)>-1) {
				$("#sku_div"+id).addClass("has-error is-focused");
				new PNotify({title: 'Please enter unique SKU in '+(j+1)+' variant !',styling: 'fontawesome',delay: '3000',type: 'error'});
				isvaliduniquesku = 0;
			}
			else{ 
				values.push(inputsku.value);
				$("#sku_div"+id).removeClass("has-error is-focused");
			}
		}
	}

	if(checkattribute==1 && checkvariant==1 && isvalidprice==1 && isvalidstock==1 && isvaliduniquebarcode == 1 && isvalidbarcode == 1 && isvalidsku == 1 && isvaliduniquesku == 1 && isvalidvariantprice == 1 && isvalidvariantqty == 1){

    var formData = new FormData($('#productvariantform')[0]);  
      var uurl = SITE_URL+"product/add-product-variant";
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
				new PNotify({title: "Product variant successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
				setTimeout(function() { window.location = SITE_URL+"product"; }, 1500);
			}else if(obj['error']==2){
				new PNotify({title: obj['index']+' product variant SKU already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
			}else{
				new PNotify({title: "Product variant not added !",styling: 'fontawesome',delay: '3000',type: 'error'});
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

function arr_diff (a1, a2) {

    var a = [], diff = [];

    for (var i = 0; i < a1.length; i++) {
        a[a1[i]] = true;
    }

    for (var i = 0; i < a2.length; i++) {
        if (a[a2[i]]) {
            delete a[a2[i]];
        } else {
            a[a2[i]] = true;
        }
    }

    for (var k in a) {
        diff.push(k);
    }

    return diff;
}

function generateBarcode(pricedivid){
	
	$("#barcode_div"+pricedivid).removeClass("has-error is-focused");
	var uurl = SITE_URL+"product/generateBarcode";
	$.ajax({
		url: uurl,
		type: 'POST',
		dataType: 'json',
		async: false,
		success: function(response){
  
		  $('#barcode'+pricedivid).val(response);
		  $('#barcodeimg'+pricedivid).attr('src',SITE_URL+'product/set_barcode/'+response);
		},
		error: function(xhr) {
		//alert(xhr.responseText);
		},
	}); 
}

function verifyBarcode(pricedivid){
  
	var barcode = $('#barcode'+pricedivid).val();
  
	var uurl = SITE_URL+"product/verifyBarcode";
	$.ajax({
		url: uurl,
		type: 'POST',
		data: {barcode: barcode},
		dataType: 'json',
		async: false,
		success: function(response){
		  if(response==1){
			$('#barcodeimg'+pricedivid).attr('src',SITE_URL+'product/set_barcode/'+barcode);
		  }else{
			setTimeout(() => {
				$('#barcode_div'+pricedivid).addClass("has-error is-focused");
			}, 100);
			new PNotify({title: 'Barcode already exist ! Please enter unique barcode.',styling: 'fontawesome',delay: '3000',type: 'error'});
		  }
		},
		error: function(xhr) {
		//alert(xhr.responseText);
		},
	}); 
}

function addnewvariantprice(MainID) {
	
	var SubID = parseInt($(".countmultipleprice"+MainID+":last").attr("id").split("_")[2])+1;

	var HTML = '<div id="countmultipleprice_'+MainID+'_'+SubID+'" class="col-md-6 countmultipleprice'+MainID+'">\
					<div class="col-md-4 pr-xs pl-xs">\
						<div class="form-group" for="variantprice_'+MainID+'_'+SubID+'" id="variantprice_div_'+MainID+'_'+SubID+'">\
							<input type="text" id="variantprice_'+MainID+'_'+SubID+'" onkeypress="return decimal(event,this.value)" class="form-control variantprices'+MainID+'" name="variantprice['+MainID+'][]" value="">\
						</div>\
					</div>\
					<div class="col-md-3 pr-xs pl-xs">\
						<div class="form-group" for="variantqty_'+MainID+'_'+SubID+'" id="variantqty_div_'+MainID+'_'+SubID+'">\
						<input type="text" id="variantqty_'+MainID+'_'+SubID+'" onkeypress="return isNumber(event)" class="form-control variantqty'+MainID+'" name="variantqty['+MainID+'][]" value="" maxlength="4">\
						</div>\
					</div>\
					<div class="col-md-2 pr-xs pl-xs">\
						<div class="form-group text-right" for="variantdiscpercent_'+MainID+'_'+SubID+'" id="variantdiscpercent_div_'+MainID+'_'+SubID+'">\
						<input type="text" id="variantdiscpercent_'+MainID+'_'+SubID+'" onkeypress="return decimal(event,this.value)" class="form-control text-right variantdiscpercent'+MainID+'" name="variantdiscpercent['+MainID+'][]" value="" onkeyup="return onlypercentage(this.id)">\
						</div>\
					</div>\
					<div class="col-md-3 mt-xs">\
						<button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice'+MainID+'" onclick="removevariantprice('+MainID+','+SubID+')" style=""><i class="fa fa-minus"></i></button>\
						<button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice'+MainID+'" onclick="addnewvariantprice('+MainID+')"><i class="fa fa-plus"></i></button>\
					</div>\
				</div>';


	$(".remove_variantprice"+MainID+":first").show();
    $(".add_variantprice"+MainID+":last").hide();
    $("#countmultipleprice_"+MainID+"_"+(SubID-1)).after(HTML);

	if($(".countmultipleprice"+MainID).length > 1){
		$("#headingmultipleprice_"+MainID+"_2").show();
	}
}

function removevariantprice(MainID,SubID) {

	$("#countmultipleprice_"+MainID+"_"+SubID).remove();

	$(".add_variantprice"+MainID+":last").show();
	if ($(".remove_variantprice"+MainID+":visible").length == 1) {
		$(".remove_variantprice"+MainID+":first").hide();
	}

	if($(".countmultipleprice"+MainID).length > 1){
		$("#headingmultipleprice_"+MainID+"_2").show();
	}else{
		$("#headingmultipleprice_"+MainID+"_2").hide();
	}
}
function onlypercentage(val){
    fieldval = $("#"+val).val();
    if (parseFloat(fieldval) < 0) $("#"+val).val(0);
    if (parseFloat(fieldval) > 100) $("#"+val).val(100);
}