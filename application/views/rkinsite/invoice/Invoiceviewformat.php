<?php require_once(APPPATH."views/".ADMINFOLDER.'invoice/Transactionheader.php');?>
<div class="row">
    <div class="col-md-12">
        <div class="panel mb-md">
            <div class="panel-body no-padding">
                <div class="table-responsive">
                    <?php require_once(APPPATH."views/".ADMINFOLDER.'invoice/Transactionproductdetails.php');?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <?php require_once(APPPATH."views/".ADMINFOLDER.'invoice/Transactionsummarydetails.php');?>
        <?php if ($transactiondata['transactiondetail']['cashorbankid'] > 0){ ?>
            <span><span style="color: #000;"><b>Bank Name : </b></span><?=$transactiondata['transactiondetail']['bankname']!=''?$transactiondata['transactiondetail']['bankname']:$transactiondata['transactiondetail']['bankname']?></span>
            <br>
            <span><span style="color: #000;"><b>Branch Name : </b></span><?=$transactiondata['transactiondetail']['branchname']!=''?$transactiondata['transactiondetail']['branchname']:'-'?></span>
            <br>
            <span><span style="color: #000;"><b>Account No. : </b></span><?=$transactiondata['transactiondetail']['bankaccountnumber']?></span>
            <br>
            <span><span style="color: #000;"><b>IFSC Code : </b></span><?=$transactiondata['transactiondetail']['ifsccode']!=''?$transactiondata['transactiondetail']['ifsccode']:'-'?></span>
            <br>
            <span><span style="color: #000;"><b>MICR Code : </b></span><?=$transactiondata['transactiondetail']['micrcode']!=''?$transactiondata['transactiondetail']['micrcode']:'-'?></span>
        <?php } ?>
        <hr>
    </div>
    <?php  if(!empty($transactiondata['transactiondetail']) && $transactiondata['transactiondetail']['remarks']!=''){ ?>
    <div class="col-md-12 mb-md">
        <p><b>Remarks : </b><?=ucfirst($transactiondata['transactiondetail']['remarks']);?></p>
    </div>
    <?php } ?>
    
    <?php if(isset($viewtype) && $viewtype=='page' && !empty($transactiondata['shippingdata'])){ ?>
        <div class="col-md-6 pr-xs">
            <div class="panel mb-md">
                <div class="panel-body no-padding">
                <?php 
                    $shippingdata = $transactiondata['shippingdata'];
                    if(!empty($shippingdata)){ ?>
                    <div class="col-md-12 col-xs-12 p-n">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered m-n invoice" width="100%" style="color: #000">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="text-center">Shipping Order Details</th>
                                    </tr>
                                    <tr>
                                        <th width="40%">Shipping By</th>
                                        <td><?=($shippingdata['shippingby']==0?"Courier":"Transporter")?></td>
                                    </tr>
                                    <tr>
                                        <th width="40%">Shipping Company</th>
                                        <td><?=ucwords($shippingdata['shippingcompany'])?></td>
                                    </tr>
                                    <tr>
                                        <th>Tracking Code</th>
                                        <td><?=$shippingdata['trackingcode']?></td>
                                    </tr>

                                    <tr>
                                        <th>Shipping Amount	(<?=CURRENCY_CODE?>)</th>
                                        <td><?=numberFormat($shippingdata['shippingamount'],2,',')?></td>
                                    </tr>
                                    <tr>
                                        <th>Invoice Amount (<?=CURRENCY_CODE?>)</th>
                                        <td><?=numberFormat(round($shippingdata['invoiceamount']),2,',')?></td>
                                    </tr>
                                    <tr>
                                        <th>Ship Date</th>
                                        <td><?=$shippingdata['shipdate']?></td>
                                    </tr>
                                    <tr>
                                        <th>Remarks</th>
                                        <td><?=ucfirst($shippingdata['remarks'])?></td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 pl-xs">
            <div class="panel mb-md">
                <div class="panel-body no-padding">
                <?php if(!empty($transactiondata['shippingdata']['shippingpackagedata'])){  
                        $shippingpackagedata = $transactiondata['shippingdata']['shippingpackagedata'];
                    ?>
                    <div class="col-md-12 col-xs-12 p-n">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered m-n invoice" width="100%" style="color: #000">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="text-center">Shipping Package Details</th>
                                    </tr>
                                    <tr>
                                        <th width="40%" class="text-right">Weight (KG)</th>
                                        <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                                    </tr>
                                    <?php foreach($shippingpackagedata as $package){ ?>

                                        <tr>
                                            <td class="text-right"><?=numberFormat($package['weight'],3,',')?></td>
                                            <td class="text-right"><?=numberFormat($package['amount'],2,',')?></td>
                                        </tr>
                                    <?php } ?>
                                </thead>
                            </table>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <hr>
</div>