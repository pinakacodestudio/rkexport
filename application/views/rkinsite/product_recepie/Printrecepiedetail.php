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
    </style>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css"  />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css"  />
</head>
<body style="background-color: #FFF;">
    <div class="row">
        <div class="col-md-12">
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Product Recepie</b></u></p>
        </div>
        <div class="col-md-12">
            <p class="" style="font-size: 14px;color: #000"><b>Product Name : </b><?=$printdata['productname']?></p>
        </div>
    </div>

    <div class="row m-n">
        <table class="table table-striped table-bordered mb-sm" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th colspan="5" <?=$style?>>Common Material</th>         
                </tr>
            </thead>
            <tbody>
        </table>
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th <?=$style?>>Sr. No.</th>         
                    <th <?=$style?>>Product Name</th>
                    <th <?=$style?>>Variant Name</th>
                    <th <?=$style?>>Unit</th>   
                    <th class="text-right" <?=$style?>>Value</th>   
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($printdata['commonrawmaterial'])){
                    foreach($printdata['commonrawmaterial'] as $k=>$row){ ?>
                        <tr>
                            <td <?=$style?>><?=$k+1?></td>
                            <td <?=$style?>><?=$row['productname']?></td>
                            <td <?=$style?>><?=$row['variantname']?></td>
                            <td <?=$style?>><?=$row['unit']?></td>
                            <td class="text-right" <?=$style?>><?=$row['value']?></td>
                        </tr>
                    <?php } ?>
                <?php }else{ ?>
                    <tr>
                        <th colspan="5" class="text-center" <?=$style?>>No data available.</th>         
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php if($printdata['variantmaterial']){ ?>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered mb-sm" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th colspan="5" <?=$style?>>Variant Wise Material</th>         
                    </tr>
                </thead>
                <tbody>
            </table>
            <?php 
                foreach($printdata['variantmaterial'] as $variantmaterial) { ?>
                    <table class="table table-striped table-bordered mb-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <td colspan="5" <?=$style?>><b>Variant Name : </b><?=$variantmaterial['variantname']?></td>         
                            </tr>
                            <tr>
                                <th <?=$style?>>Sr. No.</th>         
                                <th <?=$style?>>Product Name</th>
                                <th <?=$style?>>Variant Name</th>
                                <th <?=$style?>>Unit</th>   
                                <th class="text-right" <?=$style?>>Qty</th>   
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($variantmaterial['material'])){
                                foreach($variantmaterial['material'] as $k=>$row){ ?>
                                    <tr>
                                        <td <?=$style?>><?=$k+1?></td>
                                        <td <?=$style?>><?=$row['productname']?></td>
                                        <td <?=$style?>><?=$row['variantname']?></td>
                                        <td <?=$style?>><?=$row['unitname']?></td>
                                        <td class="text-right" <?=$style?>><?=$row['value']?></td>
                                    </tr>
                                <?php } ?>
                            <?php }else{ ?>
                                <tr>
                                    <th colspan="5" class="text-center" <?=$style?>>No data available.</th>         
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</body>
</html>



