<?php 
    $floatformat = '.';
    $decimalformat = ',';
    $style = 'style="padding: 5px;border: 1px solid #666;font-size: 12px;"';
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        /*table>thead>tr>th{
            padding: 5px;
        }*/
        @page { 
            size: landscape;
        }
    </style>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css"  />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css"  />
</head>
<body style="background-color: #FFF;">
    <?php require_once(APPPATH."views/".ADMINFOLDER.'Companyheader.php');?>
    <div class="row mb-md">
        <div class="col-md-12">
            <p style="font-size: 18px;color: #000"><b><?=$heading?></b></p>
            <p style="font-size: 18px;color: #000"><b>Date : <?=$fromdate?> to <?=$todate?></b></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th <?=$style?>>GST No.</th>
                    <th <?=$style?>><?=Member_label?> Name</th>
                    <th <?=$style?>>City Name</th>
                    <th <?=$style?>>Invoice No.</th>
                    <th <?=$style?>>Invoice Date</th>
                    <th <?=$style?> class="text-right">Invoice Value</th>
                    <th <?=$style?>>Place of Supply</th>
                    <th <?=$style?> class="text-right">Reverse Charge</th>
                    <th <?=$style?> class="text-right">Tax Rate (%)</th>
                    <th <?=$style?> class="text-right">Taxable Value</th>
                    <th <?=$style?> class="text-right">Integrated to (IGST)</th>
                    <th <?=$style?> class="text-right">Central to (CGST)</th>
                    <th <?=$style?> class="text-right">State/UT to (SGST)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    $totaltaxablevalue = $totaligst = $totalcgst = $totalsgst = 0;
                    foreach($reportdata as $row){ 
                        
                        $taxablevalue = number_format($row->taxablevalue,2,'.','');
                        $totaltaxablevalue += $taxablevalue;
                        
                        $igst = number_format($row->igst,2,'.','');
                        $totaligst += $igst;

                        $cgst = number_format($row->cgst,2,'.','');
                        $totalcgst += $cgst;

                        $sgst = number_format($row->sgst,2,'.','');
                        $totalsgst += $sgst;
                        ?>
                        
                        <tr>
                            <td <?=$style?>><?=($row->gstno!=''?$row->gstno:'-')?></td>
                            <td <?=$style?>><?=ucwords($row->buyername).' ('.$row->buyercode.')'?></td>
                            <td <?=$style?>><?=ucwords($row->cityname)?></td>
                            <td <?=$style?>><?=$row->invoiceno?></td>
                            <td <?=$style?>><?=$this->general_model->displaydate($row->invoicedate)?></td>
                            <td <?=$style?> class="text-right"><?=numberFormat($row->invoicevalue,2,',')?></td>
                            <td <?=$style?>><?=($row->placeofsupply!=''?ucwords($row->placeofsupply):'-')?></td>
                            <td <?=$style?> class="text-right"><?=numberFormat($row->reversecharge,2,',')?></td>
                            <td <?=$style?> class="text-right"><?=numberFormat($row->taxrate,2,',')?></td>
                            <td <?=$style?> class="text-right"><?=numberFormat($row->taxablevalue,2,',')?></td>
                            <td <?=$style?> class="text-right"><?=numberFormat($row->igst,2,',')?></td>
                            <td <?=$style?> class="text-right"><?=numberFormat($row->cgst,2,',')?></td>
                            <td <?=$style?> class="text-right"><?=numberFormat($row->sgst,2,',')?></td>
                        </tr>
                    
                    <?php 
                    }?>
                    <tr>
                        <th <?=$style?> colspan="9" class="text-right">Total (<?=CURRENCY_CODE?>)</th>
                        <th <?=$style?> class="text-right"><?=numberFormat($totaltaxablevalue,2,',')?></th>
                        <th <?=$style?> class="text-right"><?=numberFormat($totaligst,2,',')?></th>
                        <th <?=$style?> class="text-right"><?=numberFormat($totalcgst,2,',')?></th>
                        <th <?=$style?> class="text-right"><?=numberFormat($totalsgst,2,',')?></th>
                    </tr>
                <?php }else{ ?>
                    <tr>
                        <td <?=$style?> colspan="13" class="text-center">No data available in table.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>
</html>



