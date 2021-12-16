<?php
$style = '';
if(isset($type)){
    $style = 'style="padding: 5px;border: 1px solid #666;font-size: 12px;"';
}
?>
<table class="table table-hover table-bordered m-n invoice" width="100%" style="color: #000">
    <thead>
        <tr>
            <th rowspan="2" <?=$style?>>Sr. No.</th>
            <th rowspan="2" <?=$style?>>Product</th>
            <th rowspan="2" <?=$style?>>HSN Code</th>
            <th rowspan="2" <?=$style?> class="text-right">Qty.</th>
            <th rowspan="2" <?=$style?> class="text-right">Price<br>(Excl. Tax)</th>
            <?php if($orderdata['orderdetail']['displaydiscountcolumn']>0){ ?>
            <th class="text-right" <?=$style?>>Discount (%)</th>
            <? } ?>
            <?php if($orderdata['orderdetail']['igst']==1) { ?>
                <th class="text-right" <?=$style?> width="8%">SGST (%)</th>
                <th class="text-right" <?=$style?> width="8%">CGST (%)</th>
            <?php }else{ ?>
                <th class="text-right" <?=$style?> width="8%">IGST (%)</th>
            <?php } ?>
            <th rowspan="2" class="text-right" <?=$style?>>Amount (<?=CURRENCY_CODE?>)</th>
        </tr>
        <tr>
            <?php if($orderdata['orderdetail']['displaydiscountcolumn']==1) { ?>
            <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
            <? } ?>
            <?php if($orderdata['orderdetail']['igst']==1) { ?>
            <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
            <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
            <? }else{ ?>
            <th class="text-right" <?=$style?>>Amt. (<?=CURRENCY_CODE?>)</th>
            <? } ?>
        </tr>
    </thead>
    <tbody>
        <?php
            $finaltotal = $subtotal = $totaltaxvalue =0; 
            for($i=0;$i<count($orderdata['orderproduct']);$i++){ 
                $sgst = $cgst = $igst = 0;
                $sgstamount = $cgstamount = $igstamount = $discountamount = 0;

                if($orderdata['orderdetail']['igst']==1) {
                    $sgst = $orderdata['orderproduct'][$i]['tax']/2;
                    $cgst = $orderdata['orderproduct'][$i]['tax']/2;
                }else{
                    $igst = $orderdata['orderproduct'][$i]['tax'];
                }

                $qty = $orderdata['orderproduct'][$i]['quantity'];
                $price = $orderdata['orderproduct'][$i]['price']; 
                $discount = $orderdata['orderproduct'][$i]['discount'];
                $discountamount = ($price*$qty*$discount)/100;
            ?>
                <tr>
                    <td rowspan="2" <?=$style?>><?=$i+1?></td>
                    <td rowspan="2" <?=$style?>><?=ucwords($orderdata['orderproduct'][$i]['name'])?></td>
                    <td rowspan="2" <?=$style?>><?=$orderdata['orderproduct'][$i]['hsncode']?></td>
                    <td rowspan="2" class="text-right" <?=$style?>><?=$qty?></td>
                    <td rowspan="2" class="text-right" <?=$style?>>
                    <?php 
                        //$price = number_format(($price - ($price * $orderdata['orderproduct'][$i]['tax']) / 100), 2, ".", ",");
                        echo number_format($price, 2, ".", ",");
                    ?>
                    </td>
                    <?php 
                    if($orderdata['orderdetail']['displaydiscountcolumn']>0){
                        if($discount>0){ ?>
                            <td class="text-right" <?=$style?>><?=number_format($discount, 2, ".", ",")?></td>
                        <?php }else{ ?>
                            <td class="text-right" <?=$style?>>-</td>
                        <?php } ?>
                    <?php } ?>
                    <?php if($orderdata['orderdetail']['igst']==1) { ?>
                            <td class="text-right" <?=$style?>><?=number_format($sgst, 2, ".", ",")?></td>
                            <td class="text-right" <?=$style?>><?=number_format($cgst, 2, ".", ",")?></td>
                    <? }else{ ?>
                        <td class="text-right" <?=$style?>><?=number_format($igst, 2, ".", ",")?></td>
                    <? } ?>
                    <td rowspan="2" class="text-right" <?=$style?>>
                        <?php 

                            $totalprice = (($price * $orderdata['orderproduct'][$i]['quantity']) - $discountamount);
                            $taxvalue = ($totalprice * $orderdata['orderproduct'][$i]['tax']) / 100;
                            $total = $totalprice + $taxvalue;

                            if($orderdata['orderdetail']['igst']==1) {
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
                    <?php if($orderdata['orderdetail']['displaydiscountcolumn']>0){                 
                        if($discountamount > 0) { ?>
                            <td class="text-right" <?=$style?>>
                                <?=number_format($discountamount, 2, ".", ",");?>
                            </td>
                        <? }else{ ?>
                            <td class="text-right" <?=$style?>>-</td>
                        <? }} ?>
                    <?php if($orderdata['orderdetail']['igst']==1) { ?>
                    <td class="text-right" <?=$style?>>
                        <?=number_format($sgstamount, 2, ".", ",");?>
                    </td>
                    <td class="text-right" <?=$style?>>
                        <?=number_format($cgstamount, 2, ".", ",");?>
                    </td>
                    <? }else{ ?>
                    <td class="text-right" <?=$style?>>
                        <?=number_format($igstamount, 2, ".", ",");?>
                    </td>
                    <? } ?>
                </tr>
        <?php } ?>        
    </tbody>
</table>