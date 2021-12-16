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
                    <?php if(!empty($quotationdata['quotationdetail'])){ echo ucwords($quotationdata['quotationdetail']['shippingmembername']); } ?><br>
                    <?php if(!empty($quotationdata['quotationdetail'])){ echo ucwords($quotationdata['quotationdetail']['shippingaddress']); } ?><br/>
                    <b>Tel/Mobile : </b> <?php if(!empty($quotationdata['quotationdetail'])){ echo ucwords($quotationdata['quotationdetail']['shippingmobileno']); } ?><br >
                    <b>Email : </b> <?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['shippingemail']; } ?><br >
                </address>
            </div>
            <div class="pull-right" style="width: 50%">
                <h4 style="font-size: 15px;"><b>Billing Address</b></h4>
                <address>
                    <?php if(!empty($quotationdata)){ echo ucwords($quotationdata['quotationdetail']['membername']); } ?><br>
                    <?php if(!empty($quotationdata)){ echo ucwords($quotationdata['quotationdetail']['address']); } ?><br/>
                    <b>Tel/Mobile : </b> <?php if(!empty($quotationdata)){ echo ucwords($quotationdata['quotationdetail']['mobileno']); } ?><br >
                    <b>Email : </b> <?php if(!empty($quotationdata)){ echo $quotationdata['quotationdetail']['email']; } ?><br>
                </address>
                <ul class="text-left list-unstyled">
                    <li><b>Quotation No. : </b> <?=$quotationdata['quotationdetail']['quotationid']?></li>
                    <li><b>Quotation Date : </b> <?=$quotationdata['quotationdetail']['createddate']?></li>
                    <li><b>Payment Method : </b>
                    <?php
                      if($quotationdata['quotationdetail']['paymenttype']==1){
                          echo "COD";
                      }else if($quotationdata['quotationdetail']['paymenttype']==2){
                          echo "Online Payment";
                      }else if($quotationdata['quotationdetail']['paymenttype']==3){
                          echo "Advance Payment";
                      }else if($quotationdata['quotationdetail']['paymenttype']==4){
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