<script>
    var CURRENCY_CODE = '<?=CURRENCY_CODE?>';
    var TESTING_IMAGE = '<?=TESTING_IMAGE?>';
    var view_class = "<?=view_class?>";
    var view_text = "<?=view_text?>";
    var RETESTING = '<?=isset($RETESTING)?$RETESTING:""?>';
    var TestingId = '<?=isset($testingdata)?$testingdata['id']:''?>';
    var HIDE_PURCHASE_EXTRA_CHARGES = 'style="<?=HIDE_PURCHASE_EXTRA_CHARGES==1?"display: none;":""?>"';
    var VendorId = <?=isset($vendorid)?$vendorid:0;?>;
    var BatchNo = <?=isset($testingdata)?$testingdata['batchid']:0;?>;
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
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($testingdata)){ echo 'Re '; }else{ echo 'Add '; } ?> <?=$this->session->userdata(base_url().'submenuname');?></h1>
        <small>
          	<ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?php echo $this->session->userdata(base_url().'mainmenuname'); ?></a></li>
            <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?php echo $this->session->userdata(base_url().'submenuname'); ?></a></li>
            <li class="active"><?php if(isset($testingdata)){ echo 'Re'; }else{ echo 'Add'; } ?> <?php echo $this->session->userdata(base_url().'submenuname'); ?></li>
          	</ol>
        </small>
    </div>

    <div class="container-fluid">                      
      	<div data-widget-group="group1">
		    <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-body pt-n">
                            <div class="col-md-12">
                                <form class="form-horizontal" id="testingform" name="testingform">
                                    <input type="hidden" id="parenttestingid" name="parenttestingid" value="<?php if(isset($testingdata)){ echo $testingdata['id']; } ?>">
                                    <input type="hidden" id="testingid" name="testingid" value="">
                                    <input type="hidden" id="testingprocessid" name="testingprocessid" value="<?=isset($testingdata)?$testingdata['processid']:'';?>">
                                    <input type="hidden" id="testingbatchid" name="testingbatchid" value="<?=isset($testingdata)?$testingdata['batchid']:'';?>">
                                    
                                    <div class="row mb-xs">
                                        <div class="col-md-12 p-n">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="form-group" id="process_div">
                                                        <div class="col-sm-12 pr-sm">
                                                            <label for="processid" class="control-label">Select Process <span class="mandatoryfield">*</span></label>
                                                            <select id="processid" name="processid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="4" <?php if(isset($testingdata)){ echo "disabled"; } ?> >
                                                                <option value="0">Select Process</option>
                                                                <?php foreach($processdata as $pd){ ?>
                                                                    <option value="<?php echo $pd['id']; ?>" <?php if(isset($testingdata) && $testingdata['processid']==$pd['id']){ echo "selected"; } ?>><?php echo ucwords($pd['name']); ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group" id="batchid_div">
                                                        <div class="col-sm-12 pl-sm pr-sm">
                                                            <label for="batchid" class="control-label">Select Batch No. <span class="mandatoryfield">*</span></label>
                                                            <select id="batchid" name="batchid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="3" <?php if(isset($testingdata)){ echo "disabled"; } ?>>
                                                            <option value="0">Select Batch No.</option>
                                                            <?php /* foreach($batchnodata as $bd){ ?>
                                                                    <option value="<?php echo $bd['id']; ?>" <?php if(isset($testingdata) && $testingdata['batchid']==$bd['id']){ echo "selected"; }?>><?php echo ucwords($bd['batchno']); ?></option>
                                                            <?php } */ ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group" id="testdate_div">
                                                        <div class="col-md-12 pl-xs">
                                                            <label for="testdate" class="control-label">Date <span class="mandatoryfield">*</span></label>
                                                            <div class="input-group">
                                                                <input id="testdate" type="text" name="testdate" value="<?php if(isset($grndata)){ echo $this->general_model->displaydate($grndata['receiveddate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" class="form-control" readonly>
                                                                <span class="btn btn-default datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                            </div>
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
                                                    <h4 class="text-center">Testing And R&D</h4>
                                                    <hr>
                                                </div>
                                                <div class="panel-body no-padding">
                                                    <div class="table-responsive">
                                                        <table id="testingproducttable" class="table table-hover table-bordered m-n">
                                                            <thead>
                                                                <tr>
                                                                    <th class="width5">Sr. No.</th>
                                                                    <th>Output Product Name</th>
                                                                    <th>Qty.</th>
                                                                    <th>Mechanicle Checked</th>
                                                                    <th>Electrically Checked</th>
                                                                    <th>Visually Checked</th>
                                                                    <th>Approve Qty.</th>
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
                                        <div class="col-md-12">
                                            <div class="form-group" id="remarks_div">
                                                <div class="col-md-12 p-n">
                                                    <label for="testingremarks" class="control-label">Remarks</label>
                                                    <div class="input-group">
                                                        <textarea id="testingremarks" name="testingremarks" class="form-control" readonly><?=isset($testingdata)?$testingdata['remarks']:""?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="retestingtables">
                                        <!-- <div class="row">
                                            <div class="col-md-12 p-n">
                                                <div class="panel">
                                                    <div class="panel-heading">
                                                        <h4 class="text-center">Re Testing And R&D</h4>
                                                        <hr>
                                                    </div>
                                                    <div class="panel-body no-padding">
                                                        <div class="table-responsive">
                                                            <table id="retesttestingproducttable" class="table table-hover table-bordered m-n">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="width5">Sr. No.</th>
                                                                        <th>Output Product Name</th>
                                                                        <th>Qty.</th>
                                                                        <th>Mechanicle Checked</th>
                                                                        <th>Electrically Checked</th>
                                                                        <th>Visually Checked</th>
                                                                        <th>Approve Qty.</th>
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
                                        </div> -->
                                    </div>
                                    <div id="newretestingtable">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mt-xl pt text-center">
                                            <?php if(!empty($testingdata)){ ?>
                                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                                <!-- <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation('print')">UPDATE & PRINT</a> -->
                                            <?php }else{ ?>
                                                <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation()">ADD</a>
                                                <!-- <a href="javascript:void(0)" class="btn btn-raised btn-primary" onclick="checkvalidation('print')">SAVE & PRINT</a> -->
                                            <?php } ?>
                                            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                            
                                            <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.'testing-and-rd'?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
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
