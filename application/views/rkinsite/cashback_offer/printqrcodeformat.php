<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        /* @page { 
            size: landscape;
        } */
        
    </style>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css"  />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css"  />
</head>
<body style="background-color: #FFF;">
    <?php //require_once(APPPATH."views/".ADMINFOLDER.'Companyheader.php');?>
    <div class="row mb-md">
        <div class="col-md-12">
            <p style="font-size: 18px;color: #000"><b><?=$heading?></b></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body no-padding">
                    <?php if(!empty($cashbackofferdata) && !empty($productdata)){
                        foreach($productdata as $i=>$row){ 
                            $copies = !empty($printnoofcopies[$i])?$printnoofcopies[$i]:0;
                            
                            if($copies > 0){
                                for($j=1;$j<=$copies;$j++){ 
                                    
                                    ?>

                                    <div style="width:50%;float:left;padding:0;position: relative;">
                                        <div style="max-height:100%;height:477px;padding: 10px;border:1px dashed #373435;border-width: thin;">
                                            <!-- <img src="<?=DEFAULT_IMG.'scissors.png'?>" style="position: absolute;right: -10px;width: 20px;top:50%;margin-top: -6px;transform: rotate(90deg);"> -->
                                            <div style="text-align: center;height:100%;">
                                                <?php
                                                    $qrtext = $cashbackofferdata['id']."@".$row['productid']."@".$row['priceid']."@".$this->general_model->random_strings(8);
                                                    $src = str_replace("{encodeurlstring}",$qrtext,GENERATE_QRCODE_SRC);
                                                ?>
                                                <img src="<?=$src?>">  <!-- style="width: auto;height: 350px;" -->
                                                <p><b>Product : </b><?=$row['productname']?></p>
                                                <p><b>Offer : </b><?=$cashbackofferdata['name']?></p>
                                                <p>Scan & Earn <?=$row['earnpoints']?> Points</p>
                                                <img src="<?=DEFAULT_IMG.'scissors.png'?>" style="position: absolute;left: 50%;width: 20px;top:0;margin-top: -6px;">
                                            </div>
                                        </div>
                                    </div>


                                    <?php 

                                }   
                            }
                            ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>



