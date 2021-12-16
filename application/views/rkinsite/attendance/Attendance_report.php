<?php 
$arrSessionDetails = $this->session->userdata;
?> 
<div class="page-content">
    <div class="page-heading">     
        <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                    <div class="panel-heading filter-panel border-filter-heading">
                        <h2><?=APPLY_FILTER?></h2>
                        <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                    </div>
                    <div class="panel-body panelcollapse pt-n" style="display: none;">
                      <form action="#" class="form-horizontal">
                          <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                  <div class="col-sm-12 pr-sm">
                                      <label for="employeeid" class="control-label">Employee</label>
                                      <select class="form-control selectpicker" id="employeeid" name="employeeid" data-live-search="true" data-size="8">
                                        <option value="0">All Employee</option>
                                        <?php foreach ($employeedata as $_v) { ?>        
                                            <option value="<?php echo $_v['id'];?>" <?php if(!is_null($this->session->userdata("attendancereportemployeefilter"))){ 
                                            if($this->session->userdata("attendancereportemployeefilter")==$_v['id']){
                                                echo "selected"; }
                                            }else{ if(($_v['id']==$this->session->userdata(base_url().'ADMINID'))){echo "selected";}} ?> ><?php echo ucwords($_v['name']);?></option>
                                        <?php } ?>
                                    </select> 
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                  <div class="col-sm-12 pl-sm pr-sm">
                                    <label for="startdate" class="control-label">Date</label>
                                    <div class="input-daterange input-group" id="datepicker-range">
                                        <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php if(!empty($arrSessionDetails["attendancereportfromdatefilter"])){ echo $arrSessionDetails["attendancereportfromdatefilter"]; }else{ echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); } ?>" placeholder="Start Date" title="Start Date" readonly/>
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php if(!empty($arrSessionDetails["attendancereporttodatefilter"])){ echo $arrSessionDetails["attendancereporttodatefilter"]; }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" placeholder="End Date" title="End Date" readonly/>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-xl">
                                  <div class="col-sm-12">
                                      <label class="control-label"></label>
                                      <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                  </div>
                                </div>
                            </div>
                          </div> 
                      </form>
                    </div>
                </div>
            </div>
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-heading">
                
                <div class="col-md-6">
                  <div class="panel-ctrls panel-tbl"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="attendancereporttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>  
                        <th class="width8">Sr. No. </th>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Checkin Time</th>
                        <th>Checkout Time</th>     
                        <!-- <th>Break</th>                                
                        <th>Non Attendance</th>      -->
                        <th>Total Time</th> 
                        <th class="width12">Location</th> 
                        <th class="width8">Profile</th>                                        
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
