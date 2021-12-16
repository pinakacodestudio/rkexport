<script>
      
      
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($todolistdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($todolistdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>
    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-body">
                <div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-2 col-md-offset-2">
                  <form class="form-horizontal" id="todolistform">
                      <input type="hidden" name="id" id="id" value="<?php if(isset($todolistdata)){ echo $todolistdata['id']; } ?>">
                      <div class="form-body">
                        <div class="form-group" id="employee_div">
                          <label for="employeeid" class="col-sm-3 control-label">Employee <span class="mandatoryfield">*</span></label>
                          <div class="col-sm-6">
                            <select id="employeeid" name="employeeid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="7" >
                              <option value="0">Select Employee</option>
                            
                              <?php foreach($employee_data as $key =>$employee ){ ?>
                                  <option value="<?php echo $employee['id']; ?>" <?php  if(isset($todolistdata) && $todolistdata['employeeid']==$employee['id']){ echo "selected"; } ?>><?php echo ucwords($employee['name']); ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                    
                        <div class="form-group" id="todolist_div">
                          <label class="col-sm-3 control-label" for="todolist">To Do list <span class="mandatoryfield">*</span></label>
                          <div class="col-sm-6" id= "todoselect2">
                              <input id="todolist" type="text" name="todolist" data-url="<?php echo ADMIN_URL.'todo-list/gettodolist';?>" value="<?php if(isset($todolistdata)) {echo $todolistdata['id'];}?>" data-provide="todolist"  placeholder="To Do List">
                          </div>
                        </div>
                          <div class="form-group">
                            <div class="col-sm-12 text-center">
                                <?php if(!empty($todolistdata)){ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php }else{ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
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
