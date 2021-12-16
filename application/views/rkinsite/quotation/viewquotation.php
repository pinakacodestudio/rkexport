<div class="page-content">
    <div class="page-heading">            
        <h1>View <?=$this->session->userdata(base_url().'submenuname');?></h1>
        <small>
          	<ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?php echo $this->session->userdata(base_url().'mainmenuname'); ?></a></li>
            <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?php echo $this->session->userdata(base_url().'submenuname'); ?></a></li>
            <li class="active">View <?php echo $this->session->userdata(base_url().'submenuname'); ?></li>
          	</ol>
        </small>
    </div>

    <div class="container-fluid">        
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default">
		        <div class="panel-body">
		        	<div class="col-md-12">
						    <div class="row mb-xl">
    <div class="col-md-12">
        <?php if(!empty($invoicesettingdata)) { ?>
        <div class="pull-left" style="width: 32%">                            
            <img src="<?php echo MAIN_LOGO_IMAGE_URL.$invoicesettingdata['logo']; ?>" alt="<?php echo $invoicesettingdata['logo']; ?>" style="width: 70%">
            <address class="mt-md mb-md">
              <?php if($invoicesettingdata['address']!=''){ ?>
                <?=$invoicesettingdata['address']?><br>
              <? } ?>
             
              <?php if($invoicesettingdata['email']!=''){ ?>
                <b>Email : </b> <?=$invoicesettingdata['email']?><br>
              <? } ?>
            </address>
        </div>
        <? } ?>
        <div class="pull-right" style="width: 50%">
            <div class="pull-right" style="width: 50%">
                <h4 style="font-size: 15px;"><b>Billing Address</b></h4>
                <address>
                    <?php if(!empty($quotationdata['quotationdetail'])){ echo ucwords($quotationdata['quotationdetail']['customername']); } ?><br>
                    <?php if(!empty($quotationdata['quotationdetail'])){ echo ucwords($quotationdata['quotationdetail']['address']); } ?><br/>
                    <b>Tel/Mobile : </b> <?php if(!empty($quotationdata['quotationdetail'])){ echo ucwords($quotationdata['quotationdetail']['mobileno']); } ?><br >
                    <b>Email : </b> <?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['email']; } ?>
                </address>
            </div>
        </div>
    </div>
</div>
<div class="row mb-xl">
    <div class="col-md-12">
        <div class="panel">
            <div class="panel-body no-padding">
                <div class="table-responsive">
                    <table class="table table-hover table-bquotationed m-n">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Product</th>
                                <th>Qty.</th>
                                <th class="text-right">Price (Excl. Tax)</th>
                                <th>HSN Code</th>
                                <?php if($quotationdata['quotationdetail']['igst']==1) { ?>
                                    <th class="text-right" style="padding: 5px;" width="8%">SGST (%)</th>
                                    <th class="text-right" style="padding: 5px;" width="8%">CGST (%)</th>
                                <?php }else{ ?>
                                    <th class="text-right" style="padding: 5px;" width="8%">IGST (%)</th>
                                <?php } ?>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $finaltotal = $subtotal = $totalmoms = $totaltaxvalue = $totalproductamount= $totalunitcost = $totalproductunitpurchased=0;
                                for($i=0;$i<count($quotationdata['quotationproduct']);$i++){ ?>
                                    <tr>
                                        <td><?=$i+1?></td>
                                        <td><?=$quotationdata['quotationproduct'][$i]['name']?></td>
                                        <td><?=$quotationdata['quotationproduct'][$i]['quantity']?></td>
                                        <td class="text-right">
                                           <?php 
                                           echo number_format(($quotationdata['quotationproduct'][$i]['price'] - ($quotationdata['quotationproduct'][$i]['price']*$quotationdata['quotationproduct'][$i]['tax']/(100 + $quotationdata['quotationproduct'][$i]['tax']))), 2, ".", ",");
                                            ?>
                                        </td>
                                       <td><?=$quotationdata['quotationproduct'][$i]['hsncode']?></td>
                                       <?php if($quotationdata['quotationdetail']['igst']==1) { ?>
                                                <td class="text-right" style="padding: 5px;"><?=number_format(($quotationdata['quotationproduct'][$i]['tax']/2), 2, ".", ",")?></td>
                                                <td class="text-right" style="padding: 5px;"><?=number_format(($quotationdata['quotationproduct'][$i]['tax']/2), 2, ".", ",")?></td>
                                        <? }else{ ?>
                                            <td class="text-right" style="padding: 5px;"><?=number_format($quotationdata['quotationproduct'][$i]['tax'], 2, ".", ",")?></td>
                                        <? } ?>
                                        <!-- <td class="text-right">
                                            <?=number_format($quotationdata['quotationproduct'][$i]['tax'], 2, ".", ",");?>
                                        </td>
                                        <td class="text-right">
                                            <?=number_format($quotationdata['quotationproduct'][$i]['tax'], 2, ".", ",");?>
                                        </td> -->
                                        <td class="text-right">
                                            <?php 

                                                $total=($quotationdata['quotationproduct'][$i]['price']*$quotationdata['quotationproduct'][$i]['quantity']); 
                                                
                                                $taxvalue = ($quotationdata['quotationproduct'][$i]['quantity']*$quotationdata['quotationproduct'][$i]['price']*$quotationdata['quotationproduct'][$i]['tax'])/(100 + $quotationdata['quotationproduct'][$i]['tax']);

                                                $totaltaxvalue = $totaltaxvalue + ($quotationdata['quotationproduct'][$i]['quantity']*$quotationdata['quotationproduct'][$i]['price']*$quotationdata['quotationproduct'][$i]['tax'])/(100 + $quotationdata['quotationproduct'][$i]['tax']);
                                                
                                                $subtotal = $subtotal + $total - $taxvalue;
                                                $finaltotal = $finaltotal + $total + $totaltaxvalue;
                                                
                                                echo number_format(($quotationdata['quotationdetail']['payableamount']), 2, '.', ',');
                                            ?>
                                        </td>
                                    </tr>
                            <?php } ?>        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

   <div class="col-md-12">
        <div class="panel">
            <div class="panel-body no-padding">
                <div class="table-responsive">
                    <table class="table table-hover table-bquotationed m-n">
                        <thead>
                            <tr>
                                <th class="text-right">Sub Total</th>
                                <th class="text-right">Tax Value</th>
                                <th class="text-right">Total Amount (INR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php
                                   
                                    $finaltotal = ($subtotal + $totaltaxvalue);
                                ?>
                                <td class="text-right"><?=number_format($subtotal, 2, '.', ',');?></td>
                                <td class="text-right"><?=number_format($totaltaxvalue, 2, '.', ',');?></td>
                                
                                <td class="text-right">
                                   <span id="finaltotal_div"><?=number_format($quotationdata['quotationdetail']['payableamount'], 2, '.', ',');?></span>
                                   <script type="text/javascript">var finaltotal = parseFloat(<?=$finaltotal?>).toFixed(2);totalamount = parseFloat(<?=$finaltotal?>).toFixed(2);</script>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
    if(count($installment)>0){
    ?>
       <div class="col-md-12">
          <h3>Installment</h3>
          <div class="panel">
              <div class="panel-body no-padding">
                  <div class="">
                      <table class="table table-hover">
                          <thead>
                              <tr>
                                  <th class="text-center">Amount</th>
                                  <th class="text-center">Percentage</th>
                                  <th class="text-center">Date</th>
                                  <th class="text-center">Payment Date</th>
                                  <!-- <th class="text-center">Status</th> -->
                              </tr>
                          </thead>
                          <tbody>
                              <?php
                              foreach ($installment as $ins) {
                              ?>
                                <tr align="center">
                                  <td><?=$ins['amount']?></td>
                                  <td><?=$ins['percentage']?></td>
                                  <td><?php 
                                  if($ins['date']!="0000-00-00"){
                                    echo $this->general_model->displaydate($ins['date']);
                                  }else{ echo "-"; }
                                  ?></td>
                                  <td><?php 
                                  if($ins['paymentdate']!="0000-00-00"){
                                    $ins['paymentdate']; }else{ echo "-"; } ?></td>
                                    <!-- <td>
                                      <?php
                                      if($ins['status']==1){
                                        $btncls="btn-success";
                                        $btntxt="Paid";
                                      }else{
                                        $btncls="btn-warning";
                                        $btntxt="Pending";
                                      }
                                      ?>
                                      <div class="dropdown">
                                        <button class="btn <?=$btncls?> btn-sm btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown<?=$ins['id'];?>"><?=$btntxt?> <span class="caret"></span></button>
                                        <ul class="dropdown-menu" role="menu">
                                          <li id="dropdown-menu">
                                            <a onclick="changeinstallmentstatus(0,<?=$ins['id']?>)">Pending</a>
                                          </li>
                                          <li id="dropdown-menu">
                                            <a onclick="changeinstallmentstatus(1,<?=$ins['id']?>)">Paid</a>
                                          </li>
                                        </ul>
                                      </div>
                                    </td> -->
                                </tr>
                              <?php } ?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
    <?php } ?>

</div>




					   </div>
				    </div>
		      </div>
		    </div>
		  </div>
		</div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->