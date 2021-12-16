<?php
$floatformat = '.';
$decimalformat = ',';
?>
<script>
	var HIDE_PURCHASE_EXTRA_CHARGES = 'style="<?=HIDE_PURCHASE_EXTRA_CHARGES==1?"display: none;":""?>"';
    var CURRENCY_CODE = '<?=CURRENCY_CODE?>';
    var VendorId = <?=isset($vendorid)?$vendorid:0;?>;
    var OrderId = '<?=isset($orderid)?$orderid:'';?>';
    
	var extrachargeoptionhtml = extrachargeOrderoptionhtml = "";
    <?php 
        foreach($extrachargesdata as $extracharges){ ?>
        extrachargeoptionhtml+='<option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>"><?php echo $extracharges['extrachargename']; ?></option>';
    <?php } ?>
    <?php  
        foreach($extrachargesdataForOrder as $extrachar){ ?>
        extrachargeOrderoptionhtml+='<option data-tax="<?php echo $extrachar['tax']; ?>" data-type="<?php echo $extrachar['amounttype']; ?>" data-amount="<?php echo $extrachar['defaultamount']; ?>" value="<?php echo $extrachar['extrachargesid']; ?>"><?php echo $extrachar['extrachargesname']; ?></option>';
    <?php } ?>
    // For Edit Time  //Use In Js
    var extrach = <?php echo json_encode($extrachargesdataForOrder); ?>;
</script>
<style>
    .orderamounttable td, .orderamounttable th
    {
        padding:5px 5px !important;
    }
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($grndata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname');?></h1>
        <small>
          	<ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?php echo $this->session->userdata(base_url().'mainmenuname'); ?></a></li>
            <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?php echo $this->session->userdata(base_url().'submenuname'); ?></a></li>
            <li class="active"><?php if(isset($grndata)){ echo 'Edit'; }else{ echo 'Add'; } ?> Purchase <?php echo $this->session->userdata(base_url().'submenuname'); ?></li>
          	</ol>
        </small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body pt-n">
		        	<div class="col-md-12">
						<form class="form-horizontal" id="grnform" name="grnform">
							<input type="hidden" id="grnid" name="grnid" value="<?php if(isset($grndata)){ echo $grndata['id']; } ?>">
							<input type="hidden" id="oldorderid" name="oldorderid" value="<?php if(isset($orderid)){ echo $orderid; } ?>">
							<input type="hidden" id="oldvendorid" name="oldvendorid" value="<?php if(isset($vendorid)){ echo $vendorid; } ?>">

							<div class="row mb-xs">
								<div class="col-md-12 p-n">
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group" id="vendor_div">
												<div class="col-sm-12 pr-sm">
													<label for="vendorid" class="control-label">Select Vendor <span class="mandatoryfield">*</span></label>
													<select id="vendorid" name="vendorid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="7" <?php if(isset($vendorid)){ echo "disabled"; } ?>>
														<option value="0">Select Vendor</option>
														<?php foreach($vendordata as $vendor){ ?>
															<option value="<?php echo $vendor['id']; ?>" <?php if(isset($vendorid) && $vendorid==$vendor['id']){ echo "selected"; } ?>><?php echo ucwords($vendor['name']); ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group" id="orderid_div">
												<div class="col-sm-12 pl-sm pr-sm">
													<label for="orderid" class="control-label">Select Purchase Order <span class="mandatoryfield">*</span></label>
													<select id="orderid" name="orderid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="7" title="Select Purchase Order" data-live-search="true" data-max-options="5" multiple>
													</select>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group" id="receiveddate_div">
												<div class="col-md-12 pl-xs">
													<label for="receiveddate" class="control-label">Received Date <span class="mandatoryfield">*</span></label>
													<div class="input-group">
														<input id="receiveddate" type="text" name="receiveddate" value="<?php if(isset($grndata)){ echo $this->general_model->displaydate($grndata['receiveddate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" class="form-control" readonly>
														<span class="btn btn-default" title='Date' style="position: absolute;top: 7px !important;right: 0px;"><i class="fa fa-calendar fa-lg"></i></span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group" id="grnno_div">
												<div class="col-sm-12 pr-xs">
													<label for="grnno" class="control-label">GRN No. <span class="mandatoryfield">*</span></label>
													<input id="grnno" type="text" name="grnno" class="form-control" value="<?php if(!empty($grndata)){ echo $grndata['grnnumber']; }else if(!empty($grnno)){ echo $grnno; } ?>" readonly>
													<input id="grnnumber" type="hidden" value="<?php if(!empty($grndata)){ echo $grndata['grnnumber']; }else if(!empty($grnno)){ echo $grnno; } ?>">
													<div class="checkbox">
														<input id="editgrnnumber" type="checkbox" value="1" name="editgrnnumber" class="checkradios">
														<label for="editgrnnumber">Edit GRN No.</label>
													</div>
												</div>
											</div>
										</div>
										<div class="col-sm-8">
											<div class="form-group" id="remarks_div">
												<div class="col-sm-12 pl-sm">
													<label for="remarks" class="control-label">Remarks</label>
													<textarea rows="1" id="remarks" name="remarks" class="form-control"><?php if(isset($grndata)){ echo $grndata['remarks']; } ?></textarea>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12 p-n">
									<div class="panel">
										<div class="panel-heading">
											<h4 class="text-center">Product Details</h4>
											<hr>
										</div>
										<div class="panel-body no-padding">
											<div class="table-responsive">
												<table id="orderproducttable" class="table table-hover table-bordered m-n">
													<thead>
														<tr>
															<th rowspan="2" class="width5">Sr. No.</th>
															<th rowspan="2">Product Name</th>
															<th rowspan="2" class="width12">Qty.</th>
															<th rowspan="2" class="text-right">Rate (Excl. Tax)</th>
															<th class="text-right width8 disccol">Dis.(%)</th>
															<th class="text-right width8 sgstcol">SGST (%)</th>
															<th class="text-right width8 cgstcol">CGST (%)</th>
															<th class="text-right width8 igstcol">IGST (%)</th>
															<th rowspan="2" class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
														</tr>
														<tr>
															<th class="text-right width8 disccol">Amt. (<?=CURRENCY_CODE?>)</th>
															<th class="text-right width8 sgstcol">Amt. (<?=CURRENCY_CODE?>)</th>
															<th class="text-right width8 cgstcol">Amt. (<?=CURRENCY_CODE?>)</th>
															<th class="text-right width8 igstcol">Amt. (<?=CURRENCY_CODE?>)</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td colspan="16" class="text-center">No data available in table.</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row form-group" id="AmountTotal">
								<div class="col-md-8 p-n" style="<?=HIDE_PURCHASE_EXTRA_CHARGES==1?"display: none;":""?>">
									<div id="extracharges_div"></div>
									<div class="panel countorders" id="0">
										<div class="panel-heading">
											<h2 style="width: 35%;">Other Charges</h2>                                       
										</div>
										<div class="panel-body no-padding">                                                 
											<div class="row m-n">     
											<?php /* if(isset($grndata) && isset($grnExtraChargesdata) && !empty($grnExtraChargesdata)){
												for($i=0;$i<count($grnExtraChargesdata);$i++) { ?>
                                                    
													<div class="col-md-6 p-n countcharges0" id="countcharges_0_<?=$i+1?>">   
														<div class="col-md-6 pr-xs">
															<div class="form-group p-n" id="extracharges_0_<?=$i+1?>_div">
																<div class="col-md-12">
																	<select id="orderextrachargesid_0_<?=$i+1?>" name="orderextrachargesid[0][]" class="selectpicker orderextrachargesid form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
																		<option value="0">Select Extra Charges</option>
																		<?php if(!empty($extrachargesdata)){ 
																			foreach($extrachargesdata as $extracharges){ ?>
																				<option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>"><?php echo $extracharges['extrachargename']; ?></option>
																		<?php }
																		}?>
																	</select>
																	<input type="hidden" name="orderextrachargesmappingid[0][]" id="orderextrachargesmappingid_0_<?=$i+1?>" class="orderextrachargesmappingid" value="<?=$grnExtraChargesdata[$i]['id']?>">
																	<input type="hidden" name="orderextrachargestax[0][]" id="orderextrachargestax_0_<?=$i+1?>" class="orderextrachargestax" value="<?=$grnExtraChargesdata[$i]['taxamount']?>">
																	<input type="hidden" name="orderextrachargesname[0][]" id="orderextrachargesname_0_<?=$i+1?>" class="orderextrachargesname" value="<?=$grnExtraChargesdata[$i]['extrachargesname']?>">
																	<input type="hidden" name="orderextrachargepercentage[0][]" id="orderextrachargepercentage_0_<?=$i+1?>" class="orderextrachargepercentage" value="<?=$grnExtraChargesdata[$i]['extrachargepercentage']?>">
																</div>
															</div>
														</div>
														
														<div class="col-md-3 pl-xs pr-xs">
															<div class="form-group p-n" id="orderextrachargeamount_0_<?=$i+1?>_div">
																<div class="col-md-12">
																	<input type="text" id="orderextrachargeamount_0_<?=$i+1?>" name="orderextrachargeamount[0][]" value="<?=$grnExtraChargesdata[$i]['amount']?>" class="form-control text-right orderextrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)">
																</div>
															</div>
														</div>
														<div class="col-md-3 text-right pt-md">
														<?php if($i==0){?>
															<?php if(count($grnExtraChargesdata)>1){ ?>
																<button type="button" class="btn btn-default btn-raised remove_charges_btn m-n" onclick="removecharge(0,1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
															<?php }else { ?>
																<button type="button" class="btn btn-default btn-raised add_charges_btn m-n" onclick="addnewcharge()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
															<?php } ?>

														<?php }else if($i!=0) { ?>
															<button type="button" class="btn btn-default btn-raised remove_charges_btn m-n" onclick="removecharge(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
														<?php } ?>
															<button type="button" class="btn btn-default btn-raised remove_charges_btn m-n" onclick="removecharge(0,<?=$i+1?>)" style="padding: 3px 8px;display: none;"><i class="fa fa-minus"></i></button>
															<button type="button" class="btn btn-default btn-raised add_charges_btn m-n" onclick="addnewcharge()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
														</div>
													</div>
													<script type="text/javascript">
													$(document).ready(function() {
														
														$("#orderextrachargesid_0_<?=$i+1?>").val(<?=$grnExtraChargesdata[$i]['extrachargesid']?>);
														$("#orderextrachargesid_0_<?=$i+1?> option:not(:selected)").remove();
														$("#orderextrachargesid_0_<?=$i+1?>").selectpicker('refresh');
													});
													</script>
											<?php } }else{ */ ?>
												<div class="col-md-6 p-n countcharges0" id="countcharges_0_1" >   
													<div class="col-sm-6 pr-xs">
														<div class="form-group p-n" id="extracharges_0_1_div">
															<div class="col-sm-12">
																<select id="orderextrachargesid_0_1" name="orderextrachargesid[0][]" class="selectpicker form-control orderextrachargesid" data-live-search="true" data-select-on-tab="true" data-size="5">
																	<option value="0">Select Extra Charges</option>
																	<?php if(!empty($extrachargesdata)){ 
																		foreach($extrachargesdata as $extracharges){ ?>
																			<option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>"><?php echo $extracharges['extrachargename']; ?></option>
																	<?php }
																	}?>
																</select>

																<input type="hidden" name="orderextrachargestax[0][]" id="orderextrachargestax_0_1" class="orderextrachargestax" value="">
																<input type="hidden" name="orderextrachargesname[0][]" id="orderextrachargesname_0_1" class="orderextrachargesname" value="">
																<input type="hidden" name="orderextrachargepercentage[0][]" id="orderextrachargepercentage_0_1" class="orderextrachargepercentage" value="">
															</div>
														</div>
													</div>
													
													<div class="col-sm-3 pl-xs pr-xs">
														<div class="form-group p-n" id="orderextrachargeamount_0_1_div">
															<div class="col-sm-12">
																<input type="text" id="orderextrachargeamount_0_1" name="orderextrachargeamount[0][]" class="form-control text-right orderextrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)">
															</div>
														</div>
													</div>
													<div class="col-md-3 text-right pt-md">
														<button type="button" class="btn btn-default btn-raised  remove_charges_btn m-n" onclick="removecharge(0,1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>

														<button type="button" class="btn btn-default btn-raised  add_charges_btn m-n" onclick="addnewcharge()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
													</div>
												</div>
											<?php //} ?>
											</div>
											<input type="hidden" name="ordergrossamount[]" id="ordergrossamount_0" class="ordergrossamount" value="">                                              
											<input type="hidden" name="invoiceorderamount[]" id="invoiceorderamount_0" class="invoiceorderamount" value="">
										</div>
									</div>
								</div>
								<div class="col-md-4 pull-right pr-n">
									<div class="col-md-12 pull-right p-n mb-md">
										<div class="panel-body no-padding">
											<table class="table table-hover table-bordered m-n invoice" style="color: #000" width="100%">
												<thead>
													<tr>
														<th class="text-center">GST Summary</th>
														<th class="text-center" width="25%">Assessable Amount (<?=CURRENCY_CODE?>)</th>
														<th class="text-center" width="25%">GST Amount (<?=CURRENCY_CODE?>)</th>
													</tr>
												</thead>
												<tbody>
												<tbody>
													<tr>
														<th>Product Total</th>
														<td class="text-right"><span id="producttotal" name="totalamount">0.00</span>
														<input type="hidden" id="inputproducttotal" name="inputproducttotal" value=""></td>
														<td class="text-right"><span id="gsttotal">0.00</span>
														<input type="hidden" id="inputgsttotal" name="inputgsttotal" value=""></td>
													</tr>
													<tr style="<?=HIDE_PURCHASE_EXTRA_CHARGES==1?"display: none;":""?>">
														<th>Extra Charges Total</th>
														<td class="text-right"><span id="chargestotalassesbaleamount">0.00</span></td>
														<td class="text-right"><span id="chargestotalgstamount">0.00</span></td>
													</tr>
													<tr>
														<td></td>
														<th class="text-right pr-md"><span id="producttotalassesbaleamount">0.00</span></th>
														<th class="text-right pr-md"><span id="producttotalgstamount">0.00</span></th>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
									<div class="col-md-12 pull-right p-n">
										<div class="panel-body no-padding">
											<table class="table table-hover table-bordered m-n invoice" style="color: #000" width="100%">
												<thead>
													<tr>
														<th colspan="2" class="text-center">Goods Received Notes Summary (<?=CURRENCY_CODE?>)</th>
													</tr>
												</thead>
												<tbody>
													<tr id="totalproductrow">
														<td>Product Total (<?=CURRENCY_CODE?>)</td>
														<td class="text-right" width="25%"><span id="grossamount" name="grossamount">0.00</span>
														<input type="hidden" id="inputgrossamount" name="inputgrossamount" value="">
														</td>
													</tr>
													<tr class="tr_extracharges" id="default"></tr>
													<tr>
														<td>Round Off</td>
														<td class="text-right"><span id="roundoff">0.00</span></td>
													</tr>
													<tr>
														<td><b>Amount Payable (<?=CURRENCY_CODE?>)</b></td>
														<td class="text-right"><b><span id="totalpayableamount" name="totalpayableamount">0.00</span></b>
														<input type="hidden" id="inputtotalpayableamount" name="inputtotalpayableamount" value=""></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-12 p-n" id="orderamountdiv"></div>
							</div>
							<div class="row">
								<div class="col-md-12 mt-xl pt text-center">
									<?php if(!empty($grndata)){ ?>
										<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
										<a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation('print')">UPDATE & PRINT</a>
									<?php }else{ ?>
										<a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation()">SAVE & ADD NEW</a>
										<a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation('print')">SAVE & PRINT</a>
									<?php } ?>
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
									<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL."goods-received-notes"?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
								</div>
							</div>
						</form>
					</div>
				</div>
		      </div>
		    </div>
		  </div>
		</div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->

<script>
function calculateorderamount(OrderID){
  var orderqty = grnqty = 0;
  $(".orderquantity"+OrderID).each(function( index ) {
    var orderproductid = $(this).attr("id").match(/(\d+)/g);
    if($(this).val()!=""){
      orderqty += parseFloat($(this).val());
    }
    if($("#quantity"+orderproductid).val()!=""){
      grnqty += parseFloat($("#quantity"+orderproductid).val());
    }
  });
  var ordergrossamount = (parseFloat(grnqty) * parseFloat($("#ordergrossamount_"+OrderID).val()) / parseFloat(orderqty));
  $("#invoiceorderamount_"+OrderID).val(parseFloat(ordergrossamount).toFixed(2));
  $("#displayproducttotal"+OrderID).html(parseFloat(ordergrossamount).toFixed(2));
  changeextrachargesamount();
}
function changechargespercentage(orderid,divid){
  var type = $("#orderextrachargesid_"+orderid+"_"+divid+" option:selected").attr("data-type");
  var optiontext = $("#orderextrachargesid_"+orderid+"_"+divid+" option:selected").text();
  var grossamount = $("#invoiceorderamount_"+orderid).val();
  var amount = $("#orderextrachargeamount_"+orderid+"_"+divid).val();
  var chargespercent = 0;
  var inputgrossamount = $("#inputgrossamount").val();

  if(orderid==0){
    grossamount = parseFloat(inputgrossamount);
  }
  if(type==0){
    if(parseFloat(amount)> 0){
      chargespercent = parseFloat(amount) * 100 / parseFloat(grossamount);
    }
    optiontext = optiontext.split("(");
    $("#orderextrachargesid_"+orderid+"_"+divid+" option:selected").text(optiontext[0].trim()+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
    $("#orderextrachargesid_"+orderid+"_"+divid).selectpicker("refresh");
    $("#orderextrachargesname_"+orderid+"_"+divid).val(optiontext[0].trim()+" ("+parseFloat(chargespercent).toFixed(2)+"%)");
  }
}
function totalproductamount(orderid,divid) {
  var quantity = $("#quantity"+divid).val();
  var taxtype = $("#taxtype"+divid).val();
  var tax = $("#tax"+divid).val();
  var price = $("#price"+divid).val();
  var actualprice = $("#actualprice"+divid).val();
  var discount = $("#discount"+divid).val();
  
  var discountamount = ((parseFloat(actualprice) * parseFloat(quantity)) * parseFloat(discount) / 100);
  var totalprice = (parseFloat(price) * parseFloat(quantity));
  var taxvalue = parseFloat(parseFloat(price) * parseFloat(quantity) * parseFloat(tax) / 100);
  var total = parseFloat(totalprice) + parseFloat(taxvalue);
  
  if(taxtype==1){
    $("#sgst"+divid).html(parseFloat(taxvalue/2).toFixed(2));
    $("#cgst"+divid).html(parseFloat(taxvalue/2).toFixed(2));
  }else{
    $("#igst"+divid).html(parseFloat(taxvalue).toFixed(2));
  }
  $("#discountamount"+divid).html(parseFloat(discountamount).toFixed(2));
  $("#productnetprice"+divid).html(parseFloat(total).toFixed(2));
  $("#taxvalue"+divid).val(parseFloat(taxvalue).toFixed(2));
  $("#producttotal"+divid).val(parseFloat(parseFloat(totalprice)).toFixed(2));
  calculateorderamount(orderid);
  changeextrachargesamount();
  overallextracharges();
  netamounttotal();
}
function changeextrachargesamount(){
 
  $(".orderextrachargeamount").each(function( index ) {
    var element = $(this).attr("id").split('_');
    var orderid = element[1];
    var divid = element[2];
    calculateextracharges(orderid,divid);
  });
}
function calculateextracharges(orderid,rowid){
  var extracharges = $("#orderextrachargesid_"+orderid+"_"+rowid).val();
  var type = $("#orderextrachargesid_"+orderid+"_"+rowid+" option:selected").attr("data-type");
  var amount = $("#orderextrachargesid_"+orderid+"_"+rowid+" option:selected").attr("data-amount");
  var tax = $("#orderextrachargesid_"+orderid+"_"+rowid+" option:selected").attr("data-tax");

  var totalgrossamount = $("#invoiceorderamount_"+orderid).val();
  var inputgrossamount = $("#inputgrossamount").val();
                    
  if(orderid==0){
    totalgrossamount = parseFloat(inputgrossamount);
  }
  /* var discount = $("#discountamount").html();
  var couponamount = $("#coupondiscountamount").html(); */
  
  var chargesamount = chargestaxamount = 0;
  if(parseFloat(totalgrossamount)>0 && parseFloat(extracharges) > 0){
      if(type==0){
          chargesamount = parseFloat(totalgrossamount) * parseFloat(amount) / 100;
      }else{
          chargesamount = parseFloat(amount);
      }
      
      chargestaxamount = parseFloat(chargesamount) * parseFloat(tax) / (100+parseFloat(tax));
      
      $("#orderextrachargestax_"+orderid+"_"+rowid).val(parseFloat(chargestaxamount).toFixed(2));
      $("#orderextrachargeamount_"+orderid+"_"+rowid).val(parseFloat(chargesamount).toFixed(2));
  }else{
      $("#orderextrachargestax_"+orderid+"_"+rowid).val(parseFloat(0).toFixed(2));
      $("#orderextrachargeamount_"+orderid+"_"+rowid).val(parseFloat(0).toFixed(2));
  }
  var chargesname = $("#orderextrachargesid_"+orderid+"_"+rowid+" option:selected").text();
  $("#orderextrachargesname_"+orderid+"_"+rowid).val(chargesname.trim());
  var chargespercent = 0;
  if(type==0){
      chargespercent = parseFloat(amount);
  }
  $("#orderextrachargepercentage_"+orderid+"_"+rowid).val(parseFloat(chargespercent).toFixed(2));
  netamounttotal();
}
function overallextracharges(){
  
  /********* CALCULATE EXTRA CHARGES START *********/
  var extrachargesrow = '';
  var CHARGES_ARR = [];
  var extrachargesamnt = [];
  $(".tr_extracharges").remove();
  $("select.orderextrachargesid").each(function( index ) {
    var element = $(this).attr("id").split('_');
    var orderid = element[1];
    var divid = element[2];
    var extrachargesname = $("#orderextrachargesname_"+orderid+"_"+divid).val();
    var extrachargeamount = $("#orderextrachargeamount_"+orderid+"_"+divid).val();
    var extrachargestax = $("#orderextrachargestax_"+orderid+"_"+divid).val();
    var extrachargepercentage = $("#orderextrachargepercentage_"+orderid+"_"+divid).val();
    var extrachargesdatatype = $("#orderextrachargesid_"+orderid+"_"+divid+" option:selected").attr("data-type");
    var extrachargesid = $(this).val();

    extrachargeamount = (parseFloat(extrachargeamount)>0)?parseFloat(extrachargeamount):0;

    if(extrachargesid!=0){

      if(!CHARGES_ARR.includes(extrachargesid)){

        extrachargesrow += "<tr class='tr_extracharges' id='tr_extracharges_"+extrachargesid+"'>";
        extrachargesrow += "<td>"+extrachargesname+"</td>";
        extrachargesrow += "<td class='text-right'><span id='extrachargeamount"+extrachargesid+"'>"+parseFloat(extrachargeamount).toFixed(2)+"</span>";
        
        extrachargesrow += '<input type="hidden" name="extrachargesid[]" id="extrachargesid'+extrachargesid+'" value="'+extrachargesid+'">';

        extrachargesrow += '<input type="hidden" name="extrachargeamount[]" id="inputextrachargeamount'+extrachargesid+'" value="'+parseFloat(extrachargeamount).toFixed(2)+'">';

        extrachargesrow += '<input type="hidden" id="extrachargestax'+extrachargesid+'" name="extrachargestax[]" value="'+parseFloat(extrachargestax).toFixed(2)+'">';
        
        extrachargesrow += '<input type="hidden" name="extrachargesname[]" id="extrachargesname'+extrachargesid+'" value="'+extrachargesname+'">';

        extrachargesrow += '<input type="hidden" name="extrachargepercentage[]" id="extrachargepercentage'+extrachargesid+'" value="'+parseFloat(extrachargepercentage).toFixed(2)+'">';

        extrachargesrow += '<input type="hidden" name="extrachargesdatatype[]" id="extrachargesdatatype'+extrachargesid+'" value="'+parseInt(extrachargesdatatype)+'">';

        extrachargesrow += "</td>";
        extrachargesrow += "</tr>";

        CHARGES_ARR.push(extrachargesid);
        
      }else{

        var sumamount = sumtax = type = 0;
        $("select.orderextrachargesid").each(function( index ) {
          var elementid = $(this).attr("id").split('_');
          var OrderId = elementid[1];
          var Id = elementid[2];
          var thisid = $(this).val();
          var sumchargeamount = $("#orderextrachargeamount_"+OrderId+"_"+Id).val();
          var sumchargetax = $("#orderextrachargestax_"+OrderId+"_"+Id).val();
          var thisid = $(this).val();
          var thistype = $("#orderextrachargesid_"+OrderId+"_"+Id+" option:selected").attr("data-type");
          sumchargeamount = (parseFloat(sumchargeamount)>0)?parseFloat(sumchargeamount):0;
          sumchargetax = (parseFloat(sumchargetax)>0)?parseFloat(sumchargetax):0;

          if(thisid == extrachargesid){
            sumamount += parseFloat(sumchargeamount);
            sumtax += parseFloat(sumchargetax);
            type = thistype;
          }
        });
        extrachargesamnt.push(extrachargesid+'_'+parseFloat(sumamount).toFixed(2)+'_'+parseFloat(sumtax).toFixed(2)+'_'+type);
      }
    }
  });
  
  $("#totalproductrow").after(extrachargesrow);
  var inputgrossamount = $("#inputgrossamount").val();
  if(extrachargesamnt.length > 0){
    for(var i=0; i<extrachargesamnt.length; i++){

      var id = extrachargesamnt[i].split('_');
      var chargesid = id[0];
      var amount = id[1];
      var tax = id[2];
      var type = id[3];
      var chargespercent = 0;
      if(type==0){
        if(parseFloat(amount)> 0){
          chargespercent = parseFloat(amount) * 100 / parseFloat(inputgrossamount);
        }
        var optiontext = $("#extrachargesname"+chargesid).val();
        
        optiontext = optiontext.split("(");
        optiontext = optiontext[0].trim()+" ("+parseFloat(chargespercent).toFixed(2)+"%)";
        $("#tr_extracharges_"+chargesid+" td:first").text(optiontext);
        $("#extrachargesname"+chargesid).val(optiontext);
      }
      
      $("#extrachargeamount"+chargesid).html(parseFloat(amount).toFixed(2));
      $("#inputextrachargeamount"+chargesid).val(parseFloat(amount).toFixed(2));
      $("#extrachargestax"+chargesid).val(parseFloat(tax).toFixed(2));
      $("#extrachargesdatatype"+chargesid).val(parseInt(type));
      $("#extrachargepercentage"+chargesid).val(parseFloat(chargespercent).toFixed(2));
    }
  }
  /********* CALCULATE EXTRA CHARGES END *********/
}
function netamounttotal() {
  var producttotal = productgstamount = grossamount = extrachargesamount = extrachargestax = chargesassesbaleamount = 0;
  
  $(".producttotal").each(function( index ) {
    var divid = $(this).attr("id").match(/(\d+)/g);
    if($(this).val()!="" && $("#quantity"+divid).val() >0 ){
      producttotal += parseFloat($(this).val());
    }
  });
  $(".taxvalue").each(function( index ) {
    var divid = $(this).attr("id").match(/(\d+)/g);
    if($(this).val()!="" && $("#quantity"+divid).val() >0 ){
      productgstamount += parseFloat($(this).val());
    }
  });
  $("#producttotal").html(parseFloat(producttotal).toFixed(2));
  $("#inputproducttotal").val(parseFloat(producttotal).toFixed(2));
  $("#gsttotal").html(parseFloat(productgstamount).toFixed(2));
  $("#inputgsttotal").val(parseFloat(productgstamount).toFixed(2));

  if($("select.orderextrachargesid").length > 0){
    $(".tr_extracharges").each(function( index ) {
      if($(this).attr("id")!="default"){
        var orderid = $(this).attr("id").match(/(\d+)/g);
        var exchrgamnt = $("#extrachargeamount"+orderid).html();
        var exchrgtax = $("#extrachargestax"+orderid).val();
        if(parseFloat(exchrgamnt) > 0){
          extrachargesamount += parseFloat(exchrgamnt);
          extrachargestax += parseFloat(exchrgtax);
        }
      }
    });
  }
  chargesassesbaleamount = parseFloat(extrachargesamount) - parseFloat(extrachargestax);
  var producttotalassesbaleamount = parseFloat(producttotal) + parseFloat(chargesassesbaleamount);
  var producttotalgstamount = parseFloat(productgstamount) + parseFloat(extrachargestax);

  $("#chargestotalassesbaleamount").html(format.format(parseFloat(chargesassesbaleamount).toFixed(2)));
  $("#chargestotalgstamount").html(format.format(parseFloat(extrachargestax).toFixed(2)));
  $("#producttotalassesbaleamount").html(format.format(parseFloat(producttotalassesbaleamount).toFixed(2)));
  $("#producttotalgstamount").html(format.format(parseFloat(producttotalgstamount).toFixed(2)));

  grossamount = parseFloat(producttotal) + parseFloat(productgstamount);
  $("#grossamount").html(parseFloat(grossamount).toFixed(2));
  $("#inputgrossamount").val(parseFloat(grossamount).toFixed(2));
  
  var finalamount = parseFloat(grossamount) + parseFloat(extrachargesamount);

  if(finalamount<0){
      finalamount=0;
  }
  var roundoff =  Math.round(parseFloat(finalamount).toFixed(2))-parseFloat(finalamount);
  finalamount =  Math.round(parseFloat(finalamount).toFixed(2));
  $("#roundoff").html(format.format(roundoff));
  $("#totalpayableamount").html(format.format(finalamount));
  $("#inputtotalpayableamount").val(parseFloat(finalamount).toFixed(2));
}
</script>