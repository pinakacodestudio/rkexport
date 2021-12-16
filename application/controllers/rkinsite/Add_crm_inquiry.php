<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($sitemapdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($sitemapdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-12 col-lg-12 ">
                        <form class="form-horizontal"  id="sitemap"  method="post">
                        <input type="hidden" name="sitemapid" value="<?php if(isset($sitemapdata)){ echo $sitemapdata['id']; } ?>">

                              <div class="form-group row" id="slug_div" >
                                    <label class="col-md-4 control-label" for="slug"><?=DOMAIN_URL?>   <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-4">
                                          <input type="text" name= "slug"  id="slug"  onkeyup="setsitemapslug(this.value);" value="<?php if(!empty($sitemapdata)){ echo $sitemapdata['slug']; } ?>" class="form-control">
                                    </div>
                              </div>
                              

                              <div class="form-group row">
                                          <label class="col-md-4 control-label" for="lastchange">Last Change </label>
                                          <div class="col-md-4 "> 
                                                <input id="lastchange" name="lastchange"  type="text" class="form-control col-sm-6" value="<?php if(isset($sitemapdata) && $sitemapdata['lastchange']!="0000-00-00"){ echo $this->general_model->displaydate($sitemapdata['lastchange']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" readonly>   
                                          </div>                
                              </div>
                              
                              <div class="form-group row">
                                    <label class="col-md-4 control-label" for="priority" >Priority </label>
                                    <div class="col-md-4">
                                    <?php
                                                $selectedpriority = 1;
                                                if(!empty($sitemapdata['sitemapdata'])){
                                                    $selectedpriority = $sitemapdata['sitemapdata']['priority'];
                                                } 
                                                ?>
                                          <select  class="selectpicker form-control " data-size="8" id="priority" name="priority"  >
                                                <option value="0" <?php if(isset($sitemapdata['priority']) && $sitemapdata['priority']==0 ){ echo "selected"; } ?> >0.0</option>
                                                <option value="1" <?php if(isset($sitemapdata['priority']) && $sitemapdata['priority']==1 ){ echo "selected"; } ?>>0.1</option>
                                                <option  value="2" <?php if(isset($sitemapdata['priority']) && $sitemapdata['priority']==2 ){ echo "selected"; } ?> >0.2</option>
                                                <option   value="3" <?php if(isset($sitemapdata['priority']) && $sitemapdata['priority']==3 ){ echo "selected"; } ?> >0.3</option>
                                                <option  value="4" <?php if(isset($sitemapdata['priority']) && $sitemapdata['priority']==4 ){ echo "selected"; } ?> >0.4</option>
                                                <option   value="5" <?php if(isset($sitemapdata['priority']) && $sitemapdata['priority']==5 ){ echo "selected"; } ?> >0.5</option>
                                                <option  value="6" <?php if(isset($sitemapdata['priority']) && $sitemapdata['priority']==6 ){ echo "selected"; } ?> >0.6</option>
                                                <option  value="7" <?php if(isset($sitemapdata['priority']) && $sitemapdata['priority']==7 ){ echo "selected"; } ?> >0.7</option>
                                                <option  value="8" <?php if(isset($sitemapdata['priority']) && $sitemapdata['priority']==8 ){ echo "selected"; } ?>  >0.8</option>
                                                <option   value="9" <?php if(isset($sitemapdata['priority']) && $sitemapdata['priority']==9 ){ echo "selected"; } ?> >0.9</option>
                                                <option   value="10" <?php if(isset($sitemapdata['priority']) && $sitemapdata['priority']==10 ){ echo "selected"; } ?> >1.0</option>
                                          </select>
                                                
                                    </div>
                            </div>
                            <div class="form-group row" >
                                    <label class="col-md-4 control-label" for="changefrequency" >Change Frequency</label>
                                    <div class="col-md-4">
                                    
                                        <select  class="selectpicker form-control"  id="changefrequency" name="changefrequency" data-size="8"  >
                                                    <option value="0" <?php if(isset($sitemapdata['changefrequency']) && $sitemapdata['changefrequency']==0 ){ echo "selected"; } ?> >Always</option>
                                                    <option  value="1" <?php if(isset($sitemapdata['changefrequency']) && $sitemapdata['changefrequency']==1 ){ echo "selected"; } ?>>Hourly</option>
                                                    <option   value="2" <?php if(isset($sitemapdata['changefrequency']) && $sitemapdata['changefrequency']==2 ){ echo "selected"; } ?>>Daily</option>
                                                    <option value="3" <?php if(isset($sitemapdata['changefrequency']) && $sitemapdata['changefrequency']==3 ){ echo "selected"; } ?> >Weekly</option>
                                                    <option value="4" <?php if(isset($sitemapdata['changefrequency']) && $sitemapdata['changefrequency']==4 ){ echo "selected"; } ?> >Monthly</option>
                                                    <option value="5" <?php if(isset($sitemapdata['changefrequency']) && $sitemapdata['changefrequency']==5 ){ echo "selected"; } ?> >Yearly</option>
                                                    <option value="6" <?php if(isset($sitemapdata['changefrequency']) && $sitemapdata['changefrequency']==6 ){ echo "selected"; } ?>>Never</option>
                                        </select>
                                    </div>
                            </div>
                        

                            <div class="form-group row">
                                <label for="focusedinput"  class="col-md-4 control-label">Activate</label>
                                <div class="col-md-1 col-xs-4">
                                    <div class="radio">
                                        <input type="radio" name="status" id="yes"  value="1" <?php if(isset($sitemapdata) && $sitemapdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>  >
                                        <label for="yes"><?php echo 'Yes'; ?></label>
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-4">
                                    <div class="radio">
                                        <input type="radio" name="status" id="no" value="0" <?php if(isset($sitemapdata) && $sitemapdata['status']==0){ echo 'checked'; }?> >
                                        <label for="no" ><?php echo 'No'; ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="focusedinput" class="col-sm-4 control-label"></label>
                                <div class="col-sm-8">
                                    <?php if(!empty($sitemapdata)){ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised text-white" onclick="resetdata()">
                                    <?php }else{ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
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