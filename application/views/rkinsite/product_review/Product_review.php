<div class="page-content">
    <div class="page-heading">     
      <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
        <form action="#" id="memberform">
          <div class="col-md-12">
            <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
								<div class="panel-heading filter-panel border-filter-heading">
									<h2><?=APPLY_FILTER?></h2>
									<div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
								</div>
								<div class="panel-body panelcollapse pt-n" style="display: none;">
                  
                    <div class="row">
                      <div class="col-md-12 form-horizontal">

                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pr-sm">
                              <label class="control-label">Review Date</label>
                              <div class="input-daterange input-group" id="datepicker-range">
                                  <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-2 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                  <span class="input-group-addon">to</span>
                                  <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                              </div>
                            </div>
                          </div>
                        </div>                
                      
                        <div class="col-md-3">
                          <div class="form-group" id="productid_div">
                            <div class="col-sm-12 pl-sm pr-sm">
                              <label for="productid" class="control-label">Select Product</label>
                              <select id="productid" name="productid" class="selectpicker form-control" data-subtext="true" data-select-on-tab="true" data-size="5" data-live-search="true">
                                <option value="0">All Product</option>
                                <?php foreach($productdata as $product){ 
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

                                      <option data-content="<?php if(!empty($product['image'])){?><img src='<?=PRODUCT.$img?>' style='width:40px'> <?php } echo $productname; ?> <small class='text-muted'><?php echo $product['sku']; ?></small>" value="<?php echo $product['id']; ?>"><?php echo $productname; ?></option>
                                  
                                  <?php } ?>
                                <?php } ?>
                              </select>
                            </div>
                          </div>
                        </div>
                        
                     
                        <div class="col-md-3">
                          <div class="form-group" id="productid_div">
                            <div class="col-sm-12 pl-sm pr-sm">
                              <label for="status" class="control-label">Select Status</label>
                                    <select id="type" name="type" class="selectpicker form-control" data-live-search="true">
                                        <option value="">Select Status</option>
                                        <option value="0">Pendding</option>
                                        <option value="1">Approved</option>
                                        <option value="2">Not Approved</option>
                                    </select>  
                            </div>
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="form-group" id="productid_div">
                            <div class="col-md-12 pl-sm">
                             <label for="memberid" class="control-label">Select User type</label>
                                    <select id="memberid" name="memberid" class="selectpicker form-control" data-live-search="true">
                                        <option value="">All Users</option>
                                        <option value="1">Register</option>
                                        <option value="0">Guest</option>
                                    </select>
                            </div>
                          </div>
                        </div>

                        <div class="form-group" style="margin-top: 42px;">
                            <div class="col-md-12 pl-md">
                              <label class="control-label"></label>
                              <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                            </div>
                        </div>                      
                       
                    </div> 
                  </div>
							</div>
          </div>
        </div>
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-heading">
                <div class="col-md-6">
                  <div class="panel-ctrls panel-tbl"></div>
                </div>
                <div class="col-md-6" style="text-align: right;">
                    
                    <div class="col-md-8" style="margin-top: -8px;">
                      <select id="statustype" name="statustype" class="selectpicker form-control" data-live-search="true">
                          <option value="">Select Status</option>
                          <option value="0">Pendding</option>
                          <option value="1">Approved</option>
                          <option value="2">Not Approved</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <?php 
                          if (strpos(trim($submenuvisibility['submenuedit']),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){
                        ?>
                          <a id="editratingstatus" class="<?=editbtn_class;?>" href="javascript:void(0);" title=<?=editbtn_title?> onclick="updateratingstatus()"><?=editbtn_text;?></a>
                      <?php } ?>
                    </div>
                    <div class="col-md-2">
                      <?php
                          if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                        ?>
                          <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('','Product Review','<?=ADMIN_URL; ?>product-review/delete_mul_product_review')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                      <?php } ?>
                    </div>
                 
                </div>
              </div>
              <div class="panel-body no-padding">
              <table id="productreviewtable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width5">Sr.No.</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Mobile No.</th>
                      <th>Product Name</th>                      
                      <th class="width20">Rating</th>
                      <th class="width5">User Type</th>
                      <th class="width12">Entry Date</th>
                      <th class="width5">Action</th>
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
          </form>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Product Review</h4>
      </div>
      <div class="modal-body">
              
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>




