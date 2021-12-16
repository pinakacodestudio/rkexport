<script type="text/javascript">
  var indianpostpackagecount = fedexweightcount = 1;
  var FEDEXLABEL = '<?=FEDEXLABEL?>';
</script>
<div class="page-content">
    <ol class="breadcrumb">                        
        <?php
            $subid = $this->session->userdata(base_url().'submenuid');
            foreach($subnavtabsmenu as $row){
                if($subid == $row['id']){
          ?>
          <li class="active"><a href="javascript:void(0);"><?=$row['name']; ?></a></li>
          <?php }else{ ?>
          <li class=""><a href="<?=ADMIN_URL.$row['menuurl']; ?>"><?=$row['name']; ?></a></li>
          <?php } } ?>
    </ol>
    <div class="page-heading">            
        <h1><?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
          <ol class="breadcrumb">                        
            <li><a href="<?=base_url(); ?><?=ADMINFOLDER; ?>dashboard">Home</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
          </ol>
        </small>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default">
              <div class="panel-heading">
                <div class="col-md-12 p-n">
                  <div class="col-md-3">
                    <select id="customerid" name="customerid" class="selectpicker form-control" data-live-search="true" data-size="5">
                      <option value="0">Select Customer</option>
                      <?php foreach($customerdata as $row){ ?>
                        <option value="<?php echo $row['id']; ?>"><?=ucwords($row['customername']); ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <div class="input-daterange input-group" id="datepicker-range">
                      <input type="text" class="input-small form-control" name="fromdate" id="fromdate" value="<?=date('Y-m-d',strtotime(date("d-m-Y")." -1 month"))?>"/>
                      <span class="input-group-addon">to</span>
                      <input type="text" class="input-small form-control" name="todate" id="todate" value="<?=date('Y-m-d')?>"/>
                    </div>
                  </div>  
                </div>
                <div class="col-md-6">
                  <div class="panel-ctrls"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                  <a class="<?=pickupbtn_class;?>" href="javascript:void(0)" onclick="openpickuprequestmodal()" title="<?=pickupbtn_title?>"><?=pickupbtn_text;?></a>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="pendingshippingtable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width8">Sr.No.</th>
                      <th>Customer Name</th>
                      <th>Portal</th>
                      <th>Order No.</th>
                      <th>Order Date</th>
                      <th>Payment Method</th>
                      <th>Courier Company</th>
                      <th>Order Status</th>
                      <th class="width5">Courier API Status</th>
                      <th class="text-right">Order Amount</th>
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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Shipping Order</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" id="shippingorderform">
          <input type="hidden" name="orderid" id="orderid" value="0">
          <input type="hidden" name="invoiceamount" id="invoiceamount" value="0">
          <div class="form-group is-empty">
            <label for="courierid" class="col-sm-4 control-label">Courier Company <span class="mandatoryfield">*</span></label>
            <div class="col-sm-8">
              <select id="courierid" name="courierid" class="selectpicker form-control" data-size="5">
                <?php foreach($couriercompanylist as $row){ ?>
                  <option value="<?php echo $row['id']; ?>"><?=ucwords($row['companyname']); ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <!-- <div class="form-group" id="shipdate_div">
            <label for="focusedinput" class="col-sm-4 control-label">Ship Date <span class="mandatoryfield">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="shipdate" name="shipdate" value="<?=date('d/m/Y',strtotime(date("d-m-Y")))?>" readonly tabindex="12">
            </div>
          </div> -->
          <div id="indianpost_div" class="form-group" style="display: none;"></div>
          <div id="fedex_div" class="form-group" style="display: none;"></div>

          <div class="form-group" style="text-align: right;">
            <div class="col-sm-12">
              <input type="button" data-dismiss="modal" aria-label="Close" value="Close" class="btn">
              <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="submit" class="btn btn-primary btn-raised">
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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
  function addnewindianpostpackage(){

    if($('input[name="indianpostamount[]"]').length<4){
      indianpostpackagecount = ++indianpostpackagecount;
        $.html = '<div class="" id="indianpostpackagecount'+indianpostpackagecount+'"><div class="" id="productfile'+indianpostpackagecount+'_div"> \
                    <div class="col-md-5 p-n"> \
                      <div class="form-group" id="indianpostweight'+indianpostpackagecount+'_div"> \
                        <label class="col-sm-5 control-label">Weight (KG)</label> \
                        <div class="col-sm-7"> \
                          <input id="indianpostweight'+indianpostpackagecount+'" type="text" name="indianpostweight[]" value="" class="form-control" onkeypress="return decimal_number_validation(event,this.value,6,3)"> \
                        </div> \
                      </div> \
                    </div> \
                    <div class="col-md-5 p-n"> \
                      <div class="form-group" id="indianpostamount'+indianpostpackagecount+'_div"> \
                        <label class="col-sm-5 control-label">Amount <span class="mandatoryfield">*</span></label> \
                        <div class="col-sm-7"> \
                          <input id="indianpostamount'+indianpostpackagecount+'" type="text" name="indianpostamount[]" value="0" class="form-control" onkeypress="return decimal_number_validation(event,this.value,7)"> \
                        </div> \
                      </div> \
                    </div> \
                    <div class="col-md-2"> \
                      <button type="button" class="btn btn-default btn-raised" onclick="removeindianpostpackage('+indianpostpackagecount+')" style="margin-top: 0px;"><i class="fa fa-minus"></i><div class="ripple-container"></div></button> \
                    </div> \
                    </div></div>';
                    
        $('#indianpostpackagedetail_div').append($.html);
        
    }else{
      PNotify.removeAll();
      new PNotify({title: 'Maximum 4 package allowed !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
  }
  function removeindianpostpackage(rowid){
    $('#indianpostpackagecount'+rowid).remove();
  }
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