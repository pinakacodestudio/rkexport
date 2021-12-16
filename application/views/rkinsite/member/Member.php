<div class="page-content">
    <div class="page-heading">     
      <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
             <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
								<div class="panel-heading filter-panel border-filter-heading" display-type="<?php if(isset($panelcollapsed) && $panelcollapsed==1){ echo "0"; } else{ echo "1";}?>">
									<h2><?=APPLY_FILTER?></h2>
									<div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
								</div>
								<div class="panel-body panelcollapse pt-n" style="display: none;">
                  <form action="#" id="memberform" class="form-horizontal">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group" id="channelid_div">
                          <div class="col-sm-12 pr-xs">
                            <label for="channelid" class="control-label">Select Channel</label>
                            <select id="channelid" name="channelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" title="All Channel" data-live-search="true" data-actions-box="true" multiple>
                              <?php foreach($channeldata as $cd){ 
                                
                                  $selected = ""; 
                                  if(!empty($this->session->userdata(base_url().'CHANNEL'))){ 
                                    $arrChannel = explode(",",$this->session->userdata(base_url().'CHANNEL'));
                                    if(in_array($cd['id'], $arrChannel)){ 
                                      $selected = "selected"; 
                                    } 
                                  }else{
                                    if(isset($ChannelId) && $ChannelId!=""){
                                      $arrChannel = explode(",",$ChannelId);
                                      if(in_array($cd['id'], $arrChannel)){ 
                                        $selected = "selected"; 
                                      }
                                    }
                                  }
                                ?>
                              <option value="<?php echo $cd['id']; ?>" <?php echo $selected; ?>><?php echo $cd['name']; ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-5">
                        <div class="form-group">
                          <div class="col-sm-12 pl-xs pr-xs">
                            <label for="startdate" class="control-label">Date</label>
                            <div class="input-daterange input-group" id="datepicker-range">
                              <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php if(isset($startdate) && $startdate!=''){ echo $this->general_model->displaydate($startdate); }else{ echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 year"))); } ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="input-group-addon">to</span>
                              <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php if(isset($enddate) && $enddate!=''){ echo $this->general_model->displaydate($enddate); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" placeholder="End Date" title="End Date" readonly/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group" style="margin-top: 37px;">
                          <div class="col-sm-12 pl-xs">
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
                <div class="col-md-4 pr-n">
                  <div class="panel-ctrls panel-tbl"></div>
                </div>
                <div class="col-md-3 pr-n text-right">
                  <div class="form-group mt-n" id="status_div">
                    <div class="col-sm-12">
                      <select id="status" name="status" class="selectpicker form-control" data-live-search="true">
                        <option value="">Select Status</option>
                        <option value="1">Enable</option>
                        <option value="0">Disable</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-5 form-group pr-n pl-n" style="text-align: right;">
                  <?php if (strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                    <a class="btn btn-primary btn-raised" href="javascript:void(0)" title="UPDATE STATUS" onclick="updatememberstatus()">UPDATE</a>
                  <?php } if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                    <a class="<?=addbtn_class;?>" href="<?php echo ADMIN_URL; ?>member/member-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php } if (in_array("import-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                    <a class="<?=importbtn_class;?>" href="javascript:void(0)" onclick="importmember()" title="<?=importbtn_title?>"><?=importbtn_text;?></a>
                  <?php } if (in_array("upload-image",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                    <a class="<?=uploadproductimagebtn_class;?>" href="javascript:void(0)" onclick="uploadprofileimage()" title="<?=uploadproductimagebtn_title?>"><?=uploadproductimagebtn_text;?></a>
                  <?php } /*if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>member/check-member-use','Userrole','<?php echo ADMIN_URL; ?>member/delete-mul-member')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } */ ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="membertable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width5">Sr.No.</th>
                      <th><?=Member_label?> Name</th>
                      <th>Assign To <?=Member_label?></th>
                      <th>Seller Name</th>
                      <th class="width8">Contact Details</th>
                      <th class="width5 text-right">Cart Count</th>
                      <th class="text-right">Opening Balance</th>
                      <th>Entry Date</th>
                      <th class="width10">Action</th>
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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width:425px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
        <h4 class="modal-title">QR Code</h4>
      </div>
      <div class="modal-body" style="float: left;width: 100%;">
          <div class="col-md-12" id="qrcodeimage"></div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>
<div class="modal fade" id="openingbalanceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
        <h4 class="modal-title">Opening Balance</h4>
      </div>
      <div class="modal-body" style="float: left;width: 100%;">
        <form class="form-horizontal" id="openingbalanceform">
          <input type="hidden" id="openingbalanceid" name="openingbalanceid">
          <input type="hidden" id="memberid" name="memberid">
          <div class="form-group" id="balancedate_div">
            <label class="col-sm-4 control-label">Opening Balance Date <span class="mandatoryfield">*</span></label>
            <div class="col-sm-8">
              <input id="balancedate" type="text" name="balancedate" class="form-control" readonly >
            </div>
          </div>
          <div class="form-group" id="balance_div">
            <label class="col-sm-4 control-label">Opening Balance <span class="mandatoryfield">*</span></label>
            <div class="col-sm-8">
              <input id="balance" type="text" name="balance" class="form-control" onkeypress="return decimal_number_validation(event,this.value)">
            </div>
          </div>
          <div class="form-group">
              <div class="col-sm-offset-4 col-sm-8">
                  <div class="input-group">
                  <input type="button" class="btn btn-primary btn-raised" onclick="checkopeningbalancevalidation()" value="Submit">
                  <button class="btn btn-primary btn-raised" data-dismiss="modal">Close<div class="ripple-container"></div></button>
              </div>
              </div>
          </div>
        </form>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>
<div class="modal fade" id="myMemberImportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
      <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
          <h3 class="modal-title">Import <?=Member_label?></h3>
      </div>
      <div class="modal-body">
          <form class="form-horizontal" id="memberimportform">
            <input type="hidden" id="isvalidmemberimportfile" value="0">
          <div class="form-group" id="attachment_div">
              <label class="col-sm-4 control-label">Select Excel File <span class="mandatoryfield">*</span></label>
              <div class="col-sm-8">
                  <div class="input-group">
                  <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                    <span class="btn btn-primary btn-raised btn-file">Browse...
                      <input type="file" name="attachment" id="attachment" accept=".xls,.xlsx" >
                    </span>
                  </span>
                  <input type="text" name="Filetext" id="Filetext" class="form-control" value="" readonly>
              </div>
              </div>
          </div>
          <div class="form-group">
              <label class="col-sm-4 control-label">Download Format</label>
              <div class="col-sm-8">
                  <div class="input-group">
                  <a href="<?=IMPORT_FILE?>import-member.xls" class="btn btn-default btn-raised" download="import-member.xls"><i class="fa fa-download"></i> Download File<div class="ripple-container"></div></a>
              </div>
              </div>
          </div>
          <div class="form-group">
              <div class="col-sm-offset-4 col-sm-8">
                  <div class="input-group">
                  <input type="button" class="btn btn-primary btn-raised" onclick="checkimportmembervalidation()" value="Import">
                  <button class="btn btn-danger btn-raised" data-dismiss="modal">Close<div class="ripple-container"></div></button>
              </div>
              </div>
          </div>
          </form>
      </div>
      
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
<div class="modal fade" id="myProfileImageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h3 class="modal-title">Upload Profile Images</h3>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" id="imageuploadform">
          <div class="form-group" id="zipfile_div">
            <input type="hidden" id="validzipfile" value="0">
            <input type="hidden" id="validzipfilesize" value="0">
            <label for="Zipfiletext" class="col-sm-3 control-label">Select Zip File <span class="mandatoryfield">*</span></label>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                    <span class="btn btn-primary btn-raised btn-file">Browse...
                    <input type="file" name="zipfile" id="zipfile" accept=".zip">
                  </span>
                  </span>
                <input type="text" readonly="" id="Zipfiletext" class="form-control" value="">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-8">
                <div class="input-group">
                  <input type="button" class="btn btn-primary btn-raised" onclick="checkvalidationforprofileimage()" value="Upload">
                  <button class="btn btn-danger btn-raised" data-dismiss="modal">Close<div class="ripple-container"></div></button>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer"> </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>