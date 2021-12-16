<?php 
    $DOCUMENT_TYPE_DATA = '';
    if(!empty($documenttypedata)){
        foreach($documenttypedata as $documenttype){
            $DOCUMENT_TYPE_DATA .= '<option value="'.$documenttype['id'].'">'.$documenttype['documenttype'].'</option>';
        } 
    }
    $LICENCE_TYPE_DATA = '';
    if(!empty($this->Licencetype)){
        foreach($this->Licencetype as $k=>$val){
            $LICENCE_TYPE_DATA .= '<option value="'.$k.'">'.$val.'</option>';
        } 
    }
?>
<script>
    var DOCUMENT_TYPE_DATA = '<?=$DOCUMENT_TYPE_DATA?>';
    var LICENCE_TYPE_DATA = '<?=$LICENCE_TYPE_DATA?>';

    var countryid = '<?php if(isset($partydata)) { echo $partydata['countryid']; }else { echo '0'; } ?>';
    var provinceid = '<?php if(isset($partydata)) { echo $partydata['provinceid']; }else { echo '0'; } ?>';
    var cityid = '<?php if(isset($partydata)) { echo $partydata['cityid']; }else { echo '0'; } ?>';
</script>
<style>
    .panel-style {
        box-shadow: 0px 1px 6px #333 !important;
        margin-bottom: 20px;
    }
</style>
<div class="page-content">
    <div class="page-heading">
        <h1><?php if (isset($partydata)) { echo 'Edit'; } else { echo 'Add'; } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?= $this->session->userdata(base_url() . 'mainmenuname') ?></a></li>
                <li><a href="<?php echo ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl') ?>"><?= $this->session->userdata(base_url() . 'submenuname') ?></a>
                </li>
                <li class="active"><?php if (isset($partydata)) { echo 'Edit';} else { echo 'Add'; } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">
            <form class="form-horizontal" id="party-form">
                <input id="partyid" type="hidden" name="partyid" class="form-control" value="<?php if(isset($partydata)) { echo $partydata['id']; } ?>">
                <div class="panel panel-default border-panel">
                    <div class="panel-body pt-xs">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" id="firstname_div">
                                    <label for="firstname" class="col-md-4 control-label">First Name <span class="mandatoryfield"> *</span></label>
                                    <div class="col-md-8">
                                        <input id="firstname" type="text" name="firstname" class="form-control" value="<?php if (isset($partydata)) { echo $partydata['firstname']; } ?>" onkeypress="return onlyAlphabets(event)">
                                    </div>
                                </div>
                                <div class="form-group" id="middlename_div">
                                    <label for="middlename" class="col-md-4 control-label">Middle Name <span class="mandatoryfield"> *</span></label>
                                    <div class="col-md-8">
                                        <input id="middlename" type="text" name="middlename" class="form-control" value="<?php if (isset($partydata)) { echo $partydata['middlename']; } ?>" onkeypress="return onlyAlphabets(event)">
                                    </div>
                                </div>
                                <div class="form-group" id="lastname_div">
                                    <label for="lastname" class="col-md-4 control-label">Last Name <span class="mandatoryfield"> *</span></label>
                                    <div class="col-md-8">
                                        <input id="lastname" type="text" name="lastname" class="form-control" value="<?php if (isset($partydata)) { echo $partydata['lastname']; } ?>" onkeypress="return onlyAlphabets(event)">
                                    </div>
                                </div>
                                <div class="form-group" id="partycode_div">
                                    <label for="partycode" class="col-md-4 control-label">Party Code <span class="mandatoryfield"> *</span></label>
                                    <div class="col-md-8">
                                        <div class="col-md-10 col-xs-10 p-n">
                                            <input id="partycode" type="text" name="partycode" value="<?php if(isset($partydata)){ echo $partydata['partycode']; } ?>" class="form-control">
                                        </div>
                                        <div class="col-md-2 col-xs-2 pr-n pt-sm">
                                            <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Generate Party Code" onclick="$('#partycode').val(randString(10))"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" id="birthdate_div">
                                    <label for="birthdate" class="col-md-4 control-label">Birth Date</label>
                                    <div class="col-md-8">
                                        <input id="birthdate" type="text" name="birthdate" class="form-control" value="<?php if (isset($partydata) && $partydata['birthdate']!="0000-00-00") { echo $this->general_model->displaydate($partydata['birthdate']); } ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group" id="anniversarydate_div">
                                    <label for="anniversarydate" class="col-md-4 control-label">Anniversary Date</label>
                                    <div class="col-md-8">
                                        <input id="anniversarydate" type="text" name="anniversarydate" class="form-control" value="<?php if (isset($partydata) && $partydata['anniversarydate']!="0000-00-00") { echo $this->general_model->displaydate($partydata['anniversarydate']); } ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="partytype_div">
                                    <label for="partytypeid" class="col-md-4 control-label">Party Type <span class="mandatoryfield"> *</span></label>
                                    <div class="col-md-8">
                                        <select id="partytypeid" name="partytypeid" class="selectpicker form-control" data-live-search="true" data-size="5">
                                            <option value="0">Select Party Type</option>
                                            <?php foreach ($partytypedata as $partytype) { ?>
                                                <option value="<?php echo $partytype['id']; ?>" <?php if (isset($partydata) && $partydata['partytypeid'] == $partytype['id']) { echo "selected"; } ?>><?php echo $partytype['partytype']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="email_div">
                                    <label for="email" class="col-md-4 control-label">Email <span class="mandatoryfield"> *</span></label>
                                    <div class="col-md-8">
                                        <input id="email" type="text" name="email" class="form-control" value="<?php if (isset($partydata)) { echo $partydata['email']; } ?>">
                                    </div>
                                </div>
                                <div class="form-group" id="contactno1_div">
                                    <label for="contactno1" class="col-md-4 control-label">Contact No. 1 <span class="mandatoryfield"> *</span></label>
                                    <div class="col-md-8">
                                        <input id="contactno1" type="text" name="contactno1" class="form-control" onkeypress="return isNumber(event)" maxlength="10" value="<?php if (isset($partydata)) { echo $partydata['contactno1']; } ?>">
                                    </div>
                                </div>
                                <div class="form-group" id="contactno2_div">
                                    <label for="contactno2" class="col-md-4 control-label">Contact No. 2</label>
                                    <div class="col-md-8">
                                        <input id="contactno2" type="text" name="contactno2" class="form-control" onkeypress="return isNumber(event)" maxlength="10" value="<?php if (isset($partydata)) { echo $partydata['contactno2']; } ?>">
                                    </div>
                                </div>
                                <div class="form-group" id="education_div">
                                    <label for="education" class="col-md-4 control-label">Education</label>
                                    <div class="col-md-8">
                                        <input id="education" type="text" name="education" data-provide="education" value="<?php if (isset($partydata)) { echo $partydata['education']; } ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="focusedinput" class="col-md-4 col-xs-2 control-label">Gender</label>
                                    <div class="col-md-6">
                                        <div class="col-md-4 col-xs-2" style="padding-left: 0px; margin-left: 0px;">
                                            <div class="radio">
                                                <input type="radio" name="gender" id="male" value="0" <?php if (isset($partydata) && $partydata['gender'] == 0) { echo 'checked'; } else { echo 'checked'; } ?>>
                                                <label for="male">Male</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xs-2">
                                            <div class="radio">
                                                <input type="radio" name="gender" id="female" value="1" <?php if (isset($partydata) && $partydata['gender'] == 1) { echo 'checked'; } ?>>
                                                <label for="female">Female</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" id="country_div">
                                    <label for="countryid" class="col-md-4 control-label">Country</label>
                                    <div class="col-md-8">
                                        <select id="countryid" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                            <option value="0">Select Country</option>
                                            <?php foreach ($countrydata as $country) { ?>
                                                <option value="<?php echo $country['id']; ?>" <?php if (isset($partydata) && $partydata['countryid'] == $country['id']) { echo "selected"; } ?>><?php echo $country['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="province_div">
                                    <label for="provinceid" class="col-md-4 control-label">Province</label>
                                    <div class="col-md-8">
                                        <select id="provinceid" name="provinceid" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                            <option value="0">Select Province</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="city_div">
                                    <label for="cityid" class="col-md-4 control-label">City</label>
                                    <div class="col-md-8">
                                        <select id="cityid" name="cityid" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                            <option value="0">Select City</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">  
                                <div class="form-group" id="address_div">
                                    <label for="adderss" class="col-md-4 control-label">Address</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" id="address" name="address"><?php if (isset($partydata)) { echo $partydata['address']; } ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group" id="allowforlogin_div">
                                    <label class="col-md-4 col-xs-5 col-sm-3 control-label p-n"><b><u><span class="allowlogin" style="<?php if(isset($partydata) && $partydata['allowforlogin'] == 0){ echo "display:none;"; } ?>">Fill Below Login Details</span></u></b></label>
                                    <div class="col-md-8 col-xs-7 col-sm-5">
                                        <div class="checkbox">
                                            <input id="allowforlogin" type="checkbox" value="1" name="allowforlogin" class="checkradios" <?php if (isset($partydata) && $partydata['allowforlogin'] == 1) { echo 'checked'; } ?>>
                                            <label for="allowforlogin">Allow for Login</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group allowlogin" id="employeerole_div" style="<?php if(isset($partydata) && $partydata['allowforlogin'] == 0){ echo "display:none;"; } ?>">
                                    <label for="employeerole" class="col-md-4 control-label">Employee Role <span class="mandatoryfield"> *</span></label>
                                    <div class="col-md-8">
                                        <div class="col-md-12 p-n">
                                        <select id="employeerole" name="employeerole" class="selectpicker form-control" data-live-search="true" data-size="5">
                                            <option value="0">Select Employee Role</option>
                                                <?php foreach ($userroledata as $emprole) { ?>
                                                    <option value="<?php echo $emprole['id']; ?>" <?php if (isset($partydata) && $partydata['empolyeeroleid'] == $emprole['id']) { echo "selected"; } ?>><?php echo $emprole['role']; ?>
                                                    </option>
                                                <?php } ?>
                                        </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group allowlogin" id="password_div" style="<?php if(isset($partydata) && $partydata['allowforlogin'] == 0){ echo "display:none;"; } ?>">
                                    <label for="password" class="col-md-4 control-label">Password <span class="mandatoryfield"> *</span></label>
                                    <div class="col-md-8">
                                        <div class="col-md-10 col-xs-10 p-n">
                                            <input id="password" type="text" name="password" value="<?php if(isset($partydata)){ echo $this->general_model->decryptIt($partydata['password']); } ?>" class="form-control">
                                        </div>
                                        <div class="col-md-2 col-xs-2 pr-n pt-sm">
                                            <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Generate Password" onclick="$('#password').val(randomPassword())"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                   
                    </div>
                </div>   
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default border-panel" id="commonpanel">
                            <div class="panel-heading"><h2>Document Detail</h2></div>
                            <div class="panel-body no-padding">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-12 pl-sm pr-sm visible-md visible-lg">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <div class="col-md-12 pl-xs pr-xs">
                                                        <label class="control-label">Document Type <span class="mandatoryfield">*</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <div class="col-md-12 pl-xs pr-xs">
                                                        <label class="control-label" style="text-align: left;">Doc. No. <span class="mandatoryfield">*</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <div class="col-md-12 pl-xs pr-xs">
                                                        <label class="control-label">Register Date</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <div class="col-md-12 pl-xs pr-xs">
                                                        <label class="control-label">Due Date</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <div class="col-md-12 pl-xs pr-xs">
                                                        <label class="control-label">Type</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <div class="col-md-12 pl-xs pr-xs">
                                                        <label class="control-label">Attachment</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <?php if(isset($partydata) && !empty($partydocumentdata)) { ?>
                                            <?php for ($i=0; $i < count($partydocumentdata); $i++) { ?>
                                                <div class="col-md-12 countdocuments pl-sm pr-sm" id="countdocuments<?=($i+1)?>">
                                                    <input type="hidden" name="documentid[<?=$i+1?>]" value="<?=$partydocumentdata[$i]['id']?>" id="documentid<?=$i+1?>">
                                                    <div class="col-md-2 col-sm-4">
                                                        <div class="form-group" id="documenttype<?=($i+1)?>_div">
                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                <select id="documenttypeid<?=($i+1)?>" name="documenttypeid[<?=($i+1)?>]" class="selectpicker form-control documenttypeid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                    <option value="0">Select Document Type</option>
                                                                    <?=$DOCUMENT_TYPE_DATA?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 col-sm-4">
                                                        <div class="form-group" id="documentnumber<?=$i+1?>_div">
                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                <input id="documentnumber<?=$i+1?>" name="documentnumber[<?=$i+1?>]" placeholder="Enter Document Number" class="form-control documentrow documentnumber" value="<?php echo $partydocumentdata[$i]['documentnumber']; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-4">
                                                        <div class="form-group" id="fromdate<?=$i+1?>_div">
                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                <input type="text" id="fromdate<?=$i+1?>" name="fromdate[<?=$i+1?>]" placeholder="Enter Register Date" class="form-control documentrow fromdate" value="<?php if($partydocumentdata[$i]['fromdate']!="0000-00-00") { echo $this->general_model->displaydate($partydocumentdata[$i]['fromdate']); } ?>" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-4">
                                                        <div class="form-group" id="duedate<?=$i+1?>_div">
                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                <input type="text" id="duedate<?=$i+1?>" class="form-control documentrow duedate" placeholder="Enter Due Date" name="duedate[<?=$i+1?>]" value="<?php if($partydocumentdata[$i]['duedate']!="0000-00-00") { echo $this->general_model->displaydate($partydocumentdata[$i]['duedate']); } ?>" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-4">
                                                        <div class="form-group" id="licencetype<?=$i+1?>_div">
                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                <select id="licencetype<?=$i+1?>" name="licencetype[<?=$i+1?>]" class="selectpicker form-control documentrow documenttypeid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                    <option value="0">Select Licence Type</option>
                                                                    <?=$LICENCE_TYPE_DATA?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-4">
                                                        <div class="form-group" id="docfile<?=$i+1?>_div">
                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                <input type="hidden" id="isvaliddocfile<?=$i+1?>" value="<?=($partydocumentdata[$i]['documentfile']!=""?1:0)?>"> 
                                                                <input type="hidden" name="olddocfile[<?=$i+1?>]" id="olddocfile<?=$i+1?>" value="<?php echo $partydocumentdata[$i]['documentfile']; ?>"> 
                                                                <div class="input-group" id="fileupload<?=$i+1?>">
                                                                    <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                        <span class="btn btn-primary btn-raised btn-file"><i
                                                                                class="fa fa-upload"></i>
                                                                            <input type="file" name="docfile<?=$i+1?>"
                                                                                class="docfile" id="docfile<?=$i+1?>"
                                                                                accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),'docfile<?=$i+1?>')">
                                                                        </span>
                                                                    </span>
                                                                    <input type="text" readonly="" id="Filetextdocfile<?=$i+1?>"
                                                                        class="form-control documentrow" placeholder="Enter File" name="Filetextdocfile[<?=$i+1?>]" value="<?php echo $partydocumentdata[$i]['documentfile']; ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 pt-md pr-xs addrowbutton">
                                                        <?php if($i==0){?>
                                                            <?php if(count($partydocumentdata)>1){ ?>
                                                                <button type="button" class="btn btn-danger btn-raised remove_btn" onclick="removeDocument(1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                            <?php }else { ?>
                                                                <button type="button" class="btn btn-primary btn-raised add_btn" onclick="addNewDocument()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                            <?php } ?>
                                                        <?php }else if($i!=0) { ?>
                                                            <button type="button" class="btn btn-danger btn-raised remove_btn" onclick="removeDocument(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                        <?php } ?>
                                                        <button type="button" class="btn btn-danger btn-raised btn-sm remove_btn" onclick="removeDocument(<?=$i+1?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                        <button type="button" class="btn btn-primary btn-raised add_btn" onclick="addNewDocument()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>  
                                                    </div>
                                                    <script type="text/javascript">
                                                        $(document).ready(function() {
                                                            $("#documenttypeid<?=$i+1?>").val(<?=$partydocumentdata[$i]['documenttypeid']?>).selectpicker("refresh");
                                                            $("#licencetype<?=$i+1?>").val(<?=$partydocumentdata[$i]['licencetype']?>).selectpicker("refresh");
                                                        });
                                                    </script>
                                                </div>
                                            <?php } ?>
                                        <?php }else{ ?>
                                        <div class="col-md-12 countdocuments pl-sm pr-sm" id="countdocuments1">
                                            <div class="col-md-2 col-sm-4">
                                                <div class="form-group" id="documenttype1_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <select id="documenttypeid1" name="documenttypeid[1]" class="selectpicker form-control documentrow documenttypeid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                            <option value="0">Select Document Type</option>
                                                            <?=$DOCUMENT_TYPE_DATA?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-sm-4">
                                                <div class="form-group" id="documentnumber1_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <input id="documentnumber1" name="documentnumber[1]" placeholder="Enter Document Number" class="form-control documentrow documentnumber">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4">
                                                <div class="form-group" id="fromdate1_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <input type="text" id="fromdate1" name="fromdate[1]" placeholder="Enter Register Date" class="form-control documentrow fromdate" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4">
                                                <div class="form-group" id="duedate1_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <input type="text" id="duedate1" class="form-control documentrow duedate" placeholder="Enter Due Date" name="duedate[1]" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4">
                                                <div class="form-group" id="licencetype1_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <select id="licencetype1" name="licencetype[1]" class="selectpicker form-control documenttypeid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                            <option value="0">Select Licence Type</option>
                                                            <?=$LICENCE_TYPE_DATA?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4">
                                                <div class="form-group" id="docfile1_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <input type="hidden" id="isvaliddocfile1" value="0"> 
                                                        <input type="hidden" name="olddocfile[1]" id="olddocfile1" value=""> 
                                                        <div class="input-group" id="fileupload1">
                                                            <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                <span class="btn btn-primary btn-raised btn-file"><i
                                                                        class="fa fa-upload"></i>
                                                                    <input type="file" name="docfile1"
                                                                        class="docfile" id="docfile1"
                                                                        accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),'docfile1')">
                                                                </span>
                                                            </span>
                                                            <input type="text" readonly="" id="Filetext1"
                                                                class="form-control documentrow docfile" placeholder="Enter File" name="Filetext[1]" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 pt-md pr-xs addrowbutton" id="button_div">
                                                <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeDocument(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                <button type="button" class="btn btn-primary btn-raised add_btn m-n" onclick="addNewDocument()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="panel panel-default border-panel">
                            <div class="panel-heading"><h2>Actions</h2></div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="form-group text-center">
                                        <div class="col-md-12 col-xs-12">
                                            <?php if (!empty($partydata)) { ?>
                                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                                <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <?php } else { ?>
                                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                                <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
                                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <?php } ?>
                                            <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL.$this->session->userdata(base_url() . 'submenuurl')?>" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>