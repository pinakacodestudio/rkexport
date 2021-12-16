function amounttotal() {
  var sum = tax = 0;

  $("input[name^='creditvalue']").each(function(){
      var id = $(this).attr('id').match(/(\d+)/g);
      
      if($('#withtaxprice'+id[0]).val() && $('#creditvalue'+id[0]).val()!='' && $('#creditvalue'+id[0]).val()!=0){

        if($('#tax'+id[0]).val()){
          //tax += (parseFloat($('#creditqty'+id[0]).val()) * parseFloat($('#withtaxprice'+id[0]).val()) * parseFloat($('#tax'+id[0]).val()) )/100;
        }
      }
      
      sum += +$(this).val();
  });
  $("#subtotal").html(sum.toFixed(2));
  //$("#taxvalue").html(tax.toFixed(2));
  $("#totalamount").html( (parseFloat(sum.toFixed(2))+parseFloat(tax.toFixed(2))).toFixed(2)) ;
}
function loaddatacredittotalandtax(productcount){
    
    var quantity=price=1;
    var credittax=amount=creditqty=0;
    creditqty = $('#creditqty'+productcount).val();
    qty = $('#qty'+productcount).val();   
    actualprice = $('#actualprice'+productcount).val();
    amount = $('#amount'+productcount).val();
    oldqty = parseFloat($('#oldqty'+productcount).val());

    PNotify.removeAll();
    if(creditqty!='' && creditqty!=0){
        
        if(creditqty<=(qty-oldqty)){
            
            creditvalue = parseFloat(creditqty) * parseFloat(actualprice);
            
            $('#creditvalue'+productcount).val(creditvalue.toFixed(2));
            credittax  =parseFloat((creditvalue)*100)/ (parseFloat(amount));
            $('#credittax'+productcount).val(credittax.toFixed(2));
        }else{
            new PNotify({title: "Credit value is not more then remain credit quantity !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $('#creditqty'+productcount).val("");
            $('#credittax'+productcount).val("");
            $('#creditvalue'+productcount).val("");
        }
    }else{
        $('#creditqty'+productcount).val("");
        $('#credittax'+productcount).val("");
        $('#creditvalue'+productcount).val("");

    }
    amounttotal();
}
function loaddatacredittotal(productcount){

    var quantity=price=1;
    var credittax=amount=0;
    credittax = $('#credittax'+productcount).val();            
    amount = $('#amount'+productcount).val();

    tamount  = parseFloat(amount) * parseFloat(credittax);
    creditvalue  = tamount /100;
    if(creditvalue){

        if(credittax>100){

            $('#credittax'+productcount).val("100.00");
            $('#creditvalue'+productcount).val(amount);
        }else{
            $('#creditvalue'+productcount).val(creditvalue.toFixed(2));
        }
        
    }else{
        $('#creditvalue'+productcount).val("");
    }

    amounttotal();
}

function loaddatacredittax(productcount){

    var credittax=0;
    var creditvalue=amount=0;
    creditvalue = $('#creditvalue'+productcount).val();          
    amount = $('#amount'+productcount).val();
    oldamount = $('#oldamount'+productcount).val();
    PNotify.removeAll();

    if(creditvalue!=''){

        if(creditvalue<=(amount-oldamount)){
            creditvalue  = parseFloat(creditvalue) * 100;
            credittax  =parseFloat(creditvalue)/ (parseFloat(amount));
              
              if(credittax){

                if(credittax>100){
                    new PNotify({title: "Credit value is not more then total value !",styling: 'fontawesome',delay: '3000',type: 'error'});
                    $('#credittax'+productcount).val("");
                }else{
                    $('#credittax'+productcount).val(credittax.toFixed(2));
                
                }
              }else{
                $('#credittax'+productcount).val("");
              }
        }else{
            new PNotify({title: "Credit value is not more then remain credit value !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $('#credittax'+productcount).val("");
            $('#creditvalue'+productcount).val("");
        }
    }
    amounttotal();
  
}
function mappingproduct(productid,mappingid,price){
  var actualprice = parseFloat($('#actualprice'+productid).val());
  
  if($("#mappingcheck"+mappingid).prop('checked') == true){
    $('#actualprice'+productid).val(actualprice+price); 
  }else{
    $('#actualprice'+productid).val(actualprice-price); 
  }
  
  loaddatacredittotalandtax(productid);
  amounttotal();
}
function enabletext(id){
  var inputs = $("input[type='checkbox']");
  var creditqty = $("input[name='creditqty[]']");
  var inputstax = $("input[name='credittax[]']");
  var inputsvalue = $("input[name='creditvalue[]']");
  var isallchecked = 1,isalldechecked = 1;
  
    var elementid = id.replace ( /[^\d.]/g, '' );

  if($('#'+id).prop('checked')==true){
    currentdids[position] = $('#'+id).val();                      
    position++;
        $('#creditqty'+elementid).prop('disabled', false);
        $('#credittax'+elementid).prop('disabled', false);
        $('#creditvalue'+elementid).prop('disabled', false);        
        loaddatacredittotalandtax(elementid);
        
  }else{
    $('#creditqty'+elementid).prop('disabled', true);
    $('#credittax'+elementid).prop('disabled', true);
    $('#creditvalue'+elementid).prop('disabled', true);
    $('#credittax'+elementid).val("");
    $('#creditvalue'+elementid).val("");
    currentdids.splice($.inArray($('#'+id).val(), currentdids),1);
   
    position--;
  }
  amounttotal();
}

function checkvalidation(btntype=''){
    
  var creditqty = $("input[name='creditqty[]']").map(function(){return $(this).val();}).get();
  var credittax = $("input[name='credittax[]']").map(function(){return $(this).val();}).get();
  var creditvalue = $("input[name='creditvalue[]']").map(function(){return $(this).val();}).get();
  var description = $('#description').val();
  var ordernumber = $('#ordernumber').val();
  

  var isvalidcreditqty = isvalidcredittax = isvaliddescription = isvalidcreditvalue= 0;
  var isvalidproductcredit = 1;
  
  var inputs = $("input[name='creditcheck[]']");

  PNotify.removeAll();
  
  var totalchecked = 0;
  for (var i = 0; i < inputs.length; i++) {
    if($('#'+inputs[i].id).prop('checked') == true){
      
      if(creditqty[i] == '' || creditqty[i]==0){
        $("html, body").animate({ scrollTop: 0 }, "slow");
        new PNotify({title: "Please enter credit quantity !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcreditqty = 0;
      }else {
        isvalidcreditqty = 1;
      }
      if(credittax[i] == '' || credittax[i]==0){
        $("html, body").animate({ scrollTop: 0 }, "slow");              
        new PNotify({title: "Please enter credit tax !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcredittax = 0;
      }else {
        isvalidcredittax = 1;
      }

      if(creditvalue[i] == '' || creditvalue[i]==0){
        $("html, body").animate({ scrollTop: 0 }, "slow");
        new PNotify({title: "Please enter credit value !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcreditvalue = 0;
      }else{
        isvalidcreditvalue = 1;
      }
    }else{
      totalchecked++;
    }    
  }
  if(totalchecked==inputs.length){
    isvalidproductcredit==0;
    new PNotify({title: "Please add at least one product credit value !",styling: 'fontawesome',delay: '3000',type: 'error'});
  }
  if(description == ''){
    $("#description_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter description !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddescription = 0;
  }else{
    if(description.length < 3){
      $("#description_div").addClass("has-error is-focused"); 
      new PNotify({title: "Description require minimum 3 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvaliddescription = 0;
    }else{
      isvaliddescription = 1;
    }
  }
   
  if(isvalidcreditqty == 1 && isvalidcredittax == 1 && isvalidcreditvalue == 1 &&  isvaliddescription ==1 && isvalidproductcredit == 1){
    
    $('#credittotal').val($('#totalamount').text());

    var formData = new FormData($('#creditnoteform')[0]);
    var uurl = SITE_URL+"Creditnote/addcreditnote";

    $.ajax({
      url: uurl,
      type: 'POST',
      //data: {totalamount:$('#totalamount').text(),orderproductid:orderproductid,mappingarray:mappingarray,creditqty:creditqty,credittax:credittax,
            //creditvalue:creditvalue,description:description,ordercloseproductdata:ordercloseproductdata,invoicedata:invoicedata},
      data:formData,
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        var obj = JSON.parse(response);
        if(obj['error']==1){
          new PNotify({title: "Creditnote successfully generated !",styling: 'fontawesome',delay: '3000',type: 'success'});
          if(btntype!=''){
            setTimeout(function() { var w = window.open(CREDITNOTE_URL+"Rogermotor-Creditnote-"+obj['creditnotenumber']+".pdf",'_blank'); w.print(); }, 500);
          }
          setTimeout(function() { window.location=SITE_URL+"creditnote"; }, 1500);
        }else{
          new PNotify({title: "Creditnote not generate !",styling: 'fontawesome',delay: '3000',type: 'error'});
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