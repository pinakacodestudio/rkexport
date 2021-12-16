<?php 
$arrSessionDetails = $this->session->userdata;
$employeeid = $this->session->userdata(base_url().'ADMINID');
?> 
<style>    
    .datepicker1{
      text-align: left !important;
      border-radius: 3px !important;
    }
</style>
<div class="page-content">
    <div class="page-heading">     
        <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-heading">
                
                <div class="row">
                  <div class="col-md-8 col-md-offset-2">
                    <div class="col-md-12">          
                        <div class="row"> 
                            <div class="col-md-8">
                                <input type="button" id="btnbreakin" onclick="employeebreakinstatus(<?= $attendance['id'];?>)" name="btnbreakin" value="Break In" class="btn btn-primary btn-raised" style="display:none;">
                                <input type="button" id="btnbreakout" onclick="employeebreakoutstatus(<?= $attendance['id'];?>)" name="btnbreakout" value="Break Out" class="btn btn-primary btn-raised" style="display:none;">
                            </div>
                            <div class="col-md-3">
                                <input type="button" id="btnnonattendancein" onclick="employeenonattendanceinstatus(<?= $attendance['id'];?>)" name="btnnonattendancein" value="Non-Attendance In" class="btn btn-primary btn-raised" style="display:none;">
                                <input type="button" id="btnnonattendanceout" onclick="employeenonattendanceoutstatus(<?= $attendance['id'];?>)" name="btnnonattendanceout" value="Non-Attendance Out" class="btn btn-primary btn-raised" style="display:none;">
                            </div>
                            <div class="col-md-1">
                                <button type="button" id="showbtn" class="btn btn-success btn-raised">Show</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="border: 1px solid #c4b5b5;padding: 5px 16px;margin-left: 15px;">                    
                        <div class="row">                   
                            <div class="col-md-10">
                                <div class="input-daterange input-group" id="datepicker-range">
                                <span class="input-group-addon"><p style="margin: 5px;">From </p></span>
                                <input type="text" class="input-small form-control datepicker1" name="fromdate" id="fromdate" placeholder="From Date" value="<?php if(!empty($arrSessionDetails["attendancefromdatefilter"])){ echo $arrSessionDetails["attendancefromdatefilter"]; }else{ echo date("d/m/Y",strtotime("-1 month")); } ?>" readonly/>
                                <span class="input-group-addon"><p style="margin: 5px;">To </p></span>
                                <input type="text" class="input-small form-control datepicker1" name="todate" id="todate" placeholder="To Date"  value="<?php if(!empty($arrSessionDetails["attendancetodatefilter"])){ echo $arrSessionDetails["attendancetodatefilter"]; }else{ echo date("d/m/Y"); } ?>" readonly/>
                                </div>
                            </div>                     
                            <div class="col-md-1 p-lr-0">
                                <button type="button" id="applyfilterbtn" class="btn btn-primary btn-raised">Apply</button>
                            </div>                      
                        </div>                   
                    </div>
                            
                    <div class="col-md-12" style="border: 1px solid #c4b5b5;padding: 0px 10px;margin-left: 15px;">              
                        <div class="row"> 
                            <div class="col-md-6" style="height: 48px;">
                                <input type="button" id="btncheckin" onclick="employeecheckinstatus()" name="btncheckin" value="Check In" class="btn btn-primary btn-raised">
                                <input type="button" id="btncheckout" onclick="employeecheckoutstatus()" name="btncheckout" value="Check Out" class="btn btn-danger btn-raised" style="display:none;">
                                <input type="button" id="btnrecheckin" onclick="employeerecheckinstatus()" name="btnrecheckin" value="Re-Check In" class="btn btn-primary btn-raised" style="display:none;">
                            </div>
                            <?php 
                            if($this->session->userdata(base_url().'ADMINUSERTYPE') == 1 || $this->session->userdata(base_url().'ADMINUSERTYPE') == 2 || $this->session->userdata(base_url().'ADMINUSERTYPE') == 3){
                            ?>
                                <div class="col-md-6">                    
                                    <div class="form-group mt-n" id="filteremployee_div">    
                                        <select class="form-control selectpicker" id="employee" name="employee" data-live-search="true" data-size="8">
                                            <option value="">Select Employee</option>
                                            <?php foreach ($employeedata as $_v) { ?>        
                                                <option value="<?php echo $_v['id'];?>" <?php if(!is_null($this->session->userdata("attendanceemployeefilter"))){ 
                                                if($this->session->userdata("attendanceemployeefilter")==$_v['id']){
                                                    echo "selected"; }
                                                }else{ if(($_v['id']==$this->session->userdata(base_url().'ADMINID'))){echo "selected";}} ?> ><?php echo ucwords($_v['name']);?></option>
                                            <?php } ?>
                                            <option value="-1" <?php if(!is_null($this->session->userdata("attendanceemployeefilter"))){ 
                                                if($this->session->userdata("attendanceemployeefilter")=="-1"){
                                                    echo "selected"; }
                                                } ?>>All</option>
                                        </select> 
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-12 p-n"><hr></div>
                </div>
                <div class="col-md-6">
                  <div class="panel-ctrls panel-tbl"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="attendancetable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>  
                        <th class="width8">Sr. No. </th>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Profile</th>
                        <th>Checkin Time</th>
                        <th>Checkout Time</th>
                        <!-- <th>Break</th>  -->
                        <!-- <th>Non Attendance</th> -->                                  
                        <th>Total Time</th>                  
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
              <div class="panel-footer"></div>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->

<script language="javascript">
  $(document).ready(function() {   
    <?php if(isset($attendance) && $attendance['status']==1 && $attendance['checkout']=="00:00:00"){ ?>
        $('#btncheckout').show();
        $('#btncheckin').hide();
        $('#btnrecheckin').hide();  
    
        <?php if(isset($attendance) && $attendance['breakintime']=="00:00:00" && $attendance['breakouttime']=="00:00:00"){ 
          if($count<=1){?>
            $('#btnbreakout').show();
            $('#btnbreakin').hide();   
          <?php }
        } else if(isset($attendance) && $attendance['breakintime']!="00:00:00" && $attendance['breakouttime']=="00:00:00"){?>
          $('#btnbreakout').hide();
          $('#btnbreakin').show();  
          $('#btncheckout').hide(); 
          $('#btnnonattendancein').hide();
          $('#btnnonattendanceout').hide();    
        <?php }else if(isset($attendance) && $attendance['breakintime']!="00:00:00" && $attendance['breakouttime']!="00:00:00"){ ?> 
          $('#btnbreakout').hide();
          $('#btnbreakin').hide();   
        <?php } ?> 

        <?php if($nonattendance['nastarttime']=="" && $nonattendance['naendtime']==""){ ?>
          $('#btnnonattendanceout').show();   
          $('#btnnonattendancein').hide();     
        <?php } else if(isset($nonattendance) && $nonattendance['nastarttime']!="00:00:00" && $nonattendance['naendtime']=="00:00:00"){?>
          $('#btnnonattendanceout').hide();   
          $('#btnnonattendancein').show(); 
          $('#btncheckout').hide(); 
          $('#btnbreakout').hide();
          $('#btnbreakin').hide();  
        <?php } else if(isset($nonattendance) && $nonattendance['nastarttime']!="00:00:00" && $nonattendance['naendtime']!="00:00:00"){?>
          $('#btnnonattendanceout').show();   
          $('#btnnonattendancein').hide();      
        <?php } ?>

        <?php if(isset($attendance) && $attendance['breakintime']!="00:00:00" && $attendance['breakouttime']=="00:00:00"){ ?>
          $('#btnnonattendanceout').hide();   
          $('#btnnonattendancein').hide(); 
        <?php } ?>
    
    <?php }else if(isset($attendance) && $attendance['status']==0 && $attendance['checkout']=="00:00:00"){ ?>
        $('#btncheckin').show();
        $('#btncheckout').hide();
        $('#btnrecheckin').hide();
        $('#btnbreakout').show();
        $('#btnnonattendanceout').show();    
    <?php }elseif(isset($attendance) && $attendance['status']==0 && $attendance['checkout']!="00:00:00"){ ?>   
        $('#btncheckin').hide();
        $('#btncheckout').hide();
        $('#btnrecheckin').show();
        $('#btnbreakout').hide();
        $('#btnnonattendanceout').hide();  
    <?php }?>
    }); 
</script> 