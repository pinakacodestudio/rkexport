<?php
$style = '';
if(isset($type)){
    $style = 'style="padding: 5px;border: 1px solid #666;font-size: 12px;"';
}
if(isset($printtype) && $printtype=='creditnote' && $transactiondata['transactiondetail']['creditnotetype']==1){ ?>
    <table class="table table-hover table-bordered m-n invoice" width="100%" style="color: #000">
    <thead>
        <tr>
            <th rowspan="2" <?=$style?>>Sr. No.</th>
            <th rowspan="2" <?=$style?>>Details</th>
            <th rowspan="2" <?=$style?> class="text-right">Price<br>(Excl. Tax)</th>
            <?php 
            if($transactiondata['transactiondetail']['gstprice']==1){ ?>
                <?php if($transactiondata['transactiondetail']['igst']==1) { ?>
                    <th class="text-right" <?=$style?> width="8%">SGST (%)</th>
                    <th class="text-right" <?=$style?> width="8%">CGST (%)</th>
                <?php }else{ ?>
                    <th class="text-right" <?=$style?> width="8%">IGST (%)</th>
                <?php }
            }else{
                if($transactiondata['transactiondetail']['igst']==1) { ?>
                    <th class="text-right" <?=$style?> width="8%">SGST (%)</th>
                    <th class="text-right" <?=$style?> width="8%">CGST (%)</th>
                <?php }else{ ?>
                    <th class="text-right" <?=$style?> width="8%">IGST (%)</th>
                <?php }
            }
            ?>
            <th rowspan="2" class="text-right" <?=$style?>>Amount (<?=CURRENCY_CODE?>)</th>
        </tr>
        <tr>
            <?php 
            if($transactiondata['transactiondetail']['gstprice']==1){ ?>
                <?php if($transactiondata['transactiondetail']['igst']==1) { ?>
                    <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                    <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                <?php }else{ ?>
                    <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                <?php } 
            }else{
                if($transactiondata['transactiondetail']['igst']==1) { ?>
                    <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                    <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                <?php }else{ ?>
                    <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                <?php }
            } 
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
            $finaltotal = $subtotal = $totaltaxvalue =0; 

            if(!empty($transactiondata['transactionofferdata'])){
                for($i=0;$i<count($transactiondata['transactionofferdata']);$i++){ 
                    
                    $sgst = $cgst = $igst = 0;
                    $sgstamount = $cgstamount = $igstamount = 0;

                    if($transactiondata['transactiondetail']['igst']==1) {
                        $sgst = $transactiondata['transactionofferdata'][$i]['tax']/2;
                        $cgst = $transactiondata['transactionofferdata'][$i]['tax']/2;
                    }else{
                        $igst = $transactiondata['transactionofferdata'][$i]['tax'];
                    }

                    $price = $transactiondata['transactionofferdata'][$i]['amount']; 
                ?>
                    <tr>
                        <td rowspan="2" <?=$style?>><?=$i+1?></td>
                        <td rowspan="2" <?=$style?>><?=ucfirst($transactiondata['transactionofferdata'][$i]['creditnotedetails'])?></td>
                        <td rowspan="2" class="text-right" <?=$style?>><?php echo number_format($price, 2, ".", ","); ?></td>
                        <?php 
                        if($transactiondata['transactiondetail']['gstprice']==1){ ?>
                            <?php if($transactiondata['transactiondetail']['igst']==1) { ?>
                                <td class="text-right" <?=$style?>><?=number_format($sgst, 2, ".", ",")?></td>
                                <td class="text-right" <?=$style?>><?=number_format($cgst, 2, ".", ",")?></td>
                            <?php }else{ ?>
                                <td class="text-right" <?=$style?>><?=number_format($igst, 2, ".", ",")?></td>
                            <?php }
                        }else{
                            if($transactiondata['transactiondetail']['igst']==1) { ?>
                                <td class="text-right" <?=$style?>><?=number_format($sgst, 2, ".", ",")?></td>
                                <td class="text-right" <?=$style?>><?=number_format($cgst, 2, ".", ",")?></td>
                            <?php }else{ ?>
                                <td class="text-right" <?=$style?>><?=number_format($igst, 2, ".", ",")?></td>
                            <?php }
                        } ?>
                        <td rowspan="2" class="text-right" <?=$style?>>
                            <?php 

                                $taxvalue = ($price * $transactiondata['transactionofferdata'][$i]['tax']) / 100;
                                $total = $price + $taxvalue;

                                if($transactiondata['transactiondetail']['igst']==1) {
                                    $sgstamount = $taxvalue/2;
                                    $cgstamount = $taxvalue/2;
                                }else{
                                    $igstamount = $taxvalue;
                                }

                                $totaltaxvalue = $totaltaxvalue + $taxvalue;

                                $subtotal = $subtotal + $total;
                                $finaltotal = $finaltotal + $total;
                                
                                echo number_format(($total), 2, '.', ',');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <?php 
                        if($transactiondata['transactiondetail']['gstprice']==1){ ?>
                            <?php if($transactiondata['transactiondetail']['igst']==1) { ?>
                                <td class="text-right" <?=$style?>>
                                    <?=number_format($sgstamount, 2, ".", ",");?>
                                </td>
                                <td class="text-right" <?=$style?>>
                                    <?=number_format($cgstamount, 2, ".", ",");?>
                                </td>
                            <?php }else{ ?>
                                <td class="text-right" <?=$style?>>
                                    <?=number_format($igstamount, 2, ".", ",");?>
                                </td>
                            <?php } 
                        }else{
                            if($transactiondata['transactiondetail']['igst']==1) { ?>
                                <td class="text-right" <?=$style?>>
                                    <?=number_format($sgstamount, 2, ".", ",");?>
                                </td>
                                <td class="text-right" <?=$style?>>
                                    <?=number_format($cgstamount, 2, ".", ",");?>
                                </td>
                            <?php }else{ ?>
                                <td class="text-right" <?=$style?>>
                                    <?=number_format($igstamount, 2, ".", ",");?>
                                </td>
                            <?php }
                        } ?>
                    </tr>
            <?php }
            }else{ ?>  
                <tr colspan="6" class="text-center">No data available in table.</tr>
            <?php } ?>      
    </tbody>
</table>
<?php }else{ ?>
    <table class="table table-hover table-bordered m-n invoice" width="100%" style="color: #000">
        <thead>
            <tr>
                <th rowspan="2" <?=$style?>>Sr. No.</th>
                <th rowspan="2" <?=$style?>>Details</th>
                <th rowspan="2" <?=$style?>>HSN Code</th>
                <?php if(isset($printtype) && $printtype=="invoice"){ ?>
                <th rowspan="2" <?=$style?>>Remarks</th>
                <?php } ?>
                <th rowspan="2" <?=$style?> class="text-right">Qty.</th>
                <th rowspan="2" <?=$style?> class="text-right">Price<br>(Excl. Tax)</th>
                <?php 
                if($transactiondata['transactiondetail']['gstprice']==1){
                    if($transactiondata['transactiondetail']['displaydiscountcolumn']>0){ ?>
                        <th class="text-right width8" <?=$style?>>Discount (%)</th>
                    <?php } ?>
                    <?php if($transactiondata['transactiondetail']['igst']==1) { ?>
                        <th class="text-right" <?=$style?> width="8%">SGST (%)</th>
                        <th class="text-right" <?=$style?> width="8%">CGST (%)</th>
                    <?php }else{ ?>
                        <th class="text-right" <?=$style?> width="8%">IGST (%)</th>
                    <?php }
                }else{
                    if($transactiondata['transactiondetail']['igst']==1) { ?>
                        <th class="text-right" <?=$style?> width="8%">SGST (%)</th>
                        <th class="text-right" <?=$style?> width="8%">CGST (%)</th>
                    <?php }else{ ?>
                        <th class="text-right" <?=$style?> width="8%">IGST (%)</th>
                    <?php }
                    if($transactiondata['transactiondetail']['displaydiscountcolumn']>0){ ?>
                        <th class="text-right width8" <?=$style?>>Discount (%)</th>
                    <?php }
                }
                ?>
                <th rowspan="2" class="text-right" <?=$style?>>Amount (<?=CURRENCY_CODE?>)</th>
            </tr>
            <tr>
                <?php 
                if($transactiondata['transactiondetail']['gstprice']==1){
                    if($transactiondata['transactiondetail']['displaydiscountcolumn']==1) { ?>
                        <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                    <?php } ?>
                    <?php if($transactiondata['transactiondetail']['igst']==1) { ?>
                        <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                        <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                    <?php }else{ ?>
                        <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                    <?php } 
                }else{
                    if($transactiondata['transactiondetail']['igst']==1) { ?>
                        <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                        <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                    <?php }else{ ?>
                        <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                    <?php }

                    if($transactiondata['transactiondetail']['displaydiscountcolumn']==1) { ?>
                        <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
                    <?php }
                } 
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
                $finaltotal = $subtotal = $totaltaxvalue =0; 

                $uniquearr = (array_unique(array_column($transactiondata['transactionproduct'],'orderid')));
                $uniquearrserialno = (array_unique(array_column($transactiondata['transactionproduct'],'serialno')));


                for($i=0;$i<count($transactiondata['transactionproduct']);$i++){ 
                    
                    $sgst = $cgst = $igst = 0;
                    $sgstamount = $cgstamount = $igstamount = $discountamount = 0;

                    if($transactiondata['transactiondetail']['igst']==1) {
                        $sgst = $transactiondata['transactionproduct'][$i]['tax']/2;
                        $cgst = $transactiondata['transactionproduct'][$i]['tax']/2;
                    }else{
                        $igst = $transactiondata['transactionproduct'][$i]['tax'];
                    }

                    $qty = $transactiondata['transactionproduct'][$i]['quantity'];
                    $price = $transactiondata['transactionproduct'][$i]['price']; 

                    $originalprice = $transactiondata['transactionproduct'][$i]['originalprice']; 
                    $discount = $transactiondata['transactionproduct'][$i]['discount'];
                    $discountamount = ($originalprice*$qty*$discount)/100;

                    if($transactiondata['transactionproduct'][$i]['productimage']!=""){
                        $productimage = $transactiondata['transactionproduct'][$i]['productimage'];
                    }else{
                        $productimage = PRODUCTDEFAULTIMAGE;
                    }
                ?>
                    <tr>
                        <td rowspan="2" <?=$style?>><?=$i+1?></td>
                        <td rowspan="2" <?=$style?>>
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
                        <?=ucwords($transactiondata['transactionproduct'][$i]['name'])?><br><br>
                        <?php if(isset($printtype) && $printtype=="creditnote"){ ?>
                            <b>Invoice No.:</b><?=$transactiondata['transactionproduct'][$i]['invoiceno']?>
                        <?php }else if(isset($printtype) && ($printtype=='invoice' || $printtype=='purchase-invoice')){ 
                            if(count($uniquearr)>1){  ?>
                            <b>GRN No.:</b><?=$transactiondata['transactionproduct'][$i]['orderid']?><br>
                        <?php } 
                        }else if(isset($printtype) && $printtype=='goods_received_notes'){ 
                            if(count($uniquearr)>1){  ?>
                            <b>Order No.:</b><?=$transactiondata['transactionproduct'][$i]['orderid']?><br>
                        <?php } } ?>
                            <?php if(!empty($transactiondata['transactionproduct'][$i]['serialno'])) { if($transactiondata['transactionproduct'][$i]['serialno']!=''){ ?><b>Serial No.:</b><?=$transactiondata['transactionproduct'][$i]['serialno']?><?php } } ?>
                        </div>
                        </td>
                        
                        <td rowspan="2" <?=$style?>><?=($transactiondata['transactionproduct'][$i]['hsncode']!=""?$transactiondata['transactionproduct'][$i]['hsncode']:"-")?></td>
                        <?php if(isset($printtype) && $printtype=="invoice"){ ?>
                        <td rowspan="2" <?=$style?>><?=($transactiondata['transactionproduct'][$i]['remarks']!=""?$transactiondata['transactionproduct'][$i]['remarks']:"-")?></td>
                        <?php } ?>
                        <td rowspan="2" class="text-right" <?=$style?>><?=$qty?></td>
                        <td rowspan="2" class="text-right" <?=$style?>>
                        <?php 
                            //$price = number_format(($price - ($price * $transactiondata['transactionproduct'][$i]['tax']) / 100), 2, ".", ",");
                            echo number_format($price, 2, ".", ",");
                        ?>
                        </td>
                        <?php 
                        if($transactiondata['transactiondetail']['gstprice']==1){
                            if($transactiondata['transactiondetail']['displaydiscountcolumn']>0){
                                if($discount>0){ ?>
                                    <td class="text-right" <?=$style?>><?=number_format($discount, 2, ".", ",")?></td>
                                <?php }else{ ?>
                                    <td class="text-right" <?=$style?>>-</td>
                                <?php } ?>
                            <?php } ?>
                            <?php if($transactiondata['transactiondetail']['igst']==1) { ?>
                                    <td class="text-right" <?=$style?>><?=number_format($sgst, 2, ".", ",")?></td>
                                    <td class="text-right" <?=$style?>><?=number_format($cgst, 2, ".", ",")?></td>
                            <?php }else{ ?>
                                <td class="text-right" <?=$style?>><?=number_format($igst, 2, ".", ",")?></td>
                            <?php }
                        }else{
                            if($transactiondata['transactiondetail']['igst']==1) { ?>
                                <td class="text-right" <?=$style?>><?=number_format($sgst, 2, ".", ",")?></td>
                                <td class="text-right" <?=$style?>><?=number_format($cgst, 2, ".", ",")?></td>
                            <?php }else{ ?>
                                <td class="text-right" <?=$style?>><?=number_format($igst, 2, ".", ",")?></td>
                            <?php }
                            if($transactiondata['transactiondetail']['displaydiscountcolumn']>0){
                                if($discount>0){ ?>
                                    <td class="text-right" <?=$style?>><?=number_format($discount, 2, ".", ",")?></td>
                                <?php }else{ ?>
                                    <td class="text-right" <?=$style?>>-</td>
                                <?php } ?>
                            <?php }
                        } ?>
                        <td rowspan="2" class="text-right" <?=$style?>>
                            <?php 

                                $totalprice = (($price * $transactiondata['transactionproduct'][$i]['quantity']));
                                $taxvalue = (($price * $transactiondata['transactionproduct'][$i]['quantity']) * $transactiondata['transactionproduct'][$i]['tax']) / 100;
                                $total = $totalprice + $taxvalue;

                                if($transactiondata['transactiondetail']['igst']==1) {
                                    $sgstamount = $taxvalue/2;
                                    $cgstamount = $taxvalue/2;
                                }else{
                                    $igstamount = $taxvalue;
                                }

                                $totaltaxvalue = $totaltaxvalue + $taxvalue;

                                $subtotal = $subtotal + $total;
                                $finaltotal = $finaltotal + $total;
                                
                                echo number_format(($total), 2, '.', ',');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <?php 
                        if($transactiondata['transactiondetail']['gstprice']==1){
                            if($transactiondata['transactiondetail']['displaydiscountcolumn']>0){                 
                                if($discountamount > 0) { ?>
                                    <td class="text-right" <?=$style?>>
                                        <?=number_format($discountamount, 2, ".", ",");?>
                                    </td>
                                <?php }else{ ?>
                                    <td class="text-right" <?=$style?>>-</td>
                                <?php }} ?>
                            <?php if($transactiondata['transactiondetail']['igst']==1) { ?>
                            <td class="text-right" <?=$style?>>
                                <?=number_format($sgstamount, 2, ".", ",");?>
                            </td>
                            <td class="text-right" <?=$style?>>
                                <?=number_format($cgstamount, 2, ".", ",");?>
                            </td>
                            <?php }else{ ?>
                            <td class="text-right" <?=$style?>>
                                <?=number_format($igstamount, 2, ".", ",");?>
                            </td>
                            <?php } 
                        }else{
                            if($transactiondata['transactiondetail']['igst']==1) { ?>
                                <td class="text-right" <?=$style?>>
                                    <?=number_format($sgstamount, 2, ".", ",");?>
                                </td>
                                <td class="text-right" <?=$style?>>
                                    <?=number_format($cgstamount, 2, ".", ",");?>
                                </td>
                            <?php }else{ ?>
                                <td class="text-right" <?=$style?>>
                                    <?=number_format($igstamount, 2, ".", ",");?>
                                </td>
                            <?php }
                            if($transactiondata['transactiondetail']['displaydiscountcolumn']>0){                 
                                if($discountamount > 0) { ?>
                                    <td class="text-right" <?=$style?>>
                                        <?=number_format($discountamount, 2, ".", ",");?>
                                    </td>
                                <?php }else{ ?>
                                    <td class="text-right" <?=$style?>>-</td>
                                <?php }
                            }
                        } ?>
                    </tr>
            <?php } ?>        
        </tbody>
    </table>
<?php } ?>