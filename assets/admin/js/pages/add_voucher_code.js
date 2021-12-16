$(document).ready(function(){

  $('#expireddate').datepicker({
    language:  'fr',
    weekStart: 1,
    autoclose: 1,
    startDate: new Date(),
    startView: 3,
    forceParse: 0,
    format: "dd/mm/yyyy",
    clearBtn : true,
    todayHighlight: 1
  });

  $('#datepicker-range').datepicker({
      // todayHighlight: true,
      format: 'dd/mm/yyyy',
      autoclose: true,
      startDate: new Date(),
  });

   $('#cleardatebtn').click(function(){
      $("#startdate").val("");
      $("#enddate").val("");
   })

  $('input[name="isuniversal"]').click(function(){
    $("#vouchercode_div").removeClass("has-error is-focused");
    $("#noofcustomerused_div").removeClass("has-error is-focused");
    $("#quantity_div").removeClass("has-error is-focused");
    if ($(this).is(':checked')){

      if($(this).val() == 1){
        $('#vouchercode_div').show();
        $('#noofcustomerused_div').show();
        $('#quantity_div').hide();
      }else{                          
        $('#vouchercode_div').hide();
        $('#noofcustomerused_div').hide();
        $('#quantity_div').show();
      }
    }
  });
  $('input[name="discounttype"]').click(function(){
    $("#amount_div").removeClass("has-error is-focused");
    $("#percentageval_div").removeClass("has-error is-focused");
    if ($(this).is(':checked')){

      if($(this).val() == 1){               
        $('#amount_div').hide();
        $('#percentageval_div').show();
      }else{          
        $('#amount_div').show();
        $('#percentageval_div').hide();
      }
    }
  });

  if(ACTION==1){
    if($('input[name="discounttype"]:checked').val() == 1){
      $('#amount_div').hide();
      $('#percentageval_div').show();
    }else{          
      $('#amount_div').show();
      $('#percentageval_div').hide();
    }
  }
  $("#percentageval").keyup(function(e){
    
    if($(this).val()>100){
      $(this).val('100.00');  
    }
  });
});

function resetdata(){

  $("#name_div").removeClass("has-error is-focused");
  $("#vouchercode_div").removeClass("has-error is-focused");
  $("#maximumusage_div").removeClass("has-error is-focused");
  $("#percentageval_div").removeClass("has-error is-focused");
  $("#amount_div").removeClass("has-error is-focused");
  $("#noofcustomerused_div").removeClass("has-error is-focused");

  $('#vouchercode_div').show();
  $('#noofcustomerused_div').show();
  $('#amount_div').hide();
  $('#percentageval_div').show();

  $('#name').val('');
  $('#vouchercode').val('');
  $('#maximumusage').val(1);
  $('#percentageval').val('');
  $('#noofcustomerused').val(1);
  $('#expireddate').val('');
  $("#productid").val('');
  $('#percentage').prop("checked", true);
  $('#name').focus();
  
  $('#productid').selectpicker('refresh');
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
  
  var name = $("#name").val().trim();
  var maximumusage = $("#maximumusage").val().trim();
  var vouchercode = $("#vouchercode").val().trim();
  var noofcustomerused = $("#noofcustomerused").val().trim();
  var percentageval = $("#percentageval").val().trim();
  var amount = $("#amount").val().trim();

  var discounttype = $("input[name='discounttype']:checked").val();

  var isvalidname = isvalidmaximumusage =  0;
  var isvalidvouchercode = isvalidnoofcustomerused = isvalidpercentageval = isvalidamount = 1;
  
  PNotify.removeAll();
  if(name == ''){
    $("#name_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else { 
    if(name.length<2){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Vendor name require minmum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      isvalidname = 1;
    }
  }
  if(maximumusage == 0){
    $("#maximumusage_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter maximum usage by customer !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmaximumusage = 0;
  }else { 
    isvalidmaximumusage = 1;
  }

  if(vouchercode == ''){
      $("#vouchercode_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter voucher code !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidvouchercode = 0;
    }else { 
      if(vouchercode.length<3){
        $("#vouchercode_div").addClass("has-error is-focused");
        new PNotify({title: 'Voucher code require minmum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidvouchercode = 0;
      }
    }

    if(noofcustomerused == 0){
      $("#noofcustomerused_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter no of customer used !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidnoofcustomerused = 0;
    }

  if(discounttype==1){
    if(percentageval == 0){
      $("#percentageval_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter discount percentage !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidpercentageval = 0;
    }
  }else{
    if(amount == 0){
      $("#amount_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter discount amount !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidamount = 0;
    }
  }
  
  if(isvalidname == 1 && isvalidmaximumusage == 1 && isvalidvouchercode == 1 && isvalidnoofcustomerused == 1  && isvalidpercentageval == 1 && isvalidamount == 1){

    var formData = new FormData($('#vouchercodeform')[0]);
      
    if(ACTION==0){
          var uurl = SITE_URL+"voucher-code/add-voucher-code";
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
              new PNotify({title: "Coupon code successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"voucher-code"; }, 1500);
            }else if(response==2){
              new PNotify({title: 'Coupon code already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
            }else{
              new PNotify({title: 'Coupon code not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"voucher-code/update-voucher-code";
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
            new PNotify({title: "Coupon code successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location=SITE_URL+"voucher-code"; }, 1500);
          }else if(response==2){
              new PNotify({title: 'Coupon code already exist !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }else{
            new PNotify({title: 'Coupon code not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

