<?php 
    $floatformat = '.';
    $decimalformat = ',';
    $style = '';
    if(isset($type)){
        $style = 'style="padding: 5px;border: 1px solid #666;font-size: 12px;"';
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        table tr td{
            padding: 5px;/* border: 1px solid #666; */font-size: 12px;
        }
    </style>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css"  />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css"  />
</head>
<body style="background-color: #FFF;">
    <div class="row">
        <div class="col-md-6" style="float: left;width:50%;">
            <p style="font-size: 14px;color: #000"><b>Name :</b> <?php if(!empty($transactiondata['transactiondetail'])){ echo ucwords($transactiondata['transactiondetail']['membername']); } ?></p>
            <p style="font-size: 14px;color: #000"><b>Order No. :</b> <?=$transactiondata['transactiondetail']['orderid']?></p>
        </div>
        
        <div class="col-md-6" style="float: right;width:50%;">
            <p class="text-right" style="font-size: 14px;color: #000"><b>Order Date :</b> <?=$transactiondata['transactiondetail']['createddate']?></p>
            <p class="text-right" style="font-size: 14px;color: #000"><b>Delivery Days :</b> <?=$transactiondata['transactiondetail']['approxdeliverydays']?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Order Packaging</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
    <table class="table table-hover table-bordered m-n invoice" width="100%" style="color: #000">
        <thead>
            <tr>
                <th <?=$style?>>Sr. No.</th>
                <th <?=$style?>>Details</th>
                <th <?=$style?>>Serial No.</th>
                <th <?=$style?> class="text-right">Qty.</th>
            </tr>
        </thead>
        <tbody>
            <?php
                for($i=0;$i<count($transactiondata['transactionproduct']);$i++){ 
                    
                    $qty = $transactiondata['transactionproduct'][$i]['quantity'];

                    if($transactiondata['transactionproduct'][$i]['productimage']!=""){
                        $productimage = $transactiondata['transactionproduct'][$i]['productimage'];
                    }else{
                        $productimage = PRODUCTDEFAULTIMAGE;
                    }
                ?>
                    <tr>
                        <td <?=$style?>><?=$i+1?></td>
                        <td <?=$style?>>
                            <?php if(!isset($hideonprint)){ ?>
                                <?php if(ORDER_PRODUCTLIST==1 && ($printtype=="order" || $printtype=='purchase-order')){ ?>
                                    <img class="pull-left thumbwidth" src="<?=PRODUCT.$productimage?>" style="margin-right: 10px;">
                                <?php } ?>
                                <?php if(INVOICE_PRODUCTLIST==1 && ($printtype=="invoice" || $printtype=='purchase-invoice')){ ?>
                                    <img class="pull-left thumbwidth" src="<?=PRODUCT.$productimage?>" style="margin-right: 10px;">
                                <?php } ?>
                                <?php if(CREDITNOTE_PRODUCTLIST==1 && ($printtype=="creditnote"/*  || $printtype=='purchase-invoice' */)){ ?>
                                    <img class="pull-left thumbwidth" src="<?=PRODUCT.$productimage?>" style="margin-right: 10px;">
                                <?php } ?>
                            <div class="pull-left" style="display: contents;"> 
                            <?php }else{ ?>
                                <div class="col-md-12 p-n"> 
                            <?php } ?>
                        <?=ucwords($transactiondata['transactionproduct'][$i]['name'])?>
                        </div>
                        </td>
                        
                        <td <?=$style?>><?=($transactiondata['transactionproduct'][$i]['serialno']!=""?$transactiondata['transactionproduct'][$i]['serialno']:"-")?></td>
                        
                        <td class="text-right" <?=$style?>><?=$qty?></td>
                    </tr>
            <?php } ?>        
        </tbody>
    </table>
    </div>
    <?php if(!empty($transactiondata['transactiondetail']['remarks'])){ ?>
    <div class="row mb-xl m-n"><b>Remarks:</b> <?=$transactiondata['transactiondetail']['remarks'];?>
    </div>
    <? } ?>
</body>
</html>