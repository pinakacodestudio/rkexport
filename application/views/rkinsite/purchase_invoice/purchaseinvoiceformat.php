<?php
$floatformat = '.';
$decimalformat = ',';
?>
<script>
    var CURRENCY_CODE = '<?=CURRENCY_CODE?>';
    var HIDE_PURCHASE_EXTRA_CHARGES = 'style="<?=HIDE_PURCHASE_EXTRA_CHARGES==1?"display: none;":""?>"';
    var VendorId = <?=isset($vendorid)?$vendorid:0;?>;
    var GRNId = '<?=isset($grnid)?$grnid:'';?>';
    var addressid = <?php if(!empty($invoicedata)){ echo $invoicedata['addressid']; }else{ echo "0"; } ?>;
    var shippingaddressid = <?php if(!empty($invoicedata)){ echo $invoicedata['shippingaddressid']; }else{ echo "0"; } ?>;
    var extrachargeoptionhtml = "";
    <?php if(!empty($extrachargesdata)){ 
        foreach($extrachargesdata as $extracharges){ ?>
        extrachargeoptionhtml+='<option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>"><?php echo $extracharges['extrachargename']; ?></option>';
    <?php }
    }?>
</script>
<style>
    .orderamounttable td, .orderamounttable th
    {
        padding:5px 5px !important;
    }
</style>

<form class="form-horizontal" id="purchaseinvoiceform" name="purchaseinvoiceform">
    <input type="hidden" id="invoiceid" name="invoiceid" value="<?php if(isset($invoicedata)){ echo $invoicedata['id']; } ?>">
    <input type="hidden" id="oldorderid" name="oldorderid" value="<?php if(isset($orderid)){ echo $orderid; } ?>">
    <input type="hidden" id="oldvendorid" name="oldvendorid" value="<?php if(isset($vendorid)){ echo $vendorid; } ?>">

    <div class="row mb-xs">
        <div class="col-md-12 p-n">
            <div class="row">
                <!-- <div class="col-sm-6">
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
                </div> -->
                <div class="col-sm-6">
                    <div class="form-group" id="member_div">
                        <div class="col-sm-<?php if(isset($multiplememberchannel) && $multiplememberchannel==1){ echo "10 pr-n"; }else{ echo "12 pr-sm"; }?>" style="margin: 0px 0px 0px -7px;">
                            <label for="memberid" class="control-label">Select Party <span class="mandatoryfield">*</span></label>
                            <select id="memberid" name="memberid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" <?php if(!empty($quotationdata) && !isset($isduplicate)){ echo "disabled"; } ?>>
                            <option value="0">Party List</option>
                            <?php /* foreach($Partydata as $party){ ?>
                            <option value="<?=$party['id']?>"><?=$party['id']?></option>
                            <?php } */?>
                            </select>
                        </div>
                        <?php if(isset($multiplememberchannel) && $multiplememberchannel==1){?>
                        <!-- <div class="col-sm-2" style="padding-top: 28px !important;">
                            <a href="javascript:void(0)"class="btn btn-primary btn-raised"><i class="fa fa-plus" title="Add <?php Member_label?>"></i></a>
                            </div> -->
                        <!-- <div class="col-md-1 p-n" style="padding-top: 28px !important; margin:0 0 0 7px;">
                            <a href="javascript:void(0)" onclick="addcountry()" class="btn btn-primary btn-raised p-xs"><i class="material-icons" title="Add Unit">add</i></a>
                        </div> -->
                        <?php } ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group" id="grnid_div">
                        <div class="col-sm-12 pl-sm pr-sm">
                            <label for="grnid" class="control-label">Branch<span class="mandatoryfield">*</span></label>
                            <select id="grnid" name="grnid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="7" title="Select office" data-live-search="true" data-max-options="5" multiple>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group" id="invoiceno_div">
                        <div class="col-sm-12 pr-sm pl-sm">
                            <label for="invoiceno" class="control-label">Invoice No. <span class="mandatoryfield">*</span></label>
                            <input id="invoiceno" type="text" name="invoiceno" class="form-control" value="<?php if(!empty($invoicedata)){ echo $invoicedata['invoiceno']; }else if(!empty($invoiceno)){ echo $invoiceno; } ?>" readonly>
                            <input id="invoicenumber" type="hidden" value="<?php if(!empty($invoicedata)){ echo $invoicedata['invoiceno']; }else if(!empty($invoiceno)){ echo $invoiceno; } ?>">
                            <!-- <div class="checkbox">
                                <input id="editinvoicenumber" type="checkbox" value="1" name="editinvoicenumber" class="checkradios">
                                <label for="editinvoicenumber">Edit Invoice No.</label>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group" id="billingaddress_div">
                        <div class="col-sm-12 pl-sm pr-sm">
                            <label for="billingaddressid" class="control-label">Receive By<span class="mandatoryfield">*</span></label>
                            <select id="billingaddressid" name="billingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                <option value="0">Employee List</option>
                            </select>
                            <!-- <a href="javascript:void(0)" class="mt-sm" style="float: left;" onclick="openmodal(1)"><i class="fa fa-plus"></i> Add New Billing Address</a>
                            <input type="hidden" name="billingaddress" id="billingaddress" value=""> -->
                        </div>
                    </div>
                </div>
                <!-- <div class="col-sm-6">
                    <div class="form-group" id="billingaddress_div">
                        <div class="col-sm-12 pl-sm">
                            <label for="billingaddressid" class="control-label">Purchase Invoice No.<span class="mandatoryfield">*</span></label>
                            <select id="billingaddressid" name="billingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" title="Select Billing Address">
                            </select>
                            <input type="hidden" name="billingaddress" id="billingaddress" value="">
                        </div>
                    </div>
                </div> -->
                <div class="clearfix"></div>
                
                <div class="col-sm-6">
                    <div class="form-group" id="orderid_div">
                        <div class="col-sm-12 pl-sm pr-sm">
                            <label for="orderid" class="control-label">PO ID <span class="mandatoryfield">*</span></label>
                            <input id="orderid" type="text" name="orderid" class="form-control" value="<?php if(!empty($orderdata['orderdetail']) && !isset($isduplicate)){ echo $orderdata['orderdetail']['orderid']; }else if(!empty($orderid)){ echo $orderid; } ?>" readonly>
                            <input id="ordernumber" type="hidden" value="<?php if(!empty($orderdata['orderdetail']) && !isset($isduplicate)){ echo $orderdata['orderdetail']['orderid']; }else if(!empty($orderid)){ echo $orderid; } ?>">
                            <!-- <div class="checkbox">
                                <input id="editordernumber" type="checkbox" value="1" name="editordernumber" class="checkradios">
                                <label for="editordernumber">Edit Order ID</label>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group" id="invoicedate_div">
                        <div class="col-sm-12 pl-sm pr-sm">
                            <label for="invoicedate" class="control-label">Invoice Date <span class="mandatoryfield">*</span></label>
                            <input id="invoicedate" type="text" name="invoicedate" value="<?php if(isset($invoicedata)){ echo $this->general_model->displaydate($invoicedata['invoicedate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-sm-3">
                    <div class="form-group" id="shippingaddress_div">
                        <div class="col-sm-12 pl-sm">
                            <label for="shippingaddressid" class="control-label">Select Shipping Address <span class="mandatoryfield">*</span></label>
                            <select id="shippingaddressid" name="shippingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" title="Select Shipping Address">
                            </select>
                            <input type="hidden" name="shippingaddress" id="shippingaddress" value="">
                        </div>
                    </div>
                </div> -->
                <div class="col-sm-6">
                    <div class="form-group" id="invoicedate_div">
                        <div class="col-sm-12 pl-sm pr-sm">
                            <label for="invoicedate" class="control-label">Recieve Date <span class="mandatoryfield">*</span></label>
                            <input id="invoicedate" type="text" name="invoicedate" value="<?php if(isset($invoicedata)){ echo $this->general_model->displaydate($invoicedata['invoicedate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group" id="invoicedate_div">
                        <div class="col-sm-12 pl-sm pr-sm">
                            <label for="invoicedate" class="control-label">Payment due Date <span class="mandatoryfield">*</span></label>
                            <input id="invoicedate" type="text" name="invoicedate" value="<?php if(isset($invoicedata)){ echo $this->general_model->displaydate($invoicedata['invoicedate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-sm-6">
                    <div class="form-group" id="remarks_div">
                        <div class="col-sm-12 pl-sm">
                            <label for="remarks" class="control-label">Remarks</label>
                            <textarea rows="1" id="remarks" name="remarks" class="form-control"><?php if(isset($invoicedata)){ echo $invoicedata['remarks']; } ?></textarea>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
<br>
    <div class="row">
        <div class="col-md-12 p-n">
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="text-center">Purchase Invoice</h4>
                    <hr>
                </div>
                <div class="panel-body no-padding">
                    <div class="table-responsive">
                        <table id="invoiceproducttable" class="table table-hover table-bordered m-n">
                            <thead>
                                <tr>
                                    <!-- <th rowspan="2" class="width5">Sr. No.</th> -->
                                    <th rowspan="2">Product Name</th>
                                    <th rowspan="2" class="width12">Qty.</th>
                                    <th rowspan="2" class="text-right">Tax</th>
                                    <th rowspan="2" class="text-right">Weight</th>
                                    <th class="text-right width8 disccol">Disc (%)</th>
                                    <th class="text-right width8 sgstcol">SGST (%)</th>
                                    <th class="text-right width8 cgstcol">CGST (%)</th>
                                    <th class="text-right width8 igstcol">IGST (%)</th>
                                    <th rowspan="2" class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                                </tr>
                                <!-- <tr>
                                    <th class="text-right width8 disccol">Amt. (<?=CURRENCY_CODE?>)</th>
                                    <th class="text-right width8 sgstcol">Amt. (<?=CURRENCY_CODE?>)</th>
                                    <th class="text-right width8 cgstcol">Amt. (<?=CURRENCY_CODE?>)</th>
                                    <th class="text-right width8 igstcol">Amt. (<?=CURRENCY_CODE?>)</th>
                                </tr> -->
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="col-sm-12">
                                        <select id="productid<?=($i+1)?>" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                            <option value="0">Select Product</option>
                                        </select>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row form-group" id="AmountTotal">
        <div class="col-md-8 p-n">
            <div id="extracharges_div"></div>
            <div class="panel countorders" id="0">
                <div class="panel-heading">
                    <h2 style="width: 35%;">Other Charges</h2>                                       
                </div>
                <div class="panel-body">                                                 
                    <div class="row m-n">     
                        <div class="col-md-6 p-n countcharges0" id="countcharges_0_1" style="<?=HIDE_PURCHASE_EXTRA_CHARGES==1?"display: none;":""?>">   
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
                    </div>
                    <input type="hidden" name="ordergrossamount[]" id="ordergrossamount_0" class="ordergrossamount" value="">            <input type="hidden" name="invoiceorderamount[]" id="invoiceorderamount_0" class="invoiceorderamount" value="">
                    <div class="row m-n">
                        <div class="col-md-3 pr-sm">
                            <div class="form-group p-n text-right" id="orderdiscountpercent0_div">
                                <div class="col-sm-12">
                                    <label class="control-label" for="orderdiscountpercent0">Discount (%)</label> 
                                    <input type="text" id="orderdiscountpercent0" name="orderdiscountpercent[0]" class="form-control text-right orderdiscountpercent" placeholder="" onkeypress="return decimal_number_validation(event, this.value)" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 pl-sm pr-sm">                                          
                            <div class="form-group p-n text-right" id="orderdiscountamount0_div">  
                                <div class="col-sm-12">                                              
                                    <label class="control-label" for="orderdiscountamount0">Discount Amount</label>             
                                    <input type="text" id="orderdiscountamount0" name="orderdiscountamount[0]" class="form-control text-right orderdiscountamount" placeholder="" onkeypress="return decimal_number_validation(event, this.value)" value="">               
                                    <input type="hidden" id="invoicediscamnt0" value="">       
                                    <input type="hidden" id="orderdiscamnt0" value="">         
                                </div>
                            </div>
                        </div>
                    </div>
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
                                <th colspan="2" class="text-center">Purchase Invoice Summary (<?=CURRENCY_CODE?>)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="totalproductrow">
                                <td>Product Total (<?=CURRENCY_CODE?>)</td>
                                <td class="text-right" width="25%"><span id="grossamount" name="grossamount">0.00</span>
                                <input type="hidden" id="inputgrossamount" name="inputgrossamount" value="">
                                </td>
                            </tr>
                            <tr id="totaldiscounts" style="display:none;">
                                <td>Discount (<span id="ovdiscper">0.00</span>%)</td>
                                <td class="text-right" width="25%"><span id="ovdiscamnt">0.00</span>
                                <input type="hidden" id="inputovdiscamnt" name="inputovdiscamnt" value="">
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
        <div class="col-md-8 p-n">
            <div id="extracharges_div"></div>
            <div class="panel countorders" id="0">
                <div class="panel-heading">
                    <h2 style="width: 35%;">LUT Details</h2>
                </div>
                <div class="panel-body">                                                 
                    <div class="row m-n">     
                        <div class="col-md-6 p-n countcharges0" id="countcharges_0_1" style="<?=HIDE_PURCHASE_EXTRA_CHARGES==1?"display: none;":""?>">   
                            <div class="col-sm-6 pr-xs">
                                <div class="form-group p-n" id="extracharges_0_1_div">
                                    <div class="col-sm-12">
                                        <!-- <div class="col-sm-12"> -->
                                            <label for="invoicedate" class="control-label">Invoice Date <span class="mandatoryfield">*</span></label>
                                            <input id="invoicedate" type="text" name="invoicedate" value="<?php if(isset($invoicedata)){ echo $this->general_model->displaydate($invoicedata['invoicedate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" class="form-control" readonly>
                                        <!-- </div> -->

                                        <!-- <input type="hidden" name="orderextrachargestax[0][]" id="orderextrachargestax_0_1" class="orderextrachargestax" value="">
                                        <input type="hidden" name="orderextrachargesname[0][]" id="orderextrachargesname_0_1" class="orderextrachargesname" value="">
                                        <input type="hidden" name="orderextrachargepercentage[0][]" id="orderextrachargepercentage_0_1" class="orderextrachargepercentage" value=""> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mt-xl" id="generateinvoice_div">
                                    <!-- <div class="col-sm-12"> -->
                                        <div class="checkbox">
                                            <input id="generateinvoice" type="checkbox" value="1" name="generateinvoice" class="checkradios">
                                            <label for="generateinvoice">LUT Order</label>
                                        </div>
                                    <!-- </div> -->
                                </div>
                            </div>
                            
                            <!-- <div class="col-sm-3 pl-xs pr-xs">
                                <div class="form-group p-n" id="orderextrachargeamount_0_1_div">
                                    <div class="col-sm-12">
                                        <input type="text" id="orderextrachargeamount_0_1" name="orderextrachargeamount[0][]" class="form-control text-right orderextrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)">
                                    </div>
                                </div>
                            </div> -->

                            <!-- <div class="col-md-3 text-right pt-md">
                                <button type="button" class="btn btn-default btn-raised  remove_charges_btn m-n" onclick="removecharge(0,1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>

                                <button type="button" class="btn btn-default btn-raised  add_charges_btn m-n" onclick="addnewcharge()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                            </div> -->
                        </div>
                    </div>
                    <input type="hidden" name="ordergrossamount[]" id="ordergrossamount_0" class="ordergrossamount" value="">
                    <input type="hidden" name="invoiceorderamount[]" id="invoiceorderamount_0" class="invoiceorderamount" value="">
                    <div class="row m-n">
                        <!-- <div class="col-md-3 pr-sm">
                            <div class="form-group p-n text-right" id="orderdiscountpercent0_div">
                                <div class="col-sm-12">
                                    <label class="control-label" for="orderdiscountpercent0">Discount (%)</label> 
                                    <input type="text" id="orderdiscountpercent0" name="orderdiscountpercent[0]" class="form-control text-right orderdiscountpercent" placeholder="" onkeypress="return decimal_number_validation(event, this.value)" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 pl-sm pr-sm">                                          
                            <div class="form-group p-n text-right" id="orderdiscountamount0_div">  
                                <div class="col-sm-12">                                              
                                    <label class="control-label" for="orderdiscountamount0">Discount Amount</label>             
                                    <input type="text" id="orderdiscountamount0" name="orderdiscountamount[0]" class="form-control text-right orderdiscountamount" placeholder="" onkeypress="return decimal_number_validation(event, this.value)" value="">               
                                    <input type="hidden" id="invoicediscamnt0" value="">       
                                    <input type="hidden" id="orderdiscamnt0" value="">         
                                </div>
                            </div>
                        </div> -->
                        <div class="col-sm-8" style="margin: 0px 0 0px 0px;">
                            <div class="form-group" id="remarks_div">
                            <div class="col-sm-12">
                                <label for="remarks" class="control-label">Comments</label>
                                <textarea id="remarks" name="remarks" class="form-control"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['remarks']; }?></textarea>
                            </div>
                            </div>
                        </div>
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
    <div class="row form-group">
        <div class="col-md-12 p-n" id="orderamountdiv"></div>
    </div>
    <div class="row">
        <div class="col-md-12 mt-xl pt text-center">
            <?php if(!empty($invoicedata)){ ?>
                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation('print')">UPDATE & PRINT</a>
            <?php }else{ ?>
                <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation()">SAVE & ADD NEW</a>
                <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation('print')">SAVE & PRINT</a>
            <?php } ?>
            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
            <?php 
            if(!is_null($this->uri->segment(4)) && $this->uri->segment(4)=="purchase-order"){
                $link = ADMIN_URL."purchase-order";
            }else{
                $link = ADMIN_URL."purchase-invoice";
            }?>
            <a class="<?=cancellink_class;?>" href="<?=$link?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
        </div>
    </div>
</form>
