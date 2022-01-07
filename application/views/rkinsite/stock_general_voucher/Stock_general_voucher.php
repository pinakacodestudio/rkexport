
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
                          <div class="col-sm-12 pr-xs">
                            <label for="startdate" class="control-label">Voucher Date</label>
                            <div class="input-daterange input-group" id="datepicker-range">
                              <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="input-group-addon">to</span>
                              <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <div class="col-sm-12 pl-xs pr-xs">
                              <label for="productid" class="control-label">Product</label>
                              <select id="productid" name="productid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                <option value="0">All Product</option>
                                <?php if(!empty($productdata)){ foreach($productdata as $product){ 
                                  $productname = str_replace("'","&apos;",$product['name']);
                                  if(DROPDOWN_PRODUCT_LIST==0){ ?>
                                      <option value="<?php echo $product['id']; ?>"><?php echo $productname; ?></option>
                                  <?php }else{

                                      if($product['productimage']!="" && file_exists(PRODUCT_PATH.$product['productimage'])){
                                          $img = $product['productimage'];     
                                      }else{
                                          $img = PRODUCTDEFAULTIMAGE;
                                      }
                                      ?>

                                      <option data-content="<?php if(!empty($product['productimage'])){?><img src='<?=PRODUCT.$img?>' style='width:40px'> <?php } echo $productname; ?> " value="<?php echo $product['id']; ?>"><?php echo $productname; ?></option>
                                  
                                  <?php } ?>
                                
                                <?php }} ?>
                              </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <div class="col-sm-12 pl-xs pr-xs">
                            <label for="type" class="control-label">Type</label>
                            <select id="type" name="type" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                              <option value="">All Type</option>
                              <option value="1">Increment</option>
                              <option value="0">Decrement</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2">
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
                <div class="col-md-6">
                  <div class="panel-ctrls panel-tbl"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                    <?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                        <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>stock-general-voucher/add-stock-general-voucher" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                    <?php } if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                        <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('','Stock General Voucher','<?php echo ADMIN_URL; ?>stock-general-voucher/delete-mul-stock-general-voucher')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                    <?php }// if (in_array("import-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                      <!-- <a class="<?php //echo importbtn_class;?>" href="javascript:void(0)" onclick="importstockgeneralvoucher()" title="<?php //echo importbtn_title?>"><?php //echo importbtn_text;?></a> -->
                    <?php //} ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="stockgeneralvouchertable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width5">Sr. No.</th>
                      <th>Voucher No.</th> 
                      <th>Voucher Date</th> 
                      <th>Product</th> 
                      <th class="text-right">Price (<?=CURRENCY_CODE?>)</th> 
                      <th class="text-right">Quantity</th> 
                      <th class="text-right">Total Price (<?=CURRENCY_CODE?>)</th> 
                      <th>Type</th> 
                      <th>Entry Date</th> 
                      <th class="width12">Actions</th>
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

<div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
      <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
          <h3 class="modal-title">Import Stock General Voucher</h3>
      </div>
      <div class="modal-body">
          <form class="form-horizontal" id="importform">
          <div class="form-group" id="attachment_div">
              <label class="col-sm-4 control-label">Select Excel File <span class="mandatoryfield">*</span></label>
              <div class="col-sm-8">
                  <div class="input-group">
                  <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                      <span class="btn btn-primary btn-raised btn-file">Browse...
                      <input type="file" name="attachment" id="attachment" accept=".xls,.xlsx" >
                  </span>
                  </span>
                  <input type="text" readonly="" id="Filetext" class="form-control" value="">
              </div>
              </div>
          </div>
          <div class="form-group">
              <label class="col-sm-4 control-label">Download Format</label>
              <div class="col-sm-8">
                  <div class="input-group">
                  <a href="<?=IMPORT_FILE?>import-stock-general-voucher.xls" class="btn btn-default btn-raised" download="import-stock-general-voucher.xls"><i class="fa fa-download"></i> Download File<div class="ripple-container"></div></a>
              </div>
              </div>
          </div>
          <div class="form-group">
              <div class="col-sm-offset-4 col-sm-8">
                  <div class="input-group">
                  <input type="button" class="btn btn-primary btn-raised" onclick="checkimportvalidation()" value="Import">
                  <button class="btn btn-danger btn-raised" data-dismiss="modal">Close<div class="ripple-container"></div></button>
              </div>
              </div>
          </div>
          </form>
      </div>
      
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>