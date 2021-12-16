<?php require_once(APPPATH."views/".ADMINFOLDER.'testing_and_rd/Testingheader.php');?>

<div class="row">
    <div class="col-md-12">
        <div class="panel mb-md">
            <div class="panel-body no-padding">
                <div class="table-responsive">
                    <?php require_once(APPPATH."views/".ADMINFOLDER.'testing_and_rd/Viewtestingandrdproductdetails.php');?>
                </div>
            </div>
        </div>
    </div>
    <?php  if(!empty($headerdata['remarks'])){ ?>
    <div class="col-md-12">
        <p><b>Remarks : </b><?=$headerdata['remarks'];?></p>
    </div>
    <?php } ?>
    
</div>



