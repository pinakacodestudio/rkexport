<div class="row mb-xl">
    <div class="col-md-12">
        <div class="panel-body no-padding">
            <div class="pull-left" style="width: 40%;font-size: 12px;color: #000;">                            
                <img src="<?php echo MAIN_LOGO_IMAGE_URL.$invoicesettingdata['logo']; ?>" alt="$invoicesettingdata['logo']">
                <address class="mt-md mb-md">
                  <?php if($invoicesettingdata['address']!=''){ ?>
                    <?=$invoicesettingdata['address']?><br>
                  <? } ?>
                  <?php if($invoicesettingdata['email']!=''){ ?>
                    <b>Email : </b> <?=$invoicesettingdata['email']?><br>
                  <? } ?>
                </address>
            </div>
            <div class="pull-right" style="width: 50%;font-size: 12px;color: #000;">
                <div class="pull-right" style="width: 60%">
                    <h4 style="font-size: 12px;color: #000;"><b>Billing Address</b></h4>
                    <address>
                        <?php if(!empty($creditnotedata)){ echo ucwords($creditnotedata[0]['membername']); } ?><br>
                        <?php if(!empty($creditnotedata)){ echo ucwords($creditnotedata[0]['address']); } ?><br/>
                        <b>Tel/Mobile : </b> <?php if(!empty($creditnoteorderdata)){ echo ucwords($creditnotedata[0]['mobileno']); } ?><br >
                        <b>Email : </b> <?php if(!empty($creditnotedata)){ echo $creditnotedata[0]['email']; } ?><br>
                        <b>GST No. : </b> <?php if(!empty($creditnotedata) && $creditnotedata[0]['gstno']!=''){ echo $creditnotedata[0]['gstno']; }else{ echo "&nbsp;&nbsp;-"; } ?>
                    </address>
                    <ul class="text-left list-unstyled">
                        <li><b>CN No. : </b> <?=$creditnotedata[0]['creditnotenumber']?></li>
                        <li><b>CN Date : </b> <?=date('d/m/Y',strtotime($creditnotedata[0]['createddate']))?></li>
                    </ul>
                </div>
                <?php /* <div class="pull-right" style="width: 50%">
                    <h4 style="font-size: 12px;color: #000;"><b>Delivery Address</b></h4>
                    <address>
                        <?php if(!empty($invoicedata)){ echo ucwords($invoicedata['shippingcustomername']); } ?><br>
                        <?php if(!empty($invoicedata)){ echo ucwords($invoicedata['shippingaddress']); } ?><br/>
                        <b>Tel/Mobile : </b> <?php if(!empty($invoicedata)){ echo ucwords($invoicedata['shippingmobileno']); } ?><br >
                        <b>Email : </b> <?php if(!empty($invoicedata)){ echo $invoicedata['email']; } ?><br>
                    </address>
                    <ul class="text-left list-unstyled">
                        <li><b>Invoice No. : </b>  <?=$invoicedata['erpnumber']?></li>
                        <li><b>Invoice Date : </b> <?=date('d/m/Y',strtotime($invoicedata['orderdate']))?></li>
                        <li><b>Payment Method : </b> <?=$invoicedata['paymentmethod']?></li>
                    </ul>
                </div> */ ?>
            </div>
        </div>
         <hr>
    </div>

</div>