<?php    $productdiscount = 0; ?><script>
    var MemberID = '<?php if(isset($paymentreceiptdata)){ echo $paymentreceiptdata['memberid']; }else{ echo 0; } ?>';
    var cashorbankid = '<?php if(isset($paymentreceiptdata)){ echo $paymentreceiptdata['cashorbankid']; }else{ echo 0; } ?>';
    var method = '<?php if(isset($paymentreceiptdata)){ echo $paymentreceiptdata['method']; }else{ echo 0; } ?>';
    var invoiceoptions = "";
    var INVOICEID_ARR = [];
</script>
<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($paymentreceiptdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($paymentreceiptdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body pt-n">
		        	<div class="col-sm-12 col-md-12 col-lg-12 p-n">
					<form class="form-horizontal" id="paymentreceiptform">
                        <input id="paymentreceiptid" name="paymentreceiptid" value="<?php if(isset($paymentreceiptdata)){ echo $paymentreceiptdata['id']; }?>" type="hidden">
                        <input id="oldmemberid" name="oldmemberid" value="<?php if(isset($paymentreceiptdata)){ echo $paymentreceiptdata['memberid']; }?>" type="hidden">
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group" id="member_div">
                                    <div class="col-md-12">								
                                        <label class="control-label" for="memberid">Select Party <span class="mandatoryfield">*</span></label>
                                        <select id="memberid" name="memberid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="6" <?php if(isset($paymentreceiptdata)){ echo "disabled"; }?>>
                                            <option value="0">Select Party</option>
                                            <?php foreach($memberdata as $member){ ?>
                                                <option value="<?php echo $member['id']; ?>" <?php if(isset($paymentreceiptdata)){ if($paymentreceiptdata['memberid']==$member['id']){ echo "selected"; }} ?>><?php echo ucwords($member['namewithcodeormobile']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>	
                            <div class="col-md-4">				
                                <div class="form-group" id="transactiondate_div">
                                    <div class="col-sm-12">
                                        <label class="control-label" for="transactiondate">Transaction Date <span class="mandatoryfield">*</span></label>
                                        <input id="transactiondate" name="transactiondate"  type="text" class="form-control" value="<?php if(isset($paymentreceiptdata) && $paymentreceiptdata['transactiondate']!="0000-00-00"){ echo $this->general_model->displaydate($paymentreceiptdata['transactiondate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" readonly>
                                    </div>                
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" id="paymentreceiptno_div">
                                    <div class="col-md-12">								
                                        <label class="control-label" for="paymentreceiptno">Receipt No. <span class="mandatoryfield">*</span></label>
                                        <input id="paymentreceiptno" class="form-control" name="paymentreceiptno" value="<?php if(isset($paymentreceiptdata)){ echo $paymentreceiptdata['paymentreceiptno']; }else if(isset($paymentreceiptno)){ echo $paymentreceiptno; }?>"  type="text" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12"><hr></div>
                        </div>
                        
                        <div class="row">
                        <?php /* <div class="panel-heading"><h2>Transaction Details</h2></div>
                          <div class="col-md-10">
                                <div class="form-group">
                                    <label for="focusedinput" class="col-md-2 control-label" style="text-align: left;">Receipt Type</label>
                                    <div class="col-md-10">
                                        <div class="col-md-5 col-xs-4" style="padding-left: 0px;">
                                            <div class="radio">
                                                <input type="radio" name="isagainstreference" id="aps" value="1" <?php if(isset($paymentreceiptdata) && $paymentreceiptdata['isagainstreference']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                <label for="aps">Against Purchase / Service</label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-4">
                                            <div class="radio">
                                                <input type="radio" name="isagainstreference" id="onacc" value="2" <?php if(isset($paymentreceiptdata) && $paymentreceiptdata['isagainstreference']==2){ echo 'checked'; }?>>
                                                <label for="onacc">On Account</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            */?>
                            <div class="col-md-4">
                                <div class="form-group" id="cashorbankid_div">
                                    <div class="col-md-12">								
                                        <label class="control-label" for="cashorbankid">Cash / Bank Account <span class="mandatoryfield">*</span></label>
                                        <select id="cashorbankid" name="cashorbankid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="6">
                                            <option value="0">Select Cash / Bank Account</option>
                                            <?php 
                                            if(!empty($bankdata)){
                                                foreach($bankdata as $account){ ?> 
                                                <option value="<?=$account['id']?>" <?php if(isset($paymentreceiptdata)){ if($paymentreceiptdata['cashorbankid']==$account['id']){ echo "selected"; }} ?>><?=$account['bankname']?></option>
                                                <?php }
                                            }?>
                                        </select>
                                        <label class="control-label">Balance : <?=CURRENCY_CODE?> 0.00</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group" id="method_div">
                                    <div class="col-md-12">								
                                        <label class="control-label" for="method">Method <span class="mandatoryfield">*</span></label>
                                        <select id="method" name="method" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                            <option value="0">Select Method</option>
                                            <?php 
                                            foreach($this->Bankmethod as $key=>$value){ 
                                            ?>
                                                <option value="<?=$key?>" <?php if(isset($paymentreceiptdata)){ if($paymentreceiptdata['method']==$key){ echo "selected"; }} ?>><?=ucwords($value)?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" id="amount_div">
                                    <div class="col-md-12 text-right">								
                                        <label class="control-label" for="amount">Amount (<?=CURRENCY_CODE?>) <span class="mandatoryfield">*</span></label>
                                        <input type="text" id="amount" class="form-control text-right" name="amount" value="<?php if(isset($paymentreceiptdata)){ echo number_format($paymentreceiptdata['amount'],2,'.',''); }?>" onkeypress="return decimal_number_validation(event, this.value, 10)" <?php if(isset($paymentreceiptdata) && $paymentreceiptdata['isagainstreference']==2){ echo ""; }else{ echo "readonly"; } ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12"><hr></div>
                        </div>
                       
                        <?php /*
                        <div class="row" id="invoicedetailsdiv" style="<?php if(isset($paymentreceiptdata) && $paymentreceiptdata['isagainstreference']==2){ echo "display:none;"; }?>">
                            <!-- <div class="panel-heading"><h2>Invoice Details</h2></div> -->
                            <div class="row m-n">
                                <div class="col-md-3">
                                    <div class="form-group" id="invoice_div">
                                        <div class="col-md-12">								
                                            <label class="control-label" for="invoiceid">Select Invoice <span class="mandatoryfield">*</span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group text-right" id="amountdue_div">
                                        <div class="col-md-12">								
                                            <label class="control-label" for="amountdue">Amount Due (<?=CURRENCY_CODE?>)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group text-right" id="invoiceamount_div">
                                        <div class="col-md-12">								
                                            <label class="control-label" for="invoiceamount">Amount (<?=CURRENCY_CODE?>) <span class="mandatoryfield">*</span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group text-right" id="remainingamount_div">
                                        <div class="col-md-12">								
                                            <label class="control-label" for="remainingamount">Remaining Amount (<?=CURRENCY_CODE?>)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="removepaymentreceipttransactionsid" id="removepaymentreceipttransactionsid">
                            <?php if(isset($paymentreceiptdata) && !empty($receipttransactionsdata)) { ?>
                               
                            <?php }else{ ?>
                                <div class="countinvoice" id="countinvoice1">
                                    <div class="row m-n">
                                        <div class="col-md-3">
                                            <div class="form-group" id="invoice1_div">
                                                <div class="col-md-12">								
                                                    <select id="invoiceid1" name="invoiceid[]" class="selectpicker form-control invoiceid" data-live-search="true" data-select-on-tab="true" data-size="6">
                                                        <option value="0">Select Invoice</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="form-group" id="amountdue1_div">
                                                <div class="col-md-12">								
                                                    <input type="text" id="amountdue1" class="form-control text-right amountdue" value="" readonly>
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
    
                                        <div class="col-md-2">
                                            <div class="form-group" id="remainingamount1_div">
                                                <div class="col-md-12">								
                                                    <input type="text" id="remainingamount1" class="form-control text-right remainingamount" value="" readonly>
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
                            <div class="col-sm-12"><hr></div>
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
                                        <th>Select Invoice <span class="mandatoryfield">*</span></th>
                                        <th>Amount Due (<?=CURRENCY_CODE?>)<span class="mandatoryfield">*</span></th>
                                        <th class="text-right ">Remaining Amount (<?=CURRENCY_CODE?>)</th>
                                        <th class="text-right ">Amount (<?=CURRENCY_CODE?>)</th>
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
                                                <td <?php if($productdiscount==0){ echo "style='display:none;'"; }?>>
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
                                                            <option value="0">Select Invoice </option>
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
                                            <td <?php if($productdiscount==0){ echo "style='display:none;'"; }?>>
                                                <div class="form-group" id="discount1_div">
                                                    <div class="col-md-12">
                                                        <label for="discount1" class="control-label">Dis. (%)</label>
                                                        <input type="text" class="form-control discount" id="discount1" name="discount[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)">
                                                        <input type="hidden" value="" id="orderdiscount1">
                                                    </div>
                                                </div>
                                                <div class="form-group" id="discountinrs1_div">
                                                    <div class="col-md-12">
                                                        <label for="discountinrs1" class="control-label">Dis. (<?=CURRENCY_CODE?>)</label>
                                                        <input type="text" class="form-control discount" id="discount1" name="discount[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)">
                                                        <input type="hidden" value="" id="orderdiscount1">
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
                            <div class="col-md-4">
                                <div class="form-group" id="remarks_div">
                                    <div class="col-md-12">								
                                        <label class="control-label" for="remarks">Remarks</label>
                                        <textarea id="remarks" class="form-control" name="remarks"><?php if(isset($paymentreceiptdata)){ echo $paymentreceiptdata['remarks']; }?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <div class="form-group">
                                <?php if(isset($paymentreceiptdata)){ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                <?php }else{ ?>
                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & ADD NEW" class="btn btn-primary btn-raised">
                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->uri->segment(2)?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
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