<script>
  var size = '<?= $size ?>';
  var STORAGESPACE = '<?= STORAGESPACE ?>';
</script>
<div class="page-content">
    <div class="page-heading">     
      <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>
  <div class="container-fluid">
    <div class="row">
      <?php if (in_array("total-vehicle",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
      <a href="<?= ADMIN_URL ?>vehicle">
        <div class="col-lg-3 col-md-4 col-sm-4">
          <div class="info-tile info-tile-alt tile-warning" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);">
            <div class="stats">
              <div class="tile-content">
                <span class="material-icons tile-icon">directions_bus</span>
              </div>
            </div>
            <div class="info">
              <div class="tile-heading">
                <span>Total Vehicle</span>
              </div>
              <div class="tile-body "><span id="totalvehicle"><?= $vehiclecount ?></span></div>
            </div>
          </div>
        </div>
      </a>
      <?php } if (in_array("total-owner",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
      <a href="<?= ADMIN_URL ?>party">
        <div class="col-lg-3 col-md-4 col-sm-4">
          <div class="info-tile info-tile-alt tile-primary" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);">
            <div class="stats">
              <div class="tile-content">
                <span class="material-icons tile-icon">account_circle</span>
              </div>
            </div>
            <div class="info">
              <div class="tile-heading">
                <span>Total Owner</span>
              </div>
              <div class="tile-body "><span id="totalowner"><?=$ownercount; ?></span></div>
            </div>
          </div>
        </div>
      </a>
      <?php } if (in_array("total-drivers",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
      <a href="<?= ADMIN_URL ?>party">
        <div class="col-lg-3 col-md-4 col-sm-4">
          <div class="info-tile info-tile-alt tile-indigo" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);">
            <div class="stats">
              <div class="tile-content">
                <span class="material-icons tile-icon">drive_eta</span>
              </div>
            </div>
            <div class="info">
              <div class="tile-heading">
                <span>Total Drivers</span>
              </div>
              <div class="tile-body "><span id="totaldriver"><?=$drivercount; ?></span></div>
            </div>
          </div>
        </div>
      </a>
      <?php } if (in_array("total-garage",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
      <a href="<?= ADMIN_URL ?>party">
        <div class="col-lg-3 col-md-4 col-sm-4">
          <div class="info-tile info-tile-alt tile-success" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);">
            <div class="stats">
              <div class="tile-content">
                <span class="material-icons tile-icon">build</span>
              </div>
            </div>
            <div class="info">
              <div class="tile-heading">
                <span>Total Garage</span>
              </div>
              <div class="tile-body "><span id="totalgarage"><?= $garagecount; ?></span></div>
            </div>
          </div>
        </div>
      </a>
      <?php } if (in_array("total-site",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
      <a href="<?= ADMIN_URL ?>site">
        <div class="col-lg-3 col-md-4 col-sm-4">
          <div class="info-tile info-tile-alt tile-teal" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);">
            <div class="stats">
              <div class="tile-content">
                <span class="material-icons tile-icon">location_city</span>
              </div>
            </div>
            <div class="info">
              <div class="tile-heading">
                <span>Total Site</span>
              </div>
              <div class="tile-body "><span id="totalsite"><?= $sitecount ?></span></div>
            </div>
          </div>
        </div>
      </a>
      <?php } if (in_array("total-alert-service-parts",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
      <a href="<?= ADMIN_URL ?>alert-report">
        <div class="col-lg-3 col-md-4 col-sm-4">
          <div class="info-tile info-tile-alt tile-danger" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);">
            <div class="stats">
              <div class="tile-content">
                <span class="material-icons tile-icon">notifications_active</span>
              </div>
            </div>
            <div class="info">
              <div class="tile-heading">
                <span>Alert Service Parts</span>
              </div>
              <div class="tile-body "><span id="totalsite"><?= $alertdatacount ?></span></div>
            </div>
          </div>
        </div>
      </a>
      <?php } ?>
    </div>
    <div class="row">
      <div class="col-md-12 pl-n">
        <div class="col-md-3 pl-n pr-n">
            <div class="form-group" id="days_div">
                <label for="days" class="col-md-4 control-label">Days <span class="mandatoryfield"> *</span></label>
                <div class="col-md-8">
                    <input id="days" type="text" name="days" class="form-control" value="30">
                </div>
            </div>
        </div>
        <div class="col-md-2 pl-n">
          <div class="form-group">
            <div class="col-md-12">
              <label class="control-label"></label>
              <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <?php if (in_array("recent-upcoming-expire-vehicle-registration",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
      <div class="col-md-12">
        <div class="panel panel-default" data-widget="{ &quot;draggable&quot;: &quot;false&quot; }" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px); border:1px solid #4d5ec1;">
          <div class="panel-heading panel-indigo">
            <h4 class="text-white col-sm-8 pl-n">Upcoming Expire Vehicle Registration</h4>
            <h4 class="text-white col-sm-4 text-right p-n">
            <a class="text-white" style="font-size:15px;border: 1px solid #f1f1f1;padding: 2px 8px;border-radius: 2px;" onclick="printvehicleregistration()">Print</a>
            <a class="text-white" style="font-size:15px;border: 1px solid #f1f1f1;padding: 2px 8px;border-radius: 2px;" href="<?= ADMIN_URL ?>expire-vehicle-registration-report">View More</a>
            </h4>
          </div>
          <div class="panel-body table-responsive p-n">
            <table id='vehicleregistrationtable' class="table table-bordered table-hover m-n">
              <thead>
                <tr>
                  <th>Vehicle Name</th>
                  <th>Vehicle Number</th>
                  <th>Vehicle Type</th>
                  <th>Party Name</th>
                  <th>Contact No.</th>
                  <th>Due Date of Registration</th>
                  <th>Days</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php } if (in_array("recent-upcoming-expire-insurance",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
      <div class="col-md-12">
        <div class="panel panel-default" data-widget="{ &quot;draggable&quot;: &quot;false&quot; }" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px); border:1px solid #97c95d;">
          <div class="panel-heading panel-success">
            <h4 class="text-white col-sm-8 pl-n">Upcoming Expire Insurance</h4>
            <h4 class="text-white col-sm-4 text-right p-n">
            <a class="text-white" style="font-size:15px;border: 1px solid #f1f1f1;padding: 2px 8px;border-radius: 2px;" onclick="printexpireinsurance()">Print</a>
            <a class="text-white" style="font-size:15px;border: 1px solid #f1f1f1;padding: 2px 8px;border-radius: 2px;" href="<?= ADMIN_URL ?>expire-insurance-report">View More</a>
            </h4>
          </div>
          <div class="panel-body table-responsive p-n">
            <table id='insurancetable' class="table table-bordered table-responsive table-hover m-n">
              <thead>
                <tr>
                  <th>Vehicle Name</th>
                  <th>Insurance Company</th>
                  <th>Policy No.</th>
                  <th>Due Date</th>
                  <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                  <th>Days</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php } if (in_array("recent-upcoming-expire-document",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
      <div class="col-md-12">
        <div class="panel panel-default" data-widget="{ &quot;draggable&quot;: &quot;false&quot; }" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px); border:1px solid #29b09f;">
          <div class="panel-heading panel-teal">
            <h4 class="text-white col-sm-8 pl-n">Upcoming Expire Document</h4>
            <h4 class="text-white col-sm-4 text-right p-n">
            <a class="text-white" style="font-size:15px; border: 1px solid #f1f1f1;padding: 2px 8px;border-radius: 2px;" onclick="printexpireddocument()">Print</a>
            <a class="text-white" style="font-size:15px; border: 1px solid #f1f1f1;padding: 2px 8px;border-radius: 2px;" href="<?= ADMIN_URL ?>expire-document-report">View More</a>
            </h4>
          </div>
          <div class="panel-body table-responsive p-n">
            <table id="documenttable" class="table table-bordered table-responsive table-hover m-n">
              <thead>
                <tr>
                  <th>Vehicle Name</th>
                  <th>Party Name</th>
                  <th>Document No.</th>
                  <th>Document Type</th>
                  <th>Due Date</th>
                  <th>Days</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php } if (in_array("recent-upcoming-expire-service-parts",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
      <div class="col-md-12">
        <div class="panel panel-default" data-widget="{ &quot;draggable&quot;: &quot;false&quot; }" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px); border:1px solid #faa21b;">
          <div class="panel-heading panel-orange">
            <h4 class="text-white col-sm-8 pl-n">Upcoming Expire Service Parts</h4>
            <h4 class="text-white col-sm-4 text-right p-n">
            <a class="text-white" style="font-size:15px;border: 1px solid #f1f1f1;padding: 2px 8px;border-radius: 2px;" onclick="printservicepartdata()">Print</a>
            <a class="text-white" style="font-size:15px;border: 1px solid #f1f1f1;padding: 2px 8px;border-radius: 2px;" href="<?= ADMIN_URL ?>expire-service-part-report">View More</a>
            </h4>
          </div>
          <div class="panel-body table-responsive p-n">
            <table id="servicepartstable" class="table table-bordered table-hover m-n">
              <thead>
                <tr>
                  <th>Vehicle Name</th>
                  <th>Parts Name</th>
                  <th>Serial No.</th>
                  <th>Warranty End Date</th>
                  <th>Due Date</th>
                  <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                  <th>Days</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php } if (in_array("recent-service-part-alerts",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
      <div class="col-md-12">
        <div class="panel panel-default" data-widget="{ &quot;draggable&quot;: &quot;false&quot; }" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px); border:1px solid #eb6357;">
          <div class="panel-heading panel-danger">
            <h4 class="text-white col-sm-8 pl-n">Service Part Alerts</h4>
            <h4 class="text-white col-sm-4 text-right p-n">
            <a class="text-white" style="font-size:15px;border: 1px solid #f1f1f1;padding: 2px 8px;border-radius: 2px;" onclick="printservicepartalertdata()">Print</a>
            <a class="text-white" style="font-size:15px;border: 1px solid #f1f1f1;padding: 2px 8px;border-radius: 2px;" href="<?= ADMIN_URL ?>alert-report">View More</a>
            </h4>
          </div>
          <div class="panel-body table-responsive p-n">
            <table id="servicepartalerttable" class="table table-bordered table-hover m-n">
              <thead>
                  <tr>
                    <th>Vehicle Name</th>
                    <th>Part Name</th>
                    <th>Serial Number</th>
                    <th class="text-right">Current Km/hr</th>
                    <th class="text-right">Alert Km/hr</th>  
                  </tr>
              </thead>
              <tbody>
                <?php if (count($alertpartsdata) > 0) {
                  for ($i = 0; $i < count($alertpartsdata); $i++) { 
                    $vehiclename = "-";
                    
                    if($alertpartsdata[$i]['vehiclename']!=""){
                      $vehiclename = '<a href="'.ADMIN_URL .'vehicle/view-vehicle/'. $alertpartsdata[$i]['vehicleid'] .'#servicetab" target="_balnk">'.$alertpartsdata[$i]['vehiclename'];
                    }?>
                    <tr>
                      <td><?= $vehiclename ?></td>
                      <td><?= $alertpartsdata[$i]['partname'] ?></td>
                      <td><?= $alertpartsdata[$i]['serialnumber'] ?></td>
                      <td class="text-right"><?= numberFormat($alertpartsdata[$i]['currentkmhr'],2,',') ?></td> 
                      <td class="text-right"><?= numberFormat($alertpartsdata[$i]['alertkmhr'],2,',') ?></td> 
                    </tr>
                  <?php }
                } else { ?>
                  <tr>
                    <td colspan="5" class="text-center">No records found</td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php } if (in_array("recent-vehicle-emi-reminder",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
      <div class="col-md-12">
        <div class="panel panel-default" data-widget="{ &quot;draggable&quot;: &quot;false&quot; }" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px); border:1px solid #eb6357;">
          <div class="panel-heading panel-danger">
            <h4 class="text-white col-sm-8 pl-n">Vehicle EMI Reminder</h4>
            <h4 class="text-white col-sm-4 text-right p-n">
            <a class="text-white" style="font-size:15px;border: 1px solid #f1f1f1;padding: 2px 8px;border-radius: 2px;" onclick="printemireminderdata()">Print</a>
            <a class="text-white" style="font-size:15px;border: 1px solid #f1f1f1;padding: 2px 8px;border-radius: 2px;" href="<?= ADMIN_URL ?>vehicle-emi-report">View More</a>
            </h4>
          </div>
          <div class="panel-body table-responsive p-n">
            <table id="emiremindertable" class="table table-bordered table-hover m-n">
              <thead>
                  <tr>
                    <th>Vehicle Name</th>
                    <th class="text-right">Installment Amount (<?=CURRENCY_CODE?>)</th>
                    <th>Installment Date</th>
                    <th>Days</th>
                  </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php } ?>
        <?php /*<div class="col-md-12">
          <div class="panel panel-default" data-widget="{ &quot;draggable&quot;: &quot;false&quot; }" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px); border:1px solid #35b4fc;">
            <div class="panel-heading panel-primary">
              <h4 class="text-white col-sm-8 pl-n">Vehicle Upcoming EMI (Installment)</h4>
              <h4 class="text-white col-sm-4 text-right p-n"><a class="text-white" style="font-size:15px;border: 1px solid #f1f1f1;padding: 2px 8px;border-radius: 2px;" href="<?= ADMIN_URL ?>vehicle-emi-report">View More</a></h4>
            </div>
            <div class="panel-body table-responsive p-n">
              <table class="table table-bordered table-hover m-n">
                <thead>
                    <tr>
                      <th>Vehicle Name</th>
                      <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                      <th>Date</th>
                      <th>Days</th>
                    </tr>
                </thead>
                <tbody>
                  <?php if (count($EMIdata) > 0) {
                    for ($i = 0; $i < count($EMIdata); $i++) { 
                      $vehiclename = "-";
                      
                      if($alertpartsdata[$i]['vehiclename']!=""){
                        $vehiclename = '<a href="'.ADMIN_URL .'vehicle/view-vehicle/'. $EMIdata[$i]['vehicleid'] .'" target="_balnk">'.$EMIdata[$i]['vehiclename'];
                      }?>
                      <tr>
                        <td><?= $vehiclename ?></td>
                        <td class="text-right"><?= numberFormat($EMIdata[$i]['installmentamount'],2,',') ?></td> 
                        <td><?= $this->general_model->displaydate($EMIdata[$i]['installmentdate']) ?></td>
                        <td><?= $EMIdata[$i]['days'] ?></td>
                      </tr>
                    <?php }
                  } else { ?>
                    <tr>
                      <td colspan="4" class="text-center">No records found</td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
         */ ?>
    </div>
  </div> <!-- .container-fluid -->
</div> <!-- #page-content -->