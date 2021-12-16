<style type="text/css">
.datepicker1 {
    text-align: left !important;
    border-radius: 3px !important;
}
.nav-tabs > li {
    margin-bottom: 0px;
}
.toggle, .toggle-on, .toggle-off { border-radius: 20px; }
.toggle .toggle-handle { border-radius: 20px; }
</style>
<script>
    var DEFAULT_COUNTRY_ID = '<?=DEFAULT_COUNTRY_ID?>';
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
        <h1><?=$this->session->userdata(base_url().'submenuname')?> Detail</h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?=base_url(); ?><?=ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
                <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">
            <div class="row">
                <div class="col-sm-3">
                    <?php if(!empty($vendordata)){ ?>
                    <div class="text-center">
                        <img class="img-thumbnail sidebarprofileimage" src="<?=$vendordata['image']!=''?PROFILE.$vendordata['image']:PROFILE.'noimage.png'?>">
                        <h4 class="sub-sidebar-heading">Name : <?=ucwords($vendordata['name'])?></h4>
                    </div>
                    <?php
                    } ?>

                    <div class="inbox-menu list-group-alternate">
                        <a href="#details" data-toggle="tab" class="list-group-item <?php if(isset($activetab) && $activetab==""){ echo "active"; }?>"><i class="material-icons">inbox</i>Summary</a>
                        <a href="#billingaddress" data-toggle="tab" class="list-group-item"><i class="material-icons">insert_drive_file</i>Vendor Address</a>
                        <a href="#order" data-toggle="tab" class="list-group-item"><i class="material-icons">shopping_cart</i>Purchase Order</a>
                        <?php //if($channeldata['memberspecificproduct']==1){ ?>
                        <a href="#memberproduct" data-toggle="tab" class="list-group-item <?php if(isset($activetab) && $activetab=="products"){ echo "active"; }?>"><i class="material-icons">reorder</i>Products</a>
                        <?php //} ?>
                        <?php if($channeldata['discount']==1){ ?>
                        <a href="#discounttab" data-toggle="tab" class="list-group-item"><i class="material-icons">attach_money</i>Discount</a>
                        <?php } ?>
                        <?php if($channeldata['quotation']==1){ ?>
                        <a href="#quotation" data-toggle="tab" class="list-group-item"><i class="material-icons">receipt</i>Purchase Quotation</a>
                        <?php } ?>
                        <?php if($channeldata['identityproof']==1){ ?>
                        <a href="#identityproof" data-toggle="tab" class="list-group-item"><i class="material-icons">note_add</i>Vendor Document</a>
                        <?php } ?>
                    </div>

                </div>

                <div class="col-sm-9 pl-n">
                    <input type="hidden" id="vendorid" name="vendorid" value="<?=$vendorid?>">
                    <input type="hidden" id="channelid" name="channelid" value="<?=$channelid?>">
                    <div class="">
                        <div class="">
                            <div class="tab-content">

                                <div class="tab-pane <?php if(isset($activetab) && $activetab==""){ echo "active"; }?>" id="details">

                                    <div class="col-md-12 p-n">
                                        <div class="panel panel-default border-panel">
                                            <div class="panel-heading">
                                                <div class="col-md-6"></div>
                                                <div class="col-md-6 form-group" style="text-align: right;">
                                                    <div class="pull-right">
                                                        <a href="<?=ADMIN_URL."vendor/vendor-edit/".$vendorid."/vendor-detail"; ?>" class="btn btn-info btn-raised btn-label" title="Edit Profile" ><i class="fa fa-pencil-square-o"></i> Edit Profile</a>
                                                        <?php 
                                                            if($channeldata['debitlimit']==1){
                                                        ?>
                                                       
                                                            <button class="btn btn-primary btn-raised btn-label" title="Edit Debit Limit" data-toggle="modal" data-target="#myModal"><i class="fa fa-pencil"></i> Edit Debit Limit
                                                                <div class="ripple-container"></div>
                                                            </button>
                                                        <?php
                                                        }
                                                        ?>                      
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel-body no-padding">
                                                <div class="col-md-9">
                                                    <table class="table table-hover table-inbox table-vam">
                                                        <?php
                                                        $vendorname = '';
                                                        if($vendordata['channelid']!=0){
                                                            $channellabel="";
                                                            $key = array_search($vendordata['channelid'], array_column($channellist, 'id'));
                                                            if(!empty($channellist) && isset($channellist[$key])){
                                                                $channellabel .= '<span class="label" style="background:'.$channellist[$key]['color'].'">'.substr($channellist[$key]['name'], 0, 1).'</span> ';
                                                            }
                                                            $vendorname = $channellabel." ".ucwords($vendordata['name']);
                                                        } ?>
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%">Vendor Name</td>
                                                                <td><?=$vendorname?></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="28%">Vendor Code</td>
                                                                <td><?=$vendordata['membercode']!=''?$vendordata['membercode']:'-'?></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="28%">Email</td>
                                                                <td>
                                                                    <?php 
                                                                        if(!empty($vendordata['email'])){
                                                                            if($vendordata['emailverified']==1){
                                                                                $email = $vendordata['email'].'&nbsp;&nbsp;<span class="'.verifiedbtn_class.'">'.verifiedbtn_text.'</span>';
                                                                            }else{
                                                                                $email = $vendordata['email'].'&nbsp;&nbsp;<span class="'.notverifiedbtn_class.'">'.notverifiedbtn_text.'</span>';
                                                                                $email .= '<a href="javascript:void(0)" class="'.verifybtn_class.'" title="'.verifybtn_title.'" onclick="verifyemail(\''.$vendordata['email'].'\')">'.verifybtn_text.'</a>';
                                                                            }
                                                                        }else{
                                                                            $email = $vendordata['email'];
                                                                        }
                                                                        echo $email;
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="28%">Mobile No.</td>
                                                                <td>+<?=$vendordata['countrycode']!=""?$vendordata['countrycode']." ".$vendordata['mobile']:$vendordata['mobile']?></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="28%">Country</td>
                                                                <td><?=$vendordata['countryname']!=''?$vendordata['countryname']:'-'?></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="28%">Province</td>
                                                                <td><?=$vendordata['provincename']!=''?$vendordata['provincename']:'-'?></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="28%">City</td>
                                                                <td><?=$vendordata['cityname']!=''?$vendordata['cityname']:'-'?></td>
                                                            </tr>
                                                            <?php if($channeldata['debitlimit']==1){ ?>
                                                            <tr>
                                                                <td width="28%">Debit Limit</td>
                                                                <td><?=numberFormat($vendordata['debitlimit'],2,",")?></td>
                                                            </tr>
                                                            <?php } ?>
                                                            <tr>
                                                                <td width="28%">Credit Limit</td>
                                                                <td><?=numberFormat($vendordata['creditlimit'],2,",")?></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="28%">GST No.</td>
                                                                <td><?=$vendordata['gstno']!=''?$vendordata['gstno']:'-'?></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="28%">Minimum Stock Limit</td>
                                                                <td><?=$vendordata['minimumstocklimit']!=''?$vendordata['minimumstocklimit']:'-'?></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="28%">Payment Cycle</td>
                                                                <td><?=$vendordata['paymentcycle']!=''?$vendordata['paymentcycle']:'-'?></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="28%">Rating Status</td>
                                                                <td><?=$vendordata['memberratingstatus']!=''?$vendordata['memberratingstatus']:'-'?></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="28%">EMI Reminder Days</td>
                                                                <td><?=$vendordata['emireminderdays']!=''?$vendordata['emireminderdays']:'-'?></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="28%">Purchase Regular Product</td>
                                                                <td><div class="yesno">
                                                                    <input type="checkbox" <?php if($vendordata['purchaseregularproduct']==1){ echo 'checked'; }?> disabled>
                                                                </div>
                                                            </tr>
                                                            <tr>
                                                                <td width="28%">Entry Date</td>
                                                                <td><?=date_format(date_create($vendordata['createddate']), 'd M Y h:i A')?>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-3">
                                                    <?php
                                                    echo "<img src='".str_replace("{encodeurlstring}",$QRCode,GENERATE_QRCODE_SRC)."' class='img-thumbnail'>";
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane" id="billingaddress">

                                    <div class="col-md-12 p-n">
                                        <div class="panel panel-default border-panel">
                                            <div class="panel-heading">
                                                <div class="col-md-8">
                                                    <div class="panel-ctrls panel-tbl"></div>
                                                </div>
                                                <div class="col-md-4 form-group" style="text-align: right;">
                                                    <button class="btn btn-primary btn-raised btn-label" title="ADD" data-toggle="modal" data-target="#BillingAddressModal" onclick="loadprovinceorcity()"><i class="fa fa-plus"></i> ADD <div class="ripple-container"></div>
                                                    </button>
                                                    <?php
                                                        if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                                                        ?>
                                                    <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('','Billing Address','<?php echo ADMIN_URL; ?>vendor/delete-mul-billing-address')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                                                    <?php } ?>       
                                                </div>
                                            </div>
                                            <div class="panel-body no-padding">
                                                <table class="table table-striped table-bordered" id="billingaddresstable">
                                                    <thead>
                                                        <th>Sr.No.</th>
                                                        <th>Name</th>
                                                        <th>Address</th>
                                                        <th>Email</th>
                                                        <th>Mobile No.</th>
                                                        <th width="20%">Action</th>
                                                        <th class="width5">
                                                            <div class="checkbox">
                                                                <input id="deletecheckall" onchange="allchecked()" type="checkbox" value="all">
                                                                <label for="deletecheckall"></label>
                                                            </div>
                                                        </th>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="panel-footer"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="order">
                                    <div class="col-md-12 p-n">
                                        <div class="panel panel-default border-panel mb-md" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                                            <div class="panel-heading filter-panel border-filter-heading">
                                                <h2><?=APPLY_FILTER?></h2>
                                                <div class="panel-ctrls" data-actions-container style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                                            </div>
								            <div class="panel-body panelcollapse pt-n" style="display: none;">
                                                <form action="#" id="memberform" class="form-horizontal">
                                                    <div class="row" style="margin: 0">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <label for="orderstartdate" class="control-label">Order Date</label>
                                                                    <div class="input-daterange input-group memberdaterangepicker" id="datepicker-range">
                                                                        <input type="text" class="input-small form-control" name="orderstartdate" id="orderstartdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly />
                                                                        <span class="input-group-addon">to</span>
                                                                        <input type="text" class="input-small form-control" name="orderenddate" id="orderenddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <label for="orderstatus" class="control-label">Select Status</label>
                                                                    <select id="orderstatus" name="orderstatus" class="selectpicker form-control" data-select-on-tab="true" data-size="5">
                                                                        <option value="-1">All Status</option>
                                                                        <option value="0">Pending</option>
                                                                        <option value="1">Complete</option>
                                                                        <option value="2">Cancel</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group" style="margin-top: 39px;">
                                                                <div class="col-sm-12">
                                                                    <label class="control-label"></label>
                                                                    <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilterOrder()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12 p-n">
                                        <ul class="nav nav-tabs" style="float: left;width: 100%;">
                                            <li class="active"><a data-toggle="tab" href="#purchaseorder">Purchase</a></li>
                                            <!-- <li><a data-toggle="tab" href="#salesorder">Sales</a></li> -->
                                        </ul>

                                        <div class="tab-content">
                                            <div id="purchaseorder" class="tab-pane fade in active">
                                                <div class="col-md-12 p-n">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <div class="col-md-8">
                                                                <div class="panel-ctrls panel-tbl"></div>
                                                            </div>
                                                        </div>
                                                        <div class="panel-body no-padding">
                                                            <table class="table table-striped table-bordered" id="ordertable">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Sr. No.</th>
                                                                        <th>OrderID</th>
                                                                        <th>Order Date</th>
                                                                        <th>Order Status</th>
                                                                        <th class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="panel-footer"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div id="salesorder" class="tab-pane fade">
                                                <div class="col-md-12 p-n">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <div class="col-md-8">
                                                                <div class="panel-ctrls panel-tbl"></div>
                                                            </div>
                                                        </div>
                                                        <div class="panel-body no-padding">
                                                            <table class="table table-striped table-bordered" id="salesordertable">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Sr. No.</th>
                                                                        <th>Member Name</th>
                                                                        <th>OrderID</th>
                                                                        <th>Order Date</th>
                                                                        <th>Order Status</th>
                                                                        <th class="text-right">Total Amount</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="panel-footer"></div>
                                                    </div>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>

                                <?php //if($channeldata['memberspecificproduct']==1){ ?>
                                <div class="tab-pane <?php if(isset($activetab) && $activetab=="products"){ echo "active"; }?>" id="memberproduct">
                                    
                                    <div class="col-md-12 p-n">
                                        <div class="panel panel-default border-panel">
                                            <div class="panel-heading">
                                                <div class="col-md-7">
                                                    <div class="panel-ctrls panel-tbl"></div>
                                                </div>
                                                <div class="col-md-5 form-group" style="text-align: right;">
                                                    <?php 
                                                        if(strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                                                        ?>
                                                    <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>vendor/add-vendor-product/<?=$vendorid?>" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                                                    <?php } if (in_array("import-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                                                    <a class="<?=importbtn_class;?>" href="javascript:void(0)" onclick="importproduct()" title="<?=importbtn_title?>"><?=importbtn_text;?></a>
                                                    <?php } if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                                                    <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportproduct(<?=$vendorid;?>)" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>   
                                                    <?php } ?>  
                                                </div>
                                            </div>
                                            <div class="panel-body no-padding">
                                                <table class="table table-striped table-bordered" id="producttable">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Name</th>
                                                            <th>Category</th>
                                                            <?php if($channeldata['memberspecificproduct']==1 && $vendordata['totalproductcount']>0){ ?>
                                                            <th>Assigned By</th>
                                                            <th>Product Assign As</th>
                                                            <? } ?>
                                                            <th class="text-right"><?=Member_label?> Price</th>
                                                            <?php if($channeldata['memberspecificproduct']==1 && $vendordata['totalproductcount']>0){ ?>
                                                            <th class="text-right">Sales Price</th>
                                                            <? } ?>
                                                            <!-- <th class="text-right">Stock</th> -->
                                                            <?php if($channeldata['memberspecificproduct']==1 && $vendordata['totalproductcount']>0){ ?>
                                                            <th>Actions</th>
                                                            <? } ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="panel-footer"></div>
                                            <div class="modal fade" id="myDetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                        <h3 class="modal-title">Import Product Price</h3>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form class="form-horizontal" id="productpriceimportform">
                                                            <div class="form-group" id="attachment_div">
                                                                <label class="col-sm-4 control-label">Select Excel File <span class="mandatoryfield">*</span></label>
                                                                <div class="col-sm-8">
                                                                    <div class="input-group">
                                                                    <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                        <span class="btn btn-primary btn-raised btn-file">Browse...
                                                                        <input type="file" name="attachment" id="attachment" accept=".xls,.xlsx" >
                                                                    </span>
                                                                    </span>
                                                                    <input type="text" readonly="" id="Filetext" class="form-control" value="">
                                                                </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-4 control-label">Download Format</label>
                                                                <div class="col-sm-8">
                                                                    <div class="input-group">
                                                                    <a href="<?=IMPORT_FILE?>import-product-price.xls" class="btn btn-default btn-raised" download="import-product-price.xls"><i class="fa fa-download"></i> Download File<div class="ripple-container"></div></a>
                                                                </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-sm-offset-4 col-sm-8">
                                                                    <div class="input-group">
                                                                    <input type="button" class="btn btn-primary btn-raised" onclick="checkimportproductvalidation()" value="Import">
                                                                    <button class="btn btn-primary btn-raised" data-dismiss="modal">Close<div class="ripple-container"></div></button>
                                                                </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php //} ?>
                                <?php if($channeldata['discount']==1){ ?>
                                <div class="tab-pane" id="discounttab">
                                    
                                    <div class="col-md-12 p-n">
                                        <div class="panel panel-default border-panel">
                                            <div class="panel-heading">
                                                <div class="col-md-6">
                                                    <div class="btn-group">
                                                        <p class="lead" style="margin-bottom: 0;">Discount</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel-body no-padding">
                                                <form class="form-horizontal" id="discountform">
                                                    <input type="hidden" name="vendorid" value="<?=$vendorid?>">
                                                    <div class="row">
                                                        <div class="form-group col-md-10">
                                                            <div class="form-group">
                                                                <label for="focusedinput" class="col-sm-4 control-label">Discount On
                                                                    Bill </label>
                                                                <div class="col-sm-8">
                                                                    <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                                                        <div class="radio">
                                                                            <input type="radio" name="discountonbill" id="discountonbillyes" value="1" <?php if(count($vendordiscount)>0 &&  $vendordiscount['discountonbill']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                                            <label for="discountonbillyes">On</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-2 col-xs-6">
                                                                        <div class="radio">
                                                                            <input type="radio" name="discountonbill" id="discountonbillno" value="0" <?php if(count($vendordiscount)>0 && $vendordiscount['discountonbill']==0){ echo 'checked'; }?>>
                                                                            <label for="discountonbillno">Off</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="discountonbilldiv" class="discountonbilldiv" <?php if(count($vendordiscount)>0 && $vendordiscount['discountonbill']==0){ echo 'style="display:none;"'; }?>>
                                                                <div class="form-group">
                                                                    <label class="col-sm-4 control-label">Discount Type</label>
                                                                    <div class="col-sm-8">
                                                                        <div class="col-sm-4 col-xs-6" style="padding-left: 0px;">
                                                                            <div class="radio">
                                                                                <input type="radio" name="discountonbilltype" id="percentage" value="1" checked <?php if(count($vendordiscount)>0 && $vendordiscount['discountonbilltype']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                                                <label for="percentage">Percentage</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-4 col-xs-6">
                                                                            <div class="radio">
                                                                                <input type="radio" name="discountonbilltype" id="amounttype" value="0" <?php if(count($vendordiscount)>0 && $vendordiscount['discountonbilltype']==0){ echo 'checked'; }?>>
                                                                                <label for="amounttype">Amount</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" id="percentageval_div">
                                                                    <label class="col-sm-4 control-label" for="percentageval">Percentage (%) <span class="mandatoryfield">*</span></label>
                                                                    <div class="col-sm-8">
                                                                        <input id="percentageval" type="text" name="percentageval" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="5" value="<?php if(count($vendordiscount)>0 && $vendordiscount['discountonbilltype']==1){ echo $vendordiscount['discountonbillvalue']; } ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" id="amount_div" style="display: none;">
                                                                    <label class="col-sm-4 control-label" for="amount">Amount <span class="mandatoryfield">*</span></label>
                                                                    <div class="col-sm-8">
                                                                        <input id="amount" type="text" name="amount" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="10" value="<?php if(count($vendordiscount)>0 && $vendordiscount['discountonbilltype']==0){ echo $vendordiscount['discountonbillvalue']; } ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <button type="button" id="cleardatebtn" class="btn btn-primary btn-xs btn-raised pull-right">Clear
                                                                        Date</button>
                                                                </div>
                                                                <div class="input-daterange discountdaterangepicker" id="datepicker-range">
                                                                    <div class="form-group row" id="startdate_div">
                                                                        <label class="col-sm-4 control-label" for="startdate">Date
                                                                        </label>
                                                                        <div class="col-sm-4">
                                                                            <input id="startdate" type="text" name="startdate" value="<?php if(count($vendordiscount)>0 && $vendordiscount['discountonbillstartdate']!="0000-00-00"){ echo $this->general_model->displaydate($vendordiscount['discountonbillstartdate']); } ?>" class="form-control datepicker1" placeholder="Start" readonly>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <input id="enddate" type="text" name="enddate" value="<?php if(count($vendordiscount)>0){if($vendordiscount['discountonbillenddate']!="0000-00-00"){ echo $this->general_model->displaydate($vendordiscount['discountonbillenddate']); }} ?>" class="form-control datepicker1" placeholder="End" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" id="discountonbillminvalue_div">
                                                                    <label class="col-sm-4 control-label" for="discountonbillminamount">Minimum Bill
                                                                        Amount <span
                                                                            class="mandatoryfield">*</span></label>
                                                                    <div class="col-sm-8">
                                                                        <input id="discountonbillminamount" type="text" name="discountonbillminamount" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="10" value="<?php if(count($vendordiscount)>0 && $vendordiscount['discountonbillminamount']!=0){ echo $vendordiscount['discountonbillminamount']; } ?>">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group is-empty">
                                                                <label class="col-sm-4 control-label"></label>
                                                                <div class="col-sm-8">
                                                                    <input type="button" id="submit" onclick="checkdiscountonbillvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if($channeldata['quotation']==1){ ?>
                                <div class="tab-pane" id="quotation">

                                    <div class="col-md-12 p-n">
                                        <div class="panel panel-default border-panel mb-md" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                                            <div class="panel-heading filter-panel border-filter-heading">
                                                <h2><?=APPLY_FILTER?></h2>
                                                <div class="panel-ctrls" data-actions-container style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                                            </div>
								            <div class="panel-body panelcollapse pt-n" style="display: none;">
                                                <form action="#" id="quotationform" class="form-horizontal">
                                                    <div class="row" style="margin: 0">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <label for="quotationstartdate" class="control-label">Quotation Date</label>
                                                                    <div class="input-daterange input-group memberdaterangepicker" id="datepicker-range">
                                                                        <input type="text" class="input-small form-control" name="quotationstartdate" id="quotationstartdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly />
                                                                        <span class="input-group-addon">to</span>
                                                                        <input type="text" class="input-small form-control" name="quotationenddate" id="quotationenddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <label for="quotationstatus" class="control-label">Select Status</label>
                                                                    <select id="quotationstatus" name="quotationstatus" class="selectpicker form-control" data-select-on-tab="true" data-size="5">
                                                                        <option value="-1">All Status</option>
                                                                        <option value="0">Pending</option>
                                                                        <option value="1">Approved</option>
                                                                        <option value="2">Rejected</option>
                                                                        <option value="3">Cancel</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group" style="margin-top: 39px;">
                                                                <div class="col-sm-12">
                                                                    <label class="control-label"></label>
                                                                    <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilterQuotation()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 p-n">
                                        <ul class="nav nav-tabs" style="float: left;width: 100%;">
                                            <li class="active"><a data-toggle="tab" href="#purchasequotation">Purchase</a></li>
                                            <!-- <li><a data-toggle="tab" href="#salesquotation">Sales</a></li> -->
                                        </ul>

                                        <div class="tab-content">
                                            <div id="purchasequotation" class="tab-pane fade in active">
                                                <div id="quotationtablediv">
                                                    <div class="col-md-12 p-n">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <div class="col-md-8">
                                                                    <div class="panel-ctrls panel-tbl"></div>
                                                                </div>
                                                            </div>
                                                            <div class="panel-body no-padding">
                                                                <table id="quotationtable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="width8">Sr. No.</th>
                                                                            <th>QuotationID</th>
                                                                            <th>Quotation Date</th>
                                                                            <th class="text-center">Quotation Status</th>
                                                                            <th class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="panel-footer"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div id="salesquotation" class="tab-pane fade">
                                                <div id="salesquotationtablediv">
                                                    <div class="col-md-12 p-n">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <div class="col-md-8">
                                                                    <div class="panel-ctrls panel-tbl"></div>
                                                                </div>
                                                            </div>
                                                            <div class="panel-body no-padding">
                                                                <table id="salesquotationtable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="width8">Sr.No.</th>
                                                                            <th>Member Name</th>
                                                                            <th>QuotationID</th>
                                                                            <th>Quotation Date</th>
                                                                            <th class="text-center">Quotation Status</th>
                                                                            <th class="text-right">Total Amount</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="panel-footer"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>

                                </div>
                                <?php } ?>
                                <?php if($channeldata['identityproof']==1){ ?>
                                <div class="tab-pane" id="identityproof">
                                    <div class="col-md-12 p-n">
                                        <div class="panel panel-default border-panel">
                                            <div class="panel-heading">
                                                <div class="col-md-8">
                                                    <div class="panel-ctrls panel-tbl"></div>
                                                </div>
                                                <div class="col-md-4 form-group" style="text-align: right;">
                                                    <?php 
                                                    if(strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                                                        ?>
                                                    <a class="<?=addbtn_class;?>" href="javascript:void(0)" title=<?=addbtn_title?> data-toggle="modal" data-target="#identityproofmodal" onclick="openIDProofmodal()"><?=addbtn_text;?></a>
                                                    <?php } ?>
                                                    <?php
                                                    if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                                                    ?>
                                                    <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('','<?=Member_label?> Document','<?php echo ADMIN_URL; ?>member/delete-mul-identity-proof')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="panel-body no-padding">
                                                <table id="identityprooftable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th class="width8">Sr.No.</th>
                                                            <th>Document Title</th>
                                                            <th>Last Updated</th>
                                                            <th class="width15">Status</th>
                                                            <th width="20%">Action</th>
                                                            <th class="width5">
                                                                <div class="checkbox">
                                                                    <input id="deletecheckall" onchange="allchecked()" type="checkbox" value="all">
                                                                    <label for="deletecheckall"></label>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(!empty($identityproofData)) {  ?>
                                                        <?php $srno=0; foreach ($identityproofData as $row) { ?>
                                                        <tr>
                                                            <td><?=++$srno?></td>
                                                            <td><?=$row['title']?></td>
                                                            <td><?=date('d M Y h:i A',strtotime($row['modifieddate']))?></td>
                                                            <td>
                                                                <div class="dropdown" style="float: left;">
                                                                    <?php 
                                                                    if($row['status']==1){ ?>
                                                                    <button class="btn btn-success <?=STATUS_DROPDOWN_BTN?> btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Approved<span class="caret"></span></button>
                                                                    <ul class="dropdown-menu" role="menu">

                                                                        <li id="dropdown-menu">
                                                                            <a onclick="chageapprovestatusonmemberidproof(0,<?=$row['id']?>)">Not
                                                                                Approve</a>
                                                                        </li>
                                                                    </ul>
                                                                    <?php  }else{ ?>
                                                                    <button class="btn btn-danger <?=STATUS_DROPDOWN_BTN?> btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Not Aprroved<span class="caret"></span></button>
                                                                    <ul class="dropdown-menu" role="menu">
                                                                        <li id="dropdown-menu">
                                                                            <a onclick="chageapprovestatusonmemberidproof(1,<?=$row['id']?>)">Approve</a>
                                                                        </li>
                                                                    </ul>
                                                                    <?php }?>
                                                                </div>
                                                            </td>
                                                           
                                                            <td width="20%">
                                                                <?php 
                                                                if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                                                                ?>
                                                                <a class="<?=edit_class;?>" href="javascript:void(0)" title=<?=edit_title?> data-toggle="modal" data-target="#identityproofmodal" onclick="getIdentityProofDataById(<?=$row['id']?>)"><?=edit_text;?></a>
                                                                <?php
                                                                    }
                                                                if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                                                                    ?>
                                                                <a class="<?=delete_class;?>" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow(<?=$row['id']?>,'','<?=Member_label?> Document','<?=ADMIN_URL?>member/delete-mul-identity-proof')"><?=delete_text;?></a>
                                                                <?php
                                                                        }
                                                                    ?>
                                                                <?php if($row['idproof'] != ''){ ?>
                                                                <a class="<?=download_class;?>" href="<?php echo IDPROOF.$row['idproof']; ?>" title=<?=download_title?> download><?=download_text;?></a>
                                                                <?php } else{ ?>
                                                                <a class="<?=download_class;?>" href="javascript:void(0);" title=<?=download_title?>><?=download_text;?></a>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <div class="checkbox">
                                                                    <input id="deletecheck<?php echo $row['id']; ?>" onchange="singlecheck(this.id)" type="checkbox" value="<?php echo $row['id']; ?>" name="deletecheck<?php echo $row['id']; ?>" class="checkradios">
                                                                    <label for="deletecheck<?php echo $row['id']; ?>"></label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <? } }?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="panel-footer"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->

<?php if($channeldata['identityproof']==1){ ?>
<div class="modal fade" id="identityproofmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 610px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documenttitle">Add Vendor Document</h4>
            </div>
            <div class="modal-body" style="padding-top: 4px;">
                <form action="#" id="idproofform" class="form-horizontal">
                    <input type="hidden" name="vendorid" value="<?=$vendorid?>">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" id="titledocument_div">
                                <label for="titledocument" class="col-md-3 control-label">Title <span class="mandatoryfield">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" id="titledocument" class="form-control" name="titledocument" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" id="identityproof_div">
                                <input type="hidden" name="memberidproofid" id="memberidproofid" value="">
                                <input type="hidden" name="oldIDproof" id="oldIDproof" value="">
                                <label class="col-md-3 control-label">Select Document <span class="mandatoryfield">*</span></label>
                                <div class="col-md-8">
                                    <div class="input-group" id="fileupload1">
                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                            <span class="btn btn-primary btn-raised btn-sm btn-file">Browse...
                                                <input type="file" name="identityproof" id="identityproof" class="identityproof" onchange="validfile($(this))">
                                            </span>
                                        </span>
                                        <input type="text" id="textfile" class="form-control" name="textfile" value="" readonly>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-12">
                            <hr>
                            <div class="form-group text-center" id="btntext">
                                <input type="button" id="submit" onclick="identityproofcheckvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <a class="<?=cancellink_class;?>" href="javascript:voi(0)" title=<?=cancellink_title?>  data-dismiss="modal" aria-label="Close"><?=cancellink_text?></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<?php } ?>

<?php if($channeldata['debitlimit']==1){ ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Debit Limit</h4>
            </div>
            <div class="modal-body" style="padding-top: 4px;">
                <form action="#" id="debitlimitform" class="form-horizontal">
                    <input type="hidden" name="vendorid" value="<?=$vendorid?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="debitlimit_div">
                                <div class="col-md-12">
                                    <label class="control-label" for="debitlimit">Debit Limit <span class="mandatoryfield">*</span></label>
                                    <input id="debitlimit" type="text" onkeypress="return decimal(event,this.value)" name="debitlimit" class="form-control datepicker1" value="<?=$vendordata['debitlimit']?>" placeholder="Debit Limit">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="EDIT" class="btn btn-primary btn-raised">&nbsp;
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<?php } ?>

<div class="modal fade" id="memberpricemodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Price</h4>
            </div>
            <div class="modal-body">
                <div id="load_variants">
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>


<div class="modal fade" id="BillingAddressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Address</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="billingaddressform">

                    <input type="hidden" name="vendorid" value="<?=$vendorid?>">
                    <input type="hidden" id="channelid" name="channelid" value="<?=$channelid?>">
                    <input type="hidden" name="billingaddressid" id="billingaddressid" value="">

                    <div class="col-md-6">
                        <div class="form-group" id="baname_div">
                            <label class="col-sm-4 control-label" for="baname">Name <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <input id="baname" type="text" name="baname" class="form-control" onkeypress="return onlyAlphabets(event)" value="">
                            </div>
                        </div>
                        <div class="form-group" id="baemail_div">
                            <label class="col-sm-4 control-label" for="baemail">Email
                                <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <input id="baemail" type="text" name="baemail" class="form-control" value="">
                            </div>
                        </div>
                        <div class="form-group" id="baddress_div">
                            <label class="col-sm-4 control-label" for="baddress">Address <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <textarea id="baddress" name="baddress" value="" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group" id="batown_div">
                            <label class="col-sm-4 control-label" for="batown">Town</label>
                            <div class="col-sm-8">
                                <input id="batown" type="text" name="batown" class="form-control" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bayes" class="col-sm-4 control-label">Activate</label>
                            <div class="col-sm-8">
                                <div class="col-sm-4 col-xs-4" style="padding-left: 0px;">
                                    <div class="radio">
                                        <input type="radio" name="statusba" id="bayes" value="1" checked>
                                        <label for="bayes">Yes</label>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xs-4">
                                    <div class="radio">
                                        <input type="radio" name="statusba" id="bano" value="0">
                                        <label for="bano">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" id="bapostalcode_div">
                            <label class="col-sm-4 control-label" for="bapostalcode">Postal Code
                                <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <input id="bapostalcode" type="text" name="bapostalcode" class="form-control" onkeypress="return isNumber(event)" value="">
                            </div>
                        </div>
                        <div class="form-group" id="bamobileno_div">
                            <label class="col-sm-4 control-label" for="bamobileno">Mobile No.
                                <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <input id="bamobileno" type="text" name="bamobileno" class="form-control" onkeypress="return isNumber(event)" maxlength="10" value="">
                            </div>
                        </div>
                        <div class="form-group" id="country_div">
                            <label class="col-sm-4 control-label" for="countryid">Country</label>
                            <div class="col-sm-8">
                                <select id="countryid" name="countryid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                    <option value="0">Select Country</option>
                                    <?php foreach($countrydata as $country){ ?>
                                    <option value="<?php echo $country['id']; ?>" <?php if(DEFAULT_COUNTRY_ID == $country['id']){ echo "selected"; } ?>><?php echo $country['name']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="province_div">
                            <label class="col-sm-4 control-label" for="provinceid">Province</label>
                            <div class="col-sm-8">
                                <select id="provinceid" name="provinceid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                    <option value="0">Select Province</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="city_div">
                            <label class="col-sm-4 control-label" for="cityid">City</label>
                            <div class="col-sm-8">
                                <select id="cityid" name="cityid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                    <option value="0">Select City</option>
                                </select>
                            </div>
                        </div>


                    </div>
                    <div class="col-md-12">

                        <div class="form-group" style="text-align: center;">
                            <input type="button" id="billingaddressbtn" onclick="billingaddresscheckvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                            <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="billingaddressresetdata()">
                            <a class="<?=cancellink_class;?>" href="javascript:void(0)" title=<?=cancellink_title?> data-dismiss="modal" aria-label="Close"><?=cancellink_text?></a>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
$('.list-group-item').on('click', function() {
    $('.list-group-item').removeClass('active');
    $(this).addClass('active');
});
$(".rating").raty({
    hints: false,
    halfShow: true,
    readOnly: true,
    score: function() {
        return $(this).attr("data-score");
    }
});

function displayproductreview(id) {
    var message = $('#message' + id).html();
    $('.modal-body').html(message.replace(/&nbsp;/g, ' '));

}
</script>