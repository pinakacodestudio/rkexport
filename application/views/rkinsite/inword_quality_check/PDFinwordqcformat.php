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
    table tr td {
        padding: 5px;
        /* border: 1px solid #666; */
        font-size: 12px;
    }
    </style>
    <!-- <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css" /> -->
</head>

<body style="background-color: #FFF;">
    <div class="row">
        <div class="col-md-12">
            <p class="text-center" style="font-size: 18px;color: #000"><u><b><?=$heading?></b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th <?=$style?>>Sr. No.</th>
                    <th <?=$style?>>Product Name</th>
                    <th <?=$style?> class="text-right">Receive Qty</th>
                    <th <?=$style?> class='text-right'>Visually Check</th>
                    <th <?=$style?> class='text-right'>Dimension Check</th>
                    <th <?=$style?> class= "text-right"> Final Stock Qty</th>
                </tr>
            </thead>
                <tbody>
                    <?php foreach($productdetails as $pd=>$value){ ?>
                        <tr>
                        <td <?=$style?>><?=$pd+1?></td>
                        <td <?=$style?>><?=$productdetails[$pd]['productname']?></td>
                        <td <?=$style?>class="width8 text-right"><div class=""><?=numberFormat($productdetails[$pd]['qty'],2)?></td>
                        <td <?=$style?>>
                            <div class='col-md-12 pl-xs'><b>Checked Qty. : </b><?=numberFormat($productdetails[$pd]['visuallycheckedqty'],2)?></div>
                            <div class='col-md-12 pl-xs'><b>Defect Qty. : </b><?=numberFormat($productdetails[$pd]['visuallydefectqty'],2)?></div>
                        </td>
                        <td <?=$style?>>
                            <div class='col-md-12 pl-xs'><b>Checked Qty. : </b><?=numberFormat($productdetails[$pd]['dimensioncheckedqty'],2)?></div>
                            <div class='col-md-12 pl-xs'><b>Defect Qty. : </b><?=numberFormat($productdetails[$pd]['dimensiondefectqty'],2)?></div>
                        </td>
                        <td <?=$style?> class=' finalqty text-right' id=''><?=($productdetails[$pd]['qty']-$productdetails[$pd]['visuallydefectqty']-$productdetails[$pd]['dimensiondefectqty'])?></td>
                        </tr>
                    <?php 
                        
                    } ?>  
            </tbody>
        </table>
        <?php  if(!empty($headerdata['remarks'])){ ?>
        <div class="col-md-12 p-n">
            <p><b>Remarks : </b><?=$headerdata['remarks'];?></p>
        </div>
        <?php } ?>
    </div>
</body>

</html>