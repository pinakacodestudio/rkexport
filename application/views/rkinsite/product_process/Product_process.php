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
                  <form action="#" id="memberform" class="form-horizontal">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="col-sm-12 pr-sm">
                            <label for="startdate" class="control-label">Transaction Date</label>
                            <div class="input-daterange input-group" id="datepicker-range">
                              <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="input-group-addon">to</span>
                              <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-2">
                        <div class="form-group">
                          <div class="col-sm-12 pl-sm pr-sm">
                              <label for="processid" class="control-label">Process Group</label>
                              <select id="processgroupid" name="processgroupid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                <option value="0">All Process Group</option>
                                <?php if(!empty($processgroupdata)){ 
                                  foreach($processgroupdata as $processgroup){ ?>
                                    <option value="<?php echo $processgroup['id']; ?>"><?php echo ucwords($processgroup['name']); ?></option>
                                <?php } } ?>
                              </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-2">
                        <div class="form-group">
                          <div class="col-sm-12 pl-sm pr-sm">
                              <label for="processid" class="control-label">Process</label>
                              <select id="processid" name="processid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                <option value="0">All Process</option>
                                <?php if(!empty($processdata)){ foreach($processdata as $process){ ?>
                                <option value="<?php echo $process['id']; ?>"><?php echo ucwords($process['name']); ?></option>
                                <?php }} ?>
                              </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-2">
                        <div class="form-group">
                          <div class="col-sm-12 pl-sm pr-sm">
                              <label for="finalproductid" class="control-label">Final Product</label>
                              <select id="finalproductid" name="finalproductid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                <option value="0">All Final Product</option>
                                <?php if(!empty($finalproductdata)){ foreach($finalproductdata as $product){ 
                                  $productname = str_replace("'","&apos;",$product['name']);
                                  if(DROPDOWN_PRODUCT_LIST==0){ ?>
  
                                    <option value="<?php echo $product['id']; ?>"><?php echo $productname; ?></option>
  
                                  <?php }else{
  
                                    if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                      $img = $product['image'];
                                    }else{
                                      $img = PRODUCTDEFAULTIMAGE;
                                    }
                                    ?>
  
                                    <option data-content="<?php if(!empty($product['image'])){?><img src='<?=PRODUCT.$img?>' style='width:40px'> <?php } echo $productname; ?> "  value="<?php echo $product['id']; ?>"><?php echo $productname; ?></option>
                                    
                                  <?php } ?>
                                <?php }} ?>
                              </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-2">
                        <div class="form-group">
                          <div class="col-sm-12 pl-sm">
                              <label for="processstatus" class="control-label">Process Status</label>
                              <select id="processstatus" name="processstatus" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                <option value="-1">All Status</option>
                               <!--  <option value="0">Hold</option> -->
                                <option value="0">Running</option>
                                <option value="2">Partially</option>
                                <option value="1">Completed</option>
                              </select>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-2">
                        <div class="form-group">
                          <div class="col-sm-12 pr-sm">
                              <label for="processtype" class="control-label">Process Type</label>
                              <select id="processtype" name="processtype" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                <option value="-1">All Type</option>
                                <option value="1">IN</option>
                                <option value="0">OUT</option>
                              </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-2">
                        <div class="form-group">
                          <div class="col-sm-12 pl-sm pr-sm">
                              <label for="processedby" class="control-label">Processed By</label>
                              <select id="processedby" name="processedby" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                <option value="-1">All</option>
                                <option value="0">In-House Emp</option>
                                <option value="1">Vendor List</option>
                              </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <div class="col-sm-12 pl-sm pr-sm">
                              <label for="vendorid" class="control-label">Vendor</label>
                              <select id="vendorid" name="vendorid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                <option value="0">All Vendor</option>
                                <?php if(!empty($vendordata)){ foreach($vendordata as $vendor){ ?>
                                <option value="<?php echo $vendor['id']; ?>"><?php echo ucwords($vendor['name']); ?></option>
                                <?php }} ?>
                              </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-2">
                        <div class="form-group">
                          <div class="col-sm-12 pl-sm pr-sm">
                              <label for="batchno" class="control-label">Process Batch No.</label>
                              <select id="batchno" name="batchno" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                <option value="">All Batch No.</option>
                                <?php if(!empty($batchnodata)){ foreach($batchnodata as $batch){ ?>
                                <option value="<?php echo $batch['id']; ?>"><?php echo $batch['batchno']; ?></option>
                                <?php }} ?>
                              </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <div class="col-sm-12 pl-sm pr-sm">
                              <label for="designationid" class="control-label">Designation</label>
                              <select id="designationid" name="designationid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-actions-box="true" data-size="8">
                                <option value="0">All Designation</option>
                                <?php if(!empty($designationdata)){ foreach($designationdata as $designation){ ?>
                                <option value="<?php echo $designation['id']; ?>"><?php echo $designation['name']; ?></option>
                                <?php }} ?>
                              </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
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
                    <?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                        <a class="<?=startnewprocess_class;?>" href="<?=ADMIN_URL?>product-process/start-new-process" title=<?=startnewprocess_title?>><?=startnewprocess_text;?></a>
                    <?php } if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                        <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('','Product Process','<?php echo ADMIN_URL; ?>product-process/delete-mul-product-process')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                    <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                      <a class="<?=printbtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipleprint('<?php echo ADMIN_URL; ?>product-process/print-mul-process-challan')" title="<?=printbtn_title?> CHALLAN"><?=printbtn_text;?> Challan</a>
                    <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="productprocesstable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width5">Sr. No.</th>
                      <th>Group Name</th> 
                      <th>Process Name</th> 
                      <th>Batch No.</th> 
                      <th>Designation</th> 
                      <th>Process Type</th>
                      <th>Transaction Date</th>
                      <th>Status</th> 
                      <th>Added By</th> 
                      <th class="width15">Actions</th>
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