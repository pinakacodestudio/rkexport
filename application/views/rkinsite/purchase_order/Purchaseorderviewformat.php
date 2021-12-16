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
    <div class="col-md-12">
                                 
        <?php if(!empty($transactiondata['transactiondetail']) && ($transactiondata['transactiondetail']['deliverytype']==1 || $transactiondata['transactiondetail']['deliverytype']==2)){ 
            if($transactiondata['transactiondetail']['deliverytype']==1){
                $mindaysdate = $transactiondata['transactiondetail']['mindays'];
                $maxdaysdate = $transactiondata['transactiondetail']['maxdays'];
                $type = "Order Delivered on ".$mindaysdate." To ".$maxdaysdate." Working Days.";
            }else{
                $mindaysdate = $transactiondata['transactiondetail']['mindate'];
                $maxdaysdate = $transactiondata['transactiondetail']['maxdate'];
                $type = "Order Delivered Date From ".$mindaysdate." To ".$maxdaysdate.".";
            }
            ?>  
            <div class="col-md-6 pr-n pl-sm">
                <div class="panel border-panel mb-xl">
                    <div class="panel-heading">
                        <h2>Order Delivery Details</h2>
                    </div>
                    <div class="panel-body">
                        <b><?=$type?></b>
                    </div>
                </div>
            </div>
        <?php } if(!empty($transactiondata['transactiondetail']) && $transactiondata['transactiondetail']['deliverytype']==3){ 
            
            if(!empty($transactiondata['orderdeliverydata'])){ ?> 
            
                <div class="panel border-panel mb-xl">
                    <div class="panel-heading">
                        <h2>Order Delivery Details</h2>
                    </div>
                    <div class="panel-body no-padding mb-sm">
                        <?php foreach($transactiondata['orderdeliverydata'] as $index=>$orderdeliverydata) { ?>
                            <div class="col-md-6 mb-sm" style="<?=($index%2==0)?"padding-right: 5px;":"padding-left: 5px;"?>">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered m-n" style="border: 4px solid #e8e8e8;">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr> 
                                                <td colspan="2" style="padding: 0px 10px;">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <div class="checkbox pt-n pl-n">
                                                                <input id="isdelivered" type="checkbox" value="0" name="isdelivered" class="checkradios" <?php echo $orderdeliverydata['deliverystatus']==1?"checked":''?> disabled >
                                                                <label class="control-label">IsDelivered</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-9 text-right">
                                                        <div class="form-group">
                                                            <label class="control-label">Delivered Date : <?php if($orderdeliverydata['deliverydate']!="0000-00-00"){ echo $this->general_model->displaydate($orderdeliverydata['deliverydate']); }else { echo "&nbsp;-"; }?></label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php foreach($orderdeliverydata['deliveryproductdata'] as $deliveryproduct) { ?>
                                                <tr> 
                                                    <td><?=$deliveryproduct['productname']?></td>
                                                    <td><?=$deliveryproduct['quantity']?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

        <?php } ?>
    
    </div>
    <?php  if(!empty($transactiondata['transactiondetail']) && $transactiondata['transactiondetail']['remarks']!=''){ ?>
    <div class="col-md-12">
        <p><b>Remarks : </b><?=ucfirst($transactiondata['transactiondetail']['remarks']);?></p>
    </div>
    <?php } ?>
</div>