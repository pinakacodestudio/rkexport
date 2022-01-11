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
                          <label for="categoryid" class="control-label">Category</label>
                          <select id="categoryid" name="categoryid[]" class="selectpicker form-control"  data-live-search="true" data-select-on-tab="true" data-size="5" title="All Category" data-actions-box="true" multiple>
                            <?php foreach($categorydata as $category){ ?>
                              <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="col-sm-12 pr-sm pl-sm">
                          <label for="brandid" class="control-label">Brand</label>
                          <select id="brandid" name="brandid[]" class="selectpicker form-control"  data-live-search="true" data-select-on-tab="true" data-size="5" title="All Brand" data-actions-box="true" multiple>
                            <?php foreach($branddata as $brand){ ?>
                              <option value="<?php echo $brand['id']; ?>"><?php echo $brand['name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="col-md-12 pr-sm pl-sm">
                          <label for="producttype" class="control-label">Product Type</label>
                          <select id="producttype" name="producttype" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                            <option value="">All Product</option>
                            <option value="0">Regular Product</option>
                            <option value="1">Offer Product</option>
                            <option value="2">Raw Product</option>
                            <option value="3">Semi-Finish Product</option>
                          </select>
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
                      <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>product/add-product" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    } ?>
                    <?php
                    if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>product/check-product-use','Product','<?php echo ADMIN_URL; ?>product/delete-mul-product')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                  <a class="<?=qrcode_class?>" href="<?=ADMIN_URL?>product/qr-code" title="<?=qrcode_title?>"><?=qrcode_text?></a>
                  <?php if (in_array("import-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=importbtn_class;?>" href="javascript:void(0)" onclick="importproduct()" title="<?=importbtn_title?>"><?=importbtn_text;?></a>
                  <!-- <a class="<?php //assignproductbtn_class;?>" href="javascript:void(0)" onclick="assignproduct()" title="<?php//assignproductbtn_title?>"><?php //assignproductbtn_text;?></a> -->
                  <?php } if (in_array("upload-image",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=uploadproductimagebtn_class;?>" href="javascript:void(0)" onclick="uploadproductfile()" title="<?=uploadproductimagebtn_title?>"><?=uploadproductimagebtn_text;?></a>
                  <?php } if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportadminproduct()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="producttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width5">Sr. No.</th>
                      <th>Name</th>  
                      <th>Category</th>  
                      <th>Brand</th>  
                      <th class="text-right">Price (<?=CURRENCY_CODE?>)</th>
                      <th class="width8 text-right">Priority</th>
                      <th class="width8 text-right">Entry Date</th>
                      <th class="width8">Actions</th>
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
  <div class="modal-dialog" role="document" style="width:850px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
        <h4 class="modal-title">Variant Details</h4>
      </div>
      <div class="modal-body">
          <div class="col-md-12 p-n">
            <div class="table-responsive" style="height:350px;">
            </div>
          </div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>
<div class="modal fade" id="myProductImportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
      <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
          <h3 class="modal-title">Import Product</h3>
      </div>
      <div class="modal-body">
          <form class="form-horizontal" id="productimportform">
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
                  <a href="<?=IMPORT_FILE?>import-product.xls" class="btn btn-default btn-raised" download="import-product.xls"><i class="fa fa-download"></i> Download File<div class="ripple-container"></div></a>
              </div>
              </div>
          </div>
          <div class="form-group">
              <div class="col-sm-offset-4 col-sm-8">
                  <div class="input-group">
                  <input type="button" class="btn btn-primary btn-raised" onclick="checkimportproductvalidation()" value="Import">
                  <button class="btn btn-danger btn-raised" data-dismiss="modal">Close<div class="ripple-container"></div></button>
              </div>
              </div>
          </div>
          </form>
      </div>
      
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
<div class="modal fade" id="myDetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h3 class="modal-title">Assign Product</h3>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" id="assignproductform">
          <div class="form-group" id="assignproductattachment_div">
            <label class="col-sm-4 control-label">Select Excel File <span class="mandatoryfield">*</span></label>
            <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                    <span class="btn btn-primary btn-raised btn-file">Browse...
                    <input type="file" name="attachment" id="assignproductattachment" accept=".xls,.xlsx" >
                  </span>
                  </span>
                <input type="text" readonly="" id="assignproductFiletext" class="form-control" value="">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 control-label">Download Format</label>
            <div class="col-sm-8">
                <div class="input-group">
                  <a href="<?=IMPORT_FILE?>import-product-price.xls" class="btn btn-default btn-raised" download="import-product-price.xls"><i class="fa fa-download"></i> Download File<div class="ripple-container"></div></a>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <div class="input-group">
                  <input type="button" class="btn btn-primary btn-raised" onclick="checkassignproductvalidation()" value="Import">
                  <button class="btn btn-danger btn-raised" data-dismiss="modal">Close<div class="ripple-container"></div></button>
              </div>
            </div>
          </div>
        </form>
      </div>
      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
<div class="modal fade" id="myProductFileModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h3 class="modal-title">Upload Product Files</h3>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" id="productfileuploadform">
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
                  <input type="button" class="btn btn-primary btn-raised" onclick="checkvalidationforproductimage()" value="Upload">
                  <button class="btn btn-danger btn-raised" data-dismiss="modal">Close<div class="ripple-container"></div></button>
              </div>
            </div>
          </div>
        </form>
        
      </div>
      <div class="modal-footer">
        
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>