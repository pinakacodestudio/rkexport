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
        <hr>
    </div>

    <?php  if(!empty($quotationdata['transactiondetail']) && $quotationdata['transactiondetail']['remarks']!=''){ ?>
    <div class="col-md-12">
        <p><b>Remarks : </b><?=ucfirst($quotationdata['transactiondetail']['remarks']);?></p>
    </div>
    <? } ?>
</div>



