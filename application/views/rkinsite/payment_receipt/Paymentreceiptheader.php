<div class="row mb-xl">
    <div class="col-md-12">
        
        <?php if(!empty($invoicesettingdata)) { ?>
        <div class="pull-left" style="width: 32%">                            
            <img src="<?php echo MAIN_LOGO_IMAGE_URL.$invoicesettingdata['logo']; ?>" alt="$invoicesettingdata['logo']" style="width: 75%">
            <address class="mt-md mb-md">
              <?php if($invoicesettingdata['businessaddressfull']!=''){ ?>
                <?=$invoicesettingdata['businessaddressfull']?><br>
              <? } ?>
              <?=$invoicesettingdata['cityname']." - ".$invoicesettingdata['postcode']." (".$invoicesettingdata['provincename'].") ".$invoicesettingdata['countryname'];?><br>
              <?php if($invoicesettingdata['email']!=''){ ?>
                <b>Email : </b> <?=$invoicesettingdata['email']?><br>
              <? } ?>
              <?php if($invoicesettingdata['gstno']!=''){ ?>
                <b>GST No. : </b> <?=$invoicesettingdata['gstno']?>
              <? } ?>
            </address>
        </div>
        <? } ?>
        <div class="pull-right" style="width: 65%">
           
            <?php $width_m = "100%"; $width = "60%"; $width_s = "60%";$align ="pull-right";  
            if($paymentreceiptdata['paymentreceiptdetail']['sellermemberid']!=0){  
                $width ="42%";
                $width_s ="58%";
                $width_m = "100%";
                $align ="pull-right";   
            }?>
            <div class="pull-right" style="width: <?=$width_m?>">
                <div class="<?=$align?>" style="width: <?=$width_s?>">
                    <h4 style="font-size: 15px;"><b>Buyer Details</b></h4>
                    <address>
                        <?php if(!empty($paymentreceiptdata['paymentreceiptdetail'])){ echo ucwords($paymentreceiptdata['paymentreceiptdetail']['membername']); } ?><br>
                        <?php if(!empty($paymentreceiptdata['paymentreceiptdetail'])){ echo ucwords($paymentreceiptdata['paymentreceiptdetail']['buyeraddress']); } ?><br/>
                        <b>Tel/Mobile : </b> <?php if(!empty($paymentreceiptdata['paymentreceiptdetail'])){ echo ucwords($paymentreceiptdata['paymentreceiptdetail']['mobileno']); } ?><br >
                        <b>Email : </b> <?php if(!empty($paymentreceiptdata['paymentreceiptdetail'])){ echo $paymentreceiptdata['paymentreceiptdetail']['email']; } ?><br >
                        <b>GST No. : </b> <?php if(!empty($paymentreceiptdata['paymentreceiptdetail']) && $paymentreceiptdata['paymentreceiptdetail']['gstno']!=''){ echo $paymentreceiptdata['paymentreceiptdetail']['gstno']; }else{ echo "&nbsp;&nbsp;-"; } ?>
                    </address>
                </div>
                <?php if($paymentreceiptdata['paymentreceiptdetail']['sellermemberid']!=0){  ?>
                    <div class="<?=$align?>" style="width: <?=$width?>">
                        <h4 style="font-size: 15px;"><b>Seller Details</b></h4>
                        <address>
                            <?php if(!empty($paymentreceiptdata['paymentreceiptdetail'])){ echo ucwords($paymentreceiptdata['paymentreceiptdetail']['sellermembername']); } ?><br>
                            <?php if(!empty($paymentreceiptdata['paymentreceiptdetail'])){ echo ucwords($paymentreceiptdata['paymentreceiptdetail']['selleraddress']); } ?><br/>
                            <b>Tel/Mobile : </b> <?php if(!empty($paymentreceiptdata['paymentreceiptdetail'])){ echo ucwords($paymentreceiptdata['paymentreceiptdetail']['sellermobileno']); } ?><br >
                            <b>Email : </b> <?php if(!empty($paymentreceiptdata['paymentreceiptdetail'])){ echo $paymentreceiptdata['paymentreceiptdetail']['selleremail']; } ?><br >
                            <b>GST No. : </b> <?php if(!empty($paymentreceiptdata['paymentreceiptdetail']) && $paymentreceiptdata['paymentreceiptdetail']['sellergstno']!=''){ echo $paymentreceiptdata['paymentreceiptdetail']['sellergstno']; }else{ echo "&nbsp;&nbsp;-"; } ?>
                        </address>
                    </div>
                <?php }else{ ?>
                    
                <?php } ?>
            </div>
            <div class="pull-right" style="width: <?=$width_m?>">
                <div class="pull-right" style="width: <?=$width_s?>">
                    <table class="m-n" width="90%" style="color: #000">
                        <tbody>
                            <tr>
                                <th width="50%"><?=$heading?> No.</th>
                                <th class="width8">:</th>
                                <td class=""><?php echo $paymentreceiptdata['paymentreceiptdetail']['paymentreceiptno'];?></td>  
                            </tr>
                            <tr>
                                <th width="50%">Transaction Date</th>
                                <th class="width8">:</th>
                                <td class=""><?php if($paymentreceiptdata['paymentreceiptdetail']['transactiondate']!='0000-00-00'){ echo $this->general_model->displaydate($paymentreceiptdata['paymentreceiptdetail']['transactiondate']); }else{ echo "-"; } ?></td>
                            </tr>
                            <tr>
                                <th width="50%">Transaction Type</th>
                                <th class="width8">:</th>
                                <td class=""><?php echo $paymentreceiptdata['paymentreceiptdetail']['transactiontype'];?></td>
                            </tr>
                        </tbody>
                    </table> 
                </div>
            </div>
            
        </div>
    </div>
</div>