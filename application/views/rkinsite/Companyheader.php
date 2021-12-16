<div class="row mb-xl">
    <div class="col-md-12">
        <div class="panel-body p-n pt-md pb-xs">
            <div class="pull-left" style="width: 40%;font-size: 12px;color: #000;display: inline-block;">                            
                <img src="<?php echo MAIN_LOGO_IMAGE_URL.$invoicesettingdata['logo']; ?>" alt="<?php echo $invoicesettingdata['logo']; ?>" style="max-height: 78px;width:auto;max-width: 80%;">
            </div>
            <div class="pull-right" style="width: 60%;font-size: 12px;color: #000;display: inline-block;">
                <address style="text-align:right;">
                  <?php if($invoicesettingdata['address']!=''){ ?>
                    <?=$invoicesettingdata['address']?><br><br>
                  <?php } ?>
                  <?php if($invoicesettingdata['email']!=''){ ?>
                    <b>Email : </b> <?=explode(",",$invoicesettingdata['email'])[0]?><br>
                  <?php } ?>
                </address>
            </div>
        </div>
        <hr>
    </div>

</div>