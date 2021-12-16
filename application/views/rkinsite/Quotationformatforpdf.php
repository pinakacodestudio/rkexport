<?php
$floatformat = '.';
$decimalformat = ',';
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
       /*  table>thead>tr>th{
            padding: 8px;
        } */
    </style>
</head>
<body style="background-color: #FFF;">

<div class="row mb-sm">
    <div class="col-md-12">
        <div class="">
            <div class="panel-body no-padding">
                <div class="table-responsive">
                    <p class="text-center" style="font-size: 16px;color: #000"><u><b>Quotation</b></u></p>
                    <span style="color: #000;"><b>Part Details</b></span>
                    <?php /* ?>
                    <table class="table table-hover table-bordered m-n invoice" width="100%" style="color: #000">
                    <thead>
                        <tr>
                            
                            <th rowspan="2" style="padding: 5px;border: 1px solid #666;font-size: 12px;" width="5%">Sr. No.</th>
                            <th rowspan="2" style="padding: 5px;border: 1px solid #666;font-size: 12px;" width="25%">Product</th>
                            <th rowspan="2" style="padding: 5px;border: 1px solid #666;font-size: 12px;" width="5%">HSN Code</th>
                            <th rowspan="2" class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;" width="8%">Qty.</th>
                            <th rowspan="2" class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;" width="10%">Rate</th>
                            <?php if($QuotationData['quotationdetail']['displaydiscountcolumn']==1) { ?>
                            <th class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;" width="8%">Dis. (%)</th>
                            <? } ?>
                            <?php if($QuotationData['quotationdetail']['igst']==1) { ?>
                            <th class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;" width="8%">SGST (%)</th>
                            <th class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;" width="8%">CGST (%)</th>
                            <? }else{ ?>
                            <th class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;" width="8%">IGST (%)</th>
                            <? } ?>
                            <th rowspan="2" class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;" width="12%">Amount</th>
                        </tr>
                        <tr>
                            <?php if($QuotationData['quotationdetail']['displaydiscountcolumn']==1) { ?>
                            <th class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;">Amt.</th>
                            <? } ?>
                            <?php if($QuotationData['quotationdetail']['igst']==1) { ?>
                            <th class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;">Amt.</th>
                            <th class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;">Amt.</th>
                            <? }else{ ?>
                            <th class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;">Amt.</th>
                            <? } ?>
                        </tr>
                    </thead>
                    <tbody>
                            <?php
                                $FinalTotal = $subtotal = $totaltaxvalue = $totaldiscount = $discount = 0;
                                $hsncodedetails = $hsncode = array();
                                for($i=0;$i<count($QuotationData['quotationproduct']);$i++){ 
                                    $hsncode = array();
                                    $subtotal = 0;
                                    $sgst = $cgst = $igst = 0;
                                    $sgstamount = $cgstamount = $igstamount = $discountamount = 0;
                                    if($QuotationData['quotationdetail']['igst']==1) {
                                        $sgst = $QuotationData['quotationproduct'][$i]['tax']/2;
                                        $cgst = $QuotationData['quotationproduct'][$i]['tax']/2;
                                    }else{
                                        $igst = $QuotationData['quotationproduct'][$i]['tax'];
                                    }
                                    $discount = $QuotationData['quotationproduct'][$i]['discount'];
                                 ?>
                                    <tr>
                                        <td rowspan="2" style="padding: 8px;border: 1px solid #666;font-size: 12px;"><?=$i+1?></td>
                                        <td rowspan="2" style="padding: 8px;border: 1px solid #666;font-size: 12px;">
                                            <?php 
                                                $productname = ucwords($QuotationData['quotationproduct'][$i]['name']);
                                               
                                            ?>
                                            

                                        </td>
                                        <td rowspan="2" style="padding: 8px;border: 1px solid #666;font-size: 12px;"><?=$QuotationData['quotationproduct'][$i]['hsncode']?></td>
                                        <td rowspan="2" class="text-right" style="padding: 8px;border: 1px solid #666;font-size: 12px;">
                                            <?=$QuotationData['quotationproduct'][$i]['quantity'];?></td>
                                        <td rowspan="2" style="padding: 8px;border: 1px solid #666;font-size: 12px;" class="text-right">
                                            <?php
                                            
                                            $productprice = $QuotationData['quotationproduct'][$i]['price'];

                                            //$productprice = $productprice - ($productprice*$QuotationData['quotationproduct'][$i]['tax']/(100+$QuotationData['quotationproduct'][$i]['tax']));

                                            if ($discount>0 && $discount!=0) {
                                                $discountamount = ($productprice*$discount)/100;
                                                $productprice = $productprice - $discountamount;  
                                            }
                                            
                                            echo number_format($productprice, 2,$floatformat, $decimalformat);
                                            ?>
                                        </td>
                                        <?php if($QuotationData['quotationdetail']['displaydiscountcolumn']==1) { ?>
                                        <td <?=($discount>0)?'':'rowspan="2"'?> class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;">
                                            <?php echo ($discount>0)?number_format($discount, 2, $floatformat, $decimalformat):'-';?>      
                                        </td>
                                        <? } ?>
                                        <?php if($QuotationData['quotationdetail']['igst']==1) { ?>
                                        <td class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;"><?=number_format($sgst, 2, $floatformat, $decimalformat);?></td>
                                        <td class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;"><?=number_format($cgst, 2, $floatformat, $decimalformat);?></td>
                                        <? }else{ ?>
                                        <td class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;"><?=number_format($igst, 2, $floatformat, $decimalformat);?></td>
                                        <? } ?>
                                        <td rowspan="2" class="text-right" style="padding: 8px;border: 1px solid #666;font-size: 12px;">
                                            <?php  
                                                $total = $QuotationData['quotationproduct'][$i]['price'] * $QuotationData['quotationproduct'][$i]['quantity'];
                                                //$tax = $QuotationData['quotationproduct'][$i]['price'] * $QuotationData['quotationproduct'][$i]['tax']/(100+$QuotationData['quotationproduct'][$i]['tax']);
                                                $tax = $QuotationData['quotationproduct'][$i]['price'] * $QuotationData['quotationproduct'][$i]['tax']/100;
                                                $totaltaxvalue = $totaltaxvalue + ($tax * $QuotationData['quotationproduct'][$i]['quantity']);
                                                
                                                $subtotal = $subtotal + ($productprice * $QuotationData['quotationproduct'][$i]['quantity']);
                                                
                                                echo number_format($total, 2,$floatformat, $decimalformat); 

                                                if($QuotationData['quotationdetail']['igst']==1) {
                                                    $sgstamount = $tax/2;
                                                    $cgstamount = $tax/2;
                                                }else{
                                                    $igstamount = $tax;
                                                }

                                                $hsncode[] = $QuotationData['quotationproduct'][$i]['hsncode'];
                                                if (!array_key_exists($QuotationData['quotationproduct'][$i]['tax'],$hsncodedetails)){
                                                    $hsncodedetails[$QuotationData['quotationproduct'][$i]['tax']] = array('hsncode'=>$hsncode,'total'=>$subtotal);
                                                }else{
                                                    if(!in_array($QuotationData['quotationproduct'][$i]['hsncode'],$hsncodedetails[$QuotationData['quotationproduct'][$i]['tax']]['hsncode'])){
                                                        array_push($hsncodedetails[$QuotationData['quotationproduct'][$i]['tax']]['hsncode'], $QuotationData['quotationproduct'][$i]['hsncode']);
                                                    }
                                                    $hsncodedetails[$QuotationData['quotationproduct'][$i]['tax']]['total'] = $hsncodedetails[$QuotationData['quotationproduct'][$i]['tax']]['total'] + $subtotal;
                                                }
                                            ?>
                                            
                                        </td>
                                        
                                    </tr>
                                    <tr>
                                        <?php if($QuotationData['quotationdetail']['displaydiscountcolumn']==1 && $discountamount > 0) { ?>
                                        <td class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;">
                                            <?=number_format($discountamount, 2, ".", ",");?>
                                        </td>
                                        <? } ?>
                                        <?php if($QuotationData['quotationdetail']['igst']==1) { ?>
                                        <td class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;">
                                            <?=number_format($sgstamount, 2, ".", ",");?>
                                        </td>
                                        <td class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;">
                                            <?=number_format($cgstamount, 2, ".", ",");?>
                                        </td>
                                        <? }else{ ?>
                                        <td class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;">
                                            <?=number_format($igstamount, 2, ".", ",");?>
                                        </td>
                                        <? } ?>
                                    </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                   <?php  */ ?>

                        <?php require_once(APPPATH."views/".ADMINFOLDER.'quotation/Quotationproductdetails.php');?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-sm">
    <div class="col-md-12">
        <?php require_once(APPPATH."views/".ADMINFOLDER.'quotation/Quotationsummarydetails.php');?>
        <hr>
    </div>
</div>
<?php 
    /* $assessableamount = 0; 
    foreach ($hsncodedetails as $tax => $hsncoderow) {
        
        //$totaltaxvalue += $hsncoderow['total']*$tax/(100+$tax);
        $assessableamount += $hsncoderow['total'];
        //$subtotal += $hsncoderow['total']+($hsncoderow['total']*$tax/(100+$tax));
    }
    $subtotal = $assessableamount + $totaltaxvalue;
    $FinalTotal = (($subtotal)-$QuotationData['quotationdetail']['globaldiscount']);
    $roundoff = round($FinalTotal)-$FinalTotal;
    $FinalTotal = round($FinalTotal);
 
<div class="row mb-sm">
    <div class="col-md-8" style="width: 50%;float: left;">
        <div class="panel-body no-padding">
            <span><span style="color: #000;"><b>Amount (In Words):</b></span> <?=convert_number($FinalTotal)?></span>
        </div>
    </div>
    <div class="col-md-4" style="width: 35%;float: right;">
        <div class="panel-body no-padding" style="margin-bottom:10px;">
            <div class="table-responsive">
                <table class="table table-hover table-bordered m-n invoice" style="color: #000">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center" style="padding: 5px;border: 1px solid #666;font-size: 12px;">GST Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 5px;border: 1px solid #666;font-size: 12px;">Assessable Amount</td>
                            <td class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;"><?=number_format($assessableamount, 2,$floatformat, $decimalformat);?></td>
                        </tr>
                        <tr>
                            <td style="padding: 5px;border: 1px solid #666;font-size: 12px;">Total GST</td>
                            <td class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;"><?=number_format($totaltaxvalue, 2,$floatformat, $decimalformat);?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel-body no-padding">
            <div class="table-responsive">
                <table class="table table-hover table-bordered m-n invoice" style="color: #000">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center" style="padding: 5px;border: 1px solid #666;font-size: 12px;">Order Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 5px;border: 1px solid #666;font-size: 12px;">Sub Total</td>
                            <td class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;"><?=number_format($subtotal, 2,$floatformat, $decimalformat);?></td>
                        </tr>
                        <tr>
                            <td style="padding: 5px;border: 1px solid #666;font-size: 12px;">Discount</td>
                            <td class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;"><?=number_format($QuotationData['quotationdetail']['globaldiscount'], 2,$floatformat, $decimalformat);?></td>
                        </tr>
                        <tr>
                            <td style="padding: 5px;border: 1px solid #666;font-size: 12px;">Round Off</td>
                            <td class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;"><?=number_format($roundoff, 2,$floatformat, $decimalformat);?></td>
                        </tr>
                        <tr>
                            <td style="padding: 5px;border: 1px solid #666;font-size: 12px;"><b>Amount Payable</b></td>
                            <td class="text-right" style="padding: 5px;border: 1px solid #666;font-size: 12px;"><b><?=number_format($FinalTotal, 2,$floatformat, $decimalformat);?></b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php  */ ?>
</body>
</html>
