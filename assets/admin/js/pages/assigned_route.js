
$(document).ready(function() {
  
    oTable = $('#assignedroutetable').DataTable({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 10,
      "columnDefs": [{
        'orderable': false,
        'targets': [0,-1,-2,-5,-6]
      },{"targets":[5,6,7], className: "text-center"}],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"assigned-route/listing",
        "type": "POST",
        "data": function ( data ) {
          data.routeid = $('#routeid').val();
          data.employeeid = $('#employeeid').val();
          data.assignedbyid = $('#assignedbyid').val();
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

function viewproductlist(AssignedrouteId){
   
  if(AssignedrouteId > 0){
    
    var uurl = SITE_URL+"assigned-route/getAssignedRouteProductList";
    
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
          var productlist = '';
          var totalprice = 0;
          for(var i=0; i<response.length; i++){
              
            productlist += '<tr>\
                              <td>'+(i+1)+'</td>\
                              <td>'+response[i]['productname']+'</td>\
                              <td>'+response[i]['variantname']+'</td>\
                              <td class="text-right">'+response[i]['quantity']+'</td>\
                              <td class="text-right">'+format.format(parseFloat(response[i]['price']).toFixed(2))+'</td>\
                              <td class="text-right">'+format.format(parseFloat(response[i]['tax']).toFixed(2))+'</td>\
                              <td class="text-right">'+format.format(parseFloat(response[i]['totalprice']).toFixed(2))+'</td>\
                            </tr>';

            totalprice += parseFloat(response[i]['totalprice']);
          }
          productlist += '<tr>\
                            <th colspan="6" class="text-right">Total Price ('+CURRENCY_CODE+')</th>\
                            <th class="text-right">'+format.format(parseFloat(totalprice).toFixed(2))+'</th>\
                          </tr>';
        }else{
          productlist += '<tr>\
                            <td colspan="7" class="text-center">No data available in table.</td>\
                          </tr>';
        } 
        $("#productlist").html(productlist);
        $("#productListModal").modal("show");
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

var currentdids = [];
var position = 0;
var inputs = $("#routeListModal input[type='checkbox']");

function singlecheckmodal(id){
  var inputs = $("#routeListModal input[type='checkbox']");
  var isallchecked = 1,isalldechecked = 1;
  
  if ($('#'+id).prop('checked')==true){
    currentdids[position] = $('#'+id).val();                      
    position++;
    for(var i = 1; i<inputs.length; i++){
      if($('#'+inputs[i].id).prop('checked') == true){
        isallchecked = 1;
      }else{
        isallchecked = 0;
        break;
      }
    }
    if(isallchecked == 1){
      $('#deletecheckall_modal').prop('checked', true);
    }
  }else{
    currentdids.splice($.inArray($('#'+id).val(), currentdids),1);
    for(var i = 1; i<inputs.length; i++){
      if($('#'+inputs[i].id).prop('checked') == false){
        $('#deletecheckall_modal').prop('checked', false);
        break;
      }
    }
    position--;
  }
}

function allchecked_modal(){
  var inputs = $("#routeListModal input[type='checkbox']");
  if ($('#deletecheckall_modal').prop('checked')==true){
    for(var i = 1; i<inputs.length; i++){
      $('#'+inputs[i].id).prop('checked', true);
      if($('#'+inputs[i].id).prop('checked') == true){
        if(jQuery.inArray($('#'+inputs[i].id).val(),currentdids) == -1){
          currentdids[position] = $('#'+inputs[i].id).val();
          position++;
        }
      }
    }
  }
  else{ 
    for(var i = 1; i<inputs.length; i++){
      currentdids.splice($.inArray($('#'+inputs[i].id).val(), currentdids),1);
      $('#'+inputs[i].id).prop('checked', false);
      position--;
    }
  }
}

function checkmultipledeletemodal(url,name,deleteurl){
  var inputs = $("#routeListModal input[type='checkbox']");
  if(currentdids == ""){
    swal("Cancelled", 'Please select '+name+' !', "error");
  }else{
    if(url!=''){
      var datastr = 'ids='+currentdids;
      var baseurl = url;
      $.ajax({
        url: baseurl,
        type: 'POST',
        data: datastr,
        success: function(data){
          if(data == 0){
            swal({    title: 'Are you sure to delete '+name+'?',
              type: "warning",   
              showCancelButton: true,   
              confirmButtonColor: "#DD6B55",   
              confirmButtonText: "Yes, delete it!",   
              closeOnConfirm: false }, 
              function(isConfirm){
                if (isConfirm) {   
                  multipledelete(deleteurl);
                }else{
                  if($('#deletecheckall_modal').prop('checked') == true){
                    $('#deletecheckall_modal').prop('checked', false);
                  }
                  for(var i=1;i<inputs.length;i++){
                    if($('#'+inputs[i].id).prop('checked') == true){
                      $('#'+inputs[i].id).prop('checked', false);
                    }
                  }
                  currentdids = [];
                  position = 0;
                }
              });
          }else{
            if(data == currentdids.length){
              swal("Cancelled", "All "+ucwords(name)+" are used in other. So, you can't delete them!", "error");

              if($('#deletecheckall_modal').prop('checked') == true){
                $('#deletecheckall_modal').prop('checked', false);
              }
              for(var i=1;i<inputs.length;i++){
                if($('#'+inputs[i].id).prop('checked') == true){
                  $('#'+inputs[i].id).prop('checked', false);
                }
              }
              currentdids = [];
              position = 0;
            }

            else if(data == 1){
              swal({    title: data+' '+ucwords(name)+' is used in other. So, you can not delete it. Still you want to delete remaining '+name+'?',
                type: "warning",   
                showCancelButton: true,   
                confirmButtonColor: "#DD6B55",   
                confirmButtonText: "Yes, delete it!",   
                closeOnConfirm: false }, 
                function(isConfirm){
                  if (isConfirm) {   
                    multipledelete(deleteurl);
                  }else{
                    if($('#deletecheckall_modal').prop('checked') == true){
                      $('#deletecheckall_modal').prop('checked', false);
                    }
                    for(var i=1;i<inputs.length;i++){
                      if($('#'+inputs[i].id).prop('checked') == true){
                        $('#'+inputs[i].id).prop('checked', false);
                      }
                    }
                    currentdids = [];
                    position = 0;
                  }
                });
            }

            else{
              swal({    title: data+' '+ucwords(name)+' are used in other. So, you can not delete it. Still you want to delete remaining '+name+'?',
                type: "warning",   
                showCancelButton: true,   
                confirmButtonColor: "#DD6B55",   
                confirmButtonText: "Yes, delete it!",   
                closeOnConfirm: false }, 
                function(isConfirm){
                  if (isConfirm) {   
                    multipledelete(deleteurl);
                  }else{
                    if($('#deletecheckall_modal').prop('checked') == true){
                      $('#deletecheckall_modal').prop('checked', false);
                    }
                    for(var i=1;i<inputs.length;i++){
                      if($('#'+inputs[i].id).prop('checked') == true){
                        $('#'+inputs[i].id).prop('checked', false);
                      }
                    }
                    currentdids = [];
                    position = 0;
                  }
                });
            }
          }
        }
      });
    }else{
      swal({    title: 'Are you sure to delete '+name+'?',
        type: "warning",   
        showCancelButton: true,   
        confirmButtonColor: "#DD6B55",   
        confirmButtonText: "Yes, delete it!",   
        closeOnConfirm: false }, 
        function(isConfirm){
          if (isConfirm) {   
            multipledelete(deleteurl);
          }else{
            if($('#deletecheckall_modal').prop('checked') == true){
              $('#deletecheckall_modal').prop('checked', false);
            }
            for(var i=1;i<inputs.length;i++){
              if($('#'+inputs[i].id).prop('checked') == true){
                $('#'+inputs[i].id).prop('checked', false);
              }
            }
            currentdids = [];
            position = 0;
          }
        });
    }

  }
}

function chageroutestatus(status, assignedrouteId){
  var uurl = SITE_URL+"assigned-route/update-route-status";
  if(assignedrouteId!=''){
    swal({    title: "Are you sure to change status?",
      type: "warning",   
      showCancelButton: true,   
      confirmButtonColor: "#DD6B55",   
      confirmButtonText: "Yes, change it!",   
      closeOnConfirm: false }, 
    function(isConfirm){   
      if (isConfirm) {  
         $.ajax({
            url: uurl,
            type: 'POST',
            data: {status:status,assignedrouteid:assignedrouteId},
            beforeSend: function(){
              $('.mask').show();
              $('#loader').show();
            },
            success: function(response){
              if(response==1){
                  location.reload();
                }
            },
            complete: function(){
              $('.mask').hide();
              $('#loader').hide();
            },
            error: function(xhr) {
            //alert(xhr.responseText);
            }
          });  
      }
    });
  }           
}
