<?php 
$DESIGNATION_DATA = "";
foreach($this->Defaultdesignation as $key=>$val){
    $DESIGNATION_DATA .= '<option value="'.$key.'">'.$val.'</option>';
} ?>
<script>
    var DESIGNATION_DATA = '<?=$DESIGNATION_DATA?>';
</script>
<style>
  .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
  .toggle.ios .toggle-handle { border-radius: 20px; }
  .toggle.android { border-radius: 0px;}
  .toggle.android .toggle-handle { border-radius: 0px; }

    .approvallevelbox {
        box-shadow: 0px 1px 6px #333 !important;
        margin-bottom: 20px;
    }
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($approvallevelsdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($approvallevelsdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
                  <form class="form-horizontal" id="approvallevelsform">
                      <input type="hidden" name="approvallevelsid" value="<?php if(isset($approvallevelsdata)){ echo $approvallevelsdata['id']; } ?>">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" id="module_div">
                                    <label for="module" class="col-sm-3 control-label">Select Module <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-9">
                                        <select id="module" name="module" class="selectpicker form-control" data-live-search="true" data-size="8">
                                            <option value="0">Select Module</option>
                                            <?php if(!empty($modulelist)){ 
                                                foreach($modulelist as $module){ ?>
                                                <option value="<?php echo $module['ids']; ?>" <?php if(isset($approvallevelsdata) && $module['ids']==$approvallevelsdata['moduleids']){ echo "selected"; }?>><?php echo $module['name']; ?></option>
                                            <?php } 
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group" id="netprice_div">
                                    <label for="netprice" class="col-sm-3 control-label">Net Price</label>
                                    <div class="col-sm-8">
                                        <input id="netprice" name="netprice" class="form-control" value="<?php if(isset($approvallevelsdata)){ echo number_format($approvallevelsdata['netprice'],2,'.',''); }?>" onkeypress="return decimal_number_validation(event, this.value,8)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group" id="channel_div" style="display:none;">
                                    <label for="channelid" class="col-sm-4 control-label">Select Channel</label>
                                    <div class="col-sm-4">
                                        <select id="channelid" name="channelid" class="selectpicker form-control" data-live-search="true" data-size="8">
                                            <option value="0">Select Channel</option>
                                            <?php if(!empty($channeldata)){ 
                                                foreach($channeldata as $channel){ ?>
                                                <option value="<?php echo $channel['id']; ?>" <?php if(isset($approvallevelsdata) && $channel['id']==$approvallevelsdata['channelid']){ echo "selected"; }?>><?php echo $channel['name']; ?></option>
                                            <?php } 
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="member_div" style="display:none;">
                                    <label for="memberid" class="col-sm-4 control-label">Select <?=Member_label?></label>
                                    <div class="col-sm-4">
                                        <select id="memberid" name="memberid" class="selectpicker form-control" data-live-search="true" data-size="8">
                                            <option value="0">Select <?=Member_label?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12"><hr></div>
                            <div class="col-md-12 sortablepanel">
                                <?php if(isset($approvallevelsdata) && count($approvallevelsmapping) > 0){ ?>
                                    <input type="hidden" id="removeapprovallevelsmappingid" name="removeapprovallevelsmappingid" value="">
                                    <?php foreach($approvallevelsmapping as $k=>$alm){
                                            $approvallevelsmappingid = $alm['id']; 
                                            $level = $alm['level']; 
                                            $designation = $alm['designation']; 
                                            $isenable = ($alm['isenable']==1?"checked":""); 
                                            $sendemail = ($alm['sendemail']==1?"checked":""); 
                                        ?>
                                        <div class="panel panel-default countlevel approvallevelbox" id="countlevel<?=$level?>" style="transform:none;">
                                            <div class="panel-heading border-filter-heading">
                                                <h2 style="font-weight:600;">Approval <span id="spanlevel<?=$level?>"><?=$level?></span></h2>
                                                <input type="hidden" name="generatedlevel[]" id="generatedlevel<?=$level?>" value="<?=$level?>">
                                                <input type="hidden" name="sortablelevel[]" id="sortablelevel<?=$level?>" value="<?=$level?>">
                                                <input type="hidden" name="approvallevelsmappingid<?=$level?>" id="approvallevelsmappingid<?=$level?>" value="<?=$approvallevelsmappingid?>">
                                                <div class="pull-right">
                                                    <?php if($k==0){?>
                                                        <?php if(count($approvallevelsmapping)>1){ ?>
                                                            <button type="button" class="btn btn-default btn-raised remove_level_btn" onclick="removeLevel(<?=$level?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                        <?php }else { ?>
                                                            <button type="button" class="btn btn-default btn-raised add_level_btn" onclick="addNewLevel()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                        <?php } ?>
                                                    <?php }else if($k!=0) { ?>
                                                        <button type="button" class="btn btn-default btn-raised remove_level_btn" onclick="removeLevel(<?=$level?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                    <?php } ?>
                                                    <button type="button" class="btn btn-default btn-raised btn-sm remove_level_btn" onclick="removeLevel(<?=$level?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                    <button type="button" class="btn btn-default btn-raised add_level_btn" onclick="addNewLevel()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>  
                                                </div>
                                            </div> 
                                            <div class="panel-body no-padding">
                                                <div class="col-sm-6">
                                                    <div class="form-group" id="designation<?=$level?>_div">
                                                        <label for="designationid<?=$level?>" class="col-sm-4 control-label">Select Designation <span class="mandatoryfield">*</span></label>
                                                        <div class="col-sm-8">
                                                            <select id="designationid<?=$level?>" name="designationid<?=$level?>" class="selectpicker form-control designationid" data-live-search="true" data-size="8">
                                                                <option value="0">Select Designation</option>
                                                                <?php foreach($this->Defaultdesignation as $key=>$val){ ?>
                                                                    <option value="<?php echo $key; ?>" <?=($designation==$key?"selected":"")?>><?php echo $val; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="isenable<?=$level?>" class="col-sm-5 control-label">Enable</label>
                                                        <div class="col-sm-7" style="margin-top: 5px;">
                                                            <div class="yesno">
                                                                <input type="checkbox" name="isenable<?=$level?>" value="1" <?php echo $isenable; ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="sendemail<?=$level?>" class="col-sm-5 control-label">Email</label>
                                                        <div class="col-sm-7" style="margin-top: 5px;">
                                                            <div class="yesno">
                                                                <input type="checkbox" name="sendemail<?=$level?>" value="1" <?php echo $sendemail; ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                }else { ?>
                                    <div class="panel panel-default countlevel approvallevelbox" id="countlevel1" style="transform:none;">
                                        <div class="panel-heading border-filter-heading">
                                            <h2 style="font-weight:600;">Approval <span id="spanlevel1">1</span></h2>
                                            <input type="hidden" name="generatedlevel[]" id="generatedlevel1" value="1">
                                            <input type="hidden" name="sortablelevel[]" id="sortablelevel1" value="0">
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-default btn-raised remove_level_btn" onclick="removeLevel(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                <button type="button" class="btn btn-default btn-raised add_level_btn" onclick="addNewLevel()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div> 
                                        <div class="panel-body no-padding">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="designation1_div">
                                                    <label for="designationid1" class="col-sm-4 control-label">Select Designation <span class="mandatoryfield">*</span></label>
                                                    <div class="col-sm-8">
                                                        <select id="designationid1" name="designationid1" class="selectpicker form-control designationid" data-live-search="true" data-size="8">
                                                            <option value="0">Select Designation</option>
                                                            <?php foreach($this->Defaultdesignation as $key=>$val){ ?>
                                                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="isenable1" class="col-sm-5 control-label">Enable</label>
                                                    <div class="col-sm-7" style="margin-top: 5px;">
                                                        <div class="yesno">
                                                            <input type="checkbox" name="isenable1" value="1">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="sendemail1" class="col-sm-5 control-label">Email</label>
                                                    <div class="col-sm-7" style="margin-top: 5px;">
                                                        <div class="yesno">
                                                            <input type="checkbox" name="sendemail1" value="1">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="focusedinput" class="col-md-5 control-label">Activate</label>
                                    <div class="col-md-4">
                                        <div class="col-md-3 col-xs-4" style="padding-left: 0px;">
                                            <div class="radio">
                                            <input type="radio" name="status" id="yes" value="1" <?php if(isset($approvallevelsdata) && $approvallevelsdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                            <label for="yes">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-4">
                                            <div class="radio">
                                            <input type="radio" name="status" id="no" value="0" <?php if(isset($approvallevelsdata) && $approvallevelsdata['status']==0){ echo 'checked'; }?>>
                                            <label for="no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                  <div class="col-sm-12 text-center">
                                      <?php if(!empty($approvallevelsdata)){ ?>
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
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->