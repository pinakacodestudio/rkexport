$(document).ready(function() {

  $('#openingbalancedate').datepicker({
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    orientation: 'top',
    autoclose: true,
    todayBtn: "linked",
    clearBtn: true
  });
});

function resetdata(){
 
  $("#bankname_div").removeClass("has-error is-focused");
  $("#accountno_div").removeClass("has-error is-focused");
  $("#branchname_div").removeClass("has-error is-focused");
  $("#branchaddress_div").removeClass("has-error is-focused");

  if(ACTION==0)
  {
    $('#bankname').val('');
    $('#ifsccode').val('');
    $('#micrcode').val('');
    $('#accountno').val('');
    $('#branchname').val('');
    $('#branchaddress').val('');
    $('#openingbalance').val('');
    $('#openingbalancedate').val('');
    
    $('#yes').prop("checked", true);
  }
  $('html, body').animate({scrollTop:0},'slow');  
}

function checkvalidation(){

    var bankname = $("#bankname").val().trim();
    var accountno = $("#accountno").val();
    var branchname = $("#branchname").val().trim();
    var branchaddress = $("#branchaddress").val();    
    
    var isvalidbankname = isvalidaccountno = isvalidbranchname = isvalidbranchaddress = 1;

    PNotify.removeAll();
   
    if(accountno=="") {
        $("#accountno_div").addClass("has-error is-focused");
        new PNotify({title: 'Please enter account no. !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidaccountno = 0;
    } else {
        if(accountno.length < 6){
            $("#accountno_div").addClass("has-error is-focused");
            new PNotify({title: 'Please enter account no. more than 6 digits !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidaccountno = 0;
        }else{
            $("#accountno_div").removeClass("has-error is-focused");
        }
    }
    if(bankname==""){
      $("#bankname_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter bank name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidbankname = 0;
    } else {
        if(bankname.length < 3){
            $("#bankname_div").addClass("has-error is-focused");
            new PNotify({title: 'Bank name required minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
            isvalidbankname = 0;
        }else{
            $("#bankname_div").removeClass("has-error is-focused");
        }
    }

    if(branchname!="" && branchname.length < 3){
        $("#branchname_div").addClass("has-error is-focused");
        new PNotify({title: 'Branch name required minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidbranchname = 0;
    }else{
        $("#branchname_div").removeClass("has-error is-focused");
    }

 
    if(branchaddress!="" && branchaddress.length < 3){
        $("#branchaddress_div").addClass("has-error is-focused");
        new PNotify({title: 'Branch address required minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidbranchaddress = 0;
    }else{
        $("#branchaddress_div").removeClass("has-error is-focused");
    }
  

  if(isvalidbankname==1 && isvalidaccountno==1 && isvalidbranchname==1 && isvalidbranchaddress==1){
            
  var formData = new FormData($('#cashorbankform')[0]);
    if(ACTION == 0){    
      var uurl = SITE_URL+"cash-or-bank/add-cash-or-bank";
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
          var data = JSON.parse(response);
          if(data['error']==1){
            new PNotify({title: 'Cash or bank successfully added.',styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else if(data['error']==2) {
            new PNotify({title: 'Cash or bank account already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else if(data['error']==3) {
            new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
          } else {
            new PNotify({title: 'Cash or bank not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"cash-or-bank/update-cash-or-bank";
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
              var data = JSON.parse(response);
              if(data['error']==1){
                new PNotify({title: 'Cash or bank successfully updated.',styling: 'fontawesome',delay: '3000',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+'cash-or-bank'; }, 1500);
              }else if(data['error']==2) {
                new PNotify({title: 'Cash or bank account already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
              }else if(data['error']==3) {
                new PNotify({title: data['message'],styling: 'fontawesome',delay: '3000',type: 'error'});
              } else {
                new PNotify({title: 'Cash or bank not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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