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
   $cloop=1;
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
         <form class="form-horizontal" id="party-form" enctype="multipart/form-data">
            <input id="partyid" type="hidden" name="partyid" class="form-control" value="<?php if(isset($partydata)) { echo $partydata['id']; } ?>">
            <input id="base_url" type="hidden" value="<?=base_url()?>">
            <div class="panel panel-default border-panel">
               <div class="panel-body pt-xs">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group" id="websitename_div">
                           <label class="col-md-4 col-sm-4 control-label" for="websitename">Website Name<span class="mandatoryfield"></span></label>
                           <div class="col-md-8 col-sm-8">
                              <input type="text" id="websitename" class="form-control" name="websitename" value="<?php if(isset($partydata)){ echo $partydata['websitename']; }  ?>">
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group" id="companyid_div">
                       
                           <label for="companyid" class="col-md-3 control-label">Company <span class="mandatoryfield"> *</span></label>
                           <div class="col-md-8">
                              <select id="companyid" name="companyid" class="selectpicker form-control" data-live-search="true" data-size="5">
                                 <option value="0">Select Company</option>
                                 <?php foreach ($Companydata as $Companyrow) { ?>
                                 <option value="<?php echo $Companyrow['id']; ?>" 
                                    <?php if (isset($partydata) && $partydata['companyid'] == $Companyrow['id']) { echo "selected"; } ?>><?php echo $Companyrow['companyname']; ?>
                                 </option>
                                 <?php } ?>
                              </select>
                           </div>
                           <div class="col-md-1 p-n" style="padding-top: 5px !important;">
                              <a href="javascript:void(0)" onclick="addcountry()" class="btn btn-primary btn-raised p-xs"><i class="material-icons" title="Add Unit">add</i></a>
                           </div>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                     <div class="col-md-6">
                        <div class="form-group" id="gst_div">
                           <label class="col-md-4 col-sm-4 control-label" for="gst">Gst Number<span class="mandatoryfield"></span></label>
                           <div class="col-md-8 col-sm-8">
                              <input type="text" id="gst" class="form-control" name="gst" value="<?php if(isset($partydata)){ echo $partydata['gst']; }  ?>">
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group" id="pan_div">
                           <label class="col-md-3 col-sm-3 control-label" for="pan">Pan Number<span class="mandatoryfield"></span></label>
                           <div class="col-md-9 col-sm-9">
                              <input type="text" id="pan" class="form-control" name="pan" value="<?php if(isset($partydata)){ echo $partydata['pan']; }  ?>">
                           </div>
                        </div>
                     </div>
                     <?php /*
                     <div class="col-md-6">
                        <div class="form-group" id="partycode_div">
                           <label class="col-md-3 col-sm-3 control-label" for="partycode">Party Code<span class="mandatoryfield">*</span></label>
                           <div class="col-md-9 col-sm-9">
                              <input type="text" id="partycode" class="form-control" name="pan" value="<?php if(isset($partydata)){ echo $partydata['partycode']; } ?>">
                           </div>
                        </div>
                     </div>
                     */?>
                     <div class="col-md-6">
                        <div class="form-group" id="partycode_div">
                           <label for="partycode" class="col-md-4 col-sm-4 control-label">Party Code<span class="mandatoryfield"></span></label>
                           <div class="col-md-8">
                                 <input id="partycode" type="text" name="partycode" value="<?php if(isset($partydata)){ echo $partydata['partycode']; } ?>" class="form-control">
                            
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group" id="partytypeid_div">
                           <label for="partytypeid" class="col-md-3 control-label">Party Type <span class="mandatoryfield"> *</span></label>
                           <div class="col-md-8">
                              <select id="partytypeid" name="partytypeid" class="selectpicker form-control" data-live-search="true" data-size="5">
                                 <option value="0">Select Party Type</option>
                                 <?php foreach ($partytypedata as $partytype) { ?>
                                 <option value="<?php echo $partytype['id']; ?>" <?php if (isset($partydata) && $partydata['partytypeid'] == $partytype['id']) { echo "selected"; } ?>><?php echo $partytype['partytype']; ?>
                                 </option>
                                 <?php } ?>
                              </select>
                           </div>
                           <div class="col-md-1 p-n" style="padding-top: 5px !important;">
                              <a href="javascript:void(0)" onclick="addpartytype()" class="btn btn-primary btn-raised p-xs"><i class="material-icons" title="Add Unit">add</i></a>
                           </div>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                     <div class="col-md-6">
                        <div class="form-group" id="country_div">
                           <label for="countryid" class="col-md-4 control-label">Country</label>
                           <div class="col-md-8">
                              <select id="countryid" name="countryid" class="countryid selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                 <option value="0">Select Country</option>
                                 <?php foreach ($countrydata as $country) { ?>
                                 <option value="<?php echo $country['id']; ?>"><?php echo $country['name']; ?>
                                 </option>
                                 <?php } ?>
                              </select>
                           </div>
                        </div>
                     </div>
                     
                     <div class="col-md-6">
                        <input type="checkbox" value="1" class="" style="margin-left:22px;" id="checkbox4" /> <label> Is Login</label>
                        
                     </div>

                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        
                        <div class="form-group" id="state_div">
                           <label for="stateid" class="col-md-4 control-label">State</label>
                           <div class="col-md-8">
                              <select id="stateid" name="stateid" class="stateidu selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                 <option value="0">Select State</option>
                              </select>
                           </div>
                        </div>
                        <div class="form-group" id="cityid_div">
                           <label for="cityid" class="col-md-4 control-label">City</label>
                           <div class="col-md-8">
                              <select id="cityid" name="cityid" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                 <option value="0">Select City</option>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group" id="password_div">
                           <label for="password" class="col-md-3 control-label">Password</label>
                           <div class="col-md-9">
                           <input id="password" type="text" name="password" class="form-control" tabindex="7" value="<?php if(isset($partydata)){ echo $this->general_model->decryptIt($partydata['password']); } ?>">
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group" id="billingaddress_div">
                     <label for="billingaddress" class="col-md-4 control-label">Billing Address</label>
                     <div class="col-md-8">
                        <textarea class="form-control" id="billingaddress" name="billingaddress"><?php if (isset($partydata)) { echo $partydata['billingaddress']; } ?></textarea>
                     </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group" id="shippingaddress_div">
                     <label for="shippingaddress" class="col-md-4 control-label">Shipping Address</label>
                     <div class="col-md-8">
                        <textarea class="form-control" id="shippingaddress" name="shippingaddress"><?php if (isset($partydata)) { echo $partydata['shippingaddress']; } ?></textarea>
                     </div>
                     <div class="col-md-12">
                        <input type="checkbox" class="" style="margin-left:22px;" id="checkbox1" /> Same As Billing Address
                     </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group" id="courieraddress_div">
                     <label for="courieraddress" class="col-md-4 control-label">Courier Address</label>
                     <div class="col-md-8">
                        <textarea class="form-control" id="courieraddress" name="courieraddress"><?php if (isset($partydata)) { echo $partydata['courieraddress']; } ?></textarea>
                     </div>
                     <div class="col-md-12">
                        <input type="checkbox" class="" style="margin-left:22px;" id="checkbox2" /> Same As Billing Address
                     </div>
                     <div class="col-md-12 mt-5">
                        <input type="checkbox" class="" style="margin-left:22px;" id="checkbox3" /> Same As Shipping Address
                     </div>

                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
                  <div class="panel panel-default border-panel" id="commonpanel">
                     <div class="panel-heading">
                        <h2>Document Detail</h2>
                     </div>
                     <div class="panel-body no-padding">
                        <div class="row" id="adddocrow">
                           <div class="col-md-12">
                              <div class="col-md-6 pl-sm pr-sm visible-md visible-lg">
                                 <div class="col-md-5">
                                    <div class="form-group">
                                       <div class="col-md-12 pl-xs pr-xs">
                                          <label class="control-label" style="text-align: left;">Document Name <span class="mandatoryfield">*</span></label>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-md-5">
                                    <div class="form-group">
                                       <div class="col-md-12 pl-xs pr-xs">
                                          <label class="control-label">File</label>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12">
                              <?php 
                                 $cloopdoc = 0;
                                 $doc_id='';
                                 $doc='';
                                 $docname='';
                                 $i=0;
                                
                                 if(isset($party_docdata[0]->id ) && !empty($party_docdata[0]->id ))  {
                                     foreach ($party_docdata as $row)
                                    {
                                       $i++;
                                       $cloopdoc = $cloopdoc + 1;
                                       $doc_id = $row->id;
                                       $doc=$row->doc;
                                       $docname = $row->docname;
                                    ?>
                          

                     <input type="hidden" name="doc_id_<?=$cloopdoc?>" value="<?=$doc_id?>" id="doc_id_<?=$cloopdoc?>">
                     <div class="col-md-12">
                        <div class="col-sm-12 countdocuments pl-sm pr-sm" id="countdocuments<?=$cloopdoc?>">
                      
                      <div class="col-md-5 col-sm-5">
                          <div class="form-group" id="documentnumber_<?=$cloopdoc?>">
                              <div class="col-sm-12 pr-xs pl-xs">
                                  <input id="documentname_<?=$cloopdoc?>" value="<?=$docname?>" name="documentname_<?=$cloopdoc?>" placeholder="Enter Document Name" class="form-control documentnumber">
                              </div>
                          </div>
                      </div>
                      <div class="col-md-5 col-sm-5">
                          <div class="form-group" id="docfile<?=$cloopdoc?>">
                              <div class="col-sm-12 pr-xs pl-xs">
                                  <input type="hidden" id="isvaliddocfile<?=$cloopdoc?>" value="0">
                                  <input type="hidden" name="olddocfile_<?=$cloopdoc?>" id="olddocfile<?=$cloopdoc?>" value="">
                                  <div class="input-group" id="fileupload<?=$cloopdoc?>">
                                      <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                          <span class="btn btn-primary btn-raised btn-file">
                                          <i class="fa fa-upload"></i>
                                              <input type="file" name="olddocfile_<?=$cloopdoc?>" class="docfile" id="olddocfile_<?=$cloopdoc?>" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),&apos;docfile<?=$cloopdoc?>&apos;)">
                                          </span>
                                      </span>
                                      <input type="text" readonly="" placeholder="Enter File" id="Filetextdocfile<?=$cloopdoc?>" class="form-control docfile" name="Filetextdocfile_<?=$cloopdoc?>" value="<?=$doc?>">
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-1 addrowbutton pt-md pr-xs">
                          <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeDocument(<?=$cloopdoc?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                      </div>
                      </div>
                  </div>

                              <?php
                                 }
                                 }else {
                                     $count = 1;
                                     $cloopdoc = 0;
                                     while ($count > $cloopdoc) {
                                         $cloopdoc = $cloopdoc + 1;
                                 ?>
                              <div class="col-md-6 countdocuments pl-sm pr-sm" id="countdocuments">
                                 <div class="col-md-5 col-sm-5">
                                     <div class="form-group" id="documentnumber1_div">
                                         <div class="col-md-12 pr-xs pl-xs">
                                             <input id="documentnumber_<?=$cloopdoc?>" name="documentname_<?=$cloopdoc?>" placeholder="Enter Document Number" class="form-control documentrow documentnumber">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-5 col-sm-5">
                                     <div class="form-group" id="docfile1_div">
                                         <div class="col-md-12 pr-xs pl-xs">
                                             <input type="hidden" id="isvaliddocfile1" value="0"> 
                                             <input type="hidden" name="olddocfile_<?=$cloopdoc?>" id="olddocfile1" value=""> 
                                             <div class="input-group" id="fileupload1">
                                                 <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                     <span class="btn btn-primary btn-raised btn-file"><i
                                                             class="fa fa-upload"></i>
                                                         <input type="file" name="docfile_<?=$cloopdoc?>"
                                                             class="docfile" id="docfile1"
                                                             accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),'docfile1')">
                                                     </span>
                                                 </span>
                                                 <input type="text" readonly="" id="Filetext_<?=$cloopdoc?>"
                                                     class="form-control documentrow docfile" placeholder="Enter File" name="Filetextdocfile" value="">
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                
                                 </div>
                              <?php
                                 }
                                 } 
                                 ?>
                           </div>
                        </div>
                        <div class="form-group" style="float:left; margin:0px 50px 20px 20px;">
                            <button type="button"  onclick="addnewproduct()" class="addprodocitem btn-primary"><i class="fa fa-plus"></i></button>
                        </div>
                              <input type="hidden" name="cloopdoc" id="cloopdoc" value="<?php echo $cloopdoc; ?>">
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
                  <div class="panel panel-default border-panel" id="conect_countdocuments1">
                     <div class="panel-heading">
                        <h2>Contact Detail</h2>
                     </div>
                     <div class="panel-body">
                        <div id="addtarget">
                           <div class="row">
                           
                           <?php 
                              $cloopcount = 0;
                              $i=0;
                              if(isset($party_contactdata[0]->id ) && !empty($party_contactdata[0]->id ))  {
                                  foreach ($party_contactdata as $row)
                                  {
                                      $i++;
                                      $cloopcount = $cloopcount + 1;
                                      $con_id = $row->id;
                                      $firstname = $row->firstname;
                                      $lastname = $row->lastname;
                                      $contactno = $row->contactno;
                                      $birthdate = $row->birthdate;
                                      $anniversarydate = $row->anniversarydate;
                                      $email = $row->email;
                                    
                                      ?>
                                       <input type="hidden" name="contectid_<?=$cloopcount?>" value="<?=$con_id?>" id="contectid_<?=$cloopcount?>">
                                       <div class="data" id="contectrowdelete_<?=$cloopcount?>">
                                       
                                       <button type="button" style="float:right; margin:10px 19px 0px 0px;" onclick="removecontectpaertion('contectrowdelete_<?=$cloopcount?>')" class="btn-danger">Remove</button>
                                      <div class="clearfix"></div>
                          
                           
                                    <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                                       <div class="form-group" id="firstname_div">
                                          <label for="firstname" class="col-md-4 control-label">First Name <span class="mandatoryfield"> *</span></label>
                                          <div class="col-md-7">
                                             <input id="firstname" type="text" name="firstname_<?=$cloopcount?>" class="form-control" value="<?=$firstname?>">
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                                       <div class="form-group" id="lastname_div">
                                          <label for="lastname" class="col-md-4 control-label">Last Name <span class="mandatoryfield"> *</span></label>
                                          <div class="col-md-7">
                                             <input id="lastname" type="text" name="lastname_<?=$cloopcount?>" class="form-control" value="<?php if (isset($partydata)) { echo $lastname; } ?>">
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                                       <div class="form-group" id="contactno_div">
                                          <label for="contactno" class="col-md-4 control-label">Contact No <span class="mandatoryfield"> *</span></label>
                                          <div class="col-md-7">
                                             <input id="contactno" type="text" name="contactno_<?=$cloopcount?>" class="form-control"  value="<?php if (isset($partydata)) { echo $contactno; } ?>">
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                                       <div class="form-group" id="birthdate_div">
                                          <label for="birthdate" class="col-md-4 control-label">Birth Date</label>
                                          <div class="col-md-7">
                                             <input id="birthdate" type="text" name="birthdate_<?=$cloopcount?>" class="form-control" value="<?php if (isset($birthdate) && $birthdate!="0000-00-00") { echo $this->general_model->displaydate($birthdate); } ?>" readonly>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                                       <div class="form-group" id="anniversarydate_div">
                                          <label for="anniversarydate" class="col-md-4 control-label">Anniversary Date</label>
                                          <div class="col-md-7">
                                             <input id="anniversarydate" type="text" name="anniversarydate_<?=$cloopcount?>" class="form-control" value="<?php if (isset($anniversarydate) && $anniversarydate!="0000-00-00") { echo $this->general_model->displaydate($anniversarydate); } ?>" readonly>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                                       <div class="form-group" id="email_div">
                                          <label for="email" class="col-md-4 control-label">Email <span class="mandatoryfield">*</span></label>
                                          <div class="col-md-7">
                                             <input id="email" type="text" name="email_<?=$cloopcount?>" class="form-control" value="<?php if (isset($email)) { echo $email; } ?>">
                                          </div>
                                       </div>
                                    </div>
                           
                                  </div>
                        
                      
                        <div class="clearfix" style="margin-bottom: 30px;"></div>
                       
                        <div class="clearfix"></div>
                        <?php
                           }
                           }else {
                               $count = 1;
                               $cloopcount = 0;
                               while ($count > $cloopcount) {
                                   $cloopcount = $cloopcount + 1;
                           ?>
                        <input type="hidden" name="contectid_<?=$cloopcount?>" value="" id="contectid_<?=$cloopcount?>">
                        <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                           <div class="form-group" id="firstname_div">
                              <label for="firstname" class="col-md-4 control-label">First Name <span class="mandatoryfield"> *</span></label>
                              <div class="col-md-7">
                                 <input id="firstname" type="text" name="firstname_<?=$cloopcount?>" class="form-control" value="">
                              </div>
                           </div>
                        </div>
                        <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                           <div class="form-group" id="lastname_div">
                              <label for="lastname" class="col-md-4 control-label">Last Name <span class="mandatoryfield"> *</span></label>
                              <div class="col-md-7">
                                 <input id="lastname" type="text" name="lastname_<?=$cloopcount?>" class="form-control" value="">
                              </div>
                           </div>
                        </div>
                        <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                           <div class="form-group" id="contactno_div">
                              <label for="contactno" class="col-md-4 control-label">Contact No <span class="mandatoryfield"> *</span></label>
                              <div class="col-md-7">
                                 <input id="contactno" type="text" name="contactno_<?=$cloopcount?>" class="form-control"  value="">
                              </div>
                           </div>
                        </div>
                        <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                           <div class="form-group" id="birthdate_div">
                              <label for="birthdate" class="col-md-4 control-label">Birth Date</label>
                              <div class="col-md-7">
                                 <input id="birthdate" type="text" name="birthdate_<?=$cloopcount?>" class="form-control" value="" readonly>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                           <div class="form-group" id="anniversarydate_div">
                              <label for="anniversarydate" class="col-md-4 control-label">Anniversary Date</label>
                              <div class="col-md-7">
                                 <input id="anniversarydate" type="text" name="anniversarydate_<?=$cloopcount?>" class="form-control" value="" readonly>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-4 pl-sm pr-sm visible-md visible-lg">
                           <div class="form-group" id="email_div">
                              <label for="email" class="col-md-4 control-label">Email <span class="mandatoryfield">*</span></label>
                              <div class="col-md-7">
                                 <input id="email" type="text" name="email_<?=$cloopcount?>" class="form-control" value="">
                              </div>
                           </div>
                        </div>
                        
                     </div>
                     </div>
                     
                    
                     <div class="clearfix"></div>
                     <?php
                        }
                        } 
                        ?>
                        <input type="hidden" name="cloopcount" id="cloopcount" value="<?php echo $cloopcount; ?>">
                     
                  </div>
                  </div>
                   
                        <div class="form-group" style="float:left; margin:0px 50px 20px 50px;">
                           <button type="button" class="addpro btn-primary">Add
                           Data</button>
                        </div>
                  </div>
               </div>
            </div>
      <div class="row">
   <div class="col-md-12">
      <div class="panel panel-default border-panel" id="commonpanel">
         <div class="panel-heading">
            <h2>Balance Details</h2>
         </div>
         <div class="panel-body no-padding">
            <div class="row" style="padding: 10px;">
               <div class="col-md-6">
                  <div class="form-group" id="openingdate_div">
                     <label for="openingdate" class="col-md-5 control-label">Opening Balance Date<span class="mandatoryfield"> *</span></label>
                     <div class="col-md-7">
                        <input id="openingdate" type="text" name="openingdate" class="form-control" value="<?php if (isset($partydata)) { echo $partydata['openingdate']; } ?>" onkeypress="return onlyAlphabets(event)">
                     </div>
                  </div>
               </div>
               <div class="col-md-6 mr-5">
                  <div class="form-group" id="openingamount_div">
                     <label for="openingamount" class="col-md-5 control-label">Opening Balance Amount<span class="mandatoryfield"> *</span></label>
                     <div class="col-md-7">
                        <input id="openingamount" type="text" name="openingamount" class="form-control" value="<?php if (isset($partydata)) { echo $partydata['openingamount']; } ?>">
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-md-12 col-xs-12">
      <div class="panel panel-default border-panel">
         <div class="panel-heading">
            <h2>Actions</h2>
         </div>
         <div class="panel-body">
            <div class="row">
               <div class="form-group text-center">
                  <div class="col-md-12 col-xs-12">
                     <?php if (!empty($partydata)) { ?>
                     <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                     <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                     <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                     <?php } else { ?>
                     <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                     <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
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
<input type="hidden" name="edit_country" id="edit_country" value="<?php if (isset($partydata)) { echo $partydata['countryid']; } ?>">
<input type="hidden" name="edit_provinceid" id="edit_provinceid" value="<?php if (isset($partydata)) { echo $partydata['provinceid']; } ?>">
<input type="hidden" name="edit_cityid" id="edit_cityid" value="<?php if (isset($partydata)) { echo $partydata['cityid']; } ?>">
</form>
</div>
</div>

<!-- model code -->
      <div class="modal addunit" id="addcompanyModal" style="overflow-y: auto;">
          <div class="modal-dialog" role="document" style="width: 600px;">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
                      <h4 class="modal-title" id="post_title">Add Company</h4>
                  </div>
                  <div class="modal-body no-padding"></div>
              </div>
          </div>
      </div>
<!-- model code -->
<!-- model code -->
      <div class="modal addunit" id="addpartytypeModal" style="overflow-y: auto;">
          <div class="modal-dialog" role="document" style="width: 600px;">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
                      <h4 class="modal-title" id="post_title">Add Party Type</h4>
                  </div>
                  <div class="modal-body2 no-padding"></div>
              </div>
          </div>
      </div>
<!-- model code -->
</div>
<script>
   // $(".addpro").click(function() {
   
   //     var count1 = $('#cloopcount').val();
   //     count1++;
   //     $('#cloopcount').val(count1);
   //     // CentreStock/cloop
   //     $.get('<?php //echo base_url('rkinsite/Party/cloop/')?>' + count1, null, function(result) {
   //         $("#addtarget").append(result); // Or whatever you need to insert the result
   //     }, 'html');
   
   // });
   // $(".addprodocitem").click(function() {
   //     var count2 = $('#cloopdoc').val();
   //     count2++;
   //     $('#cloopdoc').val(count2);
   //     $.get('<?php //echo base_url('rkinsite/Party/addprodocitem/')?>' + count2, null, function(result) {
   //         $("#adddocrow").append(result); 
   //     }, 'html');
   // });

   function addcountry() {
      var uurl = SITE_URL + "Company/addcompanymodal";
      $.ajax({
         url: uurl,
         type: 'POST',
         //async: false,
         beforeSend: function() {
            $('.mask').show();
            $('#loader').show();
         },
         success: function(response) {
            $("#addcompanyModal").modal("show");
            $(".modal-body").html(response);
            include('<?=ADMIN_JS_URL?>pages/Add_company.js', function() {
            });
         },
         error: function(xhr) {
            //alert(xhr.responseText);
         },
         complete: function() {
            $('.mask').hide();
            $('#loader').hide();
         },

      });
   }
   function addpartytype() {
      var uurl = SITE_URL + "Party_type/addpartytypemodal";
      $.ajax({
         url: uurl,
         type: 'POST',
         //async: false,
         beforeSend: function() {
            $('.mask').show();
            $('#loader').show();
         },
         success: function(response) {
            $("#addpartytypeModal").modal("show");
            $(".modal-body2").html(response);
            include('<?=ADMIN_JS_URL?>pages/add_party_type.js', function() {
            });
         },
         error: function(xhr) {
            //alert(xhr.responseText);
         },
         complete: function() {
            $('.mask').hide();
            $('#loader').hide();
         },

      });
   }
   $(document).ready(function() {
      $('#checkbox1').on('change', function() {
         var checked = this.checked
         
         if(checked==true){
            var billingaddress = $('#billingaddress').val();
            var shippingaddress = $('#shippingaddress').val(billingaddress);
           
         }else if(checked==false){
            $('#shippingaddress').val('');
         }
         
      });
      $('#checkbox2').on('change', function() {
         var checked = this.checked
         
         if(checked==true){
            var billingaddress = $('#billingaddress').val();
            $('#courieraddress').val(billingaddress);
         }else if(checked==false){
            $('#courieraddress').val('');
         }
         
      });
      $('#checkbox3').on('change', function() {
         var checked = this.checked
         if(checked==true){
            var shippingaddress = $('#shippingaddress').val();
            $('#courieraddress').val(shippingaddress);
         }else if(checked==false){
            $('#courieraddress').val('');
         }
      });

      $("#password_div").hide();
      $('#checkbox4').on('change', function() {
         var checked = this.checked
         if(checked==true){
            $("#password_div").show();
         }else if(checked==false){
            $("#password_div").hide();
         }
      });

      $('#checkbox3').on('change', function() {
         var checked = this.checked
         if(checked==true){
            $( "#checkbox2" ).prop( "checked", false );
            $( "#checkbox3" ).prop( "checked", true );
         }else if(checked==false){
            $( "#checkbox3" ).prop( "checked", false );
            
         }
      });
      $('#checkbox2').on('change', function() {
         var checked = this.checked
         if(checked==true){
            $( "#checkbox3" ).prop( "checked", false );
         }
      });


     
   });

</script>

