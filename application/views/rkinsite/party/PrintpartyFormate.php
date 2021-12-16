<?php 
    $floatformat = '.';
    $decimalformat = ',';
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css"  />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css"  />
</head>
<body style="background-color: #FFF;">
    <?php require_once(APPPATH."views/".ADMINFOLDER.'Companyheader.php');?>
    <div class="row mb-md">
        <div class="col-md-12 text-center">
            <p style="font-size: 18px;color: #000"><u><b><?=$heading?></b></u></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body no-padding">
                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>            
                                <th>Sr No.</th>
                                <th>Party Name</th>
                                <th>Party Type</th>
                                <th>Contact No.1</th>
                                <th>Contact No.2</th>
                                <th>Email</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(!empty($reportdata)){
                            $sr=1;
                            foreach($reportdata as $k=>$row){ ?>
                                    <tr> 
                                        <td><?=$sr?></td>
                                        <td><?=($row->partyname!=''?$row->partyname:'-')?></td>
                                        <td><?=($row->partytype!=''?$row->partytype:'-')?></td>
                                        <td><?=($row->contactno1!=''?$row->contactno1:'-')?></td>
                                        <td><?=($row->contactno2!=''?$row->contactno2:'-')?></td>
                                        <td><?=($row->email!=''?$row->email:'-')?></td>
                                        <td><?php 
                                                $address='';
                                                if($row->address!=''){
                                                    $address.=$row->address.($row->cityid!=0?', ':'');
                                                }
                                                if($row->cityid!=0){
                                                    $address.=$row->cityname." (".$row->provincename.") ".$row->countryname;
                                                }
                                                if($address==''){
                                                    $address='-';
                                                }
                                                echo $address;
                                                ?></td>
                                        <?php $sr++ ?>
                                    </tr>
                                <?php 
                                }?>
                            <?php }else{ ?>
                                <tr>
                                    <td colspan="7" class="text-center">No data available in table.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>