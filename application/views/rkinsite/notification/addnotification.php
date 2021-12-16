<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($notificationdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($notificationdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
    </small>
    </div>

  

    <div class="container-fluid">
                                    
        <div data-widget-group="group1">
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="col-sm-12 col-md-10 col-lg-10 col-lg-offset-1 col-md-offset-1">
                    <form class="form-horizontal offset-md-1" id="form-notification" enctype="multipart/form-data">
                      <div class="form-body">

                          <div class="form-group row" for="catalogname" id="customer_div">
                            <label class="col-md-2 label-control" for="customerid">
                                 Customer
                             <span class="mandatoryfield"> * </span></label>
                            <div class="col-md-5">
                              <select id="customerid" name="customerid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" multiple data-actions-box="true" title="Select Customer">
                                <option value="0">Select Customer</option>
                                
                                <?php foreach ($customerData as $customer) { ?>
                                   <option value='<?php echo $customer['id']; ?>'><?=$customer['name']?></option>
                                <?php } ?>
                              </select>
                              </div>
                            </div>
                            <div class="form-group row" id="description_div">
                                <div id='termscontainer'>
                                     <label for="description" class="col-sm-2  label-control">Message <span class="mandatoryfield">*</span></label></label>
                                    <div class="col-sm-9">
                                          <?php $data['controlname']="description";if(isset($notificationdata) && !empty($notificationdata)){$data['controldata']=$notificationdata['description'];} ?>
                                          <?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <label for="focusedinput" class="col-sm-2 control-label"></label>
                              <div class="col-sm-8">
                                <?php if(!empty($notificationdata)){ ?>
                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php }else{ ?>
                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>notification" title=<?=cancellink_title?>><?=cancellink_text?></a>
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
</div>    					