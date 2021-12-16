<?php 
$disabled = $readonly = $processgroupmappingid = $processbymemberid = $parentproductprocessid = $extrachargesoptions = $estimatedate = $postorderid = $postproductionplanid = "";
$postvendorid = $postmachineid = 0;
if($processtype=="IN" || $processtype=="REPROCESS" || isset($action) || $processtype=="NEXTPROCESS"){
    if(!isset($action) || ($processtype=="IN" && isset($action))){
        $disabled = "disabled";
        $readonly = "readonly";
    }   
    if(isset($productprocessdata)){
        $processgroupmappingid = $productprocessdata['processgroupmappingid'];
        $processbymemberid = $productprocessdata['processbymemberid'];
        $postvendorid = $productprocessdata['vendorid'];
        $postmachineid = $productprocessdata['machineid'];
        $estimatedate = ($productprocessdata['estimatedate']!="0000-00-00"?$productprocessdata['estimatedate']:"");
        $postorderid = $productprocessdata['orderid'];
        
        if($processtype=="REPROCESS"){
            $parentproductprocessid = $productprocessdata['id'];
        }else if($processtype=="NEXTPROCESS"){
            $parentproductprocessid = $productprocessdata['parentproductprocessid'];
        }
    }
}
if($processtype=="OUT" && !isset($action) && isset($orderid)){
    $postorderid = $orderid;
}
if($processtype=="OUT" && !isset($action) && isset($productionplanid)){
    $postproductionplanid = $productionplanid;
}
if(!empty($extrachargesdata)){
    foreach($extrachargesdata as $charges){
        $extrachargesoptions .= '<option value="'.$charges['id'].'" data-tax="'.$charges['tax'].'" data-type="'.$charges['amounttype'].'" data-amount="'.$charges['defaultamount'].'">'.$charges['extrachargename'].'</option>'; 
    } 
}

$VENDORDATA = "";
if(!empty($vendordata)){ 
    foreach($vendordata as $vendor){
        $VENDORDATA .= '<option value="'.$vendor['id'].'">'.$vendor['name'].'</option>';
    }
}
$ORDERDATA = "";
if(!empty($orderdata)){ 
    foreach($orderdata as $order){
        $ORDERDATA .= '<option value="'.$order['id'].'">'.$order['orderid'].'</option>';
    }
}
?>
<?php $pgid = $processdata = "";
    if(isset($productprocessdata)){
        $pgid = $productprocessdata['processgroupid']; 
        $pgkey = array_search($pgid,array_column($processgroupdata,"id")); 
        $processdata = $processgroupdata[$pgkey]['processdata'];
    } ?>
<script>
    var PROCESSTYPE = '<?=$processtype?>';
    var DISABLED = '<?=$disabled?>';
    var READONLY = '<?=$readonly?>';
    var PROCESSGROUPMAPPINGID = "<?php echo $processgroupmappingid; ?>";
    var PROCESSGROUPID = '<?=(isset($processgroupid)?implode(",",$processgroupid):"")?>';
    var PROCESS_BATCH_NO = "<?php echo PROCESS_BATCH_NO; ?>";
    var PARENTPRODUCTPROCESSID = "<?php echo $parentproductprocessid; ?>";
    var EXTRA_CHARGES_OPTIONS = '<?=$extrachargesoptions?>';
    var MACHINEID = '<?=(isset($productprocessdata)?$productprocessdata['machineid']:0)?>';
    var STOCK_MANAGE_BY = '<?=STOCK_MANAGE_BY?>';
    var VENDORDATA = '<?=$VENDORDATA?>';
    var ORDERDATA = '<?=$ORDERDATA?>';
    var PROCESS_BATCH_NO = '<?=PROCESS_BATCH_NO?>';

    var Edit_productprocessid = "<?php if(isset($productprocessdata) && isset($action)){ echo $productprocessdata['id']; } ?>";
    var Edit_mainbatchprocessid = "<?php if(isset($productprocessdata)){ echo $productprocessdata['productprocessid']; }else{ echo "0"; } ?>";
    var Edit_parentproductprocessid = "<?php if(isset($productprocessdata) && !isset($action)){ echo $productprocessdata['id']; }else{ echo "0"; } ?>";
    var Edit_processgroupmappingid = "<?php if(isset($productprocessdata)){ echo $productprocessdata['processgroupmappingid']; }?>";
    var Edit_processbymemberid = "<?php echo $processbymemberid; ?>";
    var Edit_postvendorid = "<?php echo $postvendorid; ?>";
    var Edit_postmachineid = "<?php echo $postmachineid; ?>";
    var Edit_postestimatedate = "<?php echo $estimatedate; ?>";
    var Edit_postorderid = "<?php echo $postorderid; ?>";
    var Edit_disabled = "<?php if(isset($productprocessdata) && isset($action)){ echo "disabled"; }else{ echo $disabled; } ?>";
    var Edit_checkinemp = "<?php if((isset($productprocessdata) && $productprocessdata['processbymemberid']==1) || (!isset($productprocessdata) && $processtype=="OUT")){ echo "checked"; }?>";
    var Edit_checkotherparty = "<?php if(isset($productprocessdata) && $productprocessdata['processbymemberid']==0){ echo "checked"; }?>";
    var Edit_outdisabled = "<?php if((isset($productprocessdata) && $productprocessdata['processbymemberid']==1 || (!isset($productprocessdata) && $processtype=="OUT"))){ echo "display:none;"; }?>";
    var Edit_vendordisabled = "<?=((($processtype=="NEXTPROCESS" || $processtype=="REPROCESS" || ( $processtype=="OUT" && isset($action))) && isset($productprocessdata) && $productprocessdata['processbymemberid']==0)?"":"disabled")?>";
    var Edit_machinehide = "<?php if((isset($productprocessdata) && $productprocessdata['processbymemberid']==0)){ echo "display:none;"; }?>";
    var Edit_batchno = "<?php if(isset($productprocessdata)){ echo $productprocessdata['batchno']; }?>";
    var Edit_transactiondate = "<?php if(isset($productprocessdata) && isset($action)){ echo $this->general_model->displaydate($productprocessdata['transactiondate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>";
    var Edit_estimatedate = "<?php if(isset($productprocessdata) && isset($action) && $productprocessdata['estimatedate']!="0000-00-00"){ echo $this->general_model->displaydate($productprocessdata['estimatedate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>";
    var Edit_comments = "<?php if(isset($productprocessdata) && isset($action)){ echo $productprocessdata['comments']; } ?>";
   
    var Edit_outextracharges = "";
    <?php if(isset($action) && !empty($ExtraChargesData) && $processtype!="IN") { ?>
        <?php for ($i=0; $i < count($ExtraChargesData); $i++) { ?>            
            var optionExtraCharges = ec_btn = '';
            <?php foreach($extrachargesdata as $extracharges){ ?>
                optionExtraCharges += '<option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>" <?php if($ExtraChargesData[$i]['extrachargesid'] == $extracharges['id']){ echo "selected"; } ?>><?php if($ExtraChargesData[$i]['extrachargesid'] == $extracharges['id']){ echo $ExtraChargesData[$i]['extrachargesname']; }else { echo $extracharges['extrachargename']; } ?></option>';
            <?php } ?>
            <?php if($i==0){?>
                <?php if(count($ExtraChargesData)>1){ ?>
                    ec_btn += '<button type="button" class="btn btn-default btn-raised remove_outcharges_btn<?=$pgid?> m-n" onclick="removecharge(1,<?=$pgid?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>';
                <?php }else { ?>
                    ec_btn += '<button type="button" class="btn btn-default btn-raised add_outcharges_btn<?=$pgid?> m-n" onclick="addnewcharge(<?=$pgid?>)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>';
                <?php } ?>
            <?php }else if($i!=0) { ?>
                ec_btn += '<button type="button" class="btn btn-default btn-raised remove_outcharges_btn<?=$pgid?> m-n" onclick="removecharge(<?=$i+1?>,<?=$pgid?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>';
            <?php } ?>
            ec_btn += '<button type="button" class="btn btn-default btn-raised btn-sm remove_outcharges_btn<?=$pgid?> m-n" onclick="removecharge(<?=$i+1?>,<?=$pgid?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>';
            ec_btn += '<button type="button" class="btn btn-default btn-raised add_outcharges_btn<?=$pgid?> m-n" onclick="addnewcharge(<?=$pgid?>)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>';  
        
            Edit_outextracharges += '<div class="col-md-4 p-n countoutcharges<?=$pgid?>" id="countoutcharges_<?=$pgid?>_<?=$i+1?>">\
                                        <div class="col-sm-6 pr-xs">\
                                            <input type="hidden" name="outextrachargemappingid[<?=$pgid?>][]" value="<?=$ExtraChargesData[$i]['id']?>" id="outextrachargemappingid_<?=$pgid?>_<?=$i+1?>">\
                                            <div class="form-group p-n" id="outextracharges_<?=$pgid?>_<?=$i+1?>_div">\
                                                <div class="col-sm-12">\
                                                    <select id="outextrachargesid_<?=$pgid?>_<?=$i+1?>" name="outextrachargesid[<?=$pgid?>][]" class="selectpicker form-control outextrachargesid<?=$pgid?>" data-live-search="true" data-select-on-tab="true" data-size="5">\
                                                        <option value="0">Select Charges</option>\
                                                        '+optionExtraCharges+'\
                                                    </select>\
                                                    <input type="hidden" name="outextrachargestax[<?=$pgid?>][]" id="outextrachargestax_<?=$pgid?>_<?=$i+1?>" class="outextrachargestax<?=$pgid?>" value="<?=number_format($ExtraChargesData[$i]['taxamount'],2,'.','')?>">\
                                                    <input type="hidden" name="outextrachargesname[<?=$pgid?>][]" id="outextrachargesname_<?=$pgid?>_<?=$i+1?>" class="outextrachargesname<?=$pgid?>" value="<?=$ExtraChargesData[$i]['extrachargesname']?>">\
                                                    <input type="hidden" name="outextrachargepercentage[<?=$pgid?>][]" id="outextrachargepercentage_<?=$pgid?>_<?=$i+1?>" class="outextrachargepercentage<?=$pgid?>" value="<?=$ExtraChargesData[$i]['extrachargepercentage']?>">\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-sm-3 pl-xs pr-xs">\
                                            <div class="form-group p-n" id="outextrachargeamount_<?=$pgid?>_<?=$i+1?>_div">\
                                                <div class="col-sm-12">\
                                                    <input type="text" id="outextrachargeamount_<?=$pgid?>_<?=$i+1?>" name="outextrachargeamount[<?=$pgid?>][]" class="form-control text-right outextrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value,8)" value="<?=number_format($ExtraChargesData[$i]['amount'],2,'.','')?>">\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-3 text-right pt-md">\
                                            '+ec_btn+'\
                                        </div>\
                                    </div>';
        <?php } ?>
    <?php } ?>

    var production_plan = '<?php if($this->session->flashdata('productionplandata')) { echo json_encode($this->session->flashdata('productionplandata')); } ?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1>
            <?php if($processtype=="OUT" || $processtype=="NEXTPROCESS"){ if(isset($action)){ echo 'Edit Stock Out Process'; } else{ if($processtype=="NEXTPROCESS"){ echo 'Stock Out Process';}else{ echo 'Start New Process | Stock Out Process';} } }else if($processtype=="IN"){ if(isset($action)){ echo 'Edit Stock In Process'; } else{ echo 'Stock In Process'; } }else if($processtype=="REPROCESS"){  if(isset($action)){ echo 'Edit Reprocessing'; } else{ echo 'Send for Reprocessing'; } }else if($processtype=="REPROCESS"){  if(isset($action)){ echo 'Edit Reprocessing'; } else{ echo 'Send for Reprocessing'; } } ?> 
        </h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if($processtype=="OUT" || $processtype=="NEXTPROCESS"){  if(isset($action)){ echo 'Edit Stock Out Process'; } else{ if($processtype=="NEXTPROCESS"){ echo 'Stock Out Process';}else{ echo 'Start New Process'; } } }else if($processtype=="IN"){  if(isset($action)){ echo 'Edit Stock In Process'; } else{ echo 'Stock In Process'; } }else if($processtype=="REPROCESS"){  if(isset($action)){ echo 'Edit Reprocessing'; } else{ echo 'Send for Reprocessing'; } } ?></li>
            </ol>
        </small>
    </div>
    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <form class="form-horizontal" id="product-process-form">
                <div class="panel panel-default border-panel">
                    <div class="panel-body">
                        <div class="col-sm-12">
                            <input type="hidden" name="postprocessgroupid" id="postprocessgroupid" value="<?php if(isset($pgid)){ echo $pgid; }?>">
                            <input type="hidden" name="processtype" value="<?php if(isset($processtype)){ echo $processtype; }?>">
                            
                            <input type="hidden" name="postproductionplanid" id="postproductionplanid" value="<?php echo $postproductionplanid; ?>">
                            
                            <input type="hidden" id="removeproductprocessdetailid" name="removeproductprocessdetailid" value="">
                            <input type="hidden" name="removeextrachargemappingid" id="removeextrachargemappingid">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="processgroup_div">
                                        <label class="col-md-3 pl-sm control-label" for="processgroupid">Process Group <span class="mandatoryfield">*</span></label>
                                        <div class="col-md-9">
                                            <select id="processgroupid" name="processgroupid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" title="Select Process Group" multiple <?php if(isset($productprocessdata) && isset($action)){ echo "disabled"; }else{ echo $disabled; } ?>>
                                                <!-- <option value="0">Select Process Group</option> -->
                                                <?php if(!empty($processgroupdata)){ foreach($processgroupdata as $processgroup){ ?>
                                                <option data-process="<?=str_replace('"','\'',$processgroup['processdata'])?>" value="<?php echo $processgroup['id']; ?>" <?php if((isset($productprocessdata) && $productprocessdata['processgroupid']==$processgroup['id']) || (isset($processgroupid) && in_array($processgroup['id'], $processgroupid))){ echo "selected"; }?>><?php echo ucwords($processgroup['name']); ?></option>
                                                <?php }} ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="multiprocess">
                    <?php if(isset($processtype) && $processtype=="IN"){ ?>
                        <div class="panel panel-default border-panel">
                            <div class="panel-body">
                                <input type="hidden" name="processbymemberid[<?=$pgid?>]" value="<?=$processbymemberid?>">
                                <input type="hidden" name="postvendorid[<?=$pgid?>]" value="<?=$postvendorid?>">
                                <input type="hidden" name="postmachineid[<?=$pgid?>]" value="<?=$postmachineid?>">
                                <input type="hidden" name="postestimatedate[<?=$pgid?>]" value="<?=$estimatedate?>">
                                <input type="hidden" name="postorderid[<?=$pgid?>]" value="<?=$postorderid?>"></input>
                                <input type="hidden" id="productprocessid<?=$pgid?>" name="productprocessid[<?=$pgid?>]" value="<?php if(isset($productprocessdata) && isset($action)){ echo $productprocessdata['id']; } ?>">
                                <input type="hidden" id="mainbatchprocessid<?=$pgid?>" name="mainbatchprocessid[<?=$pgid?>]" value="<?php if(isset($productprocessdata)){ echo $productprocessdata['productprocessid']; }else{ echo "0"; } ?>">
                                <input type="hidden" id="parentproductprocessid<?=$pgid?>" name="parentproductprocessid[<?=$pgid?>]" value="<?php if(isset($productprocessdata) && !isset($action)){ echo $productprocessdata['id']; }else{ echo "0"; } ?>">

                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group" id="process_div<?=$pgid?>">
                                                <label class="col-md-3 control-label" for="processid<?=$pgid?>">Process <span class="mandatoryfield">*</span></label>
                                                <div class="col-md-9">
                                                    <textarea style="display:none;" id="jsonprocessid<?=$pgid?>"><?=$processdata?></textarea>
                                                    <input type="hidden" name="processgroupmappingid[<?=$pgid?>]" id="processgroupmappingid<?=$pgid?>" value="<?php if(isset($productprocessdata)){ echo $productprocessdata['processgroupmappingid']; }?>">
                                                    <input type="hidden" name="qcrequire[<?=$pgid?>]" id="qcrequire<?=$pgid?>" value="<?php if(isset($productprocessdata)){ echo $productprocessdata['qcrequire']; }?>">
                                                    <select id="processid<?=$pgid?>" name="processid[<?=$pgid?>]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" <?php if(isset($productprocessdata) && isset($action)){ echo "disabled"; }else{ echo $disabled; } ?>>
                                                        <option value="0">Select Process</option>
                                                    </select> 
                                                    <div id="noofsequence<?=$pgid?>"></div>  
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-3 control-label">Processed By</label>
                                                <div class="col-md-9 mt-xs">
                                                    <div class="col-md-6 col-xs-4" style="padding-left: 0px;">
                                                        <div class="radio">
                                                            <input type="radio" name="processedby[<?=$pgid?>]" id="inhouse<?=$pgid?>" value="1" <?php if((isset($productprocessdata) && $productprocessdata['processbymemberid']==1) || (!isset($productprocessdata) && $processtype=="OUT")){ echo "checked"; }?> <?=($processtype=="IN"?$disabled:"")?>>
                                                            <label for="inhouse<?=$pgid?>">In-House Emp</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xs-4">
                                                        <div class="radio">
                                                            <input type="radio" name="processedby[<?=$pgid?>]" id="otherparty<?=$pgid?>" value="0" <?php if(isset($productprocessdata) && $productprocessdata['processbymemberid']==0){ echo "checked"; }?> <?=($processtype=="IN"?$disabled:"")?>>
                                                            <label for="otherparty<?=$pgid?>">Other Party</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group" id="vendor_div<?=$pgid?>" style="<?php if((isset($productprocessdata) && $productprocessdata['processbymemberid']==1 || (!isset($productprocessdata) && $processtype=="OUT"))){ echo "display:none;"; }?>">
                                                <label class="col-md-3 control-label" for="vendorid<?=$pgid?>">Vendor</label>
                                                <div class="col-md-9">
                                                    <select id="vendorid<?=$pgid?>" name="vendorid[<?=$pgid?>]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" <?=((($processtype=="NEXTPROCESS" || $processtype=="REPROCESS" || ( $processtype=="OUT" && isset($action))) && isset($productprocessdata) && $productprocessdata['processbymemberid']==0)?"":"disabled")?>>
                                                        <option value="0">Select Vendor</option>
                                                        <?php if(!empty($vendordata)){ foreach($vendordata as $vendor){ ?>
                                                        <option value="<?php echo $vendor['id']; ?>" <?php if(isset($productprocessdata) && $productprocessdata['vendorid']==$vendor['id']){ echo "selected"; }?>><?php echo ucwords($vendor['name']); ?></option>
                                                        <?php }} ?>
                                                    </select>   
                                                </div>
                                            </div>
                                            <div class="form-group" id="machine_div<?=$pgid?>" style="<?php if((isset($productprocessdata) && $productprocessdata['processbymemberid']==0)){ echo "display:none;"; }?>">
                                                <label class="col-md-3 control-label" for="machineid<?=$pgid?>">Machine</label>
                                                <div class="col-md-9">
                                                    <select id="machineid<?=$pgid?>" name="machineid[<?=$pgid?>]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" <?=(($processtype=="IN")?"disabled":"")?>>
                                                        <option value="0">Select Machine</option>
                                                    </select>   
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" id="batchno_div<?=$pgid?>">
                                                <label class="col-md-4 control-label" for="batchno<?=$pgid?>">Batch No. <span class="mandatoryfield">*</span></label>
                                                <div class="col-md-8">
                                                    <input id="batchno<?=$pgid?>" class="form-control" name="batchno[<?=$pgid?>]" value="<?php if(isset($productprocessdata)){ echo $productprocessdata['batchno']; }elseif (isset($processbatchno)){ echo $processbatchno; }?>" <?php if($processtype=="IN"){ echo "readonly"; }?>>
                                                </div>
                                            </div>
                                            <div class="form-group" id="order_div<?=$pgid?>">
                                                <label class="col-md-4 control-label" for="orderid">Order</label>
                                                <div class="col-md-8">
                                                    <select id="orderid<?=$pgid?>" name="orderid[<?=$pgid?>]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" <?=(($processtype=="IN")?"disabled":"")?>>
                                                        <option value="0">Select Order</option>
                                                        <?php if(!empty($orderdata)){ 
                                                            foreach($orderdata as $order){?>
                                                                <option value="<?=$order['id']?>" <?php if(!empty($postorderid) && $postorderid==$order['id']){ echo "selected"; }?>><?=$order['orderid']?></option>
                                                        <?php }
                                                        } ?>
                                                    </select>   
                                                </div>
                                            </div>
                                            <div class="form-group" id="transactiondate_div<?=$pgid?>">
                                                <label for="transactiondate<?=$pgid?>" class="col-md-4 control-label">Transaction Date <span class="mandatoryfield">*</span></label>
                                                <div class="col-sm-8">
                                                    <input id="transactiondate<?=$pgid?>" type="text" name="transactiondate[<?=$pgid?>]" value="<?php if(isset($productprocessdata) && isset($action)){ echo $this->general_model->displaydate($productprocessdata['transactiondate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group" id="estimatedate_div<?=$pgid?>">
                                                <label for="estimatedate<?=$pgid?>" class="col-md-4 control-label">Estimate Date<!--  <span class="mandatoryfield">*</span> --></label>
                                                <div class="col-sm-8">
                                                    <input id="estimatedate<?=$pgid?>" type="text" name="estimatedate[<?=$pgid?>]" value="<?php if(isset($productprocessdata) && isset($action) && $productprocessdata['estimatedate']!="0000-00-00"){ echo $this->general_model->displaydate($productprocessdata['estimatedate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" class="form-control" readonly <?php if($processtype=="IN"){ echo "readonly"; } ?>>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="outproductsdiv<?=$pgid?>" style="<?php if($processtype=="OUT"){ echo "display:none"; }?>">
                                        <div class="col-md-12 p-n"><hr></div>
                                        <div class="panel-heading"><h2><b>OUT Product Details</b></h2></div>
                                        <?php if($processtype=="IN" && !empty($productprocessdata['optiondata'])){
                                            $rejkey = array_search('rejection', array_column($productprocessdata['optiondata'], 'name'));
                                            $rejectionval = ($rejkey>=0?$productprocessdata['optiondata'][$rejkey]['optionvalue']:0);
                                            
                                            $wakey = array_search('wastage', array_column($productprocessdata['optiondata'], 'name'));
                                            $wastageval = ($wakey!=""?$productprocessdata['optiondata'][$wakey]['optionvalue']:0);
                                            $lskey = array_search('lost', array_column($productprocessdata['optiondata'], 'name'));
                                            $lostval = ($lskey!=""?$productprocessdata['optiondata'][$lskey]['optionvalue']:0);
                                        } ?>
                                        
                                        <div class="col-md-12 p-n" id="productdata<?=$pgid?>">
                                        <?php if(!empty($productprocessdata['outproducts']) && $processtype=="IN"){ ?>
                                            <table id="" class="table table-striped table-bordered mb-sm" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr style="border-bottom: 2px solid #ddd;">
                                                        <th width="20%">Product</th> 
                                                        <th width="20%">Price / Variant</th> 
                                                        <th class="width8">Unit</th> 
                                                        <th class="width8">Qty</th> 
                                                        <?php if(isset($rejectionval) && $rejectionval==1){ ?>
                                                            <th>Rejection</th>
                                                        <?php } ?>
                                                        <?php if(isset($wastageval) && $wastageval==1){ ?>
                                                            <th>Wastage</th> 
                                                        <?php } ?> 
                                                        <?php if(isset($lostval) && $lostval==1){ ?>
                                                            <th>Lost</th> 
                                                        <?php } ?> 
                                                    </tr>
                                                </thead>
                                                <tbody>
                                            
                                                <?php foreach($productprocessdata['outproducts'] as $p=>$outproduct){    
                                                    $processoptionids = !empty($outproduct['processoptionids'])?explode(",",$outproduct['processoptionids']):"";
                                                    $unitids = !empty($outproduct['unitids'])?explode(",",$outproduct['unitids']):"";
                                                    $processoptionvalue = !empty($outproduct['processoptionvalue'])?explode(",",$outproduct['processoptionvalue']):"";
                                                    $productprocessoptionids = !empty($outproduct['productprocessoptionids'])?explode(",",$outproduct['productprocessoptionids']):"";
                                                    $rkey = $wkey = $lkey = 1;
                                                    
                                                    if(!empty($processoptionids)){
                                                        if(in_array($productprocessdata['optiondata'][$rejkey]['id'],$processoptionids)){
                                                            $rkey = array_search($productprocessdata['optiondata'][$rejkey]['id'],$processoptionids);
                                                        }
                                                        if(in_array($productprocessdata['optiondata'][$wakey]['id'],$processoptionids)){
                                                            $wkey = array_search($productprocessdata['optiondata'][$wakey]['id'],$processoptionids);
                                                        }
                                                        if(in_array($productprocessdata['optiondata'][$lskey]['id'],$processoptionids)){
                                                            $lkey = array_search($productprocessdata['optiondata'][$lskey]['id'],$processoptionids);
                                                        }
                                                    }
                                                    
                                                    $rejqty = $rejproductprocessoptionid = $wasqty = $wasproductprocessoptionid = $lostqty = $lostproductprocessoptionid = $rejunitid = $wasunitid = $lostunitid = "";
                                                    if(isset($action) && trim($rkey)!="" && $rkey>=0 && !empty($processoptionvalue[$rkey]) && !empty($processoptionids) && in_array($productprocessdata['optiondata'][$rejkey]['id'],$processoptionids)){
                                                        $rejqty =  $processoptionvalue[$rkey];
                                                        if(!empty($productprocessoptionids[$rkey])){
                                                            $rejproductprocessoptionid = $productprocessoptionids[$rkey];
                                                        }
                                                        if(!empty($unitids[$rkey])){
                                                            $rejunitid = $unitids[$rkey];
                                                        }
                                                    }else{
                                                        $rejqty =  "";
                                                    }
                                                    
                                                    if(isset($action) && $wkey>=0 && !empty($processoptionvalue[$wkey]) && !empty($processoptionids) && in_array($productprocessdata['optiondata'][$wakey]['id'],$processoptionids)){
                                                        $wasqty =  $processoptionvalue[$wkey];

                                                        if(!empty($productprocessoptionids[$wkey])){
                                                            $wasproductprocessoptionid = $productprocessoptionids[$wkey];
                                                        }
                                                        if(!empty($unitids[$wkey])){
                                                            $wasunitid = $unitids[$wkey];
                                                        }
                                                    }else{
                                                        $wasqty =  "";
                                                    }
                                                    if(isset($action) && $lkey>=0 && !empty($processoptionvalue[$lkey]) && !empty($processoptionids) && in_array($productprocessdata['optiondata'][$lskey]['id'],$processoptionids)){
                                                        $lostqty =  $processoptionvalue[$lkey];

                                                        if(!empty($productprocessoptionids[$lkey])){
                                                            $lostproductprocessoptionid = $productprocessoptionids[$lkey];
                                                        }
                                                        if(!empty($unitids[$lkey])){
                                                            $lostunitid = $unitids[$lkey];
                                                        }
                                                    }else{
                                                        $lostqty =  "";
                                                    }
                                                    $unitiddata = !empty($outproduct['unitiddata'])?array_unique(explode(",",$outproduct['unitiddata'])):array();
                                                    $unitnamedata = !empty($outproduct['unitnamedata'])?explode(",",$outproduct['unitnamedata']):array();
                                                    
                                                    $optionkey = $outproduct['id'].'_'.$outproduct['stocktype'].'_'.$outproduct['stocktypeid'];
                                                    ?>

                                                    <tr>
                                                        <td><?=$outproduct['productname']?>
                                                            <input type="hidden" name="outproductprocessdetailid[<?=$pgid?>][]" id="outproductprocessdetailid_<?=$pgid?>_<?=($p+1)?>" value="<?=$outproduct['id']?>">
                                                            <input type="hidden" name="tpproductpriceid[<?=$pgid?>][]" class="outproductpricesid<?=$pgid?>" id="outproductpricesid_<?=$pgid?>_<?=($p+1)?>" value="<?=$outproduct['productpriceid']?>">
                                                            <input type="hidden" name="tpreferenceid[<?=$pgid?>][]" value="<?=$outproduct['referenceid']?>">
                                                            <input type="hidden" name="tpstocktype[<?=$pgid?>][]" value="<?=$outproduct['stocktype']?>">
                                                            <input type="hidden" name="tpstocktypeid[<?=$pgid?>][]" value="<?=$outproduct['stocktypeid']?>">
                                                            <input type="hidden" name="tpproductid[<?=$pgid?>][]" value="<?=$outproduct['productid']?>">
                                                            <input type="hidden" id="tpqty_<?=$pgid?>_<?=($p+1)?>" value="<?=$outproduct['quantity']?>">
                                                        </td>
                                                        <td><?=$outproduct['variantname']?></td>
                                                        <td><?=$outproduct['unitname']?></td>
                                                        <td><?=$outproduct['quantity']?></td>
                                                        <?php if(isset($rejectionval) && $rejectionval==1){ ?>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group mt-n p-n">
                                                                        <div class="col-sm-12 pr-sm">
                                                                            <input type="text" id="rejection_<?=$pgid?>_<?=($p+1)?>" class="form-control processoptionclass<?=$pgid?>" name="optionvalue[<?=$pgid?>][<?=$optionkey?>][]" onkeypress="return decimal_number_validation(event, this.value,8)" value="<?php echo $rejqty; ?>" placeholder="Qty">
                                                                            <input type="hidden" name="optionid[<?=$pgid?>][<?=$optionkey?>][]" value="<?=$productprocessdata['optiondata'][$rejkey]['id']?>">
                                                                            <input type="hidden" name="productprocessoptionid[<?=$pgid?>][<?=$optionkey?>][]" value="<?php echo $rejproductprocessoptionid; ?>">                                    
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6 pl-xs">
                                                                    <div class="form-group mt-n p-n" id="rejectionunit_<?=$pgid?>_<?=($p+1)?>_div">
                                                                        <div class="col-sm-12 pl-sm">
                                                                            <select id="rejectionunitid_<?=$pgid?>_<?=($p+1)?>" name="unitid[<?=$pgid?>][<?=$optionkey?>][]" class="selectpicker form-control unitid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                                <option value="0">Unit</option>
                                                                                <?php if(!empty($unitiddata)){ 
                                                                                    foreach($unitiddata as $k=>$unit){ ?>
                                                                                        <option value="<?=$unit?>" <?php if((!empty($rejproductprocessoptionid) && $rejunitid!="" && $unit==$rejunitid) || ($rejunitid=="" && $unitnamedata[$k]==$outproduct['unitname'])){ echo "selected"; }?>><?=$unitnamedata[$k]?></option>
                                                                                    <?php }
                                                                                }?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <?php } ?>
                                                        <?php if(isset($wastageval) && $wastageval==1){ ?>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group mt-n p-n">
                                                                        <div class="col-sm-12 pr-sm">
                                                                            <input type="text" id="wastage_<?=$pgid?>_<?=($p+1)?>" class="form-control processoptionclass<?=$pgid?>" name="optionvalue[<?=$pgid?>][<?=$optionkey?>][]" onkeypress="return decimal_number_validation(event, this.value,8)" value="<?php echo $wasqty; ?>" placeholder="Qty">
                                                                            <input type="hidden" name="optionid[<?=$pgid?>][<?=$optionkey?>][]" value="<?=$productprocessdata['optiondata'][$wakey]['id']?>">
                                                                            <input type="hidden" name="productprocessoptionid[<?=$pgid?>][<?=$optionkey?>][]" value="<?php echo $wasproductprocessoptionid; ?>">          
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6 pl-xs">
                                                                    <div class="form-group mt-n p-n" id="wastageunit_<?=$pgid?>_<?=($p+1)?>_div">
                                                                        <div class="col-sm-12 pl-sm">
                                                                            <select id="wastageunitid_<?=$pgid?>_<?=($p+1)?>" name="unitid[<?=$pgid?>][<?=$optionkey?>][]" class="selectpicker form-control unitid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                                <option value="0">Unit</option>
                                                                                <?php if(!empty($unitiddata)){ 
                                                                                    foreach($unitiddata as $w=>$unit){ ?>
                                                                                        <option value="<?=$unit?>" <?php if((!empty($wasproductprocessoptionid) && $wasunitid!="" && $unit==$wasunitid) || ($wasunitid=="" && $unitnamedata[$w]==$outproduct['unitname'])){ echo "selected"; }?>><?=$unitnamedata[$w]?></option>
                                                                                    <?php }
                                                                                }?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div> 
                                                            </div>   
                                                        </td>
                                                        <?php } ?>
                                                        <?php if(isset($lostval) && $lostval==1){ ?>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group mt-n p-n">
                                                                        <div class="col-sm-12 pr-sm">
                                                                            <input type="text" id="lost_<?=$pgid?>_<?=($p+1)?>" class="form-control processoptionclass<?=$pgid?>" name="optionvalue[<?=$pgid?>][<?=$optionkey?>][]" onkeypress="return decimal_number_validation(event, this.value,8)" value="<?php echo $lostqty; ?>" placeholder="Qty">
                                                                            <input type="hidden" name="optionid[<?=$pgid?>][<?=$optionkey?>][]" value="<?=$productprocessdata['optiondata'][$lskey]['id']?>">
                                                                            <input type="hidden" name="productprocessoptionid[<?=$pgid?>][<?=$optionkey?>][]" value="<?php echo $lostproductprocessoptionid; ?>">          
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6 pl-xs">
                                                                    <div class="form-group mt-n p-n" id="lostunit_<?=$pgid?>_<?=($p+1)?>_div">
                                                                        <div class="col-sm-12 pl-sm">
                                                                            <select id="lostunitid_<?=$pgid?>_<?=($p+1)?>" name="unitid[<?=$pgid?>][<?=$optionkey?>][]" class="selectpicker form-control unitid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                                <option value="0">Unit</option>
                                                                                <?php if(!empty($unitiddata)){ 
                                                                                    foreach($unitiddata as $l=>$unit){ ?>
                                                                                        <option value="<?=$unit?>" <?php if((!empty($lostproductprocessoptionid) && $lostunitid!="" && $unit==$lostunitid) || ($lostunitid=="" && $unitnamedata[$l]==$outproduct['unitname'])){ echo "selected"; }?>><?=$unitnamedata[$l]?></option>
                                                                                    <?php }
                                                                                }?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div> 
                                                            </div>   
                                                        </td>
                                                        <?php } ?>
                                                    </tr>
                                                
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        <?php } ?>
                                        </div>
                                    </div>

                                    <?php if($processtype=="IN") { ?>
                                        <?php if(!empty($productprocessdata['OutExtraChargesData'])) { ?>
                                        <div class="row">
                                            <div class="col-md-6 p-n">
                                                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr style="border-bottom: 2px solid #ddd;">
                                                            <th class="width12 text-center">Sr. No.</th> 
                                                            <th>Extra Charges</th> 
                                                            <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th> 
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($productprocessdata['OutExtraChargesData'] as $key=>$charges){ ?>
                                                            <tr>
                                                                <td class="width12 text-center"><?=(++$key)?></td>
                                                                <td><?=$charges['extrachargesname']?></td>
                                                                <td class="text-right"><?=numberFormat($charges['amount'],2,',')?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="row" id="inproductsdiv<?=$pgid?>">
                                            <div class="col-md-12 p-n"><hr></div>
                                            <div class="panel-heading"><h2><b>IN Details</b></h2></div>
                                            <div class="col-md-12 p-n">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pr-xs">
                                                            <label class="control-label"><b>Final Product</b></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group p-n">
                                                        <div class="col-sm-12 pl-xs pr-xs">
                                                            <label class="control-label"><b>Select Product</b></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group p-n">
                                                        <div class="col-sm-12 pl-xs pr-xs">
                                                            <label class="control-label"><b>Select Variant</b></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group text-right">
                                                        <div class="col-sm-12 pl-xs pr-xs">
                                                            <label class="control-label"><b>Qty.</b></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pl-xs pr-xs">
                                                            <label class="control-label"><b>Pending Qty.</b></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pl-xs pr-xs">
                                                            <label class="control-label"><b>Labor Cost</b></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pl-xs pr-xs">
                                                            <label class="control-label"><b>Total Cost</b></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 p-n" id="inproductdata<?=$pgid?>">
                                                <?php if(count($productprocessdata['inproducts']) > 0){ 
                                                    foreach($productprocessdata['inproducts'] as $p=>$inproduct){ ?>

                                                        <div class="countinproducts<?=$pgid?>" id="countinproducts_<?=$pgid?>_<?=($p+1)?>" style="width: 100%;float: left;">
                                                            <input type="hidden" name="productprocessdetailid[<?=$pgid?>][]" id="productprocessdetailid_<?=$pgid?>_<?=($p+1)?>" value="<?php if(isset($action)){ echo $inproduct['id']; }?>">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <div class="col-sm-12 pl-xl pr-xs">
                                                                        <div class="yesno">
                                                                        <input type="checkbox" name="finalproduct_<?=$pgid?>_<?=($p+1)?>" value="0" <?php if(isset($action) && $inproduct['isfinalproduct']==1){ echo "checked"; }?>>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group p-n mt-n" id="inproduct_<?=$pgid?>_<?=($p+1)?>_div">
                                                                    <div class="col-sm-12 pl-xs pr-xs">
                                                                        <input type="hidden" id="preinproductid_<?=$pgid?>_<?=($p+1)?>" value="<?=$inproduct['productid']?>">
                                                                        <select id="inproductid_<?=$pgid?>_<?=($p+1)?>" name="inproductid[<?=$pgid?>][]" class="selectpicker form-control inproductid<?=$pgid?>" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                            <option value="0">Select Product</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group p-n mt-n" id="inproductvariant_<?=$pgid?>_<?=($p+1)?>_div">
                                                                    <div class="col-sm-12 pl-xs pr-xs">
                                                                        <input type="hidden" id="preinproductvariantid_<?=$pgid?>_<?=($p+1)?>" value="<?=$inproduct['productpriceid']?>">
                                                                        <select id="inproductvariantid_<?=$pgid?>_<?=($p+1)?>" name="inproductvariantid[<?=$pgid?>][]" class="selectpicker form-control inproductvariantid<?=$pgid?>" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                            <option value="0" data-price='0'>Select Variant</option>
                                                                        </select>
                                                                        <input type="hidden" name="inprice[<?=$pgid?>][]" id="inprice_<?=$pgid?>_<?=($p+1)?>" value="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group mt-n" id="inquantity_<?=$pgid?>_<?=($p+1)?>_div">
                                                                    <div class="col-sm-12 pl-xs pr-xs">
                                                                    <?php 
                                                                        $maxqty = 0;
                                                                        if(isset($action)){ 
                                                                            $qty = $inproduct['quantity']; 
                                                                        }else if(!isset($action) && !empty($inproduct['pendingquantity'])){ 
                                                                            $qty = $inproduct['pendingquantity']; 
                                                                        }else if(isset($inproduct['planquantity'])){ 
                                                                            $qty = $inproduct['planquantity']; 
                                                                            $maxqty = $inproduct['planquantity']; 
                                                                        }else{ 
                                                                            $qty = '1'; 
                                                                        }
                                                                    ?>
                                                                        <input type="text" id="inquantity_<?=$pgid?>_<?=($p+1)?>" name="inquantity[<?=$pgid?>][]" class="form-control inquantity<?=$pgid?> text-right" value="<?=(MANAGE_DECIMAL_QTY==1?$qty:(int)$qty)?>" data-maxqty="<?php if(isset($inproduct['planquantity'])){ echo $inproduct['planquantity']; } ?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>">
                                                                        <input type="hidden" id="inproductamount_<?=$pgid?>_<?=($p+1)?>" class="inproductamount<?=$pgid?>" value="">
                                                                        <input type="hidden" name="planningqty[<?=$pgid?>][]" id="planningqty_<?=$pgid?>_<?=($p+1)?>" class="planningqty<?=$pgid?>" value="<?php if(isset($inproduct['planningqty'])){ echo $inproduct['planningqty']; } ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group mt-n" id="inpendingquantity_<?=$pgid?>_<?=($p+1)?>_div">
                                                                    <div class="col-sm-12 pl-xs pr-xs">
                                                                        <input type="text" id="inpendingquantity_<?=$pgid?>_<?=($p+1)?>" name="inpendingquantity[<?=$pgid?>][]" class="form-control inpendingquantity<?=$pgid?> text-right" value="<?php if(isset($action) && $inproduct['pendingquantity']>0){ echo (MANAGE_DECIMAL_QTY==1?$inproduct['pendingquantity']:(int)$inproduct['pendingquantity']); }?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group mt-n" id="inlaborcost_<?=$pgid?>_<?=($p+1)?>_div">
                                                                    <div class="col-sm-12 pl-xs pr-xs">
                                                                        <input type="text" id="inlaborcost_<?=$pgid?>_<?=($p+1)?>" name="inlaborcost[<?=$pgid?>][]" class="form-control laborcost<?=$pgid?> text-right" value="<?php if(!empty($inproduct['laborcost'])){ echo number_format($inproduct['laborcost'],2,'.',''); }?>" onkeypress="return decimal_number_validation(event, this.value,8)">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group mt-n" id="intotalcost_<?=$pgid?>_<?=($p+1)?>_div">
                                                                    <div class="col-sm-12 pl-xs pr-xs">
                                                                        <input type="text" id="intotalcost_<?=$pgid?>_<?=($p+1)?>" name="intotalcost[<?=$pgid?>][]" class="form-control intotalcost<?=$pgid?> text-right" value="<?php if(isset($action) && $inproduct['laborcost']>0){ echo number_format($inproduct['quantity'] * $inproduct['laborcost'],2,'.',''); }?>" onkeypress="return decimal_number_validation(event, this.value,8)" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1 text-right pt-md">
                                                                <?php if(count($productprocessdata['inproducts'])>1){ ?>
                                                                    <?php if($p==0){?>
                                                                        <?php if(count($productprocessdata['inproducts'])>1){ ?>
                                                                            <button type="button" class="btn btn-default btn-raised remove_inproduct_btn<?=$pgid?> m-n" onclick="removeinproduct(1,<?=$pgid?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                                        <?php }else { ?>
                                                                            <button type="button" class="btn btn-default btn-raised add_inproduct_btn<?=$pgid?> m-n" onclick="addnewinproduct(<?=$pgid?>)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                                        <?php } ?>

                                                                    <?php }else if($p!=0) { ?>
                                                                        <button type="button" class="btn btn-default btn-raised remove_inproduct_btn<?=$pgid?> m-n" onclick="removeinproduct(<?=$p+1?>,<?=$pgid?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                                    <?php } ?>
                                                                    <button type="button" class="btn btn-default btn-raised btn-sm remove_inproduct_btn<?=$pgid?> m-n" onclick="removeinproduct(<?=$p+1?>,<?=$pgid?>)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                                
                                                                    <button type="button" class="btn btn-default btn-raised add_inproduct_btn<?=$pgid?> m-n" onclick="addnewinproduct(<?=$pgid?>)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                                <?php } ?>
                                                            </div>
                                                            <?php if(/* STOCK_MANAGE_BY==1 &&  */count($inproduct['productstockdata']) > 0){ ?>
                                                                <div class="col-md-12 p-n">
                                                                    <div class="col-md-8"></div>
                                                                    <div class="col-md-1">
                                                                        <div class="form-group">
                                                                            <div class="col-sm-12 pl-xs pr-xs">
                                                                                <label class="control-label"><b>Price</b></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <div class="form-group">
                                                                            <div class="col-sm-12 pl-xs pr-xs">
                                                                                <label class="control-label"><b>Qty</b></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php foreach($inproduct['productstockdata'] as $s=>$productstock){ ?>
                                                                    <div class="col-md-12 p-n">
                                                                        <input type="hidden" name="stockmappingid[<?=$pgid?>][<?=($p+1)?>][]" id="stockmappingid_<?=$pgid?>_<?=($p+1)?>_<?=($s+1)?>" value="<?=$productstock['id']?>">
                                                                        <input type="hidden" name="stocktype[<?=$pgid?>][<?=($p+1)?>][]" id="stocktype_<?=$pgid?>_<?=($p+1)?>_<?=($s+1)?>" value="<?=$productstock['stocktype']?>">
                                                                        <input type="hidden" name="stocktypeid[<?=$pgid?>][<?=($p+1)?>][]" id="stocktypeid_<?=$pgid?>_<?=($p+1)?>_<?=($s+1)?>" value="<?=$productstock['stocktypeid']?>">
                                                                        <input type="hidden" id="outqty_<?=$pgid?>_<?=($p+1)?>_<?=($s+1)?>" value="<?=$productstock['qty']?>">
                                                                        
                                                                        <div class="col-md-8"></div>
                                                                        <div class="col-md-1" style="border-bottom: 1px solid #D2D2D2;">
                                                                            <div class="form-group">
                                                                                <div class="col-sm-12 pl-xs pr-xs mt-sm">
                                                                                    <label class="control-label"><?=$productstock['productprice']?></label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-1">
                                                                            <div class="form-group">
                                                                                <div class="col-sm-12 pl-xs pr-xs">
                                                                                    <input type="text" id="stockqty_<?=$pgid?>_<?=($p+1)?>_<?=($s+1)?>" name="stockqty[<?=$pgid?>][<?=($p+1)?>][]" class="form-control stockqty_<?=$pgid?>_<?=($p+1)?> stockqty<?=$pgid?> text-right" maxlength="4" value="<?=isset($action)?(MANAGE_DECIMAL_QTY==0?(int)$productstock['qty']:$productstock['qty']):""?>" onkeypress="return isNumber(event)">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <?php }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                } ?>
                                                        </div>
                                                <?php }
                                                } ?>
                                            </div>
                                            <div id="inextrachargesdiv<?=$pgid?>">
                                                <div class="col-md-12"><hr></div>
                                                <div class="panel-heading"><h2><b>Extra Charges</b></h2></div>
                                                <div class="col-md-12 p-n" id="extrachargesdata<?=$pgid?>">

                                                    <?php if(isset($action) && !empty($ExtraChargesData)) { ?>
                                                        <?php for ($i=0; $i < count($ExtraChargesData); $i++) { ?>
                                                            <div class="col-md-4 p-n countincharges<?=$pgid?>" id="countincharges_<?=$pgid?>_<?=$i+1?>">
                                                                <div class="col-sm-6 pr-xs">
                                                                    <input type="hidden" name="inextrachargemappingid[<?=$pgid?>][]" value="<?=$ExtraChargesData[$i]['id']?>" id="inextrachargemappingid_<?=$pgid?>_<?=$i+1?>">
                                                                    
                                                                    <div class="form-group p-n" id="inextracharges_<?=$pgid?>_<?=$i+1?>_div">
                                                                        <div class="col-sm-12">
                                                                            <select id="inextrachargesid_<?=$pgid?>_<?=$i+1?>" name="inextrachargesid[<?=$pgid?>][]" class="selectpicker form-control inextrachargesid<?=$pgid?>" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                                <option value="0">Select Charges</option>
                                                                                <?php foreach($extrachargesdata as $extracharges){ ?>
                                                                                    <option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>" <?php if($ExtraChargesData[$i]['extrachargesid'] == $extracharges['id']){ echo "selected"; } ?>><?php if($ExtraChargesData[$i]['extrachargesid'] == $extracharges['id']){ echo $ExtraChargesData[$i]['extrachargesname']; }else{ echo $extracharges['extrachargename']; } ?></option>
                                                                                <?php } ?>
                                                                            </select>

                                                                            <input type="hidden" name="inextrachargestax[<?=$pgid?>][]" id="inextrachargestax_<?=$pgid?>_<?=$i+1?>" class="inextrachargestax<?=$pgid?>" value="<?=number_format($ExtraChargesData[$i]['taxamount'],2,'.','')?>">
                                                                            <input type="hidden" name="inextrachargesname[<?=$pgid?>][]" id="inextrachargesname_<?=$pgid?>_<?=$i+1?>" class="inextrachargesname<?=$pgid?>" value="<?=$ExtraChargesData[$i]['extrachargesname']?>">
                                                                            <input type="hidden" name="inextrachargepercentage[<?=$pgid?>][]" id="inextrachargepercentage_<?=$pgid?>_<?=$i+1?>" class="inextrachargepercentage<?=$pgid?>" value="<?=$ExtraChargesData[$i]['extrachargepercentage']?>">
                                                                        </div>
                                                                    </div>
                                                                
                                                                </div>
                                                                <div class="col-sm-3 pl-xs pr-xs">
                                                                    <div class="form-group p-n" id="inextrachargeamount_<?=$pgid?>_<?=$i+1?>_div">
                                                                        <div class="col-sm-12">
                                                                            <input type="text" id="inextrachargeamount_<?=$pgid?>_<?=$i+1?>" name="inextrachargeamount[<?=$pgid?>][]" class="form-control text-right inextrachargeamount<?=$pgid?>" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value,8)" value="<?=number_format($ExtraChargesData[$i]['amount'],2,'.','')?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 text-right pt-md">
                                                                    <?php if($i==0){?>
                                                                        <?php if(count($ExtraChargesData)>1){ ?>
                                                                            <button type="button" class="btn btn-default btn-raised remove_incharges_btn<?=$pgid?> m-n" onclick="removecharge(1,<?=$pgid?>,1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                                        <?php }else { ?>
                                                                            <button type="button" class="btn btn-default btn-raised add_incharges_btn<?=$pgid?> m-n" onclick="addnewcharge(<?=$pgid?>,1)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                                        <?php } ?>

                                                                    <?php }else if($i!=0) { ?>
                                                                        <button type="button" class="btn btn-default btn-raised remove_incharges_btn<?=$pgid?> m-n" onclick="removecharge(<?=$i+1?>,<?=$pgid?>,1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                                    <?php } ?>
                                                                    <button type="button" class="btn btn-default btn-raised btn-sm remove_incharges_btn<?=$pgid?> m-n" onclick="removecharge(<?=$i+1?>,<?=$pgid?>,1)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                                
                                                                    <button type="button" class="btn btn-default btn-raised add_incharges_btn<?=$pgid?> m-n" onclick="addnewcharge(<?=$pgid?>,1)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>  
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php }else{ ?>
                                                        <div class="col-md-4 p-n countincharges<?=$pgid?>" id="countincharges_<?=$pgid?>_1">   
                                                            <div class="col-sm-6 pr-xs">
                                                                <div class="form-group p-n" id="inextracharges_<?=$pgid?>_1_div">
                                                                    <div class="col-sm-12">
                                                                        <select id="inextrachargesid_<?=$pgid?>_1" name="inextrachargesid[<?=$pgid?>][]" class="selectpicker form-control inextrachargesid<?=$pgid?>" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                            <option value="0">Select Charges</option>
                                                                            <?php if(!empty($extrachargesdata)){ 
                                                                                foreach($extrachargesdata as $extracharges){ ?>
                                                                                    <option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>"><?php echo $extracharges['extrachargename']; ?></option>
                                                                            <?php }
                                                                            }?>
                                                                        </select>

                                                                        <input type="hidden" name="inextrachargestax[<?=$pgid?>][]" id="inextrachargestax_<?=$pgid?>_1" class="inextrachargestax<?=$pgid?>" value="">
                                                                        <input type="hidden" name="inextrachargesname[<?=$pgid?>][]" id="inextrachargesname_<?=$pgid?>_1" class="inextrachargesname<?=$pgid?>" value="">
                                                                        <input type="hidden" name="inextrachargepercentage[<?=$pgid?>][]" id="inextrachargepercentage_<?=$pgid?>_1" class="inextrachargepercentage<?=$pgid?>" value="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-sm-3 pl-xs pr-xs">
                                                                <div class="form-group p-n" id="inextrachargeamount_<?=$pgid?>_1_div">
                                                                    <div class="col-sm-12">
                                                                        <input type="text" id="inextrachargeamount_<?=$pgid?>_1" name="inextrachargeamount[<?=$pgid?>][]" class="form-control text-right inextrachargeamount<?=$pgid?>" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value,8)">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 text-right pt-md">
                                                                <button type="button" class="btn btn-default btn-raised remove_incharges_btn<?=$pgid?> m-n" onclick="removecharge(1,<?=$pgid?>,1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>

                                                                <button type="button" class="btn btn-default btn-raised add_incharges_btn<?=$pgid?> m-n" onclick="addnewcharge(<?=$pgid?>,1)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <?php 
                                                $certikey = array_search('certificate', array_column($productprocessdata['optiondata'], 'name'));
                                                $certificateval = ($certikey!=""?$productprocessdata['optiondata'][$certikey]['optionvalue']:0); ?>
                                                <input type="hidden" name="isCertificate" id="isCertificate" value="<?=$certificateval?>">
                                                <input type="hidden" name="removeproductprocesscertificatesid" id="removeproductprocesscertificatesid">
                                            <?php if($certificateval!=0){ ?>
                                                <div class="col-md-12 p-n"><hr></div>
                                                <div class="col-md-12 p-n">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <div class="col-sm-12 pr-sm">
                                                                <label class="control-label"><b>Doc. No.</b> <span class="mandatoryfield">*</span></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group p-n">
                                                            <div class="col-sm-12 pl-sm pr-sm">
                                                                <label class="control-label"><b>Title</b> <span class="mandatoryfield">*</span></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group p-n">
                                                            <div class="col-sm-12 pl-sm pr-sm">
                                                                <label class="control-label"><b>Description</b></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group p-n">
                                                            <div class="col-sm-12 pl-sm pr-sm">
                                                                <label class="control-label"><b>Uploaded File <span class="mandatoryfield">*</span></b></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group text-center">
                                                            <div class="col-sm-12 pl-sm">
                                                                <label class="control-label"><b>Doc. Date</b></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 p-n">
                                                    <?php 
                                                    if(isset($action)){
                                                        $limit = count($productprocessdata['certificatedata']);
                                                    }else{
                                                        $limit = ($certificateval==-1?1:$certificateval);
                                                    }
                                                    if($limit>0){ 
                                                        for($i=0; $i<$limit; $i++){ ?>

                                                            <div class="countcertificates" id="countcertificates<?=$i+1?>" style="width: 100%;float: left;">
                                                                <input type="hidden" name="productprocesscertificatesid[<?=$i+1?>]" value="<?php if(isset($action)){ echo $productprocessdata['certificatedata'][$i]['id']; }?>" id="productprocesscertificatesid<?=$i+1?>">
                                                                <div class="col-md-2">
                                                                    <div class="form-group p-n" id="docno<?=$i+1?>_div">
                                                                        <div class="col-sm-12 pr-sm">
                                                                            <input type="text" id="docno<?=$i+1?>" name="docno[<?=$i+1?>]" class="form-control docno" value="<?php if(isset($action)){ echo $productprocessdata['certificatedata'][$i]['docno']; } ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group p-n" id="doctitle<?=$i+1?>_div">
                                                                        <div class="col-sm-12 pl-sm pr-sm">
                                                                            <input type="text" id="doctitle<?=$i+1?>" name="doctitle[<?=$i+1?>]" class="form-control doctitle" value="<?php if(isset($action)){ echo $productprocessdata['certificatedata'][$i]['title']; } ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group p-n" id="docdescription<?=$i+1?>_div">
                                                                        <div class="col-sm-12 pl-sm pr-sm">
                                                                            <input type="text" id="docdescription<?=$i+1?>" name="docdescription[<?=$i+1?>]" class="form-control docdescription" value="<?php if(isset($action)){ echo $productprocessdata['certificatedata'][$i]['remarks']; } ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group" id="docfile<?=$i+1?>_div">
                                                                        <div class="col-md-12 pl-sm pr-sm">
                                                                            <input type="hidden" id="isvaliddocfile<?=$i+1?>" value="<?php if(isset($action) && $productprocessdata['certificatedata'][$i]['filename']!=""){ echo 1; }else{ echo 0;} ?>"> 
                                                                            <input type="hidden" name="olddocfile[<?=$i+1?>]" id="olddocfile<?=$i+1?>" value="<?php if(isset($action)){ echo $productprocessdata['certificatedata'][$i]['filename']; } ?>"> 
                                                                            <div class="input-group" id="fileupload<?=$i+1?>">
                                                                                <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                                    <span class="btn btn-primary btn-raised btn-file"><i
                                                                                            class="fa fa-upload"></i>
                                                                                        <input type="file" name="docfile<?=$i+1?>"
                                                                                            class="docfile" id="docfile<?=$i+1?>"
                                                                                            accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png,.pdf" onchange="validcertificatefile($(this),'docfile<?=$i+1?>')">
                                                                                    </span>
                                                                                </span>
                                                                                <input type="text" readonly="" id="Filetextdocfile<?=$i+1?>"
                                                                                    class="form-control" name="Filetextdocfile[<?=$i+1?>]" value="<?php if(isset($action)){ echo $productprocessdata['certificatedata'][$i]['filename']; } ?>">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group" id="docdate<?=$i+1?>_div">
                                                                        <div class="col-sm-12 pl-sm">
                                                                            <input id="docdate<?=$i+1?>" type="text" name="docdate[<?=$i+1?>]" value="<?php if(isset($action)){ echo $this->general_model->displaydate($productprocessdata['certificatedata'][$i]['documentdate']); }else { echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" class="form-control text-center docdate" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php if($certificateval==-1){ ?>
                                                                <div class="col-md-1 text-right pt-md">

                                                                    <?php if(isset($action)){ ?>
                                                                        <?php if($i==0){?>
                                                                            <?php if(count($productprocessdata['certificatedata'])>1){ ?>
                                                                                <button type="button" class="btn btn-default btn-raised remove_certificate_btn m-n" onclick="removecertificate(1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                                            <?php }else { ?>
                                                                                <button type="button" class="btn btn-default btn-raised add_certificate_btn m-n" onclick="addnewcertificate()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                                            <?php } ?>

                                                                        <?php }else if($i!=0) { ?>
                                                                            <button type="button" class="btn btn-default btn-raised remove_certificate_btn m-n" onclick="removecertificate(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                                        <?php } ?>
                                                                        <button type="button" class="btn btn-default btn-raised btn-sm remove_certificate_btn m-n" onclick="removecertificate(<?=$i+1?>)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                                        <button type="button" class="btn btn-default btn-raised add_certificate_btn m-n" onclick="addnewcertificate()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                                    <?php }else{ ?>

                                                                        <button type="button" class="btn btn-default btn-raised btn-sm remove_certificate_btn m-n" onclick="removecertificate(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                                    
                                                                        <button type="button" class="btn btn-default btn-raised add_certificate_btn m-n" onclick="addnewcertificate()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                                    <?php } ?>
                                                                </div>
                                                                <?php } ?>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <div class="row">
                                        <hr>
                                        <div class="<?php if($processtype=="IN"){ echo "col-md-12"; }else{ echo "col-md-12"; }?>">
                                            <div class="form-group" id="remarks_div<?=$pgid?>">
                                                <div class="col-md-12">
                                                    <label class="control-label" for="remarks<?=$pgid?>">Remarks</label>
                                                    <textarea id="remarks<?=$pgid?>" class="form-control" name="remarks[<?=$pgid?>]"><?php if(isset($productprocessdata) && isset($action)){ echo $productprocessdata['comments']; } ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="panel panel-default border-panel">
                    <div class="panel-body">
                        <div class="col-sm-12">
                            <div class="row">
                                <?php if($processtype=="IN" && !empty($productprocessdata['optiondata'])){
                                    $rejkey = array_search('rejection', array_column($productprocessdata['optiondata'], 'name'));
                                    $rejectionval = ($rejkey>=0?$productprocessdata['optiondata'][$rejkey]['optionvalue']:0);
                                    
                                    $wakey = array_search('wastage', array_column($productprocessdata['optiondata'], 'name'));
                                    $wastageval = ($wakey!=""?$productprocessdata['optiondata'][$wakey]['optionvalue']:0);

                                    $lostkey = array_search('lost', array_column($productprocessdata['optiondata'], 'name'));
                                    $lostval = ($lostkey!=""?$productprocessdata['optiondata'][$lostkey]['optionvalue']:0);
                                    ?>
                                    <input type="hidden" name="isRejection" id="isRejection" value="<?=$rejectionval?>">
                                    <input type="hidden" name="isWastage" id="isWastage" value="<?=$wastageval?>">
                                    <input type="hidden" name="isLost" id="isLost" value="<?=$lostval?>">

                                    <?php /*  ?>
                                    <div class="col-md-6">
                                        <?php if($rejectionval==1){ ?>
                                        <div class="form-group" id="rejection_div">
                                            <label class="col-md-6 control-label" for="rejection">Rejection</label>
                                            <div class="col-md-6">
                                                <input type="text" id="rejection" class="form-control text-right" name="optionvalue[]" onkeypress="return decimal_number_validation(event, this.value)" value="<?php if(isset($action) && !empty($productprocessdata['optiondata'][$rejkey]['productprocessoptionvalue'])){ echo number_format($productprocessdata['optiondata'][$rejkey]['productprocessoptionvalue'],2,'.',''); }?>">
                                                <input type="hidden" name="optionid[]" value="<?=$productprocessdata['optiondata'][$rejkey]['id']?>">
                                                <input type="hidden" name="productprocessoptionid[]" value="<?=$productprocessdata['optiondata'][$rejkey]['productprocessoptionid']?>">                                    
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if($wastageval==1){ ?>
                                        <div class="form-group" id="wastage_div">
                                            <label class="col-md-6 control-label" for="wastage">Wastage</label>
                                            <div class="col-md-6">
                                                <input type="text" id="wastage" class="form-control text-right" name="optionvalue[]" onkeypress="return decimal_number_validation(event, this.value)">
                                                <input type="hidden" name="optionid[]" value="<?=$productprocessdata['optiondata'][$wakey]['id']?>">
                                                <input type="hidden" name="productprocessoptionid[]" value="<?=$productprocessdata['optiondata'][$wakey]['productprocessoptionid']?>">
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if($lostval==1){ ?>
                                        <div class="form-group" id="lost_div">
                                            <label class="col-md-6 control-label" for="lost">Lost</label>
                                            <div class="col-md-6">
                                                <input type="text" id="lost" class="form-control text-right" name="optionvalue[]" onkeypress="return decimal_number_validation(event, this.value)">
                                                <input type="hidden" name="optionid[]" value="<?=$productprocessdata['optiondata'][$lostkey]['id']?>">
                                                <input type="hidden" name="productprocessoptionid[]" value="<?=$productprocessdata['optiondata'][$lostkey]['productprocessoptionid']?>">
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                <?php  */ } ?>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-sm-12 text-center">
                                        <?php if(isset($productprocessdata) && isset($action)){ ?>
                                            <input type="button" id="submit" onclick="<?php if($processtype=="IN"){ echo "checkvalidationstockin()"; }else{ echo "checkvalidationstockout()"; } ?>" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                        <?php }else{ ?>
                                            <input type="button" id="submit" onclick="<?php if($processtype=="IN"){ echo "checkvalidationstockin()"; }else{ echo "checkvalidationstockout()"; } ?>" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                            <?php if($processtype=="OUT"){ ?>
                                            <input type="button" id="submit" onclick="checkvalidationstockout(1)" name="submit" value="SAVE & ADD NEW" class="btn btn-primary btn-raised">
                                            <?php } ?>
                                        
                                        <?php } ?>
                                            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->