<div class="row mb-sm" style="width:100%;margin:0px;">
    <?php 
        $ecassessableamount = $ecgstamount = 0;
        if($orderdata['orderdetail']['globaldiscount'] > 0){
            $finaltotal -= $orderdata['orderdetail']['globaldiscount'];
        }
        if($orderdata['orderdetail']['couponcodeamount'] > 0){
            $finaltotal -= $orderdata['orderdetail']['couponcodeamount'];
        }
        if($orderdata['orderdetail']['redeempoints'] > 0){
            $finaltotal -= $orderdata['orderdetail']['redeemamount'];
        }
        if(!empty($orderdata['extracharges'])){
            $chargesamount = array_sum(array_column($orderdata['extracharges'], 'amount'));
            $ecgstamount = array_sum(array_column($orderdata['extracharges'], 'taxamount'));
            $ecassessableamount = $chargesamount - $ecgstamount;
            $finaltotal += $chargesamount;
        }
        
        $roundoff = round($finaltotal)-$finaltotal;
        $finaltotal = round($finaltotal);
    ?> 
    <div  style="width: 28%;float: left;">
        <div class="panel-body no-padding">
            <span><span style="color: #000;"><b>Amount (In Words):</b></span><br><?=convert_number($finaltotal)?></span>
        </div>
    </div>
    <div  style="width: 71%;float: right;padding:0;">
        <div  style="width: 54%;float: left;padding:0px 10px 0 0;">
            <div class="panel-body no-padding" style="margin-bottom:10px;">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered m-n invoice" style="color: #000">
                        <thead>
                            <tr>
                                <th class="text-center" <?=$style?>>GST Summary</th>
                                <th class="text-center" width="25%" <?=$style?>>Assessable Amount (<?=CURRENCY_CODE?>)</th>
                                <th class="text-center" width="25%" <?=$style?>>GST Amount (<?=CURRENCY_CODE?>)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th <?=$style?>>Product Total</th>
                                <td class="text-right" <?=$style?>><?=number_format($subtotal-$totaltaxvalue, 2,'.', ',');?></td>
                                <td class="text-right" <?=$style?>><?=number_format($totaltaxvalue, 2,'.', ',');?></td>
                            </tr>
                            <tr>
                                <th <?=$style?>>Extra Charges Total</th>
                                <td class="text-right" <?=$style?>><?=number_format($ecassessableamount, 2,'.', ',');?></td>
                                <td class="text-right" <?=$style?>><?=number_format($ecgstamount, 2,'.', ',');?></td>
                            </tr>
                            <tr>
                                <td <?=$style?>></td>
                                <th class="text-right" <?=$style?>><?=number_format(($subtotal-$totaltaxvalue+$ecassessableamount), 2,'.', ',');?></th>
                                <th class="text-right" <?=$style?>><?=number_format(($totaltaxvalue+$ecgstamount), 2,'.', ',');?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div style="width: 46%;float: left;padding-right: 0;">
            <div class="panel-body no-padding">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered m-n invoice" style="color: #000">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center" <?=$style?>>Order Summary (<?=CURRENCY_CODE?>)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td <?=$style?>>Total Of Product</td>
                                <td class="text-right" <?=$style?>><?=number_format($subtotal, 2,'.', ',');?></td>
                            </tr>
                            <?php if($orderdata['orderdetail']['globaldiscount'] > 0){ ?>
                            <tr>
                                <td <?=$style?>>Discount</td>
                                <td class="text-right" <?=$style?>><?=number_format($orderdata['orderdetail']['globaldiscount'], 2,'.', ',');?></td>
                            </tr>
                            <? } ?>
                            <?php if($orderdata['orderdetail']['couponcodeamount'] > 0){ ?>
                            <tr>
                                <td <?=$style?>>Coupon Offer</td>
                                <td class="text-right" <?=$style?>><?=number_format($orderdata['orderdetail']['couponcodeamount'], 2,'.', ',');?></td>
                            </tr>
                            <? } ?>
                            <?php if($orderdata['orderdetail']['redeempoints'] > 0){ ?>
                            <tr>
                                <td <?=$style?>>Redeem Amount (<?=$orderdata['orderdetail']['redeempoints'].'*'.$orderdata['orderdetail']['redeemrate']?>)</td>
                                <td class="text-right" <?=$style?>><?=number_format($orderdata['orderdetail']['redeemamount'], 2,'.', ',');?></td>
                            </tr>
                            <?php } ?>
                            <?php if(!empty($orderdata['extracharges'])){ 
                                foreach($orderdata['extracharges'] as $extracharge){ ?>
                            <tr>
                                <td <?=$style?>><?=$extracharge['extrachargesname']?></td>
                                <td class="text-right" <?=$style?>><?=number_format($extracharge['amount'], 2,'.', ',');?></td>
                            </tr>
                            <? } } ?>
                            <tr>
                                <td <?=$style?>>Round Off</td>
                                <td class="text-right" <?=$style?>><?=number_format($roundoff, 2,'.', ',');?></td>
                            </tr>
                            <tr>
                                <td <?=$style?>><b>Amount Payable</b></td>
                                <td class="text-right" <?=$style?>><b><?=number_format($finaltotal, 2,'.', ',');?></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>