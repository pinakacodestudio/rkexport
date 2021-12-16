<?php
$floatformat = '.';
$decimalformat = ',';
?>
<script>
    var HIDE_PURCHASE_EXTRA_CHARGES = 'style="<?=HIDE_PURCHASE_EXTRA_CHARGES==1?"display: none;":""?>"';
    var CURRENCY_CODE = '<?=CURRENCY_CODE?>';
    var CREDITNOTE_URL = '<?=CREDITNOTE?>';
    var VendorId = <?=isset($vendorid)?$vendorid:0;?>;
    var InvoiceId = '<?=isset($invoiceid)?$invoiceid:'';?>';
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

<form class="form-horizontal" id="purchasecreditnoteform" name="purchasecreditnoteform">
    <input type="hidden" id="creditnoteid" name="creditnoteid" value="<?php if(isset($invoicedata)){ echo $invoicedata['id']; } ?>">
    <input type="hidden" id="oldvendorid" name="oldvendorid" value="<?php if(isset($vendorid)){ echo $vendorid; } ?>">
    <div class="row mb-xl">
        <div class="col-md-12 p-n">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group" id="member_div">
                        <div class="col-sm-12 pr-sm">
                            <label for="vendorid" class="control-label">Select Vendor <span class="mandatoryfield">*</span></label>
                            <select id="vendorid" name="vendorid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="7" <?php if(isset($vendorid)){ echo "disabled"; } ?>>
                                <option value="0">Select Vendor</option>
                                <?php foreach($vendordata as $vendor){ ?>
                                    <option data-billingid="<?=$vendor['billingaddressid']?>" data-shippingid="<?=$vendor['shippingaddressid']?>" value="<?php echo $vendor['id']; ?>" <?php if(isset($vendorid) && $vendorid==$vendor['id']){ echo "selected"; } ?>><?php echo ucwords($vendor['name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group" id="invoiceid_div">
                        <div class="col-sm-12 pl-sm pr-sm">
                            <label for="invoiceid" class="control-label">Select Purchase Invoice <span class="mandatoryfield">*</span></label>
                            <select id="invoiceid" name="invoiceid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="7" title="Select Purchase Invoice" data-live-search="true" data-max-options="5" multiple>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group" id="billingaddress_div">
                        <div class="col-sm-12 pl-sm pr-sm">
                            <label for="billingaddressid" class="control-label">Select Billing Address <span class="mandatoryfield">*</span></label>
                            <select id="billingaddressid" name="billingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" title="Select Billing Address">
                            </select>
                            <input type="hidden" name="billingaddress" id="billingaddress" value="">
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group" id="shippingaddress_div">
                        <div class="col-sm-12 pl-sm">
                            <label for="shippingaddressid" class="control-label">Select Shipping Address <span class="mandatoryfield">*</span></label>
                            <select id="shippingaddressid" name="shippingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" title="Select Shipping Address">
                            </select>
                            <input type="hidden" name="shippingaddress" id="shippingaddress" value="">
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group" id="creditnoteno_div">
                        <div class="col-sm-12 pr-sm">
                            <label for="creditnoteno" class="control-label">Credit Note No. <span class="mandatoryfield">*</span></label>
                            <input id="creditnoteno" type="text" name="creditnoteno" class="form-control" value="<?php if(!empty($invoicedata)){ echo $invoicedata['creditnotenumber']; }else if(!empty($creditnotenumber)){ echo $creditnotenumber; } ?>" readonly>
                            <input id="creditnotenumber" type="hidden" value="<?php if(!empty($invoicedata)){ echo $invoicedata['creditnotenumber']; }else if(!empty($creditnotenumber)){ echo $creditnotenumber; } ?>">
                            <div class="checkbox">
                                <input id="editcreditnotenumber" type="checkbox" value="1" name="editcreditnotenumber" class="checkradios">
                                <label for="editcreditnotenumber">Edit Credit Note No.</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group" id="creditnotedate_div">
                        <div class="col-sm-12 pl-sm pr-sm">
                            <label for="creditnotedate" class="control-label">Credit Note Date <span class="mandatoryfield">*</span></label>
                            <input id="creditnotedate" type="text" name="creditnotedate" value="<?php if(isset($invoicedata)){ echo $this->general_model->displaydate($invoicedata['invoicedate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group" id="remarks_div">
                        <div class="col-sm-12 pl-sm">
                            <label for="remarks" class="control-label">Remarks</label>
                            <textarea rows="1" id="remarks" name="remarks" class="form-control"></textarea>
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
                    <h4 class="text-center">Purchase Returns</h4>
                    <hr>
                </div>
                <div class="panel-body no-padding">
                    <div class="table-responsive">
                        <input type="hidden" id="credittotal" name="credittotal" value="">
                        <table id="creditnoteproducttable" class="table table-hover table-bordered m-n">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="width5">Sr. No.</th>
                                    <th rowspan="2" class="width15">Product Name</th>
                                    <th rowspan="2" class='text-right'>Qty.</th>
                                    <th rowspan="2" class="text-right">Rate (Excl. Tax)</th>
                                    <th class="text-right width8 disccol">Dis.(%)</th>
                                    <th class="text-right width8 sgstcol">SGST (%)</th>
                                    <th class="text-right width8 cgstcol">CGST (%)</th>
                                    <th class="text-right width8 igstcol">IGST (%)</th>
                                    <th rowspan="2" class="text-right">Amount</th>
                                    <th rowspan="2" class="text-right">Paid Credit</th>
                                    <th rowspan="2" class="text-right width8">Credit Qty.</th>
                                    <th rowspan="2" class="text-right width8">Credit (%)</th>
                                    <th rowspan="2" class="text-right width12">Credit Amount</th>
                                    <th rowspan="2" class="text-right width12">Return Qty.</th>
                                    <th rowspan="2" class="text-right width12">Reject Qty.</th>
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
        <div class="col-md-8 p-n" id="extracharges_div">
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
                                <th colspan="2" class="text-center">Purchase Credit Note Summary (<?=CURRENCY_CODE?>)</th>
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
    </div>
    <div class="row form-group">
        <div class="col-md-12 p-n" id="invoiceamountdiv"></div>
    </div>
    <div class="row">
        <div class="col-md-12 mt-xl pt text-center">
            <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation()">SAVE & ADD NEW</a>
            <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation('print')">SAVE & PRINT</a>
            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
            <?php 
            if(isset($invoiceid)){
                $link = ADMIN_URL."purchase-invoice";
            }else{
                $link = ADMIN_URL."purchase-credit-note";
            }?>
            <a class="<?=cancellink_class;?>" href="<?=$link?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
        </div>
    </div>
</form>