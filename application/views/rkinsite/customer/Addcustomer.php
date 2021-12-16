<script type="text/javascript">
  var olduserroleid = <?php if(isset($customerdata)){ echo $customerdata['roleid'];}else { echo 0; }?>;
  var profileimgpath = '<?php echo PROFILE;?>';
  var defaultprofileimgpath = '<?php echo DEFAULT_PROFILE;?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($customerdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($customerdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
    </small>
    </div>

    <div class="container-fluid">
                                    
        <div data-widget-group="group1">
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <div class="panel-body">
          <form action="#" id="customerform" class="form-horizontal">
            <input type="hidden" name="customerid" value="<?php if(isset($customerdata)){ echo $customerdata['id']; } ?>">
                <div class="form-group row" id="name_div">
                  <label class="control-label col-md-3" for="name">Name <span class="mandatoryfield">*</span></label>
                    <div class="col-md-4">
                    <input id="name" class="form-control" name="name" value="<?php if(isset($customerdata)){ echo $customerdata['name']; } ?>" type="text" tabindex="1" onkeypress="return onlyAlphabets(event)">
                  </div>
                </div>
                <div class="form-group row" id="email_div">
                  <label class="control-label col-md-3" for="email">Email <span class="mandatoryfield">*</span></label>
                  <div class="col-md-4">
                    <input id="email" type="text" name="email" value="<?php if(isset($customerdata)){ echo $customerdata['email']; } ?>" class="form-control" tabindex="3">
                  </div>
                </div>
                <div class="form-group" id="password_div">
                  <label class="control-label col-md-3" for="password">Password </label>
                    <div class="col-md-4">
                    <div>
                      <div class="col-sm-10" style="padding: 0px;">
                        <input id="password" type="text" name="password" class="form-control" tabindex="7">
                      </div>
                      <div class="col-sm-2" style="padding-right: 0px;">
                        <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Generate Password" onclick="$('#password').val(randString())"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
            
                <div class="form-group row" id="mobile_div">
                  <label class="control-label col-md-3" for="mobileno">Mobile No <span class="mandatoryfield">*</span></label>  
                  <div class="col-md-4">
                  <div class="row">
                      <div class="col-md-4">
                        <select id="countrycodeid" name="countrycodeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" tabindex="2" data-live-search="true">
                          <option value="0">Code</option>
                          <?php foreach($countrycodedata as $countrycoderow){ ?>
                          <option value="<?php echo $countrycoderow['id']; ?>" <?php if(isset($countrycodedata)){ if($customerdata['countrycode'] == $countrycoderow['id']){ echo 'selected'; } }  ?>><?php echo $countrycoderow['phonecode']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="col-md-8">
                        <input id="mobileno" type="text" name="mobileno" value="<?php if(isset($customerdata)){ echo $customerdata['mobile']; } ?>" class="form-control" maxlength="12"  onkeypress="return isNumber(event)" tabindex="5">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="focusedinput" class="col-md-3 control-label">Activate</label>
                  <div class="col-md-8">
                    <div class="col-md-2 col-xs-2" style="padding-left: 0px;">
                      <div class="radio">
                      <input type="radio" name="status" id="yes" value="1" <?php if(isset($customerdata) && $customerdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                      <label for="yes">Yes</label>
                      </div>
                    </div>
                    <div class="col-md-2 col-xs-2">
                      <div class="radio">
                      <input type="radio" name="status" id="no" value="0" <?php if(isset($customerdata) && $customerdata['status']==0){ echo 'checked'; }?>>
                      <label for="no">No</label>
                      </div>
                    </div>
                  </div>
                </div>
              <div class="form-group row">
                <div class="col-md-offset-3 col-md-6">
                <?php if(isset($customerdata)){ ?>
                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                <?php }else{ ?>
                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                <?php } ?>
                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>customer" title=<?=cancellink_title?>><?=cancellink_text?></a>
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
