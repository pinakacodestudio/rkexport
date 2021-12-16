<div class="row mb-xl">
    <div class="col-md-12">
        
        <?php if(!empty($invoicesettingdata)) { ?>
        <div class="pull-left" style="width: 32%">                            
            <img src="<?php echo MAIN_LOGO_IMAGE_URL.$invoicesettingdata['logo']; ?>" alt="$invoicesettingdata['logo']" style="width: 75%">
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
        <div class="pull-right" style="width: 60%">
            <div class="pull-left" style="width: 50%">
                <h4 style="font-size: 15px;"><b>Shipping Address</b></h4>
                <address>
                    <?php if(!empty($orderdata['orderdetail'])){ echo ucwords($orderdata['orderdetail']['shippingmembername']); } ?><br>
                    <?php if(!empty($orderdata['orderdetail'])){ echo ucwords($orderdata['orderdetail']['shippingaddress']); } ?><br/>
                    <b>Tel/Mobile : </b> <?php if(!empty($orderdata['orderdetail'])){ echo ucwords($orderdata['orderdetail']['shippingmobileno']); } ?><br >
                    <b>Email : </b> <?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['shippingemail']; } ?><br >
                </address>
            </div>
            <div class="pull-right" style="width: 50%">
                <h4 style="font-size: 15px;"><b>Billing Address</b></h4>
                <address>
                    <?php if(!empty($orderdata)){ echo ucwords($orderdata['orderdetail']['membername']); } ?><br>
                    <?php if(!empty($orderdata)){ echo ucwords($orderdata['orderdetail']['address']); } ?><br/>
                    <b>Tel/Mobile : </b> <?php if(!empty($orderdata)){ echo ucwords($orderdata['orderdetail']['mobileno']); } ?><br >
                    <b>Email : </b> <?php if(!empty($orderdata)){ echo $orderdata['orderdetail']['email']; } ?><br>
                </address>
                <ul class="text-left list-unstyled">
                    <?php if($orderdata['orderdetail']['quotationid'] != '') { ?>
                        <li><b>Quotation No. : </b> 
                            <?php if(isset($orderdata['orderdetail']['quotationid'])){
                                echo $orderdata['orderdetail']['quotationid'];
                            }?>
                        </li>
                    <?php }?>
                    <li><b>Order No. : </b> <?=$orderdata['orderdetail']['orderid']?></li>
                    <li><b>Order Date : </b> <?=$orderdata['orderdetail']['createddate']?></li>
                    <li><b>Payment Method : </b>
                    <?php
                      if($orderdata['orderdetail']['paymenttype']==1){
                          echo "COD";
                      }else if($orderdata['orderdetail']['paymenttype']==2){
                          echo isset($this->Paymentgatewaytype[$orderdata['orderdetail']['paymentgetwayid']]) ? ucwords($this->Paymentgatewaytype[$orderdata['orderdetail']['paymentgetwayid']]) : '-';
                      }else if($orderdata['orderdetail']['paymenttype']==3){
                          echo "Advance Payment";
                      }else if($orderdata['orderdetail']['paymenttype']==4){
                          echo "Partial Payment";
                      }
                    ?>
                    </li>
                </ul>
            </div>
        </div>
        
        <hr>
    </div>
</div>