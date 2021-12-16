<?php 
$arrSessionDetails = $this->session->userdata;
?>
<script>
  var alldatarights = <?php echo $alldatarights; ?>;
  followupdatetype = "<?=FOLLOWUP_DATE_TYPE?>";
  var defaultfollowupdate= '<?=(DEFAULT_FOLLOWUP_DATE!="")?DEFAULT_FOLLOWUP_DATE:0 ?>';
  <?php if(isset($child_employee_data)){ ?>
    child_employee_data = <?php echo json_encode($child_employee_data); ?>;
  <?php }else{ ?>  
    child_employee_data = <?php echo json_encode(array()); ?>;
  <?php } ?> 
</script>

<div class="page-content">
    <div class="page-heading">     
      <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                <div class="panel-heading filter-panel border-filter-heading flpstatuspnl" display-type="<?php if(isset($arrSessionDetails["followupstatuscollapse"])){ if($arrSessionDetails["followupstatuscollapse"]=="0"){ echo '1'; }else{ echo '0'; }  }else{ echo "0"; } ?>">
                    <h2>Status Wise Followup</h2>
                    <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                </div>
                <div class="panel-body panelcollapse pt-n" style="display: none;">
                  <form action="#" class="form-horizontal">
                    <div class="row">
                      
                      <div class="col-md-3">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="status" class="control-label">Employee</label>
                            <select id="filterstatusemployee" name="employee" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                              <option value="0">Select Employee</option>
                              <?php foreach($employeedata1 as $_v){ 
                                $selected = "";
                                if(!empty($arrSessionDetails["followupstatusemployeefilter"])){ 
                                  if($arrSessionDetails["followupstatusemployeefilter"]==$_v['id']){ 
                                    $selected = "selected"; 
                                  } 
                                }else{ 
                                  if(($_v['id']==$this->session->userdata(base_url().'ADMINID'))){
                                    $selected = "selected";
                                  }
                                }
                                ?>
                                <option value="<?=$_v['id']?>" <?=$selected?>><?=ucwords($_v['name'])?></option> 
                              <?php } ?>  
                              <option value="-1" <?php if(!empty($arrSessionDetails["followupstatusemployeefilter"]) && $arrSessionDetails["followupstatusemployeefilter"]=="-1"){ echo "selected"; } ?>>All</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="startdate1" class="control-label">Date</label>
                            <div class="input-daterange input-group" id="datepicker-range">
                              <input type="text" class="input-small form-control" name="startdate1" id="startdate1" value="<?php if(!empty($arrSessionDetails["followupstatusfromdatefilter"])){ echo $arrSessionDetails["followupstatusfromdatefilter"]; }else{ echo date("d/m/Y",strtotime("-1 month")); } ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="input-group-addon">to</span>
                              <input type="text" class="input-small form-control" name="enddate1" id="enddate1" value="<?php if(!empty($arrSessionDetails["followupstatustodatefilter"])){ echo $arrSessionDetails["followupstatustodatefilter"]; }else{ echo date("d/m/Y"); } ?>" placeholder="End Date" title="End Date" readonly/>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-3">
                        <div class="form-group" style="margin-top: 39px;">
                          <div class="col-sm-12">
                            <label class="control-label"></label>
                            <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter1()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-12">
                      <?php foreach ($followupstatuses as $k=>$cs) { ?>
                        <div class="col-md-3">
                          <div class="card">
                            <div class="card-body count-card1">
                              <div class="callout callout-success" style="border-left-color:<?=$cs['color']?>">
                                <small class="text-muted"><?=$cs['name']?></small>
                                <div class="dropdown float-right" style="display:none;">
                                  <button type="button" class="btn btn-sm dropdown-toggle text-white" data-toggle="dropdown" style="background-color:<?=$cs['color']?>">
                                  <i class="fa fa-clock-o fa-lg"></i>
                                  </button>
                                  <div class="dropdown-menu">
                                    <a class="dropdown-item statusdd<?=$cs['id']?>" id="statusdd<?=$cs['id']?>1" href="#" onclick="getstatuscount(1,'<?=$cs['id']?>','<?=$cs['id']?>')" data-toggle="popover1"  data-content="<?php echo date("d/m/Y",strtotime("-1 month"))." - ".date("d/m/Y") ?>">1 Month</a>
                                    <a class="dropdown-item statusdd<?=$cs['id']?>" id="statusdd<?=$cs['id']?>2" href="#" onclick="getstatuscount(2,'<?=$cs['id']?>','<?=$cs['id']?>')" data-toggle="popover1"  data-content="<?php echo date("d/m/Y",strtotime("-3 month"))." - ".date("d/m/Y") ?>">3 Month</a>
                                    <a class="dropdown-item statusdd<?=$cs['id']?>" id="statusdd<?=$cs['id']?>3" href="#" onclick="getstatuscount(3,'<?=$cs['id']?>','<?=$cs['id']?>')" data-toggle="popover1"  data-content="<?php echo date("d/m/Y",strtotime("-6 month"))." - ".date("d/m/Y") ?>">6 Month</a>
                                    <a class="dropdown-item statusdd<?=$cs['id']?>" id="statusdd<?=$cs['id']?>4" href="#" onclick="getstatuscount(4,'<?=$cs['id']?>','<?=$cs['id']?>')" data-toggle="popover1"  data-content="<?php echo date("d/m/Y",strtotime("-12 month"))." - ".date("d/m/Y") ?>">1 Year</a>
                                    <a class="dropdown-item active statusdd<?=$cs['id']?>" id="statusdd<?=$cs['id']?>5" href="#" onclick="getstatuscount(5,'<?=$cs['id']?>','<?=$cs['id']?>')">All</a>
                                  </div>
                                </div>
                                <br>
                                <strong class="h4 status_count" id="status_count<?=$cs['id']?>" status-id="<?=$cs['id']?>">0</strong>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php } ?>
                      </div>

                    </div> 
                  </form>
                </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                <div class="panel-heading filter-panel border-filter-heading flppnl" display-type="<?php if(isset($arrSessionDetails["followupcollapse"])){ if($arrSessionDetails["followupcollapse"]=="0"){ echo '1'; }else{ echo '0'; }  }else{ echo "0"; } ?>">
                    <h2><?=APPLY_FILTER?></h2>
                    <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                </div>
                <div class="panel-body panelcollapse pt-n" style="display: none;">
                  <form action="#" class="form-horizontal">
                    <div class="row">
                      
                      <div class="col-md-3">
                        <div class="form-group" id="members_div">
                          <div class="col-sm-12">
                            <label for="filtermember" class="control-label"><?=Member_label?></label>
                            <input class="js-data-example-ajax mt-sm" id="filtermember" placeholder="Select <?=Member_label?>" style="width:100%;" value="<?php if(!empty($arrSessionDetails['followupmemberfilter'])){
                              echo $arrSessionDetails['memberfilter'];
                            } ?>" data-text="<?php if($membername!=""){ echo $membername;  }?>">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="status" class="control-label">Employee</label>
                            <select class="form-control selectpicker" id="filteremployee" name="employee" data-live-search="true" data-size="8">
                              <option value="0">Select Employee</option>
                              <?php foreach ($employeedata1 as $_v) { ?>        
                                  <option value="<?php echo $_v['id'];?>" <?php if(!empty($arrSessionDetails["followupemployeefilter"])){ 
                                    if($arrSessionDetails["followupemployeefilter"]==$_v['id']){
                                      echo "selected"; }
                                    }else{ if(($_v['id']==$this->session->userdata(base_url().'ADMINID'))){echo "selected";}} ?>>
                                  <?php echo ucwords($_v['name']);?></option>
                              <?php } ?>
                              <option value="-1" <?php if(!empty($arrSessionDetails["followupemployeefilter"])){ 
                                if($this->session->userdata("followupemployeefilter")=="-1"){
                                  echo "selected"; }
                                } ?>>All</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="status" class="control-label">Status</label>
                            <select id="filterstatus" name="filterstatus" class="selectpicker form-control" data-live-search="true" data-size="8">
                              <option value="" >Select Status</option>
                              <?php foreach($followupstatuses as $k=>$fs){ ?>
                                <option value="<?=$fs['id']?>" <?php if(!empty($arrSessionDetails["followupstatusfilter"]) && $arrSessionDetails["followupstatusfilter"]==$fs['id']){ echo "selected"; } ?>><?=$fs['name']?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-3">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="startdate" class="control-label">Date</label>
                            <div class="input-daterange input-group" id="datepicker-range1">
                              <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php if(!empty($arrSessionDetails['followupfromdatefilter'])){ echo $arrSessionDetails['followupfromdatefilter']; }else{ echo date("d/m/Y",strtotime("-1 month")); } ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="input-group-addon">to</span>
                              <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php if(!empty($arrSessionDetails['followuptodatefilter'])){ echo $arrSessionDetails['followuptodatefilter']; }else{ echo date("d/m/Y"); } ?>" placeholder="End Date" title="End Date" readonly/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="Followup Type" class="control-label">Followup Type</label>
                              <select id="filterfollowuptype" name="filterfollowuptype" class="selectpicker form-control" data-live-search="true" data-size="8">
                                <option value="" >Select Followup Type</option>
                                <?php foreach($followuptypedata as $row){ ?>
                                  <option value="<?=$row['id']?>" <?php if(!empty($arrSessionDetails['followuptypefilter']) && $arrSessionDetails['followuptypefilter']==$row['id']){ echo "selected"; } ?>><?=$row['name']?></option>
                                <?php } ?>
                              </select>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-3">
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
              <div class="panel-heading">
                <div class="col-md-6">
                  <div class="panel-ctrls panel-tbl"></div>
                </div>
               <div class="col-md-6 form-group" style="text-align: right;">
                  <?php if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                    <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>daily-followup/check-followup-use','Followup','<?php echo ADMIN_URL; ?>daily-followup/delete-mul-followup')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                    <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportdailyfollowup()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                  <?php } ?>    
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="dailyfollowuptable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th>Company Name</th>
                      <th>Assign To</th>
                      <th>Mobile</th>
                      <th><?=Followup?> Type</th>
                      <th>Date Time</th>
                      <?php if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){  ?>
                        <th>Status</th>
                      <?php } ?>
                      <th class="width15">Action</th>
                      <th class="width5">
                        <div class="checkbox table-checkbox">
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
<div class="modal" id="myModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
        <h4 class="modal-title" id="post_title">Edit <?=Followup?></h4>
      </div>
      <div class="modal-body pt-n">
        <form action="#" id="followupform" class="form-horizontal">
          <input type="hidden" name="followupid" id="followupid" value="">
          <input type="hidden" name="oldassignto" id="oldassignto" value="">                                  
          <input type="hidden" name="olddate" id="olddate" value="">
          <input type="hidden" name="oldfollowuptype" id="oldfollowuptype" value="">
          <input type="hidden" name="oldstatus" id="oldstatus" value="">
          <input type="hidden" name="oldfollowupnote" id="oldfollowupnote" value="">
          <input type="hidden" name="oldfuturenote" id="oldfuturenote" value="">
          <input type="hidden" name="oldlatitude" id="oldlatitude" value="">
          <input type="hidden" name="oldlongitude" id="oldlongitude" value="">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group" id="date_div">
                <div class="col-md-12 pl-sm pr-sm">
                  <label class="control-label" for="date">Date <span class="mandatoryfield">*</span></label>
                  <input id="date" type="text" name="date" class="form-control followupdate" value="<?=date("d/m/Y",strtotime('+'.DEFAULT_FOLLOWUP_DATE.' day'))?> <?php echo $this->general_model->gettime("H:i");?>" readonly>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group" id="employee_div">
                <div class="col-md-12 pl-sm pr-sm">
                  <label class="control-label" for="employee">Assign To </label>
                  <select class="form-control selectpicker" id="employee" name="employee" data-live-search="true" data-size="5">
                    <option value="0">Select  Employee</option>
                    <?php foreach ($employeedata as $_v) { ?>        
                        <option value="<?php echo $_v['id'];?>" 
                        <?php if(isset($child_employee_data) && !in_array($_v['id'],$child_employee_data) && isset($sibling_employee_data) && !in_array($_v['id'],$sibling_employee_data) && $checkrights==1){ echo "disabled"; } ?>>
                        <?php echo ucwords($_v['name']);?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group" id="followuptype_div">
                <div class="col-md-12 pl-sm pr-sm">
                  <label class="control-label" for="followuptype"><?=Follow_Up?> Type <span class="mandatoryfield">*</span></label>
                  <select class="form-control selectpicker" id="followuptype" name="followuptype" data-live-search="true" data-size="5">
                    <option value="0">Select <?=Follow_Up?> Type </option>
                    <?php foreach ($followuptypedata as $fd) {?>        
                        <option value="<?php echo $fd['id'];?>">
                        <?php echo $fd['name'];?></option>
                    <?php } ?>
                  </select>  
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group" id="status_div">    
                <div class="col-md-12 pl-sm pr-sm">
                  <label class="control-label" for="status">Status <span class="mandatoryfield">*</span></label>
                  <select id="status" name="status" class="selectpicker form-control" data-live-search="true" data-size="5">
                    <option value="" >Select Status</option>
                    <?php foreach($followupstatuses as $k=>$fs){ ?>
                    <option value="<?=$fs['id']?>"><?=$fs['name']?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group" id="note_div">
                <div class="col-md-12 pl-sm pr-sm">
                  <label class="control-label" for="followupnote">Note <span class="mandatoryfield">*</span></label>
                  <textarea id="followupnote" class="form-control" name="note"></textarea>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group" id="futurenote_div">
                <div class="col-md-12 pl-sm pr-sm">
                  <label class="control-label" for="futurenote">Future Note</label>
                  <textarea id="futurenote" class="form-control" name="futurenote"></textarea>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div id="reason_div" style="display:none;">
                <div class="col-md-12 pl-sm pr-sm">
                  <label class="control-label" for="reason">Reason <span class="mandatoryfield">*</span></label>
                  <textarea name="reason" id="reason" class="form-control" value=""></textarea>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row">
              <div class="col-md-4">
                <div class="form-group" id="latitude_div">
                  <div class="col-md-12 pl-sm pr-sm">
                    <label class="control-label" for="latitude">Latitude</label>
                    <input type="text" class="form-control" name="latitude" id="latitude">
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group" id="longitude_div">
                  <div class="col-md-12 pl-sm pr-sm">
                    <label class="control-label" for="longitude">Longitude</label>
                    <input type="text" class="form-control" name="longitude" id="longitude">
                  </div>
                </div>
              </div>
              <div class="col-sm-4">
                <div style="margin-top: 42px;">
                  <div class="col-md-12 pl-sm pr-sm">
                    <button type="button" class="form-control" style="width: auto;" onclick="openmodal($('#latitude').val(),$('#longitude').val())"><i class="fa fa-map-marker"></i> Pickup <?=Followup?> Location</button>
                  </div>
                </div>
              </div>
          </div>

          <div class="row">
            <div class="col-sm-12">
              <div class="form-group text-center">
                <div class="col-sm-12">
                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                  <input type="button" onclick="resetdata()" value="RESET" class="btn btn-info btn-raised">
                  <a class="<?=cancellink_class;?>" href="javascript:void(0)" data-dismiss="modal" aria-label="Close" title=<?=cancellink_title?>><?=cancellink_text?></a>
                </div>
              </div>
            </div>
          </div>
        </form>    
      </div>
    </div>
  </div>
</div>
<div id="add_data_Modal" class="modal fade">  
  <div class="modal-dialog" style="width: 500px;">  
    <div class="modal-content">  
      <div class="modal-header"> 
        <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>  
        <h4 class="modal-title" style="float:left;">OTP Verification</h4>  
      </div>  
      <div class="modal-body">  
        <form method="post" id="insert_form"> 
          <input type="hidden" id="fid" name="fid">
          <div class="form-group" id="code_div">
            <label class="control-label" for="code">Enter OTP <span class="mandatoryfield">*</span></label>
            <input type="text" id="code" name="code" class="form-control" placeholder="">
          </div>                           
          <input type="submit" name="update" id="update" value="SUBMIT" class="btn btn-primary btn-raised" />  
          <input type="button" id="resendotp" name="resendotp" value="Resend OTP" class="btn btn-success btn-raised" style="display:block;float:right;">
        </form>  
      </div>                 
    </div>  
  </div>  
</div>  
<div class="modal" id="followupModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
              <h4 class="modal-title" id="post_title">Add <?=Followup?></h4>
            </div>
            <div class="modal-body pt-n">
                <form action="#" id="newfollowupform" class="form-horizontal">
                    <input type="hidden" name="inquiryid" id="inquiryid" value="">
                    <div class="row">
                        <div class="col-md-3">
                          <div class="form-group" id="newfollowupdate_div">
                            <div class="col-md-12 pl-sm pr-sm">
                              <label class="control-label" for="newfollowupdate">Date <span class="mandatoryfield">*</span></label>
                              <input id="newfollowupdate" type="text" name="date" class="form-control followupdate" value="<?=date("d/m/Y",strtotime('+'.DEFAULT_FOLLOWUP_DATE.' day'))?> <?php echo $this->general_model->gettime("h:i");?>" readonly>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="newfollowupemployee_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="newfollowupemployee">Assign To </label>
                                <select class="form-control selectpicker" id="newfollowupemployee" name="employee" data-live-search="true" data-size="5">
                                    <option value="0">Select Employee</option>
                                    <?php foreach ($employeedata1 as $_v) { ?>
                                    <option value="<?php echo $_v['id'];?>" >
                                        <?php echo ucwords($_v['name']);?></option>
                                    <?php } ?>
                                </select>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="newfollowuptype_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="newfollowuptype"><?=Follow_Up?> Type <span class="mandatoryfield">*</span></label>
                                <select class="form-control selectpicker" id="newfollowuptype" name="followuptype" data-live-search="true" data-size="5">
                                    <option value="0">Select <?=Follow_Up?> Type</option>
                                    <?php foreach ($followuptypedata as $fd) { ?>
                                    <option value="<?php echo $fd['id'];?>">
                                        <?php echo $fd['name'];?></option>
                                    <?php } ?>
                                </select>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="newfollowupstatus_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="newfollowupstatus">Status <span class="mandatoryfield">*</span></label>
                                <select id="newfollowupstatus" name="status" class="selectpicker form-control" data-live-search="true" data-size="5">
                                    <option value="">Select Status</option>
                                    <?php foreach($followupstatuses as $k=>$fs){ ?>
                                    <option value="<?=$fs['id']?>"><?=$fs['name']?></option>
                                    <?php } ?>
                                </select>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="newfollowupnote_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="newfollowupnote">Note <span class="mandatoryfield">*</span></label>
                                <textarea id="newfollowupnote" class="form-control" name="note" value=""></textarea>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="newfollowupfuturenote_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="futurenote">Future Note</label>
                                <textarea id="newfollowupfuturenote" class="form-control" name="futurenote" value=""></textarea>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" id="newfollowuplatitude_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="newfollowuplatitude">Latitude</label>
                                <input type="text" class="form-control" name="latitude" id="newfollowuplatitude">
                              </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" id="newfollowuplongitude_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="newfollowuplongitude">Longitude</label>
                                <input type="text" class="form-control" name="longitude" id="newfollowuplongitude">
                              </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                          <div style="margin-top: 42px;">
                            <div class="col-md-12 pl-sm pr-sm">
                              <button type="button" class="form-control" style="width: auto;" onclick="openmodal($('#newfollowuplatitude').val(),$('#newfollowuplongitude').val())"><i class="fa fa-map-marker"></i> Pickup <?=Followup?> Location</button>
                            </div>
                          </div>
                        </div>
                    </div>
                    <input type="hidden" name="memberid" id="memberid" value="">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group  text-center">
                              <div class="col-sm-12">
                                <input type="button" id="submit" onclick="checkfollowupvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                <a class="<?=cancellink_class;?>" href="javascript:void(0)" data-dismiss="modal" aria-label="Close" title=<?=cancellink_title?>><?=cancellink_text?></a>
                              </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="followupModalReschedule">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
              <h4 class="modal-title" id="post_title">Reschedule <?=Followup?></h4>
            </div>
            <div class="modal-body pt-n">
                <form action="#" id="reschedulefollowupform" class="form-horizontal">
                    <input type="hidden" name="rinquiryid" id="rinquiryid" value="">
                    <input type="hidden" name="oldstatusid" id="oldstatusid" value="">
                    <input type="hidden" name="rfollowupid" id="rfollowupid" value="">

                    <div class="row">
                        <div class="col-md-3">
                          <div class="form-group" id="rfollowupdate_div">
                            <div class="col-md-12 pl-sm pr-sm">
                              <label class="control-label" for="rfollowupdate">Date <span class="mandatoryfield">*</span></label>
                              <input id="rfollowupdate" type="text" name="rdate" class="form-control followupdate" placeholder="Date" value="<?=date("d/m/Y",strtotime('+'.DEFAULT_FOLLOWUP_DATE.' day'))?> <?php echo $this->general_model->gettime("h:i");?>" readonly>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="rfollowupemployee_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="rfollowupemployee">Assign To </label>
                                <select class="form-control selectpicker" id="rfollowupemployee" name="remployee" data-live-search="true" data-size="5">
                                    <option value="0">Select Employee</option>
                                    <?php foreach ($employeedata1 as $_v) { ?>
                                    <option value="<?php echo $_v['id'];?>" >
                                        <?php echo ucwords($_v['name']);?></option>
                                    <?php } ?>
                                </select>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="rfollowuptype_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="rfollowuptype"><?=Follow_Up?> Type <span class="mandatoryfield">*</span></label>
                                <select class="form-control selectpicker" id="rfollowuptype" name="rfollowuptype" data-live-search="true" data-size="5">
                                    <option value="0">Select <?=Follow_Up?> Type</option>
                                    <?php foreach ($followuptypedata as $fd) { ?>
                                    <option value="<?php echo $fd['id'];?>">
                                        <?php echo $fd['name'];?></option>
                                    <?php } ?>
                                </select>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="rfollowupstatus_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="rfollowupstatus">Status <span class="mandatoryfield">*</span></label>
                                <select id="rfollowupstatus" name="rstatus" class="selectpicker form-control" data-live-search="true" data-size="5">
                                    <option value="">Select Status</option>
                                    <?php foreach($followupstatuses as $k=>$fs){ ?>
                                    <option value="<?=$fs['id']?>"><?=$fs['name']?></option>
                                    <?php } ?>
                                </select>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="rfollowupnote_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="rfollowupnote">Note <span class="mandatoryfield">*</span></label>
                                <textarea id="rfollowupnote" class="form-control" name="rnote" placeholder="Note" value=""></textarea>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="rfollowupfuturenote_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="rfuturenote">Future Note</label>
                                <textarea id="rfollowupfuturenote" class="form-control" name="rfuturenote" placeholder="Future Note" value=""></textarea>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" id="rfollowuplatitude_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="rfollowuplatitude">Latitude</label>
                                <input type="text" class="form-control" name="rlatitude" id="rfollowuplatitude" placeholder="Latitude">
                              </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" id="rfollowuplongitude_div">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label class="control-label" for="rfollowuplongitude">Longitude</label>
                                <input type="text" class="form-control" name="rlongitude" id="rfollowuplongitude" placeholder="Longitude">
                              </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                          <div style="margin-top: 42px;">
                            <div class="col-md-12 pl-sm pr-sm">
                              <button type="button" class="form-control" style="width: auto;" onclick="openmodal($('#rfollowuplatitude').val(),$('#rfollowuplongitude').val())"><i class="fa fa-map-marker"></i> Pickup <?=Followup?> Location</button>
                            </div>
                          </div>
                        </div>
                    </div>
                    <input type="hidden" name="memberid" id="rfmemberid" value="">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group text-center">
                              <div class="col-sm-12">
                                <input type="button" id="submit" onclick="checkfollowupvalidation1()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                <a class="<?=cancellink_class;?>" href="javascript:void(0)" data-dismiss="modal" aria-label="Close" title=<?=cancellink_title?>><?=cancellink_text?></a>
                              </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
<div id="locationModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" style="width:1000px;">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
              <h4 class="modal-title">Select Location</h4>
            </div>

            <div class="modal-body">
                <input id="pac-input" class="pac-controls" type="text" placeholder="Search Place">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>

<script>
  function openmodal(latitude,longitude){
    latitude = latitude || '';
    longitude = longitude || '';
    newLocation(latitude,longitude);
    
    $('#pac-input').val('');
    $('#locationModal').modal('show');
  }
  // Initialize and add the map
  var markers = [];
  var map;
  function initAutocomplete() {
      map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: <?=DEFAULT_LAT?>, lng: <?=DEFAULT_LNG?>},
        zoom: 6,
        mapTypeId: 'roadmap',
        disableDefaultUI: true,
        streetViewControl: false,
      });

      // Create the search box and link it to the UI element.
      var input = document.getElementById('pac-input');
      var searchBox = new google.maps.places.SearchBox(input);
      map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

      // Bias the SearchBox results towards current map's viewport.
      map.addListener('bounds_changed', function() {
        searchBox.setBounds(map.getBounds());
      });

      google.maps.event.addListener(map, 'click', function(event) {
          deleteMarkers();
          placeMarker(event.latLng);
          $('#latitude').val(event.latLng.lat());
          $('#longitude').val(event.latLng.lng());
      });

      // Listen for the event fired when the user selects a prediction and retrieve
      // more details for that place.
      searchBox.addListener('places_changed', function() {
        var places = searchBox.getPlaces();

        if (places.length == 0) {
          return;
        }

        // Clear out the old markers.
        markers.forEach(function(marker) {
          marker.setMap(null);
        });
        markers = [];

        // For each place, get the icon, name and location.
        var bounds = new google.maps.LatLngBounds();
        places.forEach(function(place) {
          if (!place.geometry) {
            console.log("Returned place contains no geometry");
            return;
          }
          var icon = {
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(25, 25)
          };

          // Create a marker for each place.
          markers.push(new google.maps.Marker({
            map: map,
            icon: icon,
            title: place.name,
            position: place.geometry.location
          }));

          if (place.geometry.viewport) {
            // Only geocodes have viewport.
            bounds.union(place.geometry.viewport);
          } else {
            bounds.extend(place.geometry.location);
          }
        });
        map.fitBounds(bounds);
      });
  }
  function newLocation(newLat,newLng){

      if(newLat!='' && newLng!=''){
          marker = new google.maps.Marker({
                  position: new google.maps.LatLng( newLat,newLng),
                  map: map,
              });
          markers.push(marker);

          // To add the marker to the map, call setMap();
          marker.setMap(map);

          map.setCenter({lat : newLat,lng : newLng});
          map.setZoom(6);
      }else{
          deleteMarkers();
          map.setCenter({lat : <?=DEFAULT_LAT?>,lng : <?=DEFAULT_LNG?>});
          map.setZoom(4);
      }
  }
  function placeMarker(location) {        
      marker = new google.maps.Marker({
          position: location, 
          map: map
      });
      markers.push(marker);
      
  }
  // Sets the map on all markers in the array.
  function setMapOnAll(map) {
      for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
      }
  }
  // Removes the markers from the map, but keeps them in the array.
  function clearMarkers() {
      setMapOnAll(null);
  }
  // Deletes all markers in the array by removing references to them.
  function deleteMarkers() {
      clearMarkers();
      markers = [];
  }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=MAP_KEY?>&libraries=places&callback=initAutocomplete"></script>