<div class="row mb-sm" style="width:100%;margin:0px;">
    <?php 
        //$roundoff = round($finaltotal)-$finaltotal;
        //$finaltotal = round($finaltotal);
    ?> 
    <div  style="width: 40%;float: left;">
        <div class="panel-body no-padding">
            <span><span style="color: #000;"><b>Amount (In Words):</b></span><br><?=convert_number($finaltotal)?></span>
        </div>
    </div>
    <div  style="width: 59%;float: right;padding:0;">
        <div  style="width: 50%;float: left;padding:0;">
            <div class="panel-body no-padding" style="margin-bottom:10px;">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered m-n invoice" style="color: #000">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center" <?=$style?>>GST Summary (<?=CURRENCY_CODE?>)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td <?=$style?>>Assessable Amount</td>
                                <td class="text-right" <?=$style?>><?=number_format($subtotal-$totaltaxvalue, 2,'.', ',');?></td>
                            </tr>
                            <tr>
                                <td <?=$style?>>Total GST</td>
                                <td class="text-right" <?=$style?>><?=number_format($totaltaxvalue, 2,'.', ',');?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div style="width: 50%;float: left;padding-right: 0;">
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
                            <!-- <tr>
                                <td <?=$style?>>Round Off</td>
                                <td class="text-right" <?=$style?>><?//=number_format($roundoff, 2,'.', ',');?></td>
                            </tr> -->
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