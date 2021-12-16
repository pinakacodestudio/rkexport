<div class="page-content">
    <ol class="breadcrumb">                        
        <?php
            $subid = $this->session->userdata(base_url().'submenuid');
            foreach($subnavtabsmenu as $row){
                if($subid == $row['id']){
          ?>
          <li class="active"><a href="javascript:void(0);"><?=$row['name']; ?></a></li>
          <?php }else{ ?>
          <li class=""><a href="<?=base_url().$row['url']; ?>"><?=$row['name']; ?></a></li>
          <?php } } ?>
    </ol>
    <div class="page-heading">            
        <h1><?=$this->session->userdata(base_url().'submenuname')?> Detail</h1>
        <small>
          <ol class="breadcrumb">                        
            <li><a href="<?=base_url(); ?><?=ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
          </ol>
        </small>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-sm-12">
            <p class="lead">Customer Name : <?=ucwords($customerdata['name'])?></p>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-3">
            <div class="inbox-menu list-group-alternate">
              <a href="#details" data-toggle="tab" class="list-group-item active"><i class="material-icons">inbox</i>Summary</a>
              <a href="#billingaddress" data-toggle="tab" class="list-group-item"><i class="material-icons">insert_drive_file</i>Billing Address</a>
              <a href="#order" data-toggle="tab" class="list-group-item"><i class="material-icons">shopping_cart</i>Order</a>
              <a href="#payment" data-toggle="tab" class="list-group-item"><i class="material-icons">attach_money</i>Transaction</a>
              <a href="#cart" data-toggle="tab" class="list-group-item"><i class="material-icons">add_shopping_cart</i>Cart</a>
            </div>
          </div>

          <div class="col-sm-9">
              
            <div class="panel panel-inbox">
              <div class="panel-body">

                <input type="hidden" name="customerid" id="customerid" value="<?=$customerid?>">

                <div class="tab-content">

                  <div class="tab-pane active" id="details">
                    <div class="inbox-mail-heading">
                      <div class="clearfix">
                        <div class="pull-left">
                          <div class="btn-group"><p class="lead" style="margin-bottom: 0;">Summary</p></div>
                        </div>
                      </div>
                    </div>
                    <table class="table table-hover table-inbox table-vam">
                      <tbody>
                        <tr>
                          <td width="25%">Name</td>
                          <td><?=ucwords($customerdata['name'])?></td>
                        </tr>
                        <tr width="25%">
                          <td>Email</td>
                          <td><?=$customerdata['email']?></td>
                        </tr>
                        <tr width="25%">
                          <td>User Name</td>
                          <td><?=$customerdata['name']?></td>
                        </tr>
                        
                        <tr width="25%">
                          <td>Entry Date</td>
                          <td><?=date_format(date_create($customerdata['createddate']), 'd M Y h:i A')?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <div class="tab-pane" id="billingaddress">
                    <div class="inbox-mail-heading">
                        <div class="clearfix">
                          <div class="pull-left">
                            <div class="btn-group"><p class="lead" style="margin-bottom: 0;">Billing Address</p></div>
                          </div> 
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="panel-ctrls1" style="padding-left: 10px;"></div>
                      </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover table-padding" id="billingaddresstable">
                      <thead>
                        <th>Sr.No.</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Mobile No.</th>
                      </thead>
                      <tbody>
                        <?php if(!empty($customershippingdata)){
                        $srno=0;
                        foreach($customershippingdata as $row){ ?>
                          
                          <tr>
                            <td id="srno" class="text-center"><?=++$srno; ?></td>
                            <td><?=ucwords($row['name'])?></td>
                            <td><address><?=$row['address'].", ".$row['town']." - ".$row['postalcode']?></address></td>
                            <td><?=$row['email']?></td>
                            <td><?=$row['mobileno']?></td>
                          </tr>
                        
                        <?php }
                        }else{ ?>
                          <!-- <tr>
                            <td colspan="3" style="text-align: center;">No data available in table</td>
                          </tr> -->
                        <? } ?> 
                      </tbody>
                    </table>
                  </div>

                  <div class="tab-pane" id="order">
                    <div class="inbox-mail-heading">
                        <div class="clearfix">
                          <div class="pull-left">
                            <div class="btn-group"><p class="lead" style="margin-bottom: 0;">Order</p></div>
                          </div> 
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="panel-ctrls2" style="padding-left: 10px;"></div>
                      </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover table-padding"  id="ordertable">
                      <thead>
                        <tr>
                          <th>Sr. No.</th>
                          <th>Customer Name</th>
                          <th>OrderID</th>
                          <th>Order Date</th>
                          <th>Order Status</th>
                          <th class="text-right">Total Amount</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if(!empty($orderData)) {  ?>
                          <?php $srno=0; foreach ($orderData as $row) { ?>
                            <tr>
                              <td><?=++$srno?></td>
                              <td>
                                <a href="<?=ADMIN_URL?>Customer/customerdetail/<?=$row['customerid']?>" title="<?=ucwords($row['customername'])?>"><?=ucwords($row['customername'])?></a>
                              </td>
                              <td><?=$row['orderid']?></td>
                              <td><?=$this->general_model->displaydatetime($row['date']);?></td>
                              <td>
                                <?php if($row['status'] == 1){ ?>
                                <span class="btn btn-warning btn-raised btn-sm">Pending</span>
                                <?php } else if($row['status'] == 2){ ?>
                                <span class="btn btn-success btn-raised btn-sm">Completed</span>
                                <?php }else{ ?>
                                <span class="btn btn-danger btn-raised btn-sm">Cancelled</span>
                                <?php } ?>
                              </td>
                              <td class="text-right"><?=number_format($row['payableamount'], 2, '.', ',')?></td>
                            </tr>  
                          <? } ?>
                        <? }else{ ?>
                          <!-- <tr>
                            <td colspan="3" style="text-align: center;">No data available in table</td>
                          </tr> -->
                        <? } ?>   
                      </tbody>
                    </table>
                  </div>

                   <div class="tab-pane" id="payment">
                    <div class="inbox-mail-heading">
                        <div class="clearfix">
                          <div class="pull-left">
                            <div class="btn-group"><p class="lead" style="margin-bottom: 0;">Payment</p></div>
                          </div> 
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="panel-ctrls3" style="padding-left: 10px;"></div>
                      </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover table-padding"  id="paymentdetailtable">
                      <thead>
                        <tr>
                          <th>Sr. No.</th>
                          <th>Transaction ID</th>
                          
                          <th>Order No.</th>
                          <th>Payment Method</th>
                          <th class="text-right">Pay Amount</th>
                          <th>Entry Date</th>
                          <th>Payment Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if(!empty($paymenttransactiondata)) {  ?>
                          <?php $srno=0; foreach ($paymenttransactiondata as $row) { ?>
                            <tr>
                              <td><?=++$srno?></td>
                              <td>
                                <?=$row['transactionid']?>
                              </td>
                              <td><?=$row['ordernumber'];?></td>
                              
                              <td>
                                <?=$row['paymentgetwayid']?>
                              </td>
                              <td class="text-right"><?=number_format($row['amount'], 2, '.', ',')?></td>
                              <td><?=$this->general_model->displaydatetime($row['createddate'])?></td>
                              <td>
                                <?php if($row['status'] == "Success"){ ?>
                                <span class="btn btn-success btn-raised btn-sm">Success</span>
                                <?php } else{ ?>
                                <span class="btn btn-danger btn-raised btn-sm">Failed</span>
                                <?php } ?>
                              </td>
                              
                            </tr>  
                          <? } ?>
                        <? }else{ ?>
                          <!-- <tr>
                            <td colspan="3" style="text-align: center;">No data available in table</td>
                          </tr> -->
                        <? } ?>   
                      </tbody>
                    </table>
                  </div> 

                  <div class="tab-pane" id="cart">
                    <div class="inbox-mail-heading">
                        <div class="clearfix">
                          <div class="pull-left">
                            <div class="btn-group"><p class="lead" style="margin-bottom: 0;">Cart</p></div>
                          </div> 
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="panel-ctrls4" style="padding-left: 10px;"></div>
                      </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover table-padding"  id="carttable">
                      <thead>
                        <tr>
                          <th>Sr. No.</th>
                          <th>Product Name</th>
                          <th>Price</th>
                          <th>Tax</th>
                          <th>Quantity</th>
                          <th>Variants</th>
                          <th>Date</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div> 
                </div>
              </div>
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
        <h4 class="modal-title">Product Review</h4>
      </div>
      <div class="modal-body">
              
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $('.list-group-item').on('click', function() {
    $('.list-group-item').removeClass('active');
    $(this).addClass('active');
  });
  $(".rating").raty({
    hints:false,
    halfShow : true,
    readOnly: true,
    score: function() {
      return $(this).attr("data-score");
    }
  });
  function displayproductreview(id){
    var message = $('#message'+id).html();
    $('.modal-body').html(message.replace(/&nbsp;/g, ' '));

  }
</script>