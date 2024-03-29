<?php $productdiscount = 0; ?><?php 
   $DOCUMENT_TYPE_DATA = '';
   if(!empty($documenttypedata)){
   foreach($documenttypedata as $documenttype){
       $DOCUMENT_TYPE_DATA .= '<option value="'.$documenttype['id'].'">'.$documenttype['documenttype'].'</option>';
   } 
   }
   $LICENCE_TYPE_DATA = '';
   if(!empty($this->Licencetype)){
   foreach($this->Licencetype as $k=>$val){
       $LICENCE_TYPE_DATA .= '<option value="'.$k.'">'.$val.'</option>';
   } 
   }
   $cloop=1;
   ?>
<script>
   var DOCUMENT_TYPE_DATA = '<?=$DOCUMENT_TYPE_DATA?>';
   var LICENCE_TYPE_DATA = '<?=$LICENCE_TYPE_DATA?>';
   
   var countryid = '<?php if(isset($partydata)) { echo $partydata['countryid']; }else { echo '0'; } ?>';
   var provinceid = '<?php if(isset($partydata)) { echo $partydata['provinceid']; }else { echo '0'; } ?>';
   var cityid = '<?php if(isset($partydata)) { echo $partydata['cityid']; }else { echo '0'; } ?>';
</script>
<style>
   .panel-style {
   box-shadow: 0px 1px 6px #333 !important;
   margin-bottom: 20px;
   }
   .tal{
   text-align: left !important;
   };
</style>
<div class="page-content">
   <div class="page-heading">
      <h1><?php if (isset($partydata)) { echo 'Edit'; } else { echo 'Add'; } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></h1>
      <small>
         <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?= $this->session->userdata(base_url() . 'mainmenuname') ?></a></li>
            <li><a href="<?php echo ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl') ?>"><?= $this->session->userdata(base_url() . 'submenuname') ?></a>
            </li>
            <li class="active"><?php if (isset($partydata)) { echo 'Edit';} else { echo 'Add'; } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></li>
         </ol>
      </small>
   </div>
   <div class="container-fluid">
      <div data-widget-group="group1">
         <form class="form-horizontal" id="party-form" enctype="multipart/form-data">
            <input id="partyid" type="hidden" name="partyid" class="form-control" value="<?php if(isset($partydata)) { echo $partydata['id']; } ?>">
            <input id="base_url" type="hidden" value="<?=base_url()?>">
            <div class="panel panel-default border-panel">
               <div class="panel-body pt-xs">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group" id="party_div">
                           <label for="party" class="col-md-4 control-label">Party <span class="mandatoryfield"> *</span></label>
                           <div class="col-md-7">
                              <select id="party" name="party" class="selectpicker form-control" data-live-search="true" data-size="5">
                                 <option value="0">Select Party</option>
                                 <?php foreach ($Companydata as $Companyrow) { ?>
                                 <option value="<?php echo $Companyrow['id']; ?>" 
                                    <?php if (isset($partydata) && $partydata['party'] == $Companyrow['id']) { echo "selected"; } ?>><?php echo $Companyrow['companyname']; ?>
                                 </option>
                                 <?php } ?>
                              </select>
                           </div>
                           <div class="col-md-1 p-n" style="padding-top: 5px !important;">
                              <a href="javascript:void(0)" onclick="addcountry()" class="btn btn-primary btn-raised p-xs"><i class="material-icons" title="Add Unit">add</i></a>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group" id="gst_div">
                           <label class="col-md-4 col-sm-4 control-label" for="gst">Invoice No <span class="mandatoryfield"></span></label>
                           <div class="col-md-8 col-sm-8">
                              <input type="text" id="gst" class="form-control" name="gst" value="<?php if(isset($partydata)){ echo $partydata['gst']; }  ?>">
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group" id="pan_div">
                           <label class="col-md-4 col-sm-4 control-label" for="pan">Sales Order<span class="mandatoryfield"></span></label>
                           <div class="col-md-8 col-sm-8">
                              <input type="text" id="pan" class="form-control" name="pan" value="<?php if(isset($partydata)){ echo $partydata['pan']; }  ?>">
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group" id="invoicedate_div">
                           <label for="invoicedate" class="col-md-4 col-sm-4 control-label">Invoice Date<span class="mandatoryfield"></span></label>
                           <div class="col-md-8">
                              <input id="invoicedate" type="text" name="invoicedate" value="<?php if(isset($partydata)){ echo $partydata['partycode']; } ?>" class="form-control" readonly>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group" id="pan_div">
                           <label class="col-md-4 col-sm-4 control-label" for="pan">Billing Address <span class="mandatoryfield"></span></label>
                           <div class="col-md-8 col-sm-8">
                              <input type="text" id="pan" class="form-control" name="pan" value="<?php if(isset($partydata)){ echo $partydata['pan']; }  ?>">
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group" id="partycode_div">
                           <label for="partycode" class="col-md-4 col-sm-4 control-label">Invoice Format<span class="mandatoryfield"></span></label>
                           <div class="col-md-8">
                              <input id="partycode" type="text" name="partycode" value="<?php if(isset($partydata)){ echo $partydata['partycode']; } ?>" class="form-control">
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                     </div>
                     <div class="col-md-6">
                        <div class="form-group" id="partycode_div">
                           <label for="partycode" class="col-md-4 col-sm-4 control-label">Payment Due Date<span class="mandatoryfield"></span></label>
                           <div class="col-md-8">
                              <input id="partycode" type="text" name="partycode" value="<?php if(isset($partydata)){ echo $partydata['partycode']; } ?>" class="form-control date" readonly >
                           </div>
                        </div>
                     </div>
                     <div class="col-md-7">
                     </div>
                     <div class="col-md-5">
                        <div class="form-group" id="partycode_div">
                           <div class="col-md-8">
                              <input type="checkbox" value="1" class="" style="margin-left:22px;" id="checkbox4">
                              Multiple Party Order
                           </div>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  <?php /*
                  <div class="row">
                     <div class="col-md-12">
                        <div class="panel panel-default border-panel" id="conect_countdocuments1">
                           <div class="panel-heading">
                              <h2>Product Details</h2>
                           </div>
                           <div class="panel-body">
                              <div id="addtarget">
                                 <div class="row">
                                    <div class="col-md-2 pl-sm pr-sm visible-md visible-lg">
                                       <div class="form-group" id="firstname_div">
                                          <label for="firstname" class="col-md-12 control-label tal">Category <span class="mandatoryfield" > *</span></label>
                                       </div>
                                    </div>
                                    <div class="col-md-2 pl-sm pr-sm visible-md visible-lg">
                                       <div class="form-group" id="lastname_div">
                                          <label for="lastname" class="col-md-12 control-label tal">product <span class="mandatoryfield"> *</span></label>
                                       </div>
                                    </div>
                                    <div class="col-md-2 pl-sm pr-sm visible-md visible-lg">
                                       <div class="form-group" id="contactno_div">
                                          <label for="contactno" class="col-md-12 control-label tal">price <span class="mandatoryfield"> *</span></label>
                                       </div>
                                    </div>
                                    <div class="col-md-1 pl-sm pr-sm visible-md visible-lg">
                                       <div class="form-group" id="birthdate_div">
                                          <label for="birthdate" class="col-md-12 control-label tal">qty </label>
                                       </div>
                                    </div>
                                    <div class="col-md-1 pl-sm pr-sm visible-md visible-lg">
                                       <div class="form-group" id="anniversarydate_div">
                                          <label for="anniversarydate" class="col-md-12 control-label tal">discount(%)</label>
                                       </div>
                                    </div>
                                    <div class="col-md-1 pl-sm pr-sm visible-md visible-lg">
                                       <div class="form-group" id="anniversarydate_div">
                                          <label for="anniversarydate" class="col-md-12 control-label tal">Tax(%)</label>
                                       </div>
                                    </div>
                                    <div class="col-md-2 pl-sm pr-sm visible-md visible-lg">
                                       <div class="form-group" id="email_div">
                                          <label for="email" class="col-md-12 control-label tal">Amount <span class="mandatoryfield">*</span></label>
                                       </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <?php if(isset($paymentreceiptdata) && !empty($receipttransactionsdata)) { ?>
                                   
                                    <?php }else{ ?>
                                    <div class="countinvoice" id="countinvoice1">
                                       <div class="row m-n">
                                          <div class="col-md-2">
                                             <div class="form-group" id="category1_div">
                                                <div class="col-md-12">
                                                   <select id="category1" name="categoryid[]" class="selectpicker form-control categoryid" data-live-search="true" data-select-on-tab="true" data-size="6">
                                                      <option value="0">Select Category </option>
                                                   </select>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-md-2">
                                             <div class="form-group" id="product1_div">
                                                <div class="col-md-12">
                                                   <select id="product1" name="product[]" class="selectpicker form-control product" data-live-search="true" data-select-on-tab="true" data-size="6">
                                                      <option value="0">Select Product</option>
                                                   </select>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-md-2">
                                             <div class="form-group" id="invoiceamount1_div">
                                                <div class="col-md-12">								
                                                   <input type="text" id="invoiceamount1" class="form-control text-right invoiceamount" name="invoiceamount[]" value="" onkeypress="return decimal_number_validation(event, this.value, 10)">
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-md-1">
                                             <div class="form-group" id="remainingamount1_div">
                                                <div class="col-md-12">								
                                                   <input type="text" id="remainingamount1" class="form-control text-right remainingamount" value="" onkeypress="return isNumber(event, this.value, 10)">
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-md-1">
                                             <div class="form-group" id="productdiscount1_div">
                                                <div class="col-md-12">								
                                                   <input type="text" id="productdiscount1" class="form-control text-right productdiscount" value="" onkeypress="return decimal_number_validation(event, this.value, 10)" >
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-md-1">
                                             <div class="form-group" id="remainingamount1_div">
                                                <div class="col-md-12">								
                                                   <input type="text" id="remainingamount1" class="form-control text-right remainingamount" value="" >
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-md-1">
                                             <div class="form-group" id="productamount1_div">
                                                <div class="col-md-12">								
                                                   <input type="text" id="productamount1" class="form-control text-right remainingamount" value="" >
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-md-2 pt-md">
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
                        

                     </div>
                  </div>
                  */?>
                  <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div id="quotationproductdivs">
                        <table id="quotationproducttable" class="table table-hover table-bordered m-n">
                                <thead>
                                    <tr>
                                        <th>Select Category <span class="mandatoryfield">*</span></th>
                                        <th>Select Product <span class="mandatoryfield">*</span></th>
                                        <th class="width12">Price <span class="mandatoryfield">*</span></th>
                                        <th class="width8">Qty <span class="mandatoryfield">*</span></th>
                                        <th class="width8">Discount</th>
                                        <th class="text-right width8">Tax (%)</th>
                                        <th class="text-right width8">Amount (<?=CURRENCY_CODE?>)</th>
                                        <th class="width8">Action</th>
                                    </tr>
                                </thead>      
                                <tbody id="productdataforpurchase">
                                    <?php if(!empty($quotationdata) && !empty($quotationdata['quotationproduct'])) { ?>
                                        <input type="hidden" name="removequotationproductid" id="removequotationproductid">
                                        
                                        <?php for ($i=0; $i < count($quotationdata['quotationproduct']); $i++) { ?>
                                            <tr class="countproducts" id="quotationproductdiv<?=($i+1)?>">
                                                <td>
                                                    <input type="hidden" name="quotationproductsid[]" value="<?=(!isset($isduplicate))?$quotationdata['quotationproduct'][$i]['id']:""?>" id="quotationproductsid<?=$i+1?>">
                                                    <input type="hidden" name="producttax[]" value="<?=$quotationdata['quotationproduct'][$i]['tax']?>" id="producttax<?=$i+1?>">
                                                    <input type="hidden" name="productrate[]" value="<?=$quotationdata['quotationproduct'][$i]['price']?>" id="productrate<?=$i+1?>">
                                                    <input type="hidden" name="originalprice[]" value="<?=$quotationdata['quotationproduct'][$i]['originalprice']?>" id="originalprice<?=$i+1?>">
                                                    <input type="hidden" name="uniqueproduct[]" value="<?=$quotationdata['quotationproduct'][$i]['productid']."_".$quotationdata['quotationproduct'][$i]['priceid']?>" id="uniqueproduct<?=$i+1?>">
                                                    <input type="hidden" name="referencetype[]" id="referencetype<?=$i+1?>" value="<?=$quotationdata['quotationproduct'][$i]['referencetype']?>">
                                                    <div class="form-group" id="product<?=($i+1)?>_div">
                                                        <div class="col-sm-12">
                                                            <select id="productid<?=($i+1)?>" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                                <option value="0">Select Product</option>
                                                                <?php /* foreach($productdata as $product){ ?>
                                                                <option value="<?php echo $product['id']; ?>" <?php if($quotationdata['quotationproduct'][$i]['productid']==$product['id']){ echo "selected"; } ?>><?php echo $product['name']; ?></option>
                                                                <?php } */ ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="price<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                            <select id="priceid<?=($i+1)?>" name="priceid[]" data-width="90%" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                                <option value="">Select Variant</option>
                                                            </select>
                                                            <div class="form-group m-n p-n" id="applyoldprice<?=($i+1)?>_div">
                                                                <div class="col-sm-12">
                                                                    <div class="checkbox pt-n pl-xs text-left">
                                                                        <input id="applyoldprice<?=($i+1)?>" type="checkbox" value="0" class="checkradios applyoldprice" checked>   
                                                                        <label for="applyoldprice<?=($i+1)?>" class="control-label p-n">Apply Old Quotation Price : <span id="oldpricewithtax<?=($i+1)?>"><?=$quotationdata['quotationproduct'][$i]['pricewithtax']?></span></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="comboprice<?=($i+1)?>_div">
                                                        <div class="col-sm-12">
                                                            <select id="combopriceid<?=($i+1)?>" name="combopriceid[]" data-width="150px" class="selectpicker form-control combopriceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                                <option value="">Price</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" id="actualprice<?=($i+1)?>_div">
                                                        <div class="col-sm-12">
                                                            <label for="actualprice<?=($i+1)?>" class="control-label">Rate (<?=CURRENCY_CODE?>)</label>
                                                            <input type="text" class="form-control actualprice text-right" id="actualprice<?=($i+1)?>" name="actualprice[]" value="<?=$quotationdata['quotationproduct'][$i]['originalprice']?>" onkeypress="return decimal_number_validation(event, this.value);" style="display: block;" div-id="<?=($i+1)?>">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="qty<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                            <input type="text" class="form-control qty" id="qty<?=($i+1)?>" name="qty[]" value="<?=$quotationdata['quotationproduct'][$i]['quantity']?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="<?=($i+1)?>">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td >
                                                    <div class="form-group" id="discount<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                            <label for="discount<?=($i+1)?>" class="control-label">Dis. (%)</label>
                                                            <input type="text" class="form-control discount" id="discount<?=($i+1)?>" name="discount[]" value="<?=$quotationdata['quotationproduct'][$i]['discount']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)">
                                                            <input type="hidden" value="<?=$quotationdata['quotationproduct'][$i]['discount']?>" id="orderdiscount<?=$i+1?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group" id="discountinrs<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                            <label for="discountinrs<?=($i+1)?>" class="control-label">Dis. (<?=CURRENCY_CODE?>)</label>
                                                            <input type="text" class="form-control discountinrs" id="discountinrs<?=($i+1)?>" name="discountinrs[]" value="" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)">	
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="tax<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                            <input type="text" class="form-control text-right tax" id="tax<?=($i+1)?>" name="tax[]" value="<?=$quotationdata['quotationproduct'][$i]['tax']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)" <?php 
                                                            if($quotationdata['quotationdetail']['vendoredittaxrate']==1 && EDITTAXRATE==1){ 
                                                                echo ""; 
                                                            }else{ 
                                                                echo "readonly"; 
                                                            }?>>	
                                                            <input type="hidden" value="<?=$quotationdata['quotationproduct'][$i]['tax']?>" id="ordertax<?=$i+1?>">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="amount<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                        <input type="text" class="form-control amounttprice" id="amount<?=($i+1)?>" name="amount[]" value="" readonly="" div-id="<?=($i+1)?>">
                                                        <input type="hidden" class="producttaxamount" id="producttaxamount<?=($i+1)?>" name="producttaxamount[]" value="" div-id="<?=($i+1)?>">		
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group pt-sm">
                                                        <div class="col-md-12 pr-n">
                                                            <?php if($i==0){?>
                                                            <?php if(count($quotationdata['quotationproduct'])>1){ ?>
                                                                <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                            <?php }else { ?>
                                                                <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                            <?php } ?>

                                                        <?php }else if($i!=0) { ?>
                                                            <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(<?=$i+1?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                        <?php } ?>
                                                        <button type="button" class="btn btn-default btn-raised btn-sm add_remove_btn_product" onclick="removeproduct(<?=$i+1?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                                    
                                                        <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>  
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <script type="text/javascript">
                                                $(document).ready(function() {
                                                    oldproductid.push(<?=$quotationdata['quotationproduct'][$i]['productid']?>);
                                                    oldpriceid.push(<?=$quotationdata['quotationproduct'][$i]['priceid']?>);
                                                    oldtax.push(<?=$quotationdata['quotationproduct'][$i]['tax']?>);
                                                    productdiscount.push(<?=$quotationdata['quotationproduct'][$i]['discount']?>);
                                                    oldcombopriceid.push(<?=$quotationdata['quotationproduct'][$i]['referenceid']?>);
                                                    oldprice.push(<?=$quotationdata['quotationproduct'][$i]['originalprice']?>);

                                                    $("#qty<?=$i+1?>").TouchSpin(touchspinoptions);
                                                    getproduct(<?=$i+1?>);
                                                    getproductprice(<?=$i+1?>);
                                                    getmultiplepricebypriceid(<?=$i+1?>);
                                                    calculatediscount(<?=$i+1?>);
                                                    changeproductamount(<?=$i+1?>);
                                                });
                                            </script>
                                        <?php } ?>
                                    <?php }else{ ?>
                                        <tr class="countproducts" id="quotationproductdiv1">
                                            <td>
                                                <input type="hidden" name="producttax[]" id="producttax1">
                                                <input type="hidden" name="productrate[]" id="productrate1">
                                                <input type="hidden" name="originalprice[]" id="originalprice1">
                                                <input type="hidden" name="uniqueproduct[]" id="uniqueproduct1">
                                                <input type="hidden" name="referencetype[]" id="referencetype1">
                                                <div class="form-group" id="product1_div">
                                                    <div class="col-sm-12">
                                                        <select id="productid1" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                            <option value="0">Select Category</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="price1_div">
                                                    <div class="col-md-12">
                                                        <select id="priceid1" name="priceid[]" data-width="90%" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                            <option value="">Select Product</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="comboprice1_div">
                                                    <div class="col-sm-12">
                                                        <select id="combopriceid1" name="combopriceid[]" data-width="150px" class="selectpicker form-control combopriceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                            <option value="">Price</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="actualprice1_div">
                                                    <div class="col-sm-12">
                                                        <label for="actualprice1" class="control-label">Rate (<?=CURRENCY_CODE?>)</label>
                                                        <input type="text" class="form-control actualprice text-right" id="actualprice1" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value)" style="display: block;" div-id="1">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="qty1_div">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control qty" id="qty1" name="qty[]" value="" maxlength="6" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="1">
                                                    </div>
                                                </div>
                                            </td>
                                            <td >
                                                <div class="form-group" id="discountinrs1_div">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control discount" id="discount1" name="discount[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)">
                                                        <input type="hidden" value="" id="orderdiscount1">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="tax1_div">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control text-right tax" id="tax1" name="tax[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)" readonly>	
                                                        <input type="hidden" value="" id="ordertax1">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="amount1_div">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control amounttprice" id="amount1" name="amount[]" value="" readonly="" div-id="1">	
                                                        <input type="hidden" class="producttaxamount" id="producttaxamount1" name="producttaxamount[]" value="" div-id="1">		
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group pt-sm">
                                                    <div class="col-md-12 pr-n">
                                                    <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>		               
                                                <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                        </table>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>

                  <div class="row">
                     <div class="col-sm-4">
                        <div class="form-group" id="delivery_div">
                           <div class="col-sm-12">
                              <label for="deliverydate" class="control-label">Container Supplay Date <span class="mandatoryfield">*</span></label>
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
                              <input type="hidden" name="oldpaymenttype" id="oldpaymenttype" value="">
                              <label for="discount" class="control-label">Discount(%) <span class="mandatoryfield">*</span></label>
                              <input id="discount" type="text" name="discount" value="" class="form-control" >
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group" id="discountamount_div">
                           <div class="col-sm-12">
                              <label for="paymenttypeid" class="control-label">Discount Amount <span class="mandatoryfield">*</span></label>
                              <input id="discountamount" type="text" name="discountamount" value="" class="form-control" >
                           </div>
                        </div>
                     </div>
                

               <div class="col-md-6">
                  <div class="panel panel-default border-panel" id="commonpanel">
                     <div class="panel-body">
                        <div class="row" id="adddocrow">
                           <div class="clearfix"></div>
                           <?php if(isset($paymentreceiptdata) && !empty($receipttransactionsdata)) { ?>
                           <?php }else{ ?>
                           <div class="countinvoicec" id="countinvoicec1">
                              <div class="row m-n">
                                 <div class="col-md-5">
                                    <div class="form-group" id="invoiceamount1_div">
                                       <div class="col-md-12">								
                                          <select id="product'+ rowcount + '" name="product[]" class="selectpicker form-control product" data-live-search="true" data-select-on-tab="true" data-size="6">
                                             <option value="0">Extra Charges</option>
                                          </select>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-md-3 col-sm-3">
                                    
                                    <div class="col-md-12">
                                       <input type="text" id="invoiceamount'+ rowcount + '" class="form-control invoiceamount"  placeholder="" name="invoiceamount[]" value="" onkeypress="return decimal_number_validation(event, this.value, 10)">
                                    </div>
                                 
                                 </div>
                                 
                                 <div class="col-md-3 pt-md">
                                    <button type="button" class="btn btn-default btn-raised  remove_ex_btn m-n" onclick="removeex(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                    <button type="button" class="btn btn-default btn-raised add_ex_btn m-n" onclick="addnewex()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                 </div>
                              </div>
                           </div>
                           <?php } ?>
                        </div>
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

                  <div class="col-sm-12 p-n">
                           <hr>
                           <div class="panel-heading p-n"><h2>Upload Purchase Quotation Documents</h2></div>
                           <div class="row m-n">
                              <div class="col-md-6 p-n" id="filesheading1"> 
                                 <div class="col-md-7">
                                       <div class="form-group">
                                          <div class="col-md-12 pl-n">
                                             <label class="control-label">Select File</label>
                                          </div>
                                       </div>
                                 </div>
                                 <div class="col-md-3">
                                       <div class="form-group">
                                          <label class="control-label">Documents Name</label>
                                       </div>
                                 </div>
                              </div>
                              <div class="col-md-6 p-n" id="filesheading2" style="<?php if(!empty($quotationattachment) && count($quotationattachment)>1) { echo "display:block;"; }else{ echo "display:none;"; }?>"> 
                                 <div class="col-md-7">
                                       <div class="form-group">
                                          <div class="col-md-12 pl-n">
                                             <label class="control-label">Select File</label>
                                          </div>
                                       </div>
                                 </div>
                                 <div class="col-md-3">
                                       <div class="form-group">
                                          <label class="control-label">Documents Name</label>
                                       </div>
                                 </div>
                              </div>
                           </div>
                           <?php if(!empty($quotationdata) && !empty($quotationattachment)) { ?>
                              <input type="hidden" name="removetransactionattachmentid" id="removetransactionattachmentid">
                              <?php for ($i=0; $i < count($quotationattachment); $i++) { ?>
                                 <div class="col-md-6 p-n countfiles" id="countfiles<?=$i+1?>">
                                       <input type="hidden" name="transactionattachmentid<?=$i+1?>" value="<?=$quotationattachment[$i]['id']?>" id="transactionattachmentid<?=$i+1?>">
                                       <div class="col-md-7">
                                          <div class="form-group" id="file<?=$i+1?>_div">
                                             <div class="col-md-12 pl-n">
                                                   <div class="input-group" id="fileupload<?=$i+1?>">
                                                      <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                         <span class="btn btn-primary btn-raised btn-file"><i
                                                                  class="fa fa-upload"></i>
                                                               <input type="file" name="file<?=$i+1?>"
                                                                  class="file" id="file<?=$i+1?>"
                                                                  accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png,.doc,.docx,.pdf" onchange="validattachmentfile($(this),'file<?=$i+1?>',this)">
                                                         </span>
                                                      </span>
                                                      <input type="text" readonly="" id="Filetext<?=$i+1?>"
                                                         class="form-control" name="Filetext[]" value="<?=$quotationattachment[$i]['filename']?>">
                                                   </div>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-md-3">
                                          <div class="form-group" id="fileremarks<?=$i+1?>_div">
                                             <input type="text" class="form-control" name="fileremarks<?=$i+1?>" id="fileremarks<?=$i+1?>" value="<?=$quotationattachment[$i]['remarks']?>">
                                          </div>
                                       </div>
                                       <div class="col-md-2 pl-sm pr-sm mt-md">
                                          <?php if($i==0){?>
                                             <?php if(count($quotationattachment)>1){ ?>
                                                   <button type="button" class="btn btn-default btn-raised remove_file_btn" onclick="removeattachfile(1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                             <?php }else { ?>
                                                   <button type="button" class="btn btn-default btn-raised add_file_btn" onclick="addattachfile()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                             <?php } ?>

                                          <?php }else if($i!=0) { ?>
                                             <button type="button" class="btn btn-default btn-raised remove_file_btn" onclick="removeattachfile(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                          <?php } ?>
                                          <button type="button" class="btn btn-default btn-raised btn-sm remove_file_btn" onclick="removeattachfile(<?=$i+1?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                       
                                          <button type="button" class="btn btn-default btn-raised add_file_btn" onclick="addattachfile()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button> 
                                       </div>
                                 </div>
                              <?php } ?>
                           <?php }else{ ?>
                              <div class="col-md-6 p-n countfiles" id="countfiles1"> 
                                 <div class="col-md-7">
                                       <div class="form-group" id="file1_div">
                                          <div class="col-md-12 pl-n">
                                             <div class="input-group" id="fileupload1">
                                                   <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                      <span class="btn btn-primary btn-raised btn-file"><i
                                                               class="fa fa-upload"></i>
                                                         <input type="file" name="file1"
                                                               class="file" id="file1"
                                                               accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png,.doc,.docx,.pdf" onchange="validattachmentfile($(this),'file1',this)">
                                                      </span>
                                                   </span>
                                                   <input type="text" readonly="" id="Filetext1"
                                                      class="form-control" name="Filetext[]" value="">
                                             </div>
                                          </div>
                                       </div>
                                 </div>
                                 <div class="col-md-3">
                                       <div class="form-group" id="fileremarks1_div">
                                          <input type="text" class="form-control" name="fileremarks1" id="fileremarks1" value="">
                                       </div>
                                 </div>
                                 <div class="col-md-2 pl-sm pr-sm mt-md">
                                       <button type="button" class="btn btn-default btn-raised remove_file_btn m-n" onclick="removeattachfile(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>

                                       <button type="button" class="btn btn-default btn-raised add_file_btn m-n" onclick="addattachfile()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                 </div>
                              </div>
                           <?php } ?>
                     </div>

               </div>
            </div>
          
            <?php /*                        
            <div class="row">
               <div class="col-md-12">
                  <div class="panel panel-default border-panel" id="commonpanel">
                     <div class="panel-heading">
                        <h2>Document Detail</h2>
                     </div>
                     <div class="panel-body">
                        <div class="row" id="adddocrow">
                           <div class="col-md-12">
                              <div class="col-md-6 pl-sm pr-sm visible-md visible-lg">
                                 <div class="col-md-5">
                                    <div class="form-group">
                                       <div class="col-md-12 pl-xs pr-xs">
                                          <label class="control-label" style="text-align:left;">Document Name <span class="mandatoryfield">*</span></label>
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
                           <div class="clearfix"></div>
                              <?php if(isset($paymentreceiptdata) && !empty($receipttransactionsdata)){?>
                             
                              <?php }else{ ?>
                              <div class="countinvoiceb" id="countinvoiceb1">
                                 <div class="row m-n">
                                    
                                    <div class="col-md-3">
                                       <div class="form-group" id="documentname1_div">
                                          <div class="col-md-12">								
                                             <input type="text" id="documentname1" placeholder="Enter Document Name" class="form-control invoiceamount" name="documentname[]" value="" >
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                       <div class="form-group" id="docfile' + rowcount + '">
                                             <div class="col-sm-12 pr-xs pl-xs">
                                                <input type="hidden" id="isvaliddocfile' + rowcount + '" value="0">
                                                <input type="hidden" name="olddocfile[' + rowcount + ']" id="olddocfile' + rowcount + '" value="">
                                                <div class="input-group" id="fileupload' + rowcount + '">
                                                   <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                         <span class="btn btn-primary btn-raised btn-file">
                                                         <i class="fa fa-upload"></i>
                                                            <input type="file" name="olddocfile_' + rowcount + '" class="docfile" id="olddocfile_' + rowcount + '" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),&apos;docfile' + rowcount + '&apos;)">
                                                         </span>
                                                   </span>
                                                   <input type="text" readonly="" placeholder="Enter File" id="Filetextdocfile' + rowcount + '" class="form-control docfile" name="Filetextdocfile_' + rowcount + '" value="">
                                                </div>
                                             </div>
                                       </div>
                                    </div>
                                   
                                    <div class="col-md-1 pt-md">
                                       <button type="button" class="btn btn-danger btn-raised  remove_doc_btn m-n" onclick="removedoc(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                       <button type="button" class="btn btn-primary btn-raised add_doc_btn m-n" onclick="addnewdoc()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                    </div>
                                 </div>
                              </div>
                              <?php } ?>





                        </div>
                     </div>
                  </div>
               </div>
            </div> */?>

                     


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
                                 <input type="button" id="submit" onclick="checkvalidation(2)" name="submit" value="SAVE & PRINT" class="btn btn-primary btn-raised">
                                 <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                                 <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                 <?php } else { ?>
                                 <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                 <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & PRINT" class="btn btn-primary btn-raised">
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
            <input type="hidden" name="edit_country" id="edit_country" value="<?php if (isset($partydata)) { echo $partydata['countryid']; } ?>">
            <input type="hidden" name="edit_provinceid" id="edit_provinceid" value="<?php if (isset($partydata)) { echo $partydata['provinceid']; } ?>">
            <input type="hidden" name="edit_cityid" id="edit_cityid" value="<?php if (isset($partydata)) { echo $partydata['cityid']; } ?>">
         </form>
      </div>
   </div>
   <!-- model code -->
   <div class="modal addunit" id="addcompanyModal" style="overflow-y: auto;">
      <div class="modal-dialog" role="document" style="width: 600px;">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
               <h4 class="modal-title" id="post_title">Add Company</h4>
            </div>
            <div class="modal-body no-padding"></div>
         </div>
      </div>
   </div>
   <!-- model code -->
   <!-- model code -->
   <div class="modal addunit" id="addpartytypeModal" style="overflow-y: auto;">
      <div class="modal-dialog" role="document" style="width: 600px;">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
               <h4 class="modal-title" id="post_title">Add Party Type</h4>
            </div>
            <div class="modal-body2 no-padding"></div>
         </div>
      </div>
   </div>
   <!-- model code -->
</div>
<script>
   function addcountry() {
      var uurl = SITE_URL + "Company/addcompanymodal";
      $.ajax({
         url: uurl,
         type: 'POST',
         //async: false,
         beforeSend: function() {
            $('.mask').show();
            $('#loader').show();
         },
         success: function(response) {
            $("#addcompanyModal").modal("show");
            $(".modal-body").html(response);
            include('<?=ADMIN_JS_URL?>pages/Add_company.js', function() {
            });
         },
         error: function(xhr) {
            //alert(xhr.responseText);
         },
         complete: function() {
            $('.mask').hide();
            $('#loader').hide();
         },
   
      });
   }
   function addpartytype() {
      var uurl = SITE_URL + "Party_type/addpartytypemodal";
      $.ajax({
         url: uurl,
         type: 'POST',
         //async: false,
         beforeSend: function() {
            $('.mask').show();
            $('#loader').show();
         },
         success: function(response) {
            $("#addpartytypeModal").modal("show");
            $(".modal-body2").html(response);
            include('<?=ADMIN_JS_URL?>pages/add_party_type.js', function() {
            });
         },
         error: function(xhr) {
            //alert(xhr.responseText);
         },
         complete: function() {
            $('.mask').hide();
            $('#loader').hide();
         },
   
      });
   }
   $(document).ready(function() {
      $('#checkbox1').on('change', function() {
         var checked = this.checked
         
         if(checked==true){
            var billingaddress = $('#billingaddress').val();
            var shippingaddress = $('#shippingaddress').val(billingaddress);
           
         }else if(checked==false){
            $('#shippingaddress').val('');
         }
         
      });
      $('#checkbox2').on('change', function() {
         var checked = this.checked
         
         if(checked==true){
            var billingaddress = $('#billingaddress').val();
            $('#courieraddress').val(billingaddress);
         }else if(checked==false){
            $('#courieraddress').val('');
         }
         
      });
      $('#checkbox3').on('change', function() {
         var checked = this.checked
         if(checked==true){
            var shippingaddress = $('#shippingaddress').val();
            $('#courieraddress').val(shippingaddress);
         }else if(checked==false){
            $('#courieraddress').val('');
         }
      });
   
      $("#password_div").hide();
      $('#checkbox4').on('change', function() {
         var checked = this.checked
         if(checked==true){
            $("#password_div").show();
         }else if(checked==false){
            $("#password_div").hide();
         }
      });
   
      $('#checkbox3').on('change', function() {
         var checked = this.checked
         if(checked==true){
            $( "#checkbox2" ).prop( "checked", false );
            $( "#checkbox3" ).prop( "checked", true );
         }else if(checked==false){
            $( "#checkbox3" ).prop( "checked", false );
            
         }
      });
      $('#checkbox2').on('change', function() {
         var checked = this.checked
         if(checked==true){
            $( "#checkbox3" ).prop( "checked", false );
         }
      });
   
   
     
   });
   
</script>