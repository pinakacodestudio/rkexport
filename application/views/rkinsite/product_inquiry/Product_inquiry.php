<script>
    var PRODUCT_PATH='<?=PRODUCT?>'; 
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
								<div class="panel-heading filter-panel border-filter-heading">
									<h2><?=APPLY_FILTER?></h2>
									<div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
								</div>
								<div class="panel-body panelcollapse pt-n" style="display: none;">
                  <form action="#" id="memberform" class="form-horizontal">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-n pr-sm">
                              <label class="control-label">Inquiry Date</label>
                              <div class="input-daterange input-group" id="datepicker-range">
                                  <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                  <span class="input-group-addon">to</span>
                                  <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-sm pr-sm">
                              <label for="channelid" class="control-label">Select Channel</label>
                              <select id="channelid" name="channelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" title="All Channel" data-live-search="true" >
                              <option value="0">All Channel</option>
                                <?php foreach($channeldata as $cd){
                                    $selected = ""; 
                                    if(!empty($this->session->userdata(base_url().'CHANNEL'))){ 
                                      $arrChannel = explode(",",$this->session->userdata(base_url().'CHANNEL'));
                                      if(in_array($cd['id'], $arrChannel)){ 
                                        $selected = "selected"; 
                                      } 
                                    }
                                  ?>
                                <option value="<?php echo $cd['id']; ?>" <?php echo $selected; ?>><?php echo $cd['name']; ?></option>
                                <?php } ?>
                                
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-sm pr-sm">
                              <label for="memberid" class="control-label">Select <?=Member_label?></label>
                              <select id="memberid" name="memberid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                <option value="">All <?=Member_label?></option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-sm pr-sm">
                              <label for="productid" class="control-label">Select Product</label>
                              <select id="productid" name="productid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                <option value="">All Product</option>
                              </select>
                            </div>
                          </div>
                        </div>
                       
                      </div>
                      <div class="col-md-12">
                          <div class="col-md-3">
                            <div class="form-group">
                              <div class="col-md-12 pl-n pr-sm">
                                  <label for="type" class="control-label">Select Type</label>
                                  <select id="type" name="type" class="selectpicker form-control" data-live-search="true">
                                      <option value="">Select Type</option>
                                      <option value="0">App</option>
                                      <option value="1">Website</option>
                                  </select>                                      
                              </div>
                            </div>
                          </div>
                          <div class="col-md-2 pt-md">
                            <div class="form-group">
                              <div class="col-md-6">
                                <label class="control-label"></label>
                                <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                              </div>
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
                  <?php
                   if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('','Product Inquiry','<?php echo ADMIN_URL; ?>product-inquiry/delete-mul-product-inquiry')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <div class="table-responsive">
                  <table id="productinquirytable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                      <tr>
                        <th class="width5">Sr.No.</th>
                        <th><?=Member_label?> Name</th>
                        <th>Product Name</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th class="width5">Request</th>
                        <th class="width8">Inquiry Date</th>
                        <th>Email</th>
                        <th>Mobile No.</th>
                        <th>Organization Name</th>
                        <th>Address</th>
                      
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
              </div>
              <div class="panel-footer"></div>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Message</h4>
      </div>
      <div class="modal-body" style="float: left;">
          <div class="col-md-12 p-n" id="message">
            <div class="table-responsive">
            </div>
          </div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>