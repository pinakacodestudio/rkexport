<script>
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
                                <?php /* for ($i=0; $i < count($receipttransactionsdata); $i++) { ?>
                                    <div class="countinvoice" id="countinvoice<?=$i+1?>">
                                        <input type="hidden" name="paymentreceipttransactionsid[]" value="<?=$receipttransactionsdata[$i]['id']?>" id="paymentreceipttransactionsid<?=$i+1?>">
                                        <div class="row m-n">
                                            <div class="col-md-3">
                                                <div class="form-group" id="invoice<?=$i+1?>_div">
                                                    <div class="col-md-12">								
                                                        <select id="invoiceid<?=$i+1?>" name="invoiceid[]" class="selectpicker form-control invoiceid" data-live-search="true" data-select-on-tab="true" data-size="6">
                                                            <option value="0">Select Invoice</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-2">
                                                <div class="form-group" id="amountdue<?=$i+1?>_div">
                                                    <div class="col-md-12">								
                                                        <input type="text" id="amountdue<?=$i+1?>" class="form-control text-right amountdue" value="" readonly>
                                                    </div>
                                                </div>
                                            </div>
        
                                            <div class="col-md-2">
                                                <div class="form-group" id="invoiceamount<?=$i+1?>_div">
                                                    <div class="col-md-12">								
                                                        <input type="text" id="invoiceamount<?=$i+1?>" class="form-control text-right invoiceamount" name="invoiceamount[]" value="<?php echo number_format($receipttransactionsdata[$i]['amount'],2,'.',''); ?>" onkeypress="return decimal_number_validation(event, this.value, 10)">
                                                    </div>
                                                </div>
                                            </div>
        
                                            <div class="col-md-2">
                                                <div class="form-group" id="remainingamount<?=$i+1?>_div">
                                                    <div class="col-md-12">								
                                                        <input type="text" id="remainingamount<?=$i+1?>" class="form-control text-right remainingamount" value="" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 pt-md">
                                                <?php if($i==0){?>
                                                    <?php if(count($receipttransactionsdata)>1){ ?>
                                                        <button type="button" class="btn btn-default btn-raised remove_invoice_btn m-n" onclick="removetransaction(1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                    <?php }else { ?>
                                                        <button type="button" class="btn btn-default btn-raised add_invoice_btn m-n" onclick="addnewinvoicetransaction()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                    <?php } ?>

                                                <? }else if($i!=0) { ?>
                                                    <button type="button" class="btn btn-default btn-raised remove_invoice_btn m-n" onclick="removetransaction(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                <? } ?>
                                                <button type="button" class="btn btn-default btn-raised btn-sm remove_invoice_btn m-n" onclick="removetransaction(<?=$i+1?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                            
                                                <button type="button" class="btn btn-default btn-raised add_invoice_btn m-n" onclick="addnewinvoicetransaction()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button> 
                                            </div>
                                        </div>
                                        <script>
                                            $(document).ready(function() {
                                                INVOICEID_ARR.push(<?=$receipttransactionsdata[$i]['invoiceid']?>);
                                            });
                                        </script>
                                    </div>
                                <?php } */?>
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