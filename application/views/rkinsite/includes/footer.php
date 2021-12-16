<!-- START FOOTER -->
<?php if(FOOTER==1){ ?>
<?php if(STARTDATETIME!="0 00:00:00" && EXPIRYDATETIME !="0000-00-00 00:00:00"){?>
<div style="margin-bottom:35px; background-color:#ccc; height:22px;">
    <marquee behaviour="scroll" direction="left" class="marquee" style="color:red;margin-top:3px;"><strong>Under Maintenance From <?php echo date('d/m/Y h:i A',strtotime(STARTDATETIME));?> To <?php echo date('d/m/Y h:i A',strtotime(EXPIRYDATETIME))?></strong></marquee>
    <div>
<?php }?>
<footer role="contentinfo">
    <div class="clearfix">
        <ul class="list-unstyled list-inline pull-left">
            
            <li>
            <?php if(COPYRIGHT==1){ ?>  
            <h6 style="margin: 0;">Copyright © <?=date("Y")?> RK Infotech All rights reserved.</h6>
            <?php } ?>
            </li>
            <li>
            <?php if(BRANDING_ALLOW == "1"){ ?>
                <?php if(BRANDING_TYPE == "1"){ 
                    $brandingtype = "Powered By";
                }else{
                    $brandingtype = "Pioneered By";
                } ?>
                <h6 style="margin: 0;text-align: right;"><?=$brandingtype?>
                <?php if(BRANDING_LOGO!=""){ ?>
                    <a href="<?=BRANDING_URL?>" target="_blank"><img src="<?php echo MAIN_LOGO_IMAGE_URL.BRANDING_LOGO;?>" width="65" height="25"></a>
                <?php }else{ ?> 
                    <a href="<?=BRANDING_URL?>" target="_blank"><?=COMPANY_NAME?></a>
                <?php } ?>
                </h6>
            <?php } ?>
            </li>
        </ul>
    </div>
</footer>
<?php } ?>
<!-- END FOOTER -->
