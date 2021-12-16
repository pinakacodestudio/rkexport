<style type="text/css">
   .datepicker1{
    text-align: left !important;
    border-radius: 3px !important;
  }
</style>
<script>
    var referenceid = '<?php if(!empty($target_data)){ echo $target_data['referenceid']; }else{ echo 0; } ?>';
</script>
<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($target_data)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($target_data)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
					<form class="form-horizontal" id="targetform" name="targetform">
                        <input type="hidden"  name="targetid" value="<?php if(isset($target_data)){ echo $target_data['targetid']; }?>" >
                           
                        <div class="col-md-12 p-n">
                            <div class="col-md-6">
                                <div class="form-group" id="targettype_div">
                                    <label for="targettype" class="col-sm-4 control-label">Target Type <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-8">
                                        <select id="type" name="type" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8" onchange="gettypedata()">
                                            <option value="0">Select Target Type</option>
                                            <?php foreach($this->Targettype as $k=>$v ){ ?>
                                                <option value="<?php echo $k; ?>" <?php if(isset($target_data)){ if($k == $target_data['reference']){ echo 'selected'; } } ?>><?php echo $v; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row" id="typeid_div">
                                    <label class="col-sm-4 control-label" for="typeid" id="targettype_heading"></label>
                                    <div class="col-md-8">
                                        <select id="typeid" name="typeid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="orders_div">
                                    <label class="col-sm-4 control-label" for="orders">Orders <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-8">								
                                    <input id="orders" type="text" name="orders" class="form-control" onkeypress="return decimal_number_validation(event,this.value,10,2)" value="<?php if(!empty($target_data)){ echo $target_data['orders']; } ?>">
                                    </div>
                                </div>
                                <div class="form-group" id="meetings_div">
                                    <label class="col-sm-4 control-label" for="meetings">Meetings <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-8">
                                        <input id="meetings" type="text" name="meetings" class="form-control" onkeypress="return decimal_number_validation(event,this.value,10,2)" value="<?php if(!empty($target_data)){ echo $target_data['meetings']; } ?>">
                                    </div>                
                                </div>
                            </div>	
                            <div class="col-md-6">
                                <div class="form-group" id="revenue_div">
                                    <label class="col-sm-4 control-label" for="revenue">Revenue <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-8">								
                                    <input id="revenue" type="text" name="revenue" class="form-control" onkeypress="return decimal_number_validation(event,this.value,10,2)" value="<?php if(!empty($target_data)){ echo $target_data['revenue']; } ?>">
                                    </div>
                                </div>
                                <div class="form-group" id="leads_div">
                                    <label class="col-sm-4 control-label" for="leads">Leads <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-8">
                                        <input id="leads" type="text" name="leads" class="form-control" onkeypress="return decimal_number_validation(event,this.value,10,2)" value="<?php if(!empty($target_data)){ echo $target_data['leads']; } ?>">
                                    </div>
                                </div>
                                <div class="form-group" id="duration_div">
                                    <label class="col-sm-4 control-label" for="duration">Duration <span class="mandatoryfield">*</span> </label>
                                    <div class="col-md-8">
                                        <select id="duration" name="duration" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8">
                                            <option value="0">Select Duration</option>
                                            <?php foreach($this->Targetduration as $k=>$v){ ?>
                                                <option value="<?php echo $k; ?>" <?php if(isset($target_data)){ if($k == $target_data['duration']){ echo 'selected'; } } ?>><?php echo $v; ?></option>
                                            <?php } ?>
                                        </select>
                                        <div class="checkbox">
                                            <input id="datecheckbox" onchange="singlecheck(this.id)" type="checkbox" name="datecheckbox" class="checkradios" >
                                            <label for="datecheckbox">Do you want to add date ?</label>
                                        </div>
                                    </div>								
                                </div>
                                <div class="form-group" id="startdate_div">
                                    <label class="col-sm-4 control-label" for="startdate">Date</label>
                                    <div class="col-sm-8">
                                        <div class="input-daterange input-group" id="datepicker-range">
                                            <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php if(!empty($target_data)){  if($target_data['startdate']!="0000-00-00"){ echo $this->general_model->displaydate($target_data['startdate']); }} ?>" placeholder="Start Date" title="Start Date" readonly/>
                                            <span class="input-group-addon">to</span>
                                            <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php if(!empty($target_data)){  if($target_data['enddate']!="0000-00-00"){ echo $this->general_model->displaydate($target_data['enddate']); }} ?>" placeholder="End Date" title="End Date" readonly/>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="input-daterange" id="datepicker-range">
                                    <div class="form-group row" id="startdate_div">
                                        <label class="col-sm-4 control-label" for="startdate">Date </label>
                                        <div class="col-sm-4">
                                            <input id="startdate" type="text" name="startdate" value="<?php if(!empty($target_data)){  if($target_data['startdate']!="0000-00-00"){ echo $this->general_model->displaydate($target_data['startdate']); }} ?>"  class="form-control datepicker1" placeholder="Start" readonly>
                                        </div>
                                        <div class="col-sm-4">
                                            <input id="enddate" type="text" name="enddate" value="<?php if(!empty($target_data)){  if($target_data['enddate']!="0000-00-00"){ echo $this->general_model->displaydate($target_data['enddate']); }} ?>"  class="form-control datepicker1" placeholder="End" readonly>
                                        </div>
                                    </div>
                                </div> -->
                            </div>  
                        </div>
                        <div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-3 col-md-offset-3">
                            <div class="form-group">
                                <label for="focusedinput" class="col-md-3 control-label">Activate</label>
                                <div class="col-md-5">
                                    <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                        <div class="radio">
                                            <input type="radio" name="status" id="yes" value="1" <?php if(isset($target_data) && $target_data['status']==1){ echo 'checked'; }else{ echo 'checked'; }?> >
                                            <label for="yes">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-4">
                                        <div class="radio">
                                            <input type="radio" name="status" id="no" value="0" <?php if(isset($target_data) && $target_data['status']==0){ echo 'checked'; } ?> >
                                            <label for="no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 text-center">
                            <div class="form-group">
                                <?php if(isset($target_data)){ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                <?php }else{ ?>
                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->uri->segment(2)?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
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