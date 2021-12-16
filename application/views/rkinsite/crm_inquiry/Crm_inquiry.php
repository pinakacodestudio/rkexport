<?php
 $arrSessionDetails = $this->session->userdata;
 $inquiryfilter = explode(',',INQUIRY_FILTER);
 
 $inquirymemberleadsource = (!empty($arrSessionDetails['inquirymemberleadsource']))?explode(",",$arrSessionDetails['inquirymemberleadsource']):'';
 $inquirymemberindustry = (!empty($arrSessionDetails['inquirymemberindustry']))?explode(",",$arrSessionDetails['inquirymemberindustry']):'';
 $inquirymemberstatus = (!empty($arrSessionDetails['inquirymemberstatus']))?explode(",",$arrSessionDetails['inquirymemberstatus']):'';
 $inquiryproductfilter = (!empty($arrSessionDetails['inquiryproductfilter']))?explode(",",$arrSessionDetails['inquiryproductfilter']):'';
?>
<script>
  loginuser = "<?php echo $arrSessionDetails[base_url().'ADMINID']; ?>";
  displaytime = "<?php echo $this->general_model->gettime("H:i");?>";
  var FOLLOWUP_DEFAULT_STATUS = "<?=FOLLOWUP_DEFAULT_STATUS?>";
  var DEFAULT_FOLLOWUP_TYPE = "<?=DEFAULT_FOLLOWUP_TYPE?>";
  var FOLLOWUP_DATE_TYPE = "<?=FOLLOWUP_DATE_TYPE?>"; 
  var DEFAULT_FOLLOWUP_DATE= '<?=(DEFAULT_FOLLOWUP_DATE!="")?DEFAULT_FOLLOWUP_DATE:0 ?>';

  var inquirymemberleadsource= '<?=(isset($arrSessionDetails['inquirymemberleadsource']))?$arrSessionDetails['inquirymemberleadsource']:'' ?>';
  var inquirymemberindustry= '<?=(isset($arrSessionDetails['inquirymemberindustry']))?$arrSessionDetails['inquirymemberindustry']:'' ?>';
  var inquirymemberstatus= '<?=(isset($arrSessionDetails['inquirymemberstatus']))?$arrSessionDetails['inquirymemberstatus']:'' ?>';
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
                <div class="panel-heading filter-panel border-filter-heading inqstatuspnl" display-type="<?php if(isset($arrSessionDetails["inquirystatuscollapse"])){ if($arrSessionDetails["inquirystatuscollapse"]=="0"){ echo '1'; }else{ echo '0'; }  }else{ echo "0"; } ?>">
                    <h2>Status Wise Inquiry</h2>
                    <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                </div>
                <div class="panel-body panelcollapse pt-n" style="display: none;">
                  <form action="#" class="form-horizontal">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="status" class="control-label">Employee</label>
                            <select id="filterstatusemployee" name="employee" class="selectpicker form-control" data-select-on-tab="true" data-size="8" data-live-search="true">
                              <option value="0">Select Employee</option>
                              <?php foreach($employeedata as $row){ ?>
                                <option value="<?php echo $row['id']; ?>" <?php if(!empty($arrSessionDetails["inquiryemployeefilter"]) && $arrSessionDetails["inquiryemployeefilter"]==$row['id']){ echo 'selected'; } else if($row['id']==$arrSessionDetails[base_url().'ADMINID']){ echo 'selected'; } ?>><?php echo ucwords($row['name']); ?></option> 
                              <?php } ?>  
                              <option value="-1" <?php if(!empty($arrSessionDetails["inquiryemployeefilter"]) && $arrSessionDetails["inquiryemployeefilter"]=="-1"){ 
                                echo "selected"; 
                              } ?>>All</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="startdate" class="control-label">Date</label>
                            <div class="input-daterange input-group" id="datepicker-range">
                              <input type="text" class="input-small form-control" name="startdate" id="startdate1" value="<?php if(!empty($arrSessionDetails["inquirystatusfromdatefilter"])){ echo $arrSessionDetails["inquirystatusfromdatefilter"]; }else{ echo date("d/m/Y",strtotime("-1 month")); } ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="input-group-addon">to</span>
                              <input type="text" class="input-small form-control" name="enddate" id="enddate1" value="<?php if(!empty($arrSessionDetails["inquirystatustodatefilter"])){ echo $arrSessionDetails["inquirystatustodatefilter"]; }else{ echo date("d/m/Y"); } ?>" placeholder="End Date" title="End Date" readonly/>
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
                    </div> 
                    <div class="row">
                      <div class="col-md-12">
                      <?php foreach ($inquirystatusdata as $k=>$cs) { ?>
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
                                        <a class="dropdown-item statusdd<?=$cs['id']?>" id="statusdd<?=$cs['id']?>1" href="#" onclick="getstatuscount(1,'<?=$cs['id']?>','<?=$cs['id']?>')" data-toggle="popover1" data-content="<?php echo date("d/m/Y",strtotime("-1 month"))." - ".date("d/m/Y") ?>">1 Month</a>
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
          <?php if(!empty(INQUIRY_FILTER)){?>
          <div class="col-md-12">
            <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                <div class="panel-heading filter-panel border-filter-heading inqpnl" display-type="<?php if(isset($arrSessionDetails["inquirycollapse"])){ if($arrSessionDetails["inquirycollapse"]=="0"){ echo '1'; }else{ echo '0'; }  }else{ echo "0"; } ?>">
                    <h2><?=APPLY_FILTER?></h2>
                    <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                </div>
                <div class="panel-body panelcollapse pt-n" style="display: none;">
                  <form action="#" class="form-horizontal">
                    <div class="row">
                      
                      <div class="col-md-3 pr-sm" style="display:<?=(!in_array('1',$inquiryfilter))?'none':'block'?>">
                        <div class="form-group" id="members_div"> 
                          <div class="col-sm-12">
                            <label for="status" class="control-label"><?=Member_label?></label>
                            <input class="js-data-example-ajax mt-sm" id="filtermember" placeholder="Select <?=Member_label?>" style="width:100%;" value="<?php if(!empty($arrSessionDetails['inquirymemberfilter'])){
                              echo $arrSessionDetails['memberfilter'];
                            } ?>" data-text="<?php if($membername!=""){ echo $membername;  }?>">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 pl-sm pr-sm" style="display:<?=(!in_array('2',$inquiryfilter))?'none':'block'?>">
                        <div class="form-group" id="filteremployee_div">
                          <div class="col-sm-12">
                            <label for="status" class="control-label">Employee</label>
                            <select id="filteremployee" name="employee" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                              <option value="0">Select Employee</option>
                              <?php foreach($employeedata as $row){ ?>
                                <option value="<?php echo $row['id']; ?>" <?php if(!empty($arrSessionDetails["inquiryemployeefilter"]) && $arrSessionDetails["inquiryemployeefilter"]==$row['id']){ echo 'selected'; } else if($row['id']==$arrSessionDetails[base_url().'ADMINID']){ echo 'selected'; } ?>><?php echo ucwords($row['name']); ?></option> 
                              <?php } ?> 
                              <option value="-1" <?php if(!empty($arrSessionDetails['inquiryemployeefilter']) && $arrSessionDetails['inquiryemployeefilter']=='-1'){ 
                                                echo "selected"; 
                                } ?>>All</option> 
                            </select>
                            <div class="checkbox col-md-6 " style="text-align: left;">
                                <input type="checkbox" name="direct" id="direct" style="display: none;" value="1" <?php if(isset($arrSessionDetails["directinquirytype"])){ if($arrSessionDetails["directinquirytype"]==1){ echo 'checked'; }}else{ echo 'checked';}?>>
                                <label class="ml7" for="direct" style="font-size: 14px;padding-left: 10px !important;">Direct</label>
                            </div>
                            <div class="checkbox col-md-6 " style="text-align: left;">
                              <input type="checkbox" name="indirect" style="display: none;" id="indirect" value="2" <?php if(!empty($arrSessionDetails["indirectinquirytype"]) && $arrSessionDetails["indirectinquirytype"]==1){ echo 'checked'; } ?>>
                              <label for="indirect" style="font-size: 14px;padding-left: 10px !important;">Indirect</label>
                            </div>
                           </div>
                        </div>
                      </div>
                      <div class="col-md-3 pl-sm pr-sm" style="display:<?=(!in_array('3',$inquiryfilter))?'none':'block'?>">
                        <div class="form-group" id="filterstatus_div">
                          <div class="col-sm-12">
                            <label for="status" class="control-label">Status</label>
                              <select id="filterstatus" name="filterstatus" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                  <option value="">Select Status</option>
                                  <?php foreach($inquirystatusdata as $row){ ?>
                                    <option value="<?php echo $row['id']; ?>" <?php if(!empty($arrSessionDetails["inquirystatusfilter"]) && $arrSessionDetails["inquirystatusfilter"]==$row['id']){ echo "selected"; } ?>><?php echo $row['name']; ?></option> 
                                  <?php } ?>  
                              </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 pl-sm" style="display:<?=(!in_array('4',$inquiryfilter))?'none':'block'?>">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="startdate" class="control-label">Date</label>
                            <div class="input-daterange input-group" id="datepicker-range1">
                              <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php if(!empty($arrSessionDetails["inquiryfromdatefilter"])){ echo $arrSessionDetails["inquiryfromdatefilter"]; }else{ echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); } ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="input-group-addon">to</span>
                              <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php if(!empty($arrSessionDetails["inquirytodatefilter"])){ echo $arrSessionDetails["inquirytodatefilter"]; }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" placeholder="End Date" title="End Date" readonly/>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3 pr-sm" style="display:<?=(!in_array('5',$inquiryfilter))?'none':'block'?>">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label  class="control-label">Inquiry Lead Source</label>
                            <select id="filterinquiryleadsource" name="filterinquiryleadsource[]" class="form-control selectpicker"  data-live-search="true" data-select-on-tab="true" data-size="8" multiple data-actions-box="true" title="Select Inquiry Lead Source">
                                <?php foreach($leadsourcedata as $row){ ?>
                                  <option value="<?php echo $row['id']; ?>" <?php if(!empty($inquirymemberleadsource) && in_array($row['id'],$inquirymemberleadsource)){ echo "selected"; } ?>><?php echo $row['name']; ?></option> 
                                <?php } ?>  
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 pl-sm pr-sm" style="display:<?=(!in_array('6',$inquiryfilter))?'none':'block'?>">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label  class="control-label"><?=Member_label?> Industry</label>
                            <select id="filtermemberindustry" name="filtermemberindustry[]" class="form-control selectpicker"  data-live-search="true" data-select-on-tab="true" data-size="8" multiple data-actions-box="true" title="Select <?=Member_label?> Industry">
                                <?php foreach($industrycategorydata as $row){ ?>
                                  <option value="<?php echo $row['id']; ?>" <?php if(!empty($inquirymemberindustry) && in_array($row['id'],$inquirymemberindustry)){ echo "selected"; } ?>><?php echo $row['name']; ?></option> 
                                <?php } ?>  
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 pl-sm pr-sm" style="display:<?=(!in_array('7',$inquiryfilter))?'none':'block'?>">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label  class="control-label"><?=Member_label?> Status</label>
                            <select id="filtermemberstatus" name="filtermemberstatus[]" class="form-control selectpicker" data-live-search="true" data-select-on-tab="true" data-size="8" multiple data-actions-box="true" title="Select <?=Member_label?> Status">
                                <?php foreach($memberstatusesdata as $row){ ?>
                                  <option value="<?php echo $row['id']; ?>" <?php if(!empty($inquirymemberstatus) && in_array($row['id'],$inquirymemberstatus)){ echo "selected"; } ?>><?php echo $row['name']; ?></option> 
                                <?php } ?>  
                              </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 pl-sm" style="display:<?=(!in_array('8',$inquiryfilter))?'none':'block'?>">
                        <div class="form-group" id="filterproduct_div">
                          <div class="col-sm-12">
                            <label  class="control-label">Product</label>
                            <select id="filterproduct" name="filterproduct" class="form-control selectpicker" data-live-search="true" data-select-on-tab="true" data-size="8" multiple data-actions-box="true" title="Select Product">
                                <?php foreach($inquiryproductdata as $product){ 
                                  $productname = str_replace("'","&apos;",$product['name']);
                                  if(DROPDOWN_PRODUCT_LIST==1){ ?>

                                      <option value="<?=$product['id']?>" <?php if(!empty($inquiryproductfilter) && in_array($row['id'],$inquiryproductfilter)){ echo "selected"; } ?>><?=$productname?></option>

                                  <?php }else{

                                      if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                          $img = $product['image'];
                                      }else{
                                          $img = PRODUCTDEFAULTIMAGE;
                                      }
                                      ?>

                                      <option data-content="<?php if(!empty($product['image'])){?><img src='<?=PRODUCT.$img?>' style='width:40px'> <?php } echo $productname; ?> " value="<?php echo $product['id']; ?>" <?php if(!empty($inquiryproductfilter) && in_array($row['id'],$inquiryproductfilter)){ echo "selected"; } ?>><?php echo $productname; ?></option>
                                  
                                  <?php } ?>
                                <?php } ?>  
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group" >
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
          <?php } ?>
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-heading">
                <div class="col-md-6">
                  <div class="panel-ctrls inquirytabel"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                  <?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                    <a class="<?=addbtn_class;?>" href="<?php echo ADMIN_URL; ?>crm-inquiry/add-crm-inquiry" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php } 
                  if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                    <a class="<?=addbtn_class;?>" href="javascript:void(0)"  onclick="transferinquiry()"  ><?=transferbtn_text;?> Transfer Inquiry</a>
                  <?php } if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>crm-inquiry/check-inquiry-use','CRM <?=Inquiry?>','<?php echo ADMIN_URL; ?>crm-inquiry/delete-mul-inquiry')"" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportinquiry()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="inquirytabel" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th>Date</th>
                      <th>Company</th>
                      <th><?=Member_label?></th>
                      <th>Mobile</th>
                      <th>Assigned To</th>
                      <th>Product</th>
                      <th>Status</th>
                      <th>Action</th>
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
              <div class="panel-footer inquirytabel"></div>
            </div>
          </div>
        </div>
      </div>
   
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
<!-- #Add Fllowup start -->
<div class="modal" id="myModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
        <h4 class="modal-title" id="post_title">Add <?=Followup?></h4>
      </div>
      <div class="modal-body pt-n">
        <form action="#" id="followupform" class="form-horizontal">
          <input type="hidden" name="inquiryid" id="inquiryid" value="">       
          <input type="hidden" name="memberid" id="rfmemberid" value="">
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
                        <option value="<?php echo $_v['id'];?>" <?php if(($_v['id']==$arrSessionDetails[base_url().'ADMINID'])){echo "selected";} ?>
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
                        <option value="<?php echo $fd['id'];?>" <?php if(DEFAULT_FOLLOWUP_TYPE!="" && DEFAULT_FOLLOWUP_TYPE==$fd['id']){ echo 'selected'; } ?>>
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
                    <?php foreach($followupstatusesdata as $k=>$fs){ ?>
                    <option value="<?=$fs['id']?>" <?php if(FOLLOWUP_DEFAULT_STATUS!="" && FOLLOWUP_DEFAULT_STATUS==$fs['id']){ echo 'selected'; } ?>><?=$fs['name']?></option>
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
                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
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
<!-- #Add Fllowup End -->
<!-- # Transfer start -->
<div class="modal" id="myModal2" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px; margin-left: 91px;">

            <div class="modal-header" style="height: 59px;">
              <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                <h4 class="modal-title transfertitle" id="post_title">Transfer</h4>
            </div>

            <div class="modal-body">
                <form action="#" id="inquiryform" class="form-horizontal">
                    <input type="hidden" name="inquiryid" id="inquiryid1" value="">
                    <input type="hidden" name="oldassignto" id="oldassignto" value="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row" id="inquiryassignto_div">
                                <label class="col-md-3 col-form-label mt" for="inquiryassignto">Assign To  <span class="mandatoryfield">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control selectpicker" id="inquiryassignto" name="employee" data-live-search="true" data-size="5" >
                                        <option value="0">Select Employee</option>
                                        <?php foreach($assigntoemployee_data as $row){ ?>
                                            <option value="<?php echo $row['id']; ?>" ><?php echo ucwords($row['username']); ?></option> 
                                        <?php } ?>                                         
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" id="reason1_div" style="display:none;">
                                <label class="col-md-3 col-form-label" for="reason1">Reason  <span class="mandatoryfield">*</span></label>
                                <div class="col-md-8">
                                    <textarea name="reason" id="reason1" class="form-control" rows="5" value="" placeholder="Reason"></textarea>
                                </div>
                            </div>
                            <div class="form-group row" id="assignmember_div">
                                <label class="col-md-3 control-label"></label>
                                <div class="col-md-8">
                                    <div class="checkbox">
                                        <input type="checkbox" name="assignmember" id="assignmember" style="display: none;" value="1" checked>
                                        <label for="assignmember" style="padding-left: 5px!important;margin-top: 10px;">Assign <?=Member_label?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label"></label>
                                <div class="col-md-8 ">
                                    <input type="button" id="submit" onclick="checkvalidation1()" name="submit" value="EDIT" class="btn btn-success btn-raised transferbutton">
                                </div>
                            </div>
                        </div>
                        
                    </div>

                   <!--  <div class="row">
                        <div class="col-sm-9">
                            <div class="form-group">
                                <input type="button" id="submit" onclick="checkvalidation1()" name="submit" value="EDIT" class="btn btn-success btn-raised">
                            </div>
                        </div>
                    </div> -->

                </form>
            </div>

        </div>
    </div>
</div>
<!-- # Transfer End -->
<!-- # Inquiry start -->
<div id="followupModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-xl">
      <div class="modal-content" style="width: 158%;margin-left: -157px;">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
              <h4 class="modal-title">Inquiry Followup</h4>
          </div>
          <div class="modal-body">
            <div class="row">
                  <div class="col-md-6">
                  <table class="table table-responsive-sm" cellspacing="0" width="100%">
                      <tbody>
                      <tr>
                          <th width="26%" style="border-top: unset;">Company Name</th>
                          <th width="1%" style="border-top: unset;">:</th>
                          <td style="border-top: unset;"><span id="companyname"></span></td>
                      </tr>
                      <tr>
                          <th width="26%" style="border-top: unset;">Mobile</th>
                          <th width="1%" style="border-top: unset;">:</th>
                          <td style="border-top: unset;"><span id="mobile"></span></td>
                      </tr>
                      </tbody>
                  </table>
                  </div>
                  <div class="col-md-6">
                  <table class="table table-responsive-sm" cellspacing="0" width="100%">
                      <tbody>
                      <tr>
                          <th width="27%" style="border-top: unset;"><?=Member_label?> Name</th>
                          <th width="1%" style="border-top: unset;">:</th>
                          <td style="border-top: unset;"><span id="membername"></span></td>
                      </tr>
                      <tr>
                          <th width="27%" style="border-top: unset;">Email</th>
                          <th width="1%" style="border-top: unset;">:</th>
                          <td style="border-top: unset;"><span id="email"></span></td>
                      </tr>
                      </tbody>
                  </table>
                  </div>
              </div>
              <table id="followuptbl" class="table table-striped table-bordered table-responsive-sm" cellspacing="0" width="100%">
                  <thead>
                      <tr>
                          <th width="1%">No.</th>
                          <th>Notes</th>
                          <th>Future Notes</th>
                          <th>Assign To</th>
                          <th>Type</th>
                          <th>Date</th>
                          <th>Status</th>
                          <th>Action</th>
                      </tr>
                  </thead>
                  <tbody></tbody>
              </table>
          </div>
      </div>
  </div>
</div>
<!-- # Inquiry End -->
<!-- # Location Modal Start -->
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
<!-- # Location Modal End -->
<script>
  function openmodal(type,latitude,longitude){
      latitude = latitude || '';
      longitude = longitude || '';
      newLocation(latitude,longitude);
      $('#latlongtype').val(type);
      $('#pac-input').val('rajkot');
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
          var latlongtype = $('#latlongtype').val();
          if(latlongtype==2){
              $('#followuplatitude').val(event.latLng.lat());
              $('#followuplongitude').val(event.latLng.lng());
          }else{
              $('#latitude').val(event.latLng.lat());
              $('#longitude').val(event.latLng.lng());
          }
          
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
          map.setZoom(14);
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
      
