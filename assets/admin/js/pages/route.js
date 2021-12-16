
$(document).ready(function() {
  
    oTable = $('#routetable').DataTable({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [0,4,-1,-2]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"route/listing",
        "type": "POST",
        "data": function ( data ) {
          data.routeid = $('#routeid').val();
        },
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        error: function(xhr) {
          //alert(xhr.responseText);
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
      },
    });
    $('.dataTables_filter input').attr('placeholder','Search...');


    //DOM Manipulation to move datatable elements integrate to panel
    $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
    $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
    $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

    
    $('.panel-footer').append($(".dataTable+.row"));
    $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");
    
    $(function () {
        $('.panel-heading.filter-panel').click(function() {
            $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
            //$(this).children().toggleClass(" ");
            $(this).next().slideToggle({duration: 200});
            $(this).toggleClass('panel-collapsed');
            
            return false;
        });
    });
    
});

function applyFilter(){
    oTable.ajax.reload(null,false);
}
/****CHANNEL CHANGE EVENT****/
$(document).on('change', 'select.channelid', function() { 
  var divid = $(this).attr("div-id");
 
  $("#uniquemember"+divid).val(this.value+"_0");
  getmember(divid);
});
/****MEMBER CHANGE EVENT****/
$(document).on('change', 'select.memberid', function() { 
  var divid = $(this).attr("div-id");
 
  var channelid = $("#channelid"+divid).val();
  $("#uniquemember"+divid).val(channelid+"_"+this.value);
});

function getmember(divid){

  $("#memberid"+divid).find('option')
              .remove()
              .end()
              .val('0')
              .append('<option value="0">Select '+Member_label+'</option>')
            ;
  $("#memberid"+divid).selectpicker('refresh');
  var channelid = $("#channelid"+divid).val();

  if(channelid!=0){
      var uurl = SITE_URL+"member/getmembers";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {channelid:channelid},
          dataType: 'json',
          async: false,
          success: function(response){
  
              for(var i = 0; i < response.length; i++) {
      
                  $("#memberid"+divid).append($('<option>', { 
                      value: response[i]['id'],
                      text : ucwords(response[i]['namewithcodeormobile'])
                  }));
              }
          },
          error: function(xhr) {
              //alert(xhr.responseText);
          },
      });
      $("#memberid"+divid).selectpicker('refresh');
  }
}
function addNewMemberRaw(){

  var divcount = parseInt($(".countmembers:last").attr("id").match(/\d+/))+1;
  
  html = '<div class="col-md-12 p-n countmembers" id="countmembers'+divcount+'">\
      <input type="hidden" name="routememberid[]" value="0" id="routememberid'+divcount+'">\
      <input type="hidden" name="uniquemember[]" id="uniquemember'+divcount+'">\
      <div class="col-sm-3 pl-sm pr-sm">\
          <div class="form-group" id="channel'+divcount+'_div">\
              <div class="col-sm-12">\
                  <select id="channelid'+divcount+'" name="channelid[]" class="selectpicker form-control channelid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="'+divcount+'">\
                      <option value="0">Select Channel</option>\
                      '+CHANNEL_DATA+'\
                  </select>\
              </div>\
          </div>\
      </div>\
      <div class="col-sm-4 pl-sm pr-sm">\
          <div class="form-group" id="member'+divcount+'_div">\
              <div class="col-md-12">\
                  <select id="memberid'+divcount+'" name="memberid[]" class="selectpicker form-control memberid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="'+divcount+'">\
                      <option value="">Select '+Member_label+'</option>\
                  </select>\
              </div>\
          </div>\
      </div>\
      <div class="col-sm-1 pl-sm pr-sm">\
          <div class="form-group" id="priority'+divcount+'_div">\
              <div class="col-md-12">\
              <input type="text" id="priority'+divcount+'" name="priority[]" class="form-control priority" div-id="'+divcount+'">\
              </div>\
          </div>\
      </div>\
      <div class="col-sm-1 pl-sm pr-sm">\
          <div class="form-group" id="active'+divcount+'_div">\
              <div class="col-md-12">\
                  <div class="yesno mt-xs">\
                      <input type="checkbox" id="active'+divcount+'" name="active'+divcount+'" value="1" checked>\
                  </div>\
              </div>\
          </div>\
      </div>\
      <div class="col-md-2 form-group m-n pt-md">\
          <button type = "button" class = "btn btn-default btn-raised remove_btn" onclick="removeMemberRaw('+divcount+')" style="padding: 5px 10px;"> <i class = "fa fa-minus"></i></button> \
          <button type="button" class="btn btn-default btn-raised add_btn" onclick="addNewMemberRaw()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> \
      </div>\
  </div>';

  $(".remove_btn:first").show();
  $(".add_btn:last").hide();
  $("#countmembers"+(divcount-1)).after(html);
  
  $('.yesno input[type="checkbox"]').bootstrapToggle({
      on: 'Yes',
      off: 'No',
      onstyle: 'primary',
      offstyle: 'danger'
  });
  $(".selectpicker").selectpicker("refresh");

  $("#priority"+divcount).val(parseInt($(".countmembers").length));
}
function removeMemberRaw(divid){

  $("#countmembers"+divid).remove();

  $(".add_btn:last").show();
  if ($(".remove_btn:visible").length == 1) {
      $(".remove_btn:first").hide();
  }
}
function viewmemberlist(RouteId){
   
  if(RouteId > 0){
    
    var uurl = SITE_URL+"route/getRouteMemberByRouteId";
   
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {routeid:String(RouteId)},
      dataType: 'json',
      async: false,
      beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
      },
      success: function(response){

          if(response.length>0){
            var HTML = '';
            var CHANNELID = [];
            MEMBERID = [];
            
            for(var i = 0; i < response.length; i++) {
              var count = i+1;
              MEMBERID.push(response[i]['memberid']);
              CHANNELID.push(response[i]['channelid']);

              var isactive = '';
              if(response[i]['active'] == 1){
                isactive = 'checked';
              }

              var btn = '';
              if(i==0){
                if(response.length > 1){
                  btn += '<button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeMemberRaw(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>';
                }else {
                  btn += '<button type="button" class="btn btn-default btn-raised add_btn" onclick="addNewMemberRaw()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>';
                }
              }else if(i!=0) {
                btn += '<button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeMemberRaw('+count+')" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>';
              }
              btn += '<button type="button" class="btn btn-default btn-raised btn-sm remove_btn" onclick="removeMemberRaw('+count+')"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>';
        
              btn += '<button type="button" class="btn btn-default btn-raised add_btn" onclick="addNewMemberRaw()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>';

              HTML += '<div class="col-md-12 p-n countmembers" id="countmembers'+count+'">\
                        <input type="hidden" name="routememberid[]" value="'+response[i]['id']+'" id="routememberid'+count+'">\
                        <input type="hidden" name="uniquemember[]" id="uniquemember'+count+'" value="'+response[i]['channelid']+'_'+response[i]['memberid']+'">\
                          <div class="col-sm-3 pl-sm pr-sm">\
                            <div class="form-group" id="channel'+count+'_div">\
                                <div class="col-sm-12">\
                                    <select id="channelid'+count+'" name="channelid[]" class="selectpicker form-control channelid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="'+count+'">\
                                        <option value="0">Select Channel</option>\
                                        '+CHANNEL_DATA+'\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-4 pl-sm pr-sm">\
                            <div class="form-group" id="member'+count+'_div">\
                                <div class="col-md-12">\
                                    <select id="memberid'+count+'" name="memberid[]" class="selectpicker form-control memberid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="'+count+'">\
                                        <option value="0">Select '+Member_label+'</option>\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-1 pl-sm pr-sm">\
                            <div class="form-group" id="priority'+count+'_div">\
                                <div class="col-md-12">\
                                    <input type="text" id="priority'+count+'" name="priority[]" value="'+response[i]['priority']+'" class="form-control priority" div-id="'+count+'">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-sm-1 pl-sm pr-sm">\
                            <div class="form-group" id="active'+count+'_div">\
                                <div class="col-md-12">\
                                    <div class="yesno mt-xs">\
                                        <input type="checkbox" id="active'+count+'" name="active'+count+'" value="'+response[i]['active']+'" '+isactive+'>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="col-md-2 form-group m-n pt-md">\
                          '+btn+'\
                        </div>\
                    </div>';
            }
          
            $('#routememberdata').html(HTML);
            $('#editrouteid').val(response[0]['routeid']);
            $('#EditroutememberModal .modal-title').html("View & Edit Route of "+response[0]['route']);
            console.log(CHANNELID);
            for(var c = 0; c < CHANNELID.length; c++){

              var id = c+1;
              $("#channelid"+id).val(CHANNELID[c]).selectpicker('refresh');
              getmember(id);
              $("#memberid"+id).val(MEMBERID[c]).selectpicker('refresh');
            }
            $(".add_btn").hide();
            $(".add_btn:last").show();

            $('.yesno input[type="checkbox"]').bootstrapToggle({
              on: 'Yes',
              off: 'No',
              onstyle: 'primary',
              offstyle: 'danger'
            });
            $(".selectpicker").selectpicker("refresh");
            $("#EditroutememberModal").modal("show");
          }
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
function checkvalidation() {
   
  var isvalidchannelid = isvalidmemberid = isvalidpriority = isvaliduniquemember = 1;
 
  PNotify.removeAll();
  
  var c=1;
  $('.countmembers').each(function(){
      var id = $(this).attr('id').match(/\d+/);
     
      if($("#channelid"+id).val() == 0){
          $("#channel"+id+"_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select '+(c)+' channel !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidchannelid = 0;
      }else {
          $("#channel"+id+"_div").removeClass("has-error is-focused");
      }
      if($("#memberid"+id).val() == 0){
          $("#member"+id+"_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select '+(c)+' '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidmemberid = 0;
      }else {
          $("#member"+id+"_div").removeClass("has-error is-focused");
      }
      if($("#priority"+id).val() == "" || $("#priority"+id).val() == "0"){
          $("#priority"+id+"_div").addClass("has-error is-focused");
          new PNotify({title: 'Please enter '+(c)+' priority !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidpriority = 0;
      }else {
          $("#priority"+id+"_div").removeClass("has-error is-focused");
      }
      
      c++;
  });

  var member = $('input[name="uniquemember[]"]');
  var values = [];
  for(j=0;j<member.length;j++) {
      var uniquemember = member[j];
      var id = uniquemember.id.match(/\d+/);
      
      if(uniquemember.value!="" && $("#memberid"+id[0]).val()!=0){
          if(values.indexOf(uniquemember.value)>-1) {
              $("#channel"+id[0]+"_div,#member"+id[0]+"_div").addClass("has-error is-focused");
              new PNotify({title: 'Please select '+(j+1)+' is different channel & '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
              isvaliduniquemember = 0;
          }
          else{ 
              values.push(uniquemember.value);
          }
      }
  } 
  if(isvalidchannelid == 1 && isvalidmemberid == 1 && isvalidpriority == 1 && isvaliduniquemember == 1){
      var formData = new FormData($('#routeform')[0]);
      // MODIFY
      var baseurl = SITE_URL + 'route/update-route-member';
      $.ajax({
        url: baseurl,
        type: 'POST',
        data: formData,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){
            
            if(response==1){
              new PNotify({title: 'Route '+member_label+' successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location = SITE_URL + "route";}, 500);
            }else{
              new PNotify({title: 'Route '+member_label+' not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }
        },
        error: function(xhr) {
        },
        complete: function(){
            $('.mask').hide();
            $('#loader').hide();
        },
        cache: false,
        contentType: false,
        processData: false
    });
      
  }
}