<style>
.popover {
  
  min-width: 30%;
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
            <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
              <div class="panel-heading filter-panel border-filter-heading">
                <h2><?=APPLY_FILTER?></h2>
                <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
              </div>
              <div class="panel-body panelcollapse pt-n" style="display: none;">
                <form action="#" id="categoryform" class="form-horizontal">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="col-sm-12 pr-sm">
                          <label for="statusid" class="control-label">Select Status</label>
                          <select id="statusid" name="statusid" class="selectpicker form-control"  data-live-search="true" data-select-on-tab="true" data-size="5"  >
                            <option value="">Select Status</option>
                            <option value="0"  <?php if(isset($arrSessionDetails['statusfilter'])){ if($arrSessionDetails['statusfilter']=='0'){echo "selected";} } ?>>Pending</option>
                            <option value="1" <?php if(isset($arrSessionDetails['statusfilter'])){ if($arrSessionDetails['statusfilter']=='1'){echo "selected";} } ?>>Approve</option>
                            <option value="2" <?php if(isset($arrSessionDetails['statusfilter'])){ if($arrSessionDetails['statusfilter']=='2'){echo "selected";} } ?>>Decline</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="col-sm-12 pr-sm pl-sm">
                          <label for="userid" class="control-label">Select Employee</label>
                          <select id="userid" name="userid" class="selectpicker form-control"  data-live-search="true" data-select-on-tab="true" data-size="5" >
                            <?php foreach ($employee_data as $key => $value) {?>
                              <option value="<?php echo $value['id'];?>" <?php if(isset($arrSessionDetails['employeefilter'])){ if($value['id']==$arrSessionDetails['employeefilter']){echo "selected";}}
                                else{ if($value['id']==$this->session->userdata(base_url().'ADMINID')){echo "selected";}}?> >
                                <?php echo $value['name'];?>
                              </option>
                            <?php }?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <div class="col-sm-12">
                          <label for="startdate" class="control-label">Select Date</label>
                          <div class="input-daterange input-group" id="datepicker-range">
                            <div class="input-group">
                              <input type="text" style="text-align: left;" class="input-small form-control text-left" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="btn btn-default datepicker_calendar_button" title='Date' ><i class="fa fa-calendar fa-lg"></i></span>
                            </div>  
                              <span class="input-group-addon">to</span>
                            <div class="input-group">  
                              <input type="text" style="text-align: left;" class="input-small form-control text-left" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                              <span class="btn btn-default datepicker_calendar_button" title='Date' ><i class="fa fa-calendar fa-lg"></i></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-2 p-n">
                      <div class="form-group" style="margin-top: 39px;">
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
              <div class="panel-heading pr-xs">
                <div class="col-md-4 p-n">
                  <div class="panel-ctrls panel-tbl"></div>
                </div>
                <div class="col-md-8 p-n" style="text-align: right;">
                  <?php 
                    if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                      <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>leave/leaveadd" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    } ?>
                    <?php
                    if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>leave/checkleaveuse','Leave','<?php echo ADMIN_URL; ?>leave/deletemulleave')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                  
                  
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="leavetable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width5">No.</th>
                      <th>Date</th>  
                      <th>Employee</th>  
                      <th>No. of Days</th>  
                      <th >From Date</th>
                      <th>To Date</th>
                      <th >Reason</th>
                      <th >Paid/Unpaid</th>
                      <th style='width:151px;' >Status</th>
                      <th style="width: 81px;">Action</th>
                      <th class="width5">
                        <div class="checkbox">
                          <input id="deletecheckall" onchange="allchecked()" type="checkbox" value="all">
                          <label for="deletecheckall"></label>
                        </div>
                      </th>
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
<div id="remark_data_Modal" class="modal fade">  
      <div class="modal-dialog">  
           <div class="modal-content">  
                <div class="modal-header"> 
                    <h4 class="modal-title" style="float:left;">Remarks for Leave</h4>  
                     <button type="button" class="close" data-dismiss="modal">&times;</button>  
                     
                </div>  
                <div class="modal-body pt-n">  
                     <form method="post" id="insert_form">                            
                            <div class="form-group" id="remark_div">
                                <label class="col-form-label" for="remarks">Reason</label>
                                <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter reason for reject leave" rows="5"></textarea>
                            <input type="hidden" id="id" name="id" class="form-control"> 
                            <input type="hidden" id="granted" name="granted" class="form-control">                          
                            <input type="hidden" id="employeeid" name="employeeid" class="form-control">                          
                            </div>                           
                          <input type="button" name="update" id="update" value="SUBMIT" class="btn btn-success btn-raised" /> 
                         
                     </form>  
                </div>                 
           </div>  
      </div>  
</div> 

<div class="modal" id="myModal2" tabindex='-1'>
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="col-md-8">Change leave to Paid/Unpaid</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                  <form action="#" id="paidunpaid_form" class="form-horizontal">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <div class="col-sm-12 pr-sm">
                                <div class="col-md-3">
                                  <label for="changeleave" class="control-label">Change Leave</label>
                                </div>
                                <div class="col-md-6">
                                  <select id="changeleave" name="changeleave" class="selectpicker form-control"  data-live-search="true" data-select-on-tab="true" data-size="5"  >
                                    <option value="0">Unpaid</option>
                                    <option value="1">Paid</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="form-group row" id="reason1_div" style="display: none;">
                              <div class="col-sm-12 pr-sm">
                                  <div class="col-md-3">
                                    <label class="control-label" for="reason1">Reason <span class="mandatoryfield">*</span></label>
                                  </div>
                                  <div class="col-md-8">
                                      <?php $data['controlname2']="reason"; 
                                      $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?> 
                                  </div>
                              </div>    
                            </div>
                            <div class="form-group row">
                                  <label class="col-md-3 control-label"></label>
                                  <div class="col-md-12 text-center">
                                      <input type="button" onclick="submitchangeleave()" value="SUBMIT" class="btn btn-success btn-raised">
                                  </div>
                            </div>
                          </div>
                          <!-- <div class="col-md-12">
                              <div class="form-group" id="changeleave_div">
                                  <label class="col-md-3 col-form-label" for="changeleave">Change Leave</label>
                                  <div class="col-md-8">
                                      <select class="form-control selectpicker" id="changeleave" name="changeleave" data-size="5" data-style="form-control">
                                          <option value="0">Unpaid</option>
                                          <option value="1">Paid</option>
                                      </select>
                                      <div class="text-danger" id="errormsg" style="display: none;">This is already selected.</div>
                                  </div>
                              </div>
                              <div class="form-group row" id="reason1_div" style="display: none;">
                                  <label class="col-md-3 col-form-label" for="reason1">Reason <span class="mandatoryfield">*</span></label>
                                  <div class="col-md-8">
                                      <?php $data['controlname2']="reason"; $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?> 
                                  </div>
                              </div>                             
                              <div class="form-group row">
                                  <label class="col-md-3 control-label"></label>
                                  <div class="col-md-8">
                                      <input type="button" onclick="submitchangeleave()" value="SUBMIT" class="btn btn-success btn-raised">
                                  </div>
                              </div>
                          </div> -->
                        </div>
                      <input type="hidden" name="leaveid" id="leaveid" value="">
                      <input type="hidden" name="previousleaveis" id="previousleaveis" value="">
                  </form>
              </div>
          </div>
      </div>
    </div>