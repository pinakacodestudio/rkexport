<?php
$panelborderclass = "border-panel";
$panelborderstyle = "";
if(isset($printtype) && $printtype==1){
    $panelborderclass = ""; 
    $panelborderstyle = "border: 1px solid #ddd;";        
}
$style = $padding0 = '';
$bordercolor = "#ddd";
if(isset($printtype) && $printtype==1){
    $style = 'style="padding: 5px;border: 1px solid #666;font-size: 12px;"';
    $padding0 = "p-n";
    $bordercolor = "#666";
}
?>
<?php if(isset($printtype) && $printtype==1){ ?>
    <div class="col-md-12 mb-md" style="padding: 0px 5px;border: 1px solid #666;">
        <h5><b>Job Name : <?=$process['processname']?></b></h5>
    
<?php }else{ ?>
    <div class="panel panel-default <?=$panelborderclass?>" style="<?=$panelborderstyle?>">
        <div class="panel-heading">
            <div class="col-md-6 p-n">
                <h2><b>Job Name : <?=$process['processname']?></b></h2>
            </div>
            <div class="col-md-6 p-n text-right">
                <?php if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printallprocessdetail(<?=$process['processgroupmappingid']?>,<?=$process['productprocessid']?>,'process')" title=<?=printbtn_title?>><?=printbtn_text?></a>
                <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0);" title="<?=exportpdfbtn_title?>" onclick="exporttopdfallprocessdetail(<?=$process['processgroupmappingid']?>,<?=$process['productprocessid']?>,'process')"><?=exportpdfbtn_text;?></a>	
                <?php } ?>			
            </div>	
        </div>
        <div class="panel-body pt-sm pb-sm">
<?php } ?>

    <?php if(!empty($process['productprocessdata'])){ 
        foreach($process['productprocessdata'] as $row){
            $calculatelandingcost = 0;
            $head = "";
            if($row['type']==0 && $row['isreprocess']==0){
                $head = "Stock OUT Details";
            }else if($row['type']==0 && $row['isreprocess']==1){
                $head = "Send for Reprocessing Details";
            }else if($row['type']==1){
                $head = "Stock IN Details";
            } ?>
    
            <?php if(isset($printtype) && $printtype==1){ ?>
                <div class="col-md-12 mb-sm pl-sm pr-sm" style="border:1px solid #666;">
                    <h5><b><?=$head?></b></h5>
            <?php }else{ ?>
                <div class="panel panel-default" style="border:2px solid #ddd;">
                    <div class="panel-heading">
                        <h2><b><?=$head?></b></h2>
                    </div>
                    <div class="panel-body no-padding">
            <?php } ?>
        
                <div class="col-md-12 <?=$padding0?>">
                    <table class="table mb-md" cellspacing="0" width="100%">
                        <tbody>
                            <tr>
                                <th width="20%">Job Card</th> 
                                <th width="2%">:</th> 
                                <td width="29%">#<?=$row['id']?></td> 
                                <th width="20%">Processed By</th> 
                                <th width="2%">:</th> 
                                <td width="29%"><?=($row['processbymemberid']==1?"In-House Emp":"Other Party")?></td>  
                            </tr>
                            <tr>
                                <th width="20%"><?=($row['processbymemberid']==1?"Machine":"Vendor")?></th> 
                                <th width="2%">:</th> 
                                <td width="29%"><?=ucwords($row['vendorname'])?></td> 
                                <th>Transaction Date</th> 
                                <th>:</th> 
                                <td><?=$this->general_model->displaydate($row['transactiondate'])?></td> 
                            </tr>
                            <tr>
                                <th>Batch No.</th> 
                                <th>:</th> 
                                <td><?=($row['batchno']!=""?$row['batchno']:"-")?></td> 
                                <th>Order Number</th> 
                                <th>:</th> 
                                <td>
                                <?php
                                if(isset($printtype) && $printtype==1){
                                    echo ($row['ordernumber']!="")?$row['ordernumber']:"-";
                                }else{
                                    echo ($row['ordernumber']!="")?'<a href="'.ADMIN_URL.'order/view-order/'.$row['orderid'].'" target="_blank">'.$row['ordernumber'].'</a>':"-";
                                }
                                ?></td> 
                            </tr>
                            <tr>
                                <th>Buyer Name</th> 
                                <th>:</th> 
                                <td>
                                <?php
                                if(isset($printtype) && $printtype==1){
                                    echo ($row['buyername']!="")?$row['buyername']:"-";
                                }else{
                                    echo ($row['buyername']!="")?'<a href="'.ADMIN_URL.'member/member-detail/'.$row['buyerid'].'" target="_blank">'.$row['buyername'].'</a>':"-";
                                } ?></td> 
                                <th>Estimate Date</th> 
                                <th>:</th> 
                                <td><?=($row['estimatedate']!="0000-00-00")?$this->general_model->displaydate($row['estimatedate']):"-"?></td>
                            </tr>
                            <tr>
                                <?php if($row['outproductcount'] == 0 && $row['inproductcount'] == 0){ ?>
                                <th>Status</th> 
                                <th>:</th> 
                                <td><?php
                                if(isset($printtype) && $printtype==1){
                                    echo $row['processstatuses'];
                                }else{
                                    if($row['processstatus']==1){
                                        echo '<span class="label label-success">'.$row['processstatuses'].'</span>';
                                    }else{
                                        echo '<span class="label label-warning">'.$row['processstatuses'].'</span>';
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
                            <?php if($row['comments']!=""){ ?>
                            <tr>
                                <th>Remarks</th> 
                                <th>:</th>  
                                <td colspan="4" class="text-justify"><?=ucfirst($row['comments'])?></td> 
                            </tr> 
                            <?php } ?>  
                        </tbody>
                    </table>
                </div>
                <?php if(count($row['outproducts']) > 0){ ?>
                <div class="col-md-12 <?=$padding0?>">
                    <table id="" class="table table-striped table-bordered mb-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr style="border-bottom:2px solid <?=$bordercolor?>;">
                                <th colspan="7" <?=$style?>>OUT Product Material Details</th>  
                            </tr>
                            <tr>
                                <th class="width5" <?=$style?>>Sr. No.</th>
                                <th <?=$style?>>Product Name</th> 
                                <th <?=$style?>>Variant Name</th> 
                                <th <?=$style?>>Unit</th> 
                                <!-- <th <?=$style?> class="text-center">Additional / Supportive</th> --> 
                                <th <?=$style?> class="text-right">Price (<?=CURRENCY_CODE?>)</th> 
                                <th <?=$style?> class="text-right">Quantity</th> 
                                <th <?=$style?> class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th> 
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(count($row['outproducts']) > 0){ 
                            $subtotal = $totalqty = 0;
                            foreach($row['outproducts'] as $p=>$outproduct){ 
                                $total = $outproduct['price']*$outproduct['quantity'];
                                $subtotal = $subtotal + $total;
                                $totalqty = $totalqty + $outproduct['quantity'];
                                ?>
                                <tr>
                                    <td <?=$style?>><?=($p+1)?></td>
                                    <td <?=$style?>><?=$outproduct['productname']?></td> 
                                    <td <?=$style?>><?=$outproduct['variantname']?></td> 
                                    <td <?=$style?>><?=$outproduct['unit']?></td> 
                                    <!-- <td <?=$style?> class="text-center"><?=($outproduct['issupportingproduct']==1?"Yes":"No")?></td> -->
                                    <td <?=$style?> class="text-right"><?=numberFormat($outproduct['price'],2,',')?></td> 
                                    <td <?=$style?> class="text-right"><?=$outproduct['quantity']?></td> 
                                    <td <?=$style?> class="text-right"><?=numberFormat($total,2,',')?></td> 
                                </tr>
                        <?php } ?>
                            <tr>
                                <th <?=$style?> colspan="5" class="text-right">Total</th>
                                <th <?=$style?> class="text-right"><?=$totalqty?></th> 
                                <th <?=$style?> class="text-right"><?=numberFormat($subtotal,2,',')?></th>  
                            </tr>
                            <?php if(count($row['outextracharges']) > 0){
                                foreach($row['outextracharges'] as $outcharges){ 
                                    $subtotal = $subtotal + $outcharges['amount'];
                                    ?>
                                <tr>
                                    <td <?=$style?> colspan="6" class="text-right"><?=$outcharges['extrachargesname']?></td>
                                    <td <?=$style?> class="text-right"><?=numberFormat($outcharges['amount'],2,',')?></td>  
                                </tr>
                            <?php } ?>
                                <tr>
                                    <th <?=$style?> colspan="6" class="text-right">Net Total</th>
                                    <th <?=$style?> class="text-right"><?=numberFormat($subtotal,2,',')?></th>  
                                </tr>
                            <?php } ?>
                        <?php }else{ ?>
                            <tr>
                                <th <?=$style?> colspan="7" class="text-center">No data available in table.</th>  
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php $calculatelandingcost = $subtotal;
                 } ?>
                <?php if($row['type']==1 && count($row['inproducts']) > 0){ ?>
                    <?php if(!empty($row['rejectiondata'])){ ?>
                        <div class="col-md-12 <?=$padding0?>">
                            <table id="" class="table table-striped table-bordered mb-sm" cellspacing="0" width="100%">
                                <thead>
                                    <tr style="border-bottom:2px solid <?=$bordercolor?>;">
                                        <th colspan="7" <?=$style?>>Rejection</th>  
                                    </tr>
                                    <tr>
                                        <th class="width5" <?=$style?>>Sr. No.</th>
                                        <th <?=$style?>>Product Name</th> 
                                        <th <?=$style?>>Variant Name</th> 
                                        <th <?=$style?>>Unit</th> 
                                        <th class="text-right" <?=$style?>>Price (<?=CURRENCY_CODE?>)</th> 
                                        <th class="text-right" <?=$style?>>Quantity</th> 
                                        <th class="text-right" <?=$style?>>Total Amount (<?=CURRENCY_CODE?>)</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $subtotal = $totalqty = 0;
                                    foreach($row['rejectiondata'] as $r=>$rejection){ 
                                        $total = $rejection['price']*$rejection['quantity'];
                                        $subtotal = $subtotal + $total;
                                        $totalqty = $totalqty + $rejection['quantity'];
                                        ?>
                                        <tr>
                                            <td <?=$style?>><?=($r+1)?></td>
                                            <td <?=$style?>><?=$rejection['productname']?></td> 
                                            <td <?=$style?>><?=$rejection['variantname']?></td> 
                                            <td <?=$style?>><?=$rejection['unit']?></td> 
                                            <td class="text-right" <?=$style?>><?=numberFormat($rejection['price'],2,',')?></td> 
                                            <td class="text-right" <?=$style?>><?=$rejection['quantity']?></td> 
                                            <td class="text-right" <?=$style?>><?=numberFormat($total,2,',')?></td> 
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <th colspan="5" class="text-right" <?=$style?>>Total Amount (<?=CURRENCY_CODE?>)</th>
                                        <th <?=$style?> class="text-right"><?=$totalqty?></th> 
                                        <th class="text-right" <?=$style?>><?=numberFormat($subtotal,2,',')?></th>  
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <?php if(!empty($row['wastagedata'])){ ?>
                        <div class="col-md-12 <?=$padding0?>">
                            <table id="" class="table table-striped table-bordered mb-sm" cellspacing="0" width="100%">
                                <thead>
                                    <tr style="border-bottom:2px solid <?=$bordercolor?>;">
                                        <th colspan="7" <?=$style?>>Wastage</th>  
                                    </tr>
                                    <tr>
                                        <th class="width5" <?=$style?>>Sr. No.</th>
                                        <th <?=$style?>>Product Name</th> 
                                        <th <?=$style?>>Variant Name</th> 
                                        <th <?=$style?>>Unit</th> 
                                        <th class="text-right" <?=$style?>>Price (<?=CURRENCY_CODE?>)</th> 
                                        <th class="text-right" <?=$style?>>Quantity</th> 
                                        <th class="text-right" <?=$style?>>Total Amount (<?=CURRENCY_CODE?>)</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $subtotal = $totalqty = 0;
                                    foreach($row['wastagedata'] as $w=>$wastage){ 
                                        $total = $wastage['price']*$wastage['quantity'];
                                        $subtotal = $subtotal + $total;
                                        $totalqty = $totalqty + $wastage['quantity'];
                                        ?>
                                        <tr>
                                            <td <?=$style?>><?=($w+1)?></td>
                                            <td <?=$style?>><?=$wastage['productname']?></td> 
                                            <td <?=$style?>><?=$wastage['variantname']?></td> 
                                            <td <?=$style?>><?=$wastage['unit']?></td> 
                                            <td class="text-right" <?=$style?>><?=numberFormat($wastage['price'],2,',')?></td> 
                                            <td class="text-right" <?=$style?>><?=$wastage['quantity']?></td> 
                                            <td class="text-right" <?=$style?>><?=numberFormat($total,2,',')?></td> 
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <th colspan="5" class="text-right" <?=$style?>>Total Amount (<?=CURRENCY_CODE?>)</th>
                                        <th <?=$style?> class="text-right"><?=$totalqty?></th> 
                                        <th class="text-right" <?=$style?>><?=numberFormat($subtotal,2,',')?></th>  
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <?php if(!empty($row['lostdata'])){ ?>
                        <div class="col-md-12 <?=$padding0?>">
                            <table id="" class="table table-striped table-bordered mb-sm" cellspacing="0" width="100%">
                                <thead>
                                    <tr style="border-bottom:2px solid <?=$bordercolor?>;">
                                        <th colspan="7" <?=$style?>>Lost</th>  
                                    </tr>
                                    <tr>
                                        <th class="width5" <?=$style?>>Sr. No.</th>
                                        <th <?=$style?>>Product Name</th> 
                                        <th <?=$style?>>Variant Name</th> 
                                        <th <?=$style?>>Unit</th> 
                                        <th class="text-right" <?=$style?>>Price (<?=CURRENCY_CODE?>)</th> 
                                        <th class="text-right" <?=$style?>>Quantity</th> 
                                        <th class="text-right" <?=$style?>>Total Amount (<?=CURRENCY_CODE?>)</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $subtotal = $totalqty = 0;
                                    foreach($row['lostdata'] as $l=>$lost){ 
                                        $total = $lost['price']*$lost['quantity'];
                                        $subtotal = $subtotal + $total;
                                        $totalqty = $totalqty + $lost['quantity'];
                                        ?>
                                        <tr>
                                            <td <?=$style?>><?=($l+1)?></td>
                                            <td <?=$style?>><?=$lost['productname']?></td> 
                                            <td <?=$style?>><?=$lost['variantname']?></td> 
                                            <td <?=$style?>><?=$lost['unit']?></td> 
                                            <td class="text-right" <?=$style?>><?=numberFormat($lost['price'],2,',')?></td> 
                                            <td class="text-right" <?=$style?>><?=$lost['quantity']?></td> 
                                            <td class="text-right" <?=$style?>><?=numberFormat($total,2,',')?></td> 
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <th colspan="5" class="text-right" <?=$style?>>Total Amount (<?=CURRENCY_CODE?>)</th>
                                        <th <?=$style?> class="text-right"><?=$totalqty?></th> 
                                        <th class="text-right" <?=$style?>><?=numberFormat($subtotal,2,',')?></th>  
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <div class="col-md-12 <?=$padding0?>">
                        <table id="" class="table table-striped table-bordered mb-sm" cellspacing="0" width="100%">
                            <thead>
                                <tr style="border-bottom:2px solid <?=$bordercolor?>;">
                                    <th <?=$style?> colspan="13" class="">IN Details</th>  
                                </tr>
                                <tr>
                                    <th <?=$style?> class="width5">Sr. No.</th>
                                    <th <?=$style?>>Product Name</th> 
                                    <th <?=$style?>>Variant Name</th> 
                                    <th <?=$style?> class="text-center">Final Product</th> 
                                    <th <?=$style?> class="text-right">Price (<?=CURRENCY_CODE?>)</th> 
                                    <th <?=$style?> class="text-right">Quantity</th> 
                                    <th <?=$style?> class="text-right">Pending Quantity</th>
                                    <th <?=$style?> class="text-right">Labor Cost (<?=CURRENCY_CODE?>)</th>
                                    <th <?=$style?> class="text-right">Total Labor Cost (<?=CURRENCY_CODE?>)</th>  
                                    <th <?=$style?> class="text-right">Landing Cost (<?=CURRENCY_CODE?>)</th>
                                    <th <?=$style?> class="text-right">Total Landing Cost (<?=CURRENCY_CODE?>)</th> 
                                    <th <?=$style?> class="text-right">Total Extra Charges (<?=CURRENCY_CODE?>)</th> 
                                    <th <?=$style?> class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th> 
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(count($row['inproducts']) > 0){ 
                                $totalinextracharges =0;
                                /* foreach($row['inproducts'] as $p=>$inproduct){
                                    $totalinamount += $inproduct['price']*$inproduct['quantity'];
                                } */
                                if(count($row['inextracharges']) > 0){
                                    foreach($row['inextracharges'] as $incharges){ 
                                        $totalinextracharges += $incharges['amount'];
                                    }
                                }
                                // $calculatelandingcost = ($calculatelandingcost + $totalinamount) / count($row['inproducts']);
                                $totalexrachargeofproduct = $totalinextracharges / count($row['inproducts']);

                                $subtotal = $totalqty = $totalpendingqty = $totallaborcost = $totallaborcostamount = $totallandingcost = $totallandingcostamount = $totalexrachargeperproduct = 0;

                                foreach($row['inproducts'] as $p=>$inproduct){
                                   
                                    $totalqty = $totalqty + $inproduct['quantity'];
                                    $totalpendingqty = $totalpendingqty + $inproduct['pendingquantity'];
                                    $totallaborcost = $totallaborcost + $inproduct['laborcost'];
                                    $totallaborcostamount = $totallaborcostamount + ($inproduct['quantity']*$inproduct['laborcost']);

                                    $inqty = $inproduct['quantity']; 

                                    /* if(count($row['outproducts']) > 0){
                                        $total_oc_amount_perproduct = 0;
                                        if(count($row['outextracharges']) > 0){
                                            $total_oc_amount_perproduct = array_sum(array_column($row['outextracharges'],"amount")) / count($row['outproducts']);
                                        }
                                        foreach($row['outproducts'] as $_op){
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
                                    /* if(count($row['outproducts']) > 0 && $inproduct['isfinalproduct']==1){
                                        $prqty = $inproduct['quantity']; 
                                        $total_oc_amount_perproduct = 0;
                                        if(count($row['outextracharges']) > 0){
                                            $total_oc_amount_perproduct = array_sum(array_column($row['outextracharges'],"amount")) / count($row['outproducts']);
                                        }
                                        foreach($row['outproducts'] as $_op){ 
                                            if($prqty > 0){
                                                if($prqty > $_op['quantity']){
                                                    $calculatelandingcost += ($_op['price'] * $_op['quantity']) + $total_oc_amount_perproduct;
                                                    $prqty -= $_op['quantity'];
                                                }else if($prqty <= $_op['quantity']){
                
                                                    $calculatelandingcost += ($_op['price'] * $prqty) + $total_oc_amount_perproduct;
                                                    $prqty = 0; 
                                                }
                                            }
                                        }
                                    }

                                    $landingcost = $calculatelandingcost / $inproduct['quantity'] + $inproduct['laborcost'];;
                                    $totallandingcost += $landingcost;
                                    $landingcostamount = $calculatelandingcost + ($inproduct['quantity']*$inproduct['laborcost']);
                                    $totallandingcostamount += $landingcostamount; */

                                    $landingcost = $inproduct['landingcostperpiece'];
                                    $totallandingcost += $landingcost;
                                    $landingcostamount = $inproduct['landingcostperpiece'] * $inqty;
                                    $totallandingcostamount += $landingcostamount;
                                    
                                    $exrachargeperproduct = $totalexrachargeofproduct / $inproduct['quantity'];
                                    $totalexrachargeperproduct += $exrachargeperproduct;

                                    $total = $landingcostamount + $totalexrachargeofproduct;
                                    $subtotal = $subtotal + $total;
                                    
                                    ?>
                                    <tr>
                                        <td <?=$style?>><?=($p+1)?></td>
                                        <td <?=$style?>><?=$inproduct['productname']?></td> 
                                        <td <?=$style?>><?=$inproduct['variantname']?></td> 
                                        <td <?=$style?> class="text-center"><?=($inproduct['isfinalproduct']==1?"Yes":"No")?></td> 
                                        <td <?=$style?> class="text-right"><?=numberFormat($inproduct['price'],2,',')?></td> 
                                        <td <?=$style?> class="text-right"><?=$inproduct['quantity']?></td> 
                                        <td <?=$style?> class="text-right"><?=($inproduct['pendingquantity']>0?$inproduct['pendingquantity']:"-")?></td> 
                                        <td <?=$style?> class="text-right"><?=($inproduct['laborcost']>0?numberFormat($inproduct['laborcost'],2,','):"-")?></td> 
                                        <td <?=$style?> class="text-right"><?=($inproduct['laborcost']>0?numberFormat($inproduct['quantity']*$inproduct['laborcost'],2,','):"-")?></td> 
                                        <td <?=$style?> class="text-right"><?=numberFormat($landingcost,2,',')?></td> 
                                        <td <?=$style?> class="text-right"><?=numberFormat($landingcostamount,2,',')?></td>  
                                        <td <?=$style?> class="text-right"><?=numberFormat($exrachargeperproduct,2,',')?></td> 
                                        <td <?=$style?> class="text-right"><?=numberFormat($total,2,',')?></td>
                                    </tr>
                            <?php } ?>
                                <tr>
                                    <th <?=$style?> colspan="5" class="text-right">Total</th>
                                    <th <?=$style?> class="text-right"><?=$totalqty?></th> 
                                    <th <?=$style?> class="text-right"><?=$totalpendingqty?></th> 
                                    <th <?=$style?> class="text-right"><?=($totallaborcost>0)?numberFormat($totallaborcost,2,','):"-"?></th>
                                    <th <?=$style?> class="text-right"><?=($totallaborcostamount>0)?numberFormat($totallaborcostamount,2,','):"-"?></th>
                                    <th <?=$style?> class="text-right"><?=($totallandingcost>0)?numberFormat($totallandingcost,2,','):"-"?></th>
                                    <th <?=$style?> class="text-right"><?=($totallandingcostamount>0)?numberFormat($totallandingcostamount,2,','):"-"?></th>
                                    <th <?=$style?> class="text-right"><?=($totallandingcostamount>0)?numberFormat($totalexrachargeperproduct,2,','):"-"?></th>
                                    <th <?=$style?> class="text-right"><?=numberFormat($subtotal,2,',')?></th>  
                                </tr>
                                <?php if(count($row['inextracharges']) > 0){
                                    foreach($row['inextracharges'] as $incharges){ 
                                        // $subtotal = $subtotal + $incharges['amount'];
                                        ?>
                                        <tr>
                                            <td <?=$style?> colspan="12" class="text-right"><?=$incharges['extrachargesname']?></td>
                                            <td <?=$style?> class="text-right"><?=numberFormat($incharges['amount'],2,',')?></td>  
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <th <?=$style?> colspan="12" class="text-right">Net Total</th>
                                        <th <?=$style?> class="text-right"><?=numberFormat($subtotal,2,',')?></th>  
                                    </tr>
                                <?php } ?>
                            <?php }else{ ?>
                                <tr>
                                    <th <?=$style?> colspan="12" class="text-center">No data available in table.</th>  
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if(!empty($row['certificatedata'])){ ?>
                    <div class="col-md-12 <?=$padding0?>">
                        <table id="" class="table table-striped table-bordered mb-sm" cellspacing="0" width="100%">
                            <thead>
                                <tr style="border-bottom:2px solid <?=$bordercolor?>;">
                                    <th <?=$style?> colspan="<?=(!isset($printtype)?6:5)?>" class="">Certificate Details</th>  
                                </tr>
                                <tr>
                                    <th <?=$style?> class="width5">Sr. No.</th>
                                    <th <?=$style?>>Doc. No.</th> 
                                    <th <?=$style?>>Title</th> 
                                    <th <?=$style?>>Description</th> 
                                    <th <?=$style?>>Document Date</th> 
                                    <?php if(!isset($printtype)){ ?>
                                    <th <?=$style?> class="text-center">Certificate</th> 
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(count($row['certificatedata']) > 0){ 
                                foreach($row['certificatedata'] as $p=>$doc){ ?>
                                    <tr>
                                        <td <?=$style?>><?=($p+1)?></td>
                                        <td <?=$style?>><?=$doc['docno']?></td> 
                                        <td <?=$style?>><?=ucwords($doc['title'])?></td>
                                        <td <?=$style?>><?=ucfirst($doc['remarks'])?></td> 
                                        <td <?=$style?>><?=$this->general_model->displaydate($doc['documentdate'])?></td> 
                                        <?php if(!isset($printtype)){ ?>
                                        <td <?=$style?> class="text-center"><a href="<?=PRODUCT_PROCESS_CERTIFICATE.$doc['filename']?>" class="<?=downloadlbltxt_class?>" title="<?=downloadlbltxt_title?>" download><?=downloadlbltxt_text?></a></td> 
                                        <?php } ?>
                                    </tr>
                            <?php }
                            }else{ ?>
                                <tr>
                                    <th <?=$style?> colspan="<?=(!isset($printtype)?6:5)?>" class="text-center">No data available in table.</th>  
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>
                <?php } ?>
            <?php if(isset($printtype) && $printtype==1){ ?>
                </div> 
            <?php }else{ ?>
                    </div>
                </div>
            <?php } ?>
        
    
    <?php }
    } ?>

<?php if(isset($printtype) && $printtype==1){ ?>
    </div>
<?php }else{ ?>
        </div>
    </div>
<?php } ?>