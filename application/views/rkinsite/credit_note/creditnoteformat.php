<?php
$floatformat = '.';
$decimalformat = ',';
?>
<script>
    var CURRENCY_CODE = '<?=CURRENCY_CODE?>';
    var MemberId = <?=isset($memberid)?$memberid:0;?>;
    var InvoiceId = '<?=isset($invoiceid)?$invoiceid:'';?>';
    var addressid = <?php if(!empty($invoicedata)){ echo $invoicedata['addressid']; }else{ echo "0"; } ?>;
    var shippingaddressid = <?php if(!empty($invoicedata)){ echo $invoicedata['shippingaddressid']; }else{ echo "0"; } ?>;
    var extrachargeoptionhtml = "";
    <?php foreach($extrachargesdata as $extracharges){ ?>
        extrachargeoptionhtml+='<option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>"><?php echo $extracharges['extrachargename']; ?></option>';
    <?php } ?>

    var OfferId = '<?=isset($offerid)?$offerid:0;?>';
</script>
<style>
    .orderamounttable td, .orderamounttable th
    {
        padding:5px 5px !important;
    }
</style>

<form class="form-horizontal" id="creditnoteform" name="creditnoteform">
<input type="hidden" id="creditnoteid" name="creditnoteid" value="<?php if(isset($creditnoteid)){ echo $creditnoteid; } ?>">
<div class="row mb-xs">
    <div class="row">
        <div class="col-md-12 p-n">
            <div class="col-sm-4">
                <div class="form-group" id="member_div">
                    <div class="col-sm-12">
                        <label for="memberid" class="control-label">Select <?=Member_label?> <span class="mandatoryfield">*</span></label>
                        <select id="memberid" name="memberid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="7" <?php if(isset($creditnoteid)){ echo "disabled"; } ?>>
                            <option value="0">Select <?=Member_label?></option>
                            <?php foreach($memberdata as $member){ ?>
                                <option value="<?php echo $member['id']; ?>" <?php if(isset($memberid) && $memberid==$member['id']){ echo "selected"; } ?>><?php echo ucwords($member['namewithcodeormobile']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group" id="invoiceid_div">
                    <div class="col-sm-12">
                        <label for="invoiceid" class="control-label">Select Invoice <span class="mandatoryfield">*</span></label>
                        <select id="invoiceid" name="invoiceid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="7" title="Select Invoice" data-live-search="true" data-max-options="5" multiple <?php if(isset($creditnoteid)){ echo "disabled"; } ?>>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group" id="billingaddress_div">
                    <div class="col-sm-12">
                        <label for="billingaddressid" class="control-label">Select Billing Address</label>
                        <select id="billingaddressid" name="billingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" title="Select Billing Address">
                        </select>
                        <input type="hidden" name="billingaddress" id="billingaddress" value="">
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group" id="shippingaddress_div">
                    <div class="col-sm-12">
                        <label for="shippingaddressid" class="control-label">Select Shipping Address</label>
                        <select id="shippingaddressid" name="shippingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" title="Select Shipping Address">
                        </select>
                        <input type="hidden" name="shippingaddress" id="shippingaddress" value="">
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group" id="creditnotedate_div">
                    <div class="col-sm-12">
                        <label for="creditnotedate" class="control-label">Credit Note Date <span class="mandatoryfield">*</span></label>
                        <input id="creditnotedate" type="text" name="creditnotedate" value="<?php if(isset($creditnotedata)){ echo $this->general_model->displaydate($creditnotedata['creditnotedate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="form-group" id="remarks_div">
                    <div class="col-sm-12">
                        <label for="remarks" class="control-label">Remarks</label>
                        <textarea rows="1" id="remarks" name="remarks" class="form-control"><?php if(isset($creditnotedata)){ echo $creditnotedata['remarks']; } ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 p-n">
            <div class="col-sm-4"> 
                <div class="form-group" id="credittypenote_div">
                    <div class="col-sm-12">
                        <label for="creditnotetype" class="control-label">Credit Note Type</label>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-4">
                            <div class="radio">
                                <input type="radio" name="creditnotetype" id="creditnotetypeproduct" value="0" <?php if(!isset($offerid)){ echo 'checked'; }?>>
                                <label for="creditnotetypeproduct">Product</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="radio">
                                <input type="radio" name="creditnotetype" id="creditnotetypeoffer" value="1" <?php if(isset($offerid)){ echo 'checked'; }?>>
                                <label for="creditnotetypeoffer">Offer</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>                      
            <div class="col-sm-3">
                <div class="form-group" id="offer_div">
                    <div class="col-sm-12">
                        <label for="offerid" class="control-label">Select Offer <span class="mandatoryfield">*</span></label>
                        <select id="offerid" name="offerid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="7">
                            <option value="0">Select Offer</option>
                            <?php foreach($offerdata as $offer){ ?>
                                <option data-targetvalue="<?php echo $offer['targetvalue']; ?>" data-rewardvalue="<?php echo $offer['rewardvalue']; ?>" data-rewardtype="<?php echo $offer['rewardtype']; ?>" value="<?php echo $offer['id']; ?>" <?php if(isset($offerid) && $offerid==$offer['id']){ echo "selected"; } ?>><?php echo ucwords($offer['name']); ?></option>
                            <?php } ?>
                        </select>  
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group" id="serialno_div">
                    <div class="col-sm-12">
                        <label for="serialno" class="control-label">Serial No.</label>
                        <input id="serialno" type="text" name="serialno" class="form-control">
                    </div>
                </div>
            </div>
            <div class="col-sm-1">
                <div class="form-group pt-xl">
                    <div class="col-sm-12 pl-sm">
                        <button type="button" name="sbmtserialno" id="sbmtserialno" class="btn btn-primary btn-raised" onclick="checkSerialNo()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="Maincreditnote_div" >
    <div class="col-md-12 p-n">
        <div class="panel">
            <div class="panel-heading">
                <h4 class="text-center">Credit Notes</h4>
                <hr>
            </div>
            <div class="panel-body no-padding">
                <div class="table-responsive">
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
                                <th rowspan="2" class="text-right width12">Stock Qty.</th>
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
                        <tr>
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
                            <th colspan="2" class="text-center">Credit Note Summary (<?=CURRENCY_CODE?>)</th>
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
<div class="row" id="Maincreditnotedetails_div">
    <div class="col-md-12 p-n">
        <div class="panel panel-default border-panel">
            <div class="panel-heading">
                <h2>Details</h2>
            </div>
            <div class="panel-body pt-n">
                <div class="col-sm-12 p-n">
                    <div class="col-md-4 pr-sm pl-sm">
                        <div class="form-group ml-n mr-n">
                            <label class="control-label">Credit Note Detail <span class="mandatoryfield">*</span></label>
                        </div>
                    </div>
                    <div class="col-md-2 pr-sm pl-sm">
                        <div class="form-group ml-n mr-n" style="text-align: right;">
                            <label class="control-label">Credit Note Amount (<?=CURRENCY_CODE?>) <span class="mandatoryfield">*</span></label>
                        </div>
                    </div>
                    <div class="col-md-2 pr-sm pl-sm">
                        <div class="form-group ml-n mr-n" style="text-align: right;">
                            <label class="control-label">Redeem Points</label>
                        </div>
                    </div>
                    <div class="col-md-1 pr-sm pl-sm">
                        <div class="form-group ml-n mr-n" style="text-align: right;">
                            <label class="control-label">Tax (%)</label>
                        </div>
                    </div>
                    <div class="col-md-2 pr-sm pl-sm">
                        <div class="form-group ml-n mr-n" style="text-align: right;">
                            <label class="control-label">Net Amount (Inc. Tax)</label>
                        </div>
                    </div>
                </div>
                <div class="creditnote" id="countcreditnote1">        

                    <div class="col-sm-12 p-n">
                        <div class="col-md-4 pr-sm pl-sm">
                            <div class="form-group ml-n mr-n" id="creditnotedetail1_div">
                                <input type="text" class="form-control creditnotedetail" name="creditnotedetail[]" id="creditnotedetail1" value="">
                            </div>
                        </div>
                        <div class="col-md-2 pr-sm pl-sm">
                            <div class="form-group ml-n mr-n" id="creditnoteamount1_div">
                                <input type="text" class="form-control creditnoteamount text-right" name="creditnoteamount[]" id="creditnoteamount1" value="" onkeypress="return decimal_number_validation(event,this.value,8)">
                            </div>
                        </div>
                        <div class="col-md-2 pr-sm pl-sm">
                            <div class="form-group ml-n mr-n" id="redeempoint1_div">
                                <input type="text" class="form-control redeempoint text-right" name="redeempoint[]" id="redeempoint1" value="" onkeypress="return isNumber(event)" maxlength="4" readonly>
                            </div>
                        </div>
                        <div class="col-md-1 pr-sm pl-sm">
                            <div class="form-group ml-n mr-n" id="creditnotetax1_div">
                                <input type="text" class="form-control creditnotetax text-right" name="creditnotetax[]" id="creditnotetax1" value="" onkeypress="return decimal_number_validation(event,this.value,3)">
                                <input type="hidden" class="creditnotetaxamount" name="creditnotetaxamount[]" id="creditnotetaxamount1">
                            </div>
                        </div>
                        <div class="col-md-2 pr-sm pl-sm">
                            <div class="form-group ml-n mr-n" id="creditnotenetamount1_div">
                                <input type="text" class="form-control creditnotenetamount text-right" name="creditnotenetamount[]" id="creditnotenetamount1" value="" onkeypress="return decimal_number_validation(event,this.value,8)" readonly>
                            </div>
                        </div>
                        <div class="col-md-1 pl-sm" style="margin-top: 20px !important;">
                            <button type="button" class="btn btn-default btn-raised remove_creditnote_btn m-n" id="c1" onclick="removecreditnote(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-default btn-raised  add_creditnote_btn m-n" id="1" onclick="addnewcreditnote()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div id="creditnote_div"></div>

                <div class="col-sm-12 p-n">
                    <div class="col-md-6 pr-sm pl-sm">
                        <div class="form-group ml-n mr-n">
                            <label for="redeempoints" class="col-md-6 control-label" style="text-align: left;"><?=Member_label?> Redeem Points (<span id="memberredeempoint">0</span>)</label>
                            <div class="col-md-3 pl-n">
                                <input id="redeempoints" name="redeempoints" class="form-control text-right" onkeypress="return isNumber(event)" maxlength="4">
                                <input type="hidden" name="redeempointsrate" id="redeempointsrate">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row form-group" id="offerAmountTotal">
    <div class="col-md-8 p-n">
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
                            <th>Offer Total</th>
                            <td class="text-right"><span id="offertotal">0.00</span>
                            <input type="hidden" id="inputoffertotal" name="inputoffertotal" value=""></td>
                            <td class="text-right"><span id="offergsttotal">0.00</span>
                            <input type="hidden" id="inputoffergsttotal" name="inputoffergsttotal" value=""></td>
                        </tr>
                        <tr>
                            <td></td>
                            <th class="text-right pr-md"><span id="offertotalassesbaleamount">0.00</span></th>
                            <th class="text-right pr-md"><span id="offertotalgstamount">0.00</span></th>
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
                            <th colspan="2" class="text-center">Credit Note Summary (<?=CURRENCY_CODE?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="totalproductrow">
                            <td>Offer Total (<?=CURRENCY_CODE?>)</td>
                            <td class="text-right" width="25%"><span id="offergrossamount">0.00</span>
                            <input type="hidden" id="inputoffergrossamount" name="inputoffergrossamount" value="">
                            </td>
                        </tr>
                        <tr>
                            <td>Round Off</td>
                            <td class="text-right"><span id="offerroundoff">0.00</span></td>
                        </tr>
                        <tr>
                            <td><b>Amount Payable (<?=CURRENCY_CODE?>)</b></td>
                            <td class="text-right"><b><span id="totalofferpayableamount">0.00</span></b>
                            <input type="hidden" id="inputtotalofferpayableamount" name="inputtotalofferpayableamount" value=""></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 mt-xl pt text-center">
        <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation()">SAVE & ADD NEW</a>
        <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation('print')">SAVE & PRINT</a>
        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
        <?php 
        if(isset($invoiceid)){
            $link = ADMIN_URL."invoice";
        }else{
            $link = ADMIN_URL."credit-note";
        }?>
        <a class="<?=cancellink_class;?>" href="<?=$link?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
    </div>
</div>
</form>