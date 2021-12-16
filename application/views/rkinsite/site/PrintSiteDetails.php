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
                            <th>Site Name</th>
                            <th>Site Manager</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>Entry Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($reportdata)){
                                $sr=1;
                                foreach($reportdata as $row){ ?>
                                    <tr>
                                        <td><?=$sr?></td>
                                        <td><?=($row->sitename!=''?$row->sitename:'-')?></td>
                                        <td><?php $sitemanager = array();
                                            $sitemanagernamearray = explode(",", $row->sitemanagername);
                                            foreach($sitemanagernamearray as $key=>$sitemanagerid){
                                            $sitemanager[] = $sitemanagernamearray[$key]; 
                                      }
                                    echo implode(", ",$sitemanager); ?></td>
                                        <td><?=($row->address!=''?$row->address:'-')?></td>
                                        <td><?=($row->cityname!=''?$row->cityname:'-')?></td>
                                        <td><?=($row->createddate!='0000-00-00 00:00:00'?$this->general_model->displaydatetime($row->createddate):'-')?></td>
                                        <?php $sr++ ?>
                                    </tr>
                              
                                <?php 
                                }?>
                            <?php }else{ ?>
                                <tr>
                                    <td colspan="6" class="text-center">No data available in table.</td>
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