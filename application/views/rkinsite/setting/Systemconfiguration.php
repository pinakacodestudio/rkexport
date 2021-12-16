<style type="text/css">
   .datepicker1{
    text-align: left !important;
    border-radius: 3px !important;
  }
  .discountonbilldiv{
    box-shadow: 0px 1px 6px #333;padding: 10px;margin-left: 30px
  }
</style>
<script type="text/javascript">
  var MAIN_LOGO_IMAGE_URL = '<?=MAIN_LOGO_IMAGE_URL?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
    </small>                  
    </div>

    <div class="container-fluid">
    

    <div data-widget-group="group1">
      <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12">
                 <form class="form-horizontal" id="settingform">
                  <div class="tab-container tab-default">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#system" data-toggle="tab">System</a></li>
                    <li><a href="#discount" data-toggle="tab">Discount</a></li>
                  </ul>
                  <div class="tab-content">
                    <div class="tab-pane active" id="system">
                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="focusedinput" class="col-sm-4 control-label">Payment </label>
                            <div class="col-sm-8">
                              <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                <div class="radio">
                                <input type="radio" name="payment" id="yes" value="1"  <?php if(isset($settingdata) && $settingdata['payment']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                <label for="yes">On</label>
                                </div>
                              </div>
                              <div class="col-sm-2 col-xs-6">
                                <div class="radio">
                                <input type="radio" name="payment" id="no" value="0" <?php if(isset($settingdata) && $settingdata['payment']==0){ echo 'checked'; }?>>
                                <label for="no">Off</label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="focusedinput" class="col-sm-4 control-label"><?=Member_label?> Management </label>
                            <div class="col-sm-8">
                              <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                <div class="radio">
                                <input type="radio" name="membermanagement" id="membermanagementyes" value="1"  <?php if(isset($settingdata) && $settingdata['vendormanagement']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                <label for="membermanagementyes">On</label>
                                </div>
                              </div>
                              <div class="col-sm-2 col-xs-6">
                                <div class="radio">
                                <input type="radio" name="membermanagement" id="membermanagementno" value="0" <?php if(isset($settingdata) && $settingdata['vendormanagement']==0){ echo 'checked'; }?>>
                                <label for="membermanagementno">Off</label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="focusedinput" class="col-sm-4 control-label">Website </label>
                            <div class="col-sm-8">
                              <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                <div class="radio">
                                <input type="radio" name="website" id="websiteyes" value="1"  <?php if(isset($settingdata) && $settingdata['website']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                <label for="websiteyes">On</label>
                                </div>
                              </div>
                              <div class="col-sm-2 col-xs-6">
                                <div class="radio">
                                <input type="radio" name="website" id="websiteno" value="0" <?php if(isset($settingdata) && $settingdata['website']==0){ echo 'checked'; }?>>
                                <label for="websiteno">Off</label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="focusedinput" class="col-sm-4 control-label">Stock Management </label>
                            <div class="col-sm-8">
                              <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                <div class="radio">
                                <input type="radio" name="stockmanagement" id="stockmanagementyes" value="1"  <?php if(isset($settingdata) && $settingdata['stockmanagement']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                <label for="stockmanagementyes">On</label>
                                </div>
                              </div>
                              <div class="col-sm-2 col-xs-6">
                                <div class="radio">
                                <input type="radio" name="stockmanagement" id="stockmanagementno" value="0" <?php if(isset($settingdata) && $settingdata['stockmanagement']==0){ echo 'checked'; }?>>
                                <label for="stockmanagementno">Off</label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="focusedinput" class="col-sm-4 control-label">GST Bill </label>
                            <div class="col-sm-8">
                              <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                <div class="radio">
                                <input type="radio" name="gstbill" id="gstbillyes" value="1"  <?php if(isset($settingdata) && $settingdata['gstbill']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                <label for="gstbillyes">On</label>
                                </div>
                              </div>
                              <div class="col-sm-2 col-xs-6">
                                <div class="radio">
                                <input type="radio" name="gstbill" id="gstbillno" value="0" <?php if(isset($settingdata) && $settingdata['gstbill']==0){ echo 'checked'; }?>>
                                <label for="gstbillno">Off</label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="focusedinput" class="col-sm-4 control-label">Dealer </label>
                            <div class="col-sm-8">
                              <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                <div class="radio">
                                <input type="radio" name="dealer" id="dealeryes" value="1"  <?php if(isset($settingdata) && $settingdata['dealer']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                <label for="dealeryes">On</label>
                                </div>
                              </div>
                              <div class="col-sm-2 col-xs-6">
                                <div class="radio">
                                <input type="radio" name="dealer" id="dealerno" value="0" <?php if(isset($settingdata) && $settingdata['dealer']==0){ echo 'checked'; }?>>
                                <label for="dealerno">Off</label>
                                </div>
                              </div>
                            </div>
                          </div>

                            <!-- <div class="form-group col-md-6" for="websiteproductsection" id="websiteproductsection_div">  
                              <label class="col-md-4 label-control" for="websiteproductsection">
                              Website Product Section</label>
                              <div class="col-md-8">
                              <?php
                                 //$websiteproductsection = explode(",",$settingdata['websiteproductsection']);
                              ?>
                              <select class="form-control selectpicker" id="websiteproductsection" name="websiteproductsection[]" multiple>
                                    <?php 
                                    /* foreach($productsection as $row){ ?>
                                      <option value="<?php echo $row['id']; ?>" <?php if(isset($settingdata)){ if(in_array($row['id'],$websiteproductsection)){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option> 
                                      <?php } */ ?>
                                </select>  
                                </div>
                            </div>

                            <div class="form-group col-md-6" for="applicationproductsection" id="applicationproductsection_div">
                              <label class="col-md-4 label-control" for="applicationproductsection">
                              Application Product Section
                              </label>
                              <div class="col-md-8">
                              <?php
                              $applicationproductsection = explode(",",$settingdata['applicationproductsection']);
                              ?>
                                <select class="form-control selectpicker" id="applicationproductsection" name="applicationproductsection[]" multiple>
                                      <?php 
                                      foreach($productsection as $row){ ?>
                                        <option value="<?php echo $row['id']; ?>" <?php if(isset($settingdata)){ if(in_array($row['id'],$applicationproductsection)){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option> 
                                        <?php } ?>
                                </select>  
                              </div>
                            </div> -->

                        </div>
                    </div>
                    <div class="tab-pane" id="discount">
                      <div class="row">                     
                        <div class="form-group col-md-6">
                          <label for="focusedinput" class="col-sm-4 control-label">Product Discount </label>
                          <div class="col-sm-8">
                            <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                              <div class="radio">
                              <input type="radio" name="productdiscount" id="productdiscountyes" value="1"  <?php if(isset($settingdata) && $settingdata['productdiscount']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                              <label for="productdiscountyes">On</label>
                              </div>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                              <div class="radio">
                              <input type="radio" name="productdiscount" id="productdiscountno" value="0" <?php if(isset($settingdata) && $settingdata['productdiscount']==0){ echo 'checked'; }?>>
                              <label for="productdiscountno">Off</label>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="focusedinput" class="col-sm-4 control-label">Discount Coupon </label>
                          <div class="col-sm-8">
                            <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                              <div class="radio">
                              <input type="radio" name="discountcoupon" id="discountcouponyes" value="1"  <?php if(isset($settingdata) && $settingdata['discountcoupon']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                              <label for="discountcouponyes">On</label>
                              </div>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                              <div class="radio">
                              <input type="radio" name="discountcoupon" id="discountcouponno" value="0" <?php if(isset($settingdata) && $settingdata['discountcoupon']==0){ echo 'checked'; }?>>
                              <label for="discountcouponno">Off</label>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="focusedinput" class="col-sm-4 control-label"><?=Member_label?> Discount </label>
                          <div class="col-sm-8">
                            <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                              <div class="radio">
                              <input type="radio" name="memberdiscount" id="memberdiscountyes" value="1"  <?php if(isset($settingdata) && $settingdata['vendordiscount']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                              <label for="memberdiscountyes">On</label>
                              </div>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                              <div class="radio">
                              <input type="radio" name="memberdiscount" id="memberdiscountno" value="0" <?php if(isset($settingdata) && $settingdata['vendordiscount']==0){ echo 'checked'; }?>>
                              <label for="memberdiscountno">Off</label>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="focusedinput" class="col-sm-4 control-label">Discount High Priority for <?=Member_label?></label>
                          <div class="col-sm-8">
                            <div class="col-sm-3 col-xs-6" style="padding-left: 0px;">
                              <div class="radio">
                              <input type="radio" name="discountpriority" id="discountpriorityyes" value="0"  <?php if(isset($settingdata) && $settingdata['discountpriority']==0){ echo 'checked'; }else{ echo 'checked'; }?>>
                              <label for="discountpriorityyes">General</label>
                              </div>
                            </div>
                            <div class="col-sm-3 col-xs-6">
                              <div class="radio">
                              <input type="radio" name="discountpriority" id="discountpriorityno" value="1" <?php if(isset($settingdata) && $settingdata['discountpriority']==1){ echo 'checked'; }?>>
                              <label for="discountpriorityno"><?=Member_label?></label>
                              </div>
                            </div>
                          </div>
                          <small>Note : If both (General and <?=Member_label?>) discount scheme are running on same day than it will take high priority based on above setting</small>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="focusedinput" class="col-sm-4 control-label">Discount On Bill </label>
                          <div class="col-sm-8">
                            <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                              <div class="radio">
                              <input type="radio" name="discountonbill" id="discountonbillyes" value="1"  <?php if(isset($settingdata) && $settingdata['discountonbill']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                              <label for="discountonbillyes">On</label>
                              </div>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                              <div class="radio">
                              <input type="radio" name="discountonbill" id="discountonbillno" value="0" <?php if(isset($settingdata) && $settingdata['discountonbill']==0){ echo 'checked'; }?>>
                              <label for="discountonbillno">Off</label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-6">
                          <div id="discountonbilldiv" class="discountonbilldiv">
                            <div class="form-group">
                              <label class="col-sm-4 control-label">Discount Type</label>
                              <div class="col-sm-8">
                                <div class="col-sm-4 col-xs-6" style="padding-left: 0px;">
                                  <div class="radio">
                                  <input type="radio" name="discountonbilltype" id="percentage" value="1" checked <?php if(isset($settingdata) && $settingdata['discountonbilltype']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                  <label for="percentage">Percentage</label>
                                  </div>
                                </div>
                                <div class="col-sm-4 col-xs-6">
                                  <div class="radio">
                                  <input type="radio" name="discountonbilltype" id="amounttype" value="0" <?php if(isset($settingdata) && $settingdata['discountonbilltype']==0){ echo 'checked'; }?>>
                                  <label for="amounttype">Amount</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="form-group" id="percentageval_div">
                              <label class="col-sm-4 control-label" for="percentageval">Percentage (%) <span class="mandatoryfield">*</span></label>
                              <div class="col-sm-8">
                                <input id="percentageval" type="text" name="percentageval" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="5" value="<?php if(!empty($settingdata) && $settingdata['discountonbilltype']==1){ echo $settingdata['discountonbillvalue']; } ?>">
                              </div>
                            </div>
                            <div class="form-group" id="amount_div" style="display: none;">
                              <label class="col-sm-4 control-label" for="amount">Amount <span class="mandatoryfield">*</span></label>
                              <div class="col-sm-8">
                                <input id="amount" type="text" name="amount" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="10" value="<?php if(!empty($settingdata) && $settingdata['discountonbilltype']==0){ echo $settingdata['discountonbillvalue']; } ?>">
                              </div>
                          </div>
                          <div class="row">
                            <button type="button" id="cleardatebtn" class="btn btn-primary btn-xs pull-right">Clear Date</button>
                          </div>
                           <div class="input-daterange" id="datepicker-range">
                                  <div class="form-group row" id="startdate_div">
                                    <label class="col-sm-4 control-label" for="startdate" >Date </label>
                                    <div class="col-sm-4">
                                      <input id="startdate" type="text" name="startdate" value="<?php if(!empty($settingdata)){  if($settingdata['discountonbillstartdate']!="0000-00-00"){ echo $this->general_model->displaydate($settingdata['discountonbillstartdate']); }} ?>" class="form-control datepicker1" placeholder="Start" readonly>
                                    </div>
                                    <div class="col-sm-4">
                                    <input id="enddate" type="text" name="enddate" value="<?php if(!empty($settingdata)){if($settingdata['discountonbillenddate']!="0000-00-00"){ echo $this->general_model->displaydate($settingdata['discountonbillenddate']); }} ?>" class="form-control datepicker1" placeholder="End" readonly>
                                    </div>
                                  </div>
                                </div>
                          <div class="form-group" id="discountonbillminvalue_div">
                              <label class="col-sm-4 control-label" for="discountonbillminamount">Minimum Bill Amount </label>
                              <div class="col-sm-8">
                                <input id="discountonbillminamount" type="text" name="discountonbillminamount" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="10" value="<?php if(!empty($settingdata) && $settingdata['discountonbillminamount']!=0){ echo $settingdata['discountonbillminamount']; } ?>">
                              </div>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="text-align: center;">
                      <div class="form-group">
                        <?php if(isset($settingdata)){ ?>
                          <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                          <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                        <?php }else{ ?>
                          <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-info btn-raised">
                          <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised">
                        <?php } ?>
                      </div>
                    </div>
                </div>
                </form>
              </div>
      </div>
    </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->