<div class="page-content">
    <div class="page-heading">            
        <h1>Edit IndiaMART Lead</h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active">Edit IndiaMART Lead</li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
                    <div class="col-sm-12">
                        <form class="form-horizontal" id="indiamartleadform" name="indiamartleadform" method="post">   
                            <div class="col-md-11 col-md-offset-1">                                            
                                <div class="form-group" id="mobileno_div">
                                    <label class="col-md-4 control-label" for="mobileno">Mobile No. <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-4">
                                        <input type="text" id="mobileno" name="mobileno" value="<?php echo $leaddata['mobileno']; ?>" class="form-control" maxlength="10" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                    </div>                                                
                                </div> 
                                <div class="form-group" id="mobilekey_div">
                                    <label class="col-md-4 control-label" for="mobilekey">Mobile Key <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-4">
                                        <input type="text" id="mobilekey" name="mobilekey" value="<?php echo $leaddata['mobilekey']; ?>" class="form-control">
                                    </div>                                                
                                </div> 
                                <div class="form-group" id="forwardemployee_div">
                                    <label for="forwardemployee" class="col-md-4 control-label">Assign To For Forward Inquiry <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-4">
                                        <select id="forwardemployee" name="forwardemployee" class="selectpicker form-control" data-live-search="true" data-size="8">
                                            <option value="0">Select Employee</option>
                                            <?php foreach($employeename as $en){?>
                                                <option value="<?=$en['id']?>" <?php if(isset($leaddata)){ if($leaddata['forwardassigntoid'] == $en['id']){ echo 'selected'; } } ?>><?=ucwords($en['name'])?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="status_div">
                                    <label for="action" class="col-md-4 control-label">Action</label>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="radio">
                                                    <input type="radio" name="action" id="enable" value="1" onclick="EnableCode1()" <?php if($leaddata['status']==1){echo "checked";}?>>
                                                    <label for="enable"><?php echo 'Enable'; ?></label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="radio">
                                                    <input type="radio" name="action" id="disable" value="0" onclick="DisableCode1()" <?php if($leaddata['status']==0){echo "checked";}?>>
                                                    <label for="disable" ><?php echo 'Disable'; ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                           
                                <div id="synch_group" style="display:none;"> 
                                    <div class="form-group" id="checkbox_div">
                                        <label class="col-md-4 control-label"></label>
                                        <div class="col-md-4">
                                            <div class="checkbox">
                                                <input type="checkbox" name="synchronize" id="synchronize" value="1">
                                                <label for="synchronize"><?php echo 'Synchronization'; ?></label>
                                                <span id="syncronizing" style="color:red;">Syncronizing...</span>
                                            </div>
                                        </div>                                                
                                    </div> 
                                </div> 
                                <div id="date_group" style="display:none;">                                      
                                    <div class="form-group" id="date_div">
                                        <div class="input-group" id="datepicker-range"> 
                                            <label for="todate" class="col-md-4 control-label">End DateTime <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-4">                                                         
                                                <input type="text" class="form-control datepicker1" name="todate" id="todate" value="<?php if($leaddata['enddate']!="0000-00-00 00:00:00" && $leaddata['enddate']!=null){ echo $this->general_model->convertdatetime($leaddata['enddate'],'d-m-Y H:i:s'); } ?>" readonly/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="backwardemployee_div"> 
                                        <label for="backwardemployee" class="col-md-4 control-label">Assign To For Backword Inquiry <span class="mandatoryfield">*</span></label>
                                        <div class="col-md-4">                                                   
                                            <select id="backwardemployee" name="backwardemployee" class="selectpicker form-control" data-live-search="true" data-size="8">
                                                <option value="0">Select Employee</option>
                                                <?php foreach($employeename as $en){?>
                                                    <option value="<?=$en['id']?>" <?php if(isset($leaddata)){ if($leaddata['backwardassigntoid'] == $en['id']){ echo 'selected'; } } ?>><?=ucwords($en['name'])?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="form-group" id="lastdate_div">
                                        <div class="input-group" id="datepicker-range"> 
                                            <label for="lastdate" class="col-md-4 control-label">Last Synchronize DateTime </label>
                                            <div class="col-md-4">                                                         
                                                <input type="text" class="form-control datepicker1" name="lastdate" id="lastdate" value="<?php if($leaddata['backdatetime']!="0000-00-00 00:00:00" && $leaddata['backdatetime']!=null){ echo $this->general_model->convertdatetime($leaddata['backdatetime'],'d-m-Y H:i:s'); } ?>" disabled/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>   
                            <div class="col-sm-12"> 
                                <hr>
                                <div class="form-group text-center">                                               
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SUBMIT" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised text-white" onclick="resetdata()">
                                    <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>dashboard" title=<?=cancellink_title?>><?=cancellink_text?></a>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.css"/>

<script language="javascript">
$(document).ready(function() {
   $('input[type="radio"]').click(function() {
       if($(this).attr('id') == 'enable') {
            $('#synch_group').show();           
       }
       else {
            $('#synch_group').hide();   
            $('#date_group').hide();  
            $('#fromdate').val("");
            $('#todate').val("");
            $('input[type="checkbox"]').prop('checked', false);
       }
   });
   $("#synchronize").click(function () {
        if ($(this).is(":checked")) {
            $('#date_group').show();
        } else {
            $('#date_group').hide();   
        }
    });

    <?php if($leaddata['status']==1){ ?>
        $('#synch_group').show();
		EnableCode1();
    <?php } ?>
    <?php if($leaddata['status']==1&&$leaddata['enddate']!="0000-00-00 00:00:00"){ ?>
        $('#synch_group').show();
        $('#date_group').show();
        $('input[type="checkbox"]').prop('checked', true);
		EnableCode1();
    <?php } ?>
    <?php if($leaddata['status']==0){ ?>
        $('#synch_group').hide();
		DisableCode1();
    <?php } ?> 
    <?php if($leaddata['enddate']!="0000-00-00 00:00:00" && $leaddata['enddate'] < $leaddata['backdatetime']){ ?>		
        $('#syncronizing').show();        
    <?php } ?> 
    <?php if($leaddata['enddate'] > $leaddata['backdatetime']){ ?>		
        $('#date_group').hide();  
        $('input[type="checkbox"]').prop('checked', false);
        $('#syncronizing').hide(); 
    <?php } ?>
    <?php if($leaddata['mobileno']=="" && $leaddata['mobilekey']==""){ ?>
        $('#mobileno').prop("disabled", true);
        $("#mobilekey").prop("disabled", true);
    <?php }?>
       
   
});

function DisableCode1(){
	$('#mobileno').prop("disabled", true);
	$("#mobilekey").prop("disabled", true);
}

function EnableCode1(){
	$('#mobileno').prop("disabled", false);
	$("#mobilekey").prop("disabled", false);
}
</script>