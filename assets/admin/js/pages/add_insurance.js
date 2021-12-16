$(document).ready(function(){

  $('#datepicker-range').datepicker({
      todayHighlight: true,
      format: 'dd/mm/yyyy'
  });
  $('#paymentdate').datepicker({
      todayHighlight: true,
      todayBtn:"linked",
      clearBtn:true,
      format: 'dd/mm/yyyy',
  });
  $(function() {
    var tabindex = 1;
      
    $('input,select,textarea,a').each(function() {
        if (this.type != "hidden") {
            var $input = $(this);
            $input.attr("tabindex", tabindex);
            tabindex++;
        }
    });
  });

  $("[data-provide='companyname']").each(function () {
      var $element = $(this);
    
      $element.select2({    
        allowClear: true,
        minimumInputLength: 3,     
        width: '100%',  
        placeholder: $element.attr("placeholder"),         
        createSearchChoice: function(term, data) {
          if ($(data).filter(function() {
            return this.text.localeCompare(term) === 0;
            }).length === 0) {
              return {
              id: term,
              text: term
              };
            }
        },
        ajax: {
          url: $element.data("url"),
          dataType: 'json',
          type: "POST",
          quietMillis: 50,
          data: function (term) {
            return {
                term: term,
            };
          },
          results: function (data) {            
            return {
              results: $.map(data, function (item) {
                return {
                    text: item.text,                        
                    id: item.text
                }
              })
            };
          }
        },
        initSelection: function (element, callback) {
          var id = $(element).val();        
          if (id !== "" && id != 0) {
            $.ajax($element.data("url"), {
                data: {
                    ids: id,
                },
                type: "POST",
                dataType: "json",
            }).done(function (data) {                
                callback(data);
            });
          }
        }
      });
  });
});
function validfile(obj,elethis){
  var val = obj.val();
  var id = obj.attr('id').match(/\d+/);
  var filename = obj.val().replace(/C:\\fakepath\\/i, '');
  if (elethis.files[0].size <= UPLOAD_MAX_FILE_SIZE) {
    switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
      case 'pdf' : case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png' :

        
        $("#textfile").val(filename);
        isvalidfiletext = 1;
        $("#proof_div").removeClass("has-error is-focused");
        break;
      default:
        $("#fileproof").val("");
        $("#textfile").val("");
        isvalidfiletext = 0;
        $("#proof_div").addClass("has-error is-focused");
        new PNotify({title: 'Accept only Image and PDF Files !',styling: 'fontawesome',delay: '3000',type: 'error'});
        break;
    }
  } else {
    isvalidfile = 0;
    $("#fileproof").val("");
    $("#textfile").val("");
    $("#proof_div").addClass("has-error is-focused");
    new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE) + ') !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  //validvideo(obj);
}
function resetdata(){

  $("#vehicleid_div").removeClass("has-error is-focused");
  $("#companyname_div").removeClass("has-error is-focused");
  $("#policyno_div").removeClass("has-error is-focused");
  $("#insurancedate_div").removeClass("has-error is-focused");
  $("#paymentdate_div").removeClass("has-error is-focused");
  $("#proof_div").removeClass("has-error is-focused");
  $("#duedate_div").removeClass("has-error is-focused");
  $("#amount_div").removeClass("has-error is-focused");

  if(ACTION==1){

  }else{
    $('#vehicleid').val('');
    $('#policyno').val('');
    $('#fromdate').val('');
    $('#todate').val('');
    $('#paymentdate').val('');
    $('#fileproof').val('');
    $('#textfile').val('');
    $('#duedate').val('');
    $('#amount').val('');
    $("#companyname").select2("val", "");
    $("#s2id_companyname > a").css({"background-color":"#FFF","border":"1px solid #D2D2D2"});

    $('.selectpicker').selectpicker('refresh');
    $('#yes').prop("checked", true);
  }
  $('html, body').animate({scrollTop:0},'slow');
}

function checkvalidation(addtype=0){
  
  var vehicleid = $("#vehicleid").val();
  var companyname = $("#companyname").val();
  var fromdate = $('#fromdate').val();
  
  var isvalidvehicleid = isvalidcompanyname = isvalidpolicyno = isvalidfromdate = isvalidpaymentdate = isvalidduedate = isvalidamount = 0;
  PNotify.removeAll();
  
  if(vehicleid == 0){
    $("#vehicle_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select vehicle !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else { 
    $("#vehicle_div").removeClass("has-error is-focused");
    isvalidvehicleid = 1;
  }
 
  if(companyname == 0){
    $("#companyname_div").addClass("has-error is-focused");
    $("#s2id_companyname > a").css({"background-color":"#FFECED","border":"1px solid #e51c23"});
    new PNotify({title: 'Please select insurance company !',styling: 'fontawesome',delay: '3000',type: 'error'});
  } else{
    $("#companyname_div").removeClass("has-error is-focused");
    $("#s2id_companyname > a").css({"background-color":"#FFF","border":"1px solid #D2D2D2"});
    isvalidcompanyname = 1;
  }
  if(fromdate == ''){
    $("#insurancedate_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select insurance date !',styling: 'fontawesome',delay: '3000',type: 'error'});
  }else { 
    $("#insurancedate_div").removeClass("has-error is-focused");
    isvalidfromdate = 1;
  }
  
  if(isvalidvehicleid == 1 && isvalidcompanyname == 1 && isvalidfromdate == 1){

    var formData = new FormData($('#insuranceform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"insurance/insurance-add";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          var data = JSON.parse(response);
          if(data['error']==1){
              new PNotify({title: "Insurance  successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              if (addtype == 1) {
                resetdata();
              } else {
                setTimeout(function() { window.location=SITE_URL+"insurance"; }, 1500);
              }
          }else if(data['error']==2){
            new PNotify({title: 'Insurance already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==4){
            new PNotify({title: 'Insurance file not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==3){
            new PNotify({title: 'Invalid file format !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==-1){
            new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Insurance not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
    }else{
      var uurl = SITE_URL+"insurance/update-insurance";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          var data = JSON.parse(response);
          if(data['error']==1){
            new PNotify({title: "Insurance successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
            if (addtype == 1) {
              setTimeout(function () { window.location = SITE_URL + "insurance/add-insurance";  }, 1500);
            } else {
              setTimeout(function() { window.location=SITE_URL+"insurance"; }, 1500);
            }
          }else if(data['error']==2){
            new PNotify({title: 'Insurance already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==4){
            new PNotify({title: 'Insurance file not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==3){
            new PNotify({title: 'Invalid file format !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==-1){
            new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Insurance not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
}