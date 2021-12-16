<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($leadsourcedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($leadsourcedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
                        <form class="form-horizontal" id="leadsourceform" name="leadsourceform" method="post">
                            <input type="hidden" name="id" value="<?php if(isset($leadsourcedata)){ echo $leadsourcedata['id']; } ?>">
                            <div class="form-group row" id="name_div">
                                <label class="col-md-3 control-label" for="name">Name <span class="mandatoryfield">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" id="name" value="<?php if(!empty($leadsourcedata)){ echo $leadsourcedata['name']; } ?>" name="name" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row" id="color_div">
                                <label class="col-md-3 control-label" for="color">Color <span class="mandatoryfield">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" id="color" class="form-control demo" name="color" value="<?php if(!empty($leadsourcedata)){ echo $leadsourcedata['color']; }else{ echo '#70c24a'; } ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="focusedinput" class="col-md-3 control-label">Activate</label>
                                <div class="col-md-2 col-xs-4">
                                    <div class="radio">
                                        <input type="radio" name="status" id="yes" value="1" <?php if(isset($leadsourcedata) && $leadsourcedata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?> >
                                        <label for="yes"><?php echo 'Yes'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-4">
                                    <div class="radio">
                                        <input type="radio" name="status" id="no" value="0" <?php if(isset($leadsourcedata) && $leadsourcedata['status']==0){ echo 'checked'; }?>>
                                        <label for="no" ><?php echo 'No'; ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="focusedinput" class="col-sm-3 control-label"></label>
                                <div class="col-sm-8">
                                    <?php if(!empty($leadsourcedata)){ ?>
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