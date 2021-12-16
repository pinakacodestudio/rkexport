


<div class="row mb-sm" style="width:100%;margin:0px;">
    <?php 
        $ecassessableamount = $ecgstamount = 0;
        if($transactiondata['transactiondetail']['globaldiscount'] > 0){
            $finaltotal -= $transactiondata['transactiondetail']['globaldiscount'];
        }
        if(isset($printtype) && $printtype=='invoice' || isset($printtype) && $printtype=='order'){
            if($transactiondata['transactiondetail']['couponcodeamount'] > 0){
                $finaltotal -= $transactiondata['transactiondetail']['couponcodeamount'];
            }
            if($transactiondata['transactiondetail']['redeempoints'] > 0){
                $finaltotal -= $transactiondata['transactiondetail']['redeemamount'];
            }
        }
        if(!empty($transactiondata['extracharges'])){
            $chargesamount = array_sum(array_column($transactiondata['extracharges'], 'amount'));
            $ecgstamount = array_sum(array_column($transactiondata['extracharges'], 'taxamount'));
            $ecassessableamount = $chargesamount - $ecgstamount;
            $finaltotal += $chargesamount;
        }
        if($finaltotal<0){
            $finaltotal = 0;
        }
        $roundoff = round($finaltotal)-$finaltotal;
        $finaltotal = round($finaltotal);
    ?> 
    <div  style="width: 53%;float: left;padding:0px 10px 0 0;">
        <div class="panel-body no-padding" style="margin-bottom:10px;">
            <div class="table-responsive">
                <table class="table table-hover table-bordered m-n invoice" style="color: #000">
                    <thead>
                        <tr>
                            <th class="text-center" <?=$style?>>GST Summary</th>
                            <th class="text-right" width="25%" <?=$style?>>Assessable Amount (<?=CURRENCY_CODE?>)</th>
                            <th class="text-right" width="25%" <?=$style?>>GST Amount (<?=CURRENCY_CODE?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th <?=$style?>><?php if(isset($printtype) && $printtype=='creditnote' && $transactiondata['transactiondetail']['creditnotetype']==1){ echo "Offer"; }else{ echo "Product";} ?> Total</th>
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
        <span><span style="color: #000;"><b>Amount (In Words) : </b></span><?=convert_number($finaltotal)?></span>
    </div>
    <div style="width: 45%;float: right;padding-right: 0;">
        <div class="panel-body no-padding">
            <div class="table-responsive">
                <table class="table table-hover table-bordered m-n invoice" style="color: #000">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center" <?=$style?>>
                                <?=$heading?> Summary (<?=CURRENCY_CODE?>)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td <?=$style?>>Total Of <?php if(isset($printtype) && $printtype=='creditnote' && $transactiondata['transactiondetail']['creditnotetype']==1){ echo "Offer"; }else{ echo "Product";} ?></td>
                            <td class="text-right" <?=$style?>><?=number_format($subtotal, 2,'.', ',');?></td>
                        </tr>
                        <?php if($transactiondata['transactiondetail']['globaldiscount'] > 0){ ?>
                        <tr>
                            <td <?=$style?>>Discount</td>
                            <td class="text-right" <?=$style?>><?=number_format($transactiondata['transactiondetail']['globaldiscount'], 2,'.', ',');?></td>
                        </tr>
                        <?php } ?>
                        <?php 
                        if(isset($printtype) && $printtype=='invoice' || isset($printtype) && $printtype=='order'){
                            if($transactiondata['transactiondetail']['couponcodeamount'] > 0){ ?>
                                <tr>
                                    <td <?=$style?>>Coupon Amount</td>
                                    <td class="text-right" <?=$style?>><?=number_format($transactiondata['transactiondetail']['couponcodeamount'], 2,'.', ',');?></td>
                                </tr>
                                <?php } ?>
                                <?php if($transactiondata['transactiondetail']['redeempoints'] > 0){ ?>
                                <tr>
                                    <td <?=$style?>>Redeem Amount</td>
                                    <td class="text-right" <?=$style?>><?=number_format($transactiondata['transactiondetail']['redeemamount'], 2,'.', ',');?></td>
                                </tr>
                        <?php }
                        } ?>
                        <?php if(!empty($transactiondata['extracharges'])){ 
                            foreach($transactiondata['extracharges'] as $extracharge){ ?>
                        <tr>
                            <td <?=$style?>><?=$extracharge['extrachargesname']?></td>
                            <td class="text-right" <?=$style?>><?=number_format($extracharge['amount'], 2,'.', ',');?></td>
                        </tr>
                        <?php } }  ?>
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

<div class="row mb-sm">
    <div class="col-md-12">
        <?php  if(!empty($invoicesettingdata) && $invoicesettingdata['notes']!='' && (isset($printnotes)) && $printnotes==1){ ?>
            <?=$invoicesettingdata['notes']?>
        <?php } ?>
    </div>
</div>
