<?php 
$PROCESS_DATA = '';
if(!empty($processdata)){
  foreach($processdata as $row){ 
    $PROCESS_DATA .= '<option value="'.$row["id"].'">'.$row["name"].'</option>';
  } 
} 
$PRODUCT_DATA = "";
if(!empty($productdata)){
  foreach($productdata as $product){ 
    $productname = str_replace("'","&apos;",$product['name']);
    if(DROPDOWN_PRODUCT_LIST==0){
      $PRODUCT_DATA .= '<option value="'.$product["id"].'" data-variants="'.htmlspecialchars(json_encode($product['variantdata']), ENT_QUOTES, 'UTF-8').'">'.addslashes($productname).'</option>';
    }else{
        $content = "";
        if(!empty($product['image']) && file_exists(PRODUCT_PATH.$product['image'])){
            $content .= '<img src=&quot;'.PRODUCT.$product['image'].'&quot; style=&quot;width:40px;&quot;> '.addslashes($productname);
        }else{
            $content .= '<img src=&quot;'.PRODUCT.PRODUCTDEFAULTIMAGE.'&quot; style=&quot;width:40px;&quot;> '.addslashes($productname);
        }
        $PRODUCT_DATA .= '<option data-content="'.$content.'" value="'.$product['id'].'" data-variants="'.htmlspecialchars(json_encode($product['variantdata']), ENT_QUOTES, 'UTF-8').'">'.addslashes($productname).'</option>';
    }
  } 
}
$UNIT_DATA = '';
if(!empty($unitdata)){
  foreach($unitdata as $row){ 
    $UNIT_DATA .= '<option value="'.$row["id"].'">'.$row["name"].'</option>';
  } 
}
?>
<script>
  var PROCESS_DATA = '<?=$PROCESS_DATA?>';
  var UNIT_DATA = '<?=$UNIT_DATA?>';
  var PRODUCT_DATA = '<?=$PRODUCT_DATA?>';
  var PROCESS_OPTION_DATA = '<?=json_encode($processoptiondata)?>';
  var ISDUPLICATE = '<?=(isset($isduplicate) && isset($processgroupdata)?1:0)?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($processgroupdata) && !isset($isduplicate)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($processgroupdata) && !isset($isduplicate)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>
    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="col-sm-12 p-n">
              <form class="form-horizontal" id="processgroupform">
                <input type="hidden" name="processgroupid" value="<?php if(isset($processgroupdata) && !isset($isduplicate)){ echo $processgroupdata['master']['id']; } ?>">
                
                <!-- Process Group Master section -->
                <div class="panel panel-default border-panel">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group" id="groupname_div">
                          <label class="col-md-3 control-label pr-n" for="groupname">Group Name <span class="mandatoryfield">*</span></label>
                          <div class="col-md-8">
                            <input type="text" id="groupname" class="form-control" name="groupname" value="<?php if(isset($processgroupdata)){ echo $processgroupdata['master']['name']; } ?>">
                          </div>
                        </div>      
                      </div>
                      <div class="col-md-6">
                        <div class="form-group" id="description_div">
                          <label class="col-md-2 control-label" for="description">Description</label>
                          <div class="col-md-10">
                            <textarea id="description" class="form-control" name="description"><?php if(isset($processgroupdata)){ echo $processgroupdata['master']['description']; } ?></textarea>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group" id="process_div">
                            <label for="processid" class="col-sm-3 control-label">Select Process <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                              <?php if(isset($processgroupdata) && $processgroupdata['master']['processid']!=""){
                                  $processIdArray = explode(",", $processgroupdata['master']['processid']);
                              }?>
                                <select id="processid" name="processid[]" class="selectpicker form-control" data-live-search="true" data-actions-box="true" title="Select Process" data-select-on-tab="true" data-size="5" multiple>
                                  <?php if(!empty($processdata)){ foreach($processdata as $process){ ?>
                                  <option value="<?php echo $process['id']; ?>" <?php if(isset($processgroupdata) && in_array($process['id'], $processIdArray)){ echo "selected"; }?>><?php echo ucwords($process['name']); ?></option>
                                  <?php }} ?>
                                </select>
                            </div>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group pt-sm">
                          <input type="button" id="generategroup" onclick="generateprocess()" name="generategroup" value="GENERATE" class="btn btn-primary btn-raised">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div id="processgroup_maindiv" class="sortablepanel">

                <?php if(isset($processgroupdata) && count($processgroupdata['mapping']) > 0){ ?>
                  <input type="hidden" id="removeprocessgroupmappingid" name="removeprocessgroupmappingid" value="">
                  <input type="hidden" id="removeprocessgroupproductid" name="removeprocessgroupproductid" value="">
                  <?php foreach($processgroupdata['mapping'] as $k=>$pgm){
                          $processgroupmappingid = $pgm['id']; 
                          $sequenceno = $pgm['sequenceno']; 
                          $processid = $pgm['processid']; 
                          $priority = $pgm['priority']; 
                          $isoptional = ($pgm['isoptional']==1?"checked":"");
                          $processqcrequire = ($pgm['qcrequire']==1?"checked":"");
                          $processedby = $pgm['processedby']; 
                        ?>
                  
                        <div class="panel panel-default border-panel processsdetailsequence" id="processsdetailsequence<?=$sequenceno?>" style="transform:none;">
                          <div class="panel-heading collapse-process-panel border-filter-heading">
                              <div class="col-md-8 pl-n">
                                  <h2>Process Details - Sequence No - <span id="spanseqno<?=$sequenceno?>"><?=$sequenceno?></span></h2>
                                  <input type="hidden" class="clsgeneratedsequenceno" name="generatedsequenceno[]" value="<?=$sequenceno?>">
                                  <input type="hidden" name="sortablesequenceno[]" id="sortablesequenceno<?=$sequenceno?>" value="<?=$sequenceno?>">
                                  <input type="hidden" name="postprocessid[]" class="processidselection" id="processidselection<?=$sequenceno?>" value="<?=$processid?>">
                                  <input type="hidden" name="processgroupmappingid[]" id="processgroupmappingid<?=$sequenceno?>" value="<?=$processgroupmappingid?>">
                              </div>
                              <div class="col-md-4 text-right pr-n">
                                  <button type="button" class="btn btn-danger btn-raised mr-md" onclick="removeprocess(<?=$sequenceno?>)"><i class="fa fa-times"></i> Remove</button>
                                  <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;padding-top: 8px;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                              </div>
                          </div>
                          <div class="panel-body p-n pb-md">
                              <div class="row m-n">
                                  <div class="col-sm-5 pr-sm pl-sm">
                                      <div class="form-group" id="process<?=$sequenceno?>_div">
                                          <label for="processid<?=$sequenceno?>" class="col-sm-4 control-label">Select Process </label>
                                          <div class="col-sm-8">
                                              <select id="processid<?=$sequenceno?>" name="processid<?=$sequenceno?>" class="selectpicker form-control" title="Select Process" data-select-on-tab="true" data-size="5" disabled>
                                                  <?php /*if(!empty($processdata)){
                                                      foreach($processdata as $row){ ?>
                                                        <option value="<?=$row["id"]?>" <?=($row["id"]==$processid?"selected":"")?>><?=$row["name"]?></option>';
                                                <?php } 
                                                    }*/
                                                    echo $PROCESS_DATA;
                                                  ?>
                                              </select>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-4 pr-sm pl-sm">
                                      <div class="form-group" id="priority<?=$sequenceno?>_div">
                                          <label class="col-md-4 control-label pr-n" for="priority<?=$sequenceno?>">Priority <span class="mandatoryfield">*</span></label>
                                          <div class="col-md-8">
                                              <input type="text" id="priority<?=$sequenceno?>" class="form-control" name="priority[]" value="<?=$priority?>">
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-3 pr-sm pl-sm">
                                      <div class="form-group">
                                          <label for="sms" class="col-sm-6 control-label">Is Optional</label>
                                          <div class="col-sm-6">
                                              <div class="yesno">
                                              <input type="checkbox" name="processisoptional<?=$sequenceno?>" value="0" <?=$isoptional?>>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="row m-n">
                                  <div class="col-md-5 pr-sm pl-sm">
                                      <div class="form-group">
                                          <label for="focusedinput" class="col-md-4 control-label">Processed By</label>
                                          <div class="col-md-8 mt-xs">
                                              <div class="col-md-7 col-xs-4 pr-n">
                                                  <div class="radio">
                                                      <input class="processedby" type="radio" name="processedby<?=$sequenceno?>" id="inhouse<?=$sequenceno?>" value="1" <?=($processedby==1?'checked':'')?>>
                                                      <label for="inhouse<?=$sequenceno?>">In-House Emp</label>
                                                  </div>
                                              </div>
                                              <div class="col-md-5 col-xs-4 p-n">
                                                  <div class="radio">
                                                      <input class="processedby" type="radio" name="processedby<?=$sequenceno?>" id="otherparty<?=$sequenceno?>" value="0" <?=($processedby==0?'checked':'')?>>
                                                      <label for="otherparty<?=$sequenceno?>">Other Party</label>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-4 pr-sm pl-sm">
                                      <div class="form-group" id="vendor<?=$sequenceno?>_div" style="<?=($processedby==1?'display:none;':'')?>">
                                          <label class="col-md-4 control-label pr-n" for="vendorid<?=$sequenceno?>">Vendor</label>
                                          <div class="col-md-8">
                                            <input type="hidden" id="prevendorid<?=$sequenceno?>" value="<?=($processedby==0 && !empty($pgm['vendorid']))?$pgm['vendorid']:0?>">
                                            <select id="vendorid<?=$sequenceno?>" name="vendorid[<?=$sequenceno?>][]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" data-actions-box="true" title="Select Vendor" multiple>
                                            </select>
                                          </div>
                                      </div>
                                      <div class="form-group" id="machine<?=$sequenceno?>_div" style="<?=($processedby==0?'display:none;':'')?>">
                                          <label class="col-md-4 control-label pr-n" for="machineid<?=$sequenceno?>">Machine</label>
                                          <div class="col-md-8">
                                            <input type="hidden" id="premachineid<?=$sequenceno?>" value="<?=($processedby==1 && !empty($pgm['machineid']))?$pgm['machineid']:0?>">
                                            <select id="machineid<?=$sequenceno?>" name="machineid[<?=$sequenceno?>][]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" data-actions-box="true" title="Select Machine" multiple>
                                            </select>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-3 pr-sm pl-sm">
                                      <div class="form-group">
                                          <label for="sms" class="col-sm-6 control-label">QC Require</label>
                                          <div class="col-sm-6">
                                              <div class="yesno">
                                              <input type="checkbox" name="processqcrequire<?=$sequenceno?>" value="0" <?=$processqcrequire?>>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                                  <div class="col-md-12"><hr></div>
                                  <script>
                                      $(document).ready(function() {
                                        $('#processid<?=$sequenceno?>').val(<?=$processid?>);
                                        $('#processid<?=$sequenceno?>').selectpicker("refresh");
                                      });
                                    </script>
                              
                              <div class="row m-n">
                                  <div class="panel-heading"><h2>OUT Product Material Details</h2></div>
                                  <div class="row m-n">
                                      <div class="col-md-3">
                                          <div class="form-group p-n">
                                              <div class="col-sm-12">
                                                  <label class="control-label">Product</label>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-3">
                                          <div class="form-group p-n">
                                              <div class="col-sm-12">
                                                  <label class="control-label">Variant</label>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-2">
                                          <div class="form-group p-n">
                                              <div class="col-sm-12">
                                                  <label class="control-label">Unit</label>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-1">
                                          <div class="form-group">
                                              <div class="col-sm-6">
                                                  <label class="control-label">Optional</label>
                                              </div>
                                          </div>
                                      </div>
                                      <!-- <div class="col-md-2">
                                          <div class="form-group">
                                              <div class="col-sm-12">
                                                  <label class="control-label">Additional / Supportive</label>
                                              </div>
                                          </div>
                                      </div> -->
                                  </div>
                                  <?php if(count($pgm['outproductdata']) > 0){?>
                                  <?php foreach($pgm['outproductdata'] as $p=>$pgp){ 
                                    $outproductisoptional = ($pgp['isoptional']==1?"checked":""); 
                                    $outproductissupportingproduct = ($pgp['issupportingproduct']==1?"checked":""); 
                                    ?>
                                    <div class="countoutproducts<?=$sequenceno?> col-md-12 p-n" id="countoutproducts<?=$sequenceno?>_<?=($p+1)?>">
                                        <div class="col-md-3">
                                            <div class="form-group p-n" id="outproduct<?=$sequenceno?>_<?=($p+1)?>_div">
                                                <div class="col-sm-12">
                                                  <input type="hidden" name="processgroupoutproductid<?=$sequenceno?>[]" id="processgroupoutproductid<?=$sequenceno?>_<?=($p+1)?>" value="<?=$pgp['id']?>">
                                                    <select id="outproductid<?=$sequenceno?>_<?=($p+1)?>" name="outproductid<?=$sequenceno?>[]" class="selectpicker form-control outproductid" data-live-search="true" data-select-on-tab="true" data-size="8">
                                                        <option value="0">Select Product</option>
                                                        <?php /*if(!empty($productdata)){
                                                          foreach($productdata as $product){ 
                                                            $productname = str_replace("'","&apos;",$product['name']);
                                                            if(DROPDOWN_PRODUCT_LIST==0){ ?>
                            
                                                              <option value="<?php echo $product['id']; ?>" <?=($product['id']==$pgp['productid']?"selected":"")?>><?php echo $productname; ?></option>
                            
                                                            <?php }else{
                            
                                                              if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                                                $img = $product['image'];
                                                              }else{
                                                                $img = PRODUCTDEFAULTIMAGE;
                                                              }
                                                              ?>
                            
                                                              <option data-content="<?php if(!empty($product['image'])){?><img src='<?=PRODUCT.$img?>' style='width:40px'> <?php } echo $productname; ?> "  value="<?php echo $product['id']; ?>" <?=($product['id']==$pgp['productid']?"selected":"")?>><?php echo $productname; ?></option>
                                                              
                                                            <?php } ?>
                                                          <?php } 
                                                        }*/
                                                        echo $PRODUCT_DATA; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group p-n" id="outproductvariant<?=$sequenceno?>_<?=($p+1)?>_div">
                                                <div class="col-sm-12">
                                                    <select id="outproductvariantid<?=$sequenceno?>_<?=($p+1)?>" name="outproductvariantid<?=$sequenceno?>[]" class="selectpicker form-control outproductvariantid<?=$sequenceno?>" data-live-search="true" data-select-on-tab="true" data-size="8">
                                                        <option value="0">Select Variant</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group p-n" id="unit<?=$sequenceno?>_<?=($p+1)?>_div">
                                                <div class="col-sm-12">
                                                    <select id="unitid<?=$sequenceno?>_<?=($p+1)?>" name="unitid<?=$sequenceno?>[]" class="selectpicker form-control unitid" data-live-search="true" data-select-on-tab="true" data-size="8">
                                                        <option value="0">Select Unit</option>
                                                        <?php /*if(!empty($unitdata)){
                                                          foreach($unitdata as $row){  ?>
                                                            <option value="<?php echo $row["id"]; ?>" <?=($row['id']==$pgp['unitid']?"selected":"")?>><?php echo $row["name"]; ?></option>';
                                                          <?php } 
                                                        }*/
                                                        echo $UNIT_DATA; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <div class="col-sm-6">
                                                    <div class="yesno">
                                                    <input type="checkbox" name="outproductisoptional<?=$sequenceno?>_<?=($p+1)?>" value="0" <?=$outproductisoptional?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-1">
                                            <div class="form-group">
                                                <div class="col-sm-6">
                                                    <div class="yesno">
                                                    <input type="checkbox" name="outproductadditional<?=$sequenceno?>_<?=($p+1)?>" value="0" <?=$outproductissupportingproduct?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="col-md-1 text-right pt-md pl-xs">
                                            <?php if($p==0){?>
                                                <?php if(count($pgm['outproductdata'])>1){ ?>
                                                    <button type="button" class="btn btn-default btn-raised remove_outproduct_btn<?=$sequenceno?>" onclick="removeoutproduct(<?=$sequenceno?>,1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                <?php }else { ?>
                                                    <button type="button" class="btn btn-default btn-raised add_outproduct_btn<?=$sequenceno?>" onclick="addnewoutproduct(<?=$sequenceno?>)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                <?php } ?>

                                            <? }else if($p!=0) { ?>
                                                <button type="button" class="btn btn-default btn-raised remove_outproduct_btn<?=$sequenceno?>" onclick="removeoutproduct(<?=$sequenceno?>, <?=$p+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                            <? } ?>
                                            <button type="button" class="btn btn-default btn-raised btn-sm remove_outproduct_btn<?=$sequenceno?>" onclick="removeoutproduct(<?=$sequenceno?>, <?=$p+1?>)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                        
                                            <button type="button" class="btn btn-default btn-raised add_outproduct_btn<?=$sequenceno?>" onclick="addnewoutproduct(<?=$sequenceno?>)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <script>
                                      $(document).ready(function() {
                                        $('#outproductid<?=$sequenceno?>_<?=($p+1)?>').val(<?=$pgp['productid']?>);
                                        $('#outproductid<?=$sequenceno?>_<?=($p+1)?>').selectpicker("refresh");

                                        $('#unitid<?=$sequenceno?>_<?=($p+1)?>').val(<?=$pgp['unitid']?>);
                                        $('#unitid<?=$sequenceno?>_<?=($p+1)?>').selectpicker("refresh");

                                        
                                        getproductvariant(<?=$sequenceno?>,<?=$p+1?>,1,<?=$pgp['productid']?>,<?=$pgp['productpriceid']?>);
                                      });
                                    </script>
                                  <?php } }else{ ?>
                                    <div class="countoutproducts<?=$sequenceno?> col-md-12 p-n" id="countoutproducts<?=$sequenceno?>_1">
                                        <div class="col-md-3">
                                            <div class="form-group p-n" id="outproduct<?=$sequenceno?>_1_div">
                                            <div class="col-sm-12">
                                                <select id="outproductid<?=$sequenceno?>_1" name="outproductid<?=$sequenceno?>[]" class="selectpicker form-control outproductid" data-live-search="true" data-select-on-tab="true" data-size="8">
                                                    <option value="0">Select Product</option>
                                                    <?=$PRODUCT_DATA?>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group p-n" id="outproductvariant<?=$sequenceno?>_1_div">
                                                <div class="col-sm-12">
                                                    <select id="outproductvariantid<?=$sequenceno?>_1" name="outproductvariantid<?=$sequenceno?>[]" class="selectpicker form-control outproductvariantid" data-live-search="true" data-select-on-tab="true" data-size="8">
                                                        <option value="0">Select Variant</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group p-n" id="unit<?=$sequenceno?>_1_div">
                                            <div class="col-sm-12">
                                                <select id="unitid<?=$sequenceno?>_1" name="unitid<?=$sequenceno?>[]" class="selectpicker form-control unitid" data-live-search="true" data-select-on-tab="true" data-size="8">
                                                    <option value="0">Select Unit</option>
                                                    <?=$UNIT_DATA?>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                            <div class="col-sm-6">
                                                <div class="yesno">
                                                <input type="checkbox" name="outproductisoptional1" value="0">
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-right pt-md pl-xs">
                                            <button type="button" class="btn btn-default btn-raised remove_outproduct_btn<?=$sequenceno?>" onclick="removeoutproduct(<?=$sequenceno?>,1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                            <button type="button" class="btn btn-default btn-raised add_outproduct_btn<?=$sequenceno?>" onclick="addnewoutproduct(<?=$sequenceno?>)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                  <?php } ?>
                              </div>
                              <div class="row m-n">
                                  <div class="col-md-12"><hr></div>
                                  <div class="panel-heading"><h2>IN Details</h2><div class="col-md-6 pull-right text-right">Note : Certificate is -1 unlimited, 0 - Not require and Otherwise limited.</div></div>
                                  <?php if(count($pgm['optiondata']) > 0){
                                    for ($l = 0; $l < count($pgm['optiondata']); $l++) { 
                                      
                                      if($pgm['optiondata'][$l]['datatype'] == 0){
                                        $checked = ($pgm['optiondata'][$l]['optionvalue']!="" && $pgm['optiondata'][$l]['optionvalue']==1 ? "checked" : "");
                                        $value = ($pgm['optiondata'][$l]['optionvalue']!="" && $pgm['optiondata'][$l]['optionvalue']==1 ? "1" : "0");
                                        ?>
                                        <div class="col-md-2">
                                          <div class="form-group">
                                            <label for="sms" class="col-sm-6 control-label"><?=ucwords($pgm['optiondata'][$l]['name'])?></label>
                                            <div class="col-sm-3">
                                                <div class="yesno">
                                                <input type="checkbox" class="processoption" id="processoption<?=$sequenceno?>_<?=$l+1?>" value="0" <?=$checked?>>
                                                </div>
                                                <input type="hidden" id="optionvalue<?=$sequenceno?>_<?=$l+1?>" name="optionvalue<?=$sequenceno?>[]" value="<?=$value?>">
                                                <input type="hidden" id="optionid<?=$sequenceno?>_<?=$l+1?>" name="optionid<?=$sequenceno?>[]" value="<?=$pgm['optiondata'][$l]['id']?>">
                                                <input type="hidden" id="processgroupoptionid<?=$sequenceno?>_<?=$l+1?>" name="processgroupoptionid<?=$sequenceno?>[]" value="<?=$pgm['optiondata'][$l]['processgroupoptionid']?>">
                                            </div>
                                          </div>
                                        </div>
                                        <?php 
                                      }else if($pgm['optiondata'][$l]['datatype'] == 3){
                                        $name = ($pgm['optiondata'][$l]['name']=='certificate'?'mincertcountrequired'.$sequenceno:$pgm['optiondata'][$l]['name'].$sequenceno);
                                        ?>
                                        <div class="col-md-5">
                                          <div class="form-group">
                                          <label for="sms" class="col-sm-6 control-label"><?=ucwords($pgm['optiondata'][$l]['name'])?></label>
                                          <div class="col-sm-5">
                                            <input type="text" id="optionvalue<?=$sequenceno?>" class="form-control" name="optionvalue<?=$sequenceno?>[]" value="<?=$pgm['optiondata'][$l]['optionvalue']?>">
                                            <input type="hidden" name="optionid<?=$sequenceno?>[]" value="<?=$pgm['optiondata'][$l]['id']?>">
                                            <input type="hidden" id="processgroupoptionid<?=$sequenceno?>_<?=$l+1?>" name="processgroupoptionid<?=$sequenceno?>[]" value="<?=$pgm['optiondata'][$l]['processgroupoptionid']?>">
                                          </div>
                                          </div>
                                      </div>
                                      <?php 
                                      } ?>

                                  <?php } } ?>
                             
                                  <div class="col-md-12"><hr></div>
                                  <div class="col-md-12">
                                      <div class="col-md-6 p-n" id="inproductlabel1_<?=$sequenceno?>">
                                          <div class="col-md-5 pr-xs pl-xs">
                                              <div class="form-group p-n">
                                                  <div class="col-sm-12">
                                                      <label class="control-label">Product</label>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-md-5 pr-xs pl-xs">
                                              <div class="form-group p-n">
                                                  <div class="col-sm-12">
                                                      <label class="control-label">Product Variant</label>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-6 p-n" id="inproductlabel2_<?=$sequenceno?>" style="display:none;">
                                          <div class="col-md-5 pr-xs pl-xs">
                                              <div class="form-group p-n">
                                                  <div class="col-sm-12">
                                                      <label class="control-label">Product</label>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-md-5 pr-xs pl-xs">
                                              <div class="form-group p-n">
                                                  <div class="col-sm-12">
                                                      <label class="control-label">Product Variant</label>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-12">
                                    <?php if(count($pgm['inproductdata']) > 0){?>
                                    <?php foreach($pgm['inproductdata'] as $p=>$pgp){ ?>
                                        <div class="countinproducts<?=$sequenceno?> col-md-6 p-n" id="countinproducts<?=$sequenceno?>_<?=$p+1?>">
                                            <div class="col-md-5 pr-xs pl-xs">
                                                <div class="form-group p-n" id="inproduct<?=$sequenceno?>_<?=$p+1?>_div">
                                                    <div class="col-sm-12">
                                                      <input type="hidden" name="processgroupinproductid<?=$sequenceno?>[]" id="processgroupinproductid<?=$sequenceno?>_<?=($p+1)?>" value="<?=$pgp['id']?>">
                                                        <select id="inproductid<?=$sequenceno?>_<?=$p+1?>" name="inproductid<?=$sequenceno?>[]" class="selectpicker form-control inproductid" data-live-search="true" data-select-on-tab="true" data-size="8">
                                                            <option value="0">Select Product</option>
                                                            <?php /*if(!empty($productdata)){
                                                              foreach($productdata as $product){ 
                                                                $productname = str_replace("'","&apos;",$product['name']);
                                                                if(DROPDOWN_PRODUCT_LIST==0){ ?>
                                
                                                                  <option value="<?php echo $product['id']; ?>" <?=($product['id']==$pgp['productid']?"selected":"")?>><?php echo $productname; ?></option>
                                
                                                                <?php }else{
                                
                                                                  if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                                                    $img = $product['image'];
                                                                  }else{
                                                                    $img = PRODUCTDEFAULTIMAGE;
                                                                  }
                                                                  ?>
                                
                                                                  <option data-content="<?php if(!empty($product['image'])){?><img src='<?=PRODUCT.$img?>' style='width:40px'> <?php } echo $productname; ?> "  value="<?php echo $product['id']; ?>" <?=($product['id']==$pgp['productid']?"selected":"")?>><?php echo $productname; ?></option>
                                                                  
                                                                <?php } ?>
                                                              <?php } 
                                                            }*/
                                                            echo $PRODUCT_DATA; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 pr-xs pl-xs">
                                                <div class="form-group p-n" id="inproductvariant<?=$sequenceno?>_<?=$p+1?>_div">
                                                    <div class="col-sm-12">
                                                        <select id="inproductvariantid<?=$sequenceno?>_<?=$p+1?>" name="inproductvariantid<?=$sequenceno?>[]" class="selectpicker form-control inproductvariantid<?=$sequenceno?>" data-live-search="true" data-select-on-tab="true" data-size="8">
                                                            <option value="0">Select Variant</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-right pt-md pl-xs">
                                                <?php if($p==0){?>
                                                    <?php if(count($pgm['inproductdata'])>1){ ?>
                                                        <button type="button" class="btn btn-default btn-raised remove_inproduct_btn<?=$sequenceno?>" onclick="removeinproduct(<?=$sequenceno?>,1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                    <?php }else { ?>
                                                        <button type="button" class="btn btn-default btn-raised add_inproduct_btn<?=$sequenceno?>" onclick="addnewinproduct(<?=$sequenceno?>)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                    <?php } ?>

                                                <? }else if($p!=0) { ?>
                                                    <button type="button" class="btn btn-default btn-raised remove_inproduct_btn<?=$sequenceno?>" onclick="removeinproduct(<?=$sequenceno?>, <?=$p+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                <? } ?>
                                                <button type="button" class="btn btn-default btn-raised btn-sm remove_inproduct_btn<?=$sequenceno?>" onclick="removeinproduct(<?=$sequenceno?>, <?=$p+1?>)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                            
                                                <button type="button" class="btn btn-default btn-raised add_inproduct_btn<?=$sequenceno?>" onclick="addnewinproduct(<?=$sequenceno?>)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <script>
                                        $(document).ready(function() {
                                            
                                            $('#inproductid<?=$sequenceno?>_<?=($p+1)?>').val(<?=$pgp['productid']?>);
                                            $('#inproductid<?=$sequenceno?>_<?=($p+1)?>').selectpicker("refresh");
                                          getproductvariant(<?=$sequenceno?>,<?=$p+1?>,0,<?=$pgp['productid']?>,<?=$pgp['productpriceid']?>);
                                        });
                                      </script>
                                    <?php } }else{ ?>
                                      <div class="countinproducts<?=$sequenceno?> col-md-6 p-n" id="countinproducts<?=$sequenceno?>_1">
                                        <div class="col-md-5 pr-xs pl-xs">
                                            <div class="form-group p-n" id="inproduct<?=$sequenceno?>_1_div">
                                                <div class="col-sm-12">
                                                    <select id="inproductid<?=$sequenceno?>_1" name="inproductid<?=$sequenceno?>[]" class="selectpicker form-control inproductid" data-live-search="true" data-select-on-tab="true" data-size="8">
                                                        <option value="0">Select Product</option>
                                                        <?=$PRODUCT_DATA?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 pr-xs pl-xs">
                                            <div class="form-group p-n" id="inproductvariant<?=$sequenceno?>_1_div">
                                            <div class="col-sm-12">
                                                <select id="inproductvariantid<?=$sequenceno?>_1" name="inproductvariantid<?=$sequenceno?>[]" class="selectpicker form-control inproductvariantid" data-live-search="true" data-select-on-tab="true" data-size="8">
                                                    <option value="0">Select Variant</option>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-right pt-md pl-xs">
                                            <button type="button" class="btn btn-default btn-raised remove_inproduct_btn<?=$sequenceno?>" onclick="removeinproduct(<?=$sequenceno?>,1)" style="padding: 3px 8px;display: none;"><i class="fa fa-minus"></i></button>
                                            <button type="button" class="btn btn-default btn-raised add_inproduct_btn<?=$sequenceno?>" onclick="addnewinproduct(<?=$sequenceno?>)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <?php } ?>
                                  </div>
                              </div>
                          </div>
                        </div>
                  <?php } ?>
                <?php } ?>
                </div>
                <div id="generatedsequence" style="display:none;"></div>
                <div id="firstprocessids" style="display:none;"><?php if(isset($processgroupdata)){ echo json_encode($processIdArray); } ?></div>
               
                <!-- Action -->
                <div class="panel panel-default border-panel">
                  <div class="panel-heading"><h2>Action</h2></div>
                  <div class="panel-body">
                    <div class="row">
                        <div class="form-group">
                            <label for="focusedinput" class="col-md-5 control-label">Activate</label>
                            <div class="col-md-4">
                              <div class="col-md-3 col-xs-4" style="padding-left: 0px;">
                                  <div class="radio">
                                  <input type="radio" name="status" id="yes" value="1" <?php if(isset($processgroupdata) && $processgroupdata['master']['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                  <label for="yes">Yes</label>
                                  </div>
                              </div>
                              <div class="col-md-3 col-xs-4">
                                  <div class="radio">
                                  <input type="radio" name="status" id="no" value="0" <?php if(isset($processgroupdata) && $processgroupdata['master']['status']==0){ echo 'checked'; }?>>
                                  <label for="no">No</label>
                                  </div>
                              </div>
                            </div>
                        </div>
                        <div class="form-group">
                          <div class="col-sm-12 text-center">
                              <?php if(!empty($processgroupdata) && !isset($isduplicate)){ ?>
                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                              <?php }else{ ?>
                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                  <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & ADD NEW" class="btn btn-primary btn-raised">
                                  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                              <?php } ?>
                              <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->