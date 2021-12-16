<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($zonedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($zonedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-6 col-lg-6 col-lg-offset-3 col-md-offset-3">
                        <form class="form-horizontal" id="zoneform" name="zoneform" method="post">

                            <input type="hidden" name="id" value="<?php if(isset($zonedata)){ echo $zonedata['id']; } ?>">

                            <div class="form-group row" id="zone_div">
                                <label class="col-md-3 control-label" for="zone">Zone <span class="mandatoryfield">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" id="zone" value="<?php if(!empty($zonedata)){ echo $zonedata['zonename']; } ?>" name="zone" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="focusedinput" class="col-md-3 control-label">Activate</label>
                                <div class="col-md-8 ml-3">
                                    <div class="col-md-3 col-xs-4" style="padding-left: 0px;">
                                        <div class="radio">
                                            <input type="radio" name="status" id="yes" value="1" <?php if(isset($zonedata) && $zonedata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?> >
                                            <label for="yes">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-4">
                                        <div class="radio">
                                            <input type="radio" name="status" id="no" value="0" <?php if(isset($zonedata) && $zonedata['status']==0){ echo 'checked'; }?>>
                                            <label for="no" >No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="form-group row">
                                <label for="focusedinput" class="col-sm-3 control-label"></label>
                                <div class="col-sm-8">
                                <?php if(!empty($zonedata)){ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised text-white" onclick="resetdata()">
                                <?php }else{ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised text-white" onclick="resetdata()">
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
		</div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->