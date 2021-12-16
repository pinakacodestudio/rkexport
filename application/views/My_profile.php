<script>
    var DEFAULT_COUNTRY_ID = '<?=DEFAULT_COUNTRY_ID?>';
</script>
<!-- slider start here -->
<div class="process-bg" style="background-color:<?=DEFAULT_COVER_IMAGE_COLOR?>;">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <h1><?=$title?></h1>
                <ul class="breadcrumbs list-inline">
                    <li><a href="<?=FRONT_URL?>">Home</a></li>
                    <li>My Profile</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- slider end here -->

<!-- myprofile start here -->
<div class="myprofile" style="background-color:#f1f1f1;">
    <div class="container">
        <div class="row profile">
            <div class="col-md-3 col-sm-12 col-xs-12 pl-xs pr-xs mb-md">
                <div class="profile-sidebar"> <!-- SIDEBAR USER TITLE --> 
                    <div class="profile-usertitle"> 
                        <div class="text-center">

                            <?php if(!is_null($this->session->userdata(base_url().'MEMBER_PROFILE_IMAGE')) && $this->session->userdata(base_url().'MEMBER_PROFILE_IMAGE')!=""){
                                $profileimage = PROFILE.$this->session->userdata(base_url().'MEMBER_PROFILE_IMAGE');
                            }else{
                                $profileimage = PROFILE."noimage.png";
                            }?>
                            <img class="img-thumbnail sidebarprofileimage" src="<?=$profileimage?>">
                            <h4 class="profile-usertitle-name"><?php echo $this->session->userdata(base_url().'MEMBER_NAME');?></h4>
                            <p><i class="fa fa-envelope-o"></i> <?php echo $this->session->userdata(base_url().'MEMBER_EMAIL');?></p> 
                        </div>
                    </div> 
                    <a href="<?=FRONT_URL?>my-profile" id="abc"></a> <!-- END SIDEBAR USER TITLE --> 
                    <!-- SIDEBAR MENU --> 
                    <div class="profile-usermenu"> 
                        <ul class="nav"> 
                            <li class="active"> 
                                <a data-toggle="tab" href="#tab-dashboard" class="item"> <i class="fa fa-dashboard"></i>Dashboard </a> 
                            </li> 
                            <li> 
                                <a data-toggle="tab" href="#tab-order" class="item active"> <i class="fa fa-shopping-cart"></i>Orders </a> 
                            </li> 
                            <li> 
                                <a data-toggle="tab" href="#tab-rewardpointhistory" class="item"> <i class="fa fa-trophy"></i>Reward Point History </a> 
                            </li> 
                            <li> 
                                <a data-toggle="tab" href="#tab-address" class="item"> <i class="fa fa-map-marker"></i>Address </a> 
                            </li> 
                            <li> 
                                <a data-toggle="tab" href="#tab-editprofile" class="item"> <i class="fa fa-user"></i>Profile </a> 
                            </li> 
                            <li> 
                                <a data-toggle="tab" href="#tab-changepassword" class="item"> <i class="fa fa-key"></i>Change Password </a> 
                            </li> 
                            <li> 
                                <a href="<?=FRONT_URL?>logout"> <i class="fa fa-sign-out"></i>Logout </a> 
                            </li> 
                        </ul> 
                    </div> 
                    <!-- END MENU --> 
                </div>
            </div>
            <div class="col-md-9 col-sm-12 col-xs-12 pl-xs pr-xs">
                <div class="profile-content">
                    <div class="tab-content">
                        <div id="tab-dashboard" class="tab-pane fade in active">
                            <p class="checkout-title"><strong>Dashboard</strong></p>
                            <hr>
                            <h4>Recent Order</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th class="text-center">Payment Method</th>
                                        <th class="text-center">Order Status</th>
                                        <th class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th>
                                        <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($recentorder)){ 
                                        foreach ($recentorder as $row) { ?>
                                            <tr>
                                                <td><?=$row['orderid']?></td>
                                                <td><?=$this->general_model->displaydatetime($row['createddate'])?></td>
                                                <td class="text-center">
                                                    <?php
                                                        if($row['paymenttype']==0){
                                                            echo "COD";
                                                        }else{
                                                            echo ucwords($this->Paymentgatewaytype[$row['paymenttype']]);
                                                        }
                                                    ?>
                                                </td>
                                                <td class="text-center"><?php
                                                if($row['orderstatus']==0){
                                                    echo "<span class='label label-warning'>Pending</span>";
                                                }else if($row['orderstatus']==1){
                                                    echo "<span class='label label-success'>Approved</span>";
                                                }else if($row['orderstatus']==2){
                                                    echo "<span class='label label-danger'>Cancelled</span>";
                                                }
                                                ?></td>
                                                <td class="text-right">
                                                    <?=numberFormat(round($row['amount']),2,'.','')?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="javascript:void(0)" class="btn" onclick="printorderinvoice(<?=$row['id']?>)" title="Print Order Invoice"><i class="fa fa-print"></i></a>
                                                </td>
                                            </tr>  
                                        <? } }else{ ?>
                                        <tr>
                                            <td colspan="6" style="text-align: center;">No data available in table</td>
                                        </tr>  
                                        <? } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="tab-order" class="tab-pane fade">
                            <p class="checkout-title"><strong>Orders</strong></p>
                            <hr>
                            <div class="table-responsive">
                                <table id="carttable" class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th class="text-center">Payment Method</th>
                                        <th class="text-center">Order Status</th>
                                        <th class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if(!empty($orderdata)){ 
                                            $srno = 1;
                                            foreach ($orderdata as $row) { ?>
                                            <tr>
                                                <td><?=$srno++?></td>
                                                <td><?=$row['orderid']?></td>
                                                <td><?=$this->general_model->displaydatetime($row['createddate'])?></td>
                                                <td class="text-center">
                                                    <?php
                                                        if($row['paymenttype']==0){
                                                            echo "COD";
                                                        }else{
                                                            echo ucwords($this->Paymentgatewaytype[$row['paymenttype']]);
                                                        }
                                                    ?>
                                                </td>
                                                <td class="text-center"><?php
                                                    if($row['orderstatus']==0){
                                                        echo "<span class='label label-warning'>Pending</span>";
                                                    }else if($row['orderstatus']==1){
                                                        echo "<span class='label label-success'>Approved</span>";
                                                    }else if($row['orderstatus']==2){
                                                        echo "<span class='label label-danger'>Cancelled</span>";
                                                    }
                                                ?></td>
                                                <td class="text-right">
                                                    <?=numberFormat(round($row['amount']),2,'.','')?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="javascript:void(0)" class="btn" onclick="printorderinvoice(<?=$row['id']?>)" title="Print Order Invoice"><i class="fa fa-print"></i></a>
                                                </td>
                                            </tr>  
                                    <? } } ?>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="tab-rewardpointhistory" class="tab-pane fade">
                            <p class="checkout-title"><strong>Reward Point History</strong></p>
                            <hr>
                            <div class="table-responsive">
                                <table id="rewardpointhistorytable" class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Order ID</th>
                                        <th>Details</th>
                                        <th class="text-center">Points</th>
                                        <th>Type</th>
                                        <th class="text-right">Closing Points</th>
                                        <th>Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if(!empty($rewardpointhistorydata)){ 
                                            $srno = 1;
                                            foreach ($rewardpointhistorydata as $row) { ?>
                                            <tr>
                                                <td><?=$srno++?></td>
                                                <td><?=$row['orderid']?></td>
                                                <td><?=ucwords($row['detail'])?></td>
                                                <td class="text-center">
                                                <?php
                                                    if($row['type']==1){
                                                        echo '<span class="label label-danger">-'.$row['point'].'</span>';
                                                    }else{
                                                        echo '<span class="label label-success">+'.$row['point'].'</span>';
                                                    }
                                                ?>
                                                </td>
                                                <td><?php
                                                if($row['type']==1){
                                                    echo "Debit";
                                                }else{
                                                    echo "Credit";
                                                }
                                                ?></td>
                                            <td class="text-right"><?=$row['closingpoint']?></td>
                                            <td><?=$this->general_model->displaydatetime($row['createddate'])?></td>
                                            </tr>  
                                    <? } } ?>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="tab-address" class="tab-pane fade">
                            <p class="checkout-title"><strong>Address</strong></p><hr>
                            <div class="row m-n">
                                <div class="form-horizontal">  
                                    <div class="col-md-12 p-n addresslist" id="billinglisting">
                                        <?php if(!empty($memberaddress)) { ?>
                                            <?php for ($i=0; $i < count($memberaddress); $i++) { ?>
                                                <div class="col-md-6 mb pl-xs pr-xs address<?=$memberaddress[$i]['id']?>">
                                                    <div class="radio billingaddress">
                                                        <input id="billingaddress<?=$memberaddress[$i]['id']?>" type="radio" name="billingaddress" value="<?=$memberaddress[$i]['id']?>" <?php if($i==0) { echo 'checked';} ?>>
                                                        
                                                        <label for="billingaddress<?=$memberaddress[$i]['id']?>" class="m-n" style="width: 100%;">
                                                            <address class="col-md-12 pl-xl pt-sm m-n" style="position: relative;">
                                                                <?php if($memberaddress[$i]['membername']!=''){?>
                                                                <span id="billingname<?=$memberaddress[$i]['id']?>"><b><?=ucwords($memberaddress[$i]['membername'])?></b></span>&nbsp;
                                                                <? } ?><br>
                                                                <?php if($memberaddress[$i]['shortaddress']!=''){?>
                                                                <span id="billingaddr<?=$memberaddress[$i]['id']?>"><?=$memberaddress[$i]['shortaddress']?></span><br>
                                                                <? } ?>
                                                                <input type="hidden" id="billingcityid<?=$memberaddress[$i]['id']?>" value="<?=$memberaddress[$i]['ctid'];?>">
                                                                <input type="hidden" id="billingpostcode<?=$memberaddress[$i]['id']?>" value="<?=$memberaddress[$i]['postalcode'];?>">
                                                                <input type="hidden" id="billingprovinceid<?=$memberaddress[$i]['id']?>" value="<?=$memberaddress[$i]['sid'];?>">
                                                                <input type="hidden" id="billingcountryid<?=$memberaddress[$i]['id']?>" value="<?=$memberaddress[$i]['cid'];?>">
                                                                <?php 
                                                                $billinglocation = '';
                                                                if($memberaddress[$i]['cityname']!=''){
                                                                    $billinglocation .= ucwords($memberaddress[$i]['cityname']);
                                                                    if($memberaddress[$i]['postalcode']!=''){
                                                                        $billinglocation .= " - ".ucwords($memberaddress[$i]['postalcode']);
                                                                    }
                                                                    $billinglocation .= ' '.ucwords($memberaddress[$i]['statename']).", ".ucwords($memberaddress[$i]['countryname']).".<br>";
                                                                   
                                                                    echo '<span id="billinglocation'.$memberaddress[$i]['id'].'">'.$billinglocation.'</span>';
                                                                }

                                                                ?>
                                                                <?php if($memberaddress[$i]['mobileno']!=''){?>
                                                                <b><i class="fa fa-phone-square"></i> </b><span id="billingmobileno<?=$memberaddress[$i]['id']?>"><?=$memberaddress[$i]['mobileno']?></span><br>
                                                                <? } ?>    
                                                                <b><i class="fa fa-envelope-o"></i> </b><span id="billingemail<?=$memberaddress[$i]['id']?>"><?=$memberaddress[$i]['email']?></span>
                                                            </address>
                                                            <span class="editaddresslbl"><a href="javascript:void(0)" class="btn action-btn" onclick="editaddress(<?=$memberaddress[$i]['id']?>)"><i class="fa fa-edit"></i> Edit</a></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php }
                                        } ?>
                                    </div>
                                </div>
                                <div class="defaultbtn col-xs-offset-3  col-md-offset-0">
                                    <a href="javascript:void(0)" class="btn btn-primary newbillingaddress" data-toggle="modal" data-target="#AddressModal" onclick="openaddressmodal()"><i class="fa fa-plus"></i> Add New Address</a>
                                </div>
                            </div>
                        </div>
                        <div id="tab-editprofile" class="tab-pane fade">
                            <p class="checkout-title"><strong>Edit Profile</strong></p><hr>
                            <div class="row">
                                <div class="col-md-12 p-n">
                                    <div id="editprofileerror" class="col-md-12"></div>
                                </div>
                                <div class="col-md-12 p-n">
                                    <form class="form-horizontal" name="profileform" id="profileform">
                                        <div class="form-group m-n mb-md">
                                            <label class="col-md-4 col-sm-4 col-xs-12 control-label" for="username">Name <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="col-md-12 col-xs-12 p-n">
                                                    <input type="text" name="username" id="username" class="input100" value="<?php if(!empty($userdata)){ echo $userdata['name']; } ?>">
                                                    <span class="focus-input100"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group m-n mb-md">
                                            <label class="col-md-4 col-sm-4 col-xs-12 control-label" for="useremail">Email <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="col-md-12 col-xs-12 p-n">
                                                    <input type="text" name="useremail" id="useremail" class="input100" value="<?php if(!empty($userdata)){ echo $userdata['email']; } ?>">
                                                    <span class="focus-input100"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group m-n mb-md">
                                            <label class="col-md-4 col-sm-4 col-xs-12 control-label" for="countrycode">Mobile No. <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-2 col-sm-2 col-xs-4 pr-n">
                                                <div class="col-md-12 col-xs-12 p-n" id="countrycode_div">
                                                    <select id="countrycode" name="countrycode" class="selectpicker input100" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                        <option value="0">Country Code</option>
                                                        <?php foreach($countrycodedata as $row){ ?>
                                                            <option value="<?php echo $row['id']; ?>" <?php if(!empty($userdata)){ if($userdata['countrycode'] == $row['id']){ echo 'selected'; } } ?>><?php echo $row['phonecodewithname']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="focus-input100"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-8">
                                                <div class="col-md-12 col-xs-12 p-n">
                                                    <input type="text" name="mobileno" id="mobileno" class="input100" value="<?php if(!empty($userdata)){ echo $userdata['mobile']; } ?>" onkeypress="return isNumber(event)" maxlength="10">
                                                    <span class="focus-input100"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group m-n mb-md">
                                            <label class="col-md-4 col-sm-4 col-xs-12 control-label" for="usergstno">GST No.</label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="col-md-12 col-xs-12 p-n">
                                                    <input type="text" name="usergstno" id="usergstno" class="input100" value="<?php if(!empty($userdata)){ echo $userdata['gstno']; } ?>" maxlength="15">
                                                    <span class="focus-input100"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group m-n mb-md">
                                            <input type="hidden" name="oldprofileimage" id="oldprofileimage" value="<?php if(!empty($userdata)){ echo $userdata['image']; }?>">
                                            <input type="hidden" name="removeoldImage" id="removeoldImage" value="0">
                                            <label class="col-md-4 col-sm-4 col-xs-12 control-label" for="image">Profile Image</label>
                                            <div class="col-md-8 col-sm-8 col-xs-12">
                                                <?php if(!empty($userdata) && $userdata['image']!=''){ ?>
                                                    <div class="imageupload" id="profileimage">
                                                        <div class="file-tab defaultbtn"><img src="<?php echo PROFILE.$userdata['image']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
                                                            <label id="profileimagelabel" class="btn btn-sm btn-primary btn-raised btn-file">
                                                                <span id="profileimagebtn">Change</span>
                                                                <!-- The file is stored here. -->
                                                                <input type="file" name="image" id="image">
                                                            </label>
                                                            <button type="button" class="bt btn btn-sm btn-danger btn-raised" id="remove" style="display: inline-block;">Remove</button>
                                                        </div>
                                                    </div>
                                                <?php }else{ ?>
                                                    <!-- <script type="text/javascript"> var ACTION = 0;</script> -->
                                                    <div class="imageupload">
                                                        <div class="file-tab defaultbtn">
                                                            <img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
                                                            <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
                                                                <span id="profileimagebtn">Select Image</span>
                                                                <input type="file" name="image" id="image">
                                                            </label>
                                                            <button type="button" class="bt btn btn-sm btn-danger btn-raised" id="remove">Remove</button>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>                  
                                        <div class="form-group m-n">
                                            <div class="col-md-8 col-sm-8 col-md-offset-4 col-sm-offset-4 col-xs-10 col-xs-offset-2 defaultbtn">
                                                <a href="javascript:void(0)" class="btn btn-primary" onclick="updateprofile()">Save Changes</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div id="tab-changepassword" class="tab-pane fade">
                            <p class="checkout-title"><strong>Change Password</strong></p><hr>
                            <div class="row">
                                <div class="col-md-12 p-n">
                                    <div id="changepassworderror" class="col-md-12"></div>
                                </div>
                                <div class="col-md-12 form-horizontal">
                                    <div class="form-group">
                                        <label class="col-md-4 col-xs-12 control-label">Current Password <span class="mandatoryfield">*</span></label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="col-md-12 col-xs-12 p-n">
                                                <input type="password" name="currentpassword" id="currentpassword" class="input100">
                                                <span class="focus-input100"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 col-xs-12 control-label">New Password <span class="mandatoryfield">*</span></label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="col-md-12 col-xs-12 p-n">
                                                <input type="password" name="newpassword" id="newpassword" class="input100">
                                                <span class="focus-input100"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 col-xs-12 control-label">Confirm Password <span class="mandatoryfield">*</span></label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="col-md-12 col-xs-12 p-n">
                                                <input type="password" name="confirmpassword" id="confirmpassword" class="input100">
                                                <span class="focus-input100"></span>
                                            </div>
                                        </div>
                                    </div>                  
                                    <div class="form-group">
                                        <div class="col-md-8 col-md-offset-4 col-xs-6 col-xs-offset-3 defaultbtn">
                                            <a href="javascript:void(0)" class="btn btn-primary" onclick="changepassword()">Save Changes</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
<!-- myprofile end here -->
<div class="modal fade" id="AddressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true"><i class="fa fa-times"></i></span></button>
                <h4 class="modal-title">Add Address</h4>
            </div>
            <div class="modal-body" style="float:left;width:100%;overflow-y: auto;max-height: 450px;">
                <form class="form-horizontal" id="memberaddressform">
                    <div id="memberaddresserror"></div>
                    <input type="hidden" name="memberid" value="<?=$this->session->userdata(base_url().'MEMBER_ID')?>">
                    <input type="hidden" name="addressid" id="addressid" value="">

                    <div class="col-md-6">
                        <div class="form-group" id="membername_div">
                            <label class="col-sm-4 control-label" for="membername">Name <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <input id="membername" type="text" name="membername" class="input100" onkeypress="return onlyAlphabets(event)" value="">
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="memberemail_div">
                            <label class="col-sm-4 control-label" for="memberemail">Email
                                <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <input id="memberemail" type="text" name="memberemail" class="input100" value="">
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="memberaddress_div">
                            <label class="col-sm-4 control-label" for="memberaddress">Address <span
                                    class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <textarea id="memberaddress" name="memberaddress" value="" class="input100"></textarea>
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="membertown_div">
                            <label class="col-sm-4 control-label" for="membertown">Town</label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <input id="membertown" type="text" name="membertown" class="input100" value="">
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" id="memberpostalcode_div">
                            <label class="col-sm-4 control-label" for="memberpostalcode">Postal Code
                                <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <input id="memberpostalcode" type="text" name="memberpostalcode" class="input100"
                                    onkeypress="return isNumber(event)" value="">
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="membermobileno_div">
                            <label class="col-sm-4 control-label" for="membermobileno">Mobile No.
                                <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <input id="membermobileno" type="text" name="membermobileno" class="input100"
                                    onkeypress="return isNumber(event)" maxlength="10" value="">
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="membercountry_div">
                            <label class="col-sm-4 control-label" for="countryid">Country</label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <select id="countryid" name="countryid" class="selectpicker input100" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select Country</option>
                                        <?php foreach($countrydata as $country){ ?>
                                        <option value="<?php echo $country['id']; ?>" <?php if(DEFAULT_COUNTRY_ID == $country['id']){ echo "selected"; } ?>><?php echo $country['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="focus-input100"></span>
                                </div>
                           </div>
                        </div>

                        <div class="form-group" id="memberprovince_div">
                            <label class="col-sm-4 control-label" for="provinceid">Province</label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <select id="provinceid" name="provinceid" class="selectpicker input100" data-live-search="true" data-select-on-tab="true" data-size="5">
                                            <option value="0">Select Province</option>
                                    </select>
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="membercity_div">
                            <label class="col-sm-4 control-label" for="cityid">City</label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <select id="cityid" name="cityid" class="selectpicker input100" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select City</option>
                                    </select>
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 checkout_sub">
                        <hr>
                        <div class="form-group mb-n defaultbtn" style="text-align: center;">
                            <input type="button" id="addaddressbtn" onclick="checkmemberaddressvalidation()"
                                name="submit" value="ADD" class="btn btn-primary">
                            <input type="button" value="RESET" class="btn btn-primary btn-raised"
                                onclick="resetaddressdata()">
                            <a class="bt btn btn-danger" href="javascript:voi(0)" title=<?=cancellink_title?>  data-dismiss="modal" aria-label="Close"><?=cancellink_text?></a>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer p-n"></div>
        </div>
    </div>
</div>