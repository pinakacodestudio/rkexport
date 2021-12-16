$(document).ready(function() {

      $('#date').datepicker({
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            orientation: 'top',
            autoclose: true,
            todayBtn: "linked"
        });
      $("#old_receipt_div").hide();

      /* $("#remove_old_receipt").click(function()
      {
        $("#isvalidcatalogfile").val(1);
        $("#old_receipt_div").show();
        $("#receipt_download_div").hide();
      }); */

      $('#remove').click(function(){
        $('#removeoldreceipt').val('1');
      });
    
    

   
    
});
function validreceiptfile(obj,element){
  var val = obj.val();
  var id = obj.attr('id').match(/\d+/);
  var filename = obj.val().replace(/C:\\fakepath\\/i, '');
  
  switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
    case 'pdf': case 'jpg': case 'jpeg': case 'png': case 'docx':
      $("#"+element+"text").val(filename);
      $("#"+element+'_div').removeClass("has-error is-focused");
      break;
    default:
      $("#"+element).val("");
      $("#"+element+"text").val("");
      $("#"+element+'_div').addClass("has-error is-focused");
      new PNotify({title: 'Please upload valid '+element+' file !',styling: 'fontawesome',delay: '3000',type: 'error'});
      break;
  }
}
function resetdata(){

  $("#expensecategory_div").removeClass("has-error is-focused");
  $("#date_div").removeClass("has-error is-focused");
  $("#amount_div").removeClass("has-error is-focused");
  $("#remarks_div").removeClass("has-error is-focused");
  $("#reason_div").removeClass("has-error is-focused");
  $("#employeeid_div").removeClass("has-error is-focused");
  $("#receipt_div").removeClass("has-error is-focused");

  if(ACTION==0){
    $("#employeeid").val("0");
    $('#expensecategory').val("0");
    $('#date').val('');
    $('#amount').val('');
    $('#remarks').val('');
    $('#reason').val('');
    $('#receipt').val('');
    $('#receipttext').val('');
    $(".selectpicker").selectpicker("refresh");
  }
 
  $('html, body').animate({scrollTop:0},'slow');
  
}
function checkvalidation(){

  var employeeid=$("#employeeid").val();
  var expensecategory = $("#expensecategory").val();
  var date = $("#date").val().trim();
  var amount = $("#amount").val().trim();
  var remarks = $("#remarks").val().trim();
  var reason = $("#reason").val().trim();
  
  var isvalidemployeeid = isvalidexpensecategory = isvaliddate = isvalidamount = isvalidremarks = isvalidreason = 1;

  PNotify.removeAll();
  if(employeeid == 0){
    $("#employeeid_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select employee !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemployeeid = 0;
  }else {
    $("#employeeid_div").removeClass("has-error is-focused");
  }

  if(expensecategory == 0){
    $("#expensecategory_div").addClass("has-error is-focused");
    new PNotify({title: "Please select expense category !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidexpensecategory = 0;
  }else {
    $("#expensecategory_div").removeClass("has-error is-focused");
  }

  if(date == ''){
    $("#date_div").addClass("has-error is-focused");
    new PNotify({title: "Please select date !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddate = 0;
  }else {
    $("#date_div").removeClass("has-error is-focused");
  } 

  if(amount == ''){
    $("#amount_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidamount = 0;
  }else {
    $("#amount_div").removeClass("has-error is-focused");
  }

  if(remarks == ''){
    $("#remarks_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter remarks !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidremarks = 0;
  }else {
    if(remarks.length<2){
      $("#remarks_div").addClass("has-error is-focused");
      new PNotify({title: 'Remarks require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidremarks = 0;
    }else{
      $("#amount_div").removeClass("has-error is-focused");
    }
  }

  if(reason == ''){
    $("#reason_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter reason !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidreason = 0;
  }else {
    if(reason.length<2){
      $("#reason_div").addClass("has-error is-focused");
      new PNotify({title: 'Reason require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidreason = 0;
    }else{
      $("#reason_div").removeClass("has-error is-focused");
    }
  } 
  
  if(isvalidemployeeid == 1 && isvalidexpensecategory == 1 && isvaliddate == 1 && isvalidamount == 1 && isvalidremarks == 1 && isvalidreason == 1){

    var formData = new FormData($('#expenseform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"Expense/expense-add";

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
            new PNotify({title: "Expense successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"expense"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Receipt file is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE)+') !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $('#receipt_div').addClass("has-error is-focused");
          }else if(response==3){
            new PNotify({title: 'Receipt file type does not valid !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $('#receipt_div').addClass("has-error is-focused");
          }else if(response==4){
            new PNotify({title: 'Receipt file not uploaded !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $('#receipt_div').addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Expense not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"Expense/expense-update";
      
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
              new PNotify({title: "Expense successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"expense"; }, 1500);
          }else if(response==2){
            new PNotify({title: "Expense already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Expense not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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