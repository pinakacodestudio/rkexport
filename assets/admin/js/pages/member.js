$(document).ready(function() {

    //list("membertable","member/listing",[0,-1]);

    oTable = $('#membertable').dataTable({
      "language": {
        "lengthMenu": "_MENU_"
      },
      "pageLength": 50,
      "columnDefs": [{
        'orderable': false,
        'targets': [0,-1,-5]
      }],
      "order": [], //Initial no order.
      'serverSide': true,//Feature control DataTables' server-side processing mode.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": SITE_URL+"member/listing",
        "type": "POST",
        "data": function ( data ) {
            data.channelid = $('#channelid').val();
            data.startdate = $('#startdate').val();
            data.enddate = $('#enddate').val();
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

    $('#datepicker-range').datepicker({
      // todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      todayBtn:"linked",
      /* startDate: new Date(), */
    });

    $('#balancedate').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy',
      /*orientation:"bottom",*/
      container:'#openingbalanceModal',
      autoclose: true,
      todayBtn:"linked",
      
    });

    $(function () {
      $('.panel-heading.filter-panel').click(function() {
          $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
          //$(this).children().toggleClass(" ");
          $(this).next().slideToggle({duration: 200});
          $(this).toggleClass('panel-collapsed');
          savecollapse($(this).attr("display-type"),'panel-heading.filter-panel');
          return false;
      });
    });
    var displaytype = $('.panel-heading.filter-panel').attr("display-type");
      if(displaytype==0){

        $('.panel-heading.filter-panel').find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
        //$(this).children().toggleClass(" ");
        $('.panel-heading.filter-panel').next().slideToggle({duration: 200});
        $('.panel-heading.filter-panel').toggleClass('panel-collapsed');
    }
    
    $('#attachment').change(function(){
      var val = $(this).val();
      var filename = $("#attachment").val().replace(/C:\\fakepath\\/i, '');
      
      switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
        case 'xl': case 'xlc': case 'xls' : case 'xlsx' : case 'ods':
          $("#Filetext").val(filename);
          $("#isvalidmemberimportfile").val('1');
          isvalidfiletext = 1;
          $("#attachment_div").removeClass("has-error is-focused");
          break;
        default:
          $("#Filetext").val("");
          $("#isvalidmemberimportfile").val('0');
          isvalidfiletext = 0;
          $("#attachment_div").addClass("has-error is-focused");
          new PNotify({title: 'Please upload valid excel file !',styling: 'fontawesome',delay: '3000',type: 'error'});
          break;
      }
    });

    $('#zipfile').change(function(){
      var val = $(this).val();
      var filename = $("#zipfile").val().replace(/C:\\fakepath\\/i, '');
      
      switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
        case 'zip':
         
          if(parseInt(this.files[0].size) > UPLOAD_MAX_ZIP_FILE_SIZE){
            $("#Zipfiletext").val('');
            $("#validzipfile").val('1');
            $("#validzipfilesize").val('0');
            isvalidfiletext = 0;
            $("#zipfile_div").addClass("has-error is-focused");
            new PNotify({title: 'Zip file is too large (max size ' + formatBytes(UPLOAD_MAX_ZIP_FILE_SIZE)+')!',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            $("#Zipfiletext").val(filename);
            $("#validzipfile").val('1');
            $("#validzipfilesize").val('1');
            isvalidfiletext = 1;
            $("#zipfile_div").removeClass("has-error is-focused");
          }
          break;
        default:
          $("#Zipfiletext").val("");
          $("#validzipfile").val('0');
          $("#validzipfilesize").val('0');
          isvalidfiletext = 0;
          $("#zipfile_div").addClass("has-error is-focused");
          new PNotify({title: 'Please upload valid zip file !',styling: 'fontawesome',delay: '3000',type: 'error'});
          break;
      }
    });

});
function applyFilter(){
  oTable.fnDraw();
}
function exportmember(){
  
  var totalRecords =$("#membertable").DataTable().page.info().recordsDisplay;
  $.skylo('end');
  if(totalRecords != 0){
    window.location= SITE_URL+"member/exportmember";
  }else{
    new PNotify({title: 'No data available !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}

function savecollapse(panelcollapsed,cls){
  var uurl = SITE_URL+"member/savecollapse";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {panelcollapsed:panelcollapsed},
    dataType: 'json',
    success: function(response){
      if(response.panelcollapsed=='1'){
        $("."+cls).attr("display-type","0");
      }else{
        $("."+cls).attr("display-type","1");
      }
    },
    error: function(xhr) {
      //alert(xhr.responseText);
    },
  }); 
}

function generateQRCode(memberid){
  var uurl = SITE_URL+"member/generateQRCode";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {memberid:memberid},
    async: false,
    success: function(response){
      
      var obj = JSON.parse(response);
      var membername = obj['memberdata']['name'];
      var qrcode = obj['qrcodedata'];
      $('#myModal .modal-title').html(ucwords(membername)+' - QR Code');
      if(qrcode!=""){
        $("#qrcodeimage").html("<center><img src='"+qrcode+"' class='img-thumbnail'></center>");
      }
      $('#myModal').modal('show');
    },
    error: function(xhr) {
      //alert(xhr.responseText);
    },
  }); 
}

function setopeningbalance(memberid,balanceid,balancedate,balance){
  PNotify.removeAll();
  $('#openingbalanceModal').modal('show');
  $('#openingbalanceid').val(balanceid);
  $('#memberid').val(memberid);
  $('#balancedate').val(balancedate);
  $('#balance').val(balance);
  $("#balancedate_div").removeClass("has-error is-focused");
  $("#balance_div").removeClass("has-error is-focused");
}

function checkopeningbalancevalidation(){

  var balancedate = $("#balancedate").val();
  var balance = $("#balance").val();

  var isvalidbalancedate = isvalidbalance = 0;
  
  PNotify.removeAll();
  
  if(balancedate==''){
    $("#balancedate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select balance date !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
    $("#balancedate_div").removeClass("has-error is-focused");
    isvalidbalancedate = 1;
  }
  if(balance==''){
    $("#balance_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter balance !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
    $("#balance_div").removeClass("has-error is-focused");
    isvalidbalance = 1;
  }
  if(isvalidbalancedate==1 && isvalidbalance==1){
    
    var formData = new FormData($('#openingbalanceform')[0]);

    var uurl = SITE_URL+"opening-balance/setopeningbalance";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: formData,
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        if(response==1){
            new PNotify({title: "Opening balance successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
            $('#openingbalanceModal').modal('hide');
            oTable.fnDraw();
        }else{
          new PNotify({title: "Opening balance not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
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

function importmember(){
  PNotify.removeAll();

  $("#attachment_div").removeClass("has-error is-focused");
  $("#Filetext").val("");
  $('.selectpicker').selectpicker('refresh');  
  $('#myMemberImportModal').modal('show');
}
function checkimportmembervalidation(){

  var filetext = $("#attachment").val();
  var isvalidmemberimportfile = $("#isvalidmemberimportfile").val();
  var isvalidfiletext = 0;
  
  PNotify.removeAll();
  //CHECK FILE
  if(filetext==''){
    $("#attachment_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select excel file !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else if(filetext!='' && isvalidmemberimportfile==0){
    $("#attachment_div").addClass("has-error is-focused");
    new PNotify({title: 'Please upload valid excel file !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
      $("#attachment_div").removeClass("has-error is-focused");
      isvalidfiletext = 1;
  }
  if(isvalidfiletext==1){
    
    var formData = new FormData($('#memberimportform')[0]);

    var uurl = SITE_URL+"member/import-member";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: formData,
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        if(response==1){
            new PNotify({title: Member_label+" successfully imported.",styling: 'fontawesome',delay: '3000',type: 'success'});
             setTimeout(function() { window.location.reload(); }, 1500);
        }else if(response=='2'){
          new PNotify({title: "Uploaded file is not an excel file !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response=='3'){
          new PNotify({title: "Excel file not uploaded !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response=='4'){
          new PNotify({title: "Some field name are not match !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response=='5'){
          new PNotify({title: "Please enter at least one "+member_label+" detail !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response=='6'){
          new PNotify({title: "Please enter valid sheet name !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else{
          new PNotify({title: response,styling: 'fontawesome',delay: '3000',type: 'error'});
        }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
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


function uploadprofileimage(){
  PNotify.removeAll();

  $("#zipfile_div").removeClass("has-error is-focused");
  $("#Zipfiletext").val("");
  $('.selectpicker').selectpicker('refresh');  
  $('#myProfileImageModal').modal('show');
}

function checkvalidationforprofileimage(){

  var filetext = $("#Zipfiletext").val();
  var validzipfile = $("#validzipfile").val();
  var validzipfilesize = $("#validzipfilesize").val();
  var isvalidfiletext = 0;
  
  PNotify.removeAll();
  
  //CHECK FILE
  if(filetext==''){
    $("#zipfile_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select zip file !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else if(validzipfile == 0){
    $("#zipfile_div").addClass("has-error is-focused");
    new PNotify({title: 'Please upload valid zip file !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else if(validzipfilesize == 0){
    $("#zipfile_div").addClass("has-error is-focused");
    new PNotify({title: 'Zip file is too large (max size ' + formatBytes(UPLOAD_MAX_ZIP_FILE_SIZE)+')!',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
      $("#zipfile_div").removeClass("has-error is-focused");
      isvalidfiletext = 1;
  }
  if(isvalidfiletext==1){
    
    var formData = new FormData($('#imageuploadform')[0]);

    var uurl = SITE_URL+"member/uploadprofileimage";
    
    $.ajax({
      url: uurl,
      type: 'POST',
      data: formData,
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        if(response==1){
            new PNotify({title: "Profile images successfully uploaded.",styling: 'fontawesome',delay: '3000',type: 'success'});
             setTimeout(function() { window.location.reload(); }, 1500);
        }else if(response=='2'){
          new PNotify({title: "Uploaded file is not an zip file !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response=='3'){
          new PNotify({title: "Zip file not uploaded !",styling: 'fontawesome',delay: '3000',type: 'error'});
        }else if(response=='4'){
          new PNotify({title: 'Zip file is too large (max size ' + formatBytes(UPLOAD_MAX_ZIP_FILE_SIZE)+')!',styling: 'fontawesome',delay: '3000',type: 'error'});
        }else{
          new PNotify({title: response,styling: 'fontawesome',delay: '3000',type: 'error'});
        }
      },
      error: function(xhr) {
      //alert(xhr.responseText);
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

function updatememberstatus(){
  var status = $("#status").val();

  if(status!=""){
    var inputs = $("input[type='checkbox']");
    if(currentdids == ""){
        swal("Cancelled", 'Please select atleast one checkbox !', "error");
    }else{
        
      var datastr = 'status='+status+'&ids='+currentdids;
      var baseurl = SITE_URL+"member/update-member-status";
      $.ajax({
        url: baseurl,
        type: 'POST',
        data: datastr,
        beforeSend: function(){
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response){
            if(response==1){
                swal.close();
                if($('#deletecheckall').prop('checked') == true){
                    $('#deletecheckall').prop('checked', false);
                }
                for(var i=1;i<inputs.length;i++){
                    if($('#'+inputs[i].id).prop('checked') == true){
                        $('#'+inputs[i].id).prop('checked', false);
                    }
                }
                currentdids = [];
                position = 0;
                window.location.reload();
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
  }else{
    $("#status_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select status !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
}