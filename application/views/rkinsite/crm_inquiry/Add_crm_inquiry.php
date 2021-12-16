<script type="text/javascript">
var productid = [];
var priceid = [];
var provinceid = cityid = areaid = 0;
var member_id = '<?php echo $member_id; ?>';
var assignto = '<?=(!empty($this->session->userdata(base_url().'ADMINID')))?$this->session->userdata(base_url().'ADMINID'):0 ?>';
var inquirydefaultstatus= '<?=(INQUIRY_DEFAULT_STATUS!="")?INQUIRY_DEFAULT_STATUS:0 ?>';
var defaultfollowuptype= '<?=(DEFAULT_FOLLOWUP_TYPE!="")?DEFAULT_FOLLOWUP_TYPE:0 ?>';
var defaultfollowupdate= '<?=(DEFAULT_FOLLOWUP_DATE!="")?DEFAULT_FOLLOWUP_DATE:0 ?>';
var inquirywithproduct= '<?=(!empty(INQUIRY_WITH_PRODUCT))?INQUIRY_WITH_PRODUCT:0 ?>';

var PRODUCT_DISCOUNT = '<?=PRODUCTDISCOUNT?>';
var PRODUCT_PATH = '<?=PRODUCT?>';
var EDITTAXRATE_SYSTEM = '<?=EDITTAXRATE?>';

var categoryoptionhtml = "";
<?php if(!empty($maincategorydata)){ 
    foreach($maincategorydata as $category){ ?>
    categoryoptionhtml += '<option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>';
<?php } } ?>
</script>
<style>
.contactdiv {
    /* border: 3px solid #cccccc; */
    padding: 5px;
    background: #f2f2f2;
    margin-bottom: 15px;
}
.form-group input[type=file] {
    opacity: 0;
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 100;
}
.form-control:disabled {
    background-color: #e4e7ea !important;
}

.fw {
    font-weight: 600;
}

.radio1 label {
    font-size: 22px !important;
}

.radio1 label:before {
    bottom: 8.52px !important;
}

.radio1 input[type=radio]+label {
    color: #4d5056;
}
</style>

<style>
.rate {
    font-size: 35px;
}

.rate .rate-hover-layer {
    color: orange;
}

.rate .rate-select-layer {
    color: orange;
}
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($inquirydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($inquirydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
        <div data-widget-group="group1">
            <form class="form-horizontal" id="crminquiryform" name="crminquiryform">
                <input type="hidden" id="latlongtype" value="">
                <input type="hidden" id="id" name="id" value="<?php if(isset($inquirydata)){ echo $myinquiryid; } ?>">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default border-panel">
                            <div class="panel-heading">
                                <h2><?=Member_label?> Detail</h2>
                            </div>
                            <div class="panel-body pt-n">
                                <div class="row ml3 mr4">
                                    <?php if(isset($memberdata)) {
                                        if(count($memberdata)>0){?>
                                    <b>Company Name : </b> <?php echo $memberdata['companyname']; ?>
                                    <?php } } ?>
                                    <?php if(!isset($inquirydata)) { ?>
                                    <div class="col-md-6">
                                        <div class="form-group" id="new_existing_member_div">
                                            <div class="col-md-12 pl-n">
                                                <label class="control-label" for="new_existing_memberid">New / Existing <?=Member_label?> </label>
                                                <select id="new_existing_memberid" name="new_existing_member" class="selectpicker form-control"  data-live-search="true" data-size="4">
                                                    <option value="new">New </option>
                                                    <option value="existing" >Existing </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" id="existingmember_div">
                                            <div class="col-md-12 pr-n">
                                                <label class="control-label" for="existingmemberid"><?=Member_label?> <span class="mandatoryfield">*</span></label>
                                                <input class="js-data-example-ajax mt-sm" id="existingmemberid" name="existingmemberid" placeholder="Select <?=Member_label?>" style="width:100%;" value="<?php if( $member_id!=""){ echo $member_id; } ?>" data-text="<?php if($membername!=""){ echo $membername; } ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="row ml3 mr4" id="existingmembercontactdiv">
                                    <div class="col-md-10 ">
                                        <div class="form-group" id="contacts_div">
                                            <label class="control-label" for="contacts">Contact <span class="mandatoryfield">*</span></label>
                                            <select id="contacts" name="contacts" class="selectpicker form-control"  data-live-search="true" data-size="5">
                                                <option value="0">Select Contact</option>
                                                <?php if(isset($contactdetail)){ foreach($contactdetail as $cd){ 
                                                    $Name = "Name : ".$cd['firstname']." ".$cd['lastname']." | Email : ".$cd['email']." | Mobile No. : ".$cd['countrycode'].$cd['mobileno'];?>
                                                    <option value="<?php echo $cd['id']; ?>" <?php if(isset($inquirydata)){ if($contactid == $cd['id']){ echo 'selected'; } } ?>><?php echo $Name; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group" id="contacts_div">
                                            <label class="control-label" for="contacts"></label>
                                            <button type="button" class="btn btn-primary pull-right addcontact" id="addcontactbtn" style="margin-top:<?=(empty($modalview))?'33px':'12px'?>;" data-toggle="modal" data-target="#addcontactmodal" <?php if(!isset($inquirydata) && empty($member_id)){ echo "disabled"; } ?>>ADD CONTACT</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(!isset($inquirydata)) { ?>
                <div class="row" id="memberdetail">
                    <div class="col-md-12">
                        <div class="panel panel-default border-panel">
                            <div class="panel-heading">
                                <h2><?=Member_label?> Details</h2>
                            </div>
                            <div class="panel-body pt-n">
                                <div id="memberdetail">
                                    <div class="row mr4 ml3">
                                        <div class="col-md-3">
                                            <div class="form-group" id="companyname_div">
                                                <div class="col-md-12 pl-n">
                                                    <label class="control-label" for="companyname">Company Name <span class="mandatoryfield">*</span></label>
                                                    <input type="text" id="companyname" value="<?php if(!empty($memberdata)){ echo $memberdata['companyname']; } ?>" name="companyname" class="form-control">
                                                    <span class="mandatoryfield" id="companynameduplicatemessage"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group" id="name_div">
                                                <div class="col-md-12 pl-n">
                                                    <label class="control-label" for="name"><?=Member_label?> Name <span class="mandatoryfield">*</span></label>
                                                    <input type="text" id="name" value="<?php if(!empty($memberdata)){ echo $memberdata['name']; } ?>" name="name" class="form-control" onkeyup="$('#firstname1').val(this.value)" onkeypress="return onlyAlphabets(event)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" id="website_div">
                                                <div class="col-md-12 pl-n">
                                                    <label class="control-label" for="website">Website</label>
                                                    <input type="text" id="website" name="website" value="<?php if(isset($memberdata)){ echo $memberdata['website']; } ?>" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="contactdivs">
                                        <div class="contactdiv" id="contactdiv1" div-id="1">
                                            <div class="row ml3">
                                                <div class="col-md-6 ">
                                                    <div class="radio radio1">
                                                        <input type="radio" name="inquirycontact" id="inquirycontact1" class='inquirycontact' value="1" checked>
                                                        <label for="inquirycontact1" class="contactheading" heading-id="1">Contact 1</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 text-right notebtn">
                                                    <span class=" mr-3" style="color:#800080">Note : Either Mobile or Email is Required</span>
                                                    <button type="button" class="<?=addbtn_class;?>" id="contactdivbtn1" onclick='addnewcontact();'><i class="fa fa-plus"></i> ADD</button>
                                                </div>
                                            </div>
                                            <div class="row  ml3 mr4 ">
                                                <div class="col-md-3 ">
                                                    <div class="form-group" id="firstname_div1">
                                                        <div class="col-md-12 pl-n">
                                                            <label class="control-label" for="firstname1">First Name</label>
                                                            <input type="text" id="firstname1" name="firstname[]" class="form-control fromgroup" onkeypress="return onlyAlphabets(event)">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 ">
                                                    <div class="form-group" id="lastname_div1">
                                                        <div class="col-md-12 pl-n">
                                                            <label class="control-label" for="lastname1">Last Name</label>
                                                            <input type="text" id="lastname1" name="lastname[]" class="form-control fromgroup"  onkeypress="return onlyAlphabets(event)">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 ">
                                                    <div class="form-group" id="mobile_div1">
                                                        <div class="col-md-12 pl-n">
                                                            <label class="control-label" for="mobileno1">Mobile No <span class="mandatoryfield" style="color:#800080">*</span></label>
                                                            <input id="mobileno1" type="text" name="mobileno[]" class="form-control mobileno number fromgroup" maxlength="10" onkeypress="return isNumber(event)" div-id="1">
                                                            <span class="mandatoryfield" id="mobilenoduplicatemessage" div-id="1"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="email_div1">
                                                        <div class="col-md-12 pl-n">
                                                            <label class="control-label" for="email1">Email <span class="mandatoryfield" style="color:#800080"> *</span></label>
                                                            <input id="email1" type="text" name="email[]" class="form-control email fromgroup"  div-id="1">
                                                            <span class="mandatoryfield" id="emailduplicatemessage" div-id="1"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row ml3 mr4">
                                                <div class="col-md-3">
                                                    <div class="form-group" id="designation_div1">
                                                        <div class="col-md-12 pl-n">
                                                            <label class="control-label" for="designation1">Designation </label>
                                                            <input type="text" id="designation1" name="designation[]" class="form-control fromgroup" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="department_div1">
                                                        <div class="col-md-12 pl-n">
                                                            <label class="control-label" for="department1">Department </label>
                                                            <input type="text" id="department1" name="department[]" class="form-control fromgroup" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="birthdate_div1">
                                                        <div class="col-md-12 pl-n">
                                                            <label class="control-label" for="birthdate1">Birth Date </label>
                                                            <input id="birthdate1" type="text" name="birthdate[]"  value="<?php if(isset($expense_data)){ echo $this->general_model->displaydate($expense_data['date']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>"class="form-control fromgroup  datepicker1"  readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="annidate_div1">
                                                        <div class="col-md-12 pl-n">
                                                            <label class="control-label" for="annidate1">Anniversary Date </label>
                                                            <input id="annidate1" type="text" name="annidate[]"  value="<?php if(isset($expense_data)){ echo $this->general_model->displaydate($expense_data['date']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>"class="form-control datepicker1 fromgroup"  readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ml3 mr4">
                                        <div class="col-sm-2">
                                            <div class="form-group" id="membercode_div">
                                                <div class="col-md-12 p-n pr-sm">
                                                    <label class="control-label" for="membercode"><?=Member_label?> Code <span class="mandatoryfield">*</span></label>
                                                    <div class="col-sm-10" style="padding: 0px;">
                                                        <input id="membercode" type="text" name="membercode" value="<?php if(isset($memberdata)){ echo $memberdata['membercode']; } ?>" class="form-control" maxlength="8">
                                                    </div>
                                                    <div class="col-sm-2 p-n pt-sm">
                                                        <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Generate Code" onclick="$('#membercode').val(randomPassword(8,8,0,0,0))"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group row" id="mobilenumber_div">
                                                <div class="col-md-12">
                                                    <label class="control-label" for="mobilenumber">Mobile No. <span class="mandatoryfield">*</span></label>  
                                                    <div class="row">
                                                        <div class="col-md-4 pr-sm">
                                                            <select id="countrycodeid" name="countrycodeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                                <option value="0">Code</option>
                                                                <?php foreach($countrycodedata as $countrycoderow){ ?>
                                                                <option value="<?php echo $countrycoderow['phonecode']; ?>" <?php if(isset($memberdata) && $memberdata['countrycode']==$countrycoderow['phonecode']){ echo 'selected'; }else{ if(DEFAULT_PHONECODE==$countrycoderow['phonecode']){ echo 'selected'; } } ?>><?php echo $countrycoderow['phonecode']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-8 pl-sm">
                                                            <input id="mobilenumber" type="text" name="mobilenumber" value="<?php if(isset($memberdata)){ echo $memberdata['mobile']; } ?>" class="form-control" maxlength="10"  onkeypress="return isNumber(event)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group row" id="memberemail_div">
                                                <label class="control-label" for="memberemail">Email <span class="mandatoryfield">*</span></label>
                                                <div class="col-md-12 pl-n pr-sm">
                                                    <input id="memberemail" type="text" name="memberemail" value="<?php if(isset($memberdata)){ echo $memberdata['email']; } ?>" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group" id="password_div">
                                                <div class="col-md-12 p-n pr-sm">
                                                    <label class="control-label" for="password">Password <span class="mandatoryfield">*</span></label>
                                                    <div class="col-sm-10" style="padding: 0px;">
                                                        <input id="password" type="text" name="password" value="<?php if(isset($memberdata)){ echo $this->general_model->decryptIt($memberdata['password']); } ?>" class="form-control">
                                                    </div>
                                                    <div class="col-sm-2 p-n pt-sm">
                                                        <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Generate Password" onclick="$('#password').val(randomPassword())"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=" row ml3 mr4">
                                        <div class="col-sm-3">
                                            <div class="form-group" id="country_div">
                                                <label class="control-label" for="countryid">Country <span class="mandatoryfield">*</span></label>
                                                <select id="countryid" name="countryid" class="selectpicker bootstrap_select  form-control"  data-live-search="true" data-size="4">
                                                <?php foreach($countrydata as $countryrow){ ?>
                                                    <option value="<?php echo $countryrow['id']; ?>" <?php if(isset($memberdata)){ if(isset($countryID['countryid'])){ if($countryrow['id']==$countryID['countryid']){ echo 'selected';} } }else { if($countryrow['id']==101){ echo 'selected';} } ?>><?php echo $countryrow['name']; ?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group" id="province_div">
                                                <label class="control-label" for="provinceid">State <span class="mandatoryfield">*</span></label>
                                                <select id="provinceid" name="provinceid" class="selectpicker bootstrap_select form-control" title="Select State"  data-live-search="true" data-size="5" readonly>
                                                    <option value="0">Select State</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group" id="city_div">
                                                <label class="control-label" for="cityid">City <span class="mandatoryfield">*</span></label>
                                                <select name="cityid" class="selectpicker form-control bootstrap_select "  id="cityid" data-live-search="true" data-size="5" readonly>
                                                    <option value="0">Select City</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group" id="area_div">
                                                <label class="control-label" for="areaid">Area</label>
                                                <select name="areaid" class="selectpicker form-control bootstrap_select "  id="areaid" data-live-search="true" data-size="5" readonly>
                                                    <option value="0">Select Area</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ml3 mr4">
                                        <div class="col-md-6 mt-6">
                                            <div class="form-group" id="address_div">
                                                <label class="col-form-label control-label" for="address">Address </label>
                                                <textarea id="address" name="address" rows="4" class="form-control" ><?php if(isset($memberdata)){ echo $memberdata['address']; } ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row" id="pincode_div">
                                                <label class="col-md-3 col-form-label control-label " for="pincode">Pincode</label>
                                                <div class="col-md-5">
                                                    <input type="text" id="pincode" value="<?php if(!empty($memberdata)){ echo $memberdata['pincode']; } ?>" name="pincode" class="form-control" >
                                                </div>
                                                <div class="col-md-4 p-0">
                                                    <button type="button" class="form-control" style="width: auto;" onclick="openmodal(1,<?php echo (!empty($memberdata))?$memberdata['latitude']:DEFAULT_LAT?>,<?php echo (!empty($memberdata))?$memberdata['longitude']:DEFAULT_LNG?>)"><i class="fa fa-map-marker"></i> Pickup Location</button>
                                                </div>
                                            </div>
                                            <div class="form-group row" id="latitude_div">
                                                <label class="col-md-3 col-form-label control-label mt10" for="latitude">Latitude </label>
                                                <div class="col-md-9">
                                                    <input type="text" id="latitude" value="<?php if(!empty($memberdata)){ echo $memberdata['latitude']; } ?>" name="latitude" class="form-control" onkeypress="return decimal(event,this.id);">
                                                </div>
                                            </div>
                                            <div class="form-group row" id="longitude_div">
                                                <label class="col-md-3 col-form-label control-label mt10" for="longitude">Longitude </label>
                                                <div class="col-md-9">
                                                    <input type="text" id="longitude" value="<?php if(!empty($memberdata)){ echo $memberdata['longitude']; } ?>" name="longitude" class="form-control"  onkeypress="return decimal(event,this.id);">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="row ml3 mr4">
                                        <div class="col-md-6">
                                            <div class="form-group row" id="leadsource_div">
                                                <label class="col-form-label col-md-3 control-label " for="leadsource"><?=Member_label?> Lead Source <span class="mandatoryfield">*</span></label>
                                                <div class="col-md-9">
                                                    <select id="leadsource" name="leadsource" class="selectpicker form-control" title=" Select Customer Lead Source"  data-live-search="true">
                                                        <option value="0">Select <?=Member_label?> Lead Source</option>
                                                        <?php foreach($leadsourcedata as $ls){ ?>
                                                            <option value="<?php echo $ls['id']; ?>" <?php if(isset($memberdata)){ if($memberdata['leadsourceid'] == $ls['id']){ echo 'selected'; } } ?>><?php echo $ls['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row" id="zoneid_div">
                                                <label class="col-form-label   control-label col-md-3" for="zoneid">Zone</label>
                                                <div class="col-md-9">
                                                    <select id="zoneid" name="zoneid" class="selectpicker form-control" data-live-search="true" data-size="4"  title="Select Zone">
                                                        <option value="0">Select Zone</option>
                                                        <?php foreach($zonedata as $row){ ?>
                                                            <option value="<?php echo $row['id']; ?>" <?php if(isset($memberdata)){ if(isset($memberdata['zoneid'])){ if($row['id']==$memberdata['zoneid']){ echo 'selected';} } } ?>><?php echo $row['zonename']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row" id="industrycategory_div">
                                                <label class="col-form-label control-label col-md-3" for="industrycategory">Industry </label>
                                                <div class="col-md-9">
                                                    <select id="industrycategory" name="industrycategory" class="selectpicker form-control" title="Select Industry" data-live-search="true" >
                                                        <option value="0">Select Industry</option>
                                                        <?php foreach($industrycategorydata as $row){ ?>
                                                            <option value="<?php echo $row['id']; ?>" <?php if(isset($memberdata)){ if($memberdata['industryid'] == $row['id']){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" id="remarks_div">
                                                <label class="col-form-label control-label" for="remarks">Remarks </label>
                                                <textarea id="remarks" name="remarks" rows="4" class="form-control" ><?php if(isset($memberdata)){ echo $memberdata['remarks']; } ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ml3 mr4">
                                        <div class="col-md-3">
                                            <div class="form-group" id="rating_div">
                                                <label class="control-label" for="rating">Rating</label>
                                                <div id="rate" class="rate"></div>
                                                <input id="rating" name="rating" type="hidden" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 ">
                                            <div class="form-group" id="types_div">
                                                <label class="control-label" for="types">Type</label>
                                                <select id="types" name="types" class="selectpicker bootstrap_select form-control" data-live-search="true" >
                                                    <option value="0">Select Type</option>
                                                    <?php foreach ($this->Membertype as $key=>$type) { ?>
                                                        <option value="<?=$key?>" <?php if(isset($memberdata)) {if ($memberdata['type'] == $key) {echo 'selected';}} ?>><?=$type?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="employee_div">
                                                <div class="col-md-12">
                                                    <label class="control-label" for="employee"><?=Member_label?> Assign To <span class="mandatoryfield">*</span></label>
                                                    <select class="selectpicker form-control" id="employee" name="employee[]" data-live-search="true" data-size="5" multiple>
                                                        <?php foreach ($employeedata as $_v) { ?>
                                                        <option value="<?php echo $_v['id'];?>" <?php if(isset($child_sibling_employee_data) && !in_array($_v['id'],$child_sibling_employee_data) && $checkrights==1){ echo "disabled"; } ?> <?php if(!empty($inquirydata))
                                                            { if(in_array($_v['id'],$assignemparr)){echo "selected";} }else{
                                                                if(($_v['id']==$this->session->userdata(base_url().'ADMINID'))){echo "selected";}
                                                            } ?>>
                                                            <?php echo $_v['name'];?></option>
                                                        <?php } ?>
                                                    </select> 
                                                </div>
                                            </div>
                                        </div>  
                                        <div class="col-md-3">
                                            <div class="form-group" id="memberstatus_div">
                                                <div class="col-md-12">
                                                    <label class="control-label" for="employee"><?=Member_label?> Status <span class="mandatoryfield">*</span></label>
                                                    <select name="memberstatus" id="memberstatus" class="selectpicker form-control" data-live-search="true" data-size="4" title="Select Member Status">                                                     
                                                        <option value="0">Select <?=Member_label?> Status</option>
                                                        <?php foreach ($memberstatusesdata as $ms) { ?>
                                                        <option value="<?php echo $ms['id'];?>" <?php if (isset($memberdata)) { if ($memberdata['status'] == $ms['id']) { echo 'selected'; } }else{ if(MEMBER_DEFAULT_STATUS!="" && MEMBER_DEFAULT_STATUS==$ms['id']){ echo 'selected'; } } ?>>
                                                            <?php echo $ms['name'];?></option>
                                                        <?php } ?>
                                                    </select>  
                                                </div>
                                            </div>
                                        </div>  
                                    </div>
                                </div>  
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default border-panel">
                            <div class="panel-heading">
                                <h2>Product Detail</h2>
                            </div>
                            <div class="panel-body pt-n">
                                <div id="multi_product">
                                    <?php if(isset($inquirydata) && isset($inquirydata[0]['productid']) && !is_null($inquirydata[0]['productid'])) {
                                        $ij=1;
                                        foreach ($inquirydata as $v1) {
                                            if(is_null($v1['productid'])){
                                                continue;
                                            }
                                            if($v1['amount']-$v1['tax']>0){
                                                $tax = number_format($v1['tax']*100/($v1['amount']-$v1['tax']),2,'.','');
                                            }else{
                                                $tax = 0;
                                            }
                                            $tax = $v1['tax'];
                                           
                                            if($v1['qty']>0){
                                                $qty = $v1['qty'];
                                            }else{
                                                $qty = 1;
                                            }
                                            $rate = $v1['rate'] * $qty;
                                            if(($rate - $v1['discount'])==0){
                                                $rate = 0;
                                            }else{
                                                $rate = $rate - $v1['discount'];
                                            } 
                                            $taxamount = (($rate*$tax)/100);
                                        ?>
                                        <div id="productrow<?php echo $ij; ?>" class="countproducts">
                                        <?php 
                                            if($ij>1){
                                                echo '<hr style="height: 1px;background: lightblue;">';
                                            } ?>
                                            <input type="hidden" name="crminquiryproductid[]" value="<?=$v1['crminquiryproductid']?>">

                                            <div class="row ml3 mr4">
                                                <div class="col-md-4">
                                                    <div class="form-group productcategorydiv" id="productcategory<?=$ij?>_div">
                                                        <label class="control-label" for="productcategory<?=$ij?>">Product Category <span class="mandatoryfield">*</span></label>
                                                        <div class="col-md-12 pl-n">
                                                            <select prow="<?=$ij?>" id="productcategory<?=$ij?>" name="productcategory[]" class="selectpicker form-control productcategory" data-live-search="true" data-size="8" >
                                                                <option value="0">Select Product Category</option>
                                                                <?php foreach($maincategorydata as $row){ ?>
                                                                <option value="<?php echo $row['id']; ?>" <?php if($row['id']==$v1['categoryid']){ echo 'selected';} ?>><?php echo $row['name']; ?>
                                                                </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group productdiv" id="product<?=$ij?>_div">
                                                        <label class="control-label" for="product<?=$ij?>">Product <span class="mandatoryfield">*</span></label>
                                                        <div class="col-md-12 pl-n">
                                                            <select product-select-id="<?=$ij?>" id="product<?=$ij?>" name="product[]" class="selectpicker form-control product" data-live-search="true" data-size="8">
                                                                <option value="0">Select Product</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group pricediv" id="price<?=$ij?>_div">
                                                        <label class="control-label" for="priceid<?=$ij?>">Variant <span class="mandatoryfield">*</span></label>
                                                        <div class="col-md-12 pl-n">
                                                            <select variant-select-id="<?=$ij?>" id="priceid<?=$ij?>" name="priceid[]" class="selectpicker form-control priceid" product-select-id="<?=$ij?>" data-live-search="true" data-size="8" >
                                                                <option value="0">Select Variant</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row ml3 mr4">
                                                <div class="col-md-1">
                                                    <div class="form-group qtydiv" id="qty<?=$ij?>_div">
                                                        <div class="col-md-12 pl-n">
                                                            <label class="control-label" for="qty<?=$ij?>">Qty. <span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="qty<?=$ij?>" value="<?php echo $v1['qty']; ?>" name="qty[]" class="qty form-control text-right" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" onchange="changeamount('<?=$ij?>')">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ratediv" id="productrate<?=$ij?>_div">
                                                        <div class="col-md-12 pl-n text-right">
                                                            <label class="control-label" for="productrate<?=$ij?>">Rate <span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="productrate<?=$ij?>" value="<?php echo $v1['rate']; ?>" name="productrate[]" class="form-control text-right productrate" onkeypress="return decimal(event,this.id);" onchange="changeamount('<?=$ij?>')">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>">
                                                    <div class="form-group discountpercentdiv" id="discountpercent<?=$ij?>_div">
                                                        <div class="col-md-12 pl-n text-right">
                                                            <label class="control-label" for="discountpercent<?=$ij?>">Dis. (%) </label>
                                                            <input type="text" id="discountpercent<?=$ij?>" value="<?php echo $v1['discountpercentage']; ?>" name="discountpercent[]" class="form-control text-right" onkeyup="return onlypercentage(this.id)" onchange="changediscount('<?=$ij?>')" onkeypress="return decimal(event,this.id);">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>">
                                                    <div class="form-group discountdiv" id="discount<?=$ij?>_div">
                                                        <div class="col-md-12 pl-n text-right">
                                                            <label class="control-label" for="discount<?=$ij?>">Dis. (<?=CURRENCY_CODE?>)</label>
                                                            <input type="text" id="discount<?=$ij?>" value="<?php if(!empty($v1)){ echo $v1['discount']; } ?>" name="discount[]" class="form-control text-right discount" onchange="changepercentage('<?=$ij?>')" onkeypress="return decimal(event,this.id);" div-id="<?=$ij?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group amountdiv" id="amount<?=$ij?>_div">
                                                        <div class="col-md-12 pl-n text-right">
                                                            <label class="control-label" for="amount<?=$ij?>">Amount (<?=CURRENCY_CODE?>)<span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="amount<?=$ij?>" value="<?php echo  $rate; ?>" name="amount[]" class="form-control text-right productsamount" readonly onkeypress="return decimal(event,this.id);">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group taxdiv" id="tax<?=$ij?>_div">
                                                        <div class="col-md-12 pl-n text-right">
                                                            <label class="control-label" for="tax<?=$ij?>">Tax (%)<span id="displaytax"></span></label>
                                                            <input type="text" id="tax<?=$ij?>" value="<?php echo number_format($tax,2,'.',''); ?>" name="tax[]" class="form-control text-right taxes" maxlength="5" onkeypress="return decimal(event,this.id);" onchange="changeamount('<?=$ij?>')" <?php if(EDITTAXRATE==0){ echo "readonly"; } ?>>
                                                            <input type="hidden" class="taxvalue" name="taxvalue[]" id="taxvalue<?=$ij?>" value="<?=number_format($taxamount,2,'.','')?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group netamountdiv" id="netamount<?=$ij?>_div">
                                                        <div class="col-md-12 pl-n text-right">
                                                            <label class="control-label" for="netamount<?=$ij?>">Net Amount <span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="netamount<?=$ij?>" value="<?php echo number_format($rate+$taxamount,2,'.',''); ?>" name="netamount[]" class="form-control text-right productsnetamount"  readonly onkeypress="return decimal(event,this.id);">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 pt-xxl">
                                                    <?php if(($ij-1)==0){?>
                                                        <?php if(count($inquirydata)>1){ ?>
                                                            <button type="button" class="btn btn-danger btn-raised  remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                        <?php }else { ?>
                                                            <button type="button" class="btn btn-primary btn-raised add_btn_product" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                        <?php } ?>

                                                    <? }else if(($ij-1)!=0) { ?>
                                                        <button type="button" class="btn btn-danger btn-raised remove_btn_product" onclick="removeproduct(<?=$ij?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                    <? } ?>
                                                    <button type="button" class="btn btn-danger btn-raised btn-sm remove_btn_product" onclick="removeproduct(<?=$ij?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                                
                                                    <button type="button" class="btn btn-primary btn-raised add_btn_product" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>  
                                                </div>
                                            </div>
                                            <script type="text/javascript">
                                            $(document).ready(function(){
                                                productid.push(<?php echo $v1['productid']; ?>);
                                                priceid.push(<?php echo $v1['priceid']; ?>);

                                                getproduct(<?=$ij?>);
                                                getproductprice(<?=$ij?>);
                                            });
                                            </script>
                                        </div> 
                                        <?php
                                        $ij++; 
                                        }
                                    }else{ ?>
                                        <div id="productrow1" class="countproducts">
                                            <div class="row ml3 mr4">
                                                <div class="col-md-4">
                                                    <div class="form-group productcategorydiv" id="productcategory1_div">
                                                        <label class="control-label" for="productcategory1">Product Category <span class="mandatoryfield">*</span></label>
                                                        <div class="col-md-12 pl-n">
                                                            <select prow="1" id="productcategory1" name="productcategory[]" class="selectpicker form-control productcategory" data-live-search="true" data-size="8" >
                                                                <option value="0">Select Product Category</option>
                                                                <?php foreach($maincategorydata as $row){ ?>
                                                                <option value="<?php echo $row['id']; ?>" ><?php echo $row['name']; ?>
                                                                </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group productdiv" id="product1_div">
                                                        <label class="control-label" for="product1">Product <span class="mandatoryfield">*</span></label>
                                                        <div class="col-md-12 pl-n">
                                                            <select product-select-id="1" id="product1" name="product[]" class="selectpicker form-control product" product-select-id="1" data-live-search="true" data-size="8" >
                                                                <option value="0">Select Product</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group pricediv" id="price1_div">
                                                        <label class="control-label" for="priceid1">Variant <span class="mandatoryfield">*</span></label>
                                                        <div class="col-md-12 pl-n">
                                                            <select variant-select-id="1" id="priceid1" name="priceid[]" class="selectpicker form-control priceid" product-select-id="1" data-live-search="true" data-size="8" >
                                                                <option value="0">Select Variant</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row ml3 mr4">
                                                <div class="col-md-1">
                                                    <div class="form-group qtydiv" id="qty1_div">
                                                        <div class="col-md-12 pl-n">
                                                            <label class="control-label" for="qty1">Qty. <span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="qty1" value="" name="qty[]" class="qty form-control text-right" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" onchange="changeamount('1')">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ratediv" id="productrate1_div">
                                                        <div class="col-md-12 pl-n text-right">
                                                            <label class="control-label" for="productrate1">Rate (<?=CURRENCY_CODE?>)<span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="productrate1" value="" name="productrate[]" class="form-control text-right productrate"  onkeypress="return decimal(event,this.id);" onchange="changeamount('1')">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>">
                                                    <div class="form-group discountpercentdiv" id="discountpercent1_div">
                                                        <div class="col-md-12 pl-n text-right">
                                                            <label class="control-label" for="discountpercent1">Dis. (%) </label>
                                                            <input type="text" id="discountpercent1" value="" name="discountpercent[]" class="form-control text-right" onkeyup="return onlypercentage(this.id)" onchange="changediscount('1')" onkeypress="return decimal(event,this.id);">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>">
                                                    <div class="form-group discountdiv" id="discount1_div">
                                                        <div class="col-md-12 pl-n text-right">
                                                            <label class="control-label" for="discount1">Dis. (<?=CURRENCY_CODE?>)</label>
                                                            <input type="text" id="discount1" value="" name="discount[]" class="form-control text-right discount" onchange="changepercentage('1')" onkeypress="return decimal(event,this.id);" div-id="1">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group amountdiv" id="amount1_div">
                                                        <div class="col-md-12 pl-n text-right">
                                                            <label class="control-label" for="amount1">Amount (<?=CURRENCY_CODE?>)<span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="amount1" value="" name="amount[]" class="form-control text-right productsamount" readonly onkeypress="return decimal(event,this.id);">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group taxdiv" id="tax1_div">
                                                        <div class="col-md-12 pl-n text-right">
                                                            <label class="control-label" for="tax1">Tax (%)<span id="displaytax"></span></label>
                                                            <input type="hidden" class="taxvalue" name="taxvalue[]" id="taxvalue1">
                                                            <input type="text" id="tax1" value="" name="tax[]" class="form-control text-right taxes" maxlength="5" onkeypress="return decimal(event,this.id);" onchange="changeamount('1')" <?php if(EDITTAXRATE==0){ echo "readonly"; } ?>>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group netamountdiv" id="netamount1_div">
                                                        <div class="col-md-12 pl-n text-right">
                                                            <label class="control-label" for="netamount1">Net Amount (<?=CURRENCY_CODE?>)<span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="netamount1" value="" name="netamount[]" class="form-control text-right productsnetamount"  readonly onkeypress="return decimal(event,this.id);">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 pt-xxl">
                                                    <button type="button" class="btn btn-danger btn-raised remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>
                                                    <button type="button" class="btn btn-primary btn-raised add_btn_product" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <hr />
                                    <div class="row">
                                        <div class="col-md-9" style="border-right: 2px solid #8e8c8c;">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <label for="focusedinput" class="col-md-5 control-label">Installment </label>
                                                        <div class="col-md-7">
                                                            <div class="row">
                                                                <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                                                    <div class="radio">
                                                                        <input type="radio" name="installmentstatus" id="installmentyes" value="1" class="from-control" <?php  if(isset($inquirydata) && $noofinstallment>0){ echo "checked";} ?>>
                                                                        <label for="installmentyes">Yes</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4 col-xs-4">
                                                                    <div class="radio">
                                                                        <input type="radio" name="installmentstatus" id="installmentno" value="0" class="from-control" <?php  if(!isset($inquirydata) || $noofinstallment==0){ echo "checked";} ?>>
                                                                        <label for="installmentno">No</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row ml3 mr4" id="installmentsetting_div">
                                                        <div class="col-md-3">
                                                            <div class="form-group" id="noofinstallment_div">
                                                                <div class="col-md-12 pl-n">
                                                                    <label class="control-label" for="text-input">No. of Installment </label>
                                                                    <input type="text" id="noofinstallment" value="<?php if(isset($inquirydata) && $noofinstallment>0){echo $noofinstallment;} ?>" name="noofinstallment" class="tax_fromgroup form-control"  maxlength="5" onkeypress="return isNumber(event);">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group" id="emidate_div1">
                                                                <div class="col-md-12 pl-n">
                                                                    <label class="control-label" for="emidate">EMI Start Date </label>
                                                                    <input id="emidate" type="text" name="emidate[]"   class="form-control datepicker1 tax_fromgroup"  readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group" id="emiduration_div">
                                                                <div class="col-md-12 pl-n">
                                                                    <label class="control-label" for="text-input">EMI Duration (In Days)</label>
                                                                    <input type="text" id="emiduration" name="emiduration" class=" tax_fromgroup form-control"  maxlength="5" onkeypress="return isNumber(event);">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button class="btn btn-primary btn-raised btn-label" type="button" id="generateinstallment" style="margin-top: 44px;">Generate</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="installmentmaindiv">
                                                <div class="row" id="installmentmaindivheading">
                                                    <div class="col-md-2 control-label text-center" style="text-align: center;"><b>Sr. No.</b></div>
                                                    <div class="col-md-2 control-label text-center"><b>Installment (%)</b></div>
                                                    <div class="col-md-2 control-label text-center"><b>Amount</b></div>
                                                    <div class="col-md-2 control-label text-center" style="text-align: left;padding-left:0;"><b>Installment Date</b></div>
                                                    <div class="col-md-2 control-label text-center" style="text-align: left;padding-left:0;"><b>Payment Date</b></div>
                                                    <div class="col-md-2 control-label text-center"><b>Received Status</b></div>
                                                </div><br />
                                                <div id="installmentdivs">
                                                <?php
                                                if(isset($inquirydata) && isset($noofinstallment) && $noofinstallment>0 && isset($installment) && count($installment)>0){
                                                    foreach ($installment as $k=>$inmt) {
                                                        $div = $k+1;
                                                    ?>
                                                        <input type="hidden" name="installmentid[]" value="<?=$inmt['id']?>">
                                                        <div class="row noofinstallmentdiv">
                                                            <div class="col-md-2 text-center"><?=$div?></div>
                                                            <div class="col-md-2 text-center">
                                                                <div class="col-md-12 pl-n">
                                                                    <div class="form-group mt-n">
                                                                        <input type="text" id="percentage<?=$div?>" name="percentage[]" class="form-control percentage text-right" value="<?=$inmt['percentage']?>" div-id="<?=$div?>" maxlength="5" onkeyup="return onlypercentage(this.id)" onkeypress="return decimal(event,this.id)">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <div class="col-md-12 pl-n">
                                                                    <div class="form-group mt-n">
                                                                        <input type="text" id="installmentamount<?=$div?>" name="installmentamount[]" class="form-control text-right installmentamount" div-id="<?=$div?>" maxlength="5" value="<?=$inmt['amount']?>" onkeypress="return decimal(event,this.id);" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <div class="col-md-12 pl-n">
                                                                    <div class="form-group mt-n">
                                                                        <input type="text" id="installmentdate<?=$div?>" name="installmentdate[]" value="<?php if($inmt['date']!="0000-00-00"){ echo $this->general_model->displaydate($inmt['date']);  } ?>" class="form-control installmentdate" div-id="<?=$div?>" maxlength="5">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <div class="col-md-12 pl-n">
                                                                    <div class="form-group mt-n">
                                                                        <input type="text" id="paymentdate<?=$div?>" name="paymentdate[]" value="<?php if($inmt['paymentdate']!="0000-00-00"){ echo $this->general_model->displaydate($inmt['paymentdate']);  } ?>" class="form-control paymentdate" div-id="<?=$div?>" maxlength="5">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <div class="checkbox">
                                                                    <input id="installmentstatus<?=$div?>" type="checkbox" value="1" name="installmentstatus<?=$div?>" <?php if($inmt['status']==1){ echo "checked";} ?> div-id="<?=$div?>" class="checkradios">
                                                                    <label for="installmentstatus<?=$div?>"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php }
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="row ml3 mr4">
                                                <div class="col-md-12">
                                                    <table style="width:100%;">
                                                        <tr>
                                                            <td><h6 class="fw fs13">Gross Amount </h6></td>
                                                            <td><h6 class="pull-right  fs13 fw" id="totalgrossamount">00.00</h6></td>
                                                        </tr>
                                                        <tr>
                                                            <td><h6 class="fw fs13">Tax </h6> </td>
                                                            <td><h6 class="pull-right fs13 fw" id="totaltaxamount">00.00</h6></td>
                                                        </tr>
                                                        <tr>
                                                            <td><h6 class="fw fs13">Net Amount </h6></td>
                                                            <td><h6 class="pull-right fs13 fw" id="totalnetamount">00.00</h6></td>
                                                        </tr>
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default border-panel">
                            <div class="panel-heading"><h2>Status</h2></div>
                            <div class="panel-body pt-n">
                                <div class="row mr4">
                                    <div class="col-md-8 notess">
                                        <div class="col-md-12 p-0 float-left">
                                            <div class="col-md-6 float-left">
                                                <div class="form-group" id="status_div">
                                                    <label class="control-label" for="status">Status  <span class="mandatoryfield">*</span></label>
                                                    <select id="status" name="status" class="selectpicker form-control bootstrap_select" title="Select Status" data-live-search="true" >
                                                        <option value="0">Select Status</option>
                                                        <?php foreach($inquirystatusdata as $row){ ?>
                                                            <option value="<?php echo $row['id']; ?>" <?php if(isset($inquirydata) && $status==$row['id']){ echo 'selected'; } else if(INQUIRY_DEFAULT_STATUS!="" && INQUIRY_DEFAULT_STATUS==$row['id']){ echo 'selected'; } ?>><?php echo $row['name']; ?></option> 
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group" id="inquiryleadsource_div">
                                                    <label class="control-label" for="inquiryleadsource">Inquiry Lead Source <span class="mandatoryfield">*</span></label>
                                                    <select id="inquiryleadsource" name="inquiryleadsource" title="Select Inquiry Lead Source"  data-live-search="true" class="selectpicker form-control bootstrap_select" >
                                                        <option value="0">Select Inquiry Lead Source</option>
                                                        <?php foreach($leadsourcedata as $row){ ?>
                                                        <option value="<?php echo $row['id']; ?>" <?php if(isset($inquirydata)){ if($inquiryleadsourceid == $row['id']){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option> 
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 float-left">
                                                <div class="form-group" id="inquiryemployee_div">
                                                    <label class="control-label" for="inquiryemployee"><?=Inquiry?> Assign To <span class="mandatoryfield">*</span></label>
                                                    <input type="hidden" value="<?php if(isset($inquiryassignto)){ echo $inquiryassignto; } ?>" name="oldinquiryassignto" id="oldinquiryassignto">
                                                    <input type="hidden" value="<?php if(isset($inquirydata[0])){ echo $inquirydata[0]['assigntoempname']; } ?>" name="oldinquiryassignname" id="oldinquiryassignname">
                                                    <select class="selectpicker form-control bootstrap_select" id="inquiryemployee"  data-live-search="true"name="inquiryemployee" title="Select  Inquiry Assign" data-size="5" >
                                                        <option value="0">Select Inquiry Assign To </option>
                                                        <?php foreach ($inquiryemployee_data as $_v) { ?>
                                                        <option value="<?php echo $_v['id'];?>" <?php if(isset($child_employee_data) && !in_array($_v['id'],$child_employee_data) && isset($sibling_employee_data) && !in_array($_v['id'],$sibling_employee_data) && $checkrights==1){ echo "disabled"; } ?> <?php if(isset($inquiryassignto))
                                                            {if($inquiryassignto==$_v['id']){echo "selected";}}else{
                                                                if(($_v['id']==$this->session->userdata(base_url().'ADMINID'))){echo "selected";}
                                                            } ?>>
                                                            <?php echo ucwords($_v['name']);?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 p-0 float-left">
                                            <div class="col-md-6 float-left">
                                                <div class="form-group" id="paymentdate_div" style="display:<?=(isset($inquirydata) && $status==INQUIRY_CONFIRM_STATUS && $noofinstallment==0)?'block':'none'?>">
                                                    <label class="control-label" for="status">Payment Date</label>
                                                    <input id="confirmdatetime" type="text" name="confirmdatetime" class="form-control  datepicker1" value="<?=(isset($inquirydata) && $inquirydata[0]['confirmdatetime']!='0000-00-00 00:00:00')?$this->general_model->displaydate($inquirydata[0]['confirmdatetime']):''?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6 float-left">
                                                <div id="reason_div" style="display:none;">
                                                    <label class="control-label" for="reason">Reason <span class="mandatoryfield">*</span></label>
                                                    <textarea name="reason" id="reason" class="form-control" value="" ></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" id="notes_div">
                                            <label class="control-label" for="notes">Notes </label>
                                            <textarea id="notes" name="notes" rows="4" class="form-control" ><?php if(isset($inquirydata)){ echo $notes; } ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(empty($inquirydata)){ ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default border-panel">
                            <div class="panel-heading"><h2>Followup</h2></div>
                            <div class="panel-body">
                                <div style="padding: 5px;background: #f2f2f2;margin-bottom: 15px;">
                                    <div class="checkbox table-checkbox text-left">
                                        <input id="addnewfollowup" type="checkbox" name="addnewfollowup" checked class="checkradios">
                                        <label for="addnewfollowup" ><h5 class="mt2">Add <?=Followup?></h5></label>
                                    </div>
                                    <div class="row  ml3 mr4">
                                        <div class="col-md-4">
                                            <div class="form-group" id="follow_up_type_div">
                                                <div class="col-md-12 pl-n">
                                                    <label class="control-label" for="follow_up_type"><?=Follow_Up?> Type</label>
                                                    <select class="selectpicker form-control bootstrap_select"  id="follow_up_type" title="Select Follow up Type" name="follow_up_type" data-live-search="true" data-size="5">
                                                        <option value="0">Select Follow up Type</option>
                                                        <?php foreach($followuptypedata as $_v1) { ?>
                                                        <option value="<?php echo $_v1['id'];?>" <?php if(DEFAULT_FOLLOWUP_TYPE!="" && DEFAULT_FOLLOWUP_TYPE==$_v1['id']){ echo 'selected'; } ?>>
                                                            <?php echo $_v1['name'];?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" id="followupdate_div">
                                                <div class="col-md-12 pl-n">
                                                    <label class="control-label" for="followupdate">Followup Date</label>
                                                    <input id="followupdate" type="text" name="followupdate" class="form-control followupdate" value="<?=date("d/m/Y",strtotime('+'.DEFAULT_FOLLOWUP_DATE.' day'))?> <?php echo $this->general_model->gettime("H:i");?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ml3 mr4">
                                        <div class="col-md-4">
                                            <div class="form-group" id="followuplatitude_div">
                                                <div class="col-md-12 pl-n">
                                                    <label class="control-label" for="followuplatitude">Latitude</label>
                                                    <input type="text" class="form-control location_fromgroup" name="followuplatitude" id="followuplatitude" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" id="followuplongitude_div">
                                                <div class="col-md-12 pl-n">
                                                    <label class="control-label" for="followuplongitude">Longitude</label>
                                                    <input type="text" class="form-control location_fromgroup" name="followuplongitude" id="followuplongitude" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button"  onclick="openmodal(2,$('#followuplatitude').val(),$('#followuplongitude').val())" class="form-control" style="width: auto;"><i class="fa fa-map-marker"></i> Pickup Followup Location</button>
                                           </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default border-panel">
                            <div class="panel-heading"><h2>Quotation</h2></div>
                            <div class="panel-body">
                                <div style="padding: 5px;background: #f2f2f2;margin-bottom: 15px;">
                                    <div class="row ml3 mr4">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="col-md-12 pl-n pr-sm">
                                                    <label class="control-label">File</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="col-md-12 pl-sm pr-sm">
                                                    <label class="control-label">Description</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <div class="col-md-12 pl-sm pr-sm">
                                                <label class="control-label">Quotation Date</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if(isset($inquirydata) && !empty($inquiryquotationfile) && isset($inquiryquotationfile)) { ?>
                                        <input type="hidden" name="removequotationfileid" id="removequotationfileid">
                                        <script type="text/javascript">
                                        var quotationfilecount = '<?=count($inquiryquotationfile) ?>';
                                        </script>
                                        <?php for ($i=0; $i < count($inquiryquotationfile); $i++) { ?>
                                            <div id="quotationfilecount<?=$i+1?>">
                                                <input type="hidden" name="quotationfileid<?=$i+1?>"value="<?=$inquiryquotationfile[$i]['id']?>" id="quotationfileid<?=$i+1?>">
                                                <div class="row ml3 mr4">
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="quotationfile<?=$i+1?>_div">
                                                            <div class="col-md-12 pl-n pr-sm">
                                                                <div class="input-group">
                                                                    <span class="input-group-btn" style="padding: 0 12px 0px 0px;">
                                                                        <span class="btn btn-primary btn-raised btn-file">Browse...
                                                                        <input type="file" class="quotationfile" name="quotationfile<?=$i+1?>" id="quotationfile<?=$i+1?>" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf,.doc,.docx" onchange="validquotationfile($(this),'quotationfile<?=$i+1?>',this)">
                                                                    </span>
                                                                    </span>
                                                                    <input type="text" readonly="" id="Filetext<?=$i+1?>" value="<?=$inquiryquotationfile[$i]['file']?>" class="form-control" placeholder="File">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="quotationdescription<?=$i+1?>_div">
                                                            <div class="col-md-12 pl-sm pr-sm">
                                                                <input type="text" id="quotationdescription<?=$i+1?>" name="quotationdescription<?=$i+1?>" value="<?=$inquiryquotationfile[$i]['description']?>" class="form-control" placeholder="Description">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group" id="quotationdate1_div">
                                                            <div class="col-md-12 pl-sm pr-sm">
                                                                <input id="quotationdate<?=$i+1?>" type="text" name="quotationdate<?=$i+1?>" value="<?=(!empty($inquiryquotationfile[$i]['date']) && $inquiryquotationfile[$i]['date']!='0000-00-00')?$this->general_model->displaydate($inquiryquotationfile[$i]['date']):''?>" class="form-control quotationdate" placeholder="Quotation Date" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <?php if($i==0){?>
                                                            <?php if(count($inquiryquotationfile)>1){ ?>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-raised file_remove_btn" id="p<?=$i+1?>"
                                                                    onclick="removequotationfile(<?=$i+1?>)"
                                                                    style="padding: 5px 10px;margin-top: 18px;">
                                                                    <i class="fa fa-minus"></i>
                                                                </button>
                                                            <?php } else { ?>
                                                                <button type="button"
                                                                    class="btn btn-primary btn-raised file_add_btn" id="<?=$i+1?>"
                                                                    onclick="addnewquotationfile()"
                                                                    style="padding: 5px 10px;margin-top: 18px;">
                                                                    <i class="fa fa-plus"></i>
                                                                    
                                                                </button>
                                                            <?php } ?>
                                                        <?php }else if($i!=0) { ?>
                                                            <button type="button"
                                                                class="btn btn-danger btn-raised file_remove_btn" id="p<?=$i+1?>"
                                                                onclick="removequotationfile(<?=$i+1?>)"
                                                                style="padding: 5px 10px;margin-top: 18px;">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        <?php } ?>
                                                        <button type="button"
                                                            class="btn btn-danger btn-raised file_remove_btn" id="p<?=$i+1?>"
                                                            onclick="removequotationfile(<?=$i+1?>)"
                                                            style="padding: 5px 10px;margin-top: 18px;display: none;">
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-primary btn-raised file_add_btn" id="<?=$i+1?>"
                                                            onclick="addnewquotationfile()"
                                                            style="padding: 5px 10px;margin-top: 18px;">
                                                            <i class="fa fa-plus"></i>
                                                            
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <script type="text/javascript">var quotationfilecount = 1;</script>
                                        <div id="quotationfilecount1">
                                            <div class="row ml3 mr4">
                                                <div class="col-md-4">
                                                    <div class="form-group" id="quotationfile1_div">
                                                        <div class="col-md-12 pl-n pr-sm">
                                                            <div class="input-group">
                                                                <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                    <span class="btn btn-primary btn-raised btn-file">Browse...
                                                                    <input type="file" class="quotationfile" name="quotationfile1" id="quotationfile1" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf,.doc,.docx" onchange="validquotationfile($(this),'quotationfile1',this)">
                                                                </span>
                                                                </span>
                                                                <input type="text" readonly="" id="Filetext1" name="Filetext[]" class="form-control" placeholder="File">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group" id="quotationdescription1_div">
                                                        <div class="col-md-12 pl-sm pr-sm">
                                                            <input type="text" id="quotationdescription1" name="quotationdescription1" class="form-control" placeholder="Description">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group" id="quotationdate1_div">
                                                        <div class="col-md-12 pl-sm pr-sm">
                                                            <input id="quotationdate1" type="text" name="quotationdate1" class="form-control quotationdate" placeholder="Quotation Date" value="" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button"
                                                        class="btn btn-danger btn-raised file_remove_btn" id="p1"
                                                        onclick="removequotationfile(1)"
                                                        style="padding: 5px 10px;margin-top: 18px;display: none;">
                                                        <i class="fa fa-minus"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-primary btn-raised file_add_btn" id="1"
                                                        onclick="addnewquotationfile()"
                                                        style="padding: 5px 10px;margin-top: 18px;">
                                                        <i class="fa fa-plus"></i>
                                                        
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div id="quotationfiledata_div"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default border-panel">
                                    <div class="panel-heading"><h2>Actions</h2></div>
                                    <div class="panel-body">
                                        <div class="form-group row">
                                            <label for="focusedinput" class="col-sm-4 control-label"></label>
                                            <div class="col-sm-7">
                                                <?php if(!empty($inquirydata)){ ?>
                                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised text-white" onclick="resetdata()">
                                                <?php }else{ ?>
                                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                                    <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & ADD NEW" class="btn btn-primary btn-raised">
                                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                                <?php } ?>
                                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            </form>
		</div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
   


<script type="text/javascript">
$(document).ready(function() {
    var options = {
        max_value: 5,
        step_size: 0.5,
        update_input_field_name: $("#rating"),
    }
   
    $("#rate").rate(options);
    $('#status').change(function(){
        changestatus();
    });
});
function changestatus(){
    var value = $('#status').val();
    $("#paymentdate").val("");
    if(value==5 && $("#installmentno").is(":checked")==true){
        $('#paymentdate_div').show();
    }else{
        $('#paymentdate_div').hide();
    }
}
</script>

<div class="modal" id="addcontactmodal" tabindex='-1'>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                <h4 class="modal-title">New Contact</h4>
            </div>

            <div class="modal-body pt-n">
                <form class="form-horizontal" id="newcontactform" name="newcontactform" method="post">
                    <input type="hidden" name="countrycode" id="countrycode" value="<?php if(isset($contactdetail[0])){ echo $contactdetail[0]['countrycode']; } ?>">
                    <input type="hidden" name="memberid" id="newcontactmemberid" value="<?php if(isset($memberid) && $memberid!=""){ echo $memberid; } ?>">

                    <div class="row mr4 ml3">
                        <div class="col-md-3">
                            <div class="form-group" id="newcontactfirstname_div">
                                <div class="col-md-12 pl-n">
                                    <label class="control-label" for="newcontactfirstname">First Name </label>
                                    <input type="text" id="newcontactfirstname" name="firstname" class="form-control" onkeypress="return onlyAlphabets(event)">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="newcontactlastname_div">
                                <div class="col-md-12 pl-n">
                                    <label class="control-label" for="newcontactlastname">Last Name </label>
                                    <input type="text" id="newcontactlastname" name="lastname" class="form-control"  onkeypress="return onlyAlphabets(event)">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group" id="newcontactmobile_div">
                                <div class="col-md-12 pl-n">
                                    <label class="control-label" for="newcontactmobileno">Mobile No <span class="mandatoryfield">*</span></label>
                                    <input id="newcontactmobileno" type="text" name="mobileno" class="form-control newcontactmobileno number" maxlength="10"  div-id="1" onkeypress="return isNumber(event)">
                                    <span class="control-label mandatoryfield" id="mobilenoduplicatemessage" div-id="1"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="newcontactemail_div">
                                <div class="col-md-12 pl-n">
                                    <label class="control-label" for="newcontactemail">Email <span class="mandatoryfield"> *</span></label>
                                    <input id="newcontactemail" type="text" name="email" class="form-control newcontactemail"  div-id="1">
                                    <span class="control-label mandatoryfield" id="emailduplicatemessage" div-id="1"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mr4 ml3">
                        <div class="col-md-3">
                            <div class="form-group" id="newcontactdesignation_div">
                                <div class="col-md-12 pl-n">
                                    <label class="control-label" for="newcontactdesignation">Designation </label>
                                    <input type="text" id="newcontactdesignation" name="designation" class="form-control" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="newcontactdepartment_div">
                                <div class="col-md-12 pl-n">
                                    <label class="control-label" for="newcontactdepartment">Department </label>
                                    <input type="text" id="newcontactdepartment" name="department" class="form-control" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="newcontactbirthdate_div">
                                <div class="col-md-12 pl-n">
                                    <label class="control-label" for="newcontactbirthdate">Birth Date </label>
                                    <input id="newcontactbirthdate" type="text" name="birthdate" class="form-control datepicker1"  value="<?php if(isset($expense_data)){ echo $this->general_model->displaydate($expense_data['date']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="newcontactannidate_div">
                                <div class="col-md-12 pl-n">
                                    <label class="control-label" for="newcontactannidate">Anniversary Date </label>
                                    <input id="newcontactannidate" type="text" name="annidate" class="form-control datepicker1"  value="<?php if(isset($expense_data)){ echo $this->general_model->displaydate($expense_data['date']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mr4 ml3 text-center">
                        <input type="button" id="newcontactsubmit" onclick="checknewcontactvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                        <a href="javascript:void(0)" data-dismiss="modal" title="<?=cancellink_title?>" class="<?=cancellink_class?>"><?=cancellink_text?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="duplicatecustomermodal" tabindex='-1'>
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="duplicate_title">Customer With Same Company</h4>
                <button type="button" class="close">&times;</button>
            </div>

            <div class="modal-body">
                <table class="table table-responsive" id="duplicatecustomertable">

                </table>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width: 151%;height: 424px; margin-left: -96px;">
           <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
                <h4 class="modal-title">Select Location</h4>
            </div>
            <div class="modal-body">
              
              <!--   <div id="map"></div> -->
              <div id="map" style="position: relative; overflow: hidden;"></div>
              <input id="pac-input" class="pac-controls" style="z-index: 0;position: absolute;left: 8px;top: 13px;" type="text" placeholder="Search Place">
            </div>
           <div class="modal-footer"></div>
       </div>
   </div>
</div>

<script>
    function openmodal(type,latitude,longitude){
        latitude = latitude || '';
        longitude = longitude || '';
        newLocation(latitude,longitude);
        $('#latlongtype').val(type);
        $('#pac-input').val('rajkot');
        $('#locationModal').modal('show');
    }
    // Initialize and add the map
    var markers = [];
    var map;
    function initAutocomplete() {
        map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: <?=DEFAULT_LAT?>, lng: <?=DEFAULT_LNG?>},
          zoom: 6,
          mapTypeId: 'roadmap',
          disableDefaultUI: true,
          streetViewControl: false,
        });

        // Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
          searchBox.setBounds(map.getBounds());
        });

        google.maps.event.addListener(map, 'click', function(event) {
            deleteMarkers();
            placeMarker(event.latLng);
            var latlongtype = $('#latlongtype').val();
            if(latlongtype==2){
                $('#followuplatitude').val(event.latLng.lat());
                $('#followuplongitude').val(event.latLng.lng());
            }else{
                $('#latitude').val(event.latLng.lat());
                $('#longitude').val(event.latLng.lng());
            }
            
        });

        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
          var places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
          }

          // Clear out the old markers.
          markers.forEach(function(marker) {
            marker.setMap(null);
          });
          markers = [];

          // For each place, get the icon, name and location.
          var bounds = new google.maps.LatLngBounds();
          places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }
            var icon = {
              url: place.icon,
              size: new google.maps.Size(71, 71),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(17, 34),
              scaledSize: new google.maps.Size(25, 25)
            };

            // Create a marker for each place.
            markers.push(new google.maps.Marker({
              map: map,
              icon: icon,
              title: place.name,
              position: place.geometry.location
            }));

            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds);
        });
    }
    function newLocation(newLat,newLng){

        if(newLat!='' && newLng!=''){
            marker = new google.maps.Marker({
                    position: new google.maps.LatLng( newLat,newLng),
                    map: map,
                });
            markers.push(marker);

            // To add the marker to the map, call setMap();
            marker.setMap(map);

            map.setCenter({lat : newLat,lng : newLng});
            map.setZoom(14);
        }else{
            deleteMarkers();
            map.setCenter({lat : <?=DEFAULT_LAT?>,lng : <?=DEFAULT_LNG?>});
            map.setZoom(4);
        }
    }
    function placeMarker(location) {        
        marker = new google.maps.Marker({
            position: location, 
            map: map
        });
        markers.push(marker);
        
    }
    // Sets the map on all markers in the array.
    function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(map);
        }
    }
    // Removes the markers from the map, but keeps them in the array.
    function clearMarkers() {
        setMapOnAll(null);
    }
    // Deletes all markers in the array by removing references to them.
    function deleteMarkers() {
        clearMarkers();
        markers = [];
    }


     
</script> 
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=MAP_KEY?>&libraries=places&callback=initAutocomplete"></script>
      
                                                                         
                                                
                               


                    