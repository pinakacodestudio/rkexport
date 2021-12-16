<div class="page-content">
<div class="page-heading">     
      <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>
    <div class="container-fluid">
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
             <div class="panel panel-default border-panel mb-md" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
								<div class="panel-heading filter-panel border-filter-heading">
									<h2><?=APPLY_FILTER?></h2>
									<div class="panel-ctrls" data-actions-container style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
								</div>
								<div class="panel-body panelcollapse pt-n" style="display: none;">
                  <form action="#" id="vehicleform" class="form-horizontal">
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-sm-12 pr-sm">
                              <label for="insurancecompany" class="control-label">Insurance Company</label>
                              <select id="insurancecompany" name="insurancecompany" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                <option value="">All Insurance Company</option>
                                <?php foreach($insurancecompanydata as $ic){ ?>
                                <option value="<?php echo $ic['companyname']; ?>"><?php echo $ic['companyname']; ?></option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group SetTopMarginOnButton">
                            <div class="col-sm-12 pl-sm pr-sm">
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
                
                <div class="col-md-6 ResponsivePaddingNone">
                  <div class="panel-ctrls panel-tbl"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                  <?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){?>
                    <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>insurance/add-insurance/<?=$this->uri->segment(3);?>" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php } if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                    <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?=ADMIN_URL?>insurance/check-insurance-use','Insurance','<?php echo ADMIN_URL; ?>insurance/delete-mul-insurance')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportToExcelInsurance()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                  <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exportToPDFInsurance()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                  <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printInsuranceDetails()" title="<?=printbtn_title?>"><?=printbtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="insurancetable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width5">Sr. No.</th>
                      <th>Vehicle Name</th>
                      <th>Insurance Company</th>
                      <th>Policy No.</th>
                      <th>Register Date</th>
                      <th>Due Date</th>
                      <th>Amount (<?=CURRENCY_CODE?>)</th>
                      <th>Entry Date</th>
                      <th class="width12">Action</th>
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