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
        table tr td{
            padding: 5px;/* border: 1px solid #666; */font-size: 12px;
        }
        /* @page {
            size: landscape;
        } */
    </style>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css"  />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css"  />
</head>
<body style="background-color: #FFF;">
    <div class="row mb-md">
        <div class="col-md-12">
            <div style="width:50%;font-size: 12px;color: #000;display: inline-block;float:left;">                          
                <table class="tbl" cellspacing="0" width="100%">
                    <tr>
                        <th width="20%" style="font-size: 12px;">Vendor</th>                              
                        <th width="5%" style="font-size: 12px;">&nbsp;:&nbsp;</th> 
                        <td style="font-size: 12px;"><?php echo $memberdata['name'].' ('.$memberdata['membercode'].')';?></td>                               
                    </tr> 
                    <tr>
                        <th style="font-size: 12px;">E-mail</th>                              
                        <th style="font-size: 12px;">&nbsp;:&nbsp;</th> 
                        <td style="font-size: 12px;"><?php echo $memberdata['email'];?></td>                               
                    </tr> 
                    <tr>
                        <th style="font-size: 12px;">Mobile No.</th>                              
                        <th style="font-size: 12px;">&nbsp;:&nbsp;</th> 
                        <td style="font-size: 12px;"><?php echo $memberdata['countrycode'].$memberdata['mobileno'];?></td>                               
                    </tr> 
                </table>
            </div>
        
            <div style="width:35%;font-size: 12px;color: #000;display: inline-block;float:right;">
                <table class="tbl" cellspacing="0" width="100%">
                    <tr>
                        <th width="33%" style="font-size: 12px;">Opening Qty</th>                              
                        <th width="5%" style="font-size: 12px;">&nbsp;:&nbsp;</th> 
                        <td style="font-size: 12px;"><?php echo numberFormat($openingbalance,2,','); ?></td>                               
                    </tr> 
                    <tr>
                        <th style="font-size: 12px;">Closing Qty</th>                              
                        <th style="font-size: 12px;">&nbsp;:&nbsp;</th> 
                        <td style="font-size: 12px;"><?php echo numberFormat($closingbalance,2,','); ?></td>                               
                    </tr> 
                    <tr>
                        <th style="font-size: 12px;">Date</th>                              
                        <th style="font-size: 12px;">&nbsp;:&nbsp;</th> 
                        <td style="font-size: 12px;"><?php echo $date; ?></td>                               
                    </tr> 
                </table>
            </div> 
        </div>     
    </div> 
    <div class="row">
        <div class="col-md-12">
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Vendor Job Work Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th <?=$style?> class="width5">Sr. No.</th>          
                    <th <?=$style?>>Job Card</th>
                    <th <?=$style?>>Job Name</th>
                    <th <?=$style?>>Batch No.</th>
                    <th <?=$style?>>OrderID</th>
                    <th <?=$style?>>Product</th>
                    <th class="text-right" <?=$style?>>In Qty</th>
                    <th class="text-right" <?=$style?>>Out Qty</th>
                    <th class="text-right" <?=$style?>>Rejection Qty</th>
                    <th class="text-right" <?=$style?>>Wastage Qty</th>
                    <th class="text-right" <?=$style?>>Lost Qty</th>
                    <th class="text-right" <?=$style?>>Balance Qty</th>
                    <th <?=$style?>>Transaction Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    $totalqty = $totalamount = 0;
                    foreach($reportdata as $k=>$row){ ?>
                        <tr>
                            <td <?=$style?>><?=$k+1?></td>
                            <td <?=$style?>><?=$row->jobcard?></td>
                            <td <?=$style?>><?=$row->jobname?></td>
                            <td <?=$style?>><?=$row->batchno?></td>
                            <td <?=$style?>><?=($row->ordernumber!=""?$row->ordernumber:"-")?></td>
                            <td <?=$style?>><?=$row->productname?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat(($row->inqty!=''?$row->inqty:0),2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat(($row->outqty!=''?$row->outqty:0),2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat(($row->rejectqty!=''?$row->rejectqty:0),2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat(($row->wastageqty!=''?$row->wastageqty:0),2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat(($row->lostqty!=''?$row->lostqty:0),2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->balanceqty,2,',')?></td>
                            <td <?=$style?>><?=($row->transactiondate!="")?$this->general_model->displaydate($row->transactiondate):""?></td>
                        </tr>
                    <?php } ?>
                <?php }else{ ?>
                    <tr>
                        <td colspan="13" class="text-center" <?=$style?>>No data available.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



