<?php
$ADMINID = $this->session->userdata[base_url().'ADMINID'];
?>
<div class="page-content">
    <div class="page-heading">            
        <h1><?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
                    <div class="col-sm-12">
                        <form class="form-horizontal" id="companytransactionprefixform" name="companytransactionprefixform" method="post">   
                            <input type="hidden" id="defaulttransactionid" value="<?=time().$ADMINID.rand(10,99).rand(10,99)?>">
                            <div class="row">  
                                <div class="col-sm-6">                                          
                                    <div class="form-group" id="channel_div">
                                        <label class="col-md-4 control-label" for="channelid">Select Channel</label>
                                        <div class="col-md-8">
                                            <select id="channelid" name="channelid" class="selectpicker form-control" data-live-search="true" data-size="8">
                                                <option value="0">Company</option>
                                                <?php foreach($channeldata as $channel){?>
                                                    <option value="<?=$channel['id']?>"><?=ucwords($channel['name'])?></option>
                                                <?php } ?>
                                            </select>
                                        </div>                                                
                                    </div> 
                                </div> 
                                <div class="col-sm-6">
                                    <div class="form-group" id="member_div">
                                        <label class="col-md-4 control-label" for="memberid">Select <?=Member_label?></label>
                                        <div class="col-md-8">
                                            <select id="memberid" name="memberid" class="selectpicker form-control" data-live-search="true" data-size="8">
                                                <option value="0">Select <?=Member_label?></option>
                                            </select>
                                        </div>                                                
                                    </div> 
                                </div> 
                            </div> 
                            <div class="row">  
                                <div class="col-sm-12"><hr></div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-6">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">Sales Quotation Prefix</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Last No.</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Suffix Length</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="yesno" style="margin-top: 5px;">
                                                    <input type="checkbox" id="quotationprefix" name="quotationprefix" value="1" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group" id="quotationprefixformat_div">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="quotationprefixformat" value="" id="quotationprefixformat" class="form-control transactionformat" data-id="quotationprefix">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="quotationprefixlastno" value="" id="quotationprefixlastno" class="form-control transactionformat" data-id="quotationprefix" onkeypress="return isNumber(event)">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <select id="quotationprefixsuffixlength" name="quotationprefixsuffixlength" class="selectpicker form-control suffixlength" data-id="quotationprefix">
                                                    <option value="1">1</option> 
                                                    <option value="2">2</option> 
                                                    <option value="3">3</option> 
                                                    <option value="4">4</option> 
                                                    <option value="5">5</option> 
                                                    <option value="6">6</option> 
                                                </select>                             
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm pt-sm" style="font-size: 14px;">
                                                <span id="quotationprefixpreview" class="preview"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">
                                    <div class="col-sm-8 col-sm-offset-1 pl-sm pr-sm">
                                        <button type="button" id="quotationprefixbtn1" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YYYY-YY','quotationprefix')">Financial Year (<?=date("Y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="quotationprefixbtn2" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YY-YY','quotationprefix')">Financial Year (<?=date("y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="quotationprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('autonumber','quotationprefix')">Auto Number</button>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">  
                                <div class="col-sm-12"><hr></div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-6">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">Purchase Quotation Prefix</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Last No.</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Suffix Length</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="yesno" style="margin-top: 5px;">
                                                    <input type="checkbox" id="purchasequotationprefix" name="purchasequotationprefix" value="1" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group" id="purchasequotationprefixformat_div">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="purchasequotationprefixformat" value="" id="purchasequotationprefixformat" class="form-control transactionformat" data-id="purchasequotationprefix">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="purchasequotationprefixlastno" value="" id="purchasequotationprefixlastno" class="form-control transactionformat" data-id="purchasequotationprefix" onkeypress="return isNumber(event)">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <select id="purchasequotationprefixsuffixlength" name="purchasequotationprefixsuffixlength" class="selectpicker form-control suffixlength" data-id="purchasequotationprefix">
                                                    <option value="1">1</option> 
                                                    <option value="2">2</option> 
                                                    <option value="3">3</option> 
                                                    <option value="4">4</option> 
                                                    <option value="5">5</option> 
                                                    <option value="6">6</option> 
                                                </select>                             
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm pt-sm" style="font-size: 14px;">
                                                <span id="purchasequotationprefixpreview" class="preview"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">
                                    <div class="col-sm-8 col-sm-offset-1 pl-sm pr-sm">
                                        <button type="button" id="purchasequotationprefixbtn1" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YYYY-YY','purchasequotationprefix')">Financial Year (<?=date("Y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="purchasequotationprefixbtn2" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YY-YY','purchasequotationprefix')">Financial Year (<?=date("y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="purchasequotationprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('autonumber','purchasequotationprefix')">Auto Number</button>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">  
                                <div class="col-sm-12"><hr></div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-6">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">Sales Order Prefix</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Last No.</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Suffix Length</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="yesno" style="margin-top: 5px;">
                                                    <input type="checkbox" id="orderprefix" name="orderprefix" value="1" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group" id="orderprefixformat_div">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="orderprefixformat" value="" id="orderprefixformat" class="form-control transactionformat" data-id="orderprefix">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="orderprefixlastno" value="" id="orderprefixlastno" class="form-control transactionformat" data-id="orderprefix" onkeypress="return isNumber(event)">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <select id="orderprefixsuffixlength" name="orderprefixsuffixlength" class="selectpicker form-control suffixlength" data-id="orderprefix">
                                                    <option value="1">1</option> 
                                                    <option value="2">2</option> 
                                                    <option value="3">3</option> 
                                                    <option value="4">4</option> 
                                                    <option value="5">5</option> 
                                                    <option value="6">6</option> 
                                                </select>                             
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm pt-sm" style="font-size: 14px;">
                                                <span id="orderprefixpreview" class="preview"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">
                                    <div class="col-sm-8 col-sm-offset-1 pl-sm pr-sm">
                                        <button type="button" id="orderprefixbtn1" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YYYY-YY','orderprefix')">Financial Year (<?=date("Y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="orderprefixbtn2" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YY-YY','orderprefix')">Financial Year (<?=date("y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="orderprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('autonumber','orderprefix')">Auto Number</button>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">  
                                <div class="col-sm-12"><hr></div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-6">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">Purchase Order Prefix</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Last No.</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Suffix Length</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="yesno" style="margin-top: 5px;">
                                                    <input type="checkbox" id="purchaseorderprefix" name="purchaseorderprefix" value="1" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group" id="purchaseorderprefixformat_div">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="purchaseorderprefixformat" value="" id="purchaseorderprefixformat" class="form-control transactionformat" data-id="purchaseorderprefix">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="purchaseorderprefixlastno" value="" id="purchaseorderprefixlastno" class="form-control transactionformat" data-id="purchaseorderprefix" onkeypress="return isNumber(event)">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <select id="purchaseorderprefixsuffixlength" name="purchaseorderprefixsuffixlength" class="selectpicker form-control suffixlength" data-id="purchaseorderprefix">
                                                    <option value="1">1</option> 
                                                    <option value="2">2</option> 
                                                    <option value="3">3</option> 
                                                    <option value="4">4</option> 
                                                    <option value="5">5</option> 
                                                    <option value="6">6</option> 
                                                </select>                             
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm pt-sm" style="font-size: 14px;">
                                                <span id="purchaseorderprefixpreview" class="preview"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">
                                    <div class="col-sm-8 col-sm-offset-1 pl-sm pr-sm">
                                        <button type="button" id="purchaseorderprefixbtn1" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YYYY-YY','purchaseorderprefix')">Financial Year (<?=date("Y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="purchaseorderprefixbtn2" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YY-YY','purchaseorderprefix')">Financial Year (<?=date("y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="purchaseorderprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('autonumber','purchaseorderprefix')">Auto Number</button>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">  
                                <div class="col-sm-12"><hr></div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-6">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">Sales Invoice Prefix</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Last No.</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Suffix Length</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="yesno" style="margin-top: 5px;">
                                                    <input type="checkbox" id="invoiceprefix" name="invoiceprefix" value="1" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group" id="invoiceprefixformat_div">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="invoiceprefixformat" value="" id="invoiceprefixformat" class="form-control transactionformat" data-id="invoiceprefix">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="invoiceprefixlastno" value="" id="invoiceprefixlastno" class="form-control transactionformat" data-id="invoiceprefix" onkeypress="return isNumber(event)">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <select id="invoiceprefixsuffixlength" name="invoiceprefixsuffixlength" class="selectpicker form-control suffixlength" data-id="invoiceprefix">
                                                    <option value="1">1</option> 
                                                    <option value="2">2</option> 
                                                    <option value="3">3</option> 
                                                    <option value="4">4</option> 
                                                    <option value="5">5</option> 
                                                    <option value="6">6</option> 
                                                </select>                             
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm pt-sm" style="font-size: 14px;">
                                                <span id="invoiceprefixpreview" class="preview"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">
                                    <div class="col-sm-8 col-sm-offset-1 pl-sm pr-sm">
                                        <button type="button" id="invoiceprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YYYY-YY','invoiceprefix')">Financial Year (<?=date("Y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="invoiceprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YY-YY','invoiceprefix')">Financial Year (<?=date("y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="invoiceprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('autonumber','invoiceprefix')">Auto Number</button>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">  
                                <div class="col-sm-12"><hr></div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-6">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">Purchase Invoice Prefix</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Last No.</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Suffix Length</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="yesno" style="margin-top: 5px;">
                                                    <input type="checkbox" id="purchaseinvoiceprefix" name="purchaseinvoiceprefix" value="1" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group" id="purchaseinvoiceprefixformat_div">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="purchaseinvoiceprefixformat" value="" id="purchaseinvoiceprefixformat" class="form-control transactionformat" data-id="purchaseinvoiceprefix">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="purchaseinvoiceprefixlastno" value="" id="purchaseinvoiceprefixlastno" class="form-control transactionformat" data-id="purchaseinvoiceprefix" onkeypress="return isNumber(event)">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <select id="purchaseinvoiceprefixsuffixlength" name="purchaseinvoiceprefixsuffixlength" class="selectpicker form-control suffixlength" data-id="purchaseinvoiceprefix">
                                                    <option value="1">1</option> 
                                                    <option value="2">2</option> 
                                                    <option value="3">3</option> 
                                                    <option value="4">4</option> 
                                                    <option value="5">5</option> 
                                                    <option value="6">6</option> 
                                                </select>                             
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm pt-sm" style="font-size: 14px;">
                                                <span id="purchaseinvoiceprefixpreview" class="preview"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">
                                    <div class="col-sm-8 col-sm-offset-1 pl-sm pr-sm">
                                        <button type="button" id="purchaseinvoiceprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YYYY-YY','purchaseinvoiceprefix')">Financial Year (<?=date("Y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="purchaseinvoiceprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YY-YY','purchaseinvoiceprefix')">Financial Year (<?=date("y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="purchaseinvoiceprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('autonumber','purchaseinvoiceprefix')">Auto Number</button>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">  
                                <div class="col-sm-12"><hr></div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-6">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">Sales Credit Note Prefix</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Last No.</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Suffix Length</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="yesno" style="margin-top: 5px;">
                                                    <input type="checkbox" id="creditnoteprefix" name="creditnoteprefix" value="1" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group" id="creditnoteprefixformat_div">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="creditnoteprefixformat" value="" id="creditnoteprefixformat" class="form-control transactionformat" data-id="creditnoteprefix">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="creditnoteprefixlastno" value="" id="creditnoteprefixlastno" class="form-control transactionformat" data-id="creditnoteprefix" onkeypress="return isNumber(event)">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <select id="creditnoteprefixsuffixlength" name="creditnoteprefixsuffixlength" class="selectpicker form-control suffixlength" data-id="creditnoteprefix">
                                                    <option value="1">1</option> 
                                                    <option value="2">2</option> 
                                                    <option value="3">3</option> 
                                                    <option value="4">4</option> 
                                                    <option value="5">5</option> 
                                                    <option value="6">6</option> 
                                                </select>                             
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm pt-sm" style="font-size: 14px;">
                                                <span id="creditnoteprefixpreview" class="preview"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">
                                    <div class="col-sm-8 col-sm-offset-1 pl-sm pr-sm">
                                        <button type="button" id="creditnoteprefixbtn1" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YYYY-YY','creditnoteprefix')">Financial Year (<?=date("Y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="creditnoteprefixbtn2" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YY-YY','creditnoteprefix')">Financial Year (<?=date("y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="creditnoteprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('autonumber','creditnoteprefix')">Auto Number</button>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">  
                                <div class="col-sm-12"><hr></div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-6">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">Purchase Credit Note Prefix</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Last No.</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Suffix Length</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="yesno" style="margin-top: 5px;">
                                                    <input type="checkbox" id="purchasecreditnoteprefix" name="purchasecreditnoteprefix" value="1" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group" id="purchasecreditnoteprefixformat_div">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="purchasecreditnoteprefixformat" value="" id="purchasecreditnoteprefixformat" class="form-control transactionformat" data-id="purchasecreditnoteprefix">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="purchasecreditnoteprefixlastno" value="" id="purchasecreditnoteprefixlastno" class="form-control transactionformat" data-id="purchasecreditnoteprefix" onkeypress="return isNumber(event)">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <select id="purchasecreditnoteprefixsuffixlength" name="purchasecreditnoteprefixsuffixlength" class="selectpicker form-control suffixlength" data-id="purchasecreditnoteprefix">
                                                    <option value="1">1</option> 
                                                    <option value="2">2</option> 
                                                    <option value="3">3</option> 
                                                    <option value="4">4</option> 
                                                    <option value="5">5</option> 
                                                    <option value="6">6</option> 
                                                </select>                             
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm pt-sm" style="font-size: 14px;">
                                                <span id="purchasecreditnoteprefixpreview" class="preview"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">
                                    <div class="col-sm-8 col-sm-offset-1 pl-sm pr-sm">
                                        <button type="button" id="purchasecreditnoteprefixbtn1" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YYYY-YY','purchasecreditnoteprefix')">Financial Year (<?=date("Y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="purchasecreditnoteprefixbtn2" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YY-YY','purchasecreditnoteprefix')">Financial Year (<?=date("y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="purchasecreditnoteprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('autonumber','purchasecreditnoteprefix')">Auto Number</button>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">  
                                <div class="col-sm-12"><hr></div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-6">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">Stock General Voucher Prefix</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Last No.</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Suffix Length</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="yesno" style="margin-top: 5px;">
                                                    <input type="checkbox" id="stockgeneralvoucherprefix" name="stockgeneralvoucherprefix" value="1" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group" id="stockgeneralvoucherprefixformat_div">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="stockgeneralvoucherprefixformat" value="" id="stockgeneralvoucherprefixformat" class="form-control transactionformat" data-id="stockgeneralvoucherprefix">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="stockgeneralvoucherprefixlastno" value="" id="stockgeneralvoucherprefixlastno" class="form-control transactionformat" data-id="stockgeneralvoucherprefix" onkeypress="return isNumber(event)">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <select id="stockgeneralvoucherprefixsuffixlength" name="stockgeneralvoucherprefixsuffixlength" class="selectpicker form-control suffixlength" data-id="stockgeneralvoucherprefix">
                                                    <option value="1">1</option> 
                                                    <option value="2">2</option> 
                                                    <option value="3">3</option> 
                                                    <option value="4">4</option> 
                                                    <option value="5">5</option> 
                                                    <option value="6">6</option> 
                                                </select>                             
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm pt-sm" style="font-size: 14px;">
                                                <span id="stockgeneralvoucherprefixpreview" class="preview"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">
                                    <div class="col-sm-8 col-sm-offset-1 pl-sm pr-sm">
                                        <button type="button" id="stockgeneralvoucherprefixbtn1" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YYYY-YY','stockgeneralvoucherprefix')">Financial Year (<?=date("Y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="stockgeneralvoucherprefixbtn2" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YY-YY','stockgeneralvoucherprefix')">Financial Year (<?=date("y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="stockgeneralvoucherprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('autonumber','stockgeneralvoucherprefix')">Auto Number</button>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">  
                                <div class="col-sm-12"><hr></div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-6">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">Goods Received Notes Prefix</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Last No.</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Suffix Length</label>                                      
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">    
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="yesno" style="margin-top: 5px;">
                                                    <input type="checkbox" id="goodsreceivednotesprefix" name="goodsreceivednotesprefix" value="1" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">    
                                        <div class="form-group" id="goodsreceivednotesprefixformat_div">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="goodsreceivednotesprefixformat" value="" id="goodsreceivednotesprefixformat" class="form-control transactionformat" data-id="goodsreceivednotesprefix">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" name="goodsreceivednotesprefixlastno" value="" id="goodsreceivednotesprefixlastno" class="form-control transactionformat" data-id="goodsreceivednotesprefix" onkeypress="return isNumber(event)">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <select id="goodsreceivednotesprefixsuffixlength" name="goodsreceivednotesprefixsuffixlength" class="selectpicker form-control suffixlength" data-id="goodsreceivednotesprefix">
                                                    <option value="1">1</option> 
                                                    <option value="2">2</option> 
                                                    <option value="3">3</option> 
                                                    <option value="4">4</option> 
                                                    <option value="5">5</option> 
                                                    <option value="6">6</option> 
                                                </select>                             
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">    
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm pt-sm" style="font-size: 14px;">
                                                <span id="goodsreceivednotesprefixpreview" class="preview"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">
                                    <div class="col-sm-8 col-sm-offset-1 pl-sm pr-sm">
                                        <button type="button" id="goodsreceivednotesprefixbtn1" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YYYY-YY','goodsreceivednotesprefix')">Financial Year (<?=date("Y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="goodsreceivednotesprefixbtn2" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('YY-YY','goodsreceivednotesprefix')">Financial Year (<?=date("y")?>-<?=(date("y")+1)?>)</button>
                                        <button type="button" id="goodsreceivednotesprefixbtn3" class="btn btn-primary btn-raised btn-xs" onclick="settransactionformat('autonumber','goodsreceivednotesprefix')">Auto Number</button>
                                    </div>  
                                </div>
                            </div>

                            <div class="row">  
                                <div class="col-sm-12"> 
                                    <hr>
                                    <div class="form-group text-center">                                               
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised text-white" onclick="resetdata()">
                                    </div>   
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
