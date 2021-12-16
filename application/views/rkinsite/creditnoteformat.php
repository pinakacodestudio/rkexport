<?php
$floatformat = '.';
$decimalformat = ',';
?>
<script>
    var CREDITNOTE_URL = '<?=CREDITNOTE?>';
    var MemberId = <?=isset($memberid)?$memberid:0;?>;
    var InvoiceId = '<?=isset($invoiceid)?$invoiceid:'';?>';
</script>
<style>
    .orderamounttable td, .orderamounttable th
    {
        padding:5px 5px !important;
    }
</style>

<form class="form-horizontal" id="creditnoteform" name="creditnoteform">
<div class="row mb-xl">
    <div class="col-md-12 p-n">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group" id="member_div">
                    <div class="col-sm-12">
                        <label for="memberid" class="control-label">Select <?=Member_label?> <span class="mandatoryfield">*</span></label>
                        <select id="memberid" name="memberid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="7">
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
                        <select id="invoiceid" name="invoiceid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="7" title="Select Invoice" data-live-search="true" data-max-options="5" multiple>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group" id="billingaddress_div">
                    <div class="col-sm-12">
                        <label for="billingaddressid" class="control-label">Select Billing Address <span class="mandatoryfield">*</span></label>
                        <select id="billingaddressid" name="billingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" title="Select Billing Address">
                        </select>
                        <input type="hidden" name="billingaddress" id="billingaddress" value="">
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group" id="shippingaddress_div">
                    <div class="col-sm-12">
                        <label for="shippingaddressid" class="control-label">Select Shipping Address <span class="mandatoryfield">*</span></label>
                        <select id="shippingaddressid" name="shippingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" title="Select Shipping Address">
                        </select>
                        <input type="hidden" name="shippingaddress" id="shippingaddress" value="">
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group" id="invoicedate_div">
                    <div class="col-sm-12">
                        <label for="invoicedate" class="control-label">Invoice Date <span class="mandatoryfield">*</span></label>
                        <input id="invoicedate" type="text" name="invoicedate" value="<?php if(isset($invoicedata)){ echo $this->general_model->displaydate($invoicedata['invoicedate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="form-group" id="remarks_div">
                    <div class="col-sm-12">
                        <label for="remarks" class="control-label">Remarks</label>
                        <textarea rows="1" id="remarks" name="remarks" class="form-control"><?php if(isset($invoicedata)){ echo $invoicedata['remarks']; } ?></textarea>
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
                <h4 class="text-center">Credit Notes</h4>
                <hr>
            </div>
            <div class="panel-body no-padding">
                <div class="table-responsive">
                    <input type="hidden" id="credittotal" name="credittotal" value="">
                    <table id="creditnoteproducttable" class="table table-hover table-bordered m-n">
                        <thead>
                            <tr>
                                <th class="width5">Sr.No.</th>
                                <th class="width15">Product Name</th>
                                <th class='text-right'>Qty.</th>
                                <th class="text-right">Rate</th>
                                <th class="text-right">Dis.(%)</th>
                                <th class="text-right" id="sgstcol">SGST (%)</th>
                                <th class="text-right" id="cgstcol">CGST (%)</th>
                                <th class="text-right" id="igstcol">IGST (%)</th>
                                <th class="text-right">Amount</th>
                                <th class="text-right">Paid Credit</th>
                                <th class="text-right width5">Credit Qty.</th>
                                <th class="text-right width8">Credit (%)</th>
                                <th class="text-right width12">Credit Amount</th>
                                <th class="text-right width12">Stock Qty.</th>
                                <th class="text-right width12">Reject Qty.</th>
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
    <div class="col-md-6 p-n" id="orderamountdiv">
       
    </div>
    <div class="col-md-6 pull-ight pr-n">
        <div class="col-md-6 p-n">
            <div class="panel-body no-padding" style="margin-bottom:10px;">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered m-n invoice" style="color: #000">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center">GST Summary (<?=CURRENCY_CODE?>)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Assessable Amount</td>
                                <td class="text-right"><span id="subtotal">0.00</span></td>
                            </tr>
                            <tr>
                                <td>Total GST</td>
                                <td class="text-right"><span id="totaltax">0.00</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 p-n">
            <div class="panel-body no-padding">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered m-n invoice" style="color: #000">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center">Return Order Summary (<?=CURRENCY_CODE?>)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Of Product</td>
                                <td class="text-right"><span id="totalamount" name="totalamount">0.00</span></td>
                            </tr>
                            <tr>
                                <td><b>Amount Payable</b></td>
                                <td class="text-right"><b><span id="totalpayableamount" name="totalpayableamount">0.00</span></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <input type="hidden" id="totalorderamount" name="totalorderamount" value="">        
    </div>
   
</div>


<div class="row">
    <div class="col-md-12  mt-xl pt">
        <div class="pull-right">
            <a href="javascript:window.print()" class="btn btn-inverse"><i class="ti ti-printer"></i></a>
            <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation()"><?php if(isset($creditnoteid)){ echo "UPDATE"; }else{ echo "SUBMIT"; } ?></a>
            <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation('print')"><?php if(isset($creditnoteid)){ echo "UPDATE"; }else{ echo "SUBMIT"; } ?> & Print</a>
        </div>
    </div>
</div>
</form>

<script type="text/javascript">
    
</script>