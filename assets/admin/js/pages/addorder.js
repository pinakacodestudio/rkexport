var countryid = 101;
$(document).ready(function(){
  addproductdetail();
  getprovince(countryid);
});
$('#provinceid').change(function(){
  var provinceid = $("#provinceid").val();
 
  $('#cityid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select City</option>')
      .val('whatever')
  ;
       
  if(countryid!=0 && provinceid!=0){
    getcity(provinceid);
  }
  $('#cityid').selectpicker('refresh');
});
$('#customerid').change(function(){
  $('#customerbillingid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select Billing Address</option>')
      .val('whatever')
  ;
  $('#customershippingid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select Shipping Address</option>')
      .val('whatever')
  ;
  if(this.value!=0){
    getcustomerbillingaddress(this.value);
  }
  $('#customerbillingid').selectpicker('refresh');
  $('#customershippingid').selectpicker('refresh');
});
$("#shippingamount").keyup(function(){
    amounttotal();
});
$("#codamount").keyup(function(){
    amounttotal();
});
function openmodal(type){
  PNotify.removeAll();
  $('#type').val(type);
  $("#firstname_div").removeClass("has-error is-focused");
  $("#lastname_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");
  $("#mobileno_div").removeClass("has-error is-focused");
  $("#address_div").removeClass("has-error is-focused");
  $("#postcode_div").removeClass("has-error is-focused");
  $("#provinceid_div").removeClass("has-error is-focused");
  $("#cityid_div").removeClass("has-error is-focused");

  $("#firstname").val("");
  $("#lastname").val("");
  $("#email").val("");
  $("#mobileno").val("");
  $("#address").val("");
  $("#postcode").val("");
  $("#provinceid").val(0);
  $('#provinceid').selectpicker('refresh');
  getcity(0);

  if(type==1){
    $('.modal-title').html('Add Customer');
    $('#myModal').modal('show');
  }else if(type==2){
    var customerid = $('#customerid').val();
    if(customerid!=0){
      $('.modal-title').html('Add Customer Billing Address');
      $('#myModal').modal('show');  
    }else{
      $("#customerid_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select customer !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
    
  }else if(type==3){
    var customerid = $('#customerid').val();
    if(customerid!=0){
      $('.modal-title').html('Add Customer Shipping Address');
      $('#myModal').modal('show');  
    }else{
      $("#customerid_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select customer !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
    
  }
}
function getcustomerbillingaddress(customerid,billingid=0,shippingid=0){
  if(customerid!=0){
    var uurl = SITE_URL+"Customer/getCustomerBillingAddress";
      
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {customerid:customerid},
      dataType:'json',
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){

        $('#customerbillingid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select Billing Address</option>')
            .val('whatever')
        ;
        $('#customershippingid')
            .find('option')
            .remove()
            .end()
            .append('<option value="0">Select Shipping Address</option>')
            .val('whatever')
        ;

        var temp = response;
        response = temp['billingaddress'];
        for(var i = 0; i < response.length; i++) {
          var address = response[i]['address'];
          if(response[i]['cityname']!=''){
            address += ' ' + ucwords(response[i]['cityname']);
          }
          if(response[i]['postcode']!=''){
            address += " - " + ucwords(response[i]['postcode']);
          }
          address += ' ' + ucwords(response[i]['provincename']) + ", " + ucwords(response[i]['countryname']);
          $('#customerbillingid').append($('<option>', { 
            value: response[i]['id'],
            text : address
          }));
          if(billingid!=0){
            $('#customerbillingid').val(billingid);
          }
        }
        $('#customerbillingid').selectpicker('refresh');

        response = temp['shippingaddress'];
        for(var i = 0; i < response.length; i++) {
          var address = response[i]['address'];
          if(response[i]['cityname']!=''){
            address += ' ' + ucwords(response[i]['cityname']);
          }
          if(response[i]['postcode']!=''){
            address += " - " + ucwords(response[i]['postcode']);
          }
          address += ' ' + ucwords(response[i]['provincename']) + ", " + ucwords(response[i]['countryname']);
          $('#customershippingid').append($('<option>', { 
            value: response[i]['id'],
            text : address
          }));
          if(shippingid!=0){
            $('#customershippingid').val(shippingid);
          }
        }
        $('#customershippingid').selectpicker('refresh');
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
function getcarmodel(productid,elementid){
  var succeed = 0;
  var uurl = SITE_URL+"carmodel/getActiveProductCarmodel";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {productid:productid},
    dataType: 'json',
    async: false,
    success: function(response){
      $('#carmodelid'+elementid)
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select Car Model</option>')
      .val('whatever')
      ;
      
      for(var i = 0; i < response.length; i++) {

        $('#carmodelid'+elementid).append($('<option>', { 
          value: response[i]['id'],
          text : response[i]['name']
        }));

      }
      $('#carmodelid'+elementid).selectpicker('refresh');
    },
    complete: function(){
      succeed = 1;
    },
    error: function(xhr) {
          //alert(xhr.responseText);
          succeed = 0;
        },
      });
  return succeed;
}
function getproductpricedata(productid,carmodelid='',elementid){
  if(productid!=0){
    var uurl = SITE_URL+"Product/getProductPriceByCarmodel";
      
    $.ajax({
      url: uurl,
      type: 'POST',
      data: {productid:productid,carmodelid:carmodelid},
      datatype:'json',
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){
        var response = JSON.parse(response);
        
        if(response!=0){
          $('#price'+elementid).val(response['price']);
          if(response['productstock']>0){
            $('#productstock'+elementid).val(response['productstock']);
            $('#price'+elementid+'_div').append('<span class="mandatoryfield">In Stock '+response['productstock']+'</span>');
          }else{
            $('#productstock'+elementid).val(0);
            $('#price'+elementid+'_div').append('<span class="mandatoryfield">Sold Out</span>');
          }
        }else{
          $('#price'+elementid).val(0);
          $('#productstock'+elementid).val(0);
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
function countproductprice(elementid){
    
    var quantity = price = 1;
    var amount=0;
    quantity = $('#qty'+elementid).val();            
    price = $('#price'+elementid).val();
    if(price==0 || price==""){
       price=1;
       $('#amount'+elementid).val(0);
    }else{
      amount  = parseFloat(quantity) * parseFloat(price);
      $('#amount'+elementid).val((amount).toFixed(2));
    }
}
function amounttotal(){

    var sum = 0;

    $(".amounttprice").each(function(){
        var id = $(this).attr('id').match(/(\d+)/g);
        sum += +$(this).val();
    });
    var shippingvalue = ($('#shippingamount').val()!='')?parseFloat($('#shippingamount').val()):0;
    var codvalue = ($('#codamount').val()!='')?parseFloat($('#codamount').val()):0;
    $("#subtotal").html(sum.toFixed(2));
    $("#shippingvalue").html(shippingvalue.toFixed(2));
    $("#codvalue").html(codvalue.toFixed(2));
    $("#totalamount").html((sum+shippingvalue+codvalue).toFixed(2));
    $("#finalamount").val((sum).toFixed(2));
}
function removeproductdetail(productcount){
  var removeorderproductdetailid = $('#removeorderproductdetailid').val();
  
  amount = $('#amount'+productcount).val();            
  totalamount = $('#totalamount').val();

  $('#productcount'+productcount).remove();
  amounttotal();
  
}
function addcustomer(){

  var type = $("#type").val().trim();
  var firstname = $("#firstname").val().trim();
  var lastname = $("#lastname").val().trim();
  var email = $("#email").val().trim();
  var mobileno = $("#mobileno").val().trim();
  var address = $("#address").val().trim();
  var postcode = $("#postcode").val().trim();
  var provinceid = $("#provinceid").val();
  var cityid = $("#cityid").val();
  
  var isvalidfirstname = isvalidlastname = isvalidemail = isvalidmobileno = isvalidaddress = isvalidpostcode = isvalidprovinceid = isvalidcityid = 1;
  PNotify.removeAll();
  if(firstname == ''){
    $("#firstname_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter first name !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfirstname = 0;
  }else if(firstname.length<3){
    $("#firstname_div").addClass("has-error is-focused");
    new PNotify({title: "First name require minimum 3 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfirstname = 0;
  }else{
    $("#firstname_div").removeClass("has-error is-focused");
    isvalidfirstname = 1;
  }
  if(lastname == ''){
    $("#lastname_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter last name !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidlastname = 0;
  }else if(lastname.length<3){
    $("#lastname_div").addClass("has-error is-focused");
    new PNotify({title: "Last name require minimum 3 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidlastname = 0;
  }else{
    $("#lastname_div").removeClass("has-error is-focused");
    isvalidlastname = 1;
  }
  if(email != ''){
    if(!ValidateEmail(email)){
      $("#lastname_div").addClass("has-error is-focused");
      new PNotify({title: "Please enter valid email address !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidemail = 0;
    }else{
      $("#email_div").removeClass("has-error is-focused");
      isvalidemail = 1;
    }
  }else{
    $("#email_div").removeClass("has-error is-focused");
    isvalidemail = 1;
  }
  if(mobileno == ''){
    $("#mobileno_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter mobile number !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmobileno = 0;
  }else if(mobileno < 4){
    $("#mobileno_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter minimum 4 digit mobile number !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmobileno = 0;
  }else{
    $("#mobileno_div").removeClass("has-error is-focused");
    isvalidmobileno = 1;
  }
  if(address == ''){
    $("#address_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter address !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidaddress = 0;
  }else if(address < 3){
    $("#address_div").addClass("has-error is-focused");
    new PNotify({title: "Address require minimum 3 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidaddress = 0;
  }else{
    $("#address_div").removeClass("has-error is-focused");
    isvalidaddress = 1;
  }
  if(postcode == ''){
    $("#postcode_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter postcode !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpostcode = 0;
  }else if(postcode < 4){
    $("#postcode_div").addClass("has-error is-focused");
    new PNotify({title: "Please enter minimum 4 digit post number !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidpostcode = 0;
  }else{
    $("#postcode_div").removeClass("has-error is-focused");
    isvalidpostcode = 1;
  }
  if(provinceid == 0){
    $("#provinceid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select province !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidprovinceid = 0;
  }else{
    $("#provinceid_div").removeClass("has-error is-focused");
    isvalidprovinceid = 1;
  }
  if(cityid == 0){
    $("#cityid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select city !",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcityid = 0;
  }else{
    $("#cityid_div").removeClass("has-error is-focused");
    isvalidcityid = 1;
  }
  
  if(isvalidfirstname == 1 && isvalidlastname == 1 && isvalidemail == 1 && isvalidmobileno == 1 && isvalidaddress == 1 && 
      isvalidpostcode == 1 && isvalidprovinceid == 1 && isvalidcityid == 1){

    if(type==2 || type==3){
       var customerid = $("<input>")
               .attr("type", "hidden")
               .attr("name", "customerid").val($('#customerid').val()); 
      $('#customerform').append($(customerid));
    }

    var formData = new FormData($('#customerform')[0]);
    var uurl = SITE_URL+"customer/addcustomer";
    $.ajax({
      url: uurl,
      type: 'POST',
      data: formData,
      dataType: 'json',
      //async: false,
      beforeSend: function(){
        $('.mask').show();
        $('#loader').show();
      },
      success: function(response){

        if($.isNumeric(response['customerid'])){
          if(type==1){
            new PNotify({title: "Customer details successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});  
            $('#customerid').append('<option value="'+response['customerid']+'">'+response['customername']+'</option>')
                            .val(response['customerid']);

            getcustomerbillingaddress(response['customerid'],response['customerbillingid'],response['customershippingid']);
            
            $('#customerid').selectpicker('refresh');
            $('#myModal').modal('hide');
          }else if(type==2){
            getcustomerbillingaddress(response['customerid'],response['customerbillingid']);
            $('#myModal').modal('hide');
          }else if(type==3){
            getcustomerbillingaddress(response['customerid'],$('#customerbillingid').val(),response['customershippingid']);
            $('#myModal').modal('hide');
          }
          
        }else if(response==2){
          new PNotify({title: "Customer already exists!",styling: 'fontawesome',delay: '3000',type: 'error'});
        }
        else{
          new PNotify({title: "Customer details not added!",styling: 'fontawesome',delay: '3000',type: 'error'});
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

  var productid = $("select[name='productid[]']").map(function(){return $(this).val();}).get();
  var carmodelid = $("select[name='carmodelid[]']").map(function(){return $(this).val();}).get();
  var qty = $("input[name='qty[]']").map(function(){return $(this).val();}).get();
  var price = $("input[name='price[]']").map(function(){return $(this).val();}).get();
  var productstock = $("input[name='productstock[]']").map(function(){return $(this).val();}).get();
  var customerid = $("#customerid").val();
  var customerbillingid = $("#customerbillingid").val();
  var customershippingid = $("#customershippingid").val();
  var portalid = $("#portalid").val();
  var courierid = $("#courierid").val();
  var orderid = $("#orderid").val().trim();
  var gstname = $("#gstname").val().trim();
  var gstno = $("#gstno").val().trim();
  var regexp = /^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/;

  var isvalidcustomerid = isvalidcustomerbillingid = isvalidcustomershippingid = isvalidportalid = isvalidcourierid = 0;
  var isvalidorderid =  isvalidproductid = isvalidcarmodelid = isvalidqty = isvalidprice = isvalidproductstock = isvalidgstname = isvalidgstno = 1;

  var productlength = (productid.length>1)?productid.length-1:1;

  PNotify.removeAll();
  if(customerid==0){
    $("#customerid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select customer!",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcustomerid = 0;
  }else{
    $("#customerid_div").removeClass("has-error is-focused");
    isvalidcustomerid = 1;
  }
  if(customerbillingid==0){
    $("#customerbillingid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select customer billing address!",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcustomerbillingid = 0;
  }else{
    $("#customerbillingid_div").removeClass("has-error is-focused");
    isvalidcustomerbillingid = 1;
  }
  if(customershippingid==0){
    $("#customershippingid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select customer shipping address!",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcustomershippingid = 0;
  }else{
    $("#customershippingid_div").removeClass("has-error is-focused");
    isvalidcustomershippingid = 1;
  }
  if(portalid==0){
    $("#portalid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select portal!",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidportalid = 0;
  }else{
    $("#portalid_div").removeClass("has-error is-focused");
    isvalidportalid = 1;
    if(portalid!=1){
      if(orderid==''){
        $("#orderid_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter orderid!",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidorderid = 0;
      }else{
        $("#orderid_div").removeClass("has-error is-focused");
      }
    }else{
      $("#orderid_div").removeClass("has-error is-focused");
    }
  }
  if(courierid==0){
    $("#courierid_div").addClass("has-error is-focused");
    new PNotify({title: "Please select courier!",styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcourierid = 0;
  }else{
    $("#courierid_div").removeClass("has-error is-focused");
    isvalidcourierid = 1;
  }
  if(gstname != ''){   
    if(gstname.length<3){
      $("#gstname_div").addClass("has-error is-focused");
      new PNotify({title: "Name require minimum 3 characters !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidgstname = 0;
    }else{
      $("#gstname_div").removeClass("has-error is-focused");
      isvalidgstname = 1;
    }
  }else{
    $("#gstname_div").removeClass("has-error is-focused");
    isvalidgstname = 1;
  }
  if(gstno != ''){
    if(gstno.length!=15){
      $("#gstno_div").addClass("has-error is-focused");
      new PNotify({title: "GST number must be 15 characters!",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidgstno = 0;
    }else if(!regexp.test(gstno)){
      $("#gstno_div").addClass("has-error is-focused");
      new PNotify({title: "GST number should have at least 1 alphabet and 1 digit!",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidgstno = 0;
    }else { 
      $("#gstno_div").removeClass("has-error is-focused");
      isvalidgstno = 1;
    }
  }else{
    $("#gstno_div").removeClass("has-error is-focused");
    isvalidgstno = 1;
  }
  if(productlength==0){
    new PNotify({title: "Please Refresh Page No Product Show!",styling: 'fontawesome',delay: '3000',type: 'error'});
  }else{
    
    for (var i = 0; i < productlength; i++) {
      if(productid[i]==0){
        $("#productid"+(i+1)+"_div").addClass("has-error is-focused");
        new PNotify({title: "Please select "+(i+1)+" product name!",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidproductid = 0;
      }else{
        $("#productid"+(i+1)+"_div").removeClass("has-error is-focused");
      }    
      if(carmodelid[i]==0 && $('select#carmodelid'+(i+1)+' option').length>1){
        $("#carmodelid"+(i+1)+"_div").addClass("has-error is-focused");
        new PNotify({title: "Please select "+(i+1)+" car model name!",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcarmodelid = 0;
      }else{
        $("#carmodelid"+(i+1)+"_div").removeClass("has-error is-focused");
      }
      if(price[i]==0 || price[i]==''){
        $("#price"+(i+1)+"_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter product price!",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidprice = 0;
      }else{
        $("#price"+(i+1)+"_div").removeClass("has-error is-focused");
      }
      if(qty[i]==0 || qty[i]==''){
        $("#qty"+(i+1)+"_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter product quantity!",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidqty = 0;
      }else{
        if(productstock[i]==0){
          $("#qty"+(i+1)+"_div").addClass("has-error is-focused");
          new PNotify({title: "Product "+(i+1)+" input quantity not available!",styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidproductstock = 0;
        }else{
          $("#qty"+(i+1)+"_div").removeClass("has-error is-focused");
          $("#productstock"+(i+1)+"_div").removeClass("has-error is-focused");
        }
      }
    }
  }
  
  if(isvalidproductid == 1 && isvalidcarmodelid == 1 && isvalidqty == 1 && isvalidprice == 1){

    for (var i = 0; i < productlength; i++) {
      var checkproductid = $("#productid"+(i+1)).val();
      var checkcarmodelid = $("#carmodelid"+(i+1)).val();
      for (var j = i+1; j < productlength; j++) {
        var dubproductid = $("#productid"+(j+1)).val();
        var dubcarmodelid = $("#carmodelid"+(j+1)).val();
        if((checkproductid==dubproductid) && (checkcarmodelid==dubcarmodelid)){
          $("#productid"+(i+1)+"_div").addClass("has-error is-focused");
          $("#carmodelid"+(i+1)+"_div").addClass("has-error is-focused");
          new PNotify({title: "Please do not select product and carmodel same!",styling: 'fontawesome',delay: '3000',type: 'error'});
          isvalidcarmodelid = 0;
          isvalidproductid = 0;
        }else{
          $("#productid"+(i+1)+"_div").removeClass("has-error is-focused");
          $("#carmodelid"+(i+1)+"_div").removeClass("has-error is-focused");
        }
      }
    }

  }

  if(isvalidcustomerid == 1 && isvalidcustomerbillingid == 1 && isvalidcustomershippingid == 1 && isvalidportalid == 1 && isvalidcourierid == 1 &&
    isvalidorderid == 1 && isvalidproductid == 1 && isvalidcarmodelid == 1 && isvalidqty == 1 && isvalidprice == 1 &&
    isvalidproductstock == 1 && isvalidgstname == 1  && isvalidgstno == 1 ){
      
    var formData = new FormData($('#formorder')[0]);
    var uurl = SITE_URL+"Order/addorder";
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
          new PNotify({title: "Order successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
          setTimeout(function() { window.location.reload(); }, 1500);
        }else{
          new PNotify({title: "Order not added!",styling: 'fontawesome',delay: '3000',type: 'error'});
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