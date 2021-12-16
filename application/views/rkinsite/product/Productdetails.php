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
                <div class="col-md-4 pl-xs pr-xs mb-sm" style="width:32%;float:left;">
                    <div class="col-md-12 pl-xs pr-xs" style="border: 3px solid #e8e8e8;width:100%;float:left;">
                        <div class="col-md-4 pull-left p-n" style="float:left;width:40%;">
                            <?php
                            if($combination['sku']!=""){
                                $qrtext = "SKU:".$combination['sku']."|".$product['id']."|".$priceid;
                                $src = str_replace("{encodeurlstring}",$qrtext,GENERATE_QRCODE_SRC);
                            }else{
                                $src = DEFAULT_IMG."qrcodenotavailable.jpg";
                            }
                            echo "<img style='width: 100%;' src='".$src."'>";
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

            <div class="col-md-4 pl-xs pr-xs mb-sm" style="width:32%;float:left;">
                <div class="col-md-12 pl-xs pr-xs" style="border: 3px solid #e8e8e8;width:100%;float:left;">
                    <div class="col-md-4 pull-left p-n" style="float:left;width:40%;">
                        <?php
                        if($product['sku']!=""){
                            $qrtext = "SKU:".$product['sku']."|".$product['id']."|".$product['priceid'];
                            $src = str_replace("{encodeurlstring}",$qrtext,GENERATE_QRCODE_SRC);
                        }else{
                            $src = DEFAULT_IMG."qrcodenotavailable.jpg";
                        }
                        echo "<img style='width: 100%;' src='".$src."' class=''>";
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