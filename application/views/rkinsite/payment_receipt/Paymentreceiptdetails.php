<?php
$style = '';
if(isset($type)){
    $style = 'style="padding: 5px;border: 1px solid #666;font-size: 12px;"';
}
$borderpanel = "border-panel";
if(isset($printtype) && $printtype==1){
    $borderpanel = ""; 
}

?>
<div class="col-md-12 mb-sm">
        <div class="panel mb-md <?=$borderpanel?>">
            <div class="panel-body no-padding">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered m-n invoice" width="100%" style="color: #000">
                        <thead>
                            <tr>
                                <th <?=$style?>>Sr. No.</th>
                                <?php if($paymentreceiptdata['paymentreceiptdetail']['isagainstreference']==1){ ?>
                                    <th <?=$style?>>Invoice No.</th>
                                    <th class="text-right" <?=$style?>>Invoice Amount (<?=CURRENCY_CODE?>)</th>
                                    <th class="text-right" <?=$style?>><?=$heading?> Amount (<?=CURRENCY_CODE?>)</th>
                                <? }else{ ?>
                                    <th <?=$style?>>Transaction Type</th>
                                    <th <?=$style?> class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                                <? } ?>
                            </tr>
                        </thead>
                        <tbody>

                        <?php if($paymentreceiptdata['paymentreceiptdetail']['isagainstreference']==1){ 
                            
                            if(!empty($paymentreceiptdata['paymentreceipttransaction'])){
                                foreach($paymentreceiptdata['paymentreceipttransaction'] as $index=>$row){ ?>
                                    <tr>
                                        <td <?=$style?>><?=(++$index)?></td>
                                        <td <?=$style?>><?=$row['invoiceno']?></td>
                                        <td class="text-right" <?=$style?>><?=number_format($row['invoiceamount'],2,'.',',')?></td>
                                        <td class="text-right" <?=$style?>><?=number_format($row['amount'],2,'.',',')?></td>
                                    </tr>
                            <?php }
                            } ?>

                            <? }else{ ?>
                                <tr>
                                    <td <?=$style?>>1</td>
                                    <td <?=$style?>><?=$this->Bankmethod[$paymentreceiptdata['paymentreceiptdetail']['method']]?></td>
                                    <td class="text-right" <?=$style?>><?=number_format($paymentreceiptdata['paymentreceiptdetail']['totalamount'],2,'.',',')?></td>
                                </tr>

                            <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 mb-sm">
        <?php //require_once(APPPATH."views/".ADMINFOLDER.'invoice/Transactionsummarydetails.php');?>
        
        <div class="pr-sm" style="width: 58%;float: left;">
            <div class="panel <?=$borderpanel?>">
                <div class="panel-body no-padding">
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered m-n invoice" style="color: #000">
                            <tbody>
                                <tr>
                                    <th width="35%" <?=$style?>>Amount (In Words)</th>
                                    <td <?=$style?>><span><?=convert_number($paymentreceiptdata['paymentreceiptdetail']['totalamount'])?></span></td>
                                </tr>
                                <tr>
                                    <th colspan="2" <?=$style?>>Remarks</th>
                                </tr>
                                <tr>
                                    <td colspan="2" <?=$style?>><?=($paymentreceiptdata['paymentreceiptdetail']['remarks']!="")?ucfirst($paymentreceiptdata['paymentreceiptdetail']['remarks']):'-';?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pl-sm" style="width: 42%;float: right;">
            <div class="panel <?=$borderpanel?>">
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
                                    <th <?=$style?>>Total <?=$heading?> Amount</th>
                                    <td class="text-right" <?=$style?>><?=number_format($paymentreceiptdata['paymentreceiptdetail']['totalamount'],2,'.',',');?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>