<?php 
$CHANNEL_DATA = '';
if(!empty($channeldata)){
    foreach($channeldata as $channel){
        $CHANNEL_DATA .= '<option value="'.$channel['id'].'">'.$channel['name'].'</option>';
    } 
}
?>
<?php
$isdelete = 0;
if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
  $isdelete = 1;
}
?>
<script>
    var ASSIGNED_ROUTE_PATH = '<?=ASSIGNED_ROUTE?>';
    var CHANNEL_DATA = '<?=$CHANNEL_DATA?>';
    var IS_DELETE = '<?=$isdelete?>';
    var delete_class = "<?=delete_class?>";
    var delete_title = "<?=delete_title?>";
    var delete_text = "<?=delete_text?>";

    var view_class = "<?=view_class?>";
    var view_title = "<?=view_title?>";
    var view_text = "<?=view_text?>";
    var IMG_PATH = "<?=PRODUCT_PATH?>";
    
</script>
<div class="page-content">
    <div class="page-heading">     
        <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                    <div class="panel-heading filter-panel border-filter-heading">
                        <h2><?=APPLY_FILTER?></h2>
                        <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                    </div>
                    <div class="panel-body panelcollapse pt-n" style="display: none;">
                      <form action="#" class="form-horizontal">
                          <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                  <div class="col-sm-12 pr-sm">
                                      <label for="employeeid" class="control-label">Employee</label>
                                      <select id="employeeid" name="employeeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                          <option value="0">All Employee</option>
                                          <?php foreach ($employeedata as $employee) { ?>
                                              <option value="<?=$employee['id']?>"><?=ucwords($employee['name'])?></option>
                                          <?php } ?> 
                                      </select>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                  <div class="col-sm-12 pr-sm pl-sm">
                                      <label for="routeid" class="control-label">Route</label>
                                      <select id="routeid" name="routeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                          <option value="0">All Route</option>
                                          <?php foreach ($routedata as $route) { ?>
                                              <option value="<?=$route['id']?>"><?=ucfirst($route['route'])?></option>
                                          <?php } ?> 
                                      </select>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                  <div class="col-sm-12 pr-sm pl-sm">
                                      <label for="assignedbyid" class="control-label">Assigned By</label>
                                      <select id="assignedbyid" name="assignedbyid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                          <option value="0">All Assigned By</option>
                                          <?php foreach ($assignedbydata as $abd) { ?>
                                              <option value="<?=$abd['id']?>"><?=ucfirst($abd['name'])?></option>
                                          <?php } ?> 
                                      </select>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-xl">
                                  <div class="col-sm-12">
                                      <label class="control-label"></label>
                                      <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                  </div>
                                </div>
                            </div>
                          </div> 
                      </form>
                    </div>
                </div>
            </div>
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-heading">
                
                <div class="col-md-6">
                  <div class="panel-ctrls panel-tbl"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                  <?php 
                    if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                      <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>assigned-route/assigned-route-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    } ?>
                    <?php
                    if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('','Assigned Route','<?php echo ADMIN_URL; ?>assigned-route/delete-mul-assigned-route')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                  </div>
              </div>
              <div class="panel-body no-padding">
                <table id="assignedroutetable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width5">Sr. No.</th>
                      <th>Route Name</th>
                      <th>Sales Person</th>
                      <th>Vehicle Name</th>
                      <th>Date</th>
                      <th>Time</th>
                      <th>Route List</th>
                      <th>Product List</th>
                      <th>Status</th>
                      <th>Assigned By</th>
                      <th width="10%">Action</th>
                      <th class="width5">
                        <div class="checkbox">
                          <input id="deletecheckall" onchange="allchecked()" type="checkbox" value="all">
                          <label for="deletecheckall"></label>
                        </div>
                      </th>
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

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
<div class="modal fade" id="productListModal" tabindex="-1" role="dialog" aria-labelledby="productListLabel">
  <div class="modal-dialog" role="document" style="width: 900px;">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
            <h4 class="modal-title">Product List</h4>
          </div>
          <div class="modal-body pt-n" style="float: left;width: 100%;padding:8px 16px;overflow-y: auto;max-height: 420px;">
            <div class="row">
              <div class="col-md-12 p-n">
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th width="9%">Sr. No.</th>
                      <th>Product Name</th>
                      <th>Variant</th>
                      <th class="text-right">Qty.</th>
                      <th class="text-right">Price (<?=CURRENCY_CODE?>)</th>
                      <th width="9%" class="text-right">Tax (%)</th>
                      <th class="text-right">Total Price (<?=CURRENCY_CODE?>)</th>
                    </tr>
                  </thead>
                  <tbody id="productlist">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer"></div>
      </div>
  </div>
</div>

<div class="modal fade" id="routeListModal" tabindex="-1" role="dialog" aria-labelledby="routeListLabel">
  <div class="modal-dialog" role="document" style="width: 950px;">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
            <?php if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                <a class="<?=deletebtn_class;?> pull-right mr-md" href="javascript:void(0)" onclick="checkmultipledelete('','Route','<?php echo ADMIN_URL; ?>assigned-route/delete-mul-route')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
            <?php } ?>
            <h4 class="modal-title">Route List</h4>
          </div>
          <div class="modal-body pt-n" style="float: left;width: 100%;padding:8px 16px;overflow-y: auto;max-height: 420px;">
            <div class="row">
              <div class="col-md-12 p-n">
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>  
                      <th width="9%">Sr. No.</th>          
                      <th><?=Member_label?> Name</th>
                      <th>Area</th>
                      <th>Invoice No.</th>
                      <th class="text-right">Invoice Price (<?=CURRENCY_CODE?>)</th>
                      <th>Visited</th>
                      <th>Reason</th>
                      <th class="text-center">Action</th>
                      <th class="width5">
                        <div class="checkbox">
                          <input id="deletecheckall_modal" onchange="allchecked_modal()" type="checkbox" value="all">
                          <label for="deletecheckall_modal"></label>
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody id="routelist">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer"></div>
      </div>
  </div>
</div>
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" style="z-index: 125000000;">
    <div class="modal-dialog modal-lg" role="document" style="width: 500px;">
        <div class="modal-content" id="modal-content-for-image">
            <button type="button" style="position: absolute; right:10px ; top:10px;z-index: 1;" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="col-md-12 p-n" id="thubviewlistofimg">
            <img src="" id="displayvisitimage" style="width:auto;height:auto;max-width: 550px;max-height: 100%;min-width: 500px;">
            </div>
        </div>
    </div>
</div>
<?php
$channelidarray = array_column($channeldata, 'id');

?>

<script>
  var channelidarray = <?=json_encode($channelidarray)?>;
  var channeldata = <?=json_encode($channeldata)?>;
  function viewroutelist(AssignedrouteId){
   
   if(AssignedrouteId > 0){
     
     var uurl = SITE_URL+"assigned-route/getAssignedRouteList";
     
     $.ajax({
       url: uurl,
       type: 'POST',
       data: {assignedrouteid:String(AssignedrouteId)},
       dataType: 'json',
       async: false,
       beforeSend: function(){
           $('.mask').show();
           $('#loader').show();
       },
       success: function(response){
 
         if(response.length > 0){
           var routelist = '';
           for(var i=0; i<response.length; i++){
               
             var invoiceprice = "-";
             if(response[i]['invoiceprice'] > 0){
               invoiceprice = format.format(parseFloat(response[i]['invoiceprice']).toFixed(2));
             }
             var action = checkbox = '';
             if(response[i]['image']!=''){
               action +=  '<a class="'+view_class+'" href="javascript:void(0)" onclick="displayimage(\''+response[i]['image']+'\')" title="'+view_title+'">'+view_text+'</a>';
              }
             if(IS_DELETE==1){
               action +=  '<a class="'+delete_class+'" href="javascript:void(0)" title="'+delete_title+'" onclick=deleterow('+response[i]['id']+',"","Route","'+SITE_URL+'assigned-route/delete-mul-route") >'+delete_text+'</a>';
               
               checkbox = '<td><div class="checkbox"><input value="'+response[i]['id']+'" type="checkbox" class="checkradios" name="modalcheck'+response[i]['id']+'" id="modalcheck'+response[i]['id']+'" onchange="singlecheckmodal(this.id)"><label for="modalcheck'+response[i]['id']+'"></label></div></td>';
               
              }
            var invoiceno = "-";
            if(response[i]['invoiceno']!=""){
              invoiceno = '<a href="<?=ADMIN_URL?>invoice/view-invoice/'+response[i]['invoiceid']+'" title="View Invoice" target="_blank">'+response[i]['invoiceno']+'</a>';
            }
            var key = channelidarray.indexOf(response[i]['channelid']);
            var membername = '<span class="label" style="background:'+channeldata[key]['color']+'">'+channeldata[key]['name'].substring(0, 1)+'</span> <a href="<?=ADMIN_URL?>member/member-detail/'+response[i]['memberid']+'" title="'+ucwords(response[i]['membername'])+'" target="_blank">'+response[i]['membername']+'</a>';
 
             routelist += '<tr>\
                               <td>'+(i+1)+'</td>\
                               <td>'+membername+'</td>\
                               <td>'+response[i]['route']+'</td>\
                               <td>'+invoiceno+'</td>\
                               <td class="text-right">'+invoiceprice+'</td>\
                               <td>'+(response[i]['isvisited']==0?'No':"Yes")+'</td>\
                               <td>'+(response[i]['reason']!=""?response[i]['reason']:"-")+'</td>\
                               <td class="text-center">'+action+'</td>\
                               '+checkbox+'\
                             </tr>';
                             
           }
         }else{
           routelist += '<tr>\
                             <td colspan="7" class="text-center">No data available in table.</td>\
                           </tr>';
         } 
         $("#routelist").html(routelist);
         $("#routeListModal").modal("show");
       },
       error: function(xhr) {
       //alert(xhr.responseText);
       },
       complete: function(){
           $('.mask').hide();
           $('#loader').hide();
       },
     });
     
     
   }
 }
 
 function displayimage(imagename){
  $('#imageModal').modal('show');

  $("#displayvisitimage").attr('src',(imagename!=''?ASSIGNED_ROUTE_PATH+imagename:""));

 }
</script>