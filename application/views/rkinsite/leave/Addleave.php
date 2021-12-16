<script>
      var FullDay = '<?php if(isset($leavedata)){echo $leavedata['leavetype'];}else{echo 0;}?>' ;
      
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($leavedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($leavedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
                  <form class="form-horizontal" id="leaveform">
                      <input type="hidden" name="id" id="id" value="<?php if(isset($leavedata)){ echo $leavedata['id']; } ?>">
                      <div class="form-body">
                          <div class="form-group" id="leavetype_div">
                            <label for="leavetype" class="col-sm-3 control-label">Leave</label>
                            <div class="col-sm-8">
                              <div class="row">
                                <div class="col-sm-4" style="padding-left: 15px;">
                                  <div class="radio">
                                    <input type="radio" name="leavetype" id="full" value="1" <?php if(isset($leavedata) && $leavedata['leavetype']==1){ echo 'checked'; }else{ echo 'checked'; }?> >
                                    <label for="full">Full Day</label>
                                  </div>
                                </div>
                                <div class="col-sm-4">
                                  <div class="radio">
                                    <input type="radio" name="leavetype" id="half" value="0" <?php if(isset($leavedata) && $leavedata['leavetype']==0){ echo 'checked'; }?>>
                                    <label for="half" >Half Day</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group" id="fromdate_div">
                              <label class="col-md-3 control-label" for="fromdate">From Date <span class="mandatoryfield">*</span></label>
                              <div class="col-md-6">
                                <div class="input-group">
                                  <input type="text" id="fromdate" class="form-control" name="fromdate" value="<?php if(isset($leavedata)){ echo $this->general_model->displaydate($leavedata['fromdate']); } ?>" readonly>
                                  <span class="btn btn-default datepicker_calendar_button" title='Date' ><i class="fa fa-calendar fa-lg"></i></span>
                                </div>
                              </div>
                          </div>
                          <div class="form-group" id="todate_div">
                              <label class="col-md-3 control-label" for="todate">To Date <span class="mandatoryfield">*</span></label>
                              <div class="col-md-6">
                                <div class="input-group">
                                  <input type="text" id="todate" class="form-control" name="todate" value="<?php if(isset($leavedata)){ echo $this->general_model->displaydate($leavedata['todate']); } ?>" readonly>
                                  <span class="btn btn-default datepicker_calendar_button" title='Date' ><i class="fa fa-calendar fa-lg"></i></span>
                                </div>
                              </div>
                          </div>
                          <div class="form-group" id="halfleave_div">
                            <label for="leavetype" class="col-sm-3 control-label">Half Leave</label>
                            <div class="col-sm-8">
                              <div class="row">
                                <div class="col-sm-4" style="padding-left: 15px;">
                                  <div class="radio">
                                    <input type="radio" name="halfleave" id="firsthalf" value="first" <?php if(isset($leavedata) && $leavedata['halfleave']=='first'){ echo 'checked'; }else{ echo 'checked'; }?> >
                                    <label for="firsthalf">First Half</label>
                                  </div>
                                </div>
                                <div class="col-sm-4">
                                  <div class="radio">
                                    <input type="radio" name="halfleave" id="secondhalf" value="second" <?php if(isset($leavedata) && $leavedata['halfleave']=='second'){ echo 'checked'; }?>>
                                    <label for="secondhalf" >Second Half</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group" id="reason_div">
                            <label class="col-md-3 control-label" for="todate">Reason <span class="mandatoryfield">*</span></label>
                            <div class="col-md-6">
                              <textarea class="form-control" name="reason" id="reason" rows="5" placeholder="Reason"><?php if(!empty($leavedata)){ echo $leavedata['reason']; } ?></textarea>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-sm-12 text-center">
                                <?php if(!empty($leavedata)){ ?>
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
