<?php require_once(APPPATH."views/".ADMINFOLDER.'payment_receipt/Paymentreceiptheader.php');?>
<div class="row">
    <div class="col-md-12 mb-sm">
        <p class="text-center" style="font-size: 18px;color: #000"><b><?=$heading?> Voucher</b></p>
    </div>
    <?php require_once(APPPATH."views/".ADMINFOLDER.'payment_receipt/Paymentreceiptdetails.php');?>
</div>
<?php if(!empty($paymentreceiptdata['paymentreceiptstatushistory'])){ ?>
<div class="row">
    <div class="col-md-12 mb-sm">
        <div class="panel mb-md border-panel">
            <div class="panel-heading no-padding">
                <h2>Status History</h2>
            </div>
            <div class="panel-body no-padding">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered m-n invoice" width="100%" style="color: #000">
                        <thead>
                            <tr>
                                <th class="width8" <?=$style?>>Sr. No.</th>
                                <th <?=$style?>>Modified By</th>
                                <th <?=$style?>>Modified Date</th>
                                <th <?=$style?>>Reason</th>
                                <th class="width15 text-center" <?=$style?>>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php if(!empty($paymentreceiptdata['paymentreceiptstatushistory'])){
                                foreach($paymentreceiptdata['paymentreceiptstatushistory'] as $k=>$prsh){ ?>
                                    <tr>
                                        <td <?=$style?>><?=(++$k)?></td>
                                        <td <?=$style?>>
                                            <?php 
                                                $channellabel = '';
                                                $key = array_search($prsh['channelid'], array_column($channeldata, 'id'));
                                                if($prsh['channelid']!=0 && $prsh['type']==1){
                                                  if(!empty($channeldata) && isset($channeldata[$key])){
                                                      echo '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> '; 
                                                    ?>
                                                      <a href="<?=ADMIN_URL.'member/member-detail/'.$prsh['modifiedby']?>" title="<?=ucwords($prsh['name'])?>" target="_blank"><?=ucwords($prsh['name'])?></a>
                                                    <?php
                                                  }
                                                }else{
                                                  echo '<span class="label" style="background:#49bf88;">COMPANY</span>';
                                                }
                                                ?>
                                        </td>
                                        <td <?=$style?>><?=$this->general_model->displaydatetime($prsh['modifieddate'])?></td>
                                        <td><?=($prsh['reason']!="")?$prsh['reason']:"-"?></td>
                                        <td class="text-center" <?=$style?>>
                                            <?php
                                                if($prsh['status']==0){
                                                    echo "<span class='label label-warning'>Pending</span>";
                                                }else if($prsh['status']==1){
                                                    echo "<span class='label label-success'>Approved</span>";
                                                }else if($prsh['status']==2){
                                                    echo "<span class='label label-danger'>Cancelled</span>";
                                                }
                                            ?>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>


