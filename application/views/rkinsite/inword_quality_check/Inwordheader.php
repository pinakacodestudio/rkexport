<div class="row mb-xl">
    <div class="col-md-12">
        
        <?php if(!empty($invoicesettingdata)) { ?>
        <div class="pull-left" style="width: 40%">                            
            <?php if(file_exists(SETTINGS_PATH.$invoicesettingdata['logo'])){ ?>
            <img src="<?php echo MAIN_LOGO_IMAGE_URL.$invoicesettingdata['logo']; ?>" alt="<?=$invoicesettingdata['logo']?>" style="width: auto;max-width: 100%;max-height: 120px;">
            <?php } ?>
            <address class="mt-md mb-md">
              <?php if($invoicesettingdata['businessaddressfull']!=''){ ?>
                <?=$invoicesettingdata['businessaddressfull']?><br>
              <?php } ?>
              <?=$invoicesettingdata['cityname']." - ".$invoicesettingdata['postcode']." (".$invoicesettingdata['provincename'].") ".$invoicesettingdata['countryname'];?><br>
              <?php if($invoicesettingdata['invoiceemail']!=''){ ?>
                <b>Email : </b> <?=$invoicesettingdata['invoiceemail']?><br>
              <?php } ?>
              <?php if($invoicesettingdata['gstno']!=''){ ?>
              <b>GST No. : </b> <?=$invoicesettingdata['gstno']?>
              <?php } ?>
            </address>
        </div>
        <?php } ?>
        <div class="pull-right pt-xl" style="width: 60%">
            
            <div class="pull-right" style="width: 60%">
                <b>Order ID: </b><?=$headerdata['orderid']?><br>
                <b>Vendor Name : </b><?=$headerdata['vendor']?><br>
                <b>GRN No. : </b><?=$headerdata['grnumber']?><br>
                <b>GRN Date : </b><?=$this->general_model->displaydate($headerdata['grndate'])?><br>
                <b>Inword Q.C. Date : </b><?=$this->general_model->displaydatetime($headerdata['createddate'])?><br>
                <b>Tested By : </b><?=$headerdata['addedby']?><br>
                <b>Status : </b><?php switch($headerdata['status']){case 0: echo "Pending";break; case 1 : echo "Partially";break; case 2 : echo "Complete";break; case 3 : echo "Cancel";break;}?><br>
            </div>
        </div>
    </div>
</div>