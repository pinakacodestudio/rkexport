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
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css" />
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
                    <th <?=$style?>>Sr No.</th>
                    <th <?=$style?>>Party Name</th>
                    <th <?=$style?>>Party Type</th>
                    <th <?=$style?>>Contact No. 1</th>
                    <th <?=$style?>>Contact No. 2</th>
                    <th <?=$style?>>Email</th>
                    <th <?=$style?>>Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    $sr=1;
                    foreach($reportdata as $k=>$row){ ?>
                <tr> 
                    <td <?=$style?>><?=$sr?></td>
                    <td <?=$style?>><?=($row->partyname!=''?$row->partyname:'-')?></td>
                    <td <?=$style?>><?=($row->partytype!=''?$row->partytype:'-')?></td>
                    <td <?=$style?>><?=($row->contactno1!=''?$row->contactno1:'-')?></td>
                    <td <?=$style?>><?=($row->contactno2!=''?$row->contactno2:'-')?></td>
                    <td <?=$style?>><?=($row->email!=''?$row->email:'-')?></td>
                    <td <?=$style?>><?php $address='';
                                                if($row->address!=''){
                                                    $address.=$row->address.($row->cityid!=0?', ':'');
                                                }
                                                if($row->cityid!=0){
                                                    $address.=$row->cityname." (".$row->provincename.") ".$row->countryname;
                                                }
                                                if($address==''){
                                                    $address='-';
                                                }
                                                echo $address; ?></td>
                    <?php $sr++ ?>
                </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>
</body>

</html>