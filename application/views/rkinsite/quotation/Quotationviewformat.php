<?php require_once(APPPATH."views/".ADMINFOLDER.'invoice/Transactionheader.php');?>
<div class="row">
    <div class="col-md-12">
        <div class="panel border-panel mb-xl">
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
            <span><span style="color: #000;"><b>Bank Name : </b></span><?=$transactiondata['transactiondetail']['bankname']!=''?$transactiondata['transactiondetail']['bankname']:'-'?></span>
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
    <div class="col-md-12">
   
    <?php  if(!empty($quotationdata['transactiondetail']) && $quotationdata['transactiondetail']['remarks']!=''){ ?>
    <div class="col-md-12">
        <p><b>Remarks : </b><?=ucfirst($quotationdata['transactiondetail']['remarks']);?></p>
    </div>
    <?php } ?>
</div>