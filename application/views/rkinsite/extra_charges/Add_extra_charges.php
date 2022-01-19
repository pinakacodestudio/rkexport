<script>
    var CHANNELID = '<?php if(isset($extrachargesdata)){ echo $extrachargesdata['channelid']; } ?>';
    var MEMBERID = '<?php if(isset($extrachargesdata)){ echo $extrachargesdata['memberid']; } ?>';
    var HSNCODEID = '<?php if(isset($extrachargesdata)){ echo $extrachargesdata['hsncodeid']; } ?>';
</script>
<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($extrachargesdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($extrachargesdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-12 col-lg-12 p-n">
					<form class="form-horizontal" id="extrachargesform">
						<input type="hidden" id="extrachargesid" name="extrachargesid" value="<?php if(isset($extrachargesdata)){ echo $extrachargesdata['id']; } ?>">
                        <div class="col-md-6">
                            <div class="form-group" id="name_div">
                                <label class="col-sm-4 control-label" for="name">Name <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-7">
                                    <input id="name" type="text" name="name" value="<?php if(!empty($extrachargesdata)){ echo $extrachargesdata['name']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
                                </div>
                            </div>
                            <div class="form-group" id="hsncode_div">
                                <label class="col-sm-4 control-label" for="hsncodeid">Select HSN Code</label>
                                <div class="col-sm-7">
                                    <select id="hsncodeid" name="hsncodeid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                    <option value='0'>Select HSN Code</option>
                                        <?php foreach ($hsncodedata as $hsncode) { ?>
                                            <option value='<?php echo $hsncode['id']; ?>' <?php if(isset($extrachargesdata) && $extrachargesdata['hsncodeid']==$hsncode['id']){ echo "selected"; } ?>><?=$hsncode['hsncode']?></option>
                                            <?php } ?>
                                        </select>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="focusedinput" class="col-sm-4 control-label">Amount Type</label>
                                <div class="col-sm-7">
                                    <div class="col-sm-4 col-xs-6" style="padding-left: 0px;">
                                        <div class="radio">
                                        <input type="radio" name="amounttype" id="amount" value="1" <?php if(isset($extrachargesdata) && $extrachargesdata['amounttype']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                        <label for="amount">Amount</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-xs-6">
                                        <div class="radio">
                                        <input type="radio" name="amounttype" id="percentage" value="0" <?php if(isset($extrachargesdata) && $extrachargesdata['amounttype']==0){ echo 'checked'; }?>>
                                        <label for="percentage">Percentage</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="defaultamount_div">
                                <label class="col-sm-4 control-label" for="defaultamount">Default Amount <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-7">
                                    <input id="defaultamount" type="text" name="defaultamount" value="<?php if(!empty($extrachargesdata)){ echo number_format($extrachargesdata['defaultamount'],2,'.',''); } ?>" class="form-control" onkeypress="return decimal_number_validation(event,this.value,10,2);">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="focusedinput" class="col-sm-4 control-label">Type</label>
                                <div class="col-sm-8">
                                    <div class="col-sm-4 col-xs-6" style="padding-left: 0px;">
                                        <div class="radio">
                                        <input type="radio" name="chargetype" id="overall" value="0" <?php if(isset($extrachargesdata) && $extrachargesdata['chargetype']==0){ echo 'checked'; }else{ echo 'checked'; }?>>
                                        <label for="overall">Overall</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-6">
                                        <div class="radio">
                                        <input type="radio" name="chargetype" id="pcswise" value="1" <?php if(isset($extrachargesdata) && $extrachargesdata['chargetype']==1){ echo 'checked'; }?>>
                                        <label for="pcswise">Pcs Wise</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                                    <hr>
                            <div class="form-group">
                                <label for="focusedinput" class="col-sm-5 control-label">Activate</label>
                                <div class="col-sm-6">
                                    <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                        <div class="radio">
                                        <input type="radio" name="status" id="yes" value="1" <?php if(isset($extrachargesdata) && $extrachargesdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                        <label for="yes">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 col-xs-6">
                                        <div class="radio">
                                        <input type="radio" name="status" id="no" value="0" <?php if(isset($extrachargesdata) && $extrachargesdata['status']==0){ echo 'checked'; }?>>
                                        <label for="no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <div class="col-sm-12">
                                    <?php if(!empty($extrachargesdata)){ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
                                    <?php }else{ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
                                    <?php } ?>
                                    <a class="<?=cancellink_class;?>" href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
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