<?php
$panelborderclass = "border-panel";
$panelborderstyle = "";
if(isset($printtype) && $printtype==1){
    $panelborderclass = ""; 
    $panelborderstyle = "border: 1px solid #ddd;";        
}
$calculatelandingcost = 0;
?>
<div class="col-md-12">
    <table id="productprocesstable" class="table mb-sm <?=$panelborderclass?>" cellspacing="0" width="100%" style="<?=$panelborderstyle?>">
        <thead>
            <tr style="border-bottom:2px solid #ddd;">
                <th colspan="6" class="">Process Details</th>  
            </tr>
        </thead>
        <tbody>
            <tr>
                <th width="20%">Job Card</th> 
                <th width="2%">:</th> 
                <td width="29%">#<?=$productprocessdata['id']?></td> 
                <th width="20%">Process Group</th> 
                <th width="2%">:</th> 
                <td width="29%"><?=$productprocessdata['processgroup']?></td> 
            </tr>
            <tr>
                <th>Job Name</th> 
                <th>:</th> 
                <td><?=$productprocessdata['processname']?></td> 
                <th>Processed By</th>
                <th>:</th> 
                <td><?=($productprocessdata['processbymemberid']==1?"In-House Emp":"Other Party")?></td> 
            </tr>
            <tr>
                <th><?=($productprocessdata['processbymemberid']==1?"Machine":"Vendor")?></th>
                <th>:</th> 
                <td><?=$productprocessdata['vendorname']?></td> 
                <th>Transaction Date</th> 
                <th>:</th> 
                <td><?=$this->general_model->displaydate($productprocessdata['transactiondate'])?></td> 
            </tr>
            <tr>
                <th>Batch No.</th> 
                <th>:</th> 
                <td><?=($productprocessdata['batchno']!=""?$productprocessdata['batchno']:"-")?></td> 
                <th>Order Number</th> 
                <th>:</th> 
                <td><?php
                if(isset($printtype) && $printtype==1){
                    echo ($productprocessdata['ordernumber']!="")?$productprocessdata['ordernumber']:"-";
                }else{
                    echo ($productprocessdata['ordernumber']!="")?'<a href="'.ADMIN_URL.'order/view-order/'.$productprocessdata['orderid'].'" target="_blank">'.$productprocessdata['ordernumber'].'</a>':"-";
                }
                ?></td> 
            </tr>
                <th>Buyer Name</th> 
                <th>:</th> 
                <td><?php
                if(isset($printtype) && $printtype==1){
                    echo ($productprocessdata['buyername']!="")?$productprocessdata['buyername']:"-";
                }else{
                    echo ($productprocessdata['buyername']!="")?'<a href="'.ADMIN_URL.'member/member-detail/'.$productprocessdata['buyerid'].'" target="_blank">'.$productprocessdata['buyername'].'</a>':"-";
                } ?>
                </td> 
                <th>Estimate Date</th> 
                <th>:</th> 
                <td><?=($productprocessdata['estimatedate']!="0000-00-00")?$this->general_model->displaydate($productprocessdata['estimatedate']):"-"?></td> 
            <tr>
                <?php if($productprocessdata['outproductcount'] == 0 && $productprocessdata['inproductcount'] == 0){ ?>
                <th>Status</th> 
                <th>:</th> 
                <td><?php
                if(isset($printtype) && $printtype==1){
                    echo $productprocessdata['processstatuses'];
                }else{
                    if($productprocessdata['processstatus']==1){
                        echo '<span class="label label-success">'.$productprocessdata['processstatuses'].'</span>';
                    }else{
                        echo '<span class="label label-warning">'.$productprocessdata['processstatuses'].'</span>';
                    }
                }?></td>
                <?php }else{ ?>
                    <th></th> 
                    <th></th> 
                    <td></td>
                <?php } ?>
                <th></th> 
                <th></th> 
                <td></td>
            </tr>
            <?php if($productprocessdata['comments']!=""){ ?>
            <tr>
                <th>Remarks</th> 
                <th>:</th>  
                <td class="text-justify" colspan="4"><?=ucfirst($productprocessdata['comments'])?></td>  
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php if($productprocessdata['outproductcount'] > 0){ ?>
<div class="col-md-12">
    <table id="" class="table table-striped table-bordered mb-sm <?=$panelborderclass?>" cellspacing="0" width="100%" style="<?=$panelborderstyle?>">
        <thead>
            <tr style="border-bottom:2px solid #ddd;">
                <th colspan="8" class="">OUT Product Material Details</th>  
            </tr>
            <tr>
                <th class="width5">Sr. No.</th>
                <th>Product Name</th> 
                <th>Variant Name</th> 
                <th>Unit</th> 
                <!-- <th class="text-center">Additional / Supportive</th>  -->
                <th class="text-right">Price (<?=CURRENCY_CODE?>)</th> 
                <th class="text-right">Quantity</th> 
                <th class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th> 
            </tr>
        </thead>
        <tbody>
        <?php if(count($productprocessdata['outproducts']) > 0){ 
            $subtotal = $totalqty = 0;
            foreach($productprocessdata['outproducts'] as $p=>$outproduct){ 
                $total = $outproduct['price']*$outproduct['quantity'];
                $subtotal = $subtotal + $total;
                $totalqty = $totalqty + $outproduct['quantity'];
                ?>
                <tr>
                    <td><?=($p+1)?></td>
                    <td><?=$outproduct['productname']?></td> 
                    <td><?=$outproduct['variantname']?></td> 
                    <td><?=$outproduct['unit']?></td> 
                    <!-- <td class="text-center"><?php //echo ($outproduct['issupportingproduct']==1?"Yes":"No")?></td> -->
                    <td class="text-right"><?=numberFormat($outproduct['price'],2,',')?></td> 
                    <td class="text-right"><?=numberFormat($outproduct['quantity'],2,',')?></td> 
                    <td class="text-right"><?=numberFormat($total,2,',')?></td> 
                </tr>
        <?php } ?>
            <tr>
                <th colspan="5" class="text-right">Total</th>
                <th class="text-right"><?=numberFormat($totalqty,2,',')?></th> 
                <th class="text-right"><?=numberFormat($subtotal,2,',')?></th>  
            </tr>
            <?php if(count($productprocessdata['outextracharges']) > 0){
                foreach($productprocessdata['outextracharges'] as $outcharges){
                    if($outcharges['chargetype'] == 0){
                        $amount = $outcharges['amount'];
                        $subtotal = $subtotal + $outcharges['amount'];
                    }else{
                        $amount = $outcharges['amount'] * $totalqty;
                        $subtotal = $subtotal + $amount;
                    } 
                    ?>
                    <tr>
                        <td colspan="6" class="text-right"><?=$outcharges['extrachargesname']?></td>
                        <td class="text-right"><?=numberFormat($amount,2,',')?></td>  
                    </tr>
                <?php } ?>
                <tr>
                    <th colspan="6" class="text-right">Net Total</th>
                    <th class="text-right"><?=numberFormat($subtotal,2,',')?></th>  
                </tr>
            <?php } ?>
        <?php 
        $calculatelandingcost = $subtotal;
        }else{ ?>
            <tr>
                <th colspan="7" class="text-center">No data available in table.</th>  
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php } ?>
<?php if($productprocessdata['type']==1 && $productprocessdata['inproductcount'] > 0){ ?>
    <?php if(!empty($productprocessdata['rejectiondata'])){ ?>
    <div class="col-md-12">
        <table id="" class="table table-striped table-bordered mb-sm <?=$panelborderclass?>" cellspacing="0" width="100%" style="<?=$panelborderstyle?>">
            <thead>
                <tr style="border-bottom:2px solid #ddd;">
                    <th colspan="7">Rejection</th>  
                </tr>
                <tr>
                    <th class="width5">Sr. No.</th>
                    <th>Product Name</th> 
                    <th>Variant Name</th> 
                    <th>Unit</th> 
                    <th class="text-right">Price (<?=CURRENCY_CODE?>)</th> 
                    <th class="text-right">Quantity</th> 
                    <th class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th> 
                </tr>
            </thead>
            <tbody>
                <?php $subtotal = $totalqty = 0;
                foreach($productprocessdata['rejectiondata'] as $r=>$rejection){ 
                    $total = $rejection['price']*$rejection['quantity'];
                    $subtotal = $subtotal + $total;
                    $totalqty = $totalqty + $rejection['quantity'];
                    ?>
                    <tr>
                        <td><?=($r+1)?></td>
                        <td><?=$rejection['productname']?></td> 
                        <td><?=$rejection['variantname']?></td> 
                        <td><?=$rejection['unit']?></td> 
                        <td class="text-right"><?=numberFormat($rejection['price'],2,',')?></td> 
                        <td class="text-right"><?=$rejection['quantity']?></td> 
                        <td class="text-right"><?=numberFormat($total,2,',')?></td> 
                    </tr>
                <?php } ?>
                <tr>
                    <th colspan="5" class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th>
                    <th class="text-right"><?=$totalqty?></th> 
                    <th class="text-right"><?=numberFormat($subtotal,2,',')?></th>  
                </tr>
            </tbody>
        </table>
    </div>
    <?php } ?>
    <?php if(!empty($productprocessdata['wastagedata'])){ ?>
    <div class="col-md-12">
        <table id="" class="table table-striped table-bordered mb-sm <?=$panelborderclass?>" cellspacing="0" width="100%" style="<?=$panelborderstyle?>">
            <thead>
                <tr style="border-bottom:2px solid #ddd;">
                    <th colspan="7">Wastage</th>  
                </tr>
                <tr>
                    <th class="width5">Sr. No.</th>
                    <th>Product Name</th> 
                    <th>Variant Name</th> 
                    <th>Unit</th> 
                    <th class="text-right">Price (<?=CURRENCY_CODE?>)</th> 
                    <th class="text-right">Quantity</th> 
                    <th class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th> 
                </tr>
            </thead>
            <tbody>
                <?php $subtotal = $totalqty = 0;
                foreach($productprocessdata['wastagedata'] as $w=>$wastage){ 
                    $total = $wastage['price']*$wastage['quantity'];
                    $subtotal = $subtotal + $total;
                    $totalqty = $totalqty + $wastage['quantity'];
                    ?>
                    <tr>
                        <td><?=($w+1)?></td>
                        <td><?=$wastage['productname']?></td> 
                        <td><?=$wastage['variantname']?></td> 
                        <td><?=$wastage['unit']?></td> 
                        <td class="text-right"><?=numberFormat($wastage['price'],2,',')?></td> 
                        <td class="text-right"><?=$wastage['quantity']?></td> 
                        <td class="text-right"><?=numberFormat($total,2,',')?></td> 
                    </tr>
                <?php } ?>
                <tr>
                    <th colspan="5" class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th>
                    <th class="text-right"><?=$totalqty?></th> 
                    <th class="text-right"><?=numberFormat($subtotal,2,',')?></th>  
                </tr>
            </tbody>
        </table>
    </div>
    <?php } ?>
    <?php if(!empty($productprocessdata['lostdata'])){ ?>
    <div class="col-md-12">
        <table id="" class="table table-striped table-bordered mb-sm <?=$panelborderclass?>" cellspacing="0" width="100%" style="<?=$panelborderstyle?>">
            <thead>
                <tr style="border-bottom:2px solid #ddd;">
                    <th colspan="7">Lost</th>  
                </tr>
                <tr>
                    <th class="width5">Sr. No.</th>
                    <th>Product Name</th> 
                    <th>Variant Name</th> 
                    <th>Unit</th> 
                    <th class="text-right">Price (<?=CURRENCY_CODE?>)</th> 
                    <th class="text-right">Quantity</th> 
                    <th class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th> 
                </tr>
            </thead>
            <tbody>
                <?php $subtotal = $totalqty = 0;
                foreach($productprocessdata['lostdata'] as $l=>$lost){ 
                    $total = $lost['price']*$lost['quantity'];
                    $subtotal = $subtotal + $total;
                    $totalqty = $totalqty + $lost['quantity'];
                    ?>
                    <tr>
                        <td><?=($l+1)?></td>
                        <td><?=$lost['productname']?></td> 
                        <td><?=$lost['variantname']?></td> 
                        <td><?=$lost['unit']?></td> 
                        <td class="text-right"><?=numberFormat($lost['price'],2,',')?></td> 
                        <td class="text-right"><?=$lost['quantity']?></td> 
                        <td class="text-right"><?=numberFormat($total,2,',')?></td> 
                    </tr>
                <?php } ?>
                <tr>
                    <th colspan="5" class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th>
                    <th class="text-right"><?=$totalqty?></th> 
                    <th class="text-right"><?=numberFormat($subtotal,2,',')?></th>  
                </tr>
            </tbody>
        </table>
    </div>
    <?php } ?>
    <div class="col-md-12">
        <table id="" class="table table-striped table-bordered mb-sm <?=$panelborderclass?>" cellspacing="0" width="100%" style="<?=$panelborderstyle?>">
            <thead>
                <tr style="border-bottom:2px solid #ddd;">
                    <th colspan="13" class="">IN Details</th>  
                </tr>
                <tr>
                    <th class="width5">Sr. No.</th>
                    <th>Product Name</th> 
                    <th>Variant Name</th> 
                    <th class="text-center">Final Product</th> 
                    <th class="text-right">Price (<?=CURRENCY_CODE?>)</th> 
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Pending Quantity</th> 
                    <th class="text-right">Labor Cost (<?=CURRENCY_CODE?>)</th>
                    <th class="text-right">Total Labor Cost (<?=CURRENCY_CODE?>)</th> 
                    <th class="text-right">Landing Cost (<?=CURRENCY_CODE?>)</th>
                    <th class="text-right">Total Landing Cost (<?=CURRENCY_CODE?>)</th> 
                    <th class="text-right">Total Extra Charges (<?=CURRENCY_CODE?>)</th> 
                    <th class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th> 
                </tr>
            </thead>
            <tbody>
            <?php 
            if(count($productprocessdata['inproducts']) > 0){ 
                $totalinextracharges = $addedcharge = 0;
               
                if(count($productprocessdata['inextracharges']) > 0){
                    foreach($productprocessdata['inextracharges'] as $incharges){ 

                        $addedcharge += $incharges['amount']; 
                        if($incharges['chargetype'] == 0){
                            $amount = $incharges['amount'];
                        }else{
                            $amount = $incharges['amount'] * array_sum(array_column($productprocessdata['inproducts'], 'quantity'));
                        } 
                        $totalinextracharges += $amount;
                    }
                }
                
                $totalexrachargeofproduct = $totalinextracharges / count($productprocessdata['inproducts']);
                
                $subtotal = $totalqty = $totalpendingqty = $totallaborcost = $totallaborcostamount = $totallandingcost = $totallandingcostamount = $totalexrachargeperproduct = 0;

                foreach($productprocessdata['inproducts'] as $p=>$inproduct){
                    
                    $totalqty = $totalqty + $inproduct['quantity'];
                    $totalpendingqty = $totalpendingqty + $inproduct['pendingquantity'];
                    $totallaborcost = $totallaborcost + $inproduct['laborcost'];
                    $totallaborcostamount = $totallaborcostamount + ($inproduct['quantity']*$inproduct['laborcost']);

                    $inqty = $inproduct['quantity']; 

                    /* if(count($productprocessdata['outproducts']) > 0){
                        $total_oc_amount_perproduct = 0;
                        if(count($productprocessdata['outextracharges']) > 0){
                            $total_oc_amount_perproduct = array_sum(array_column($productprocessdata['outextracharges'],"amount")) / count($productprocessdata['outproducts']);
                        }
                        foreach($productprocessdata['outproducts'] as $_op){
                            $finalqty = $_op['quantity'] - $_op['scrapqty'];
                            if($inproduct['isfinalproduct']==1 || $_op['productpriceid'] == $inproduct['productpriceid']){
                                if($inqty > 0){
                                    if($inqty > $finalqty){
                                        $calculatelandingcost += ($_op['price'] * $finalqty) + $total_oc_amount_perproduct;
                                        $inqty -= $finalqty;

                                        // $calqty += $finalqty;
                                    }else if($inqty <= $finalqty){

                                        $calculatelandingcost += ($_op['price'] * $inqty) + $total_oc_amount_perproduct;
                                        $inqty = 0; 

                                        // $calqty += $inqty;
                                    }
                                }
                            }
                        }
                        if($calculatelandingcost > 0){
                            $landingcost = $calculatelandingcost / $inproduct['quantity'] + $inproduct['laborcost'];
                            $totallandingcost += $landingcost;
                            $landingcostamount = $calculatelandingcost + ($inproduct['quantity']*$inproduct['laborcost']);
                            $totallandingcostamount += $landingcostamount;
                        }
                    } */

                    $landingcost = $inproduct['landingcostperpiece'];
                    $totallandingcost += $landingcost;
                    $landingcostamount = $inproduct['landingcostperpiece'] * $inqty;
                    $totallandingcostamount += $landingcostamount;
                    
                    // $exrachargeperproduct = $totalexrachargeofproduct / $inproduct['quantity'];
                    $exrachargeperproduct = $addedcharge * $inproduct['quantity'];
                    
                    $totalexrachargeperproduct += $exrachargeperproduct;

                    $total = $landingcostamount + $totalexrachargeofproduct;
                    $subtotal = $subtotal + $total;
                    ?>
                    <tr>
                        <td><?=($p+1)?></td>
                        <td><?=$inproduct['productname']?></td> 
                        <td><?=$inproduct['variantname']?></td> 
                        <td class="text-center"><?=($inproduct['isfinalproduct']==1?"Yes":"No")?></td> 
                        <td class="text-right"><?=numberFormat($inproduct['price'],2,',')?></td> 
                        <td class="text-right"><?=$inproduct['quantity']?></td> 
                        <td class="text-right"><?=($inproduct['pendingquantity']>0?$inproduct['pendingquantity']:"-")?></td> 
                        <td class="text-right"><?=($inproduct['laborcost']>0?numberFormat($inproduct['laborcost'],2,','):"-")?></td> 
                        <td class="text-right"><?=($inproduct['laborcost']>0?numberFormat($inproduct['quantity']*$inproduct['laborcost'],2,','):"-")?></td> 
                        <td class="text-right"><?=numberFormat($landingcost,2,',')?></td> 
                        <td class="text-right"><?=numberFormat($landingcostamount,2,',')?></td> 
                        <td class="text-right"><?=numberFormat($exrachargeperproduct,2,',')?></td> 
                        <td class="text-right"><?=numberFormat($total,2,',')?></td> 
                    </tr>
            <?php } ?>
                <tr>
                    <th colspan="5" class="text-right">Total</th>
                    <th class="text-right"><?=$totalqty?></th> 
                    <th class="text-right"><?=($totalpendingqty>0)?numberFormat($totalpendingqty,2,','):"-"?></th> 
                    <th class="text-right"><?=($totallaborcost>0)?numberFormat($totallaborcost,2,','):"-"?></th>
                    <th class="text-right"><?=($totallaborcostamount>0)?numberFormat($totallaborcostamount,2,','):"-"?></th>
                    <th class="text-right"><?=($totallandingcost>0)?numberFormat($totallandingcost,2,','):"-"?></th>
                    <th class="text-right"><?=($totallandingcostamount>0)?numberFormat($totallandingcostamount,2,','):"-"?></th>
                    <th class="text-right"><?=($totallandingcostamount>0)?numberFormat($totalexrachargeperproduct,2,','):"-"?></th>
                    <th class="text-right"><?=numberFormat($subtotal,2,',')?></th>  
                </tr>
                <?php if(count($productprocessdata['inextracharges']) > 0){
                    foreach($productprocessdata['inextracharges'] as $incharges){ 
                        //$subtotal = $subtotal + $incharges['amount'];
                        if($incharges['chargetype'] == 0){
                            $amount = $incharges['amount'];
                        }else{
                            $amount = $incharges['amount'] * $totalqty;
                        } 
                        ?>
                    <tr>
                        <td colspan="12" class="text-right"><?=$incharges['extrachargesname']?></td>
                        <td class="text-right"><?=numberFormat($amount,2,',')?></td>  
                    </tr>
                <?php } ?>
                    <tr>
                        <th colspan="12" class="text-right">Net Total</th>
                        <th class="text-right"><?=numberFormat($subtotal,2,',')?></th>  
                    </tr>
                <?php } ?>
            <?php }else{ ?>
                <tr>
                    <th colspan="13" class="text-center">No data available in table.</th>  
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php if(!empty($productprocessdata['certificatedata'])){ ?>
    <div class="col-md-12">
        <table id="" class="table table-striped table-bordered mb-sm <?=$panelborderclass?>" cellspacing="0" width="100%" style="<?=$panelborderstyle?>">
            <thead>
                <tr style="border-bottom:2px solid #ddd;">
                    <th colspan="<?=(!isset($printtype)?7:6)?>" class="">Certificate Details</th>  
                </tr>
                <tr>
                    <th class="width5">Sr. No.</th>
                    <th>Doc. No.</th> 
                    <th>Title</th> 
                    <th>Description</th> 
                    <th>Document Date</th> 
                    <?php if(!isset($printtype)){ ?>
                    <th class="text-center">Certificate</th> 
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
            <?php if(count($productprocessdata['certificatedata']) > 0){ 
                foreach($productprocessdata['certificatedata'] as $p=>$doc){ ?>
                    <tr>
                        <td><?=($p+1)?></td>
                        <td><?=$doc['docno']?></td> 
                        <td><?=ucwords($doc['title'])?></td>
                        <td><?=ucfirst($doc['remarks'])?></td> 
                        <td><?=$this->general_model->displaydate($doc['documentdate'])?></td>
                        <?php if(!isset($printtype)){ ?>
                        <td class="text-center"><a href="<?=PRODUCT_PROCESS_CERTIFICATE.$doc['filename']?>" class="<?=downloadlbltxt_class?>" title="<?=downloadlbltxt_title?>" download><?=downloadlbltxt_text?></a></td> 
                        <?php } ?> 
                    </tr>
            <?php }
            }else{ ?>
                <tr>
                    <th colspan="<?=(!isset($printtype)?7:6)?>" class="text-center">No data available in table.</th>  
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
<?php } ?>
<?php /* if($productprocessdata['type']==1){ ?>
<div class="col-md-6 mb-md">
    <table id="" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr style="border-bottom:2px solid #ddd;">
                <th colspan="2" class="">Process Option</th>  
            </tr>
        </thead>
        <tbody>
        <?php if(count($productprocessdata['optiondata']) > 0){ 
            foreach($productprocessdata['optiondata'] as $p=>$option){ ?>
                <tr>
                    <th><?=ucwords($option['name'])?></th>
                    <td class="text-right"><?=numberFormat($option['optionvalue'],2,',')?></td>
                </tr>
        <?php }
        }else{ ?>
            <tr>
                <th colspan="2" class="text-center">No data available in table.</th>  
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php } */ ?>