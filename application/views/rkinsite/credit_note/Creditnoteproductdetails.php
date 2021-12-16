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
            <th rowspan="2" <?=$style?> class="text-right">Price (Excl. Tax)</th>
            
            <?php if($creditnotedata[0]['igst']==1) { ?>
                <th class="text-right" <?=$style?>>SGST (%)</th>
                <th class="text-right" <?=$style?>>CGST (%)</th>
            <?php }else{ ?>
                <th class="text-right" <?=$style?>>IGST (%)</th>
            <?php } ?>
            <th rowspan="2" class="text-right" <?=$style?>>Amount (<?=CURRENCY_CODE?>)</th>
        </tr>
        <tr>
            <?php if($creditnotedata[0]['igst']==1) { ?>
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
            for($i=0;$i<count($creditnotedata);$i++){ 
                $sgst = $cgst = $igst = 0;
                $sgstamount = $cgstamount = $igstamount = $discountamount = 0;

                if($creditnotedata[0]['igst']==1) {
                    $sgst = $creditnotedata[$i]['tax']/2;
                    $cgst = $creditnotedata[$i]['tax']/2;
                }else{
                    $igst = $creditnotedata[$i]['tax'];
                }

                $qty = $creditnotedata[$i]['qty'];
                $price = $creditnotedata[$i]['price'];
                
            ?>
                <tr>
                    <td rowspan="2" <?=$style?>><?=$i+1?></td>
                    <td rowspan="2" <?=$style?>><?=ucwords($creditnotedata[$i]['name'])?></td>
                    <td rowspan="2" <?=$style?>><?=$creditnotedata[$i]['hsncode']?></td>
                    <td rowspan="2" class="text-right" <?=$style?>><?=$qty?></td>
                    <td rowspan="2" class="text-right" <?=$style?>>
                    <?php 
                        echo number_format($price, 2, ".", ",");
                    ?>
                    </td>
                    
                    <?php if($creditnotedata[0]['igst']==1) { ?>
                            <td class="text-right" <?=$style?>><?=number_format($sgst, 2, ".", ",")?></td>
                            <td class="text-right" <?=$style?>><?=number_format($cgst, 2, ".", ",")?></td>
                    <? }else{ ?>
                        <td class="text-right" <?=$style?>><?=number_format($igst, 2, ".", ",")?></td>
                    <? } ?>
                    <td rowspan="2" class="text-right" <?=$style?>>
                        <?php 

                            //$totalprice = (($price - $discountamount) * $creditnotedata[$i]['qty']);
                            $total = $totalprice = $creditnotedata[$i]['creditamount'];
                            $taxvalue = ($totalprice * $creditnotedata[$i]['tax']) / 100;
                            //$total = $totalprice + $taxvalue;

                            if($creditnotedata[0]['igst']==1) {
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
                    <?php if($creditnotedata[0]['igst']==1) { ?>
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