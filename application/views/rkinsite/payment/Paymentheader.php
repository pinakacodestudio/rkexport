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
           
            <div class="pull-right" style="width: 100%">
                <div class="pull-right" style="width: 58%">
                    <h4 style="font-size: 15px;"><b>Vendor Details</b></h4>
                    <address>
                        <?php if(!empty($paymentreceiptdata['paymentreceiptdetail'])){ echo ucwords($paymentreceiptdata['paymentreceiptdetail']['vendorname']); } ?><br>
                        <?php if(!empty($paymentreceiptdata['paymentreceiptdetail'])){ echo ucwords($paymentreceiptdata['paymentreceiptdetail']['vendoraddress']); } ?><br/>
                        <b>Tel/Mobile : </b> <?php if(!empty($paymentreceiptdata['paymentreceiptdetail'])){ echo ucwords($paymentreceiptdata['paymentreceiptdetail']['mobileno']); } ?><br >
                        <b>Email : </b> <?php if(!empty($paymentreceiptdata['paymentreceiptdetail'])){ echo $paymentreceiptdata['paymentreceiptdetail']['email']; } ?><br >
                        <b>GST No. : </b> <?php if(!empty($paymentreceiptdata['paymentreceiptdetail']) && $paymentreceiptdata['paymentreceiptdetail']['gstno']!=''){ echo $paymentreceiptdata['paymentreceiptdetail']['gstno']; }else{ echo "&nbsp;&nbsp;-"; } ?>
                    </address>
                </div>
            </div>
            <div class="pull-right" style="width: 100%">
                <div class="pull-right" style="width: 58%">
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