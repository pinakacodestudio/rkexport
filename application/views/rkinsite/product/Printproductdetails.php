<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css"  />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css"  />
    <style>
    @media print {
        .pageBreak {
            page-break-after: always;
        }
    }
    </style>
</head>
<body style="background-color: #FFF;">
    <div class="row">
        <div class="col-md-12">
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>View Product Details</b></u></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php if(!empty($productdata)){
                foreach($productdata as $k=>$product){ 
                    $pagebreakclass = '';
                    if(($k+1) % 5 == 0){
                        $pagebreakclass = "pageBreak";
                    }
                    $displayproductname = '';    
                    if($productname){
                        $displayproductname = '<b>'.$product['name'].'</b><br>';
                    }
                    ?>
                    <?php 
                    if($product['isuniversal']==0){
                        foreach($product['variant'] as $priceid=>$combination){ 
                            $displaysku = $displayproductprice = $displayvariant = '';
                            if($sku){
                                $displaysku = $combination['sku']!=''?'<b>SKU : </b>'.$combination['sku'].'<br>':'';
                            }
                            if($productprice){
                                $displayproductprice = '<div class="text-center"><b>'.CURRENCY_CODE.'</b> '.$combination['price'].'</div>';
                            }
                            if($variant){
                                $variants_html = '';
                                $variants = $combination['variants'];
                                
                                foreach($combination['variants'] as $variant){
                                    $variants_html .= '<b>'.$variant['variantname'].' : </b>'.$variant['variantvalue'].'<br>';
                                }
                                $displayvariant = $variants_html;
                            }?>
                            <div class="col-md-4 pl-xs pr-xs mb-sm" style="width:33.33%;float:left;">
                                <div class="col-md-12 pl-xs pr-xs" style="border: 3px solid #e8e8e8;width:100%;float:left;">
                                    <div class="col-md-4 pull-left p-n" style="float:left;width:40%;">
                                        <?php
                                        $id = $product['id'].','.$priceid;
                                        echo "<img style='width: 100%;' src='".str_replace("{encodeurlstring}",$id,GENERATE_QRCODE_SRC)."'>";
                                        ?>
                                        <?=$displayproductprice?>
                                    </div>
                                    <div class="col-md-8 p-n mt-sm" style="float:left;width:60%;">
                                        <p><?=$displayproductname.$displaysku.$displayvariant?></p>
                                    </div>
                                </div>
                            </div>
                    <?php }
                    }else{ ?>

                        <?php $displayproductname = $displaysku = $displayproductprice = '';    
                        if($productname){
                            $displayproductname = '<b>'.$product['name'].'</b><br>';
                        }
                        if($sku){
                            $displaysku = $product['sku']!=''?'<b>SKU : </b>'.$product['sku']:'';
                        }
                        if($productprice){
                            $displayproductprice = '<div class="text-center"><b>'.CURRENCY_CODE.'</b> '.$product['price'].'</div>';
                        }?>

                        <div class="col-md-4 pl-xs pr-xs mb-sm" style="width:33.33%;float:left;">
                            <div class="col-md-12 pl-xs pr-xs" style="border: 3px solid #e8e8e8;width:100%;float:left;">
                                <div class="col-md-4 pull-left p-n" style="float:left;width:40%;">
                                    <?php
                                    echo "<img style='width: 100%;' src='".str_replace("{encodeurlstring}",$product['id'].",0",GENERATE_QRCODE_SRC)."' class=''>";
                                    ?>
                                    <?=$displayproductprice?>
                                </div>
                                <div class="col-md-8 p-n mt-sm" style="float:left;width:60%;">
                                    <p><?=$displayproductname.$displaysku?></p>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
            <?php }
            } ?>
        </div>
    </div>
</body>
</html>



