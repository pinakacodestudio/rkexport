<?php
$floatformat = '.';
$decimalformat = ',';
?>
<script>
    var CURRENCY_CODE = '<?=CURRENCY_CODE?>';
    var VendorId = <?=isset($inworddata)?$inworddata['vendorid']:0;?>;
    var GRNId = '<?=isset($inworddata)?$inworddata['grnid']:'';?>';
    var InwordId = '<?=isset($inworddata)?$inworddata['id']:'';?>';
    // var visuallydefectqty = visuallydefectqty;
    // var dimensiondefectqty = dimensiondefectqty;
    // var filename = filename;
   
    // var extrachargeoptionhtml = "";
    <?php /* if(!empty($extrachargesdata)){ 
        foreach($extrachargesdata as $extracharges){ ?>
        extrachargeoptionhtml+='<option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>"><?php echo $extracharges['extrachargename']; ?></option>';
    <?php }
    }*/?>
</script>
<style>
    .orderamounttable td, .orderamounttable th
    {
        padding:5px 5px !important;
    }
</style>

<form class="form-horizontal" id="inwordqcform" name="inwordqcform">
    
    <input type="hidden" id="inwordid" name="inwordid" value="<?php if(isset($inworddata)){ echo $inworddata['id']; } ?>">
    
    <div class="row mb-xs">
        <div class="col-md-12 p-n">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group" id="vendor_div">
                        <div class="col-sm-12 pr-sm">
                            <label for="vendorid" class="control-label">Select Vendor <span class="mandatoryfield">*</span></label>
                            <select id="vendorid" name="vendorid" class="selectpicker form-control" data-live-search="true" data-size="4" data-select-on-tab="true" <?php if(isset($inworddata)){ echo "disabled"; } ?>>
                                <option value="0">Select Vendor</option>
                               
                                <?php foreach($vendordata as $vendor){ ?>
                                    <option value="<?php echo $vendor['id']; ?>" <?php if(isset($inworddata) && $inworddata['vendorid']==$vendor['id']){ echo "selected"; } ?>><?php echo ucwords($vendor['name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group" id="grnid_div">
                        <div class="col-sm-12 pl-sm pr-sm">
                            <label for="grnid" class="control-label">Select Good Received Notes <span class="mandatoryfield">*</span></label>
                            <select id="grnid" name="grnid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="4" title="Select Good Received Notes" data-live-search="true" data-max-options="5" <?php if(isset($inworddata)){ echo "disabled"; } ?>>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group" id="inworddate_div">
                        <div class="col-sm-12 pl-sm pr-sm">
                            <label for="inworddate" class="control-label">Date <span class="mandatoryfield">*</span></label>
                            <div class="input-group">
                                <input type="text" name="inworddate" id="inworddate" class="form-control" value="<?php if(isset($inworddata) && $inworddata['createddate']!='0000-00-00 00:00:00'){ echo $this->general_model->displaydatetime($inworddata['createddate'],'d/m/Y h:i A'); }else{ echo $this->general_model->displaydatetime($this->general_model->getCurrentDateTime(),'d/m/Y h:i A'); }?>" >
                                <span class="btn btn-default datepicker_calendar_button"><i class="fa fa-clock-o fa-lg"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-sm-3">
                    <div class="form-group" id="billingaddress_div">
                        <div class="col-sm-12 pl-sm pr-sm">
                            <label for="billingaddressid" class="control-label">Select Billing Address <span class="mandatoryfield">*</span></label>
                            <select id="billingaddressid" name="billingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" title="Select Billing Address">
                            </select>
                            <input type="hidden" name="billingaddress" id="billingaddress" value="">
                        </div>
                    </div>
                </div> -->
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
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 p-n">
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="text-center">Inword Q.C.</h4>
                    <hr>
                </div>
                <div class="panel-body no-padding">
                    <div class="table-responsive">
                        <table id="inwordtable" class="table table-hover table-bordered m-n">
                            <thead>
                                <tr>
                                    <th class="width5">Sr. No.</th>
                                    <th>Product Name</th>
                                    <th class="width8">Receive Qty.</th>
                                    <th>Visually Check</th>
                                    <th>Dimension Check</th>
                                    <th class="width8">Final Stock Qty</th>
                                    <th>Upload Report</th>
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
    <div class="row form-group">
        <div class="col-md-12 p-n" id="orderamountdiv"></div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group" id="inworddate_div">
                <div class="col-sm-12 p-n">
                    <label for="inwordremarks" class="control-label">Remaks</label> 
                    <textarea name="inwordremarks" id="inwordremarks" class="form-control"><?=isset($inworddata)?$inworddata['remarks']:''?></textarea>
                </div>
            </div>
        </div>
        
    </div>
    <div class="row">
        <div class="col-md-12 mt-xl pt text-center">
            <?php if(!empty($inworddata)){ ?>
                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                <!-- <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation('print')">UPDATE & PRINT</a> -->
            <?php }else{ ?>
                <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation()">SAVE & ADD NEW</a>
            <?php } ?>
            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
            <?php /*
            if(!is_null($this->uri->segment(4)) && $this->uri->segment(4)=="purchase-order"){
                $link = ADMIN_URL."purchase-order";
            }else{
                $link = ADMIN_URL."purchase-invoice";
            }*/?>
            <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.'inword-quality-check'?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
        </div>
    </div>
</form>
