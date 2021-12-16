$(document).ready(function() {
    loadpopover();
    $('#discountreasondiv').hide();
  
    $('#deliverydate').datepicker({
        startDate: new Date(),
        todayHighlight: true,
        format: 'dd/mm/yyyy'
    });
    
    $('#deliverydate').datepicker()
      .on("changeDate", function(e) {
        $(this).datepicker('hide');
    });
  
    $('#discountprice').keypress(function(event) {
      if (((event.which != 46 || (event.which == 46 && $(this).val() == '')) ||
              $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
  
          event.preventDefault();
      }
          
    }).on('paste', function(event) {
          event.preventDefault();
    });
  
    $('#discountprice').keyup(function(event) {
      var disprice = parseFloat('00.00');
      if($(this).val() != ''){
        
        if(parseFloat($(this).val())>totalamount){
          PNotify.removeAll();
          new PNotify({title: "Discount price must be less-then to total amount",styling: 'fontawesome',delay: '3000',type: 'error'});
          $('#discount_div').number( disprice.toString(), 2, '.', ',' );
          $('#finaltotal_div').number( finaltotal.toString(), 2, '.', ',' );
        }else{
          
          disprice = parseFloat($(this).val()).toFixed(2);
          var total = parseFloat(finaltotal).toFixed(2);
          $('#discount_div').number( disprice.toString(), 2, '.', ',' );
          total = parseFloat(total - disprice).toFixed(2);
          
          $('#finaltotal_div').number( total.toString(), 2, '.', ',' );
        }
      }else{
        $('#discount_div').number( disprice.toString(), 2, '.', ',' );
        $('#finaltotal_div').number( finaltotal.toString(), 2, '.', ',' );
      }
      
    });
    
    $("#discountreason").on('keyup',function(e){    
      $('#discountreasontext').html($(this).val());
    });
  
  });
  
  $('#orderstatus').change(function(){
    if($(this).val()==1){
      $('#fordeliveronly_div').show();
      $('#savebtn_div').hide();
      $('#actionbtn_div').show();
    }else{
      $('#fordeliveronly_div').hide();
      $('#savebtn_div').show();
      $('#actionbtn_div').hide();
    }
  });
  
  function discount(val){
    $('#discountprice_div').removeClass("has-error is-focused");
    if(val==1){
        $('#discountprice').attr("disabled",false);
        $('#discountreason').attr("disabled",false);
        $('#discountreasondiv').show(); 
    }else{
      $('#discountprice').val('');
      $('#discountreason').val('');
      $('#discountprice').attr("disabled",true);
      $('#discountreason').attr("disabled",true);
      $('#discountreasondiv').hide();  
      var disprice = parseFloat('00.00');
      $('#discount_div').number( disprice.toString(), 2, '.', ',' );
      $('#finaltotal_div').number( finaltotal.toString(), 2, '.', ',' );
    }
  }
  
  function checkvalidation(type=''){
    var orderstatus = $('#orderstatus').val();
    var discountstatus = $('input[name=discountstatus]:checked').val();
    var discountprice = $('#discountprice').val();
    var deliverydate = $('#deliverydate').val();
    var discountreason = $('#discountreason').val();
  
    var isvaliddeliverydate= 0
    var isvaliddiscountprice = isvaliddiscountreason =  1;
  
    PNotify.removeAll();
  
    if(deliverydate == ''){
      $("html, body").animate({ scrollTop: 0 }, "slow");
      $("#deliverydate_div").addClass("has-error is-focused");
      new PNotify({title: "Please enter Delivered date",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvaliddeliverydate = 0;
    }else {
      $("#deliverydate_div").removeClass("has-error is-focused");
      isvaliddeliverydate = 1;
    }
  
    if(discountstatus==1){
      if(discountprice == ''){
        $("html, body").animate({ scrollTop: 0 }, "slow");
              $("#discountprice_div").addClass("has-error is-focused");
              new PNotify({title: "Please enter discount price !",styling: 'fontawesome',delay: '3000',type: 'error'});
              isvaliddiscountprice = 0;
      }else {
        if(discountprice>totalamount){
          $("html, body").animate({ scrollTop: 0 }, "slow");
            $("#discountprice_div").addClass("has-error is-focused");
            new PNotify({title: "Discount price must be less-then to total amount !",styling: 'fontawesome',delay: '3000',type: 'error'});
            isvaliddiscountprice = 0;
        }else{
          $("#paymentenddate_div").removeClass("has-error is-focused");
          isvaliddiscountprice = 1;
        }
      }
  
      if(discountreason != '' && discountreason<3){
        $("html, body").animate({ scrollTop: 0 }, "slow");
        $("#discountreason_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter discount reason !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddiscountreason = 0;
      }
    }else{
      var disprice = parseFloat('00.00');
      $('#discount_div').number( disprice.toString(), 2, '.', ',' );
      $('#finaltotal_div').number( finaltotal.toString(), 2, '.', ',' );
    }
  
    if(isvaliddeliverydate == 1 &&  isvaliddiscountprice ==1 && isvaliddiscountreason ==1){
  
      var uurl = SITE_URL+"invoice/add-invoice";
  
      $.ajax({
        url: uurl,
        type: 'POST',
        data: {finaltotal:$('#finaltotal_div').html(),orderstatus:orderstatus,deliverydate:deliverydate,discountstatus:discountstatus,discountprice:discountprice,discountreason:discountreason,
              orderproduct:orderproduct,orderdetail:orderdetail},
        //async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          var obj = JSON.parse(response);
          if(obj['error']==1){
            new PNotify({title: "Invoice successfully generated.",styling: 'fontawesome',delay: '3000',type: 'success'});
            if(type!=''){
              setTimeout(function() { var w = window.open(INVOICE_URL+'Rogermotor-Invoice-'+obj['invoicenumber']+".pdf",'_blank'); w.print(); }, 500);
            }
            setTimeout(function() { window.location=SITE_URL+"Invoice"; }, 1500);
            
          }else{
            new PNotify({title: "Invoice not generate !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
  function updateorderstatus(){
    var orderstatus = $('#orderstatus').val();
    
    var uurl = SITE_URL+"purchase-order/update-order-status";
  
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {orderstatus:orderstatus,ordernumber:$('#ordernumber').val()},
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        
        if(response==1){
          new PNotify({title: "Purchase order status successfully updated.",styling: 'fontawesome',delay: '3000',type: 'success'});
          setTimeout(function() { window.location=SITE_URL+"order"; }, 1500);
          
        }else{
          new PNotify({title: "Purchase order status not updated !",styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  function changeinstallmentstatus(status, installmentid){
      var uurl = SITE_URL+"purchase-order/update-installment-status";
          if(installmentid!=''){
                swal({    title: "Are you sure to pay installment ?",
                  type: "warning",   
                  showCancelButton: true,   
                  confirmButtonColor: "#DD6B55",   
                  confirmButtonText: "Yes, payment it!",   
                  closeOnConfirm: true }, 
                  function(isConfirm){   
                    if (isConfirm) {   
                      $.ajax({
                          url: uurl,
                          type: 'POST',
                          data: {status:status,installmentid:installmentid},
                          
                          success: function(response){
                            if(response==1){
                              if(status==1){
                                $("#btndropdown"+installmentid).removeClass("btn-warning");
                                $("#btndropdown"+installmentid).addClass("btn-success");
                                $("#btndropdown"+installmentid).html("Paid <span class='caret'></span>");
                              }else{
                                $("#btndropdown"+installmentid).removeClass("btn-success");
                                $("#btndropdown"+installmentid).addClass("btn-warning");
                                $("#btndropdown"+installmentid).html("Pending <span class='caret'></span>");
                              }
                             }
                             location.reload();
                           },
                          error: function(xhr) {
                          //alert(xhr.responseText);
                          }
                        });  
                      }
                    });
  
              }           
  }
  function printorderinvoice(id){
  
    var uurl = SITE_URL + "purchase-order/printOrderInvoice";
    $.ajax({
        url: uurl,
        type: 'POST',
        data: {
            id:id
        },
        //dataType: 'json',
        async: false,
        beforeSend: function() {
            $('.mask').show();
            $('#loader').show();
        },
        success: function(response) {
            
            var data = JSON.parse(response);
            var html = data['content'];
          
            var frame1 = document.createElement("iframe");
            frame1.name = "frame1";
            frame1.style.position = "absolute";
            frame1.style.top = "-1000000px";
            document.body.appendChild(frame1);
            var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
            frameDoc.document.open();
            frameDoc.document.write(html);
            frameDoc.document.close();
            setTimeout(function () {
              window.frames["frame1"].focus();
              window.frames["frame1"].print();
              document.body.removeChild(frame1);
            }, 500);
        },
        error: function(xhr) {
            //alert(xhr.responseText);
        },
        complete: function() {
            $('.mask').hide();
            $('#loader').hide();
        },
    });
  }