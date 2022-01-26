<script>
   var partialpayment = '<?php if(!empty($channelsetting)){ echo $channelsetting['partialpayment']; } ?>';
   var addressid = <?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['addressid']; }else{ echo "0"; } ?>;
   
   var shippingaddressid = <?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['shippingaddressid']; }else{ echo "0"; } ?>;
   var ISDUPLICATE = <?php if(isset($isduplicate) && $isduplicate==1){ echo '1'; }else{ echo "0"; } ?>;
   
   oldproductid = [];
   oldpriceid = [];
   oldtax = [];
   productdiscount = [];
   oldcombopriceid = [];
   oldprice = [];
   var EMIreceived = 0;
   var productoptionhtml = "";
   var salesproducthtml = "";
   
   var PRODUCT_DISCOUNT = '<?=PRODUCTDISCOUNT?>';
   var DEFAULT_COUNTRY_ID = '<?=DEFAULT_COUNTRY_ID?>';
   
   var GSTonDiscount = '<?php //if(isset($gstondiscount)){ echo $gstondiscount; } ?>';
   var globaldicountper = '<?php //if(isset($globaldiscountper)){ echo $globaldiscountper; } ?>';
   var globaldicountamount = '<?php //if(isset($globaldiscountamount)){ echo $globaldiscountamount; } ?>';
   var discountminamount = '<?php //if(isset($discountonbillminamount)){ echo $discountonbillminamount; }else{ echo -1; } ?>';
   
   var extrachargeoptionhtml = "";

   var EDITTAXRATE_SYSTEM = '<?=EDITTAXRATE?>';
   var EDITTAXRATE_CHANNEL = '<?php if(!empty($quotationdata) && isset($quotationdata['quotationdetail']['memberedittaxrate'])){ echo $quotationdata['quotationdetail']['memberedittaxrate']; }?>';
</script>
<style> .mt-30{ margin-top: 30px; }
   .combopriceid .dropdown-menu{
   width: max-content;
   }
   .productid .dropdown-menu.open{
   right: unset;
   }
   .productid .dropdown-menu.inner{
   width: max-content;
   max-width: 300px;
   }
</style>
<div class="page-content">
   <div class="page-heading">
      <h1><?php if(isset($quotationdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>
      <small>
         <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
            <li class="active"><?php if(isset($quotationdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
         </ol>
      </small>
   </div>
   <div class="container-fluid">
      <div data-widget-group="group1">
         <div class="row">
            <div class="col-md-12">
               <div class="panel panel-default border-panel">
                  <div class="panel-body pt-n">
                     <form class="form-horizontal" id="quotationform" name="quotationform">
                     <input type="hidden" name="quotationsid" id="quotationsid" value="<?php if(!empty($quotationdata) && !isset($isduplicate)){ echo $quotationdata['quotationdetail']['id']; } ?>">
                     <input type="hidden" name="isduplicate" id="isduplicate" value="<?php if(isset($isduplicate) && $isduplicate==1){ echo $isduplicate; } ?>">
                     <div class="row">
                        <input type="hidden" id="oldmemberid" name="oldmemberid" value="<?php if(!empty($quotationdata) && !isset($isduplicate)){ echo $quotationdata['quotationdetail']['memberid']; } ?>">
                        <div class="col-sm-6">
                           <div class="form-group" id="partyid_div">
                              <div class="col-sm-<?php if(isset($multiplememberchannel) && $multiplememberchannel==1){ echo "10 pr-n"; }else{ echo "12 pr-sm"; }?>" style="margin: 0px 0px 0px -7px;">
                                 <label for="partyid" class="control-label">Select Party <span class="mandatoryfield">*</span></label>
                                 <select id="partyid" name="partyid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" <?php if(!empty($quotationdata) && !isset($isduplicate)){ echo "disabled"; } ?>>
                                    <option value="0">Select Party</option>
                                    <?php /* foreach($Partydata as $party){ ?>
                                    <option value="<?=$party['id']?>"><?=$party['id']?></option>
                                    <?php } */?>
                                 </select>
                              </div>
                              <?php if(isset($multiplememberchannel) && $multiplememberchannel==1){?>
                              <!-- <div class="col-sm-2" style="padding-top: 28px !important;">
                                 <a href="javascript:void(0)"class="btn btn-primary btn-raised"><i class="fa fa-plus" title="Add <?php Member_label?>"></i></a>
                                 </div> -->
                              <div class="col-md-1 p-n" style="padding-top: 28px !important; margin:0 0 0 7px;">
                                 <a href="javascript:void(0)" onclick="addcountry()" class="btn btn-primary btn-raised p-xs"><i class="material-icons" title="Add Unit">add</i></a>
                              </div>
                              <?php } ?>
                           </div>
                        </div>
                        <div class="col-sm-6" style="margin: 0 0 0 -10px; width:50rem;">
                           <div class="form-group" id="quotationid_div">
                              <div class="col-sm-12 pr-sm">
                                 <label for="quotationid" class="control-label">Inquiry No <span class="mandatoryfield">*</span></label>
                                 <input id="quotationid" type="text" name="quotationid" class="form-control" value="<?php if(!empty($quotationdata['quotationdetail']) && !isset($isduplicate)){ echo $quotationdata['quotationdetail']['quotationid']; }else if(!empty($quotationid)){echo $quotationid; }?>" readonly>
                              </div>
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group" id="inquiryname_div">
                              <div class="col-sm-12 pl-sm pr-sm">
                                 <label for="inquiryname" class="control-label">Inquiry Name <span class="mandatoryfield">*</span></label>
                                 <div class="input-group">
                                    <input id="inquiryname" type="text" name="inquiryname" value="" class="form-control">
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group" id="quotationdate_div">
                              <div class="col-sm-12 pl-sm pr-sm">
                                 <label for="quotationdate" class="control-label">Quotation Date <span class="mandatoryfield">*</span></label>
                                 <div class="input-group">
                                    <input id="quotationdate" type="text" name="quotationdate" value="<?php if(!empty($quotationdata['quotationdetail']) && $quotationdata['quotationdetail']['quotationdate']!="0000-00-00"){ echo $this->general_model->displaydate($quotationdata['quotationdetail']['quotationdate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate());} ?>" class="form-control" readonly>
                                    <span class="btn btn-default datepicker_calendar_button"><i class="fa fa-calendar fa-lg"></i></span>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group" id="billingaddress_div">
                              <div class="col-sm-12 pr-sm pl-sm">
                                 <label for="billingaddressid" class="control-label">
                                    Select Billing Address<!--  <span class="mandatoryfield">*</span> -->
                                 </label>
                                 <select id="billingaddressid" name="billingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                    <option value="0">Select Billing Address</option>
                                    <?php if(isset($Partydata)){ 
                                       foreach($Partydata as $ba){ 
                                       ?>
                                    <option value="<?php echo $ba['id']; ?>"><?php echo ucwords($ba['billingaddress']); ?></option>
                                    <?php }} ?>
                                 </select>
                                 <!-- <a href="javascript:void(0)" class="mt-sm" style="float: left;" onclick="openmodal(1)"><i class="fa fa-plus"></i> Add New Billing Address</a> -->
                                 <input type="hidden" name="billingaddress" id="billingaddress" value="">
                              </div>
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group" id="shippingaddress_div">
                              <div class="col-sm-12 pl-sm">
                                 <label for="shippingaddressid" class="control-label">Select Shipping Address</label>
                                 <select id="shippingaddressid" name="shippingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                    <option value="0">Select Shipping Address</option>
                                    <?php if(isset($Partydata)){ 
                                       foreach($Partydata as $sa){ ?>
                                    <option value="<?php echo $sa['id']; ?>"><?php echo ucwords($sa['shippingaddress']); ?></option>
                                    <?php }} ?>
                                 </select>
                                 <!-- <a href="javascript:void(0)" class="mt-sm" style="float: left;" onclick="openmodal(2)"><i class="fa fa-plus"></i> Add New Shipping Address</a> -->
                                 <input type="hidden" name="shippingaddress" id="shippingaddress" value="">
                              </div>
                           </div>
                        </div>
                     
                     

                   

            <div class="">
               <div class="col-md-12">
                  <div class="panel panel-default border-panel" id="conect_countdocuments1">
                     
                     <div class="panel-body">
                        <div id="addtarget">
                           <div class="row">
                              <div class="col-md-2 pl-sm pr-sm ">
                                 <div class="form-group" id="firstname_div">
                                    <label for="firstname" class="col-md-12 control-label tal">Category <span class="mandatoryfield"> *</span></label>
                                 </div>
                              </div>
                              <div class="col-md-2 pl-sm pr-sm ">
                                 <div class="form-group" id="lastname_div">
                                    <label for="lastname" class="col-md-12 control-label tal">product <span  class="mandatoryfield"> *</span></label>
                                 </div>
                              </div>
                              
                              <div class="col-md-1 pl-sm pr-sm ">
                                 <div class="form-group" id="birthdate_div">
                                    <label for="birthdate" class="col-md-12 control-label tal">qty </label>
                                 </div>
                              </div>
                              <div class="col-md-2 pl-sm pr-sm ">
                                 <div class="form-group" id="contactno_div">
                                    <label for="contactno" class="col-md-12 control-label tal">Delivery Priarity <span class="mandatoryfield" > *</span></label>
                                 </div>
                              </div>
                             
                              <div class="clearfix"></div>
                              <?php if(isset($paymentreceiptdata) && !empty($receipttransactionsdata)){?>
                            
                              <?php }else{ ?>
                              <div class="countcategory" id="countcategory1">
                                 <div class="row m-n">
                                    <div class="col-md-2">
                                       <div class="form-group" id="category1_div">
                                          <div class="col-md-12">
                                             <select id="categoryid1" name="categoryid[]" class="selectpicker form-control categoryid" data-live-search="true" data-select-on-tab="true" data-size="6">
                                                <option value="0">Select Category </option>
                                             </select>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-2">
                                       <div class="form-group" id="productid1_div">
                                          <div class="col-md-12">
                                             <select id="productid1" name="productid[]" class="selectpicker form-control product" data-live-search="true" data-select-on-tab="true" data-size="6">
                                                <option value="0">Select Product</option>
                                             </select>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-2">
                                       <div class="form-group" id="qty1_div">
                                          <div class="col-md-12">								
                                             <input type="text" id="qty1" class="form-control text-right qty" name="qty[]" value="" onkeypress="return decimal_number_validation(event, this.value, 10)">
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-2">
                                       <div class="form-group" id="deliverypriarity1_div">
                                          <div class="col-md-12">								
                                             <input type="text" id="deliverypriarity1" class="form-control text-right deliverypriarity" value="" onkeypress="return decimal_number_validation(event, this.value, 10)" >
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-1 pt-md">
                                       <button type="button" class="btn btn-danger btn-raised  remove_invoice_btn m-n" onclick="removetransaction(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                       <button type="button" class="btn btn-primary btn-raised add_invoice_btn m-n" onclick="addnewinvoicetransaction()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                    </div>
                                 </div>
                              </div>
                              <?php } ?>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-4">
                        <div class="form-group" id="deliverydate_div">
                           <div class="col-sm-12">
                              <label for="deliverydate" class="control-label">Select Approx Delivery Date <span class="mandatoryfield">*</span></label>
                              <div class="input-group">
                                 <input id="deliverydate" type="text" name="deliverydate" value="" class="form-control date" readonly>
                                 <span class="btn btn-default datepicker_calendar_button"><i class="fa fa-calendar fa-lg"></i></span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group" id="discount_div">
                           <div class="col-sm-12">
                              <input type="hidden" name="olddiscount" id="olddiscount" value="">
                              <label for="discountid" class="control-label">Discount(%) <span class="mandatoryfield">*</span></label>
                              <input id="discount" type="text" name="discount" value="" class="form-control" onkeypress="return decimal_number_validation(event, this.value, 10)">
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group" id="amount_div">
                           <div class="col-sm-12">
                              <input type="hidden" name="oldamount" id="oldamount" value="">
                              <label for="amountid" class="control-label">Amount <span class="mandatoryfield">*</span></label>
                              <input id="amount" type="text" onkeypress="return decimal_number_validation(event, this.value, 10)" name="discountamount" value="" class="form-control" >
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12 p-n">
                        <div class="col-md-3">
                           <div class="form-group" id="remarks_div">
                              <div class="col-sm-12 pr-n">
                                 <label for="remarks" class="control-label">Remarks</label>
                                 <textarea id="remarks" name="remarks" class="form-control"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['remarks']; }?></textarea>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-9 pull-right p-n">
                           <div class="col-md-6 pr-xs">
                           </div>
                           <div class="col-md-6 pl-xs">
                              <input type="hidden" name="removeextrachargemappingid" id="removeextrachargemappingid">
                              <table id="example" class="table table-bordered table-striped" cellspacing="0" width="100%" style="border: 1px solid #e8e8e8;">
                                 <tbody>
                                    <tr>
                                       <th colspan="2" class="text-center">Quotation Summary (<?=CURRENCY_CODE?>)</th>
                                    </tr>
                                    <tr>
                                       <th>Total Of Product</th>
                                       <td class="text-right" width="30%">
                                          <span id="grossamount"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['quotationamount']; }else{ echo "0.00"; }?></span>
                                          <input type="hidden" id="inputgrossamount" name="grossamount" value="<?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['quotationamount']; } ?>">
                                       </td>
                                    </tr>
                                    <tr id="discountrow" style="display: none;">
                                       <th>Discount (<span id="discountpercentage"><?php if(!empty($quotationdata['ordquotationdetailrdetail'])){ echo number_format($quotationdata['quotationdetail']['globaldiscount']*100/$quotationdata['quotationdetail']['quotationamount'],2); }else{ echo "0"; }?></span>%)
                                       </th>
                                       <td class="text-right">
                                          <span id="discountamount"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['globaldiscount']; }else{ echo "0.00"; }?></span>
                                       </td>
                                    </tr>
                                    <tr>
                                       <th>Round Off</th>
                                       <td class="text-right">
                                          <span id="roundoff">0.00</span>
                                          <input type="hidden" id="inputroundoff" name="inputroundoff" value="0.00">
                                       </td>
                                    </tr>
                                    <tr>
                                       <th>Amount Payable</th>
                                       <th class="text-right">
                                          <span id="netamount" name="netamount"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['payableamount']; }else{ echo "0.00"; } ?></span>
                                          <input type="hidden" id="inputnetamount" name="netamount" value="<?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['payableamount']; }?>">
                                       </th>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>



                        <?php /*
                           <div class="col-sm-2">
                               <div class="form-group" id="salesperson_div">
                                   <div class="col-sm-12 pl-sm pr-sm">
                                       <label for="salespersonid" class="control-label">Select Sales Person</label>
                                       <select id="salespersonid" name="salespersonid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                           <option value="0">Select Sales Person</option>
                                           <?php if(isset($employeedata)){ foreach($employeedata as $emp){ ?>
                        <option value="<?php echo $emp['id']; ?>" <?php if(!empty($quotationdata['quotationdetail']) && $quotationdata['quotationdetail']['salespersonid'] == $emp['id']){ echo "selected"; }?>><?php echo ucwords($emp['name']); ?></option>
                        <?php }} ?>
                        </select>
                     </div>
                  </div>
               </div>
               */?>
               <?php /*
                  <div class="col-sm-2">
                      <div class="form-group" id="salesperson_div">
                          <div class="col-sm-12 pl-sm pr-sm">
                              <label for="cashorbankid" class="control-label">Select Bank</label>
                              <select id="cashorbankid" name="cashorbankid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                  <option value="0">Select Bank</option>
                                  <?php if(isset($cashorbankdata)){ foreach($cashorbankdata as $cashOrBank){
                     if(strtolower($cashOrBank['bankname'])!='cash'){ ?>
               <option value="<?php echo $cashOrBank['id']; ?>" <?php if(!empty($quotationdata['quotationdetail'])){ if($quotationdata['quotationdetail']['cashorbankid'] == $cashOrBank['id']) { echo "selected";} }else{ if($defaultbankdata == $cashOrBank['id']){ echo "selected"; } }?>><?php echo ucwords($cashOrBank['bankname']); ?></option>
               <?php }}} ?>
               </select>
            </div>
         </div>
      </div>
      */ ?>
   </div>
   <!-- <div class="row">
      <div class="col-md-12">
         <hr>
      </div>
      </div> -->
   <div class="row">
      <div class="col-sm-3">
         <div class="form-group" id="quotationdate_div">
            <div class="col-sm-12 pl-sm pr-sm">
               <label for="quotationdate" class="control-label">Select Approx Delivery Date <span class="mandatoryfield">*</span></label>
               <div class="input-group">
                  <input id="quotationdate" type="text" name="quotationdate" value="<?php if(!empty($quotationdata['quotationdetail']) && $quotationdata['quotationdetail']['quotationdate']!="0000-00-00"){ echo $this->general_model->displaydate($quotationdata['quotationdetail']['quotationdate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate());} ?>" class="form-control" readonly>
                  <span class="btn btn-default datepicker_calendar_button"><i class="fa fa-calendar fa-lg"></i></span>
               </div>
            </div>
         </div>
      </div>
      <?php /*
         <div class="col-sm-3">
             <div class="form-group" id="assignto_div">
                 <div class="col-sm-12 pl-sm pr-sm">
                     <label for="quotationdate" class="control-label">Assign to  <span class="mandatoryfield">*</span></label>
                     <div class="input-group">
                         <select id="assignto" name="assignto" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                             <option value="0">Select Assign to</option>
                             <option value="1" <?php if(!empty($quotationdata)){ if($quotationdata['quotationdetail']['paymenttype']==1){ echo "selected"; } }?>>COD</option>
                              <option value="3" <?php if(!empty($quotationdata)){ if($quotationdata['quotationdetail']['paymenttype']==3){ echo "selected"; } }?>>Advance Payment</option>
                              <?php if(HIDE_EMI==0){ ?>
                              <option value="4" <?php if(!empty($quotationdata)){ if($quotationdata['quotationdetail']['paymenttype']==4){ echo "selected"; } }?>>EMI Payment</option>
                              <?php } ?>
                              <option value="5" <?php if(!empty($quotationdata)){ if($quotationdata['quotationdetail']['paymenttype']==5){ echo "selected"; } }?>>Debit</option>
                              </select>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
      */?>
      <div class="col-sm-3">
         <div class="form-group" id="paymenttype_div">
            <div class="col-sm-12">
               <input type="hidden" name="oldpaymenttype" id="oldpaymenttype" value="<?php if(!empty($quotationdata['quotationdetail']) && !isset($isduplicate)){ echo $quotationdata['quotationdetail']['paymenttype']; } ?>">
               <label for="paymenttypeid" class="control-label">Assign to <span class="mandatoryfield">*</span></label>
               <select id="paymenttypeid" name="paymenttypeid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                  <option value="0">Select Assign to</option>
                  <?php if(isset($Partydata)){ 
                     foreach($Partydata as $sa){ ?>
                  <option value="<?php echo $sa['id']; ?>"><?php echo ucwords($sa['shippingaddress']); ?></option>
                  <?php }} ?>
               </select>
            </div>
         </div>
      </div>
      <div class="col-sm-3">
         <div class="form-group" id="paymenttype_div">
            <div class="col-sm-12">
               <input type="hidden" name="oldpaymenttype" id="oldpaymenttype" value="<?php if(!empty($quotationdata['quotationdetail']) && !isset($isduplicate)){ echo $quotationdata['quotationdetail']['paymenttype']; } ?>">
               <label for="paymenttypeid" class="control-label">Select Payment Type <span class="mandatoryfield">*</span></label>
               <select id="paymenttypeid" name="paymenttypeid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                  <option value="0">Select Payment Type</option>
                  <option value="1" <?php if(!empty($quotationdata)){ if($quotationdata['quotationdetail']['paymenttype']==1){ echo "selected"; } }?>>COD</option>
                  <option value="3" <?php if(!empty($quotationdata)){ if($quotationdata['quotationdetail']['paymenttype']==3){ echo "selected"; } }?>>Advance Payment</option>
                  <?php if(HIDE_EMI==0){ ?>
                  <option value="4" <?php if(!empty($quotationdata)){ if($quotationdata['quotationdetail']['paymenttype']==4){ echo "selected"; } }?>>EMI Payment</option>
                  <?php } ?>
                  <option value="5" <?php if(!empty($quotationdata)){ if($quotationdata['quotationdetail']['paymenttype']==5){ echo "selected"; } }?>>Debit</option>
               </select>
            </div>
         </div>
      </div>
      <div class="col-md-12 p-n">
         <div class="col-md-4 p-n">
            <div class="col-md-12 p-n">
               <?php 
                  $discountpercentage =  $discountamount = $discountminamount = "";
                  /* if(isset($discountonbillminamount) && $discountonbillminamount>=0){
                     if(isset($globaldiscountper)){
                        $discountpercentage = $globaldiscountper;    
                     }
                     if(isset($globaldiscountamount)){
                        $discountamount = $globaldiscountamount;    
                     }
                     $discountminamount = $discountonbillminamount;    
                  } */
                  
                  if(!empty($quotationdata['quotationdetail'])){ 
                     if($quotationdata['quotationdetail']['globaldiscount'] > 0){
                        /* if(isset($gstondiscount) && $gstondiscount==1){ 
                              $discountper = $quotationdata['quotationdetail']['globaldiscount']*100/ ($quotationdata['quotationdetail']['quotationamount']); 
                        }else{
                              $discountper = $quotationdata['quotationdetail']['globaldiscount']*100/ ($quotationdata['quotationdetail']['quotationamount'] + $quotationdata['quotationdetail']['taxamount']); 
                        }
                        
                        if($discountper!=0){
                              $discountpercentage = number_format($discountper,2); 
                        } */
                  
                        $discountper = $quotationdata['quotationdetail']['globaldiscount']*100/ ($quotationdata['quotationdetail']['quotationamount'] + $quotationdata['quotationdetail']['taxamount']); 
                        $discountpercentage = number_format($discountper,2); 
                        
                        $discountamnt = $quotationdata['quotationdetail']['globaldiscount']; 
                        $discountamount = number_format($discountamnt,2,'.',''); 
                     }else{
                        $discountpercentage = $discountamount = '';
                     }
                     ?>
               <script type="text/javascript">
                  globaldicountper = "<?=$discountpercentage?>"; 
                  globaldicountamount = "<?=$discountamount?>"; 
               </script>
               <?php } ?>
               <!-- <div class="col-md-6 pr-sm">
                  <div class="form-group  ml-n mr-n text-right">
                     <label for="overalldiscountpercent" class="control-label">Discount (%)</label>
                     <input type="text" class="form-control overalldiscountpercent" id="overalldiscountpercent" name="overalldiscountpercent" value="<?php //if(!empty($quotationdata['quotationdetail'])){ echo $discountpercentage; } ?>" onkeypress="return decimal_number_validation(event, this.value)" style="text-align:right;">	
                     <span class="material-input"></span>
                  </div>
                  </div> -->
               <!-- <div class="col-md-6 pl-sm pr-n">
                  <div class="form-group  ml-n mr-n text-right">
                     <label for="overalldiscountamount" class="control-label">Discount (<?php //CURRENCY_CODE?>)</label>
                     <input type="text" class="form-control overalldiscountamount" id="overalldiscountamount" name="overalldiscountamount" value="<?php //if(!empty($quotationdata['quotationdetail'])){ echo $discountamount; } ?>" onkeypress="return decimal_number_validation(event, this.value)" style="text-align:right;">	
                     <span class="material-input"></span>
                  </div>
                  </div> -->
            </div>
         </div>
         <div class="col-md-9 pull-right p-n">
            <div class="col-md-6 pr-xs">
               <!-- <table class="table table-bordered table-striped" cellspacing="0" width="100%" style="border: 1px solid #e8e8e8;">
                  <thead>
                     <tr>                  
                        <th class="text-center">GST Summary</th>
                        <th class="text-center">Assessable Amount (<?=CURRENCY_CODE?>)</th>
                        <th class="text-center">GST Amount (<?=CURRENCY_CODE?>)</th>
                     </tr>  
                  </thead>
                  <tbody>
                     <tr>                  
                        <th>Product Total</th>
                        <td class="text-right" width="20%">
                              <span id="productassesbaleamount">0.00</span>
                        </td>
                        <td class="text-right" width="20%">
                              <span id="productgstamount">0.00</span>
                        </td>
                     </tr>
                     <tr>                    
                        <th>Extra Charges Total</th>
                        <td class="text-right"><span id="chargestotalassesbaleamount">0.00</span></td>
                        <td class="text-right"><span id="chargestotalgstamount">0.00</span></td>
                     </tr>
                     <tr>                    
                        <th></th>
                        <th class="text-right">
                              <span id="producttotalassesbaleamount">0.00</span>
                              <input type="hidden" id="totalgrossamount" name="totalgrossamount" value="">
                        </th>
                        <th class="text-right">
                              <span id="producttotalgstamount">0.00</span>
                              <input type="hidden" id="inputtotaltaxamount" name="inputtotaltaxamount" value="">
                        </th>
                     </tr>  
                  </tbody>
                  </table> -->
            </div>
            <div class="col-md-4 ">
               <input type="hidden" name="removeextrachargemappingid" id="removeextrachargemappingid">
               <table id="example" class="table table-bordered table-striped" cellspacing="0" style="border: 1px solid #e8e8e8; margin: -50px 0 0 110px;">
                  <tbody>
                     <tr>
                        <th colspan="2" class="text-center">Quotation Summary (<?=CURRENCY_CODE?>)</th>
                     </tr>
                     <tr>
                        <th>Total Of Product</th>
                        <td class="text-right" width="30%">
                           <span id="grossamount"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['quotationamount']; }else{ echo "0.00"; }?></span>
                           <input type="hidden" id="inputgrossamount" name="grossamount" value="<?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['quotationamount']; } ?>">
                        </td>
                     </tr>
                     <tr id="discountrow" style="display: none;">
                        <th>Discount (<span id="discountpercentage"><?php if(!empty($quotationdata['ordquotationdetailrdetail'])){ echo number_format($quotationdata['quotationdetail']['globaldiscount']*100/$quotationdata['quotationdetail']['quotationamount'],2); }else{ echo "0"; }?></span>%)
                        </th>
                        <td class="text-right">
                           <span id="discountamount"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['globaldiscount']; }else{ echo "0.00"; }?></span>
                        </td>
                     </tr>
                     <?php  /*
                        if(!empty($quotationdata) && !empty($ExtraChargesData)) { ?>
                     <?php for ($i=0; $i < count($ExtraChargesData); $i++) { ?>
                     <!-- <tr class="countcharges" id="countcharges<?php //$i+1?>">                  <th>
                        <input type="hidden" name="extrachargemappingid[]" value="<?=(!isset($isduplicate))?$ExtraChargesData[$i]['id']:""?>" id="extrachargemappingid<?=$i+1?>">
                        <div class="col-md-9 p-n">
                           <div class="form-group p-n" id="extracharges<?=$i+1?>_div">
                              <div class="col-sm-12">
                                    <select id="extrachargesid<?=$i+1?>" name="extrachargesid[]" class="selectpicker form-control extrachargesid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                       <option value="0">Select Extra Charges</option>
                                       <?php foreach($extrachargesdata as $extracharges){ ?>
                                          <option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>" <?php if($ExtraChargesData[$i]['extrachargesid'] == $extracharges['id']){ echo "selected"; } ?>><?php echo $extracharges['extrachargename']; ?></option>
                                       <?php } ?>
                                    </select>
                        
                                    <input type="hidden" name="extrachargestax[]" id="extrachargestax<?=$i+1?>" class="extrachargestax" value="<?=number_format($ExtraChargesData[$i]['taxamount'],2,'.','')?>">
                                    <input type="hidden" name="extrachargesname[]" id="extrachargesname<?=$i+1?>" class="extrachargesname" value="<?=$ExtraChargesData[$i]['extrachargesname']?>">
                                    <input type="hidden" name="extrachargepercentage[]" id="extrachargepercentage<?=$i+1?>" class="extrachargepercentage" value="<?=$ExtraChargesData[$i]['extrachargepercentage']?>">
                              </div>
                           </div>
                        </div>
                        <div class="col-md-3 text-right p-n pt-md">
                           <?php if($i==0){?>
                              <?php if(count($ExtraChargesData)>1){ ?>
                                    <button type="button" class="btn btn-default btn-raised remove_charges_btn" onclick="removecharge(1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                              <?php }else { ?>
                                    <button type="button" class="btn btn-default btn-raised add_charges_btn" onclick="addnewcharge()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                              <?php } ?>
                        
                           <?php }else if($i!=0) { ?>
                              <button type="button" class="btn btn-default btn-raised remove_charges_btn" onclick="removecharge(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                           <?php } ?>
                           <button type="button" class="btn btn-default btn-raised btn-sm remove_charges_btn" onclick="removecharge(<?=$i+1?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                        
                           <button type="button" class="btn btn-default btn-raised add_charges_btn" onclick="addnewcharge()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>  
                        </div>
                        </th>
                        
                        <td class="text-right">
                        <div class="form-group p-n" id="extrachargeamount<?=$i+1?>_div">
                           <div class="col-sm-12">
                              <input type="text" id="extrachargeamount<?=$i+1?>" name="extrachargeamount[]" value="<?=number_format($ExtraChargesData[$i]['amount'],2,'.','')?>" class="form-control text-right extrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)">
                           </div>
                        </div>
                        </td>
                        
                        </tr> -->
                     <?php } ?>
                     <?php }else{ ?>
                     <!-- <tr class="countcharges" id="countcharges1">                    
                        <th>
                           <div class="col-md-9 p-n">
                              <div class="form-group p-n" id="extracharges1_div">
                                    <div class="col-sm-12">
                                       <select id="extrachargesid1" name="extrachargesid[]" class="selectpicker form-control extrachargesid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                          <option value="0">Select Extra Charges</option>
                                          <?php foreach($extrachargesdata as $extracharges){ ?>
                                                <option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>"><?php echo $extracharges['extrachargename']; ?></option>
                                          <?php } ?>
                                       </select>
                        
                                       <input type="hidden" name="extrachargestax[]" id="extrachargestax1" class="extrachargestax" value="">
                                       <input type="hidden" name="extrachargesname[]" id="extrachargesname1" class="extrachargesname" value="">
                                       <input type="hidden" name="extrachargepercentage[]" id="extrachargepercentage1" class="extrachargepercentage" value="">
                                    </div>
                              </div>
                           </div>
                           <div class="col-md-3 text-right p-n pt-md">
                              <button type="button" class="btn btn-default btn-raised  remove_charges_btn m-n" onclick="removecharge(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                        
                              <button type="button" class="btn btn-default btn-raised  add_charges_btn m-n" onclick="addnewcharge()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                           </div>
                        </th>
                        
                        <td class="text-right">
                           <div class="form-group p-n" id="extrachargeamount1_div">
                              <div class="col-sm-12">
                                    <input type="text" id="extrachargeamount1" name="extrachargeamount[]" class="form-control text-right extrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)">
                              </div>
                           </div>
                        </td>
                        
                        </tr> -->
                     <?php } */?>
                     <tr>
                        <th>Round Off</th>
                        <td class="text-right">
                           <span id="roundoff">0.00</span>
                           <input type="hidden" id="inputroundoff" name="inputroundoff" value="0.00">
                        </td>
                     </tr>
                     <tr>
                        <th>Amount Payable</th>
                        <th class="text-right">
                           <span id="netamount" name="netamount"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['payableamount']; }else{ echo "0.00"; } ?></span>
                           <input type="hidden" id="inputnetamount" name="netamount" value="<?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['payableamount']; }?>">
                        </th>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-6" style="margin: -40px 0 40px 10px;">
            <div class="form-group" id="remarks_div">
               <div class="col-sm-12">
                  <label for="remarks" class="control-label">Remarks</label>
                  <textarea id="remarks" name="remarks" class="form-control"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['remarks']; }?></textarea>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-default border-panel" id="commonpanel">
            <div class="panel-heading">
               <h2>Document Detail</h2>
            </div>
            <div class="panel-body no-padding">
               <div class="row" id="adddocrow">
                  <div class="col-md-12">
                     <div class="col-md-12 pl-sm pr-sm visible-md visible-lg">
                        <div class="col-md-5">
                           <div class="form-group">
                              <div class="col-md-12 pl-xs pr-xs">
                                 <label class="control-label" style="text-align: left;">Document Name <span class="mandatoryfield">*</span></label>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-5">
                           <div class="form-group">
                              <div class="col-md-12 pl-xs pr-xs">
                                 <label class="control-label">File</label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-12">
                     <?php 
                        $cloopdoc = 0;
                        $doc_id='';
                        $doc='';
                        $docname='';
                        $i=0;
                        
                        if(isset($party_docdata[0]->id ) && !empty($party_docdata[0]->id ))  {
                              foreach ($party_docdata as $row)
                           {
                              $i++;
                              $cloopdoc = $cloopdoc + 1;
                              $doc_id = $row->id;
                              $doc=$row->doc;
                              $docname = $row->docname;
                        ?>

                     <input type="hidden" name="doc_id_<?=$cloopdoc?>" value="<?=$doc_id?>" id="doc_id_<?=$cloopdoc?>">
                     <div class="col-md-12">
                        <div class="col-sm-12 countdocuments pl-sm pr-sm" id="countdocuments<?=$cloopdoc?>">
                     
                           <div class="col-md-5 col-sm-5">
                              <div class="form-group" id="documentnumber_<?=$cloopdoc?>">
                                 <div class="col-sm-12 pr-xs pl-xs">
                                    <input id="documentname_<?=$cloopdoc?>" value="<?=$docname?>" name="documentname_<?=$cloopdoc?>" placeholder="Enter Document Name" class="form-control documentnumber">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-5 col-sm-5">
                              <div class="form-group" id="docfile<?=$cloopdoc?>">
                                 <div class="col-sm-12 pr-xs pl-xs">
                                    <input type="hidden" id="isvaliddocfile<?=$cloopdoc?>" value="0">
                                    <input type="hidden" name="olddocfile_<?=$cloopdoc?>" id="olddocfile<?=$cloopdoc?>" value="">
                                    <div class="input-group" id="fileupload<?=$cloopdoc?>">
                                       <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                             <span class="btn btn-primary btn-raised btn-file">
                                             <i class="fa fa-upload"></i>
                                                <input type="file" name="olddocfile_<?=$cloopdoc?>" class="docfile" id="olddocfile_<?=$cloopdoc?>" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),&apos;docfile<?=$cloopdoc?>&apos;)">
                                             </span>
                                       </span>
                                       <input type="text" readonly="" placeholder="Enter File" id="Filetextdocfile<?=$cloopdoc?>" class="form-control docfile" name="Filetextdocfile_<?=$cloopdoc?>" value="<?=$doc?>">
                                    </div>
                                 </div>
                              </div>
                           </div>
                        <div class="col-md-1 addrowbutton pt-md pr-xs">
                           <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeDocument(<?=$cloopdoc?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                        </div>
                     </div>
                  </div>

                     <?php
                        }
                        }else {
                              $count = 1;
                              $cloopdoc = 0;
                              while ($count > $cloopdoc) {
                                 $cloopdoc = $cloopdoc + 1;
                        ?>
                     <div class="col-md-12 countdocuments pl-sm pr-sm" id="countdocuments">
                        <div class="col-md-5 col-sm-5">
                              <div class="form-group" id="documentnumber1_div">
                                 <div class="col-md-12 pr-xs pl-xs">
                                    <input id="documentnumber_<?=$cloopdoc?>" name="documentname_<?=$cloopdoc?>" placeholder="Enter Document Number" class="form-control documentrow documentnumber">
                                 </div>
                              </div>
                        </div>
                        <div class="col-md-5 col-sm-5">
                              <div class="form-group" id="docfile1_div">
                                 <div class="col-md-12 pr-xs pl-xs">
                                    <input type="hidden" id="isvaliddocfile1" value="0"> 
                                    <input type="hidden" name="olddocfile_<?=$cloopdoc?>" id="olddocfile1" value=""> 
                                    <div class="input-group" id="fileupload1">
                                          <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                             <span class="btn btn-primary btn-raised btn-file"><i
                                                      class="fa fa-upload"></i>
                                                <input type="file" name="docfile_<?=$cloopdoc?>"
                                                      class="docfile" id="docfile1"
                                                      accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),'docfile1')">
                                             </span>
                                          </span>
                                          <input type="text" readonly="" id="Filetext_<?=$cloopdoc?>"
                                             class="form-control documentrow docfile" placeholder="Enter File" name="Filetextdocfile" value="">
                                    </div>
                                 </div>
                              </div>
                        </div>
                        
                        </div>
                     <?php
                        }
                        } 
                        ?>
                  </div>
               </div>
               <div class="form-group" style="float:left; margin:0px 50px 20px 20px;">
                     <button type="button"  onclick="addnewproduct()" class="addprodocitem btn-primary"><i class="fa fa-plus"></i></button>
               </div>
               <input type="hidden" name="cloopdoc" id="cloopdoc" value="<?php echo $cloopdoc; ?>">
            </div>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12 col-xs-12">
         <div class="panel panel-default border-panel">
            <div class="panel-heading">
               <h2>Actions</h2>
            </div>
            <div class="panel-body">
               <div class="row">
                  <div class="form-group text-center">
                     <div class="col-md-12 col-xs-12">
                        <?php if (!empty($partydata)) { ?>
                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                        <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                        <?php } else { ?>
                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                        <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                        <?php } ?>
                        <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL.$this->session->userdata(base_url() . 'submenuurl')?>" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

<?php if(!empty($channelsetting) && $channelsetting['partialpayment']==1){ 
   $received = 0;
   if(!empty($installmentdata)){ 
        //$key = array_search('1', array_column($installmentdata, 'status'));
        $key = false;
        $search = ['status' => 1];
        foreach ($installmentdata as $k => $v) {
            if ($v['status'] == $search['status']) {
                $key = true;
                // key found - break the loop
                break;
            }
        }
        if($key!=false){
            $received = 1;
        }else{
            $received = 0;
        }
   }
    ?>
<script>
   EMIreceived = <?=$received?>;
</script>
<div class="row">
   <div class="col-md-12">
      <div class="row" id="installmentsetting_div" style="<?php if(!empty($installmentdata) && HIDE_EMI==0){ echo "display: block;"; }else{ echo "display: none;"; } ?>">
         <div class="col-sm-2">
            <div class="form-group" id="noofinstallment_div">
               <div class="col-sm-12">
                  <label for="noofinstallment" class="control-label">No. of Installment</label>
                  <input type="text" class="form-control" id="noofinstallment" name="noofinstallment" maxlength="2" value="<?php if(!empty($installmentdata)){ echo count($installmentdata); } ?>" onkeypress="return isNumber(event)">
               </div>
            </div>
         </div>
         <div class="col-sm-3">
            <div class="form-group" id="emidate_div">
               <div class="col-sm-12">
                  <label for="emidate" class="control-label">EMI Start Date</label>
                  <input id="emidate" type="text" name="emidate" value="<?php if(!empty($installmentdata)){ echo $this->general_model->displaydate($installmentdata[0]['date']); } ?>" class="form-control" readonly>
               </div>
            </div>
         </div>
         <div class="col-sm-3">
            <div class="form-group" id="emiduration_div">
               <div class="col-sm-12">
                  <label for="emiduration" class="control-label">EMI Duration (In Days)</label>
                  <input id="emiduration" type="text" name="emiduration" value="<?php if(!empty($installmentdata)){ if(count($installmentdata)==1){ echo "1"; } else{ echo ceil(abs(strtotime($installmentdata[0]['date']) - strtotime($installmentdata[1]['date'])) / 86400);
                     } }?>" class="form-control" maxlength="4">
               </div>
            </div>
         </div>
         <div class="col-sm-3 pt-xxl">
            <div class="form-group">
               <!-- <div class="col-sm-12">
                  <button type="button" onclick="generateinstallment()" class="btn btn-primary btn-raised" <?php if($received==1 && !isset($isduplicate)){ echo "disabled"; } ?>>Generate</button>
               </div> -->
            </div>
         </div>
      </div>
   </div>
</div>
<div id="installmentmaindiv" style="margin-top: 12px;<?php if(HIDE_EMI==0){ echo "display:block;"; } else{ echo "display:none;"; } ?>">
   <div class="row" id="installmentmaindivheading" style="<?php if(!empty($installmentdata)){ echo "display: block;"; }else{ echo "display: none;"; } ?>">
      <div class="col-md-1 text-center"><b>Sr.No</b></div>
      <div class="col-md-2 text-center"><b>Installment (%)</b></div>
      <div class="col-md-2 text-center"><b>Amount</b></div>
      <div class="col-md-2 text-center"><b>Installment Date</b></div>
      <div class="col-md-2 text-center"><b>Payment Date</b></div>
      <div class="col-md-2 text-center"><b>Received Status</b></div>
   </div>
</div>
<div id="installmentdivs" style="<?php if(HIDE_EMI==0){ echo "display:block;"; } else{ echo "display:none;"; } ?>">
   <?php if(!empty($installmentdata)){ 
      for($i=0; $i < count($installmentdata); $i++){ ?>
   <input type="hidden" name="installmentid[]" value="<?=(!isset($isduplicate))?$installmentdata[$i]['id']:""?>">   
   <div class="row noofinstallmentdiv">
      <div class="col-md-1 text-center">
         <div class="form-group">
            <div class="col-sm-12"><?=($i+1)?></div>
         </div>
      </div>
      <div class="col-md-2 text-center">
         <div class="form-group">
            <div class="col-sm-12">
               <input type="text" id="percentage<?=($i+1)?>" value="<?=$installmentdata[$i]['percentage']?>" name="percentage[]" class="form-control text-right percentage"  div-id="<?=($i+1)?>" maxlength="5" onkeyup="return onlypercentage(this.id)" onkeypress="return decimal(event,this.id)" <?php if($received==1 && !isset($isduplicate)){ echo "readonly"; } ?>>
            </div>
         </div>
      </div>
      <div class="col-md-2 text-center">
         <div class="form-group">
            <div class="col-sm-12">
               <input type="text" id="installmentamount<?=($i+1)?>" value="<?=$installmentdata[$i]['amount']?>" name="installmentamount[]" class="form-control text-right installmentamount" div-id="<?=($i+1)?>" maxlength="5" onkeypress="return decimal(event,this.id);" readonly>
            </div>
         </div>
      </div>
      <div class="col-md-2 text-center">
         <div class="form-group">
            <div class="col-sm-12">
               <input type="text" id="installmentdate<?=($i+1)?>" value="<?=$this->general_model->displaydate($installmentdata[$i]['date'])?>" name="installmentdate[]" class="form-control installmentdate" div-id="<?=($i+1)?>" maxlength="5" <?php if($received==1 && !isset($isduplicate)){ echo "disabled"; } ?>>
            </div>
         </div>
      </div>
      <div class="col-md-2 text-center">
         <div class="form-group">
            <div class="col-sm-12">
               <input type="text" id="paymentdate<?=($i+1)?>" value="<?=($installmentdata[$i]['paymentdate']!='' && !isset($isduplicate))?$this->general_model->displaydate($installmentdata[$i]['paymentdate']):''?>" name="paymentdate[]" class="form-control paymentdate" div-id="<?=($i+1)?>" maxlength="5" <?php if($received==1 && !isset($isduplicate)){ echo "disabled"; } ?>>
            </div>
         </div>
      </div>
      <div class="col-md-2 text-center">
         <div class="form-group">
            <div class="col-sm-12">
               <div class="checkbox">
                  <input id="installmentstatus<?=($i+1)?>" type="checkbox" value="<?=$installmentdata[$i]['status']?>" name="installmentstatus<?=($i+1)?>" div-id="<?=($i+1)?>" class="checkradios" <?php if($received==1 && !isset($isduplicate)){ echo "disabled"; } ?> <?=($installmentdata[$i]['status']==1 && !isset($isduplicate))?"checked":""?> >
                  <label for="installmentstatus<?=($i+1)?>"></label>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php } ?>
   <?php } ?>
</div>
<?php } ?>
<!-- <div class="col-md-12" id='adddocitem'>
   <?php 
      $cloopdoc = 0;
      $i=0;
      
      if(isset($party_docdata[0]->id ) && !empty($party_docdata[0]->id ))  {
          foreach ($party_docdata as $row)
         {
             $i++;
             $cloopdoc = $cloopdoc + 1;
             $doc_id = $row->id;
             $doc=$row->doc;
             $docname = $row->docname;
         ?>
   <div class="col-md-12 countdocuments pl-sm pr-sm" id="countdocuments">
      <input type="hidden" name="doc_id_<?=$cloopdoc?>" value="<?=$doc_id?>" id="doc_id">
      <div class="col-md-6 col-sm-6">
         <div class="form-group" id="documentname_div">
            <div class="col-md-12 pr-xs pl-xs">
               <input id="documentname" name="documentname_<?=$cloopdoc?>" placeholder="Enter Document Number" class="form-control documentrow documentnumber" value="<?=$docname?>">
            </div>
         </div>
      </div>
      <div class="col-md-6 col-sm-6">
         <div class="form-group" id="docfile_div">
            <div class="col-md-12 pr-xs pl-xs">
               <input type="hidden" id="isvaliddocfile" value=""> 
               <input type="hidden" name="olddocfile_<?=$cloopdoc?>" id="olddocfile" value=""> 
               <div class="input-group" id="fileupload">
                  <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                  <span class="btn btn-primary btn-raised btn-file"><i
                     class="fa fa-upload"></i>
                  <input type="file" name="docfile_<?=$cloopdoc?>"
                     class="docfile" id="docfile"
                     accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),'docfile')">
                  </span>
                  </span>
                  <input type="text" readonly="" id="Filetextdocfile"
                     class="form-control documentrow" placeholder="Enter File" name="Filetextdocfile_<?=$cloopdoc?>" value="<?=$doc?>">
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php
      }
      }else {
          $count = 1;
          $cloopdoc = 0;
          while ($count > $cloopdoc) {
              $cloopdoc = $cloopdoc + 1;
      ?>
   <div class="col-md-12 countdocuments pl-sm pr-sm" id="countdocuments">
      <div class="col-md-6 col-sm-6">
         <div class="form-group" id="documentnumber1_div">
            <div class="col-md-12 pr-xs pl-xs">
               <input id="documentnumber_<?=$cloopdoc?>" name="documentname_<?=$cloopdoc?>" placeholder="Enter Document Number" class="form-control documentrow documentnumber">
            </div>
         </div>
      </div>
      <div class="col-md-6 col-sm-">
         <div class="form-group" id="docfile1_div">
            <div class="col-md-12 pr-xs pl-xs">
               <input type="hidden" id="isvaliddocfile1" value="0"> 
               <input type="hidden" name="olddocfile_<?=$cloopdoc?>" id="olddocfile1" value=""> 
               <div class="input-group" id="fileupload1">
                  <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                  <span class="btn btn-primary btn-raised btn-file"><i
                     class="fa fa-upload"></i>
                  <input type="file" name="docfile_<?=$cloopdoc?>"
                     class="docfile" id="docfile1"
                     accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),'docfile1')">
                  </span>
                  </span>
                  <input type="text" readonly="" id="Filetext_<?=$cloopdoc?>"
                     class="form-control documentrow docfile" placeholder="Enter File" name="Filetextdocfile" value="">
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php
      }
      } 
      ?>
</div> -->
<!-- <div class="form-group" style="float:left; margin:0px 50px 20px 20px;">
   <button type="button" class="addprodocitem btn-primary"><i class="fa fa-plus"></i></button>
</div> -->
<input type="hidden" name="cloopdoc" id="cloopdoc" value="<?php echo $cloopdoc; ?>">
<div class="row">
   <div class="col-sm-5">
   </div>
</div>
<?php if(!empty($channelsetting) && $channelsetting['partialpayment']==1){ 
   $received = 0;
   if(!empty($installmentdata)){ 
        //$key = array_search('1', array_column($installmentdata, 'status'));
        $key = false;
        $search = ['status' => 1];
        foreach ($installmentdata as $k => $v) {
            if ($v['status'] == $search['status']) {
                $key = true;
                // key found - break the loop
                break;
            }
        }
        if($key!=false){
            $received = 1;
        }else{
            $received = 0;
        }
   }
    ?>
<script>
   EMIreceived = <?=$received?>;
</script>
<!-- <div class="row">
   <div class="col-md-12">
      <div class="row" id="installmentsetting_div" style="<?php if(!empty($installmentdata) && HIDE_EMI==0){ echo "display: block;"; }else{ echo "display: none;"; } ?>">
         <div class="col-sm-2">
            <div class="form-group" id="noofinstallment_div">
               <div class="col-sm-12">
                  <label for="noofinstallment" class="control-label">No. of Installment</label>
                  <input type="text" class="form-control" id="noofinstallment" name="noofinstallment" maxlength="2" value="<?php if(!empty($installmentdata)){ echo count($installmentdata); } ?>" onkeypress="return isNumber(event)">
               </div>
            </div>
         </div>
         <div class="col-sm-3">
            <div class="form-group" id="emidate_div">
               <div class="col-sm-12">
                  <label for="emidate" class="control-label">EMI Start Date</label>
                  <input id="emidate" type="text" name="emidate" value="<?php if(!empty($installmentdata)){ echo $this->general_model->displaydate($installmentdata[0]['date']); } ?>" class="form-control" readonly>
               </div>
            </div>
         </div>
         <div class="col-sm-3">
            <div class="form-group" id="emiduration_div">
               <div class="col-sm-12">
                  <label for="emiduration" class="control-label">EMI Duration (In Days)</label>
                  <input id="emiduration" type="text" name="emiduration" value="<?php if(!empty($installmentdata)){ if(count($installmentdata)==1){ echo "1"; } else{ echo ceil(abs(strtotime($installmentdata[0]['date']) - strtotime($installmentdata[1]['date'])) / 86400);
                     } }?>" class="form-control" maxlength="4">
               </div>
            </div>
         </div>
         <div class="col-sm-3 pt-xxl">
            <div class="form-group">
               <div class="col-sm-12">
                  <button type="button" onclick="generateinstallment()" class="btn btn-primary btn-raised" <?php if($received==1 && !isset($isduplicate)){ echo "disabled"; } ?>>Generate</button>
               </div>
            </div>
         </div>
      </div>
   </div>
</div> -->
<!-- <div id="installmentmaindiv" style="margin-top: 12px;<?php if(HIDE_EMI==0){ echo "display:block;"; } else{ echo "display:none;"; } ?>">
   <div class="row" id="installmentmaindivheading" style="<?php if(!empty($installmentdata)){ echo "display: block;"; }else{ echo "display: none;"; } ?>">
      <div class="col-md-1 text-center"><b>Sr.No</b></div>
      <div class="col-md-2 text-center"><b>Installment (%)</b></div>
      <div class="col-md-2 text-center"><b>Amount</b></div>
      <div class="col-md-2 text-center"><b>Installment Date</b></div>
      <div class="col-md-2 text-center"><b>Payment Date</b></div>
      <div class="col-md-2 text-center"><b>Received Status</b></div>
   </div>
</div> -->
<!-- <div id="installmentdivs" style="<?php if(HIDE_EMI==0){ echo "display:block;"; } else{ echo "display:none;"; } ?>">
   <?php if(!empty($installmentdata)){ 
      for($i=0; $i < count($installmentdata); $i++){ ?>
   <input type="hidden" name="installmentid[]" value="<?=(!isset($isduplicate))?$installmentdata[$i]['id']:""?>">   
   <div class="row noofinstallmentdiv">
      <div class="col-md-1 text-center">
         <div class="form-group">
            <div class="col-sm-12"><?=($i+1)?></div>
         </div>
      </div>
      <div class="col-md-2 text-center">
         <div class="form-group">
            <div class="col-sm-12">
               <input type="text" id="percentage<?=($i+1)?>" value="<?=$installmentdata[$i]['percentage']?>" name="percentage[]" class="form-control text-right percentage"  div-id="<?=($i+1)?>" maxlength="5" onkeyup="return onlypercentage(this.id)" onkeypress="return decimal(event,this.id)" <?php if($received==1 && !isset($isduplicate)){ echo "readonly"; } ?>>
            </div>
         </div>
      </div>
      <div class="col-md-2 text-center">
         <div class="form-group">
            <div class="col-sm-12">
               <input type="text" id="installmentamount<?=($i+1)?>" value="<?=$installmentdata[$i]['amount']?>" name="installmentamount[]" class="form-control text-right installmentamount" div-id="<?=($i+1)?>" maxlength="5" onkeypress="return decimal(event,this.id);" readonly>
            </div>
         </div>
      </div>
      <div class="col-md-2 text-center">
         <div class="form-group">
            <div class="col-sm-12">
               <input type="text" id="installmentdate<?=($i+1)?>" value="<?=$this->general_model->displaydate($installmentdata[$i]['date'])?>" name="installmentdate[]" class="form-control installmentdate" div-id="<?=($i+1)?>" maxlength="5" <?php if($received==1 && !isset($isduplicate)){ echo "disabled"; } ?>>
            </div>
         </div>
      </div>
      <div class="col-md-2 text-center">
         <div class="form-group">
            <div class="col-sm-12">
               <input type="text" id="paymentdate<?=($i+1)?>" value="<?=($installmentdata[$i]['paymentdate']!='' && !isset($isduplicate))?$this->general_model->displaydate($installmentdata[$i]['paymentdate']):''?>" name="paymentdate[]" class="form-control paymentdate" div-id="<?=($i+1)?>" maxlength="5" <?php if($received==1 && !isset($isduplicate)){ echo "disabled"; } ?>>
            </div>
         </div>
      </div>
      <div class="col-md-2 text-center">
         <div class="form-group">
            <div class="col-sm-12">
               <div class="checkbox">
                  <input id="installmentstatus<?=($i+1)?>" type="checkbox" value="<?=$installmentdata[$i]['status']?>" name="installmentstatus<?=($i+1)?>" div-id="<?=($i+1)?>" class="checkradios" <?php if($received==1 && !isset($isduplicate)){ echo "disabled"; } ?> <?=($installmentdata[$i]['status']==1 && !isset($isduplicate))?"checked":""?> >
                  <label for="installmentstatus<?=($i+1)?>"></label>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php } ?>
   <?php } ?>
</div> -->

<?php } ?>
<input type="hidden" name="cloopdoc" id="cloopdoc" value="<?php echo $cloopdoc; ?>">

<!-- <div class="row">
   <div class="col-sm-5">
   </div>
</div> -->
<!-- <div class="col-md-12 form-group ml-n" id="AmountTotal">
   <div class="col-md-8 form-group ml-n mr-n"></div>
   <div class="col-md-4 pull-ight"> 
   </div>
</div> -->
<!-- <div class="col-md-12 mb-sm">
   <div class="form-group">
      <label for="focusedinput" class="col-sm-5 control-label"></label>
      <div class="col-sm-6">
         <?php if(!empty($quotationdata)){ ?>
         <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
         <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
         <?php }else{ ?>
         <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
         <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
         <?php } ?>
         <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>quotation" title=<?=cancellink_title?>><?=cancellink_text?></a>
      </div>
   </div>
</div> -->
</div>
</div>
</div>
</div>
</div>
<div class="modal fade" id="addbuyerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog" role="document" style="width: 535px;">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
               aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Search Buyer Party</h4>
         </div>
         <div class="modal-body" style="padding-top: 4px;">
            <div class="row">
               <div class="col-md-12">
                  <div class="form-group" id="buyercode_div">
                     <label class="col-sm-4 control-label" for="buyercode">Buyer Code <span
                        class="mandatoryfield">*</span></label>
                     <div class="col-md-6">
                        <input id="buyercode" type="text" name="buyercode" class="form-control"
                           value="">
                     </div>
                  </div>
               </div>
               <div class="col-sm-12 text-center">
                  <div class="form-group">
                     <input type="button" id="submit" onclick="searchmembercode()" name="submit" value="SEARCH" class="btn btn-primary btn-raised">
                     <a class="<?=cancellink_class;?>" href="javascript:voi(0)" title=<?=cancellink_title?>  data-dismiss="modal" aria-label="Close"><?=cancellink_text?></a>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer"></div>
      </div>
   </div>
</div>
</div> <!-- .container-fluid -->
</div> <!-- #page-content -->
<!--- Start Address Model -->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            <h4 class="modal-title">Add Address</h4>
         </div>
         <div class="modal-body pt-sm">
            <div class="col-md-6">
               <div class="form-group" id="baname_div">
                  <label class="col-sm-4 control-label" for="baname">Name <span class="mandatoryfield">*</span></label>
                  <div class="col-sm-8">
                     <input id="baname" type="text" name="baname" class="form-control" onkeypress="return onlyAlphabets(event)" value="">
                  </div>
               </div>
               <div class="form-group" id="baemail_div">
                  <label class="col-sm-4 control-label" for="baemail">Email
                  <span class="mandatoryfield">*</span></label>
                  <div class="col-sm-8">
                     <input id="baemail" type="text" name="baemail" class="form-control" value="">
                  </div>
               </div>
               <div class="form-group" id="baddress_div">
                  <label class="col-sm-4 control-label" for="baddress">Address <span
                     class="mandatoryfield">*</span></label>
                  <div class="col-sm-8">
                     <textarea id="baddress" name="baddress" value="" class="form-control"></textarea>
                  </div>
               </div>
               <div class="form-group" id="batown_div">
                  <label class="col-sm-4 control-label" for="batown">Town</label>
                  <div class="col-sm-8">
                     <input id="batown" type="text" name="batown" class="form-control" value="">
                  </div>
               </div>
               <div class="form-group" id="sameasbillingaddress_div">
                  <div class="checkbox col-md-10 col-md-offset-2 control-label">
                     <input type="checkbox" name="sameasbillingaddress" id="sameasbillingaddress" checked>
                     <label for="sameasbillingaddress">Use billing address as shipping address.</label>
                  </div>
               </div>
            </div>
            <div class="col-md-6">
               <div class="form-group" id="bapostalcode_div">
                  <label class="col-sm-4 control-label" for="bapostalcode">Postal Code
                  <span class="mandatoryfield">*</span></label>
                  <div class="col-sm-8">
                     <input id="bapostalcode" type="text" name="bapostalcode" class="form-control"
                        onkeypress="return isNumber(event)" value="">
                  </div>
               </div>
               <div class="form-group" id="bamobileno_div">
                  <label class="col-sm-4 control-label" for="bamobileno">Mobile No.
                  <span class="mandatoryfield">*</span></label>
                  <div class="col-sm-8">
                     <input id="bamobileno" type="text" name="bamobileno" class="form-control"
                        onkeypress="return isNumber(event)" maxlength="10" value="">
                  </div>
               </div>
               <div class="form-group" id="country_div">
                  <label class="col-sm-4 control-label" for="countryid">Country</label>
                  <div class="col-sm-8">
                     <select id="countryid" name="countryid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                        <option value="0">Select Country</option>
                        <?php foreach($countrydata as $country){ ?>
                        <option value="<?php echo $country['id']; ?>" <?php if(DEFAULT_COUNTRY_ID == $country['id']){ echo "selected"; } ?>><?php echo $country['name']; ?></option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
               <div class="form-group" id="province_div">
                  <label class="col-sm-4 control-label" for="provinceid">Province</label>
                  <div class="col-sm-8">
                     <select id="provinceid" name="provinceid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                        <option value="0">Select Province</option>
                     </select>
                  </div>
               </div>
               <div class="form-group" id="city_div">
                  <label class="col-sm-4 control-label" for="cityid">City</label>
                  <div class="col-sm-8">
                     <select id="cityid" name="cityid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                        <option value="0">Select City</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="col-md-12">
               <hr>
               <div class="form-group" style="text-align: center;">
                  <input type="button" id="addressbtn" onclick="memberaddresscheckvalidation()"
                     name="submit" value="ADD" class="btn btn-primary btn-raised">
                  <a href="javascript:voi(0)" class="btn btn-info btn-raised"
                     onclick="memberaddressresetdata()">RESET</a>
                  <a class="<?=cancellink_class;?>" href="javascript:voi(0)" title=<?=cancellink_title?>  data-dismiss="modal" aria-label="Close"><?=cancellink_text?></a>
               </div>
            </div>
         </div>
         <div class="modal-footer"></div>
      </div>
   </div>
</div>
<script>
   $(".addnewproductitem").click(function() {
   
       var count1 = $('#cloop').val();
       count1++;
       $('#cloop').val(count1);
       // CentreStock/cloop
       $.get('<?= base_url('rkinsite/Quotation/addnewproductitemcloop/')?>' + count1, null, function(result) {
           $("#addproductitem").append(result); // Or whatever you need to insert the result
       }, 'html');
   
   });
   $(".addprodocitem").click(function() {
       var count2 = $('#cloop').val();
       count2++;
       $('#cloop').val(count2);
       $.get('<?= base_url('rkinsite/Quotation/addprodocitem/')?>' + count2, null, function(result) {
           $("#adddocitem").append(result); 
       }, 'html');
   });
</script>
<!-- End Address Model -->