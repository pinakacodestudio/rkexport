<script type="text/javascript">
  var indianpostpackagecount = fedexweightcount = 1;
  var fedexcourierid = '<?php echo $fedexcourierid; ?>';
  var FEDEXLABEL = '<?=FEDEX_LABEL?>';

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
                              <div class="col-md-2 pr-xs">
                                <div class="form-group">
                                  <div class="col-md-12">
                                    <label for="buyerchannelid" class="control-label">Buyer Channel</label>
                                    <select id="buyerchannelid" name="buyerchannelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                      <option value="">All Buyer Channel</option>
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
                              <div class="col-md-4">
                                <div class="form-group">
                                  <div class="col-md-12">
                                    <label for="buyermemberid" class="control-label">Buyer <?=Member_label?></label>
                                    <select id="buyermemberid" name="buyermemberid[]" multiple data-actions-box="true" title="All Buyer <?=Member_label?>"  class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                    </select>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-2 pl-xs pr-xs">
                                <div class="form-group">
                                  <div class="col-md-12">
                                    <label for="sellerchannelid" class="control-label">Seller Channel</label>
                                    <select id="sellerchannelid" name="sellerchannelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                      <option value="">All Seller Channel</option>
                                      <option value="0">Company</option>
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
                              <div class="col-md-4">
                                <div class="form-group">
                                  <div class="col-md-12">
                                    <label for="sellermemberid" class="control-label">Seller <?=Member_label?></label>
                                    <select id="sellermemberid" name="sellermemberid[]" multiple data-actions-box="true" title="All Seller <?=Member_label?>"  class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                    </select>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-4">
                                  <div class="form-group">
                                      <div class="col-sm-12">
                                          <label for="startdate" class="control-label">Invoice Date</label>
                                          <div class="input-daterange input-group" id="datepicker-range">
                                              <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-3 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                              <span class="input-group-addon">to</span>
                                              <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <div class="col-md-12">
                                    <label for="status" class="control-label">Invoice Status</label>
                                    <select id="status" name="status" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                      <option value="">All Invoice Status</option>
                                      <option value="0"><?=$this->Invoicestatus[0]?></option>
                                      <option value="4"><?=$this->Invoicestatus[4]?></option>
                                    </select>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group mt-xxl">
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
                        </div>
                    </div>
                <div class="panel-body no-padding">
                    <table id="pendingshippingtable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                        <th class="width8">Sr.No.</th>
                        <th>buyer Name</th>
                        <th>Seller Name</th>
                        <th>Invoice No.</th>
                        <th>Invoice Date</th>
                        <th>Payment Method</th>
                        <th>Shipping Company</th>
                        <th>Invoice Status</th>
                        <th class="text-right">Invoice Amount (<?=CURRENCY_CODE?>)</th>
                        <th class="width12">Action</th>
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
  <div class="modal-dialog" role="document" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
        <h4 class="modal-title">Shipping Order</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" id="shippingorderform">
          <input type="hidden" name="invoiceid" id="invoiceid" value="0">
          <input type="hidden" name="invoiceamount" id="invoiceamount" value="0">
          <div class="form-group">
              <label for="focusedinput" class="col-sm-4 control-label">Shipping By</label>
              <div class="col-sm-8">
                  <div class="col-sm-3 col-xs-6" style="padding-left: 0px;">
                      <div class="radio">
                      <input type="radio" name="shippingby" id="shippingbycourier" value="0" checked>
                      <label for="shippingbycourier">Courier</label>
                      </div>
                  </div>
                  <div class="col-sm-3 col-xs-6">
                      <div class="radio">
                      <input type="radio" name="shippingby" id="shippingbytransporter" value="1">
                      <label for="shippingbytransporter">Transporter</label>
                      </div>
                  </div>
              </div>
          </div>
          <div class="form-group is-empty" id="courier_div">
            <label for="courierid" class="col-sm-4 control-label">Courier Company <span class="mandatoryfield">*</span></label>
            <div class="col-sm-8">
              <select id="courierid" name="courierid" class="selectpicker form-control" data-size="5">
                  <option value="0">Select Courier Company</option>
                <?php foreach($couriercompanylist as $row){ ?>
                  <option value="<?php echo $row['id']; ?>"><?=ucwords($row['companyname']); ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group is-empty" id="transporter_div" style="display:none;">
            <label for="transporterid" class="col-sm-4 control-label">Select Transporter <span class="mandatoryfield">*</span></label>
            <div class="col-sm-8">
              <select id="transporterid" name="transporterid" class="selectpicker form-control" data-size="5">
                  <option value="0">Select Transporter</option>
                <?php foreach($transporterlist as $row){ ?>
                  <option value="<?php echo $row['id']; ?>"><?=ucwords($row['companyname']); ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div id="indianpost_div" class="form-group" style="display: none;"></div>
          <div id="fedex_div" class="form-group" style="display: none;"></div>
          <div class="form-group">
            <!-- <div class="col-md-12">
              <div class="form-group" id="indianposttracking_div">
                <label class="col-sm-4 control-label">Tracking Code <span class="mandatoryfield">*</span></label>
                <div class="col-sm-8">
                    <input id="indianposttrackingcode" type="text" name="indianposttrackingcode" value="" class="form-control">
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="col-sm-4 control-label">Remarks</label>
                <div class="col-sm-8">
                  <textarea id="indianpostremarks" name="indianpostremarks" class="form-control" rows="3" maxlength="100"></textarea>
                </div>
              </div>
            </div>
            <div class="col-md-12 countcourierpackage" id="indianpostpackagecount1">
                <div class="col-md-5 p-n">
                    <div class="form-group" id="indianpostweight1_div">
                        <label class="col-sm-5 control-label">Weight (KG)</label>
                        <div class="col-sm-7">
                        <input id="indianpostweight1" type="text" name="indianpostweight[]" value="" class="form-control" onkeypress="return decimal_number_validation(event,this.value,6,3)">
                        </div>
                    </div>
                </div>
                <div class="col-md-5 p-n">
                    <div class="form-group" id="indianpostamount1_div">
                        <label class="col-sm-5 control-label">Amount <span class="mandatoryfield">*</span></label>
                        <div class="col-sm-7">
                        <input id="indianpostamount1" type="text" name="indianpostamount[]" value="0" class="form-control" onkeypress="return decimal_number_validation(event,this.value,7)">
                        </div>
                    </div>
                </div>
                <div class="col-md-2 text-right pt-md">
                   
                    <button type="button" class="btn btn-default btn-raised remove_package_btn m-n" onclick="removeindianpostpackage(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>

                    <button type="button" class="btn btn-default btn-raised add_package_btn m-n" onclick="addnewindianpostpackage()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                </div>
            </div> 
          -->
           
          </div>
          <div class="form-group" style="text-align: center;">
            <div class="col-sm-12">
                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="submit" class="btn btn-primary btn-raised">
                <input type="button" data-dismiss="modal" aria-label="Close" value="Close" class="<?=cancellink_class?>">
            </div>
          </div>  
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
        <h4 class="modal-title">Shipping Order Details</h4>
      </div>
      <div class="modal-body" id="shippingdetail">
        
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Pickup Request</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" id="pickuprequestform">
          <div class="form-group" id="readytime_div">
            <label class="col-sm-4 control-label">Ready time <span class="mandatoryfield">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="readytime" name="readytime">
            </div>
          </div>
          <div class="form-group" id="totalpackage_div">
            <label class="col-sm-4 control-label">Total no. of packages <span class="mandatoryfield">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control number" id="totalpackage" name="totalpackage" maxlength="2">
            </div>
          </div>
          <div class="form-group" id="totalweight_div">
            <label for="focusedinput" class="col-sm-4 control-label">Total weight <span class="mandatoryfield">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="totalweight" name="totalweight" onkeypress="return decimal_number_validation(event,this.value,6,3)">
            </div>
          </div>
          <div class="form-group" style="text-align: right;">
            <div class="col-sm-12">
              <input type="button" data-dismiss="modal" aria-label="Close" value="Close" class="btn">
              <input type="button" id="submit" onclick="pickuprequest()" name="submit" value="submit" class="btn btn-primary btn-raised">
            </div>
          </div>  
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">ERP Request & Response</h4>
      </div>
      <div class="modal-body">
        <h4>Request:</h4>
        <p id="requesttext" style="word-break: break-word;"></p>
        <h4>Response:</h4>
        <p id="responsetext" style="word-break: break-word;"></p>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  

function addnewfedexpackage(){

  if($('input[name="fedexweight[]"]').length<4){
    fedexweightcount = ++fedexweightcount;
      $.html = '<div class="col-md-12" id="fedexweightcount'+fedexweightcount+'"> \
                  <div class="col-md-12 p-n"> \
                      <div class="form-group" id="fedexweight'+fedexweightcount+'_div"> \
                        <div class="col-sm-2"> \
                          <label class="col-sm-12 control-label p-n text-left">Weight</label> \
                          <input id="fedexweight'+fedexweightcount+'" type="text" name="fedexweight[]" placeholder="" class="form-control" onkeypress="return decimal_number_validation(event,this.value,6,3)"> \
                        </div> \
                        <div class="col-sm-2"> \
                          <label class="col-sm-12 control-label p-n text-left">Length</label> \
                          <input id="length'+fedexweightcount+'" type="text" name="length[]" placeholder="" class="form-control" onkeypress="return decimal_number_validation(event,this.value)"> \
                        </div> \
                        <div class="col-sm-2"> \
                          <label class="col-sm-12 control-label p-n text-left">Width</label> \
                          <input id="width'+fedexweightcount+'" type="text" name="width[]" placeholder="" class="form-control" onkeypress="return decimal_number_validation(event,this.value)"> \
                        </div> \
                        <div class="col-sm-2"> \
                          <label class="col-sm-12 control-label p-n text-left">Height</label> \
                          <input id="height'+fedexweightcount+'" type="text" name="height[]" placeholder="" class="form-control" onkeypress="return decimal_number_validation(event,this.value)"> \
                        </div> \
                        <div class="col-sm-2"> \
                          <label class="col-sm-12 control-label p-n text-left">Dim. on</label> \
                          <select id="units'+fedexweightcount+'" name="units[]" class="selectpicker form-control"> \
                            <option value="IN">IN</option> \
                            <option value="CM">CM</option> \
                          </select> \
                        </div> \
                        <div class="col-md-2"> \
                          <button type="button" class="btn btn-default btn-raised" onclick="removefedexpackage('+fedexweightcount+')" style="margin-top: 0px;"><i class="fa fa-minus"></i><div class="ripple-container"></div></button> \
                        </div> \
                      </div> \
                  </div> \
                  </div>';
                  
      $('#multiplepackage').append($.html);
      $('#units'+fedexweightcount).selectpicker('refresh');
      
  }else{
    PNotify.removeAll();
    new PNotify({title: 'Maximum 4 package allowed !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}
function removefedexpackage(rowid){
  $('#fedexweightcount'+rowid).remove();
}
function setfedexdata(weight,invoiceamount,fedexcodamount,paymentmethod){
  //alert(paymentmethod);
  
  $('#indianpost_div').hide();
  $('#indianpost_div').html('');
  $('#fedex_div').show();

  $.html = '<div class="col-md-12"> \
              <div class="row"> \
                <div class="form-group" id="fedexinvoiceamount_div"> \
                  <div class="col-md-12"> \
                    <label class="col-sm-4 control-label">Fedex Account</label> \
                    <div class="col-sm-8"> \
                      <select id="fedexdetailid" name="fedexdetailid" class="selectpicker form-control" data-live-search="true" data-size="5"> \
                        <?php foreach($fedexaccountdata as $row){ ?> \
                          <option value="<?php echo $row['id']; ?>"><?=$row['accountnumber']; ?></option> \
                        <?php } ?> \
                      </select> \
                    </div> \
                  </div> \
                </div> \
              </div> \
              \
              <div class="form-group" id="fedexweight1_div"> \
                <div class="col-sm-2"> \
                  <label class="col-sm-12 control-label p-n text-left">Weight</label> \
                  <input id="fedexweight1" type="text" name="fedexweight[]" placeholder="" value="'+weight+'" class="form-control" onkeypress="return decimal_number_validation(event,this.value,6,3)"> \
                </div> \
                <div class="col-sm-2"> \
                  <label class="col-sm-12 control-label p-n text-left">Length</label> \
                  <input id="length1" type="text" name="length[]" placeholder="" class="form-control" onkeypress="return decimal_number_validation(event,this.value)"> \
                </div> \
                <div class="col-sm-2"> \
                  <label class="col-sm-12 control-label p-n text-left">Width</label> \
                  <input id="width1" type="text" name="width[]" placeholder="" class="form-control" onkeypress="return decimal_number_validation(event,this.value)"> \
                </div> \
                <div class="col-sm-2"> \
                  <label class="col-sm-12 control-label p-n text-left">Height</label> \
                  <input id="height1" type="text" name="height[]" placeholder="" class="form-control" onkeypress="return decimal_number_validation(event,this.value)"> \
                </div> \
                <div class="col-sm-2"> \
                  <label class="col-sm-12 control-label p-n text-left">Dim. on</label> \
                  <select id="units1" name="units[]" class="selectpicker form-control"> \
                    <option value="IN">IN</option> \
                    <option value="CM">CM</option> \
                  </select> \
                </div> \
                  <div class="col-md-2"> \
                    <button type="button" class="btn btn-default btn-raised" onclick="addnewfedexpackage()" style="margin-top: 0px;"><i class="material-icons">plus_one</i><div class="ripple-container"></div></button> \
                  </div> \
              </div> \
              <div id="multiplepackage" class="form-group"></div> \
              \
              <div class="form-group" id="fedexinvoiceamount_div"> \
                <label class="col-sm-4 control-label">Invoice Amount</label> \
                <div class="col-sm-8"> \
                  <input id="fedexinvoiceamount" type="text" name="fedexinvoiceamount" value="'+invoiceamount+'" class="form-control number" readonly> \
                </div> \
              </div>';
              if(paymentmethod=='COD'){
                $.html += '<div class="form-group" id="fedexcodamount_div"> \
                            <label class="col-sm-4 control-label">COD Amount</label> \
                            <div class="col-sm-8"> \
                              <input id="fedexcodamount" type="text" name="fedexcodamount" value="'+fedexcodamount+'" class="form-control number" readonly> \
                            </div> \
                          </div>';
              }
              $.html += '<div class="form-group"> \
                              <label class="col-sm-4 control-label">Remarks</label> \
                              <div class="col-sm-8"> \
                                <textarea id="fedexremarks" name="fedexremarks" class="form-control" rows="3" maxlength="100"></textarea> \
                              </div> \
                            </div> \
                          <div class="form-group" id="fedexservice_div"> \
                            <label class="col-sm-4 control-label">Service <span class="mandatoryfield">*</span></label> \
                            <div class="col-sm-8"> \
                              <select id="fedexservice" name="fedexservice" class="selectpicker form-control" data-size="5" data-live-search="true"> \
                                <option value="0">Select Service</option> \
                                <option value="FEDEX_EXPRESS_SAVER" selected>FEDEX_EXPRESS_SAVER</option> \
                                <option value="STANDARD_OVERNIGHT">STANDARD_OVERNIGHT</option> \
                                <option value="FEDEX_FIRST_FREIGHT">FEDEX_FIRST_FREIGHT</option> \
                                <option value="FEDEX_1_DAY_FREIGHT">FEDEX_1_DAY_FREIGHT</option> \
                                <option value="FEDEX_2_DAY">FEDEX_2_DAY</option> \
                                <option value="FEDEX_2_DAY_AM">FEDEX_2_DAY_AM</option> \
                                <option value="FEDEX_2_DAY_FREIGHT">FEDEX_2_DAY_FREIGHT</option> \
                                <option value="FEDEX_3_DAY_FREIGHT">FEDEX_3_DAY_FREIGHT</option> \
                                <option value="FEDEX_FREIGHT_ECONOMY">FEDEX_FREIGHT_ECONOMY</option> \
                                <option value="FEDEX_FREIGHT_PRIORITY">FEDEX_FREIGHT_PRIORITY</option> \
                                <option value="FEDEX_GROUND">FEDEX_GROUND</option> \
                                <option value="FIRST_OVERNIGHT">FIRST_OVERNIGHT</option> \
                                <option value="INTERNATIONAL_ECONOMY">INTERNATIONAL_ECONOMY</option> \
                                <option value="INTERNATIONAL_ECONOMY_FREIGHT">INTERNATIONAL_ECONOMY_FREIGHT</option> \
                                <option value="INTERNATIONAL_FIRST">INTERNATIONAL_FIRST</option> \
                                <option value="INTERNATIONAL_PRIORITY">INTERNATIONAL_PRIORITY</option> \
                                <option value="INTERNATIONAL_PRIORITY_FREIGHT">INTERNATIONAL_PRIORITY_FREIGHT</option> \
                              </select> \
                            </div> \
                          </div>';
            $.html += '<div class="form-group"> \
                            <label class="col-sm-4 control-label"></label> \
                            <div class="col-sm-8"> \
                              <input type="button" value="Calculate Rate" onclick="calculateshippingcharges()" class="btn btn-raised"> \
                            </div> \
                        </div> \
                        <div id="shippingprice_div"></div> \
                        </div>';


  $('#fedex_div').html($.html); 
  $('#fedexdetailid').selectpicker('refresh');
  $('#fedexservice').selectpicker('refresh');
  $('#units1').selectpicker('refresh');
}



</script>