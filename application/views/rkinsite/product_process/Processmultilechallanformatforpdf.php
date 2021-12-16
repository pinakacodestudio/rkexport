<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <style type="text/css">        
            .table tr td, .table tr th {
                padding: 10px 15px;
                border: 1px solid #666;
                font-size: 12px;
            }
            .tbl tr td, .tbl tr th {
                padding: 2px 0px;
                font-size: 12px;
            }
            .other-pages{
                page-break-after: always;
            }
        </style>
    </head>
    <body style="background-color:#FFF;"> 
    <?php if(!empty($multiplechallandata)){ 
            foreach($multiplechallandata as $k=>$challandata) { 
                
                $class="";
                if($k!=(count($multiplechallandata)-1) && count($challandata['productdata'])>5){
                    $class="other-pages";
                }
                ?>
        <div id="contentdiv" style="width: 100%;height:380px;">     
            <div class="row">
                <div class="col-md-12">
                <div style="width:70%;font-size: 12px;color: #000;display: inline-block;float:left;">                          
                    <?php if(!empty($challandata['vendorid'])){ ?>
                    <address>                               
                    <?php if($challandata['vendorname']!=''){ ?>
                        <?php echo '<b style="font-weight:bold;font-size:12px;">'."M/s., ".ucfirst($challandata['vendorname']).'</b>';?><br>
                    <?php } ?>
                    <?php if($challandata['vendoraddress']!=''){ ?>
                        <?php echo ucfirst($challandata['vendoraddress']);?><br>
                    <?php } ?>                 
                    <?php if(!empty($challandata['vendorcity'])){ 
                        if(!empty($challandata['vendorpincode'])){
                        ?>
                        <?php echo $challandata['vendorcity']." - ".$challandata['vendorpincode'].", ".$challandata['vendorprovince'].", ".$challandata['vendorcountry'];?><br>
                    <?php }else{ ?>
                        <?php echo $challandata['vendorcity'].", ".$challandata['vendorprovince'].", ".$challandata['vendorcountry'];?><br>
                    <?php }} ?> 
                    <?php if($challandata['vendoremail']!=''){ ?>
                        <b style="font-weight:bold;">Email : </b> <?php echo $challandata['vendoremail'];?><br>
                    <?php } ?> 
                    <?php if($challandata['vendormobile']!=''){ ?>
                        <b style="font-weight:bold;">Mobile : </b> <?php echo $challandata['vendormobile'];?>                   
                    <?php } ?>
                    
                    </address>
                    <?php } ?>
                </div>
                
                <div style="width:25%;font-size: 12px;color: #000;display: inline-block;float:right;">

                    <table class="tbl" cellspacing="0" width="100%">
                       <!--  <tr>
                            <th width="40%">Challan No.</th>                              
                            <th width="5%">&nbsp;:&nbsp;</th> 
                            <td>#<?php echo $challandata['id']; ?></td>                               
                        </tr> --> 
                        <tr>
                            <th>Challan Date</th>                              
                            <th>&nbsp;:&nbsp;</th> 
                            <td><?php echo $this->general_model->displaydatetime($challandata['createddate']); ?></td>                               
                        </tr> 
                        <tr>
                            <th>Process Name</th>                              
                            <th>&nbsp;:&nbsp;</th> 
                            <td><?php echo $challandata['processname']; ?></td>                               
                        </tr> 
                        <tr>
                            <th>Outwarded By</th>                              
                            <th>&nbsp;:&nbsp;</th> 
                            <td><?php echo $challandata['employeename']; ?></td>                               
                        </tr> 
                    </table>
                </div>     
            </div> 
        
                <div class="col-md-12">  
                    <p class="text-center" style="font-size:15px;color:#000;margin-top:0px;font-weight:bold;"><u>Out Product Detail</u></p>                        
                </div>
                </br></br>
                <div class="col-md-12">
                    <table style="margin-top:10px;" class="table table-striped table-bordered mb-sm" cellspacing="0" width="100%">
                        <thead>                      
                            <tr>
                                <th class="width8">Sr. No.</th>                              
                                <th>Job Card No.</th> 
                                <th>Product</th> 
                                <th>Variant</th>   
                                <th>Unit</th>   
                                <th style="text-align:right">Quantity</th>                                
                            </tr>                                                
                        </thead>
                        <tbody>
                        <?php 
                        $count=0;                       
                        if ($challandata['productdata']) {
                            $totalqty = 0;
                            for ($j=0;$j<count($challandata['productdata']);$j++) {
                                $row = $challandata['productdata'][$j];
                                $totalqty += $row['quantity'];
                                ?>
                                <tr>
                                    <td><?=++$count?></td>                                  
                                    <td>#<?=$row['productprocessid']?></td> 
                                    <td><?=$row['productname']?></td>  
                                    <td><?=($row['variantname']!=""?$row['variantname']:'-')?></td>  
                                    <td><?=$row['unit']?></td>
                                    <td style="text-align:right"><?=$row['quantity']?></td>
                                </tr>               
                            <?php
                            } ?>
                                <tr>
                                    <th colspan="5" class="text-right">Total Quantity</th>  
                                    <th class="text-right"><?=numberFormat($totalqty,2,',')?></th>  
                                </tr>               
                        <?php }else{?>
                            <tr><td colspan="6" style="text-align:center;font-weight:bold;">No data available.</td></tr>
                        <?php } ?>  
                        </tbody>
                    </table>            
                </div>
            </div>

            <div class="row mb-xl <?php echo $class; ?>">
                <div class="col-md-12">
                    <div class="panel-body p-n pt-md pb-xs">
                        <div style="padding-top:10px;width:50%;font-size: 10px;color: #000;display: inline-block;float:left"> 
                                        
                        </div>
                        <div style="margin-right:-25px;margin-top:50px;width:18%;font-size: 12px;color: #000;display: inline-block;float:right">                       
                            <label><b style="font-weight:bold;">Signature</b></label>                     
                        </div> 
                    </div>
                </div>            
            </div>        
        <div>
    <?php } } ?>
    </body>
</html>