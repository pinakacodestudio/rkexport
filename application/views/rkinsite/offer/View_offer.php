<style>
    .table{
        background-color: #fff !important;
    }
    .toggle, .toggle-on, .toggle-off { border-radius: 20px; }
    .toggle .toggle-handle { border-radius: 20px; }
</style>
<script>
    var offertype = '<?php if(!empty($offerdata)){ echo  $offerdata['offerdata']['type'];  }?>';
</script>
<div class="page-content">
    <div class="page-heading">
        <div class="btn-group dropdown dropdown-l dropdown-breadcrumbs">
            <a class="dropdown-toggle dropdown-toggle-style" data-toggle="dropdown" aria-expanded="false"><span>
                <i class="material-icons" style="font-size: 26px;">menu</i>
            </span> </a>
            <ul class="dropdown-menu dropdown-tl" role="menu">
            <label class="mt-sm ml-sm mb-n">Menu</label>
            <?php
                $subid = $this->session->userdata(base_url().'submenuid');
                foreach($subnavtabsmenu as $row){
                if($subid == $row['id']){ ?>
                    
                    <li class="active"><a href="javascript:void(0);"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
                
                <?php }else{ ?>
                    <li><a href="<?=base_url().ADMINFOLDER.$row['url']; ?>"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
                <?php } 
                } ?>
            </ul>
        </div> 
        <h1>View Offer Details</h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?=base_url(); ?><?=ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
                <li><a href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl'); ?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
                <li class="active">View Offer Details</li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-body no-padding">
                            <div class="col-md-10">
                                <?php if(!empty($offerdata)){ ?>
                                <h4><b>Offer : </b><?=ucwords($offerdata['offerdata']['name'])?></h4>
                                <?php
                                } ?>
                                <input type="hidden" id="offerid" value="<?php if(!empty($offerdata)){ echo $offerdata['offerdata']['id']; } ?>">
                            </div>
                            <div class="col-md-2 text-right pt-xs pb-xs">
                                <?php /* if(!empty($companydata)){ ?>
                                    <a class="<?=editbtn_class;?>" href="<?php echo ADMIN_URL.'member/company-edit/'.$companydata['id'].'/profile';?>" title="<?=editbtn_title?>"><?=editbtn_text;?></a>
                                <?php
                                } */ ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="tab-container tab-default">
                        <ul class="nav nav-tabs">
                            <li class="dropdown pull-right tabdrop hide">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-angle-down"></i> </a><ul class="dropdown-menu"></ul>
                            </li>
                            <li class="active">
                                <a href="#offertab" data-toggle="tab" aria-expanded="false">Offer Details<div class="ripple-container"></div></a>
                            </li>
                            <li class="">
                                <a href="#offerparticipantstab" data-toggle="tab" aria-expanded="false">Offer Participants<div class="ripple-container"></div></a>
                            </li>
                        </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="offertab">
                            <div class="row">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6" style="text-align: right;">
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <div class="col-md-6 pr-xs">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                    <th colspan="2" class="text-center">OFFER DETAILS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td width="30%">Offer Name</td>
                                                        <td><?=ucwords($offerdata['offerdata']['name'])?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%">Start Date</td>
                                                        <td><?=($offerdata['offerdata']['startdate']!="0000-00-00"?$this->general_model->displaydate($offerdata['offerdata']['startdate']):"-")?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%">End Date</td>
                                                        <td><?=($offerdata['offerdata']['enddate']!="0000-00-00"?$this->general_model->displaydate($offerdata['offerdata']['enddate']):"-")?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%">Minimum Bill Amount</td>
                                                        <td><?=(!empty($offerdata['offerdata']['minbillamount'])?"&#8377;".numberFormat($offerdata['offerdata']['minbillamount'],2,','):"-")?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%">No. Of Customer Used</td>
                                                        <td><?=(!empty($offerdata['offerdata']['noofcustomerused'])?$offerdata['offerdata']['noofcustomerused']:'-')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%">Maximum Usage</td>
                                                        <td><?=(!empty($offerdata['offerdata']['maximumusage'])?$offerdata['offerdata']['maximumusage']:'-')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%">Type</td>
                                                        <td><?php if($offerdata['offerdata']['type']==2){ echo "Product"; }elseif($offerdata['offerdata']['type']==3){ echo "Service"; }elseif($offerdata['offerdata']['type']==4){ echo "Target"; }else{ echo "Display Only"; } ?></td>
                                                    </tr>
                                                    <?php if($offerdata['offerdata']['type']==4){ ?>    
                                                    <tr>
                                                        <td width="30%">Target Value</td>
                                                        <td><?php echo numberFormat($offerdata['offerdata']['targetvalue'],2,','); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%">Reward Value</td>
                                                        <td><?php echo numberFormat($offerdata['offerdata']['rewardvalue'],2,',').($offerdata['offerdata']['rewardtype']==1?"%":CURRENCY_CODE); ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td width="30%">Offer Type</td>
                                                        <td><?php if($offerdata['offerdata']['offertype']==1){ echo "Fix"; }else{ echo "Brand"; } ?></td>
                                                    </tr>
                                                    <?php if($offerdata['offerdata']['type']==1){ ?>
                                                    <tr>
                                                        <td width="30%">User Activation Required</td>
                                                        <td><?php if($offerdata['offerdata']['useractivationrequired']==1){ echo "Yes"; }else{ echo "No"; } ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td width="30%">Entry Date</td>
                                                        <td><?=$this->general_model->displaydatetime($offerdata['offerdata']['createddate'])?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6 pl-xs">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                    <th colspan="2" class="text-center">OFFER DESCRIPTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td width="25%">Short Description</td>
                                                        <td><?=($offerdata['offerdata']['shortdescription']!=""?ucfirst($offerdata['offerdata']['shortdescription']):"-")?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="25%">Description</td>
                                                        <td><?=ucfirst($offerdata['offerdata']['description'])?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php if($offerdata['offerdata']['type']!=1){ ?>
                                        <div class="col-md-12 pr-xs">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                    <th colspan="2">OFFER PRODUCT COMBINATION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php if(!empty($offerdata['combination'])){ 
                                                    foreach($offerdata['combination'] as $combination){
                                                    ?>
                                                        <tr class="border-panel">
                                                            <td width="50%" colspan="<?=$offerdata['offerdata']['offertype']==1?"2":""?>"> 
                                                                <div class="yesno">
                                                                    <b>Multiplication&nbsp;&nbsp; : &nbsp;&nbsp;</b>
                                                                    <input type="checkbox" <?php if(isset($combination) && $combination['multiplication']==1){ echo 'checked'; }?> disabled>
                                                                </div>
                                                            </td>
                                                            <?php if($offerdata['offerdata']['offertype']==0){ ?>
                                                            <td><b>Minimum Purchase Amount&nbsp;&nbsp; : &nbsp;&nbsp;</b><?="&#8377; ".numberFormat($offerdata['offerdata']['minimumpurchaseamount'],2,',')?></td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr>
                                                            <td class="p-n">
                                                                <?php if(!empty($combination['purchaseproductdata'])){ ?>
                                                                <table class="table table-bordered mb-n">
                                                                    <tr>
                                                                        <th colspan="2">OFFER PURCHASE PRODUCTS</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th width="25%">Product Name</th>
                                                                        <th width="25%">Quantity</th>
                                                                    </tr>
                                                                    <?php foreach($combination['purchaseproductdata'] as $pp){ ?>
                                                                    <tr>
                                                                        <td><?=$pp['productname']?></td>
                                                                        <td><?=$pp['quantity']?></td>
                                                                    </tr>
                                                                <?php } 
                                                                } ?>
                                                                </table>
                                                            </td>
                                                            <td class="p-n">
                                                                <table class="table table-bordered mb-n">
                                                                    <tr>
                                                                        <th colspan="2">GIFT PRODUCTS</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th width="25%">Product Name</th>
                                                                        <th width="25%">Quantity</th>
                                                                    </tr>
                                                                <?php if(!empty($combination['offerproductdata'])){ ?>
                                                                <?php  
                                                                    foreach($combination['offerproductdata'] as $op){ ?>
                                                                     <tr>
                                                                    <td><?=$op['productname']." (".$op['offerdiscountlabel'].")"?></td>
                                                                    <td><?=$op['quantity']?></td>
                                                                    </tr>
                                                                <?php } 
                                                                } ?>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    <?php } 
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="offerparticipantstab">
                            <div class="row">
                            <?php if($offerdata['offerdata']['type']!=1){ ?>
                                <?php $this->load->view(ADMINFOLDER.'offer/Offerproductdetail.php');?>
                            <?php }else{ ?>
                                <?php $this->load->view(ADMINFOLDER.'offer/Offerparticipantsdetail.php');?>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
<script type="text/javascript">
$('.list-group-item').on('click', function() {
    $('.list-group-item').removeClass('active');
    $(this).addClass('active');
});

</script>