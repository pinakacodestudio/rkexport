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
    $calculatelandingcost = 0;
    if(!empty($producttotal['totaloutproducts'])){ ?>
        <div class="col-md-12 p-n">
            <table id="" class="table table-striped table-bordered mb-md" cellspacing="0" width="100%">
                <thead>
                    <tr style="border-bottom:2px solid <?=$bordercolor?>;">
                        <th <?=$style?> colspan="7">Total OUT Product</th>  
                    </tr>
                    <tr>
                        <th <?=$style?> class="width5">Sr. No.</th>
                        <th <?=$style?>>Product Name</th> 
                        <th <?=$style?>>Variant Name</th> 
                        <th <?=$style?>>Unit</th> 
                        <th <?=$style?> class="text-right">Price (<?=CURRENCY_CODE?>)</th> 
                        <th <?=$style?> class="text-right">Quantity</th> 
                        <th <?=$style?> class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totaloutproductamount = $totalqty = 0;
                    foreach($producttotal['totaloutproducts'] as $i=>$op){
                        $totaloutproductamount = $totaloutproductamount + $op['totalamount'];
                        $totalqty = $totalqty + $op['quantity'];
                        ?>
                        <tr>
                            <td <?=$style?>><?=($i+1)?></td>
                            <td <?=$style?>><?=$op['productname']?></td> 
                            <td <?=$style?>><?=$op['variantname']?></td> 
                            <td <?=$style?>><?=$op['unit']?></td> 
                            <td <?=$style?> class="text-right"><?=numberFormat($op['price'],2,',')?></td> 
                            <td <?=$style?> class="text-right"><?=$op['quantity']?></td> 
                            <td <?=$style?> class="text-right"><?=numberFormat($op['totalamount'],2,',')?></td> 
                        </tr>
                    <?php } ?>
                    <tr>
                        <th <?=$style?> colspan="5" class="text-right">Total</th>
                        <th <?=$style?> class="text-right"><?=$totalqty?></th> 
                        <th <?=$style?> class="text-right"><?=numberFormat($totaloutproductamount,2,',')?></th>  
                    </tr>
                    <?php if(count($producttotal['totaloutcharges']) > 0){
                        foreach($producttotal['totaloutcharges'] as $outcharges){ 
                            $totaloutproductamount = $totaloutproductamount + $outcharges['amount'];
                            ?>
                            <tr>
                                <td <?=$style?> colspan="6" class="text-right"><?=$outcharges['extrachargesname']?></td>
                                <td <?=$style?> class="text-right"><?=numberFormat($outcharges['amount'],2,',')?></td>  
                            </tr>
                        <?php } ?>
                        <tr>
                            <th <?=$style?> colspan="6" class="text-right">Net Total</th>
                            <th <?=$style?> class="text-right"><?=numberFormat($totaloutproductamount,2,',')?></th>  
                        </tr>
                    <?php } 
                    $calculatelandingcost = $totaloutproductamount;
                    ?>
                </tbody>
            </table>
        </div>
    <?php } 
    
    if(!empty($producttotal['totalrejection'])){ ?>
        <div class="col-md-12 p-n">
            <table id="" class="table table-striped table-bordered mb-md" cellspacing="0" width="100%">
                <thead>
                    <tr style="border-bottom:2px solid <?=$bordercolor?>;">
                        <th <?=$style?> colspan="7">Total Rejection</th>  
                    </tr>
                    <tr>
                        <th <?=$style?> class="width5">Sr. No.</th>
                        <th <?=$style?>>Product Name</th> 
                        <th <?=$style?>>Variant Name</th> 
                        <th <?=$style?>>Unit</th> 
                        <th <?=$style?> class="text-right">Price (<?=CURRENCY_CODE?>)</th> 
                        <th <?=$style?> class="text-right">Quantity</th> 
                        <th <?=$style?> class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalrejectionamount = $total = $totalqty = 0;
                    foreach($producttotal['totalrejection'] as $r=>$rej){
                        $total = $rej['price'] * $rej['quantity'];
                        $totalrejectionamount = $totalrejectionamount + $total;
                        $totalqty = $totalqty + $rej['quantity'];
                        ?>
                        <tr>
                            <td <?=$style?>><?=($r+1)?></td>
                            <td <?=$style?>><?=$rej['productname']?></td> 
                            <td <?=$style?>><?=$rej['variantname']?></td> 
                            <td <?=$style?>><?=$rej['unit']?></td> 
                            <td <?=$style?> class="text-right"><?=numberFormat($rej['price'],2,',')?></td> 
                            <td <?=$style?> class="text-right"><?=$rej['quantity']?></td> 
                            <td <?=$style?> class="text-right"><?=numberFormat($total,2,',')?></td> 
                        </tr>
                    <?php } ?>
                    <tr>
                        <th <?=$style?> colspan="5" class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th>
                        <th <?=$style?> class="text-right"><?=$totalqty?></th> 
                        <th <?=$style?> class="text-right"><?=numberFormat($totalrejectionamount,2,',')?></th>  
                    </tr>
                </tbody>
            </table>
        </div>
    <?php }
    
    if(!empty($producttotal['totalwastage'])){ ?>
        <div class="col-md-12 p-n">
            <table id="" class="table table-striped table-bordered mb-md" cellspacing="0" width="100%">
                <thead>
                    <tr style="border-bottom:2px solid <?=$bordercolor?>;">
                        <th <?=$style?> colspan="7">Total Wastage</th>  
                    </tr>
                    <tr>
                        <th <?=$style?> class="width5">Sr. No.</th>
                        <th <?=$style?>>Product Name</th> 
                        <th <?=$style?>>Variant Name</th> 
                        <th <?=$style?>>Unit</th> 
                        <th <?=$style?> class="text-right">Price (<?=CURRENCY_CODE?>)</th> 
                        <th <?=$style?> class="text-right">Quantity</th> 
                        <th <?=$style?> class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalwastageamount = $total = $totalqty = 0;
                    foreach($producttotal['totalwastage'] as $w=>$was){
                        $total = $was['price'] * $was['quantity'];
                        $totalwastageamount = $totalwastageamount + $total;
                        $totalqty = $totalqty + $was['quantity'];
                        ?>
                        <tr>
                            <td <?=$style?>><?=($w+1)?></td>
                            <td <?=$style?>><?=$was['productname']?></td> 
                            <td <?=$style?>><?=$was['variantname']?></td> 
                            <td <?=$style?>><?=$was['unit']?></td> 
                            <td <?=$style?> class="text-right"><?=numberFormat($was['price'],2,',')?></td> 
                            <td <?=$style?> class="text-right"><?=$was['quantity']?></td> 
                            <td <?=$style?> class="text-right"><?=numberFormat($total,2,',')?></td> 
                        </tr>
                    <?php } ?>
                    <tr>
                        <th <?=$style?> colspan="5" class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th>
                        <th <?=$style?> class="text-right"><?=$totalqty?></th> 
                        <th <?=$style?> class="text-right"><?=numberFormat($totalwastageamount,2,',')?></th>  
                    </tr>
                </tbody>
            </table>
        </div>
    <?php } 

    if(!empty($producttotal['totallost'])){ ?>
        <div class="col-md-12 p-n">
            <table id="" class="table table-striped table-bordered mb-md" cellspacing="0" width="100%">
                <thead>
                    <tr style="border-bottom:2px solid <?=$bordercolor?>;">
                        <th <?=$style?> colspan="7">Total Lost</th>  
                    </tr>
                    <tr>
                        <th <?=$style?> class="width5">Sr. No.</th>
                        <th <?=$style?>>Product Name</th> 
                        <th <?=$style?>>Variant Name</th> 
                        <th <?=$style?>>Unit</th> 
                        <th <?=$style?> class="text-right">Price (<?=CURRENCY_CODE?>)</th> 
                        <th <?=$style?> class="text-right">Quantity</th> 
                        <th <?=$style?> class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totallostamount = $total = $totalqty = 0;
                    foreach($producttotal['totallost'] as $l=>$lost){
                        $total = $lost['price'] * $lost['quantity'];
                        $totallostamount = $totallostamount + $total;
                        $totalqty = $totalqty + $lost['quantity'];
                        ?>
                        <tr>
                            <td <?=$style?>><?=($l+1)?></td>
                            <td <?=$style?>><?=$lost['productname']?></td> 
                            <td <?=$style?>><?=$lost['variantname']?></td> 
                            <td <?=$style?>><?=$lost['unit']?></td> 
                            <td <?=$style?> class="text-right"><?=numberFormat($lost['price'],2,',')?></td> 
                            <td <?=$style?> class="text-right"><?=$lost['quantity']?></td> 
                            <td <?=$style?> class="text-right"><?=numberFormat($total,2,',')?></td> 
                        </tr>
                    <?php } ?>
                    <tr>
                        <th <?=$style?> colspan="5" class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th>
                        <th <?=$style?> class="text-right"><?=$totalqty?></th> 
                        <th <?=$style?> class="text-right"><?=numberFormat($totallostamount,2,',')?></th>  
                    </tr>
                </tbody>
            </table>
        </div>
    <?php } 

    if(!empty($producttotal['totalinproducts'])){ ?>
        <div class="col-md-12 p-n">
            <table id="" class="table table-striped table-bordered mb-md" cellspacing="0" width="100%">
                <thead>
                    <tr style="border-bottom:2px solid <?=$bordercolor?>;">
                        <th <?=$style?> colspan="12">Total IN Product</th>  
                    </tr>
                    <tr>
                        <th <?=$style?> class="width5">Sr. No.</th>
                        <th <?=$style?>>Product Name</th> 
                        <th <?=$style?>>Variant Name</th> 
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
                    <?php 
                    $totalinextracharges =0;
                    /* foreach($producttotal['totalinproducts'] as $p=>$inproduct){
                        $totalinamount += $inproduct['price']*$inproduct['quantity'];
                    } */
                    if(count($producttotal['totalincharges']) > 0){
                        foreach($producttotal['totalincharges'] as $incharges){ 
                            $totalinextracharges += $incharges['amount'];
                        }
                    }
                    // $calculatelandingcost = ($calculatelandingcost + $totalinamount) / count($producttotal['totalinproducts']);
                    $totalexrachargeofproduct = $totalinextracharges / count($producttotal['totalinproducts']);

                    $totalinproductamount = $totalqty = $totalpendingqty = $totallaborcost = $totallaborcostamount = $totallandingcost = $totallandingcostamount = $totalexrachargeperproduct = 0;

                    foreach($producttotal['totalinproducts'] as $i=>$ip){
                       
                        // $totalinproductamount = $totalinproductamount + $ip['totalamount'];
                        $totalqty = $totalqty + $ip['quantity'];
                        $totalpendingqty = $totalpendingqty + $ip['pendingquantity'];
                        $totallaborcost = $totallaborcost + $ip['laborcost'];
                        $totallaborcostamount = $totallaborcostamount + ($ip['totallaborcost']);

                        // $landingcost = $calculatelandingcost / $ip['quantity'] + $ip['laborcost'];;
                        // $totallandingcost += $landingcost;
                        // $landingcostamount = $calculatelandingcost + ($ip['quantity']*$ip['laborcost']);
                        // $totallandingcostamount += $landingcostamount;

                        $landingcost = $ip['landingcostperpiece'];
                        $totallandingcost += $landingcost;
                        $landingcostamount = $ip['landingcostperpiece'] * $ip['quantity'];
                        $totallandingcostamount += $landingcostamount;

                        $exrachargeperproduct = $totalexrachargeofproduct / $ip['quantity'];
                        $totalexrachargeperproduct += $exrachargeperproduct;

                        $total = $landingcostamount + $totalexrachargeofproduct;
                        $totalinproductamount = $totalinproductamount + $total;
                        ?>
                        <tr>
                            <td <?=$style?>><?=($i+1)?></td>
                            <td <?=$style?>><?=$ip['productname']?></td> 
                            <td <?=$style?>><?=$ip['variantname']?></td> 
                            <td <?=$style?> class="text-right"><?=numberFormat($ip['price'],2,',')?></td> 
                            <td <?=$style?> class="text-right"><?=$ip['quantity']?></td> 
                            <td <?=$style?> class="text-right"><?=($ip['pendingquantity']>0?$ip['pendingquantity']:"-")?></td> 
                            <td <?=$style?> class="text-right"><?=($ip['laborcost']>0?numberFormat($ip['laborcost'],2,','):"-")?></td> 
                            <td <?=$style?> class="text-right"><?=($ip['laborcost']>0?numberFormat($ip['totallaborcost'],2,','):"-")?></td> 
                            <td <?=$style?> class="text-right"><?=numberFormat($landingcost,2,',')?></td> 
                            <td <?=$style?> class="text-right"><?=numberFormat($landingcostamount,2,',')?></td> 
                            <td <?=$style?> class="text-right"><?=numberFormat($exrachargeperproduct,2,',')?></td> 
                            <td <?=$style?> class="text-right"><?=numberFormat($total,2,',')?></td> 
                        </tr>
                    <?php } ?>
                    <tr>
                        <th <?=$style?> colspan="4" class="text-right">Total</th>
                        <th <?=$style?> class="text-right"><?=$totalqty?></th> 
                        <th <?=$style?> class="text-right"><?=$totalpendingqty?></th> 
                        <th <?=$style?> class="text-right"><?=($totallaborcost>0)?numberFormat($totallaborcost,2,','):"-"?></th>
                        <th <?=$style?> class="text-right"><?=($totallaborcostamount>0)?numberFormat($totallaborcostamount,2,','):"-"?></th>
                        <th <?=$style?> class="text-right"><?=($totallandingcost>0)?numberFormat($totallandingcost,2,','):"-"?></th>
                        <th <?=$style?> class="text-right"><?=($totallandingcostamount>0)?numberFormat($totallandingcostamount,2,','):"-"?></th>
                        <th <?=$style?> class="text-right"><?=numberFormat($totalexrachargeperproduct,2,',')?></th>
                        <th <?=$style?> class="text-right"><?=numberFormat($totalinproductamount,2,',')?></th>  
                    </tr>
                    <?php if(count($producttotal['totalincharges']) > 0){
                        foreach($producttotal['totalincharges'] as $incharges){ 
                            // $totalinproductamount = $totalinproductamount + $incharges['amount'];
                            ?>
                            <tr>
                                <td <?=$style?> colspan="11" class="text-right"><?=$incharges['extrachargesname']?></td>
                                <td <?=$style?> class="text-right"><?=numberFormat($incharges['amount'],2,',')?></td>  
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    <tr>
                        <th <?=$style?> colspan="11" class="text-right">Net Total</th>
                        <th <?=$style?> class="text-right"><?=numberFormat($totalinproductamount,2,',')?></th>  
                    </tr>
                </tbody>
            </table>
        </div>
    <?php } ?>
