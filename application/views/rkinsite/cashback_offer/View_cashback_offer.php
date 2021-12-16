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
        <h1>View Cashback Offer Details</h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?=base_url(); ?><?=ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
                <li><a href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl'); ?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
                <li class="active">View Cashback Offer Details</li>
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
                                <?php if(!empty($cashbackofferdata)){ ?>
                                <h4><b>Cashback Offer : </b><?=ucwords($cashbackofferdata['name'])?></h4>
                                <?php
                                } ?>
                                <input type="hidden" id="offerid" value="<?php if(!empty($cashbackofferdata)){ echo $cashbackofferdata['id']; } ?>">
                            </div>
                            <div class="col-md-2 text-right pt-xs pb-xs">
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
                                <a href="#offertab" data-toggle="tab" aria-expanded="false">Cashback Offer Details<div class="ripple-container"></div></a>
                            </li>
                            <li class="">
                                <a href="#producttab" data-toggle="tab" aria-expanded="false">Product Details<div class="ripple-container"></div></a>
                            </li>
                        </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="offertab">
                            <div class="row">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                        <tr>
                                            <td width="20%">Cashback Offer Name</td>
                                            <td><?=ucwords($cashbackofferdata['name'])?></td>
                                        </tr>
                                        <tr>
                                            <td>Start Date</td>
                                            <td><?=($cashbackofferdata['startdate']!="0000-00-00"?$this->general_model->displaydate($cashbackofferdata['startdate']):"-")?></td>
                                        </tr>
                                        <tr>
                                            <td>End Date</td>
                                            <td><?=($cashbackofferdata['enddate']!="0000-00-00"?$this->general_model->displaydate($cashbackofferdata['enddate']):"-")?></td>
                                        </tr>
                                        <tr>
                                            <td>Minimum Bill Amount (<?=CURRENCY_CODE?>)</td>
                                            <td><?=(!empty($cashbackofferdata['minbillamount'])?numberFormat($cashbackofferdata['minbillamount'],2,','):"-")?></td>
                                        </tr>
                                        <tr>
                                            <td>Short Description</td>
                                            <td><?=($cashbackofferdata['shortdescription']!=""?ucfirst($cashbackofferdata['shortdescription']):"-")?></td>
                                        </tr>
                                        <tr>
                                            <td>Description</td>
                                            <td><?=ucfirst($cashbackofferdata['description'])?></td>
                                        </tr>
                                        <tr>
                                            <td>Status</td>
                                            <td>
                                            <?php if($cashbackofferdata['status']==1){
                                                echo '<span class="label label-success">Active</span>';
                                            }else{
                                                echo '<span class="label label-danger">In Active</span>';
                                            }?>
                                        </tr>
                                        <tr>
                                            <td>Entry Date</td>
                                            <td><?=$this->general_model->displaydatetime($cashbackofferdata['createddate'])?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="producttab">
                            <div class="row">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <div class="col-md-8">
                                            <div class="panel-ctrls panel-tbl"></div>
                                        </div>
                                        <div class="col-md-4 form-group" style="text-align: right;">
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <table id="producttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>            
                                                    <th class="width8">Sr. No.</th>
                                                    <th>Product Name</th>  
                                                    <th>Earn Points</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                if(!empty($cashbackofferproductdata)){
                                                    foreach($cashbackofferproductdata as $i=>$row){?>
                                                        <tr>
                                                            <td><?=($i+1)?></td>
                                                            <td><?=$row['productname']?></td>
                                                            <td><?=$row['earnpoints']?></td>
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