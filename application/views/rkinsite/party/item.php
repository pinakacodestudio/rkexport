<div class="data" id="contectrowdelete_<?=$id?>">
    <div class="clearfix"></div>
    <div class="panel-heading" >
        <h2>Contect Detail </h2>
                <button type="button" style="float:right; margin:10px 19px 0px 0px;" onclick="removecontectpaertion('contectrowdelete_<?=$id?>')" class="addprodocitem btn-danger">Remove</button>
    </div>   
    <div class="row">
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
            <input type="hidden" name="contectid_<?=$id?>" value="0" id="contectid_<?=$id?>">
                <div class="form-group" id="firstname_div">
                    <label for="firstname" class="col-md-4 control-label">First Name <span class="mandatoryfield"> *</span></label>
                    <div class="col-md-7">
                        <input id="firstname" type="text" name="firstname_<?=$id?>" class="form-control" value="<?php if (isset($partydata)) {echo $partydata['firstname'];}?>" onkeypress="return onlyAlphabets(event)">
                    </div>
                </div>
            </div>
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                <div class="form-group" id="lastname_div">
                    <label for="lastname" class="col-md-4 control-label">Last Name <span class="mandatoryfield"> *</span></label>
                    <div class="col-md-7">
                        <input id="lastname" type="text" name="lastname_<?=$id?>" class="form-control" value="<?php if (isset($partydata)) {echo $partydata['lastname'];}?>" onkeypress="return onlyAlphabets(event)">
                    </div>
                </div>
            </div>
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                <div class="form-group" id="contactno_div">
                    <label for="contactno" class="col-md-4 control-label">Contact No <span class="mandatoryfield"> *</span></label>
                    <div class="col-md-7">
                        <input id="contactno" type="text" name="contactno_<?=$id?>" class="form-control" onkeypress="return isNumber(event)" maxlength="10" value="<?php if (isset($partydata)) {echo $partydata['contactno'];}?>">
                    </div>
                </div>
            </div>
    
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                <div class="form-group" id="birthdate_div">
                    <label for="birthdate" class="col-md-4 control-label">Birth Date</label>
                    <div class="col-md-7">
                        <input id="birthdate" type="text" name="birthdate_<?=$id?>" class="form-control date" value="<?php if (isset($partydata) && $partydata['birthdate'] != "0000-00-00") {echo $this->general_model->displaydate($partydata['birthdate']);}?>" readonly>
                    </div>
                </div>
            </div>
    
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                <div class="form-group" id="anniversarydate_div">
                    <label for="anniversarydate" class="col-md-4 control-label">Anniversary Date</label>
                    <div class="col-md-7">
                        <input id="anniversarydate" type="text" name="anniversarydate_<?=$id?>" class="form-control date" value="<?php if (isset($partydata) && $partydata['anniversarydate'] != "0000-00-00") {echo $this->general_model->displaydate($partydata['anniversarydate']);}?>" readonly>
                    </div>
                </div>
            </div>
    
            <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                <div class="form-group" id="email_div">
                    <label for="email" class="col-md-4 control-label">Email <span class="mandatoryfield">*</span></label>
                    <div class="col-md-7">
                        <input id="email" type="text" name="email_<?=$id?>" class="form-control" value="<?php if (isset($partydata)) {echo $partydata['email'];}?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>