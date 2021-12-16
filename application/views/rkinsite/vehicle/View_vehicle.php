<style>
  .dataTables_length .panel-ctrls-center, .dataTables_filter{
    padding: 0px 8px;
  }
</style>
<script>
    var vehicleid = '<?php echo !empty($vehicledata)?$vehicledata['id']:"0"; ?>';
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
        <h1>View <?=$this->session->userdata(base_url().'submenuname')?></h1>    
        <small>
          <ol class="breadcrumb">                        
            <li><a href="<?=base_url(); ?><?=ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li><a href="<?=ADMIN_URL?>vehicle"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
            <li class="active">View <?=$this->session->userdata(base_url().'submenuname')?></li>
          </ol>
        </small>                
    </div>
    <div class="container-fluid">
      <div data-widget-group="group1">
        <div class="row">   
            <div class="col-md-12">
                <div class="panel panel-default border-panel">
                    <div class="panel-heading">
                        <div class="col-md-6 col-sm-6 col-xs-9 p-n pt-xs">
                            <h2 style="font-size: 14px;"><b>Vehicle name : </b><?=$vehicledata['vehiclename']." (".$vehicledata['vehicleno'].")"?></b></h2>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-3 p-n text-right">
                            <a class="<?=editbtn_class;?>" href="<?=ADMIN_URL.'vehicle/edit-vehicle/'.$vehicledata['id']?>" title=<?=editbtn_title?>><?=editbtn_text?></a>
                        </div>
                    </div>
                </div>
            </div>
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-body p-n pt-1">
                <div class="tab-container tab-default mb-n">
                  <ul class="nav nav-tabs " id="myTab" >
                      <li class="dropdown pull-right tabdrop hide active">
                          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-angle-down"></i> </a><ul class="dropdown-menu"></ul>
                      </li>
                      <li class="">
                          <a href="#vehicledetails" data-toggle="tab" aria-expanded="false">Vehicle Details<div class="ripple-container li-line"></div></a>
                      </li>
                      <li class="">
                          <a href="#documenttab" data-toggle="tab" aria-expanded="false">Document<div class="ripple-container li-line"></div></a>
                      </li> 
                      <li class="">
                          <a href="#fueltab" data-toggle="tab" aria-expanded="false">Fuel<div class="ripple-container li-line"></div></a>
                      </li>
                      <li class="" >
                          <a href="#servicetab" data-toggle="tab" aria-expanded="false">Service <div class="ripple-container li-line"></div></a>
                      </li>
                      <li class="" >
                          <a href="#insurancetab" data-toggle="tab" aria-expanded="false">Insurance <div class="ripple-container li-line"></div></a>
                      </li>
                      <li class="" >
                          <a href="#challantab" data-toggle="tab" aria-expanded="false">Challan <div class="ripple-container li-line"></div></a>
                      </li>
                      <li class="" >
                          <a href="#assignedsitetab" data-toggle="tab" aria-expanded="false">Assigned Site <div class="ripple-container li-line"></div></a>
                      </li>
                      <li class="" >
                          <a href="#assignedpartytab" data-toggle="tab" aria-expanded="false">Assigned Party <div class="ripple-container li-line"></div></a>
                      </li>
                      <li class="" >
                          <a href="#insuranceclaimtab" data-toggle="tab" aria-expanded="false">Insurance Claim <div class="ripple-container li-line"></div></a>
                      </li>
                      <li class="" >
                          <a href="#emiremindertab" data-toggle="tab" aria-expanded="false">EMI Reminder <div class="ripple-container li-line"></div></a>
                      </li>
                  </ul>
                  
                  <div class="tab-content">  
                      <div class="tab-pane" id="vehicledetails">
                          <div class="row">
                            <div class="col-md-6 p-n">
                                <?php if(count($vehicledata)>0) { ?>
                                <table class="table table-striped table-bordered table-responsive-sm" width="100%">
                                    <tr>
                                        <th width="35%">Company Name</th>
                                        <td><?=$vehicledata['companyname']?></td>
                                    </tr>
                                    <tr>
                                        <th>Vehicle Name</th>
                                        <td><?=$vehicledata['vehiclename']?></td>
                                    </tr>
                                    <tr>
                                        <th>Vehicle Number</th>
                                        <td><?=$vehicledata['vehicleno']?></td>
                                    </tr>
                                    <tr>
                                        <th>Engine Number</th>
                                        <td><?=$vehicledata['engineno']?></td>
                                    </tr>
                                    <tr>
                                        <th>Chassis Number</th>
                                        <td><?=$vehicledata['chassisno']?></td>
                                    </tr>
                                    <tr>
                                        <th>Owner Name</th>
                                        <td><?='<a href="'.ADMIN_URL.'/party/view-party/'.$vehicledata['ownerid'].'" target="_blank">'.$vehicledata['ownername'].'</a>'?></td>
                                    </tr>
                                    <tr>
                                        <th>Vehicle Type</th>
                                        <td><?=$this->Licencetype[$vehicledata['vehicletype']]?></td>
                                    </tr>
                                    <tr>
                                        <th>Petro Card No.</th>
                                        <td><?=($vehicledata['petrocardno']!=""?$vehicledata['petrocardno']:'-')?></td>
                                    </tr>
                                    <tr>
                                        <th>Buyer</th>
                                        <td><?=($vehicledata['buyername']!=""?'<a href="'.ADMIN_URL.'/party/view-party/'.$vehicledata['buyerid'].'" target="_blank">'.$vehicledata['buyername'].'</a>':'-')?></td>
                                    </tr>
                                    <tr>
                                        <th>Remarks</th>
                                        <td><?=($vehicledata['remarks']!=""?$vehicledata['remarks']:'-')?></td>
                                    </tr>
                                </table>
                                <?php } ?>
                            </div>
                            <div class="col-md-6 p-n">
                                <?php if(count($vehicledata)>0) { ?>
                                <table class="table table-striped table-bordered table-responsive-sm" width="100%">
                                    <tr>
                                        <th>Date of Registration</th>
                                        <td><?=($vehicledata['dateofregistration']!="0000-00-00"?$this->general_model->displaydate($vehicledata['dateofregistration']):"-")?></td>
                                    </tr>
                                    <tr>
                                        <th>Due Date of Registration</th>
                                        <td><?=($vehicledata['duedateofregistration']!="0000-00-00"?$this->general_model->displaydate($vehicledata['duedateofregistration']):"-")?></td>
                                    </tr>
                                    <tr>
                                        <th>Commercial</th>
                                        <td><?=($vehicledata['commercial']==0?"Commercial":"Non Commercial")?></td>
                                    </tr>
                                    <tr>
                                        <th>Fuel Type</th>
                                        <td><?=($vehicledata['fueltype']!=0?$this->Fueltype[$vehicledata['fueltype']]:'-')?></td>
                                    </tr>
                                    <tr>
                                        <th>Starting KM</th>
                                        <td><?=($vehicledata['startingkm']!="0.00"?$vehicledata['startingkm']:'-')?></td>
                                    </tr>
                                    <tr>
                                        <th>Fuel Rate</th>
                                        <td><?=$vehicledata['fuelrate']."/".($vehicledata['fuelratetype']=="1"?"Per KM":'Per Hour')?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td><?=($vehicledata['sold']==0?"Not Sold":"Sold")?></td>
                                    </tr>
                                    <?php if($vehicledata['sold']==1){ ?>
                                    <tr>
                                        <th>Sold Date</th>
                                        <td><?=($vehicledata['solddate']!="0000-00-00"?$this->general_model->displaydate($vehicledata['solddate']):"-")?></td>
                                    </tr>
                                    <tr>
                                        <th>Sold Party Name</th>
                                        <td><?=($vehicledata['soldpartyid']!=0)?'<a href="'.ADMIN_URL.'/party/view-party/'.$vehicledata['soldpartyid'].'" target="_blank">'.$vehicledata['soldpartyname'].'</a>':"-"?></td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <th>Fast Tag Account No.</th>
                                        <td><?=($vehicledata['accountno']!=''?$vehicledata['accountno']:'-')?></td>
                                    </tr>
                                    <tr>
                                        <th>Fast Tag Wallet ID</th>
                                        <td><?=($vehicledata['walletid']!=''?$vehicledata['walletid']:'-')?></td>
                                    </tr>
                                    <tr>
                                        <th>Fast Tag RFID No.</th>
                                        <td><?=($vehicledata['rfidno']!=''?$vehicledata['rfidno']:'-')?></td>
                                    </tr>
                                </table>
                                <?php } ?>
                            </div>  
                        </div>
                      </div>
                      <div class="tab-pane" id="documenttab">
                          <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="panel panel-default mb-n" style="box-shadow: unset !important;">
                                    <div class="panel-heading">
                                        <div class="col-md-6 p-n">
                                            <div class="panel-ctrls document-tbl"></div>
                                        </div>
                                        <div class="col-md-6 form-group" style="text-align: right;">
                                            <?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                                <a class="<?=addbtn_class;?>" onclick="openDocumentModal(0,<?=$vehicledata['id']?>)" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                                            <?php }
                                            if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                                <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>document/check-document-use','Document','<?php echo ADMIN_URL; ?>document/delete-mul-document','documenttable','deletecheckalldocument')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <div class="table-responsive">
                                            <table id="documenttable" class="table table-bordered table-striped table-responsive-sm" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="width5">Sr. No.</th>
                                                        <th>Document Type</th>
                                                        <th>Document Number</th>
                                                        <th>Register Date</th>
                                                        <th>Due Date</th>
                                                        <th>Entry Date</th>
                                                        <th class="width12">Action</th>
                                                        <th class="width5">
                                                            <div class="checkbox">
                                                                <input id="deletecheckalldocument" onchange="allchecked('documenttable','deletecheckalldocument')" type="checkbox" value="all">
                                                                <label for="deletecheckalldocument"></label>
                                                            </div>
                                                        </th>     
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="panel-footer document-tbl"></div>
                                </div>
                                <?php $this->load->view(ADMINFOLDER.'document/Documentmodal');?>
                            </div>
                          </div>
                      </div>
                      <div class="tab-pane" id="fueltab">
                          <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="panel panel-default mb-n" style="box-shadow: unset !important;">
                                    <div class="panel-heading">
                                        <form action="#" id="vehiclefuelform" class="form-horizontal">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-sm-3">
                                                        <div class="form-group" style="margin-top: -7px !important;">
                                                            <div class="col-md-12 pl-n pr-sm">
                                                                <select id="fuelpartyid" name="fuelpartyid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                                <option value="0">All Party</option>
                                                                <?php foreach ($driverdata as $driver) { ?>
                                                                    <option value="<?php echo $driver['id']; ?>"><?php echo $driver['name']; ?></option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group" style="margin-top: -3px !important;">
                                                            <div class="col-md-12 pl-sm pr-sm">
                                                                    <div class="input-group">
                                                                        <div class="input-daterange input-group daterangepicker-filter" id="datepicker-range">
                                                                        <input type="text" class="input-small form-control" name="fuelstartdate" style="text-align: left;" id="fuelstartdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 year"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
                                                                    <span class="input-group-addon">to</span>
                                                                    <div class="input-group">
                                                                        <input type="text" class="input-small form-control" name="fuelenddate" style="text-align: left;" id="fuelenddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 p-n">
                                                        <div class="form-group m-n">
                                                            <div class="col-sm-12">
                                                                <label class="control-label"></label>
                                                                <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter('fuel')" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="col-md-5 p-n">
                                            <div class="panel-ctrls fuel-tbl"></div>
                                        </div>
                                        <div class="col-md-7" style="text-align: right;">
                                        <?php if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                                <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>fuel/check-fuel-use','Fuel','<?php echo ADMIN_URL; ?>fuel/delete-mul-fuel','fueltable','deletecheckallfuel')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <table id="fueltable" class="table table-bordered table-striped table-responsive-sm" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Fuel</th>
                                                    <th>Party</th>
                                                    <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                                                    <th class="text-right">KM / Hr</th>
                                                    <th>Entry Date</th>
                                                    <th class="width15">Action</th>
                                                    <th class="width5">
                                                        <div class="checkbox">
                                                            <input id="deletecheckallfuel" onchange="allchecked('fueltable','deletecheckallfuel')" type="checkbox" value="all">
                                                            <label for="deletecheckallfuel"></label>
                                                        </div>
                                                    </th>   
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="panel-footer fuel-tbl"></div>
                                </div>
                            </div>                                                                                              
                          </div>
                      </div>    
                      <div class="tab-pane" id="servicetab">
                        <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="panel panel-default mb-n" style="box-shadow: unset !important;">
                                    <div class="panel-heading">
                                        <form action="#" id="vehicleserviceform" class="form-horizontal">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-sm-2">
                                                        <div class="form-group" style="margin-top: -7px !important;">
                                                            <div class="col-md-12 pl-n pr-sm">
                                                                <select id="servicegarageid" name="servicegarageid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                                <option value="0">All Garage</option>
                                                                <?php foreach ($garagedata as $garage) { ?>
                                                                    <option value="<?php echo $garage['id']; ?>"><?php echo $garage['name']; ?></option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <div class="form-group" style="margin-top: -7px !important;">
                                                            <div class="col-md-12 pl-sm pr-sm">
                                                                <select id="servicedriverid" name="servicedriverid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                                <option value="0">All Driver</option>
                                                                <?php foreach ($driverdata as $driver) { ?>
                                                                    <option value="<?php echo $driver['id']; ?>"><?php echo $driver['name']; ?></option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <div class="form-group" style="margin-top: -7px !important;">
                                                            <div class="col-md-12 pl-sm pr-sm">
                                                                <select id="servicetypeid" name="servicetypeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                                <option value="0">All Service Type</option>
                                                                <?php foreach ($servicetypedata as $servicetype) { ?>
                                                                    <option value="<?php echo $servicetype['id']; ?>"><?php echo $servicetype['name']; ?></option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group" style="margin-top: -3px !important;">
                                                            <div class="col-md-12 pl-sm pr-sm">
                                                                <div class="input-daterange input-group daterangepicker-filter" id="datepicker-range">
                                                                    <div class="input-group">
                                                                        <input type="text" class="input-small form-control" name="servicestartdate" style="text-align: left;" id="servicestartdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 year"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
                                                                    <span class="input-group-addon">to</span>
                                                                    <div class="input-group">
                                                                        <input type="text" class="input-small form-control" name="serviceenddate" style="text-align: left;" id="serviceenddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-2">
                                                        <div class="form-group" style="margin-top: -3px !important;">
                                                            <div class="col-sm-12">
                                                                <!-- <label class="control-label"></label> -->
                                                                <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter('service')" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="col-md-5 p-n">
                                            <div class="panel-ctrls service-tbl"></div>
                                        </div>
                                        <div class="col-md-7" style="text-align: right;">
                                        <?php if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                                <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>service/check-service-use','Service','<?php echo ADMIN_URL; ?>service/delete-mul-service','servicetable','deletecheckallservice')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <table id="servicetable" class="table table-bordered table-striped table-responsive-sm" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Service Type</th>
                                                    <th>Date</th>
                                                    <th>Driver</th>
                                                    <th>Garage</th>
                                                    <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                                                    <th>Entry Date</th>
                                                    <th class="width15">Action</th>
                                                    <th class="width5">
                                                        <div class="checkbox">
                                                            <input id="deletecheckallservice" onchange="allchecked('servicetable','deletecheckallservice')" type="checkbox" value="all">
                                                            <label for="deletecheckallservice"></label>
                                                        </div>
                                                    </th>   
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="panel-footer service-tbl"></div>
                                </div>
                            </div>                                                                                              
                        </div>                                       
                      </div> 
                      <div class="tab-pane" id="insurancetab">
                          <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="panel panel-default mb-n" style="box-shadow: unset !important;">
                                    <div class="panel-heading">
                                        <form action="#" id="vehicleinsuranceform" class="form-horizontal">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-sm-3">
                                                        <div class="form-group" style="margin-top: -7px !important;">
                                                            <div class="col-md-12 pl-n pr-sm">
                                                                <select id="insurancecompany" name="insurancecompany" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                                <option value="">All Insurance Company</option>
                                                                <?php foreach ($insurancecompanydata as $company) { ?>
                                                                    <option value="<?php echo $company['companyname']; ?>"><?php echo $company['companyname']; ?></option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group" style="margin-top: -3px !important;">
                                                            <div class="col-md-12 pl-sm pr-sm">
                                                                <div class="input-daterange input-group daterangepicker-filter" id="datepicker-range">
                                                                    <div class="input-group">
                                                                    <input type="text" class="input-small form-control" name="insurancestartdate" style="text-align: left;" id="insurancestartdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 year"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
    
                                                                    <span class="input-group-addon">to</span>
                                                                    <div class="input-group">
                                                                    <input type="text" class="input-small form-control" name="insuranceenddate" style="text-align: left;" id="insuranceenddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 p-n">
                                                        <div class="form-group m-n">
                                                            <div class="col-sm-12 pl-n">
                                                                <label class="control-label"></label>
                                                                <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter('insurance')" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="col-md-5 p-n">
                                            <div class="panel-ctrls insurance-tbl"></div>
                                        </div>
                                        <div class="col-md-7" style="text-align: right;">
                                        <?php if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                                <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('','Insurance','<?php echo ADMIN_URL; ?>insurance/delete-mul-insurance','insurancetable','deletecheckallinsurance')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <table id="insurancetable" class="table table-bordered table-striped table-responsive-sm" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Insurance Company</th>
                                                    <th>Register Date</th>
                                                    <th>Due Date</th>
                                                    <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                                                    <th>Entry Date</th>
                                                    <th class="width15">Action</th>
                                                    <th class="width5">
                                                        <div class="checkbox">
                                                            <input id="deletecheckallinsurance" onchange="allchecked('insurancetable','deletecheckallinsurance')" type="checkbox" value="all">
                                                            <label for="deletecheckallinsurance"></label>
                                                        </div>
                                                    </th>   
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="panel-footer insurance-tbl"></div>
                                </div>
                            </div>   
                          </div>
                      </div>
                      <div class="tab-pane" id="challantab">
                        <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="panel panel-default mb-n" style="box-shadow: unset !important;">
                                    <div class="panel-heading">
                                        <form action="#" id="vehiclechallanform" class="form-horizontal">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-sm-3">
                                                        <div class="form-group" style="margin-top: -7px !important;">
                                                            <div class="col-md-12 pl-n pr-sm">
                                                                <select id="challandriverid" name="challandriverid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                                <option value="0">All Driver</option>
                                                                <?php foreach ($driverdata as $driver) { ?>
                                                                    <option value="<?php echo $driver['id']; ?>"><?php echo $driver['name']; ?></option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <div class="form-group" style="margin-top: -7px !important;">
                                                            <div class="col-md-12 pl-sm pr-sm">
                                                                <select id="challantypeid" name="challantypeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                                <option value="0">All Challan Type</option>
                                                                <?php foreach ($challantypedata as $challantype) { ?>
                                                                    <option value="<?php echo $challantype['id']; ?>"><?php echo $challantype['challantype']; ?></option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group" style="margin-top: -3px !important;">
                                                            <div class="col-md-12 pl-sm pr-sm">
                                                                <div class="input-daterange input-group daterangepicker-filter" id="datepicker-range">
                                                                    <div class="input-group">
                                                                    <input type="text" class="input-small form-control" name="challanstartdate" style="text-align: left;" id="challanstartdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 year"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
                                                                    <span class="input-group-addon">to</span>
                                                                    <div class="input-group">
                                                                    <input type="text" class="input-small form-control" name="challanenddate" style="text-align: left;" id="challanenddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-2 p-n">
                                                        <div class="form-group m-n">
                                                            <div class="col-sm-12 pl-n">
                                                                <label class="control-label"></label>
                                                                <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter('challan')" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="col-md-5 p-n">
                                            <div class="panel-ctrls challan-tbl"></div>
                                        </div>
                                        <div class="col-md-7" style="text-align: right;">
                                        <?php if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                                <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>challan/check-challan-use','Challan','<?php echo ADMIN_URL; ?>challan/delete-mul-challan','challantable','deletecheckallchallan')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <table id="challantable" class="table table-bordered table-striped table-responsive-sm" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Challan Type</th>
                                                    <th>Date</th>
                                                    <th>Driver</th>
                                                    <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                                                    <th>Entry Date</th>
                                                    <th class="width15">Action</th>
                                                    <th class="width5">
                                                        <div class="checkbox">
                                                            <input id="deletecheckallchallan" onchange="allchecked('challantable','deletecheckallchallan')" type="checkbox" value="all">
                                                            <label for="deletecheckallchallan"></label>
                                                        </div>
                                                    </th>   
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="panel-footer challan-tbl"></div>
                                </div>
                            </div>                                                                                              
                        </div>
                      </div>
                      <div class="tab-pane" id="assignedsitetab">
                        <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="panel panel-default mb-n" style="box-shadow: unset !important;">
                                    <div class="panel-heading">
                                        <form action="#" id="vehicleassignedsiteform" class="form-horizontal">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-sm-3">
                                                        <div class="form-group" style="margin-top: -7px !important;">
                                                            <div class="col-md-12 pl-n pr-sm">
                                                                <select id="assignedsitecityid" name="assignedsitecityid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                                <option value="0">All City</option>
                                                                <?php foreach ($assignedsitecitydata as $city) { ?>
                                                                    <option value="<?php echo $city['id']; ?>"><?php echo $city['name']; ?></option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group" style="margin-top: -3px !important;">
                                                            <div class="col-md-12 pl-sm pr-sm">
                                                                <div class="input-daterange input-group daterangepicker-filter" id="datepicker-range">
                                                                    <div class="input-group">
                                                                    <input type="text" class="input-small form-control" name="assignedsitestartdate" style="text-align: left;" id="assignedsitestartdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 year"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
    
                                                                    <span class="input-group-addon">to</span>
                                                                    <div class="input-group">
                                                                    <input type="text" class="input-small form-control" name="assignedsiteenddate" style="text-align: left;" id="assignedsiteenddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 p-n">
                                                        <div class="form-group m-n">
                                                            <div class="col-sm-12 pl-n">
                                                                <label class="control-label"></label>
                                                                <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter('assignedsite')" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="col-md-5 p-n">
                                            <div class="panel-ctrls assignedsite-tbl"></div>
                                        </div>
                                        <div class="col-md-7" style="text-align: right;">
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <table id="assignedsitetable" class="table table-bordered table-striped table-responsive-sm" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Site Name</th>
                                                    <th>Site City</th>
                                                    <th>Site State</th>
                                                    <th>Entry Date</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="panel-footer assignedsite-tbl"></div>
                                </div>
                            </div>                                                                                       
                        </div>
                      </div>
                      <div class="tab-pane" id="assignedpartytab">
                        <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="panel panel-default mb-n" style="box-shadow: unset !important;">
                                    <div class="panel-heading">
                                        <form action="#" id="vehicleassignedpartyform" class="form-horizontal">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-sm-3">
                                                        <div class="form-group" style="margin-top: -7px !important;">
                                                            <div class="col-md-12 pl-n pr-sm">
                                                                <select id="assignedpartycityid" name="assignedpartycityid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                                <option value="0">All City</option>
                                                                <?php foreach ($assignedsitecitydata as $city) { ?>
                                                                    <option value="<?php echo $city['id']; ?>"><?php echo $city['name']; ?></option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group" style="margin-top: -3px !important;">
                                                            <div class="col-md-12 pl-sm pr-sm">
                                                                <div class="input-daterange input-group daterangepicker-filter" id="datepicker-range">
                                                                    <div class="input-group">
                                                                        <input type="text" class="input-small form-control" name="assignedpartystartdate" style="text-align: left;" id="assignedpartystartdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 year"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
    
                                                                    <span class="input-group-addon">to</span>
                                                                    <div class="input-group">
                                                                        <input type="text" class="input-small form-control" name="assignedpartyenddate" style="text-align: left;" id="assignedpartyenddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 p-n">
                                                        <div class="form-group m-n">
                                                            <div class="col-sm-12 pl-n">
                                                                <label class="control-label"></label>
                                                                <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter('assignedparty')" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="col-md-5 p-n">
                                            <div class="panel-ctrls assignedparty-tbl"></div>
                                        </div>
                                        <div class="col-md-7" style="text-align: right;">
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <table id="assignedpartytable" class="table table-bordered table-striped table-responsive-sm" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Party Name</th>
                                                    <th>Site City</th>
                                                    <th>Site State</th>
                                                    <th>Entry Date</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="panel-footer assignedparty-tbl"></div>
                                </div>
                            </div>                                                                                                 
                        </div>
                      </div>
                      <div class="tab-pane" id="insuranceclaimtab">
                          <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="panel panel-default mb-n" style="box-shadow: unset !important;">
                                    <div class="panel-heading">
                                        <form action="#" id="vehicleinsuranceclaimform" class="form-horizontal">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-sm-3">
                                                        <div class="form-group" style="margin-top: -7px !important;">
                                                            <div class="col-md-12 pl-n pr-sm">
                                                                <select id="insuranceclaimcompany" name="insuranceclaimcompany" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                                <option value="">All Insurance Company</option>
                                                                <?php foreach ($insurancecompanydata as $company) { ?>
                                                                    <option value="<?php echo $company['companyname']; ?>"><?php echo $company['companyname']; ?></option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group" style="margin-top: -3px !important;">
                                                            <div class="col-md-12 pl-sm pr-sm">
                                                                <div class="input-daterange input-group daterangepicker-filter" id="datepicker-range">
                                                                    <div class="input-group">
                                                                    <input type="text" class="input-small form-control" name="insuranceclaimstartdate" style="text-align: left;" id="insuranceclaimstartdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 year"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
                                                                    <span class="input-group-addon">to</span>
                                                                    <div class="input-group">
                                                                    <input type="text" class="input-small form-control" name="insuranceclaimenddate" style="text-align: left;" id="insuranceclaimenddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 p-n">
                                                        <div class="form-group m-n">
                                                            <div class="col-sm-12 pl-n">
                                                                <label class="control-label"></label>
                                                                <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter('insuranceclaim')" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="col-md-5 p-n">
                                            <div class="panel-ctrls insuranceclaim-tbl"></div>
                                        </div>
                                        <div class="col-md-7" style="text-align: right;">
                                        <?php if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                                <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('','Insurance Claim','<?php echo ADMIN_URL; ?>insurance-claim/delete-mul-insurance-claim','insuranceclaimtable','deletecheckallinsuranceclaim')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <table id="insuranceclaimtable" class="table table-bordered table-striped table-responsive-sm" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Insurance Company</th>
                                                    <th>Policy No.</th>
                                                    <th>Agent Name</th>
                                                    <th>Claim Number</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th>Entry Date</th>
                                                    <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                                                    <th class="width15">Action</th>
                                                    <th class="width5">
                                                        <div class="checkbox">
                                                            <input id="deletecheckallinsuranceclaim" onchange="allchecked('insuranceclaimtable','deletecheckallinsuranceclaim')" type="checkbox" value="all">
                                                            <label for="deletecheckallinsuranceclaim"></label>
                                                        </div>
                                                    </th>   
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="panel-footer insuranceclaim-tbl"></div>
                                </div>
                            </div>  
                          </div>
                      </div>
                      <div class="tab-pane" id="emiremindertab">
                          <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="panel panel-default mb-n" style="box-shadow: unset !important;">
                                    <div class="panel-heading">
                                        <div class="col-md-5 p-n">
                                            <div class="panel-ctrls emireminder-tbl"></div>
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <table id="emiremindertable" class="table table-bordered table-striped table-responsive-sm" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Sr. No.</th>
                                                    <th class="text-right">Loan Amount (<?=CURRENCY_CODE?>)</th>
                                                    <th class="text-right">Installment Amount (<?=CURRENCY_CODE?>) </th> 
                                                    <th>Installment Date</th>
                                                    <th>Days</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(isset($emiremainder) && !empty($emiremainder)){
                                                    $srno=1;
                                                    foreach($emiremainder as $row){ ?>
                                                    <tr>
                                                        <td><?=$srno; ?></td>
                                                        <td class="text-right"><?=$row['totalamount'] ?></td>
                                                        <td class="text-right"><?=$row['amount'] ?></td>
                                                        <td><?=$row['date']!=''?$this->general_model->displaydate($row['date']):'-';?></td>
                                                        <td><?=$row['days'] ?></td>
                                                    
                                                    </tr>
                                                <?php $srno++; } } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="panel-footer emireminder-tbl"></div>
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
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->