<div class="page-content">
    <div class="page-heading">
        <h1><?php if(isset($insuranceagent)){ echo 'Edit'; }else{ echo 'Add'; } ?>
                <?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
                <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a>
                </li>
                <li class="active"><?php if(isset($insuranceagent)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>
      
    <div class="container-fluid">
        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-body">
                            <form class="form-horizontal" id="form-insuranceagent">
                                <input id="id" type="hidden" name="id" class="form-control" value="<?php if (isset($insuranceagent)) { echo $insuranceagent['id']; } ?>">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group" id="agent_div">
                                            <label class="col-md-4 control-label text-right" for="agent">Agent Name <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" id="agent" class="form-control" name="agent" value="<?php if(isset($insuranceagent)){ echo $insuranceagent['agentname'];}?>">
                                            </div>
                                        </div>
                                        <?php
                                            if(isset($insuranceagent['insuranceid'])){
                                                $CompanyArray=explode(",",$insuranceagent['insuranceid']);
                                            }
                                        ?>
                                        <div class="form-group" id="insurancecompany_div">
                                            <label class="col-md-4 control-label text-right" for="insurancecompany">Insurance Company <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-4">
                                                <select id="insurancecompany" name="insurancecompany[]" title="Select Insurance Company" multiple data-actions-box="true" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="8">
                                                    <?php foreach ($insurancecompanydata as $icd) { ?>
                                                        <option value="<?=$icd['id']?>" <?php if (isset($insuranceagent)) { if(in_array($icd['id'],$CompanyArray))echo "selected"; } ?>><?=$icd['companyname']?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="email_div">
                                            <label class="col-md-4 control-label text-right" for="email">Email <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" id="email" class="form-control" name="email" value="<?php if(isset($insuranceagent)){ echo $insuranceagent['email'];}?>">
                                            </div>
                                        </div>
                                        <div class="form-group" id="mobileno_div">
                                            <label class="col-md-4 control-label text-right" for="mobileno">Mobile No. <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" id="mobileno" class="form-control" name="mobileno" value="<?php if(isset($insuranceagent)){ echo $insuranceagent['mobileno'];}?>" onkeypress="return isNumber(event)" maxlength="10">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="focusedinput" class="col-md-4 col-sm-4 col-xs-4 control-label">Activate</label>
                                            <div class="col-md-8">
                                                <div class="col-md-1 col-sm-1 col-xs-2" style="padding-left: 0px;">
                                                    <div class="radio">
                                                        <input type="radio" name="status" id="yes" value="1" <?php if(isset($insuranceagent) && $insuranceagent['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                        <label for="yes">Yes</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-2 col-xs-2">
                                                    <div class="radio">
                                                        <input type="radio" name="status" id="no" value="0" <?php if(isset($insuranceagent) && $insuranceagent['status']==0){ echo 'checked'; }?>>
                                                        <label for="no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4"></label>
                                    <div class="col-sm-8">
                                        <?php if(!empty($insuranceagent)){ ?>
                                            <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                            <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                                            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                        <?php }else{ ?>
                                            <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                            <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
                                            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                        <?php } ?>
                                        <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->