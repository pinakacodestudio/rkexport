
$(document).ready(function() {

  getallstatuscounts();
  oTable = $('#dailyfollowuptable').DataTable({
    "processing": true,//Feature control the processing indicator.
    "language": {
        "lengthMenu": "_MENU_"
    },
    drawCallback: function () {
      $('.popoverButton').popover({
          "html": true,
          trigger: 'manual',
          placement: 'right',
          "content": function () {
              return "";
          }
      })
    },
    "columnDefs": [ {
      "targets": [0,3,-1,-2],
      "orderable": false
    } ],
    responsive: true,
    'serverSide': true,//Feature control DataTables' server-side processing mode.
    "ajax": {
      "url": SITE_URL+"daily-followup/listing",
      "type": "POST",
      "data" :function ( data ) {
        data.filterstatus = $("#filterstatus").val();
        data.filtermember = $("#filtermember").val();
        data.filteremployee = $("#filteremployee").val();
        data.fromdate = $('#startdate').val();
        data.todate = $('#enddate').val();
        data.filterfollowuptype = $('#filterfollowuptype').val();
      },
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      complete: function(){
        $('.mask').hide();
        $('#loader').hide();
      },
    }
  });

  $('.dataTables_filter input').attr('placeholder','Search...');


  //DOM Manipulation to move datatable elements integrate to panel
  $('.panel-ctrls.panel-tbl').append($('.dataTables_filter').addClass("pull-right")).find("label").addClass("panel-ctrls-center");
  $('.panel-ctrls.panel-tbl').append("<i class='separator'></i>");
  $('.panel-ctrls.panel-tbl').append($('.dataTables_length').addClass("pull-left")).find("label").addClass("panel-ctrls-center");

  $('.panel-footer').append($(".dataTable+.row"));
  $('.dataTables_paginate>ul.pagination').addClass("pull-right pagination-md");

  $('#datepicker-range').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayBtn:"linked"
  });
  $('#datepicker-range1').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayBtn:"linked"
  });
  
  $(function () {
    $('.panel-heading.filter-panel').click(function() {
        $(this).find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
        //$(this).children().toggleClass(" ");
        $(this).next().slideToggle({duration: 200});
        $(this).toggleClass('panel-collapsed');
        if($(this).hasClass("flpstatuspnl")){
          savecollapse($(this).attr("display-type"),'panel-heading.filter-panel.flpstatuspnl','status');
        }else{
          savecollapse($(this).attr("display-type"),'panel-heading.filter-panel.flppnl','followup');
        }
        return false;
    });
  });

  var panel1displaytype = $('.panel-heading.filter-panel.flpstatuspnl').attr("display-type");
  if(panel1displaytype==0){
    $('.panel-heading.filter-panel.flpstatuspnl').find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
    //$(this).children().toggleClass(" ");
    $('.panel-heading.filter-panel.flpstatuspnl').next().slideToggle({duration: 200});
    $('.panel-heading.filter-panel.flpstatuspnl').toggleClass('panel-collapsed');
  }
  var panel2displaytype = $('.panel-heading.filter-panel.flppnl').attr("display-type");
  if(panel2displaytype==0){
    $('.panel-heading.filter-panel.flppnl').find('.button-icon span').html($(this).find('.button-icon span').text() == 'keyboard_arrow_down' ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
    //$(this).children().toggleClass(" ");
    $('.panel-heading.filter-panel.flppnl').next().slideToggle({duration: 200});
    $('.panel-heading.filter-panel.flppnl').toggleClass('panel-collapsed');
  }

  $('.js-data-example-ajax').select2({
    placeholder: "Select "+Member_label,
    // minimumInputLength: 3,
    ajax: {
      url: SITE_URL+'daily-followup/getmembers',
      dataType: 'json',
      quietMillis: 100,
      type:"post",
      data: function (term, page) { // page is the one-based page number tracked by Select2
        return {
            term: term, //search term
            page_limit: 25, // page size
            page: page, // page number
            gettype:1,
        };
      },
      results: function (data, page) {
        var more = (page * 25) < data.total; // whether or not there are more results available
        result = data.results;
        // notice we return the value of more so Select2 knows if more results can be loaded
        return {results: result, more: more};
      }
    },
    initSelection: function(element, callback) {
      if($("#filtermember").val()!=""){
        callback({id: $("#filtermember").val(), text: $("#filtermember").attr("data-text") });
      }
    },
    formatResult: styleFormatResult, // omitted for brevity, see the source of this page
    dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
    escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
  });

  $("#resendotp").click(function(event){         
    event.preventDefault();  

    $.ajax({  
      url: SITE_URL+"daily-followup/resendOtpData",  
      method:"POST",  
      data:$('#insert_form').serialize(),       
      success:function(data){                           
          $('#add_data_Modal').modal('show');  
          if(data==1){
            new PNotify({title: "OTP send successfully.",styling: 'fontawesome',delay: '3000',type: 'success'});
          }else{
            new PNotify({title: "OTP not send !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }
      }  
    });     
  }); 
  
  $('#insert_form').on("submit", function(event){             
    event.preventDefault();  
    PNotify.removeAll();
    if($("#code").val() == ""){
      $("#code_div").addClass("has-error is-focused");
      new PNotify({title: "Please enter OTP !",styling: 'fontawesome',delay: '3000',type: 'error'});
    }else{
      $.ajax({  
        url: SITE_URL+"daily-followup/update-followup-status",  
        method:"POST",  
        data:$('#insert_form').serialize(),  
        beforeSend:function(){  
              $('#update').val("Verifying...");  
        },   
        success:function(data){                           
          if(data==1){
            new PNotify({title: "Followup status successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
            $('#add_data_Modal').modal('hide');
            $("#code_div").removeClass("has-error is-focused");  
            setTimeout(function() { window.location=SITE_URL+"daily-followup"; }, 1500);
          }else if(data==2){
            $("#code_div").addClass("has-error is-focused");
            new PNotify({title: "OTP is not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: "Followup status not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
          }
        },
        complete:function(){  
          $('#update').val("SUBMIT");  
    },  
      }); 
    }
  });

  var date = new Date();
  date.setDate(date.getDate());
  displaydate = date.getDate()+"/"+(date.getMonth()+1)+"/"+date.getFullYear();
  
});

function applyFilter(){
  oTable.ajax.reload(null,false);
}

function applyFilter1(){
  getallstatuscounts();
}
function styleFormatResult(style) {
  var markup = style.text;
  return markup;
}

function getallstatuscounts(){
  
  fromdate = $('#startdate1').val();
  todate = $('#enddate1').val();
  filteremployee = $('#filterstatusemployee').val();
  var uurl = SITE_URL+"daily-followup/savestatusfilter";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {fromdate:fromdate,todate:todate,filteremployee:filteremployee},
    dataType: 'json',
    async: false,
    success: function(response){
      $.each(response, function( key, value ) {
        $("#status_count"+value.id).html(value.statuscount);
      });
    },
    error: function(xhr) {
          //alert(xhr.responseText);
        },
  }); 
}

function savecollapse(displaytype,cls,panel){
  var uurl = SITE_URL+"daily-followup/savecollapse";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {displaytype:displaytype,panel:panel},
    dataType: 'json',
    success: function(response){
      if(response.displaytype=='1'){
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

function changefollowupstatus(sts,followupid,statustext,color="",mobileno) {
  swal({title: 'Are you sure to change status?',
      type: "warning",   
      showCancelButton: true,   
      confirmButtonColor: "#DD6B55",   
      confirmButtonText: "Yes, change it!",
      timer: 2000,   
    }, 
    function(isConfirm){
      if (isConfirm) {               
        if(sts == 6){               
          var uurl = SITE_URL+"daily-followup/getotpdata";
          $.ajax({
            url: uurl,
            type: 'POST',
            data: {status:sts,id:followupid,mobileno:mobileno},
            dataType: 'json',
            //async: false,
            success: function(data){ 
              $('#fid').val(data.id); 
              $('#add_data_Modal').modal('show');
            }, 
            error: function(xhr) {
              //alert(xhr.responseText);
            },
          }); 
        }else{
          var uurl = SITE_URL+"daily-followup/change-followup-status";
          $.ajax({
            url: uurl,
            type: 'POST',
            data: {status:sts,id:followupid,mobileno:mobileno},
            dataType: 'json',
            //async: false,
            success: function(response){
              if(response) {                                                                   
                $("#btndropdown"+followupid).css("background",color);
                $("#btndropdown"+followupid).html(statustext+' <span class="caret"></span>');                      
                $("#ddm"+followupid+" li").removeClass("active");
                $("#ddm"+followupid+" li").each(function() {
                  if($(this).find('a').text() == statustext){
                    $(this).addClass("active");
                  }
                });
              }
            },
            error: function(xhr) {
              //alert(xhr.responseText);
            },
          }); 
        }
      }
    }
  );
}

function loadfollowup_modal(followupid) {
  $("#followupid").val(followupid);
  var uurl = SITE_URL+"daily-followup/getfollowupdetail";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {followupid:followupid},
    dataType: 'json',
    async: false,
    success: function(response){
      $("#date").val(response.date+' '+response.time);
      $("#olddate").val(response.date+' '+response.time);
      $("#reason_div").hide();
      $('#reason').val('');
      $("#employee").val(response.assignto);
      $("#oldassignto").val(response.assignto);
      $("#followuptype").val(response.followuptype);
      $("#oldfollowuptype").val(response.followuptype);
      $("#status").val(response.status);
      $("#oldstatus").val(response.status);
      $("#followupnote").val(response.notes);
      $("#oldfollowupnote").val(response.notes);
      $("#futurenote").val(response.futurenotes);
      $("#oldfuturenote").val(response.futurenotes);
      $("#latitude").val(response.latitude);
      $("#oldlatitude").val(response.latitude);
      $("#longitude").val(response.longitude);
      $("#oldlongitude").val(response.longitude);
      if(($("#employee").val()==null || $.inArray(response.assignto,child_employee_data) == -1) && alldatarights==0){
        $("#employee").append("<option value='"+response.assignto+"' class='newoptions'>"+response.assignemp+"<option>");
        $("#employee").val(response.assignto);
        $("#employee").prop("disabled",true);
      }else{
        $("#employee").prop("disabled",false);
      }
      $("#employee").selectpicker("refresh");
      $("#followuptype").selectpicker("refresh");
      $("#status").selectpicker("refresh");

      $('.followupdate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: true,
        timePicker: true,
        /*timePicker24Hour: true,*/
        //minYear: 1901,
        minDate: displaydate,
        locale: {
          format: 'DD/MM/YYYY HH:mm'
        },
      });
    },
    error: function(xhr) {
      //alert(xhr.responseText);
    },
  });
}

function clonefollowup(followupid) {
  
  var uurl = SITE_URL+"daily-followup/getfollowupdetail";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {followupid:followupid},
    dataType: 'json',
    async: false,
    success: function(response){
      $("#inquiryid").val(response.inquiryid);
      
      $("#newfollowupemployee").val(response.assignto);
      $("#newfollowuptype").val(response.followuptype);
      $("#newfollowupstatus").val(response.status);
      $("#newfollowupnote").val(response.notes);
      $("#newfollowupfuturenote").val(response.futurenotes);
      $("#newfollowuplatitude").val(response.latitude);
      $("#newfollowuplongitude").val(response.longitude);
      $("#memberid").val(response.memberid);

      if(($("#newfollowupemployee").val()==null || $.inArray(response.assignto,child_employee_data) == -1) && alldatarights==0){
        $("#newfollowupemployee").append("<option value='"+response.assignto+"' class='newoptions'>"+response.assignemp+"<option>");
        $("#newfollowupemployee").val(response.assignto);
        $("#newfollowupemployee").prop("disabled",true);
      }else{
        $("#newfollowupemployee").prop("disabled",false);
      }

      $("#newfollowupemployee").selectpicker("refresh");
      $("#newfollowuptype").selectpicker("refresh");
      $("#newfollowupstatus").selectpicker("refresh");
      
      $('.followupdate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: true,
        timePicker: true,
        /*timePicker24Hour: true,*/
        //minYear: 1901,
        minDate: displaydate,
        locale: {
          format: 'DD/MM/YYYY HH:mm'
        },
      });
    },
    error: function(xhr) {
      //alert(xhr.responseText);
    },
  });
}

function reschedulefollowup(followupid) {
  
  var uurl = SITE_URL+"daily-followup/getfollowupdetail";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {followupid:followupid},
    dataType: 'json',
    async: false,
    success: function(response){
      
      $("#rinquiryid").val(response.inquiryid);
      $("#rfollowupid").val(response.id);
      $("#oldstatusid").val(response.followuptype);
      $("#rfollowupemployee").val(response.assignto);
      $("#rfollowuptype").val(response.followuptype);
      $("#rfollowupstatus").val(response.status);
      $("#rfollowupnote").val(response.notes);
      $("#rfollowupfuturenote").val(response.futurenotes);
      $("#rfollowuplatitude").val(response.latitude);
      $("#rfollowuplongitude").val(response.longitude);
      $("#rfmemberid").val(response.memberid);

      if(($("#rfollowupemployee").val()==null || $.inArray(response.assignto,child_employee_data) == -1) && alldatarights==0){
        $("#rfollowupemployee").append("<option value='"+response.assignto+"' class='newoptions'>"+response.assignemp+"<option>");
        $("#rfollowupemployee").val(response.assignto);
        $("#rfollowupemployee").prop("disabled",true);
      }else{
        $("#rfollowupemployee").prop("disabled",false);
      }

      $("#rfollowupemployee").selectpicker("refresh");
      $("#rfollowuptype").selectpicker("refresh");
      $("#rfollowupstatus").selectpicker("refresh");
      
      $('.followupdate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: true,
        timePicker: true,
        /*timePicker24Hour: true,*/
        //minYear: 1901,
        minDate: displaydate,
        locale: {
          format: 'DD/MM/YYYY HH:mm'
        },
      });
    },
    error: function(xhr) {
      //alert(xhr.responseText);
    },
  });
}

function checkfollowupvalidation(){
  
  var date = $("#newfollowupdate").val().trim();
  var followuptype = $("#newfollowuptype").val().trim();
  var status = $("#newfollowupstatus").val().trim();
  var note = $("#newfollowupnote").val().trim();
  
  isvalidfollowuptype = isvalidstatus = isvaliddate = isvalidnote = 0 ;

  PNotify.removeAll();
  if(date == ""){
    $("#newfollowupdate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddate = 0;
  }else {
      isvaliddate = 1;
  }

  if(followuptype == 0 || followuptype == null || followuptype == ""){
    $("#newfollowuptype_div").addClass("has-error is-focused");
    new PNotify({title: "Please select "+Follow_up_label+" type !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfollowuptype = 0;
  }else {
      isvalidfollowuptype = 1;
  }
 
  if(status == null || status == ""){
    $("#newfollowupstatus_div").addClass("has-error is-focused");
    new PNotify({title: "Please Select Status !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidstatus = 0;
  }else {
    isvalidstatus = 1;
  } 

  if(note == ''){
    $("#newfollowupnote_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter note !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidnote = 0;
  }else {
    isvalidnote = 1;
  }

  if(isvaliddate == 1 && isvalidfollowuptype == 1 && isvalidstatus == 1 && isvalidnote == 1) {

    var formData = new FormData($('#newfollowupform')[0]);
    var uurl = SITE_URL+"crm-inquiry/addfollowup";
    
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
          new PNotify({title: Followup_label+" successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
          oTable.ajax.reload(null,false);
          $("#followupModal").modal("toggle");
          $("#newfollowupdate_div,#newfollowupemployee_div,#newfollowuptype_div,#newfollowupstatus_div,#newfollowupnote_div,#newfollowupfuturenote_div").removeClass("has-error is-focused");
        }else{
          new PNotify({title: Followup_label+' not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

function checkfollowupvalidation1(){
  
  var rdate = $("#rfollowupdate").val().trim();
  var rfollowuptype = $("#rfollowuptype").val().trim();
  var rstatus = $("#rfollowupstatus").val().trim();
  var rnote = $("#rfollowupnote").val().trim();
  
  isvalidrfollowuptype = isvalidrstatus = isvalidrdate = isvalidrnote = 0 ;

  PNotify.removeAll();
  if(rdate == ""){
    $("#rfollowupdate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidrdate = 0;
  }else {
    isvalidrdate = 1;
  }

  if(rfollowuptype == 0 || rfollowuptype == null || rfollowuptype == ""){
    $("#rfollowuptype_div").addClass("has-error is-focused");
    new PNotify({title: "Please select "+Follow_up_label+" type !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidrfollowuptype = 0;
  }else {
      isvalidrfollowuptype = 1;
  }
 
  if(rstatus == null || rstatus == ""){
    $("#rfollowupstatus_div").addClass("has-error is-focused");
    new PNotify({title: "Please Select Status !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidrstatus = 0;
  }else {
    isvalidrstatus = 1;
  } 

  if(rnote == ''){
    $("#rfollowupnote_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter note !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidrnote = 0;
  }else {
      isvalidrnote = 1;
  }

  if(isvalidrdate == 1 && isvalidrfollowuptype == 1 && isvalidrstatus == 1 && isvalidrnote == 1) {

    var formData = new FormData($('#reschedulefollowupform')[0]);
    var uurl = SITE_URL+"daily-followup/addreschedulefollowup";
    
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
          new PNotify({title: Followup_label+" successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
          oTable.ajax.reload(null,false);
          
          $("#followupModalReschedule").modal("toggle");
          $("#rfollowupdate_div,#rfollowupemployee_div,#rfollowuptype_div,#rfollowupstatus_div,#rfollowupnote_div,#rfollowupfuturenote_div").removeClass("has-error is-focused");
        }else{
          new PNotify({title: Followup_label+' not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
function checkvalidation(){
  
  var date = $("#date").val();
  var time = $("#time").val();
  var followuptype = $("#followuptype").val();
  var status = $("#status").val();
  var note = $("#followupnote").val();
  var reason = $("#reason").val();
  
  isvalidfollowuptype = isvalidstatus = isvaliddate = isvalidnote = isvalidreason = isvalidtime = 0 ;

  PNotify.removeAll();
  if(date == ""){
    $("#date_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select date !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddate = 0;
  }else {
    isvaliddate = 1;
  }

  if(time == ""){
    $("#time_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select time !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidtime = 0;
  }else {
    isvalidtime = 1;
  }

  if(followuptype == 0 || followuptype == null || followuptype == ""){
    $("#followuptype_div").addClass("has-error is-focused");
    new PNotify({title: "Please select "+follow_up_label+" type !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfollowuptype = 0;
  }else {
    isvalidfollowuptype = 1;
  }
 
  if(status == null || status == ""){
    $("#status_div").addClass("has-error is-focused");
    new PNotify({title: "Please Select Status !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidstatus = 0;
  }else {
    isvalidstatus = 1;
  } 

  if(note == ''){
    $("#note_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter note !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidnote = 0;
  }else {
    isvalidnote = 1;
  }

  if(reason == '' && $("#reason_div").is(":visible")){
    $("#reason_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter reason !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidreason = 0;
  }else {
    isvalidreason = 1;
  }

  if(isvaliddate == 1 && isvalidfollowuptype == 1 && isvalidstatus == 1 && isvalidnote == 1 && isvalidreason==1 && isvalidtime==1) {
    $("#employee").prop("disabled",false);

    var formData = new FormData($('#followupform')[0]);
    var uurl = SITE_URL+"daily-followup/upfate-followup";
    
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
          new PNotify({title: Followup_label+" successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
          oTable.ajax.reload(null,false);
          $("#myModal").modal("toggle");
          $("#date_div,#time_div,#employee_div,#followuptype_div,#status_div,#note_div,#futurenote_div").removeClass("has-error is-focused");
        }else{
          new PNotify({title: Followup_label+' not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

function resetdata(){
  $("#date").val($('#olddate').val());
  $("#employee").val($('#oldassignto').val());
  $("#followuptype").val($('#oldfollowuptype').val());
  $("#status").val($('#oldstatus').val());
  $("#followupnote").val($('#oldfollowupnote').val());
  $("#futurenote").val($('#oldfuturenote').val());
  $("#latitude").val($('#oldlatitude').val());
  $("#longitude").val($('#oldlongitude').val());

  $("#date_div").removeClass("has-error is-focused");
  $("#time_div").removeClass("has-error is-focused");
  $("#followuptype_div").removeClass("has-error is-focused");
  $("#status_div").removeClass("has-error is-focused");
  $("#note_div").removeClass("has-error is-focused");
  $("#reason_div").removeClass("has-error is-focused");

  $("#reason_div").hide();
  $('#reason').val('');

  $("#employee").selectpicker("refresh");
  $("#followuptype").selectpicker("refresh");
  $("#status").selectpicker("refresh");
}

function exportdailyfollowup(){

  var filterstatus = $('#filterstatus').val();
  var filtermember = $('#filtermember').val();
  var filteremployee = $('#filteremployee').val();
  var fromdate = $('#startdate').val();
  var todate = $('#enddate').val();
  var filterfollowuptype = $('#filterfollowuptype').val();

  var totalRecords =$("#dailyfollowuptable").DataTable().page.info().recordsDisplay;
  if(totalRecords != 0){
    window.location= SITE_URL+"daily-followup/exporttoexceldailyfollowup?filterstatus="+filterstatus+"&filtermember="+filtermember+"&filteremployee="+filteremployee+"&fromdate="+fromdate+"&todate="+todate+"&filterfollowuptype="+filterfollowuptype;
  }else{
    new PNotify({title: 'No follow up found as per set filter !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  
}
